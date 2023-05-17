<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('App\Http\Middleware\AuthMiddleware');
    }
    public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users], 200);
    }

    public function userProfile($id) 
{
    $user = User::findOrFail($id);

    return response()->json([
        'profile' => $user
    ]);
}

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'string',
            'age' => 'integer',
            'email' => [
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ], [
                'email.unique' => 'correo electrónico ya en uso por otro usuario.',
            ]);;

        $user->update($validatedData);

        return response()->json(['message' => 'Usuario actualizado correctamente', $user], 200);
    }
    public function destroy(Request $request, $id)
{
    $user = User::find($id);
    $user->delete();
    return response()->json(['message' => 'Usuario eliminado correctamente', $user], 200);
}
}