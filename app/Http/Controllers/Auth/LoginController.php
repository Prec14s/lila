<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginController extends Controller
{
    protected array $homeRoutes = [
        'superadmin' => 'superadmin.dashboard',
        'owner' => 'owner.dashboard',
        'dapur' => 'dapur.dashboard',
    ];

    public function show(): View|RedirectResponse
    {
        if (Auth::check()) {
            $role = Auth::user()->role;
            if (isset($this->homeRoutes[$role])) {
                return redirect()->route($this->homeRoutes[$role]);
            }
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::lower($credentials['email']) . '|' . $request->ip();

        // 3 kali salah password -> kunci 1 menit (60 detik)
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withErrors(['email' => "Terlalu banyak percobaan login gagal. Silakan coba lagi dalam {$seconds} detik."])
                ->onlyInput('email');
        }

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            $attempts = RateLimiter::attempts($throttleKey);
            $remaining = 3 - $attempts;

            if ($remaining > 0) {
                $errorMsg = "Email atau kata sandi salah. Sisa percobaan: {$remaining} kali.";
            } else {
                $seconds = RateLimiter::availableIn($throttleKey);
                $errorMsg = "Terlalu banyak percobaan login gagal. Silakan coba lagi dalam {$seconds} detik.";
            }

            return back()->withErrors(['email' => $errorMsg])->onlyInput('email');
        }

        if (! $user->is_active) {
            return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi Super Admin.'])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        \App\Models\ActivityLog::record('Login', "Pengguna {$user->name} ({$user->roleLabel()}) berhasil masuk ke dalam sistem.", $user);

        $role = $user->role;
        $targetRoute = $this->homeRoutes[$role] ?? 'home';

        return redirect()->intended(route($targetRoute));
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            \App\Models\ActivityLog::record('Logout', "Pengguna {$user->name} ({$user->roleLabel()}) keluar dari sistem.", $user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
