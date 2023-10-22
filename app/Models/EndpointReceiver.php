<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EndpointReceiver extends Model
{
    use HasFactory;

    protected $table = 'endpoints_receivers';

    public function logsreceivers(): HasMany
    {
        return $this->hasMany(LogReceiver::class, 'endpoints_receivers_id', 'id');
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
