@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-4 py-10 sm:py-14">
    <div class="text-center mb-6">
        <span class="text-4xl">🔎</span>
        <h1 class="text-xl font-extrabold text-coffee-800 mt-2">Cek Status Pesanan</h1>
        <p class="text-coffee-500 text-sm">Masukkan Nomor Order yang kamu terima setelah checkout.</p>
    </div>

    <form action="{{ route('order.status') }}" method="GET" class="flex gap-2 mb-6">
        <input name="order_number" value="{{ request('order_number') }}" type="text" placeholder="WS-20260824-0001" required
               class="flex-1 border border-coffee-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
        <button class="btn-press bg-coffee-800 hover:bg-coffee-900 text-white font-bold px-5 rounded-xl transition">Cari</button>
    </form>

    @if(session('error'))
        <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs p-3.5 text-center font-medium">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    @if($notFound)
        <div class="text-center py-10 animate-popin">
            <span class="text-4xl">🙁</span>
            <p class="text-coffee-500 mt-2 text-sm">Nomor order tidak ditemukan. Periksa kembali penulisannya.</p>
        </div>
    @endif

    @if($order)
        <div class="bg-white rounded-2xl shadow-sm border border-coffee-100 p-5 card-hover animate-popin">
            <div class="flex justify-between items-center mb-3" x-data="{
                copied: false,
                copyText(text) {
                    try {
                        const input = document.createElement('textarea');
                        input.value = text;
                        input.style.position = 'fixed';
                        input.style.left = '-9999px';
                        document.body.appendChild(input);
                        input.focus();
                        input.select();
                        document.execCommand('copy');
                        document.body.removeChild(input);
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    } catch (e) {
                        alert('Nomor Order: ' + text);
                    }
                }
            }">
                <span class="text-coffee-500 text-xs uppercase">No. Order</span>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-coffee-800 font-mono select-all bg-coffee-50 px-2 py-0.5 rounded border border-coffee-200/60">{{ $order->order_number }}</span>
                    <button @click="copyText('{{ trim($order->order_number) }}')"
                            type="button"
                            class="btn-press text-[11px] font-bold px-2.5 py-1 rounded-lg transition-all">
                        <span x-show="!copied" class="text-coffee-700 bg-coffee-100 px-2.5 py-1 rounded-lg hover:bg-coffee-200">📋 Salin</span>
                        <span x-show="copied" x-cloak class="bg-emerald-600 text-white px-2.5 py-1 rounded-lg shadow-xs">✅ Tersalin!</span>
                    </button>
                </div>
            </div>

            <div class="flex gap-2 mb-4 flex-wrap">
                <span class="text-xs font-semibold px-3 py-1 rounded-full whitespace-nowrap {{ $order->paymentStatusColor() }}">
                    💳 {{ $order->paymentStatusLabel() }}
                </span>
                <span class="text-xs font-semibold px-3 py-1 rounded-full whitespace-nowrap {{ $order->orderStatusColor() }}">
                    🍽️ {{ $order->orderStatusLabel() }}
                </span>
                <span class="text-xs font-semibold px-3 py-1 rounded-full whitespace-nowrap {{ $order->paymentCategoryColor() }}">
                    {{ $order->paymentCategoryIcon() }} {{ $order->paymentCategoryLabel() }}
                </span>
            </div>

            <p class="text-sm text-coffee-600 mb-1">Atas nama: <span class="font-semibold text-coffee-800">{{ $order->customer_name }}</span> · No. Meja: <span class="font-bold text-coffee-800 bg-coffee-100 px-2 py-0.5 rounded">🪑 {{ $order->table_number ?? '-' }}</span></p>

            <div class="space-y-1.5 mt-3">
                @foreach($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-coffee-700">{{ $item->menu_name }} <span class="text-coffee-400">x{{ $item->qty }}</span></span>
                        <span class="font-medium text-coffee-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-dashed border-coffee-200 mt-3 pt-3 flex justify-between items-center">
                <span class="font-semibold text-coffee-700">Total</span>
                <span class="font-extrabold text-coffee-800">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>

            @if($order->payment_status === 'rejected')
                <div class="mt-4 rounded-xl bg-red-50 border border-red-200 p-3.5 text-red-800">
                    <p class="text-xs font-bold uppercase tracking-wider text-red-700">❌ Pesan / Alasan Penolakan dari Owner:</p>
                    <p class="text-sm font-semibold mt-1.5 bg-white p-3 rounded-lg border border-red-200 shadow-xs">{{ $order->rejection_reason ?? 'Bukti pembayaran tidak sesuai atau belum valid.' }}</p>
                    <p class="text-xs text-red-600 mt-2">Silakan hubungi kasir/staf warkop untuk mengonfirmasi ulang.</p>
                </div>
            @endif

            @if($order->payment_status === 'approved' || auth()->check())
                <div class="mt-4 pt-3 border-t border-coffee-100 flex justify-end">
                    <a href="{{ route('order.receipt', $order->order_number) }}" target="_blank"
                       class="btn-press text-xs font-semibold px-4 py-2.5 rounded-xl bg-coffee-800 hover:bg-coffee-900 text-white shadow transition">
                        🧾 Lihat & Download Struk (PDF)
                    </a>
                </div>
            @else
                <div class="mt-4 pt-3 border-t border-coffee-100">
                    <div class="rounded-xl bg-amber-50 border border-amber-200/90 p-3 text-center text-amber-900 text-xs font-semibold shadow-xs">
                        ⏳ Struk PDF dapat diunduh setelah pembayaran disetujui (ACC) oleh Owner.
                    </div>
                </div>
            @endif
        </div>
    @endif

    <a href="{{ route('menu.index') }}" class="block mt-6 text-center text-coffee-400 text-sm underline">← Kembali ke menu</a>
</div>
@endsection
