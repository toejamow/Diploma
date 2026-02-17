<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function register(RegistrationRequest $request): JsonResponse
    {
        $user = User::query()->create($request->validated());

        return response()->json([
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'code' => 201,
                'message' => 'Пользователь создан'
            ]
        ], 201);
    }

    public function login(AuthRequest $request) {
        if (auth()->attempt($request->validated())) {
            $user = auth()->user();

            return [
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $user->createToken('api')->plainTextToken,
            ];
        }

        return response()->json([
            "code" => 401,
            "message" => "Login failed"
        ], 401);
    }

    public function logout() {
        auth()->user()->currentAccessToken()->delete();
        return response()->noContent();
    }
}
