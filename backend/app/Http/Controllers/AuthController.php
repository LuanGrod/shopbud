<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email:rfc,dns|unique:users",
            "password" => "required|min:8|confirmed"
        ]);

        $user = User::create([
            "name" => $validated["name"],
            "email" => $validated["email"],
            "password" => Hash::make($validated["password"])
        ]);

        $token = $user->createToken("api-token", ["*"])->plainTextToken;

        return response()->json(["success" => true, "user" => $user, "token" => $token], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "email" => "required|email:rfc,dns",
            "password" => "required|min:8"
        ]);

        if (Auth::attempt($validated)) {
            $user = User::where("email", $validated["email"])->first();

            $token = $user->createToken("api-token", ["*"])->plainTextToken;

            return response()->json(["success" => true, "token" => "$token"]);
        }

        return response()->json(["success" => false, "msg" => "Usuário não encontrado"], 401);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(["success" => true, "msg" => "Logout realizado com sucesso"]);
    }
}
