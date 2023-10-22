<?php

namespace App\Connector\Helpers\Endpoint;
use App\Models\Log as Logmodel;
use App\Models\LogDataFacebook;

use Illuminate\Support\Facades\Log;

class EndpointHelperFacebook extends AbstractEndpointHelper
{
    public function verifiedRequest($endpoint, $request)
    {
        // case of GET request method, begin the verification process
        if(request()->isMethod('GET'))
        {
            $token = $endpoint->verification_token;
            $verifyToken = $request->input('hub_verify_token') ?? null;
            if($verifyToken !== $token)
            {
                return false;
            }
            Log::info('Verify Token: ' . $verifyToken);
            Log::info('Hub Challenge: '. $request->input('hub_challenge'));
            $hubChallenge = $request->input('hub_challenge');
            return true;
        }
        

        // case of POST request method, check if the endpoint is already verified
        if(request()->isMethod('POST'))
        {
            Log::info('Verified POST Request');
            return true;
        }
        
    }

    public function createLogData($endpoint, $logMessage): int
    {
        $log = Logmodel::create([
            'endpoint_id' => $endpoint->id,
            'log_type' => Logmodel::LOG_TYPE_GOOGLE,
        ]);

        $log_data_google = LogDataFacebook::create([
            'log_id' => $log->id,
            'data_received',
        ]);

        return (int) $log->id;
    }
}