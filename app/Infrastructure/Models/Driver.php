<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = 'drivers';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'status',
        'document_verified_at',
    ];
}
