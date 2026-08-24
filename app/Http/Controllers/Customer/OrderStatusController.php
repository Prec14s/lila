<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderStatusController extends Controller
{
    public function index(Request $request): View
    {
        $order = null;
        $notFound = false;

        if ($request->filled('order_number')) {
            $order = Order::with(['items', 'verifier'])
                ->where('order_number', trim($request->input('order_number')))
                ->first();

            $notFound = ! $order;
        }

        return view('customer.status', compact('order', 'notFound'));
    }
}
