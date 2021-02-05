<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ApiToken
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
        $cookie = $request->cookie('token');
        if (!$cookie) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $cookie = Crypt::decryptString($cookie);
        $cookie = explode('|', $cookie)[1];

        if (!User::whereToken($cookie)->first()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
