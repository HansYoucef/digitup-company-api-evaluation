<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            "name"     => "required",
            "email"    => "required|email|unique:users",
            "password" => "required|confirmed",
            "role"     => "required",
        ]);

        User::create([
            "name"     => $request->name,
            "email"    => $request->email,
            "password" => Hash::make($request->password),
            "role"     => $request->role,
        ]);

        return response()->json(["message" => "Utilisateur Inscrit."], status: 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            "email"    => "required|email|exists:users",
            "password" => "required",
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les informations d\'identification fournies sont incorrectes.'],
            ]);
        }

        return response()->json([
            'message' => 'Utilisateur Connecté',
            'token'   => $user->createToken($request->email)->plainTextToken,
        ], status: 200);
    }

    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Utilisateur Déconnecté',
            'check'   => auth()->check()
        ], status: 200);
    }
}
