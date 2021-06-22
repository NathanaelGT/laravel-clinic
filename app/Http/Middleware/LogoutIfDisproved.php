<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutIfDisproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user?->is_valid === 0) {
            Auth::logout();

            $route = $request->route()->uri;

            if (!in_array($route, ['/', '/home', '/daftar'])) {
                return redirect(route('login'))
                    ->withInput(['email' => $user->email])
                    ->withErrors(['verify' => 'Akun ini tidak terverifikasi.']);
            }
        }

        return $next($request);
    }
}
