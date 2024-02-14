<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Endpoint extends Model
{
    use HasFactory;

    const SOCIAL_MEDIA_TYPE_FACEBOOK = 0;
    const SOCIAL_MEDIA_TYPE_GOOGLE   = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'path',
        'verification_token',
        'page_access_token',
        'type',
        'page_access_token_expiration_date',
    ];

    protected $casts = [
        'page_access_token_expiration_date' => 'datetime',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    // protected $with = ['receivers'];

    public function connections(): HasMany
    {
        return $this->hasMany(Connection::class);
    }
}
