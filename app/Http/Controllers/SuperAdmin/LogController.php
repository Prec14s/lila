<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('superadmin.logs.index', compact('logs'));
    }

    public function clear(): RedirectResponse
    {
        $count = ActivityLog::count();
        ActivityLog::truncate();

        ActivityLog::record('Hapus Log', "SuperAdmin membersihkan {$count} data log aktivitas pengguna.");

        return back()->with('success', "Berhasil menghapus {$count} log aktivitas pengguna.");
    }
}
