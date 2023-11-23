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

    const TYPE_POWERAPP_SCHEMA = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'url',
        'authentication_method_id',
        'auth_data'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'auth_data' => 'array',
    ];

    public function connections(): HasMany
    {
        return $this->hasMany(Connection::class);
    }

    public function authenticationmethod(): BelongsTo
    {
        return $this->belongsTo(AuthenticationMethod::class);
    }
}
