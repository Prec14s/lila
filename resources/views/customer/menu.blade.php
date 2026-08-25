@extends('layouts.app')

@section('content')
<div x-data="orderApp()" x-init="init()" x-cloak class="pb-28">

    {{-- ============ HEADER ============ --}}
    <header class="bg-gradient-to-br from-coffee-800 via-coffee-700 to-coffee-600 text-coffee-50 px-5 pt-8 pb-14 rounded-b-[2.5rem] relative overflow-hidden shadow-lg text-center">
        {{-- Awan Bergerak (Floating Clouds) --}}
        <div class="absolute left-4 top-3 text-3xl animate-floaty select-none opacity-80 pointer-events-none" style="animation-duration: 3.5s; animation-delay: 0s;">☁️</div>
        <div class="absolute left-1/4 top-10 text-2xl animate-floaty select-none opacity-50 pointer-events-none" style="animation-duration: 4.5s; animation-delay: 1s;">☁️</div>
        <div class="absolute right-12 top-4 text-3xl animate-floaty select-none opacity-60 pointer-events-none" style="animation-duration: 3.8s; animation-delay: 0.5s;">☁️</div>
        <div class="absolute left-10 bottom-3 text-2xl animate-floaty select-none opacity-50 pointer-events-none" style="animation-duration: 4.2s; animation-delay: 1.8s;">☁️</div>
        <div class="absolute right-1/4 bottom-4 text-3xl animate-floaty select-none opacity-40 pointer-events-none" style="animation-duration: 5s; animation-delay: 2.2s;">☁️</div>
        <div class="absolute right-3 top-16 text-xl animate-floaty select-none opacity-50 pointer-events-none" style="animation-duration: 3.2s; animation-delay: 1.4s;">☁️</div>
        
        <div class="relative max-w-4xl mx-auto">
            <div class="flex justify-end mb-1">
                <button @click="statusOpen = true" class="btn-press bg-white/15 hover:bg-white/25 backdrop-blur text-xs sm:text-sm font-medium px-4 py-2 rounded-full">
                    🔎 Cek Order
                </button>
            </div>
            
            <p class="text-coffee-200 text-xs sm:text-sm tracking-widest uppercase mt-1">Selamat datang di</p>
            <h1 class="text-2xl sm:text-4xl font-extrabold flex items-center justify-center gap-2 mt-1">
                <span class="relative inline-block">
                    ☕
                    <span class="absolute -top-2 left-1/2 -translate-x-1/2 w-1.5 h-3 bg-white/40 rounded-full animate-steam"></span>
                </span>
                Warkop Samalila
            </h1>
            <p class="relative text-coffee-100 text-sm sm:text-base mt-2.5 max-w-md mx-auto">Pilih menu favoritmu, bayar mandiri, pesanan langsung kami siapkan 🔥</p>
        </div>
    </header>

    {{-- ============ SEARCH & CATEGORY TABS ============ --}}
    <div class="sticky top-0 z-20 bg-coffee-50/95 backdrop-blur px-4 sm:px-6 lg:px-8 -mt-8 relative pt-2 pb-3 shadow-xs border-b border-coffee-100/50">
        <div class="max-w-7xl mx-auto space-y-3">
            {{-- Search Bar --}}
            <div class="relative max-w-md mx-auto sm:mx-0">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-coffee-400">
                    🔎
                </span>
                <input x-model="searchQuery" type="text" placeholder="Cari nama menu favoritmu..."
                       class="w-full bg-white border border-coffee-200 rounded-2xl pl-10 pr-10 py-2.5 text-sm text-coffee-800 focus:ring-2 focus:ring-coffee-400 focus:outline-none shadow-sm transition">
                <button x-show="searchQuery.length > 0" @click="searchQuery = ''" type="button" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-coffee-400 hover:text-coffee-600 text-sm">
                    ✕
                </button>
            </div>

            {{-- Category Tabs --}}
            <div class="flex gap-2 overflow-x-auto pb-1 no-scrollbar justify-start sm:justify-center">
                @foreach($categories as $i => $category)
                    <button @click="activeCategory = '{{ $category->id }}'"
                            :class="activeCategory === '{{ $category->id }}' && searchQuery === '' ? 'bg-coffee-700 text-white shadow-lg scale-105' : 'bg-white text-coffee-700 border border-coffee-200'"
                            class="btn-press whitespace-nowrap flex items-center gap-1.5 px-4 sm:px-5 py-2.5 rounded-full text-sm font-semibold transition-all duration-200">
                        <span>{{ $category->icon }}</span> {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ============ MENU GRID ============ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 space-y-8">
        @forelse($categories as $category)
            <section x-show="hasCategoryMatches('{{ $category->id }}')" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <h2 class="font-bold text-coffee-800 text-lg sm:text-xl mb-4 flex items-center gap-2">{{ $category->icon }} {{ $category->name }}</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3.5 sm:gap-5">
                    @foreach($category->menus as $menu)
                        <div x-show="matchesSearch('{{ addslashes($menu->name) }}', '{{ $category->id }}')" class="card-hover bg-white rounded-2xl overflow-hidden border border-coffee-100 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="h-28 sm:h-36 bg-coffee-100 overflow-hidden">
                                    <img src="{{ $menu->photo_url }}" alt="{{ $menu->name }}" class="w-full h-full object-cover" loading="lazy">
                                </div>
                                <div class="p-3.5">
                                    <p class="font-semibold text-sm text-coffee-800 leading-snug line-clamp-2 h-9">{{ $menu->name }}</p>
                                    <p class="text-coffee-500 font-bold text-sm mt-1">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="p-3.5 pt-0">

                                <template x-if="!cartHas({{ $menu->id }})">
                                    <button @click="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }})"
                                            class="btn-press mt-2 w-full bg-coffee-700 hover:bg-coffee-800 text-white text-xs font-bold py-2 rounded-lg transition">
                                        + Tambah
                                    </button>
                                </template>
                                <template x-if="cartHas({{ $menu->id }})">
                                    <div class="mt-2 flex items-center justify-between bg-coffee-50 rounded-lg px-1.5 py-1">
                                        <button @click="decrement({{ $menu->id }})" class="btn-press w-7 h-7 rounded-md bg-white shadow text-coffee-700 font-bold">−</button>
                                        <span x-text="qtyOf({{ $menu->id }})" class="font-bold text-sm text-coffee-800"></span>
                                        <button @click="increment({{ $menu->id }})" class="btn-press w-7 h-7 rounded-md bg-coffee-700 text-white shadow font-bold">+</button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @empty
            <p class="text-center text-coffee-400 py-16">Menu belum tersedia. Silakan hubungi Owner.</p>
        @endforelse

        {{-- PESAN JIKA HASIL CARI KOSONG --}}
        <div x-show="searchQuery.trim() !== '' && !hasAnyMatches()" x-cloak class="text-center py-16 animate-popin">
            <span class="text-4xl">🔍</span>
            <p class="text-coffee-800 font-bold text-lg mt-2">Menu tidak ditemukan</p>
            <p class="text-coffee-500 text-sm mt-1">Tidak ada menu yang sesuai dengan kata kunci "<span class="font-semibold text-coffee-800" x-text="searchQuery"></span>".</p>
            <button @click="searchQuery = ''" class="mt-4 btn-press text-xs font-bold px-4 py-2.5 rounded-xl bg-coffee-100 text-coffee-800 hover:bg-coffee-200 shadow-xs">
                Lihat Semua Menu
            </button>
        </div>
    </div>

    {{-- ============ FLOATING CART BUTTON ============ --}}
    <div x-show="cart.length > 0" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed bottom-4 left-1/2 -translate-x-1/2 w-[calc(100%-2rem)] max-w-md z-30">
        <button @click="openCart()" class="btn-press w-full bg-coffee-800 hover:bg-coffee-900 text-white rounded-2xl shadow-2xl px-5 py-3.5 flex items-center justify-between animate-popin border border-coffee-700/50">
            <span class="flex items-center gap-2.5 font-semibold text-sm">
                <span class="relative text-base">
                    🛒
                    <span x-text="totalQty()" class="absolute -top-2 -right-2.5 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow"></span>
                </span>
                Lihat Keranjang
            </span>
            <span class="font-extrabold text-sm" x-text="formatRupiah(totalPrice())"></span>
        </button>
    </div>

    {{-- ============ IDENTITY MODAL ============ --}}
    <div x-show="identityOpen" x-cloak class="fixed inset-0 z-50 flex items-end sm:items-center justify-center">
        <div class="absolute inset-0 bg-black/50" x-transition.opacity @click="identityOpen = false; cartOpen = true"></div>
        <div x-show="identityOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
             class="relative bg-white w-full sm:max-w-sm sm:rounded-3xl rounded-t-3xl p-6 shadow-2xl">
            <div class="text-center mb-4">
                <span class="text-4xl">👋</span>
                <h3 class="font-bold text-lg text-coffee-800 mt-1">Kenalan dulu, yuk!</h3>
                <p class="text-coffee-500 text-sm">Isi nama & nomor WhatsApp untuk melanjutkan pesanan.</p>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Nama Lengkap</label>
                    <input x-model="customerName" type="text" placeholder="Contoh: Budi Santoso"
                           class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Nomor WhatsApp <span class="text-coffee-400 font-normal">(Hanya Angka, Max 12 Digit)</span></label>
                    <input x-model="customerPhone"
                           @input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '').slice(0, 12); customerPhone = $event.target.value"
                           @keydown="if ($event.key.length === 1 && !/[0-9]/.test($event.key) && !$event.ctrlKey && !$event.metaKey && $event.key !== 'Backspace' && $event.key !== 'Delete' && $event.key !== 'Tab') $event.preventDefault()"
                           type="text" inputmode="numeric" pattern="[0-9]*" maxlength="12" placeholder="Contoh: 081234567890"
                           class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none font-semibold">
                </div>
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Nomor Meja <span class="text-coffee-400 font-normal">(Hanya Angka)</span></label>
                    <input x-model="tableNumber" @input="tableNumber = tableNumber.replace(/[^0-9]/g, '')" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="Contoh: 05"
                           class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none font-semibold">
                </div>

                {{-- Alert Peringatan Pindah Meja --}}
                <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-center text-amber-800 text-xs">
                    <p class="leading-relaxed">
                        ⚠️ <strong class="font-bold">Penting:</strong> Bila Anda berpindah meja, harap segera beritahu orang/staf dapur agar pesanan disajikan ke meja yang tepat.
                    </p>
                </div>

                <button @click="saveIdentity()" class="btn-press w-full bg-coffee-700 hover:bg-coffee-800 text-white font-bold py-3 rounded-xl transition">
                    Lanjutkan
                </button>
            </div>
        </div>
    </div>

    {{-- ============ CART SLIDE-OVER ============ --}}
    <div x-show="cartOpen" x-cloak class="fixed inset-0 z-50 flex items-end justify-center">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" x-transition.opacity @click="cartOpen = false"></div>
        <div x-show="cartOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
             class="relative bg-white w-full max-w-md rounded-t-3xl max-h-[88vh] flex flex-col shadow-2xl pb-safe">
            <div class="w-12 h-1.5 bg-coffee-200 rounded-full mx-auto mt-3 sm:hidden"></div>
            <div class="px-5 pt-3 pb-3 border-b border-coffee-100 flex items-center justify-between">
                <h3 class="font-bold text-lg text-coffee-800">🛒 Keranjang Pesanan</h3>
                <button @click="cartOpen = false" class="text-coffee-400 text-2xl leading-none font-light">&times;</button>
            </div>

            <div class="flex-1 overflow-y-auto px-5 py-3 space-y-3">
                <template x-if="cart.length === 0">
                    <p class="text-center text-coffee-400 py-10 text-sm">Keranjang masih kosong.</p>
                </template>
                <template x-for="item in cart" :key="item.id">
                    <div class="flex items-center justify-between bg-coffee-50 rounded-xl px-3 py-2.5">
                        <div>
                            <p class="text-sm font-semibold text-coffee-800" x-text="item.name"></p>
                            <p class="text-xs text-coffee-500" x-text="formatRupiah(item.price) + ' x ' + item.qty"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="decrement(item.id)" class="btn-press w-7 h-7 rounded-md bg-white shadow text-coffee-700 font-bold">−</button>
                            <span x-text="item.qty" class="font-bold text-sm w-4 text-center"></span>
                            <button @click="increment(item.id)" class="btn-press w-7 h-7 rounded-md bg-coffee-700 text-white shadow font-bold">+</button>
                        </div>
                    </div>
                </template>

                <div x-show="cart.length > 0" class="pt-2">
                    <label class="text-xs font-semibold text-coffee-600">Catatan (opsional)</label>
                    <input x-model="note" type="text" placeholder="Contoh: kopi tidak pakai gula"
                           class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
                </div>
            </div>

            <div class="px-5 py-4 border-t border-coffee-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-coffee-600 text-sm">Total Pembayaran</span>
                    <span class="font-extrabold text-coffee-800 text-lg" x-text="formatRupiah(totalPrice())"></span>
                </div>
                <button @click="checkout()" :disabled="cart.length === 0 || submitting"
                        class="btn-press w-full bg-coffee-800 hover:bg-coffee-900 disabled:opacity-40 text-white font-bold py-3.5 rounded-xl transition flex items-center justify-center gap-2">
                    <span x-show="!submitting">Checkout Sekarang →</span>
                    <span x-show="submitting">Memproses...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ============ STATUS CHECK QUICK LINK MODAL ============ --}}
    <div x-show="statusOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-black/50" x-transition.opacity @click="statusOpen = false"></div>
        <div x-show="statusOpen" x-transition class="relative bg-white w-full max-w-sm rounded-3xl p-6 shadow-2xl">
            <h3 class="font-bold text-lg text-coffee-800 mb-1">🔎 Cek Status Pesanan</h3>
            <p class="text-coffee-500 text-sm mb-4">Masukkan Nomor Order kamu.</p>
            <form action="{{ route('order.status') }}" method="GET" class="space-y-3">
                <input name="order_number" type="text" placeholder="WS-20260824-0001" required
                       class="w-full border border-coffee-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
                <button class="btn-press w-full bg-coffee-700 hover:bg-coffee-800 text-white font-bold py-3 rounded-xl transition">Cek Sekarang</button>
            </form>
            <button @click="statusOpen = false" class="mt-3 w-full text-coffee-400 text-xs">Tutup</button>
        </div>
    </div>

</div>

<script>
function orderApp() {
    return {
        categories: @json($categories->map(fn($c) => ['id' => (string) $c->id])),
        allMenus: @json($categories->flatMap(fn($c) => $c->menus->map(fn($m) => ['id' => $m->id, 'name' => $m->name, 'cat' => (string) $c->id]))),
        cart: [],
        activeCategory: '{{ $categories->first()->id ?? '' }}',
        searchQuery: '',
        cartOpen: false,
        identityOpen: false,
        statusOpen: false,
        submitting: false,
        customerName: '',
        customerPhone: '',
        tableNumber: '',
        note: '',

        init() {
            localStorage.removeItem('ws_name');
            localStorage.removeItem('ws_phone');
            localStorage.removeItem('ws_table');

            this.$watch('customerPhone', val => {
                if (val) {
                    const cleaned = String(val).replace(/[^0-9]/g, '').slice(0, 12);
                    if (cleaned !== val) {
                        this.customerPhone = cleaned;
                    }
                }
            });
        },

        matchesSearch(name, catId) {
            const q = this.searchQuery.trim().toLowerCase();
            if (!q) return this.activeCategory === catId;
            return name.toLowerCase().includes(q);
        },

        hasCategoryMatches(catId) {
            const q = this.searchQuery.trim().toLowerCase();
            if (!q) return this.activeCategory === catId;
            return this.allMenus.some(m => m.cat === catId && m.name.toLowerCase().includes(q));
        },

        hasAnyMatches() {
            const q = this.searchQuery.trim().toLowerCase();
            if (!q) return true;
            return this.allMenus.some(m => m.name.toLowerCase().includes(q));
        },

        cartHas(id) { return this.cart.some(i => i.id === id); },
        qtyOf(id) { const item = this.cart.find(i => i.id === id); return item ? item.qty : 0; },

        addToCart(id, name, price) {
            this.cart.push({ id, name, price, qty: 1 });
        },
        increment(id) {
            const item = this.cart.find(i => i.id === id);
            if (item) item.qty++;
        },
        decrement(id) {
            const item = this.cart.find(i => i.id === id);
            if (!item) return;
            item.qty--;
            if (item.qty <= 0) this.cart = this.cart.filter(i => i.id !== id);
        },
        totalQty() { return this.cart.reduce((sum, i) => sum + i.qty, 0); },
        totalPrice() { return this.cart.reduce((sum, i) => sum + (i.qty * i.price), 0); },
        formatRupiah(n) { return 'Rp ' + n.toLocaleString('id-ID'); },

        openCart() {
            this.cartOpen = true;
        },

        saveIdentity() {
            this.customerPhone = this.customerPhone.replace(/[^0-9]/g, '').slice(0, 12);
            this.tableNumber = this.tableNumber.replace(/[^0-9]/g, '');
            if (!this.customerName.trim() || !this.customerPhone.trim() || !this.tableNumber.trim()) {
                alert('Nama, nomor WhatsApp (hanya angka maksimal 12 digit), dan nomor meja wajib diisi.');
                return;
            }
            if (this.customerPhone.length > 12) {
                alert('Nomor WhatsApp maksimal 12 digit angka.');
                return;
            }
            this.identityOpen = false;
            this.submitCheckout();
        },

        checkout() {
            if (this.cart.length === 0) return;
            if (!this.customerName.trim() || !this.customerPhone.trim() || !this.tableNumber.trim()) {
                this.cartOpen = false;
                this.identityOpen = true;
                return;
            }
            this.submitCheckout();
        },

        async submitCheckout() {
            this.submitting = true;
            try {
                const payload = {
                    _token: '{{ csrf_token() }}',
                    customer_name: this.customerName,
                    customer_phone: this.customerPhone,
                    table_number: this.tableNumber,
                    note: this.note,
                    cart: JSON.stringify(this.cart.map(i => ({ id: i.id, qty: i.qty })))
                };

                const res = await fetch('{{ route('order.store', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (res.ok) {
                    const data = await res.json();
                    if (data.redirect) {
                        window.location.replace(data.redirect);
                        return;
                    }
                }
                const errData = await res.json();
                alert(Object.values(errData.errors || {})[0]?.[0] || 'Gagal memproses pesanan.');
                this.submitting = false;
            } catch (err) {
                alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
                this.submitting = false;
            }
        },
    }
}
</script>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@endsection
