<?php

namespace App\Domain\Entities;

class Driver
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly ?string $document_verified_at = null
    ) {}
}
