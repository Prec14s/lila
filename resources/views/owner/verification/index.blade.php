@extends('layouts.admin')

@section('content')
@php $title = 'Verifikasi Pembayaran'; @endphp

<div class="mb-5 flex items-center gap-3 flex-wrap">
    <span class="text-sm text-coffee-500">Filter cepat:</span>
    <span class="text-xs font-semibold px-3 py-1.5 rounded-full bg-indigo-100 text-indigo-700">📲 Non-Tunai: cek bukti transfer/QRIS</span>
    <span class="text-xs font-semibold px-3 py-1.5 rounded-full bg-orange-100 text-orange-700">💵 Tunai: konfirmasi setelah bayar di kasir</span>
</div>

<div class="grid gap-4">
    @forelse($orders as $order)
        <div class="card-pop bg-white rounded-2xl border border-coffee-100 shadow-sm p-5">
            <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-bold text-coffee-800">{{ $order->order_number }}</p>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $order->paymentCategoryColor() }}">
                            {{ $order->paymentCategoryIcon() }} {{ $order->paymentCategoryLabel() }}
                        </span>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-coffee-100 text-coffee-700">
                            {{ $order->paymentMethodLabel() }}
                        </span>
                    </div>
                    <p class="text-sm text-coffee-500 mt-1">{{ $order->customer_name }} · {{ $order->customer_phone }} · <span class="font-bold text-coffee-800 bg-coffee-100 px-2 py-0.5 rounded">🪑 {{ $order->table_number ?? '-' }}</span></p>
                    <p class="text-xs text-coffee-400 mt-0.5">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                </div>
                <p class="font-extrabold text-lg text-coffee-800">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                {{-- Rincian item --}}
                <div class="bg-coffee-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-coffee-500 uppercase mb-2">Rincian Pesanan</p>
                    <div class="space-y-1">
                        @foreach($order->items as $item)
                            <div class="flex justify-between text-sm">
                                <span class="text-coffee-700">{{ $item->menu_name }} x{{ $item->qty }}</span>
                                <span class="text-coffee-800 font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    @if($order->note)
                        <p class="text-xs text-coffee-500 italic mt-2">Catatan: {{ $order->note }}</p>
                    @endif
                </div>

                {{-- Bukti pembayaran / instruksi tunai --}}
                <div>
                    @if($order->isCash())
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 h-full flex flex-col items-center justify-center text-center">
                            <span class="text-3xl mb-1">💵</span>
                            <p class="font-semibold text-orange-700 text-sm">Pelanggan memilih bayar TUNAI di kasir</p>
                            <p class="text-orange-500 text-xs mt-1">Pastikan uang sudah diterima sebelum menekan ACC.</p>
                        </div>
                    @else
                        <p class="text-xs font-semibold text-coffee-500 uppercase mb-2">Bukti Pembayaran</p>
                        @if($order->payment_proof)
                            <a href="{{ asset('storage/'.$order->payment_proof) }}" target="_blank">
                                <img src="{{ asset('storage/'.$order->payment_proof) }}" class="w-full h-40 object-contain rounded-xl bg-coffee-50 border border-coffee-100 hover:opacity-90 transition">
                            </a>
                        @else
                            <div class="w-full h-40 rounded-xl bg-coffee-50 border border-coffee-100 flex items-center justify-center text-coffee-300 text-sm">Belum ada bukti</div>
                        @endif
                    @endif
                </div>
            </div>

            <div x-data="{ rejectOpen: false }" class="mt-4">
                <div class="flex gap-3">
                    <form action="{{ route('owner.verification.approve', $order) }}" method="POST" class="flex-1">
                        @csrf
                        <button class="btn-press w-full {{ $order->isCash() ? 'bg-orange-600 hover:bg-orange-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white font-semibold py-2.5 rounded-xl transition text-sm">
                            {{ $order->isCash() ? '💵 Tunai Diterima & ACC' : '✅ ACC Pembayaran' }}
                        </button>
                    </form>
                    <button type="button" @click="rejectOpen = !rejectOpen" class="btn-press flex-1 bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2.5 rounded-xl transition text-sm border border-red-200">
                        ❌ Tolak Pembayaran
                    </button>
                </div>

                {{-- FORM ALASAN PENOLAKAN --}}
                <div x-show="rejectOpen" x-transition class="mt-3 p-4 bg-red-50/80 rounded-xl border border-red-200">
                    <form action="{{ route('owner.verification.reject', $order) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-red-800 mb-1">Pesan / Alasan Penolakan untuk Pelanggan:</label>
                            <input type="text" name="rejection_reason" placeholder="Contoh: Bukti transfer tidak jelas / nominal kurang..." required
                                   class="w-full border border-red-300 rounded-lg px-3.5 py-2 text-sm bg-white focus:ring-2 focus:ring-red-400 focus:outline-none">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="rejectOpen = false" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-coffee-600 bg-white border border-coffee-200">Batal</button>
                            <button type="submit" class="px-4 py-1.5 rounded-lg text-xs font-bold text-white bg-red-600 hover:bg-red-700 transition">Kirim & Tolak Pembayaran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-16 bg-white rounded-2xl border border-coffee-100">
            <span class="text-4xl">📭</span>
            <p class="text-coffee-400 mt-2 text-sm">Tidak ada pesanan yang menunggu verifikasi.</p>
        </div>
    @endforelse
</div>
@endsection
