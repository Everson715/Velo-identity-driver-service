<?php

namespace App\Domain\Services;

use App\Domain\Interfaces\IDriverRepository;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class DriverApprovalService
{
    private IDriverRepository $driverRepository;

    public function __construct(IDriverRepository $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    }

    public function updateStatus(string $driverId, string $oldStatus, string $newStatus, array $data = []): void
    {
        $driver = $this->driverRepository->updateStatusSafe($driverId, $oldStatus, $newStatus, $data);

        if (!$driver) {
            throw new ConflictHttpException("Driver status could not be updated to '{$newStatus}' due to a conflict or invalid state.");
        }
    }

    public function approveDriver(string $driverId): void
    {
        $this->updateStatus($driverId, 'pending', 'approved', [
            'document_verified_at' => now()->toDateTimeString()
        ]);
    }

    public function rejectDriver(string $driverId): void
    {
        $this->updateStatus($driverId, 'pending', 'rejected');
    }

    public function suspendDriver(string $driverId): void
    {
        $this->updateStatus($driverId, 'approved', 'suspended');
    }
}
