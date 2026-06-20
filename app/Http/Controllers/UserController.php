<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Requests\ResendVerificationRequest;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function register(RegisterUserRequest $request): JsonResponse
    {
        // TODO: Implement user registration logic
        return response()->json(['message' => 'User registered successfully'], 201);
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
