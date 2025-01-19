<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends BaseController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return $this->sendResponse($user->toArray(), 'User register successfully.');
    }

    /**
     * @throws AuthenticationException
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendResponse("", 'Unauthorised', 401);
        }
        return $this->sendResponse($user->createToken('auth_token')->plainTextToken, 'User login successfully.', 200, "token");
    }

    /**
     * @throws AuthenticationException
     */
    public function logout(Request $request): JsonResponse
    {
        if (!$request->bearerToken()) {
            throw new AuthenticationException('Unauthenticated.');
        }
        $token = PersonalAccessToken::findToken($request->bearerToken());
        if (!$token) {
            throw new AuthenticationException('Token not found or expired.');
        }
        return $this->sendResponse([], 'User logout successfully.');
    }
}
