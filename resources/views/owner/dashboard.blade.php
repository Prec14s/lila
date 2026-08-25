@extends('layouts.admin')

@section('content')
@php $title = 'Dashboard Owner'; @endphp

{{-- BANNER HEADER WIDGET --}}
<div class="mb-6 bg-white rounded-2xl border border-coffee-100 p-4 lg:p-5 shadow-sm">
    <h2 class="font-bold text-coffee-800 text-lg">Ringkasan Operasional Warkop</h2>
</div>

{{-- FILTER PERIODE WIDGET --}}
<div class="mb-6 bg-white rounded-2xl border border-coffee-100 p-4 lg:p-5 shadow-sm">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-coffee-800 text-sm md:text-base flex items-center gap-2">
                <span>🗓️ Filter Periode Laporan</span>
                @if($day || $month || $year)
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">Filter Aktif</span>
                @endif
            </h3>
            <p class="text-xs text-coffee-500 mt-0.5">Filter data berdasarkan tanggal, bulan, dan tahun operasional</p>
        </div>

        <form action="{{ route('owner.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-2">
            {{-- Tanggal --}}
            <div class="w-28">
                <select name="day" class="w-full text-xs rounded-xl border-coffee-200 bg-coffee-50/50 text-coffee-800 font-semibold focus:ring-coffee-500 focus:border-coffee-500 py-2">
                    <option value="">Semua Tgl</option>
                    @for($d = 1; $d <= 31; $d++)
                        @php $dVal = str_pad($d, 2, '0', STR_PAD_LEFT); @endphp
                        <option value="{{ $dVal }}" {{ (string)$day === (string)$dVal ? 'selected' : '' }}>Tanggal {{ $dVal }}</option>
                    @endfor
                </select>
            </div>

            {{-- Bulan --}}
            <div class="w-36">
                @php
                    $months = [
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ];
                @endphp
                <select name="month" class="w-full text-xs rounded-xl border-coffee-200 bg-coffee-50/50 text-coffee-800 font-semibold focus:ring-coffee-500 focus:border-coffee-500 py-2">
                    <option value="">Semua Bulan</option>
                    @foreach($months as $mNum => $mName)
                        <option value="{{ $mNum }}" {{ (string)$month === (string)$mNum ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tahun --}}
            <div class="w-28">
                <select name="year" class="w-full text-xs rounded-xl border-coffee-200 bg-coffee-50/50 text-coffee-800 font-semibold focus:ring-coffee-500 focus:border-coffee-500 py-2">
                    <option value="">Semua Thn</option>
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ (string)$year === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Button --}}
            <button type="submit" class="btn-press bg-coffee-700 hover:bg-coffee-800 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition shadow-xs flex items-center gap-1">
                🔍 Filter
            </button>

            {{-- Reset Button --}}
            @if($day || $month || $year)
                <a href="{{ route('owner.dashboard') }}" class="btn-press bg-coffee-100 hover:bg-coffee-200 text-coffee-700 text-xs font-bold px-3 py-2 rounded-xl transition border border-coffee-200">
                    🔄 Reset
                </a>
            @endif

            {{-- Cetak Laporan Button --}}
            <button type="submit" formaction="{{ route('owner.dashboard.report') }}" formtarget="_blank" class="btn-press bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition shadow-xs flex items-center gap-1.5">
                🖨️ Cetak Laporan
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
        $periodLabel = ($day || $month || $year) ? 'Periode Filter' : 'Hari Ini';
        $cards = [
            ['label' => 'Menunggu Verifikasi', 'value' => $stats['waiting_verification'], 'icon' => '⏳', 'color' => 'from-amber-400 to-amber-500'],
            ['label' => 'Disetujui (' . $periodLabel . ')', 'value' => $stats['approved_today'], 'icon' => '✅', 'color' => 'from-emerald-400 to-emerald-500'],
            ['label' => 'Sedang Diproses Dapur', 'value' => $stats['processing'], 'icon' => '🍳', 'color' => 'from-blue-400 to-blue-500'],
            ['label' => 'Omzet (' . $periodLabel . ')', 'value' => 'Rp '.number_format($stats['revenue_today'], 0, ',', '.'), 'icon' => '💰', 'color' => 'from-coffee-500 to-coffee-700'],
        ];
    @endphp
    @foreach($cards as $i => $card)
        <div class="card-pop bg-white rounded-2xl border border-coffee-100 p-4 lg:p-5 shadow-sm" style="animation-delay: {{ $i * 60 }}ms">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $card['color'] }} flex items-center justify-center text-lg mb-3">
                {{ $card['icon'] }}
            </div>
            <p class="text-xl lg:text-2xl font-extrabold text-coffee-800">{{ $card['value'] }}</p>
            <p class="text-coffee-500 text-xs mt-1">{{ $card['label'] }}</p>
        </div>
    @endforeach
</div>

<div class="bg-white rounded-2xl border border-coffee-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-coffee-100 flex items-center justify-between">
        <h2 class="font-bold text-coffee-800">Pesanan Terbaru</h2>
        <a href="{{ route('owner.orders.index') }}" class="text-xs font-semibold text-coffee-500 hover:text-coffee-700">Lihat Semua →</a>
    </div>
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
                @forelse($latestOrders as $order)
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
                            @if($order->payment_status === 'rejected')
                                <form action="{{ route('owner.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ditolak ini?')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-press text-xs font-semibold px-2.5 py-1 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition border border-red-200">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('owner.orders.receipt', $order) }}" target="_blank" class="btn-press text-xs font-semibold px-2.5 py-1 rounded-lg border border-coffee-200 text-coffee-600 hover:bg-coffee-50">
                                    🧾 Struk
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-5 py-10 text-center text-coffee-400">Belum ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
