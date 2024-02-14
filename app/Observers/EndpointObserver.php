<?php

namespace App\Observers;

use App\Models\Endpoint;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EndpointObserver
{
    /**
     * Handle the Endpoint "created" event.
     */
    public function created(Endpoint $endpoint): void
    {
        if(!is_null($endpoint->page_access_token))
        {
            $page_access_token_expiration_date = Carbon::now()->addDays(40);
            $endpoint->page_access_token_expiration_date = $page_access_token_expiration_date;
            $endpoint->saveQuietly();
        }
    }

    /**
     * Handle the Endpoint "updated" event.
     */
    public function updated(Endpoint $endpoint): void
    {
        if($endpoint->type === Endpoint::SOCIAL_MEDIA_TYPE_FACEBOOK)
        {
            $page_access_token_expiration_date = Carbon::now()->addDays(40);
            $endpoint->page_access_token_expiration_date = $page_access_token_expiration_date;
            $endpoint->saveQuietly();
        }
    }

    /**
     * Handle the Endpoint "deleted" event.
     */
    public function deleted(Endpoint $endpoint): void
    {
        //
    }

    /**
     * Handle the Endpoint "restored" event.
     */
    public function restored(Endpoint $endpoint): void
    {
        //
    }

    /**
     * Handle the Endpoint "force deleted" event.
     */
    public function forceDeleted(Endpoint $endpoint): void
    {
        //
    }
}
