<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

/**
 * AuthController class
 * 
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Register a new user.
     * 
     * @param Request $request The request object.
     * @return JsonResponse The response object.
     */
    public function register(Request $request): JsonResponse
    {
        try {
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

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['message' => 'User registered successfully', 'user' => $user, 'access_token' => $token], 201);
        } catch (\Exception $exception) {
            return response()->json([$exception->getMessage()], 400);
        }
    }

    /**
     * Login a user.
     * 
     * @param Request $request The request object.
     * @return JsonResponse The response object.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['message' => 'Logged in successfully', 'user' => $user], 200);
        } catch (\Exception $exception) {
            return response()->json([$exception->getMessage()], 400);
        }
    }

    /**
     * Logout a user.
     * 
     * @param Request $request The request object.
     * @return JsonResponse The response object.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (\Exception $exception) {
            return response()->json([$exception->getMessage()], 400);
        }
    }
}
