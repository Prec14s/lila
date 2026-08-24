<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Category;

class MenuController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->with(['menus' => fn ($q) => $q->where('is_available', true)->orderBy('name')])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($c) => $c->menus->isNotEmpty())
            ->values();

        $business = BusinessSetting::instance();

        return view('customer.menu', compact('categories', 'business'));
    }
}
