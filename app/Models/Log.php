<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Log extends Model
{
    use HasFactory;

    const LOG_TYPE_FACEBOOK = 0;
    const LOG_TYPE_GOOGLE = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'endpoint_id',
        'log_type',
    ];

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(Endpoint::class);
    }

    public function receivers(): BelongsToMany
    {
        return $this->belongsToMany(Receiver::class, 'logs_receivers')->withPivot('status')->withTimestamps();
    }

    public function log_data_facebook(): HasOne
    {
        return $this->hasOne(LogDataFacebook::class);
    }

    public function log_data_google(): HasOne
    {
        return $this->hasOne(LogDataGoogle::class);
    }
}
