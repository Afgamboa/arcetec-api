<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next)
    {
        try {
            $token = $request->bearerToken();
            $payload = JWT::decode($token, 'secret_key', ['HS256']);
    
            $user = User::findOrFail($payload->sub);
            Auth::login($user);
    
            return $next($request);
        } catch (\Throwable $th) {
            // Si el token no es válido o el usuario no existe en la base de datos, retornamos una respuesta de error.
            return response()->json(['error' => 'Token inválido'], 401);
        }
    }
}
