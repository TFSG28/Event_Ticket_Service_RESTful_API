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
     * @Request({
     *     summary: Register a new user,
     *     description: Register a new user,
     *     tags: Auth
     * })
     * @Response({
     *     code: 200,
     *     description: User registered successfully,
     *     ref: User
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
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
     * @Request({
     *     summary: Login a user,
     *     description: Login a user,
     *     tags: Auth
     * })
     * @Response({
     *     code: 200,
     *     description: User login successfully,
     *     ref: User
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendResponse("", 'Wrong credentials', 401);
        }
        return $this->sendResponse($user->createToken('auth_token')->plainTextToken, 'User login successfully.', 200, "token");
    }

    /**
     * @Request({
     *     summary: Logout a user,
     *     description: Logout a user,
     *     tags: Auth
     * })
     * @Response({
     *     code: 200,
     *     description: User logout successfully,
     *     ref: User
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
     * @param Request $request
     * @return JsonResponse
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
        if (!$token->delete()) {
            return $this->sendResponse([], 'User logout failed.');
        }
        return $this->sendResponse([], 'User logout successfully.');
    }
}
