<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search');
        $category = $request->string('category');

        $books = Book::with('category')
            ->when($search->isNotEmpty(), function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('publisher', 'like', "%{$search}%");
                });
            })
            ->when($category->isNotEmpty(), fn ($query) => $query->where('category_id', $category))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('books.index', [
            'books' => $books,
            'categories' => Category::orderBy('name')->get(),
            'isAdmin' => $request->user()->isAdmin(),
        ]);
    }

    public function create(): View
    {
        return view('books.create', [
            'book' => new Book(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateBook($request);
        $validated['cover_image'] = $this->storeCoverImage($request->file('cover_image'));
        $validated['stock_available'] = $validated['stock_total'];

        $book = Book::create($validated);

        ActivityLogger::log(
            $request->user(),
            'book.create',
            'Menambahkan data buku baru: '.$book->title,
            $book,
            [
                'kode_buku' => $book->code,
                'stok_total' => $book->stock_total,
            ],
        );

        return redirect()->route('catalog.index')->with('success', 'Data buku berhasil ditambahkan.');
    }

    public function edit(Book $book): View
    {
        return view('books.edit', [
            'book' => $book,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $this->validateBook($request, $book->id);
        $validated['cover_image'] = $book->cover_image;

        if ($request->hasFile('cover_image')) {
            $this->deleteUploadedCover($book->cover_image);
            $validated['cover_image'] = $this->storeCoverImage($request->file('cover_image'));
        }

        $borrowedCount = max(0, $book->stock_total - $book->stock_available);
        $validated['stock_available'] = max(0, $validated['stock_total'] - $borrowedCount);

        $book->update($validated);

        ActivityLogger::log(
            $request->user(),
            'book.update',
            'Memperbarui data buku: '.$book->title,
            $book,
            [
                'kode_buku' => $book->code,
                'stok_total' => $book->stock_total,
                'stok_tersedia' => $book->stock_available,
            ],
        );

        return redirect()->route('catalog.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Request $request, Book $book): RedirectResponse
    {
        if ($book->loans()->whereIn('status', ['pending', 'borrowed', 'late'])->exists()) {
            return back()->with('error', 'Buku masih memiliki transaksi aktif dan tidak bisa dihapus.');
        }

        ActivityLogger::log(
            $request->user(),
            'book.delete',
            'Menghapus data buku: '.$book->title,
            $book,
            [
                'kode_buku' => $book->code,
            ],
        );

        $this->deleteUploadedCover($book->cover_image);
        $book->delete();

        return redirect()->route('catalog.index')->with('success', 'Data buku berhasil dihapus.');
    }

    private function validateBook(Request $request, ?int $bookId = null): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'code' => ['required', 'max:50', Rule::unique('books', 'code')->ignore($bookId)],
            'title' => ['required', 'max:255'],
            'author' => ['required', 'max:255'],
            'publisher' => ['nullable', 'max:255'],
            'year' => ['nullable', 'digits:4'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'rack_location' => ['nullable', 'max:100'],
            'stock_total' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ]);
    }

    private function storeCoverImage(?UploadedFile $file): ?string
    {
        if (! $file) {
            return null;
        }

        return 'storage/'.$file->store('book-covers', 'public');
    }

    private function deleteUploadedCover(?string $coverImage): void
    {
        if (! $coverImage || ! str_starts_with($coverImage, 'storage/book-covers/')) {
            return;
        }

        Storage::disk('public')->delete(str_replace('storage/', '', $coverImage));
    }
}
