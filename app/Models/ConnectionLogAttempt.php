<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConnectionLogAttempt extends Model
{
    use HasFactory;

    const STATUS_SUCCESS = [200, 201];
    const STATUS_SAMEID = 305;
    const STATUS_EMPTYLEADID = 400;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'connections_logs_id',
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

    public function connectionlog(): BelongsTo
    {
        return $this->belongsTo(ConnectionLog::class);
    }
}
