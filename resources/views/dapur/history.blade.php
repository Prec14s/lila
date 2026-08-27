@extends('layouts.admin')

@section('content')
@php $title = 'Riwayat Waktu Pengerjaan (Dapur)'; @endphp

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-extrabold text-coffee-800 flex items-center gap-2">
            <span>⏱️ Riwayat Waktu Pengerjaan</span>
        </h1>
        <p class="text-xs text-coffee-500 mt-0.5">Catatan durasi memasak dari tombol 'Mulai Proses' hingga 'Tandai Selesai'</p>
    </div>

    {{-- Filter Tanggal --}}
    <form action="{{ route('dapur.history') }}" method="GET" class="flex items-center gap-2">
        <input type="date" name="date" value="{{ request('date') }}"
               class="text-xs rounded-xl border-coffee-200 bg-white text-coffee-800 font-semibold focus:ring-coffee-500 focus:border-coffee-500 px-3 py-2 shadow-xs">
        <button type="submit" class="btn-press bg-coffee-800 hover:bg-coffee-900 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs">
            🔍 Filter
        </button>
        @if(request('date'))
            <a href="{{ route('dapur.history') }}" class="btn-press bg-coffee-100 text-coffee-700 hover:bg-coffee-200 text-xs font-bold px-3 py-2 rounded-xl border border-coffee-200">
                🔄 Reset
            </a>
        @endif
    </form>
</div>

{{-- CARDS STATISTIK WAKTU --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-coffee-100 p-4 lg:p-5 shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg mb-2">
            ⏱️
        </div>
        <p class="text-xl lg:text-2xl font-extrabold text-coffee-800">{{ $stats['avg_duration'] }}</p>
        <p class="text-coffee-500 text-xs mt-0.5">Rata-rata Waktu Masak</p>
    </div>

    <div class="bg-white rounded-2xl border border-coffee-100 p-4 lg:p-5 shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg mb-2">
            ✅
        </div>
        <p class="text-xl lg:text-2xl font-extrabold text-coffee-800">{{ $stats['total_completed'] }}</p>
        <p class="text-coffee-500 text-xs mt-0.5">Total Pesanan Selesai</p>
    </div>

    <div class="bg-white rounded-2xl border border-coffee-100 p-4 lg:p-5 shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg mb-2">
            ⚡
        </div>
        <p class="text-xl lg:text-2xl font-extrabold text-coffee-800">{{ $stats['fastest_duration'] }}</p>
        <p class="text-coffee-500 text-xs mt-0.5">Durasi Tercepat</p>
    </div>

    <div class="bg-white rounded-2xl border border-coffee-100 p-4 lg:p-5 shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg mb-2">
            🐢
        </div>
        <p class="text-xl lg:text-2xl font-extrabold text-coffee-800">{{ $stats['slowest_duration'] }}</p>
        <p class="text-coffee-500 text-xs mt-0.5">Durasi Terlama</p>
    </div>
</div>

{{-- TABEL RIWAYAT WAKTU PENGERJAAN --}}
<div class="bg-white rounded-2xl border border-coffee-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-coffee-100 flex items-center justify-between">
        <h2 class="font-bold text-coffee-800 text-sm md:text-base">Daftar Pesanan Selesai & Durasi Pengerjaan</h2>
        @if(request('date'))
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-coffee-100 text-coffee-800 border border-coffee-200">
                Filter: {{ \Carbon\Carbon::parse(request('date'))->translatedFormat('d M Y') }}
            </span>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm whitespace-nowrap">
            <thead class="bg-coffee-50 text-coffee-500 text-xs uppercase font-bold whitespace-nowrap">
                <tr>
                    <th class="px-5 py-3.5 text-left whitespace-nowrap">No. Order</th>
                    <th class="px-5 py-3.5 text-left whitespace-nowrap">Pelanggan</th>
                    <th class="px-5 py-3.5 text-left whitespace-nowrap">Menu yang Dimasak</th>
                    <th class="px-5 py-3.5 text-left whitespace-nowrap">Mulai Masak</th>
                    <th class="px-5 py-3.5 text-left whitespace-nowrap">Selesai</th>
                    <th class="px-5 py-3.5 text-right whitespace-nowrap">Durasi Pengerjaan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-coffee-50 whitespace-nowrap">
                @forelse($orders as $order)
                    <tr class="hover:bg-coffee-50/50 transition">
                        <td class="px-5 py-3.5 font-bold text-coffee-800 whitespace-nowrap">{{ $order->order_number }}</td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="font-medium text-coffee-800">{{ $order->customer_name }}</span>
                            <span class="text-xs text-coffee-500 bg-coffee-100 px-1.5 py-0.5 rounded font-bold whitespace-nowrap inline-block ml-1">🪑 Meja {{ $order->table_number ?? '-' }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-coffee-700 whitespace-nowrap">
                            <div class="space-y-0.5 whitespace-nowrap">
                                @foreach($order->items as $item)
                                    <div><strong class="text-coffee-800">{{ $item->menu_name }}</strong> (x{{ $item->qty }})</div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-coffee-600 whitespace-nowrap">
                            {{ $order->kitchen_processing_started_at ? $order->kitchen_processing_started_at->format('H:i:s') : '-' }} WIB
                        </td>
                        <td class="px-5 py-3.5 text-xs text-coffee-600 whitespace-nowrap">
                            {{ $order->kitchen_completed_at ? $order->kitchen_completed_at->format('H:i:s') : '-' }} WIB
                        </td>
                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                            <span class="inline-block px-3 py-1 rounded-xl text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 whitespace-nowrap">
                                ⏱️ {{ $order->kitchenDurationFormatted() }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-coffee-400 whitespace-nowrap">
                            Belum ada riwayat pesanan selesai dengan catatan waktu pengerjaan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-coffee-100">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
