<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Driver;

interface DriverRepositoryInterface
{
    public function findById(string $id): ?Driver;
    public function updateStatusSafe(string $driverId, string $oldStatus, string $newStatus, array $data = []): ?Driver;
}
