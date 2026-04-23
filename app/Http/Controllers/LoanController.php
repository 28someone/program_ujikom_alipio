<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LoanController extends Controller
{
    private function activeStatuses(): array
    {
        return ['borrowed', 'late'];
    }

    private function determineLoanStatus(string $dueAt, ?string $returnedAt = null): string
    {
        if ($returnedAt !== null) {
            return 'returned';
        }

        return Carbon::parse($dueAt)->isPast() ? 'late' : 'borrowed';
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $search = $request->string('search');
        $status = $request->string('status');

        $loans = Loan::with(['user', 'book', 'processor'])
            ->when($user->isMember(), fn ($query) => $query->where('user_id', $user->id))
            ->when($search->isNotEmpty(), function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('loan_code', 'like', "%{$search}%")
                        ->orWhereHas('book', fn ($bookQuery) => $bookQuery->where('title', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status->isNotEmpty(), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('loans.index', [
            'loans' => $loans,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->isAdmin(), 403);

        return view('loans.create', [
            'loan' => new Loan(),
            'members' => User::where('role', 'member')->orderBy('name')->get(),
            'books' => Book::where('stock_available', '>', 0)->orderBy('title')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'user_id' => [$user->isAdmin() ? 'required' : 'nullable', 'exists:users,id'],
            'book_id' => ['required', 'exists:books,id'],
            'borrowed_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after_or_equal:borrowed_at'],
            'note' => ['nullable', 'string'],
        ]);

        $memberId = $user->isAdmin() ? (int) $validated['user_id'] : $user->id;
        $book = Book::findOrFail($validated['book_id']);

        if ($book->stock_available < 1) {
            return back()->with('error', 'Stok buku sedang habis.')->withInput();
        }

        DB::transaction(function () use ($validated, $memberId, $book, $user) {
            $isAdmin = $user->isAdmin();

            if ($isAdmin) {
                $book->decrement('stock_available');
            }

            $loan = Loan::create([
                'loan_code' => 'TRX-'.str_pad((string) (Loan::count() + 1), 4, '0', STR_PAD_LEFT),
                'user_id' => $memberId,
                'book_id' => $book->id,
                'processed_by' => $isAdmin ? $user->id : null,
                'borrowed_at' => $validated['borrowed_at'],
                'due_at' => $validated['due_at'],
                'quantity' => 1,
                'status' => $isAdmin ? $this->determineLoanStatus($validated['due_at']) : 'pending',
                'note' => $validated['note'] ?? null,
                'fine_amount' => 0,
            ]);

            $loan->load(['user', 'book']);

            ActivityLogger::log(
                $user,
                $isAdmin ? 'loan.create' : 'loan.request',
                $isAdmin
                    ? 'Menambahkan transaksi peminjaman: '.$loan->loan_code
                    : 'Mengajukan permintaan peminjaman: '.$loan->loan_code,
                $loan,
                [
                    'anggota' => $loan->user->name,
                    'buku' => $loan->book->title,
                    'jatuh_tempo' => $loan->due_at?->format('Y-m-d'),
                    'status' => $loan->status,
                ],
            );
        });

        return redirect()->route('loans.index')->with(
            'success',
            $user->isAdmin()
                ? 'Transaksi peminjaman berhasil disimpan.'
                : 'Permintaan peminjaman berhasil dikirim dan menunggu persetujuan admin.'
        );
    }

    public function edit(Request $request, Loan $loan): View
    {
        abort_unless($request->user()->isAdmin(), 403);

        return view('loans.edit', [
            'loan' => $loan->load(['user', 'book']),
        ]);
    }

    public function update(Request $request, Loan $loan): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'borrowed_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after_or_equal:borrowed_at'],
            'status' => ['required', Rule::in(['pending', 'rejected', 'borrowed', 'returned', 'late'])],
            'note' => ['nullable', 'string'],
        ]);

        $wasActive = in_array($loan->status, $this->activeStatuses(), true);
        $willBeActive = in_array($validated['status'], $this->activeStatuses(), true);

        if (! $wasActive && $willBeActive && $loan->book->stock_available < 1) {
            return back()->with('error', 'Stok buku tidak mencukupi untuk menyetujui transaksi ini.')->withInput();
        }

        DB::transaction(function () use ($loan, $validated, $request, $wasActive, $willBeActive) {
            if (! $wasActive && $willBeActive) {
                $loan->book->decrement('stock_available');
            }

            if ($wasActive && ! $willBeActive) {
                $loan->book->increment('stock_available');
            }

            $returnedAt = $validated['status'] === 'returned'
                ? ($loan->returned_at ?? now()->toDateString())
                : null;

            $loan->update([
                ...$validated,
                'returned_at' => $returnedAt,
                'processed_by' => in_array($validated['status'], ['pending', 'rejected'], true)
                    ? ($validated['status'] === 'rejected' ? $request->user()->id : null)
                    : ($loan->processed_by ?? $request->user()->id),
                'status' => in_array($validated['status'], ['pending', 'rejected'], true)
                    ? $validated['status']
                    : ($validated['status'] === 'returned'
                        ? 'returned'
                        : $this->determineLoanStatus($validated['due_at'])),
                'fine_amount' => $validated['status'] === 'returned'
                    ? $loan->calculateFineAmount(Carbon::parse($returnedAt))
                    : 0,
            ]);
        });

        $loan->load(['user', 'book']);

        ActivityLogger::log(
            $request->user(),
            'loan.update',
            'Memperbarui transaksi peminjaman: '.$loan->loan_code,
            $loan,
            [
                'anggota' => $loan->user->name,
                'buku' => $loan->book->title,
                'status' => $loan->status,
            ],
        );

        return redirect()->route('loans.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function approve(Request $request, Loan $loan): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Transaksi ini tidak lagi menunggu persetujuan admin.');
        }

        if ($loan->book->stock_available < 1) {
            return back()->with('error', 'Stok buku tidak mencukupi untuk menyetujui transaksi ini.');
        }

        DB::transaction(function () use ($loan, $request) {
            $loanDuration = max(1, Carbon::parse($loan->borrowed_at)->diffInDays(Carbon::parse($loan->due_at)));
            $borrowedAt = now();
            $dueAt = (clone $borrowedAt)->addDays($loanDuration);

            $loan->book->decrement('stock_available');

            $loan->update([
                'processed_by' => $request->user()->id,
                'borrowed_at' => $borrowedAt->toDateString(),
                'due_at' => $dueAt->toDateString(),
                'status' => $this->determineLoanStatus($dueAt->toDateString()),
            ]);
        });

        $loan->load(['user', 'book']);

        ActivityLogger::log(
            $request->user(),
            'loan.approve',
            'Menyetujui permintaan peminjaman: '.$loan->loan_code,
            $loan,
            [
                'anggota' => $loan->user->name,
                'buku' => $loan->book->title,
                'status' => $loan->status,
            ],
        );

        return redirect()->route('loans.index')->with('success', 'Permintaan peminjaman berhasil disetujui.');
    }

    public function reject(Request $request, Loan $loan): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Transaksi ini tidak lagi menunggu persetujuan admin.');
        }

        $loan->update([
            'processed_by' => $request->user()->id,
            'status' => 'rejected',
            'fine_amount' => 0,
            'return_note' => null,
        ]);

        $loan->load(['user', 'book']);

        ActivityLogger::log(
            $request->user(),
            'loan.reject',
            'Menolak permintaan peminjaman: '.$loan->loan_code,
            $loan,
            [
                'anggota' => $loan->user->name,
                'buku' => $loan->book->title,
                'status' => $loan->status,
            ],
        );

        return redirect()->route('loans.index')->with('success', 'Permintaan peminjaman berhasil diubah menjadi Rejected.');
    }

    public function destroy(Request $request, Loan $loan): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        ActivityLogger::log(
            $request->user(),
            'loan.delete',
            'Menghapus transaksi peminjaman: '.$loan->loan_code,
            $loan,
            [
                'anggota_id' => $loan->user_id,
                'buku_id' => $loan->book_id,
            ],
        );

        DB::transaction(function () use ($loan) {
            if (in_array($loan->status, $this->activeStatuses(), true)) {
                $loan->book->increment('stock_available');
            }

            $loan->delete();
        });

        return redirect()->route('loans.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function borrowForm(Request $request): View
    {
        abort_unless($request->user()->isMember(), 403);

        return view('loans.borrow', [
            'books' => Book::with('category')
                ->where('stock_available', '>', 0)
                ->orderBy('title')
                ->paginate(15),
        ]);
    }

    public function returnBook(Request $request, Loan $loan): RedirectResponse
    {
        $user = $request->user();
        abort_unless($loan->user_id === $user->id || $user->isAdmin(), 403);

        if ($loan->status === 'returned') {
            return back()->with('error', 'Buku ini sudah dikembalikan.');
        }

        if ($loan->status === 'pending') {
            return back()->with('error', 'Peminjaman ini masih menunggu persetujuan admin dan belum bisa dikembalikan.');
        }

        if ($loan->status === 'rejected') {
            return back()->with('error', 'Peminjaman ini berstatus Rejected dan tidak dapat diproses sebagai pengembalian.');
        }

        DB::transaction(function () use ($loan, $request, $user) {
            $loan->book->increment('stock_available');
            $returnedAt = now();

            $loan->update([
                'processed_by' => $user->id,
                'returned_at' => $returnedAt->toDateString(),
                'status' => 'returned',
                'return_note' => $request->input('return_note'),
                'fine_amount' => $loan->calculateFineAmount($returnedAt),
            ]);
        });

        $loan->load(['user', 'book']);

        ActivityLogger::log(
            $user,
            'loan.return',
            'Memproses pengembalian buku untuk transaksi: '.$loan->loan_code,
            $loan,
            [
                'anggota' => $loan->user->name,
                'buku' => $loan->book->title,
                'tanggal_kembali' => $loan->returned_at?->format('Y-m-d'),
                'denda' => 'Rp '.number_format($loan->fine_amount, 0, ',', '.'),
            ],
        );

        $message = 'Pengembalian buku berhasil diproses.';

        if ($loan->fine_amount > 0) {
            $message .= ' Denda: Rp '.number_format($loan->fine_amount, 0, ',', '.').'.';
        }

        return redirect()->route('loans.index')->with('success', $message);
    }
}
