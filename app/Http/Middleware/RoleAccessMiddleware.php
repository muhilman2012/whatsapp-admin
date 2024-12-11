<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAccessMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        $user = Auth::guard('admin')->user();

        if ($role === 'analis') {
            // Analis hanya bisa melihat data yang diassign
            $laporanId = $request->route('laporan');
            if (!$user->laporans()->where('id', $laporanId)->exists()) {
                abort(403, 'Unauthorized action.');
            }
        } elseif (!in_array($user->role, explode('|', $role))) {
            // Role lain: periksa apakah role cocok
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
