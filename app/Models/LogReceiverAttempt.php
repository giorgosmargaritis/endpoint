<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogReceiverAttempt extends Model
{
    use HasFactory;

    const STATUS_SUCCESS = 200;
    const STATUS_SAMEID = 305;
    const STATUS_EMPTYLEADID = 400;

    protected $table = 'logs_receivers_attempts';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'logs_receivers_id',
        'status_code',
        'response',
    ];

    // /**
    //  * The attributes that should be cast.
    //  *
    //  * @var array
    //  */
    // protected $casts = [
    //     'response' => 'array',
    // ];

    public function logsreceivers(): BelongsTo
    {
        return $this->belongsTo(LogReceiver::class, 'logs_receivers_id');
    }
}
