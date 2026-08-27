@extends('layouts.app')

@section('content')
<script>
function paymentForm() {
    return {
        method: '{{ old('payment_method', $paymentSettings->first()->type ?? 'qris') }}',
        preview: null,
        submitting: false,
        errorMessage: '',
        copied: false,
        isCash() { return this.method === 'cash'; },
        copyOrderNumber(text) {
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
        },
        setPreview(e) { 
            const f = e.target.files[0]; 
            if (f) this.preview = URL.createObjectURL(f); 
        },
        async submitPayment() {
            if (this.submitting) return;
            this.submitting = true;
            this.errorMessage = '';

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('payment_method', this.method);

            const fileInput = document.querySelector('input[name="payment_proof"]');
            if (fileInput && fileInput.files[0]) {
                formData.append('payment_proof', fileInput.files[0]);
            }

            try {
                const res = await fetch("{{ route('order.upload-proof', $order->order_number) }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json().catch(() => null);

                if (res.ok && data && data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                if (data && data.errors) {
                    const firstErr = Object.values(data.errors)[0];
                    this.errorMessage = Array.isArray(firstErr) ? firstErr[0] : (firstErr || 'Gagal memproses pembayaran.');
                } else {
                    this.errorMessage = 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.';
                }
                this.submitting = false;
            } catch (err) {
                console.error('Payment error:', err);
                this.errorMessage = 'Terjadi kesalahan koneksi. Silakan coba lagi.';
                this.submitting = false;
            }
        }
    }
}
</script>

<div x-data="paymentForm()" class="max-w-2xl mx-auto px-4 py-8 sm:py-12">

    <div class="text-center mb-6">
        <span class="text-4xl">💳</span>
        <h1 class="text-xl font-extrabold text-coffee-800 mt-2">Selesaikan Pembayaran</h1>
        <div class="flex items-center justify-center gap-2 mt-2">
            <p class="text-coffee-600 text-sm">No. Order: <span class="font-bold text-coffee-800 font-mono bg-coffee-50 px-2 py-0.5 rounded border border-coffee-200/60 select-all">{{ $order->order_number }}</span></p>
            <button @click="copyOrderNumber('{{ trim($order->order_number) }}')"
                    type="button"
                    class="btn-press text-xs font-bold transition-all inline-flex items-center">
                <span x-show="!copied" class="text-coffee-700 bg-coffee-100 px-2.5 py-1 rounded-lg hover:bg-coffee-200 flex items-center gap-1">📋 Salin</span>
                <span x-show="copied" x-cloak class="bg-emerald-600 text-white px-2.5 py-1 rounded-lg shadow-xs flex items-center gap-1">✅ Tersalin!</span>
            </button>
        </div>
        <p class="text-coffee-500 text-xs mt-1.5 font-medium">Silahkan salin no order untuk cek proses pesanan kamu</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-coffee-100 p-5 mb-5 card-hover">
        <p class="text-coffee-500 text-xs uppercase tracking-wide mb-2">Ringkasan Pesanan</p>
        <div class="space-y-1.5">
            @foreach($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-coffee-700">{{ $item->menu_name }} <span class="text-coffee-400">x{{ $item->qty }}</span></span>
                    <span class="font-medium text-coffee-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
        <div class="border-t border-dashed border-coffee-200 mt-3 pt-3 flex justify-between items-center">
            <span class="font-semibold text-coffee-700">Total Bayar</span>
            <span class="font-extrabold text-lg text-coffee-800">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    <div x-show="errorMessage" x-cloak class="mb-5 rounded-2xl bg-red-50 border border-red-200 text-red-800 p-4 text-xs font-semibold shadow-xs">
        ⚠️ <span x-text="errorMessage"></span>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-2xl bg-red-50 border border-red-200 text-red-800 p-4 text-xs font-semibold shadow-xs">
            ⚠️ {{ $errors->first() }}
        </div>
    @endif

    <div class="space-y-5">
        {{-- ============ PILIH CARA BAYAR: TUNAI / NON-TUNAI ============ --}}
        <div>
            <p class="text-sm font-semibold text-coffee-700 mb-2">Pilih Cara Bayar</p>
            <div class="grid grid-cols-2 xs:grid-cols-{{ min(3, max(2, $paymentSettings->count())) }} gap-2.5">
                @foreach($paymentSettings as $ps)
                    <button type="button"
                            @click="method = '{{ $ps->type }}'"
                            :class="method === '{{ $ps->type }}' ? 'border-coffee-800 bg-coffee-100 ring-2 ring-coffee-800 scale-[1.02]' : 'border-coffee-200 bg-white hover:bg-coffee-50'"
                            class="border-2 rounded-xl p-3 text-center transition-all duration-150 btn-press flex flex-col items-center justify-center cursor-pointer select-none">
                        <span class="text-2xl block mb-1 pointer-events-none">{{ $ps->icon() }}</span>
                        <span class="text-xs font-bold text-coffee-800 leading-tight block pointer-events-none">{{ $ps->displayLabel() }}</span>
                    </button>
                @endforeach
            </div>
            <div class="flex justify-center gap-2 mt-3 text-[11px] flex-wrap">
                <span class="px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 font-semibold">📲 Non-Tunai = QRIS / Transfer</span>
                <span class="px-2.5 py-1 rounded-full bg-orange-100 text-orange-700 font-semibold">💵 Tunai = Bayar di Kasir</span>
            </div>
        </div>

        {{-- ============ DETAIL NON-TUNAI (QRIS / TRANSFER) ============ --}}
        @foreach($paymentSettings as $ps)
            @if(! $ps->isCash())
                <div x-show="method === '{{ $ps->type }}'" x-transition class="bg-coffee-50 border border-coffee-200/80 rounded-2xl p-4 text-center">
                    @if($ps->type === 'qris')
                        <p class="text-coffee-700 text-xs font-bold uppercase tracking-wide mb-2">QRIS Owner Warkop Samalila</p>
                        @if($ps->qris_image)
                            <img src="{{ asset('storage/'.$ps->qris_image) }}" alt="QRIS Warkop" class="w-56 h-56 object-contain mx-auto rounded-xl bg-white p-2 shadow-sm border border-coffee-200">
                        @else
                            <div class="w-56 h-56 mx-auto rounded-xl bg-white flex flex-col items-center justify-center text-coffee-400 text-xs shadow-sm border border-coffee-200 p-4">
                                <span class="text-3xl mb-1">📱</span>
                                <span>Owner belum mengunggah gambar QRIS. Silakan minta QRIS langsung ke kasir warkop.</span>
                            </div>
                        @endif
                        <p class="text-xs text-coffee-600 mt-2 font-medium">Scan QRIS di atas menggunakan aplikasi E-Wallet / Mobile Banking kamu.</p>
                    @else
                        <p class="text-coffee-600 text-xs uppercase tracking-wide font-bold">Transfer ke Rekening</p>
                        <p class="text-lg font-extrabold text-coffee-800 mt-1">{{ $ps->bank_name }}</p>
                        <p class="text-2xl font-mono font-bold text-coffee-800 tracking-wider mt-1 bg-white inline-block px-3 py-1 rounded-lg border border-coffee-200 shadow-xs">{{ $ps->account_number }}</p>
                        <p class="text-sm font-semibold text-coffee-700 mt-1.5">a.n. {{ $ps->account_holder }}</p>
                    @endif
                </div>
            @endif
        @endforeach

        {{-- ============ DETAIL TUNAI ============ --}}
        @php $cashSetting = $paymentSettings->firstWhere('type', 'cash'); @endphp
        @if($cashSetting)
            <div x-show="method === 'cash'" x-transition class="bg-orange-50 border border-orange-200 rounded-2xl p-4 text-center">
                <span class="text-3xl block mb-1">💵</span>
                <p class="font-bold text-orange-700">Bayar Tunai di Kasir</p>
                <p class="text-orange-600 text-sm mt-1">{{ $cashSetting->instruction ?: 'Silakan bayar langsung ke kasir saat mengambil pesanan.' }}</p>
                <p class="text-orange-500 text-xs mt-2">Owner akan mengonfirmasi pembayaranmu setelah kamu bayar di kasir.</p>
            </div>
        @endif

        {{-- ============ UPLOAD BUKTI (HANYA NON-TUNAI) ============ --}}
        <div x-show="!isCash()" x-transition>
            <label class="text-sm font-semibold text-coffee-700 mb-2 block">Unggah Bukti Pembayaran <span class="text-red-500">*</span></label>
            <label class="block border-2 border-dashed border-coffee-300 rounded-2xl p-4 text-center cursor-pointer hover:border-coffee-500 transition bg-white">
                <template x-if="preview">
                    <img :src="preview" class="w-full h-40 object-contain rounded-xl mb-2">
                </template>
                <template x-if="!preview">
                    <div class="py-6">
                        <span class="text-3xl block mb-1">📤</span>
                        <span class="text-sm text-coffee-600 font-semibold">Ketuk untuk pilih foto/screenshot bukti bayar</span>
                    </div>
                </template>
                <input type="file" name="payment_proof" accept="image/*" class="hidden" @change="setPreview($event)">
            </label>
        </div>

        <button type="button"
                @click="submitPayment()"
                :disabled="submitting"
                class="btn-press w-full text-white font-bold py-3.5 rounded-xl transition shadow-lg disabled:opacity-50"
                :class="isCash() ? 'bg-orange-600 hover:bg-orange-700' : 'bg-coffee-800 hover:bg-coffee-900'">
            <span x-show="!submitting && !isCash()">Konfirmasi Pembayaran ✅</span>
            <span x-show="!submitting && isCash()">Konfirmasi Pesanan (Bayar di Kasir) 💵</span>
            <span x-show="submitting">Memproses... ⏳</span>
        </button>
    </div>
</div>
@endsection
