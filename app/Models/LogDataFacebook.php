<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogDataFacebook extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'log_id',
        'data_received',
        'data_requested',
        'data_requested_response',
        'data_requested_status',
    ];

    protected $casts = [
        'data_received' => 'array',
        'data_requested' => 'array',
    ];

    public function log(): BelongsTo
    {
        return $this->belongsTo(Log::class);
    }
}
