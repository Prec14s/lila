@extends('layouts.admin')

@section('content')
@php $title = 'Metode Pembayaran'; @endphp

<div x-data="{ open: false, type: 'qris' }" class="mb-6">
    <button @click="open = true" class="btn-press bg-coffee-800 hover:bg-coffee-900 text-white font-semibold px-5 py-2.5 rounded-xl text-sm">
        + Tambah Metode Pembayaran
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-black/50" @click="open = false" x-transition.opacity></div>
        <div x-show="open" x-transition class="relative bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <h3 class="font-bold text-lg text-coffee-800 mb-4">Tambah Metode Pembayaran</h3>

            <form action="{{ route('owner.payment-settings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Jenis Pembayaran</label>
                    <div class="grid grid-cols-3 gap-2 mt-1">
                        <label :class="type === 'qris' ? 'border-coffee-700 bg-coffee-50' : 'border-coffee-200'" class="cursor-pointer border-2 rounded-xl p-2.5 text-center">
                            <input type="radio" name="type" value="qris" x-model="type" class="hidden"> 📱<br><span class="text-[11px] font-semibold">QRIS</span>
                        </label>
                        <label :class="type === 'bank_transfer' ? 'border-coffee-700 bg-coffee-50' : 'border-coffee-200'" class="cursor-pointer border-2 rounded-xl p-2.5 text-center">
                            <input type="radio" name="type" value="bank_transfer" x-model="type" class="hidden"> 🏦<br><span class="text-[11px] font-semibold">Transfer</span>
                        </label>
                        <label :class="type === 'cash' ? 'border-orange-600 bg-orange-50' : 'border-coffee-200'" class="cursor-pointer border-2 rounded-xl p-2.5 text-center">
                            <input type="radio" name="type" value="cash" x-model="type" class="hidden"> 💵<br><span class="text-[11px] font-semibold">Tunai</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-coffee-600">Label (opsional)</label>
                    <input name="label" type="text" placeholder="Contoh: QRIS Warkop Samalila"
                           class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
                </div>

                <div x-show="type === 'qris'" x-transition>
                    <label class="text-xs font-semibold text-coffee-600">Gambar QRIS</label>
                    <input name="qris_image" type="file" accept="image/*"
                           class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                </div>

                <div x-show="type === 'bank_transfer'" x-transition class="space-y-3">
                    <div>
                        <label class="text-xs font-semibold text-coffee-600">Nama Bank</label>
                        <input name="bank_name" type="text" placeholder="Contoh: BCA"
                               class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-coffee-600">Nomor Rekening</label>
                        <input name="account_number" type="text"
                               class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-coffee-600">Atas Nama</label>
                        <input name="account_holder" type="text"
                               class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                </div>

                <div x-show="type === 'cash'" x-transition>
                    <label class="text-xs font-semibold text-coffee-600">Instruksi untuk Pelanggan</label>
                    <textarea name="instruction" rows="2" placeholder="Contoh: Bayar langsung ke kasir saat mengambil pesanan"
                              class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="open = false" class="flex-1 border border-coffee-200 text-coffee-600 font-semibold py-2.5 rounded-xl text-sm">Batal</button>
                    <button type="submit" class="flex-1 bg-coffee-800 hover:bg-coffee-900 text-white font-semibold py-2.5 rounded-xl text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-4">
    @forelse($paymentSettings as $ps)
        <div class="card-pop bg-white rounded-2xl border border-coffee-100 shadow-sm p-5 flex items-start justify-between gap-4">
            <div class="flex gap-3">
                <span class="text-3xl">{{ $ps->icon() }}</span>
                <div>
                    <div class="flex items-center gap-2">
                        <p class="font-bold text-coffee-800">{{ $ps->displayLabel() }}</p>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $ps->isCash() ? 'bg-orange-100 text-orange-700' : 'bg-indigo-100 text-indigo-700' }}">
                            {{ $ps->isCash() ? 'TUNAI' : 'NON-TUNAI' }}
                        </span>
                    </div>
                    @if($ps->type === 'bank_transfer')
                        <p class="text-sm text-coffee-500 mt-1">{{ $ps->account_number }} a.n. {{ $ps->account_holder }}</p>
                    @elseif($ps->type === 'cash')
                        <p class="text-sm text-coffee-500 mt-1">{{ $ps->instruction }}</p>
                    @elseif($ps->qris_image)
                        <img src="{{ asset('storage/'.$ps->qris_image) }}" class="w-16 h-16 object-contain mt-2 rounded-lg border border-coffee-100">
                    @endif
                    <span class="inline-block mt-2 text-[11px] font-semibold px-2.5 py-0.5 rounded-full {{ $ps->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $ps->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
            <div class="flex flex-col gap-2 shrink-0">
                <form action="{{ route('owner.payment-settings.toggle', $ps) }}" method="POST">
                    @csrf @method('PATCH')
                    <button class="btn-press text-xs font-semibold px-3 py-1.5 rounded-lg border border-coffee-200 text-coffee-600 hover:bg-coffee-50">
                        {{ $ps->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
                <form action="{{ route('owner.payment-settings.destroy', $ps) }}" method="POST" onsubmit="return confirm('Hapus metode ini?')">
                    @csrf @method('DELETE')
                    <button class="btn-press text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 w-full">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-coffee-400 text-sm">Belum ada metode pembayaran.</p>
    @endforelse
</div>
@endsection
