@extends('layouts.admin')

@section('content')
@php $title = 'Kelola Akun'; @endphp

<div x-data="{ open: false }" class="mb-6">
    <button @click="open = true" class="btn-press bg-coffee-800 hover:bg-coffee-900 text-white font-semibold px-5 py-2.5 rounded-xl text-sm">
        + Tambah Akun
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-black/50" @click="open = false" x-transition.opacity></div>
        <div x-show="open" x-transition class="relative bg-white w-full max-w-sm rounded-3xl p-6 shadow-2xl">
            <h3 class="font-bold text-lg text-coffee-800 mb-4">Tambah Akun</h3>
            <form action="{{ route('superadmin.accounts.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Role</label>
                    <select name="role" required class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                        <option value="owner">👑 Owner</option>
                        <option value="dapur">🍳 Dapur</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Nama</label>
                    <input name="name" type="text" required class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Email</label>
                    <input name="email" type="email" required class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-coffee-600">No. Telepon (opsional)</label>
                    <input name="phone" type="text" class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Kata Sandi</label>
                    <input name="password" type="password" required minlength="6" class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
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
                <th class="px-5 py-3 text-left">Nama</th>
                <th class="px-5 py-3 text-left">Email</th>
                <th class="px-5 py-3 text-left">Role</th>
                <th class="px-5 py-3 text-left">Status</th>
                <th class="px-5 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-coffee-50">
            @forelse($accounts as $account)
                <tr class="hover:bg-coffee-50/60 transition">
                    <td class="px-5 py-3 font-semibold text-coffee-800">{{ $account->name }}</td>
                    <td class="px-5 py-3 text-coffee-600">{{ $account->email }}</td>
                    <td class="px-5 py-3 capitalize">{{ $account->role }}</td>
                    <td class="px-5 py-3">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $account->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $account->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right space-x-2 whitespace-nowrap">
                        <form action="{{ route('superadmin.accounts.toggle', $account) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button class="btn-press text-xs font-semibold px-3 py-1.5 rounded-lg border border-coffee-200 text-coffee-600 hover:bg-coffee-50">
                                {{ $account->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        <form action="{{ route('superadmin.accounts.destroy', $account) }}" method="POST" class="inline" onsubmit="return confirm('Hapus akun ini?')">
                            @csrf @method('DELETE')
                            <button class="btn-press text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-coffee-400">Belum ada akun.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
