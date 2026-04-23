<?php

namespace App\Http\Controllers;

use App\Models\ClassCategory;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClassCategoryController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->isAdmin(), 403);

        $search = $request->string('search');

        $classCategories = ClassCategory::withCount('users')
            ->when($search->isNotEmpty(), fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('class-categories.index', compact('classCategories'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->isAdmin(), 403);

        return view('class-categories.create', ['classCategory' => new ClassCategory()]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $this->validateClassCategory($request);
        $classCategory = ClassCategory::create([
            ...$validated,
            'slug' => Str::slug($validated['name']),
        ]);

        ActivityLogger::log(
            $request->user(),
            'class-category.create',
            'Menambahkan kategori kelas: '.$classCategory->name,
            $classCategory,
            ['kategori_kelas' => $classCategory->name],
        );

        return redirect()->route('class-categories.index')->with('success', 'Kategori kelas berhasil ditambahkan.');
    }

    public function edit(Request $request, ClassCategory $classCategory): View
    {
        abort_unless($request->user()->isAdmin(), 403);

        return view('class-categories.edit', compact('classCategory'));
    }

    public function update(Request $request, ClassCategory $classCategory): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $this->validateClassCategory($request, $classCategory->id);
        $classCategory->update([
            ...$validated,
            'slug' => Str::slug($validated['name']),
        ]);

        $classCategory->users()->update(['class_name' => $classCategory->name]);

        ActivityLogger::log(
            $request->user(),
            'class-category.update',
            'Memperbarui kategori kelas: '.$classCategory->name,
            $classCategory,
            ['kategori_kelas' => $classCategory->name],
        );

        return redirect()->route('class-categories.index')->with('success', 'Kategori kelas berhasil diperbarui.');
    }

    public function destroy(Request $request, ClassCategory $classCategory): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        if ($classCategory->users()->exists()) {
            return back()->with('error', 'Kategori kelas masih digunakan anggota dan tidak bisa dihapus.');
        }

        ActivityLogger::log(
            $request->user(),
            'class-category.delete',
            'Menghapus kategori kelas: '.$classCategory->name,
            $classCategory,
            ['kategori_kelas' => $classCategory->name],
        );

        $classCategory->delete();

        return redirect()->route('class-categories.index')->with('success', 'Kategori kelas berhasil dihapus.');
    }

    private function validateClassCategory(Request $request, ?int $categoryId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('class_categories', 'name')->ignore($categoryId)],
        ]);
    }
}
