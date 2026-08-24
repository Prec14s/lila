<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $business = BusinessSetting::instance();

        return view('owner.settings.whatsapp', compact('business'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'wa_owner_number' => ['required', 'string', 'max:20'],
            'wa_dapur_number' => ['required', 'string', 'max:20'],
        ]);

        BusinessSetting::instance()->update($validated);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
