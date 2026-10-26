<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userRole = Auth::user()->role;
        if (!empty($roles) && !in_array($userRole, $roles)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}
