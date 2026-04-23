<?php

namespace App\Http\Controllers;

use App\Models\ClassCategory;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search');

        $members = User::query()
            ->where('role', 'member')
            ->when($search->isNotEmpty(), function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('student_id', 'like', "%{$search}%")
                        ->orWhere('class_name', 'like', "%{$search}%")
                        ->orWhereHas('classCategory', fn ($classQuery) => $classQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->with('classCategory')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('members.index', compact('members'));
    }

    public function create(): View
    {
        return view('members.create', [
            'member' => new User(),
            'classCategories' => ClassCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $member = User::create($this->validateMember($request));
        $member->load('classCategory');

        ActivityLogger::log(
            $request->user(),
            'member.create',
            'Menambahkan anggota baru: '.$member->name,
            $member,
            [
                'username' => $member->username,
                'nis' => $member->student_id,
                'kelas' => $member->classCategory?->name,
            ],
        );

        return redirect()->route('members.index')->with('success', 'Data anggota berhasil ditambahkan.');
    }

    public function edit(User $member): View
    {
        abort_unless($member->isMember(), 404);

        return view('members.edit', [
            'member' => $member,
            'classCategories' => ClassCategory::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $member): RedirectResponse
    {
        abort_unless($member->isMember(), 404);

        $member->update($this->validateMember($request, $member->id, false));
        $member->load('classCategory');

        ActivityLogger::log(
            $request->user(),
            'member.update',
            'Memperbarui data anggota: '.$member->name,
            $member,
            [
                'username' => $member->username,
                'nis' => $member->student_id,
                'kelas' => $member->classCategory?->name,
            ],
        );

        return redirect()->route('members.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(Request $request, User $member): RedirectResponse
    {
        abort_unless($member->isMember(), 404);

        if ($member->loans()->whereIn('status', ['pending', 'borrowed', 'late'])->exists()) {
            return back()->with('error', 'Anggota masih memiliki transaksi aktif dan tidak bisa dihapus.');
        }

        ActivityLogger::log(
            $request->user(),
            'member.delete',
            'Menghapus data anggota: '.$member->name,
            $member,
            [
                'username' => $member->username,
                'nis' => $member->student_id,
            ],
        );

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Data anggota berhasil dihapus.');
    }

    private function validateMember(Request $request, ?int $memberId = null, bool $requirePassword = true): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($memberId)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($memberId)],
            'student_id' => ['required', 'string', 'max:50', Rule::unique('users', 'student_id')->ignore($memberId)],
            'class_category_id' => ['required', 'exists:class_categories,id'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'password' => [$requirePassword ? 'required' : 'nullable', 'confirmed', 'min:6'],
        ]);

        $classCategory = ClassCategory::findOrFail($validated['class_category_id']);

        $payload = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'student_id' => $validated['student_id'],
            'class_name' => $classCategory->name,
            'class_category_id' => $classCategory->id,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => 'member',
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        return $payload;
    }
}
