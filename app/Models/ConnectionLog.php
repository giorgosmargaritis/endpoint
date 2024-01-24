<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConnectionLog extends Model
{
    use HasFactory;

    const STATUS_FAIL = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_PENDING = 2;
    const STATUS_FAIL_FROM_FACEBOOK = 4;

    protected $table = 'connections_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'connection_id',
        'log_id',
        'campaign_id',
        'leadgen_id',
        'transformed_data',
        'status',
        'is_test',
    ];

    public function connectionlogattempts(): HasMany
    {
        return $this->hasMany(ConnectionLogAttempt::class, 'connections_logs_id');
    }

    public function log(): BelongsTo
    {
        return $this->belongsTo(Log::class);
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(Connection::class);
    }
}
