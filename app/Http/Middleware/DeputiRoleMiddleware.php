<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeputiRoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        $user = Auth::guard('admin')->user();

        if ($user->role !== $role) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
