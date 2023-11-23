<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Connection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'endpoint_id',
        'receiver_id',
    ];

    public function connectionslogs(): HasMany
    {
        return $this->hasMany(ConnectionLog::class);
    }

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(Endpoint::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Receiver::class);
    }
}
