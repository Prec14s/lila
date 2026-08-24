<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentSettingController extends Controller
{
    public function index(): View
    {
        $paymentSettings = PaymentSetting::latest()->get();

        return view('owner.payment-settings.index', compact('paymentSettings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:qris,bank_transfer,cash'],
            'label' => ['nullable', 'string', 'max:50'],
            'qris_image' => ['nullable', 'required_if:type,qris', 'image', 'max:2048'],
            'bank_name' => ['nullable', 'required_if:type,bank_transfer', 'string', 'max:50'],
            'account_number' => ['nullable', 'required_if:type,bank_transfer', 'string', 'max:50'],
            'account_holder' => ['nullable', 'required_if:type,bank_transfer', 'string', 'max:100'],
            'instruction' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('qris_image')) {
            $validated['qris_image'] = $request->file('qris_image')->store('qris', 'public');
        }

        $validated['is_active'] = true;

        PaymentSetting::create($validated);

        return back()->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function toggle(PaymentSetting $paymentSetting): RedirectResponse
    {
        $paymentSetting->update(['is_active' => ! $paymentSetting->is_active]);

        return back()->with('success', 'Status metode pembayaran diperbarui.');
    }

    public function destroy(PaymentSetting $paymentSetting): RedirectResponse
    {
        $paymentSetting->delete();

        return back()->with('success', 'Metode pembayaran berhasil dihapus.');
    }
}
