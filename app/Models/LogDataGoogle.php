<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogDataGoogle extends Model
{
    use HasFactory;

    protected $table = 'log_data_google';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'log_id',
        'data_received',
    ];

    // protected $casts = [
    //     'data_received' => 'array',
    // ];

    public function log(): BelongsTo
    {
        return $this->belongsTo(Log::class);
    }
}
