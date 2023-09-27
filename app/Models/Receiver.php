<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receiver extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'url',
    ];

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(Endpoint::class);
    }

    public function authenticationmethod(): BelongsTo
    {
        return $this->belongsTo(AuthenticationMethod::class);
    }

    public function logs(): BelongsToMany
    {
        return $this->belongsToMany(Log::class, 'logs_receivers')->withPivot('status')->withTimestamps();
    }

    public function logsreceivers(): HasMany
    {
        return $this->hasMany(LogReceiver::class);
    }
}
