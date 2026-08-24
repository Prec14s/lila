<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        $accounts = User::whereIn('role', ['owner', 'dapur'])->latest()->get();

        return view('superadmin.accounts.index', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:owner,dapur'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        $user = User::create($validated);

        \App\Models\ActivityLog::record(
            'Buat Akun',
            "SuperAdmin membuat akun baru: {$user->name} ({$user->email}) dengan role {$user->roleLabel()}."
        );

        return back()->with('success', 'Akun berhasil ditambahkan.');
    }

    public function update(Request $request, User $account): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,'.$account->id],
            'password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $account->update($validated);

        \App\Models\ActivityLog::record(
            'Edit Akun',
            "SuperAdmin memperbarui data akun: {$account->name} ({$account->email})."
        );

        return back()->with('success', 'Akun berhasil diperbarui.');
    }

    public function toggle(User $account): RedirectResponse
    {
        $account->update(['is_active' => ! $account->is_active]);

        $statusStr = $account->is_active ? 'mengaktifkan' : 'menonaktifkan';
        \App\Models\ActivityLog::record(
            'Status Akun',
            "SuperAdmin {$statusStr} akun: {$account->name} ({$account->email})."
        );

        return back()->with('success', 'Status akun diperbarui.');
    }

    public function destroy(User $account): RedirectResponse
    {
        $name = $account->name;
        $email = $account->email;
        $account->delete();

        \App\Models\ActivityLog::record(
            'Hapus Akun',
            "SuperAdmin menghapus akun: {$name} ({$email})."
        );

        return back()->with('success', 'Akun berhasil dihapus.');
    }
}
