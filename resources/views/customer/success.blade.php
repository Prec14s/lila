@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-4 py-10 sm:py-14 text-center">

    <div class="w-24 h-24 mx-auto rounded-full bg-emerald-100 flex items-center justify-center animate-popin">
        <span class="text-5xl">🎉</span>
    </div>

    <h1 class="text-xl font-extrabold text-coffee-800 mt-5">Pesanan Berhasil Dikirim!</h1>
    <p class="text-coffee-500 text-sm mt-1">Langkah terakhir, kirim konfirmasi pesananmu ke WhatsApp Owner ya 👇</p>

    <div class="bg-white rounded-2xl shadow-sm border border-coffee-100 p-5 mt-6 text-left card-hover">
        <div class="flex justify-between items-center mb-3" x-data="{
            copied: false,
            copyText(text) {
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text);
                } else {
                    const input = document.createElement('textarea');
                    input.value = text;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    document.body.removeChild(input);
                }
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
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
        <div class="flex justify-between items-center mb-3 text-sm border-b border-coffee-100 pb-2">
            <span class="text-coffee-600">Nomor Meja</span>
            <span class="font-extrabold text-coffee-800 bg-coffee-100 px-2.5 py-0.5 rounded-lg">🪑 {{ $order->table_number ?? '-' }}</span>
        </div>
        <div class="space-y-1.5">
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
        <div class="mt-3 flex gap-2 flex-wrap">
            <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full {{ $order->paymentStatusColor() }}">
                {{ $order->paymentStatusLabel() }}
            </span>
            <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full {{ $order->paymentCategoryColor() }}">
                {{ $order->paymentCategoryIcon() }} {{ $order->paymentCategoryLabel() }} · {{ $order->paymentMethodLabel() }}
            </span>
        </div>
    </div>

    <div class="mt-4 rounded-2xl bg-amber-50 border border-amber-200 p-3.5 text-center text-amber-900 text-xs shadow-sm">
        <p class="leading-relaxed">
            ⚠️ <strong class="font-bold">Perhatian:</strong> Bila Anda berpindah tempat duduk/meja, mohon segera kabari orang/staf dapur agar pesanan diantarkan ke meja yang baru.
        </p>
    </div>

    @if($order->payment_status === 'rejected')
        <div class="mt-4 rounded-2xl bg-red-50 border border-red-200 p-4 text-left text-red-800">
            <p class="text-xs font-bold uppercase tracking-wider text-red-700">❌ Pesan / Alasan Penolakan dari Owner:</p>
            <p class="text-sm font-semibold mt-1.5 bg-white p-3 rounded-xl border border-red-200 shadow-xs">{{ $order->rejection_reason ?? 'Bukti pembayaran tidak sesuai atau belum valid.' }}</p>
            <p class="text-xs text-red-600 mt-2">Silakan hubungi kasir/staf warkop untuk mengonfirmasi ulang.</p>
        </div>
    @endif

    <a href="{{ $waOwnerLink }}" target="_blank"
       class="btn-press mt-6 w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 rounded-2xl shadow-lg flex items-center justify-center gap-2 animate-floaty">
        📲 Kirim Konfirmasi ke WhatsApp Owner
    </a>

    <a href="{{ route('order.status', ['order_number' => $order->order_number]) }}"
       class="btn-press mt-3 block w-full bg-white border border-coffee-200 text-coffee-700 font-semibold py-3 rounded-2xl">
        Cek Status Pesanan
    </a>

    @if($order->payment_status === 'approved')
        <a href="{{ route('order.receipt', $order->order_number) }}" target="_blank"
           class="btn-press mt-3 block w-full bg-coffee-800 hover:bg-coffee-900 text-white font-bold py-3.5 rounded-2xl shadow-md transition">
            🧾 Lihat & Download Struk (PDF)
        </a>
    @else
        <div class="mt-3 rounded-2xl bg-coffee-100/70 border border-coffee-200/80 p-3 text-center text-coffee-700 text-xs font-semibold">
            ⏳ Struk PDF akan dapat diunduh setelah pembayaran disetujui (ACC) oleh Owner.
        </div>
    @endif

    <a href="{{ route('menu.index') }}" class="block mt-4 text-coffee-400 text-sm underline">Pesan menu lainnya</a>
</div>
@endsection
