<?php

namespace App\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use App\Infrastructure\Http\Requests\RegisterUserRequest;
use App\Infrastructure\Http\Requests\VerifyEmailRequest;
use App\Infrastructure\Http\Requests\ResendVerificationRequest;
use Illuminate\Http\JsonResponse;
use App\Infrastructure\Persistence\Eloquent\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['role'] = $data['role'] ?? 'passenger';

        $user = User::create($data);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }

    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        // TODO: Implement email verification logic
        return response()->json(['message' => 'Email verified successfully']);
    }

    public function resendVerification(ResendVerificationRequest $request): JsonResponse
    {
        // TODO: Implement resend verification logic
        return response()->json(['message' => 'Verification email sent']);
    }
}
