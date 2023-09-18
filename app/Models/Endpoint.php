<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Endpoint extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $filable = [
        'name',
        'verification_token',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    public function receivers(): HasMany
    {
        return $this->hasMany(Receiver::class);
    }
}
