<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Entities\Driver as DriverEntity;
use App\Domain\Repositories\DriverRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Driver as DriverEloquentModel;

class EloquentDriverRepository implements DriverRepositoryInterface
{
    public function findById(string $id): ?DriverEntity
    {
        $model = DriverEloquentModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function updateStatusSafe(string $driverId, string $oldStatus, string $newStatus, array $data = []): ?DriverEntity
    {
        $affectedRows = DriverEloquentModel::where('id', $driverId)
            ->where('status', $oldStatus)
            ->update(array_merge(['status' => $newStatus], $data));

        if ($affectedRows === 0) {
            return null;
        }

        return $this->findById($driverId);
    }

    private function toEntity(DriverEloquentModel $model): DriverEntity
    {
        return new DriverEntity(
            id: $model->id,
            status: $model->status,
            document_verified_at: $model->document_verified_at
        );
    }
}
