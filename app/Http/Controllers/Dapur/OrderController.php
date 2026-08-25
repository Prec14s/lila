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
            ->orderBy('verified_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $completedToday = Order::where('order_status', 'completed')
            ->whereDate('updated_at', today())
            ->count();

        return view('dapur.dashboard', compact('orders', 'completedToday'));
    }

    public function process(Order $order): RedirectResponse
    {
        $order->update([
            'order_status' => 'processing',
            'kitchen_processing_started_at' => $order->kitchen_processing_started_at ?? now(),
        ]);

        \App\Models\ActivityLog::record(
            'Proses Dapur',
            "Staf Dapur mulai memproses pesanan {$order->order_number} (Meja: {$order->table_number})."
        );

        return back()->with('success', "Pesanan {$order->order_number} mulai diproses.");
    }

    public function complete(Order $order): RedirectResponse
    {
        $order->update([
            'order_status' => 'completed',
            'kitchen_completed_at' => now(),
        ]);

        \App\Models\ActivityLog::record(
            'Selesai Dapur',
            "Staf Dapur menyelesaikan pesanan {$order->order_number} (Meja: {$order->table_number})."
        );

        return back()->with('success', "Pesanan {$order->order_number} selesai.");
    }

    public function history(\Illuminate\Http\Request $request): View
    {
        $query = Order::with('items')
            ->where('order_status', 'completed')
            ->whereNotNull('kitchen_processing_started_at')
            ->whereNotNull('kitchen_completed_at');

        if ($request->filled('date')) {
            $query->whereDate('kitchen_completed_at', $request->input('date'));
        }

        $orders = $query->latest('kitchen_completed_at')->paginate(20)->withQueryString();

        $allCompleted = Order::where('order_status', 'completed')
            ->whereNotNull('kitchen_processing_started_at')
            ->whereNotNull('kitchen_completed_at')
            ->get();

        $durations = $allCompleted->map(fn ($o) => $o->kitchenDurationInSeconds())->filter(fn ($d) => $d !== null);

        $avgSeconds = $durations->count() > 0 ? (int) round($durations->avg()) : 0;
        $minSeconds = $durations->count() > 0 ? (int) $durations->min() : 0;
        $maxSeconds = $durations->count() > 0 ? (int) $durations->max() : 0;

        $formatSec = function (int $sec): string {
            if ($sec <= 0) return '-';
            if ($sec < 60) return "{$sec} dtk";
            $m = floor($sec / 60);
            $s = $sec % 60;
            return $s == 0 ? "{$m} mnt" : "{$m}m {$s}s";
        };

        $stats = [
            'total_completed' => $allCompleted->count(),
            'avg_duration' => $formatSec($avgSeconds),
            'fastest_duration' => $formatSec($minSeconds),
            'slowest_duration' => $formatSec($maxSeconds),
        ];

        return view('dapur.history', compact('orders', 'stats'));
    }
}
