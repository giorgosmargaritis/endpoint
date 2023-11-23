<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'log_type',
    ];

    public function connectionlogs(): HasMany
    {
        return $this->hasMany(ConnectionLog::class);
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
