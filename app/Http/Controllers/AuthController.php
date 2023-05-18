<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid email or password'
        ], 401);
    }

    $token = null;
    try {
        if (!$token = JWTAuth::fromUser($user)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
    } catch (JWTException $e) {
        return response()->json([
            'message' => 'Could not create token'
        ], 500);
    }

    return response()->json([
        'token' => $token,
        'user' => $user
    ]);
}
public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }

    $existingUser = User::where('email', $request->email)->first();
    if ($existingUser) {
        return response()->json([
            'message' => 'El correo electrónico ya ha sido registrado.',
        ], 400);
    }

    $user = User::create(
        array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        )
    );
    return response()->json([
        'message' => 'Usuario registrado correctamente',
        'user' => $user
    ], 201);
}
}