<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_owner' => User::where('role', 'owner')->count(),
            'total_dapur' => User::where('role', 'dapur')->count(),
            'total_orders' => Order::count(),
            'revenue_total' => Order::where('payment_status', 'approved')->sum('total'),
        ];

        $recentOrders = Order::latest()->take(10)->get();

        return view('superadmin.dashboard', compact('stats', 'recentOrders'));
    }
}
