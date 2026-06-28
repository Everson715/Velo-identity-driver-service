<?php

namespace App\Application\Http\Controllers;

use App\Domain\Services\DriverApprovalService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use OpenApi\Attributes as OA;

#[OA\Info(title: "Identity Driver API", version: "1.0.0")]
class DriverIdentityController extends Controller
{
    private DriverApprovalService $approvalService;

    public function __construct(DriverApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    #[OA\Post(
        path: '/api/drivers/{driverId}/approve',
        summary: 'Approve a driver',
        tags: ['Drivers'],
        description: 'Approves a driver. Protected against race conditions using Optimistic Locking.',
    )]
    #[OA\Parameter(
        name: 'driverId',
        in: 'path',
        required: true,
        description: 'The ID of the driver to approve',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 200,
        description: 'Driver successfully approved'
    )]
    #[OA\Response(
        response: 409,
        description: 'Conflict - Driver state could not be updated due to concurrent modifications',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Driver status could not be updated to 'approved' due to a conflict or invalid state.")
            ]
        )
    )]
    public function approveDriver(Request $request, string $driverId)
    {
        $this->approvalService->approveDriver($driverId);

        return response()->json(['message' => 'Driver successfully approved']);
    }
}
