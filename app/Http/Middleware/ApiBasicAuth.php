<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('sanctum')->check()) {
            $username = $request->getUser();
            $password = $request->getPassword();

            if ($username && $password) {
                $credentials = [
                    'email' => $username,
                    'password' => $password,
                ];

                if (Auth::attempt($credentials)) {
                    return $next($request);
                }
            }

            return response()->json([
                'message' => 'Unauthenticated'
            ], 401)->header('WWW-Authenticate', 'Basic realm="UptimeCore API"');
        }

        return $next($request);
    }
}

