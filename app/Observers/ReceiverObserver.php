<?php

namespace App\Observers;

use App\Models\AuthenticationMethod;
use App\Models\Receiver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReceiverObserver
{
    /**
     * Handle the Receiver "created" event.
     */
    public function created(Receiver $receiver): void
    {
        
    }

    /**
     * Handle the Receiver "updated" event.
     */
    public function updated(Receiver $receiver): void
    {
        Log::info($receiver->authenticationmethod->type);
        Log::info(AuthenticationMethod::TYPE_NOAUTH);
        Log::info($receiver->authenticationmethod->type === AuthenticationMethod::TYPE_NOAUTH);
        Log::info(($receiver->authenticationmethod->type) === AuthenticationMethod::TYPE_NOAUTH);
        Log::info((($receiver->authenticationmethod->type) === AuthenticationMethod::TYPE_NOAUTH));

        if(($receiver->authenticationmethod->type) === AuthenticationMethod::TYPE_NOAUTH)
        {
            Log::info('Here');
            $receiver->auth_data = null;
            $receiver->saveQuietly();
        }
    }

    /**
     * Handle the Receiver "deleted" event.
     */
    public function deleted(Receiver $receiver): void
    {
        //
    }

    /**
     * Handle the Receiver "restored" event.
     */
    public function restored(Receiver $receiver): void
    {
        //
    }

    /**
     * Handle the Receiver "force deleted" event.
     */
    public function forceDeleted(Receiver $receiver): void
    {
        //
    }
}
