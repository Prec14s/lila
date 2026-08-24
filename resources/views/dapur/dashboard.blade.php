@extends('layouts.admin')

@section('content')
@php $title = 'Pesanan Masuk (Dapur)'; @endphp

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <div class="bg-white rounded-2xl border border-coffee-100 shadow-sm px-5 py-3">
            <p class="text-xs text-coffee-400">Antrean Dapur</p>
            <p class="text-xl font-extrabold text-amber-600">{{ $orders->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-coffee-100 shadow-sm px-5 py-3">
            <p class="text-xs text-coffee-400">Selesai Hari Ini</p>
            <p class="text-xl font-extrabold text-emerald-600">{{ $completedToday }}</p>
        </div>
    </div>

    {{-- AUTO REFRESH WIDGET WITH COUNTDOWN --}}
    <div class="flex items-center gap-2.5 bg-white px-4 py-2.5 rounded-2xl border border-coffee-200/80 shadow-sm text-xs font-semibold text-coffee-700"
         x-data="{ countdown: 8, timer: null }"
         x-init="timer = setInterval(() => { countdown--; if(countdown <= 0) { window.location.reload(); } }, 1000)">
        <span class="relative flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
        </span>
        <span>Auto Update: Memeriksa order baru dalam <strong class="font-extrabold text-coffee-800" x-text="countdown">8</strong>d</span>
        <button @click="window.location.reload()" class="btn-press ml-1 text-[11px] bg-coffee-100 hover:bg-coffee-200 text-coffee-800 font-bold px-2.5 py-1 rounded-lg transition shadow-xs">
            🔄 Refresh Realtime
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($orders as $order)
        <div class="card-pop bg-white rounded-2xl border border-coffee-100 shadow-sm p-4 sm:p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="font-bold text-coffee-800">{{ $order->order_number }}</p>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $order->orderStatusColor() }}">{{ $order->orderStatusLabel() }}</span>
                </div>
                <div class="flex items-center justify-between mb-3 bg-coffee-50/70 p-2.5 rounded-xl border border-coffee-100/50">
                    <p class="text-sm text-coffee-700 font-semibold truncate">👤 {{ $order->customer_name }}</p>
                    <span class="text-xs font-extrabold px-2.5 py-1 rounded-lg bg-coffee-800 text-white shadow-sm shrink-0">🪑 {{ $order->table_number ?? '-' }}</span>
                </div>

                <div class="bg-coffee-50 rounded-xl p-3 space-y-1.5 mb-4">
                    @foreach($order->items as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-coffee-800 font-medium">{{ $item->menu_name }}</span>
                            <span class="font-extrabold text-coffee-900 bg-white px-2 py-0.5 rounded shadow-xs text-xs">x{{ $item->qty }}</span>
                        </div>
                    @endforeach
                </div>
                @if($order->note)
                    <p class="text-xs text-amber-800 bg-amber-50 p-2.5 rounded-xl border border-amber-200/60 italic mb-4">📝 Note: {{ $order->note }}</p>
                @endif
            </div>

            @if($order->order_status === 'waiting')
                <form action="{{ route('dapur.orders.process', $order) }}" method="POST">
                    @csrf
                    <button class="btn-press w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-extrabold py-3 rounded-xl text-sm sm:text-base transition shadow-md">
                        🍳 Mulai Proses
                    </button>
                </form>
            @elseif($order->order_status === 'processing')
                <form action="{{ route('dapur.orders.complete', $order) }}" method="POST">
                    @csrf
                    <button class="btn-press w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold py-3 rounded-xl text-sm sm:text-base transition shadow-md">
                        ✅ Tandai Selesai
                    </button>
                </form>
            @endif
        </div>
    @empty
        <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-coffee-100">
            <span class="text-4xl">🍽️</span>
            <p class="text-coffee-400 mt-2 text-sm">Belum ada pesanan yang masuk.</p>
            <p class="text-coffee-400 text-xs mt-1">Pesanan yang disetujui (ACC) oleh Owner akan otomatis muncul di sini.</p>
        </div>
    @endforelse
</div>
@endsection
