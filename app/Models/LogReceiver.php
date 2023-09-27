<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Nova\Actions\Actionable;

class LogReceiver extends Model
{
    use HasFactory, Actionable;

    const STATUS_FAIL = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_PENDING = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'log_id',
        'receiver_id',
        'status',
    ];

    protected $table = 'logs_receivers';

    public function logsreceiversattempts(): HasMany
    {
        return $this->hasMany(LogReceiverAttempt::class, 'logs_receivers_id', 'id');
    }

    public function log(): BelongsTo
    {
        return $this->belongsTo(Log::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Receiver::class);
    }
}
