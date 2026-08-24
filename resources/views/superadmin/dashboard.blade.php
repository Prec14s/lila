@extends('layouts.admin')

@section('content')
@php $title = 'Dashboard Super Admin'; @endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
        $cards = [
            ['label' => 'Akun Owner', 'value' => $stats['total_owner'], 'icon' => '👑'],
            ['label' => 'Akun Dapur', 'value' => $stats['total_dapur'], 'icon' => '🍳'],
            ['label' => 'Total Pesanan', 'value' => $stats['total_orders'], 'icon' => '🧾'],
            ['label' => 'Total Omzet', 'value' => 'Rp '.number_format($stats['revenue_total'], 0, ',', '.'), 'icon' => '💰'],
        ];
    @endphp
    @foreach($cards as $card)
        <div class="card-pop bg-white rounded-2xl border border-coffee-100 p-5 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-coffee-700 flex items-center justify-center text-lg mb-3">{{ $card['icon'] }}</div>
            <p class="text-2xl font-extrabold text-coffee-800">{{ $card['value'] }}</p>
            <p class="text-coffee-500 text-xs mt-1">{{ $card['label'] }}</p>
        </div>
    @endforeach
</div>

<div class="bg-white rounded-2xl border border-coffee-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-coffee-100">
        <h2 class="font-bold text-coffee-800">Transaksi Terbaru</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-coffee-50 text-coffee-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">No. Order</th>
                <th class="px-5 py-3 text-left">Cara Bayar</th>
                <th class="px-5 py-3 text-left">Status</th>
                <th class="px-5 py-3 text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-coffee-50">
            @forelse($recentOrders as $order)
                <tr class="hover:bg-coffee-50/60 transition">
                    <td class="px-5 py-3 font-semibold text-coffee-800">{{ $order->order_number }}</td>
                    <td class="px-5 py-3"><span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $order->paymentCategoryColor() }}">{{ $order->paymentCategoryIcon() }} {{ $order->paymentCategoryLabel() }}</span></td>
                    <td class="px-5 py-3"><span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $order->paymentStatusColor() }}">{{ $order->paymentStatusLabel() }}</span></td>
                    <td class="px-5 py-3 text-right font-semibold text-coffee-800">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-10 text-center text-coffee-400">Belum ada transaksi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
