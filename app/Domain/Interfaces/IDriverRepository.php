<?php

namespace App\Domain\Interfaces;

use App\Domain\Entities\Driver;

interface IDriverRepository
{
    public function findById(string $id): ?Driver;
    public function updateStatusSafe(string $driverId, string $oldStatus, string $newStatus, array $data = []): ?Driver;
}
