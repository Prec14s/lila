<?php

namespace App\Http\Controllers\Dapur;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with('items')
            ->where('payment_status', 'approved')
            ->whereIn('order_status', ['waiting', 'processing'])
            ->oldest()
            ->get();

        $completedToday = Order::where('order_status', 'completed')
            ->whereDate('updated_at', today())
            ->count();

        return view('dapur.dashboard', compact('orders', 'completedToday'));
    }

    public function process(Order $order): RedirectResponse
    {
        $order->update(['order_status' => 'processing']);

        \App\Models\ActivityLog::record(
            'Proses Dapur',
            "Staf Dapur mulai memproses pesanan {$order->order_number} (Meja: {$order->table_number})."
        );

        return back()->with('success', "Pesanan {$order->order_number} sedang diproses.");
    }

    public function complete(Order $order): RedirectResponse
    {
        $order->update(['order_status' => 'completed']);

        \App\Models\ActivityLog::record(
            'Selesai Dapur',
            "Staf Dapur menyelesaikan pesanan {$order->order_number} (Meja: {$order->table_number})."
        );

        return back()->with('success', "Pesanan {$order->order_number} selesai.");
    }
}
