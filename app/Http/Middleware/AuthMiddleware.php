<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;
use \App\Models\User;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        
        if (!$token) {
            return response()->json(['error' => 'Necesita Autorizacion'], 401);
        }

        preg_match('/Bearer\s(\S+)/', $token, $matches);
        if (!isset($matches[1])) {
            return response()->json(['error' => 'Token de Autorizacion Invalido'], 401);
        }

        $tokenValue = $matches[1];
        $token = new Token($tokenValue);
        try {
            $decoded = JWTAuth::decode($token, env('JWT_SECRET'), ['HS256']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Inauthorized'], 401);
        }
        $user = User::find($decoded['sub']);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 401);
        }

        Auth::login($user);

        return $next($request);
    }
}