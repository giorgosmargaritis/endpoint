<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuthenticationMethod extends Model
{
    use HasFactory;

    const TYPE_NOAUTH = 0;
    const TYPE_HEADER = 1;

    // protected $casts = [
    //     'auth_data' => 'array'
    // ];

    public function receivers(): HasMany
    {
        return $this->hasMany(Receiver::class);
    }
}
