<?php

namespace App\Observers;

use App\Models\AuthenticationMethod;
use App\Models\Receiver;
use Carbon\Carbon;

class ReceiverOberver
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
        if($receiver->authenticationmethod->type == AuthenticationMethod::TYPE_NOAUTH)
        {
            $receiver->auth_data = null;
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
