<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{

    function register(Request $request)
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

    function login(Request $request)
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

    function logout(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(["success" => false, "msg" => "Token não informado"], 400);
        }

        $access_token = PersonalAccessToken::findToken($token);

        if (!$access_token) {
            return response()->json(["success" => false, "msg" => "Token fornecido é invalido"], 400);
        }

        $access_token->delete();

        return response()->json(["success" => true, "msg" => "Logout realizado com sucesso"]);
    }
}
