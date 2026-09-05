<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'sync_date',
        'sync_type',
        'total_records',
        'success_records',
        'failed_records',
        'status',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'sync_date' => 'datetime',
            'total_records' => 'integer',
            'success_records' => 'integer',
            'failed_records' => 'integer',
        ];
    }
}
