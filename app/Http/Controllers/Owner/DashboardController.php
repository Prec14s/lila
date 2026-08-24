<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'waiting_verification' => Order::where('payment_status', 'waiting_verification')->count(),
            'approved_today' => Order::where('payment_status', 'approved')->whereDate('verified_at', today())->count(),
            'processing' => Order::where('order_status', 'processing')->count(),
            'revenue_today' => Order::where('payment_status', 'approved')->whereDate('verified_at', today())->sum('total'),
        ];

        $latestOrders = Order::where('payment_status', '!=', 'waiting_payment')->latest()->take(8)->get();

        return view('owner.dashboard', compact('stats', 'latestOrders'));
    }
}
