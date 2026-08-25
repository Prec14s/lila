<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $day = $request->input('day');
        $month = $request->input('month');
        $year = $request->input('year');

        $applyFilter = function ($query) use ($day, $month, $year) {
            if ($day) {
                $query->whereDay('created_at', $day);
            }
            if ($month) {
                $query->whereMonth('created_at', $month);
            }
            if ($year) {
                $query->whereYear('created_at', $year);
            }
            return $query;
        };

        $waitingVerificationQuery = Order::where('payment_status', 'waiting_verification');
        $applyFilter($waitingVerificationQuery);

        $approvedQuery = Order::where('payment_status', 'approved');
        $applyFilter($approvedQuery);

        $processingQuery = Order::where('order_status', 'processing');
        $applyFilter($processingQuery);

        $revenueQuery = Order::where('payment_status', 'approved');
        $applyFilter($revenueQuery);

        $stats = [
            'waiting_verification' => $waitingVerificationQuery->count(),
            'approved_today' => $approvedQuery->count(),
            'processing' => $processingQuery->count(),
            'revenue_today' => $revenueQuery->sum('total'),
        ];

        $ordersQuery = Order::where('payment_status', '!=', 'waiting_payment');
        $applyFilter($ordersQuery);
        $latestOrders = $ordersQuery->latest()->get();

        $availableYears = Order::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter()
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        return view('owner.dashboard', compact('stats', 'latestOrders', 'day', 'month', 'year', 'availableYears'));
    }

    public function printReport(Request $request): View
    {
        $day = $request->input('day');
        $month = $request->input('month');
        $year = $request->input('year');

        $query = Order::with('items')->where('payment_status', '!=', 'waiting_payment');

        if ($day) {
            $query->whereDay('created_at', $day);
        }
        if ($month) {
            $query->whereMonth('created_at', $month);
        }
        if ($year) {
            $query->whereYear('created_at', $year);
        }

        $orders = $query->latest()->get();

        $business = BusinessSetting::instance();

        $stats = [
            'total_orders' => $orders->count(),
            'approved_orders' => $orders->where('payment_status', 'approved')->count(),
            'waiting_orders' => $orders->where('payment_status', 'waiting_verification')->count(),
            'rejected_orders' => $orders->where('payment_status', 'rejected')->count(),
            'total_revenue' => $orders->where('payment_status', 'approved')->sum('total'),
            'cash_revenue' => $orders->where('payment_status', 'approved')->where('payment_category', 'cash')->sum('total'),
            'cashless_revenue' => $orders->where('payment_status', 'approved')->where('payment_category', '!=', 'cash')->sum('total'),
        ];

        return view('owner.reports.dashboard_print', compact('orders', 'business', 'stats', 'day', 'month', 'year'));
    }
}

