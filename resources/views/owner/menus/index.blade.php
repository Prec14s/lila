@extends('layouts.admin')

@section('content')
@php $title = 'Data Menu'; @endphp

<div x-data="{ open: false }" class="mb-6">
    <button @click="open = true" class="btn-press bg-coffee-800 hover:bg-coffee-900 text-white font-semibold px-5 py-2.5 rounded-xl text-sm">
        + Tambah Menu
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-black/50" @click="open = false" x-transition.opacity></div>
        <div x-show="open" x-transition class="relative bg-white w-full max-w-sm rounded-3xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <h3 class="font-bold text-lg text-coffee-800 mb-4">Tambah Menu</h3>
            <form action="{{ route('owner.menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Kategori</label>
                    <select name="category_id" required class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->icon }} {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Nama Menu</label>
                    <input name="name" type="text" required class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div x-data="{
                    displayPrice: '',
                    rawPrice: '',
                    formatRupiah(val) {
                        let num = val.replace(/[^0-9]/g, '');
                        this.rawPrice = num;
                        this.displayPrice = num ? new Intl.NumberFormat('id-ID').format(num) : '';
                    }
                }">
                    <label class="text-xs font-semibold text-coffee-600">Harga Menu</label>
                    <div class="relative mt-1">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-coffee-700 select-none">Rp</span>
                        <input type="text"
                               x-model="displayPrice"
                               @input="formatRupiah($event.target.value)"
                               placeholder="15.000"
                               required
                               class="w-full border border-coffee-200 rounded-xl pl-10 pr-4 py-2.5 text-sm font-semibold text-coffee-800 focus:ring-2 focus:ring-coffee-400 focus:outline-none">
                        <input type="hidden" name="price" :value="rawPrice">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Deskripsi (opsional)</label>
                    <textarea name="description" rows="2" class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm"></textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Foto (opsional)</label>
                    <input name="photo" type="file" accept="image/*" class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="open = false" class="flex-1 border border-coffee-200 text-coffee-600 font-semibold py-2.5 rounded-xl text-sm">Batal</button>
                    <button type="submit" class="flex-1 bg-coffee-800 hover:bg-coffee-900 text-white font-semibold py-2.5 rounded-xl text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-coffee-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-coffee-50 text-coffee-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">Menu</th>
                <th class="px-5 py-3 text-left">Kategori</th>
                <th class="px-5 py-3 text-left">Harga</th>
                <th class="px-5 py-3 text-left">Status</th>
                <th class="px-5 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-coffee-50">
            @forelse($menus as $menu)
                <tr x-data="{ editOpen: false }" class="hover:bg-coffee-50/60 transition">
                    <td class="px-5 py-3 flex items-center gap-3">
                        <img src="{{ $menu->photo_url }}" class="w-10 h-10 rounded-lg object-cover">
                        <span class="font-semibold text-coffee-800">{{ $menu->name }}</span>
                    </td>
                    <td class="px-5 py-3 text-coffee-600">{{ $menu->category?->icon }} {{ $menu->category?->name }}</td>
                    <td class="px-5 py-3 text-coffee-800 font-medium">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $menu->is_available ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right space-x-2 whitespace-nowrap">
                        <button @click="editOpen = true" class="btn-press text-xs font-semibold px-3 py-1.5 rounded-lg border border-coffee-200 text-coffee-600 hover:bg-coffee-50">Edit</button>
                        <form action="{{ route('owner.menus.destroy', $menu) }}" method="POST" class="inline" onsubmit="return confirm('Hapus menu ini?')">
                            @csrf @method('DELETE')
                            <button class="btn-press text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50">Hapus</button>
                        </form>
                    </td>

                    <td>
                        <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
                            <div class="absolute inset-0 bg-black/50" @click="editOpen = false" x-transition.opacity></div>
                            <div x-show="editOpen" x-transition class="relative bg-white w-full max-w-sm rounded-3xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto text-left">
                                <h3 class="font-bold text-lg text-coffee-800 mb-4">Edit Menu</h3>
                                <form action="{{ route('owner.menus.update', $menu) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="text-xs font-semibold text-coffee-600">Kategori</label>
                                        <select name="category_id" required class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                                            @foreach($categories as $c)
                                                <option value="{{ $c->id }}" @selected($c->id === $menu->category_id)>{{ $c->icon }} {{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-coffee-600">Nama Menu</label>
                                        <input name="name" type="text" required value="{{ $menu->name }}" class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                                    </div>
                                    <div x-data="{
                                        displayPrice: '{{ number_format($menu->price, 0, ',', '.') }}',
                                        rawPrice: '{{ (int)$menu->price }}',
                                        formatRupiah(val) {
                                            let num = val.replace(/[^0-9]/g, '');
                                            this.rawPrice = num;
                                            this.displayPrice = num ? new Intl.NumberFormat('id-ID').format(num) : '';
                                        }
                                    }">
                                        <label class="text-xs font-semibold text-coffee-600">Harga Menu</label>
                                        <div class="relative mt-1">
                                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-coffee-700 select-none">Rp</span>
                                            <input type="text"
                                                   x-model="displayPrice"
                                                   @input="formatRupiah($event.target.value)"
                                                   placeholder="15.000"
                                                   required
                                                   class="w-full border border-coffee-200 rounded-xl pl-10 pr-4 py-2.5 text-sm font-semibold text-coffee-800 focus:ring-2 focus:ring-coffee-400 focus:outline-none">
                                            <input type="hidden" name="price" :value="rawPrice">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-coffee-600">Deskripsi</label>
                                        <textarea name="description" rows="2" class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">{{ $menu->description }}</textarea>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-coffee-600">Ganti Foto (opsional)</label>
                                        <input name="photo" type="file" accept="image/*" class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                                    </div>
                                    <label class="flex items-center gap-2 text-sm text-coffee-600">
                                        <input type="hidden" name="is_available" value="0">
                                        <input type="checkbox" name="is_available" value="1" @checked($menu->is_available) class="rounded"> Menu tersedia
                                    </label>
                                    <div class="flex gap-3 pt-2">
                                        <button type="button" @click="editOpen = false" class="flex-1 border border-coffee-200 text-coffee-600 font-semibold py-2.5 rounded-xl text-sm">Batal</button>
                                        <button type="submit" class="flex-1 bg-coffee-800 hover:bg-coffee-900 text-white font-semibold py-2.5 rounded-xl text-sm">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-coffee-400">Belum ada menu.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
