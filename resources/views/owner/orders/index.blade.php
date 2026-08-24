@extends('layouts.admin')

@section('content')
@php $title = 'Riwayat & Cek Order'; @endphp

<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <input name="order_number" value="{{ request('order_number') }}" type="text" placeholder="🔎 Cari Nomor Order..."
           class="flex-1 min-w-[200px] border border-coffee-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
    <select name="status" class="border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
        <option value="">Semua Status Bayar</option>
        <option value="waiting_payment" @selected(request('status')==='waiting_payment')>Menunggu Pembayaran</option>
        <option value="waiting_verification" @selected(request('status')==='waiting_verification')>Menunggu Verifikasi</option>
        <option value="approved" @selected(request('status')==='approved')>Disetujui</option>
        <option value="rejected" @selected(request('status')==='rejected')>Ditolak</option>
    </select>
    <button class="btn-press bg-coffee-800 hover:bg-coffee-900 text-white font-semibold px-5 rounded-xl text-sm">Cari</button>
</form>

<div class="bg-white rounded-2xl border border-coffee-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-coffee-50 text-coffee-500 text-xs uppercase font-bold">
                <tr>
                    <th class="px-5 py-3.5 text-left">No. Order</th>
                    <th class="px-5 py-3.5 text-left">Waktu Order</th>
                    <th class="px-5 py-3.5 text-left">Pelanggan</th>
                    <th class="px-5 py-3.5 text-left">Cara Bayar</th>
                    <th class="px-5 py-3.5 text-left">Status Bayar</th>
                    <th class="px-5 py-3.5 text-left">Status Pesanan</th>
                    <th class="px-5 py-3.5 text-right">Total</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-coffee-50">
                @forelse($orders as $order)
                    <tr class="hover:bg-coffee-50/60 transition">
                        <td class="px-5 py-3 font-bold text-coffee-800">{{ $order->order_number }}</td>
                        <td class="px-5 py-3 text-xs text-coffee-600 whitespace-nowrap">
                            {{ $order->created_at->translatedFormat('d M Y') }}, <span class="font-bold text-coffee-800">{{ $order->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-5 py-3 text-coffee-600">{{ $order->customer_name }} <span class="text-xs font-bold text-coffee-800 bg-coffee-100 px-1.5 py-0.5 rounded">🪑 {{ $order->table_number ?? '-' }}</span></td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $order->paymentCategoryColor() }}">
                                {{ $order->paymentCategoryIcon() }} {{ $order->paymentCategoryLabel() }}
                            </span>
                        </td>
                        <td class="px-5 py-3"><span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $order->paymentStatusColor() }}">{{ $order->paymentStatusLabel() }}</span></td>
                        <td class="px-5 py-3"><span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $order->orderStatusColor() }}">{{ $order->orderStatusLabel() }}</span></td>
                        <td class="px-5 py-3 text-right font-bold text-coffee-800">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($order->payment_status === 'rejected')
                                    <form action="{{ route('owner.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ditolak ini?')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-press text-xs font-semibold px-2.5 py-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition border border-red-200">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('owner.orders.receipt', $order) }}" target="_blank" class="btn-press text-xs font-semibold px-3 py-1.5 rounded-lg border border-coffee-200 text-coffee-600 hover:bg-coffee-50">
                                        🧾 Struk
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-5 py-10 text-center text-coffee-400">Tidak ada data pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5">{{ $orders->links() }}</div>
@endsection
