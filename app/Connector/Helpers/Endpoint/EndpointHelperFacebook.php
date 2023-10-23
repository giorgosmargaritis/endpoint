<?php

namespace App\Connector\Helpers\Endpoint;
use App\Models\Log as Logmodel;
use App\Models\LogDataFacebook;
use Illuminate\Support\Facades\Http;

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
            'log_type' => Logmodel::LOG_TYPE_FACEBOOK,
        ]);

        $log_data_facebook = LogDataFacebook::create([
            'log_id' => $log->id,
            'data_received' => $logMessage,
        ]);

        $leadgen_id = $log_data_facebook->data_received['entry'][0][0]['value']['leadgen_id'];
        $access_token = 'EAAJg0XJD27IBOZCnFvl9i7MkOWTOL2gfyFWk2XZCuRxlaNDvEJnod6aX3PwM3TT0uN4j4AuVgK1zSfLZCe9Kpr2NpFXSC2Vj1yn8Hn2PjcnjrrmznYJjo1T38f7aRHUaebUcOqq2kZBRe96kxnOh0UamxboSBBIGLXhhCCOdH2LEZAQWJoD8ZAbFpUEsuEZCK8ZD';

        $response = Http::get('https://graph.facebook.com/' . $leadgen_id . '/', [
            'access_token' => $access_token
        ]);

        Log::info($response);

        return (int) $log->id;
    }
}