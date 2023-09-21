<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;

class LogReceiver extends Model
{
    use HasFactory, Actionable;

    protected $table = 'log_receiver';

    public function log()
    {
        return $this->belongsTo(Log::class);
    }

    public function receiver()
    {
        return $this->belongsTo(Receiver::class);
    }
}
