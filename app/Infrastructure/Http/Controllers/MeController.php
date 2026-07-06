<?php

namespace App\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use App\Infrastructure\Http\Requests\UpdateProfileRequest;
use App\Infrastructure\Http\Requests\UploadAvatarRequest;
use App\Infrastructure\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\JsonResponse;

class MeController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        // TODO: Implement get profile logic
        return response()->json(['user' => $request->user()]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        // TODO: Implement update profile logic
        return response()->json(['message' => 'Profile updated successfully']);
    }

    public function avatar(UploadAvatarRequest $request): JsonResponse
    {
        // TODO: Implement upload avatar logic
        return response()->json(['message' => 'Avatar uploaded successfully']);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        // TODO: Implement update password logic
        return response()->json(['message' => 'Password updated successfully']);
    }

    public function sessions(Request $request): JsonResponse
    {
        // TODO: Implement list sessions logic
        return response()->json(['sessions' => []]);
    }

    public function revokeSession($id): JsonResponse
    {
        // TODO: Implement revoke session logic
        return response()->json(['message' => 'Session revoked']);
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $user->delete();
            return response()->json(['message' => 'Account deleted successfully']);
        }
        return response()->json(['message' => 'User not found'], 404);
    }
}
