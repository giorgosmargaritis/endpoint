<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogDataFacebook extends Model
{
    use HasFactory;

    const DATA_REQUESTED_STATUS_SUCCESS = 1;
    const DATA_REQUESTED_STATUS_FAIL = 2;

    protected $table = 'log_data_facebook';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'log_id',
        'data_received',
        'data_requested',
        'data_requested_status',
        'times_requested',
    ];

    public function log(): BelongsTo
    {
        return $this->belongsTo(Log::class);
    }
}
