<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function index(): View
    {
        $orders = Order::with('items')
            ->where('payment_status', 'waiting_verification')
            ->oldest()
            ->get();

        return view('owner.verification.index', compact('orders'));
    }

    public function approve(Order $order): RedirectResponse
    {
        $order->update([
            'payment_status' => 'approved',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        $business = BusinessSetting::instance();
        $lines = [];
        $lines[] = "Nama: {$order->customer_name}";
        $lines[] = "No. Meja: ".($order->table_number ?? '-');
        $lines[] = '';
        $lines[] = 'Pesanan:';
        foreach ($order->items as $i => $item) {
            $lines[] = ($i + 1).". {$item->menu_name} x{$item->qty}";
        }
        if ($order->note) {
            $lines[] = "Catatan: {$order->note}";
        }

        $waText = rawurlencode(implode("\n", $lines));
        $waDapurLink = 'https://wa.me/'.preg_replace('/[^0-9]/', '', (string) $business->wa_dapur_number).'?text='.$waText;

        \App\Models\ActivityLog::record(
            'ACC Pembayaran',
            "Owner menyetujui (ACC) pembayaran untuk order {$order->order_number} atas nama {$order->customer_name}."
        );

        return back()->with('success', 'Pesanan disetujui.')->with('wa_dapur_link', $waDapurLink);
    }

    public function reject(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $reason = $validated['rejection_reason'] ?? 'Bukti pembayaran tidak sesuai / belum valid.';

        $order->update([
            'payment_status' => 'rejected',
            'rejection_reason' => $reason,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        \App\Models\ActivityLog::record(
            'Tolak Pembayaran',
            "Owner menolak pembayaran order {$order->order_number} ({$order->customer_name}). Alasan: {$reason}"
        );

        return back()->with('success', 'Pembayaran ditolak.');
    }
}
