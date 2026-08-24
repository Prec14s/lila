<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with('items')->latest();

        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%'.$request->input('order_number').'%');
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->input('status'));
        } else {
            $query->where('payment_status', '!=', 'waiting_payment');
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('owner.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $business = BusinessSetting::instance();

        return view('owner.orders.receipt', compact('order', 'business'));
    }

    public function forwardKitchen(Order $order): RedirectResponse
    {
        $order->update(['forwarded_to_kitchen_at' => now()]);

        return back()->with('success', 'Pesanan diteruskan ke dapur.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        if ($order->payment_status !== 'rejected') {
            return back()->withErrors(['error' => 'Hanya pesanan yang ditolak yang dapat dihapus.']);
        }

        $orderNumber = $order->order_number;

        if ($order->payment_proof && \Illuminate\Support\Facades\Storage::disk('public')->exists($order->payment_proof)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($order->payment_proof);
        }

        $order->delete();

        \App\Models\ActivityLog::record(
            'Hapus Pesanan Ditolak',
            "Owner menghapus data pesanan yang ditolak: {$orderNumber}."
        );

        return back()->with('success', "Pesanan {$orderNumber} berhasil dihapus.");
    }
}
