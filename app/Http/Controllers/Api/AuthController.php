<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Handle user login and return an API token.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Les identifiants sont incorrects.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('login-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->only(['id', 'name', 'email']),
        ], Response::HTTP_OK);
    }

    /**
     * Get the authenticated user info.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ], Response::HTTP_OK);
    }

    /**
     * Logout the current user by deleting the current access token.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Déconnexion réussie.',
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la déconnexion.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
