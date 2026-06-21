<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthController extends Controller
{
    private function getPrivateKey()
    {
        return file_get_contents(storage_path('oauth-private.key'));
    }

    private function getPublicKey()
    {
        return file_get_contents(storage_path('oauth-public.key'));
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $privateKey = $this->getPrivateKey();

        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // valid for 1 hour
        $payload = array(
            'iss' => config('app.url', 'http://localhost'),
            'sub' => $user->id,
            'aud' => 'velo-identity',
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'roles' => ['passenger'], // mock or get from DB
            'email' => $user->email,
        );

        $jwt = JWT::encode($payload, $privateKey, 'RS256', 'key-1');

        return response()->json([
            'access_token' => $jwt,
            'token_type' => 'bearer',
            'expires_in' => 3600,
            'refresh_token' => 'mock-refresh-token', // TODO: Implement persistent refresh token
        ]);
    }

    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        // TODO: Implement refresh logic verifying refresh token and issuing a new JWT
        return response()->json([
            'access_token' => '...',
            'refresh_token' => '...',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        // TODO: Implement logout logic (revoke refresh tokens)
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        return response()->json(['message' => 'Password reset email sent']);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        return response()->json(['message' => 'Password reset successfully']);
    }

    public function verify(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            $publicKey = $this->getPublicKey();
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

            return response()->json([
                'valid' => true,
                'user_id' => $decoded->sub,
                'roles' => $decoded->roles,
                'email' => $decoded->email ?? null,
                'exp' => $decoded->exp
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Invalid or expired token', 'message' => $e->getMessage()], 403);
        }
    }

    public function certs(): JsonResponse
    {
        $publicKeyString = $this->getPublicKey();
        $res = openssl_pkey_get_public($publicKeyString);
        $details = openssl_pkey_get_details($res);

        // Convert the RSA components to base64url format for JWKS
        $n = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($details['rsa']['n']));
        $e = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($details['rsa']['e']));

        $jwks = [
            'keys' => [
                [
                    'kty' => 'RSA',
                    'alg' => 'RS256',
                    'use' => 'sig',
                    'kid' => 'key-1',
                    'n' => $n,
                    'e' => $e,
                ]
            ]
        ];

        return response()->json($jwks);
    }
}
