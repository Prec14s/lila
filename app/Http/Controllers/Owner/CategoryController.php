<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('menus')->orderBy('sort_order')->get();

        return view('owner.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'icon' => ['nullable', 'string', 'max:10'],
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.Str::random(4),
            'icon' => $validated['icon'] ?? '🍽️',
            'sort_order' => Category::max('sort_order') + 1,
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'icon' => ['nullable', 'string', 'max:10'],
        ]);

        $category->update($validated);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->menus()->exists()) {
            return back()->withErrors(['category' => 'Kategori tidak dapat dihapus karena masih memiliki menu.']);
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
