<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $menus = Menu::with('category')->latest()->get();
        $categories = Category::orderBy('sort_order')->get();

        return view('owner.menus.index', compact('menus', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('menus', 'public');
        }

        Menu::create($validated);

        return back()->with('success', 'Menu berhasil ditambahkan.');
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $validated['is_available'] = $request->boolean('is_available');

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('menus', 'public');
        }

        $menu->update($validated);

        return back()->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus.');
    }
}
