<?php

namespace App\Console\Commands;

use App\Models\LogDataFacebook;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Connector\Helpers\Endpoint\EndpointHelperFacebook;
use App\Models\Endpoint;
use Carbon\Carbon;

class VerifyFacebookAccessToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verify-facebook-accesstoken';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $endpointsForVerification = Endpoint::where('type', Endpoint::SOCIAL_MEDIA_TYPE_FACEBOOK)->where('page_access_token_expiration_date', '<', Carbon::now()->addDays(50))->get();

        foreach($endpointsForVerification as $endpointForVerification)
        {
            Log::info($endpointForVerification->name);
        }
    }
}
