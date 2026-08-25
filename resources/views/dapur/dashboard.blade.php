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
    @forelse($orders as $index => $order)
        <div class="card-pop bg-white rounded-2xl border {{ $loop->first ? 'border-amber-200 bg-amber-50/20' : 'border-coffee-100' }} shadow-sm p-4 sm:p-5 flex flex-col justify-between relative overflow-hidden">
            @if($loop->first)
                <div class="absolute top-0 right-0 bg-amber-100 text-amber-800 border-l border-b border-amber-200 text-[10px] font-bold px-2.5 py-0.5 rounded-bl-xl shadow-xs">
                    📌 Urutan Pertama
                </div>
            @endif

            <div>
                {{-- NOMOR ANTREAN & NO. ORDER --}}
                <div class="flex items-center justify-between mb-3 border-b border-coffee-100 pb-2.5">
                    <div class="flex items-center gap-2">
                        <div class="flex flex-col items-center justify-center min-w-[42px] px-2.5 py-1 rounded-xl font-black shrink-0 {{ $loop->first ? 'bg-amber-700 text-white' : 'bg-coffee-800 text-white' }}">
                            <span class="text-[9px] uppercase font-bold tracking-tighter opacity-80 leading-none">Antrean</span>
                            <span class="text-base leading-none mt-0.5">#{{ $loop->iteration }}</span>
                        </div>
                        <div>
                            <p class="font-extrabold text-coffee-800 text-sm leading-tight">{{ $order->order_number }}</p>
                            <p class="text-[10px] text-coffee-500 font-medium">ACC: {{ $order->verified_at ? $order->verified_at->format('H:i') : $order->created_at->format('H:i') }} WIB</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $order->orderStatusColor() }} shrink-0">
                        {{ $order->orderStatusLabel() }}
                    </span>
                </div>

                <div class="flex items-center justify-between mb-3 bg-coffee-50/70 p-2.5 rounded-xl border border-coffee-100/50">
                    <p class="text-sm text-coffee-700 font-semibold truncate">👤 {{ $order->customer_name }}</p>
                    <span class="text-xs font-extrabold px-2.5 py-1 rounded-lg bg-coffee-800 text-white shadow-sm shrink-0">🪑 {{ $order->table_number ?? '-' }}</span>
                </div>

                @if($order->order_status === 'processing' && $order->kitchen_processing_started_at)
                    <div class="mb-3 bg-blue-50 border border-blue-200/80 rounded-xl p-2.5 text-center"
                         x-data="{
                             startTime: {{ $order->kitchen_processing_started_at->timestamp * 1000 }},
                             elapsedStr: '0m 00s',
                             updateTimer() {
                                 const now = Date.now();
                                 const diffSec = Math.max(0, Math.floor((now - this.startTime) / 1000));
                                 const m = Math.floor(diffSec / 60);
                                 const s = diffSec % 60;
                                 this.elapsedStr = `${m}m ${String(s).padStart(2, '0')}s`;
                             }
                         }"
                         x-init="updateTimer(); setInterval(() => updateTimer(), 1000)">
                        <p class="text-[11px] font-bold text-blue-800 flex items-center justify-center gap-1.5">
                            <span>⏱️ Waktu Pengerjaan Berjalan:</span>
                            <span class="font-extrabold text-blue-900 bg-white px-2 py-0.5 rounded border border-blue-200 shadow-xs font-mono" x-text="elapsedStr">0m 00s</span>
                        </p>
                    </div>
                @endif

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
                        🍳 Mulai Proses (Antrean #{{ $loop->iteration }})
                    </button>
                </form>
            @elseif($order->order_status === 'processing')
                <form action="{{ route('dapur.orders.complete', $order) }}" method="POST">
                    @csrf
                    <button class="btn-press w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold py-3 rounded-xl text-sm sm:text-base transition shadow-md">
                        ✅ Tandai Selesai (Antrean #{{ $loop->iteration }})
                    </button>
                </form>
            @endif
        </div>
    @empty
        <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-coffee-100">
            <span class="text-4xl">🍽️</span>
            <p class="text-coffee-400 mt-2 text-sm">Belum ada pesanan yang masuk.</p>
            <p class="text-coffee-400 text-xs mt-1">Pesanan yang disetujui (ACC) oleh Owner akan otomatis muncul di sini sesuai urutan waktu ACC.</p>
        </div>
    @endforelse
</div>
@endsection
