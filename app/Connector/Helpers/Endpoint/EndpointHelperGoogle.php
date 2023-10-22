<?php

namespace App\Connector\Helpers\Endpoint;
use App\Models\Log as Logmodel;
use App\Models\LogDataGoogle;

class EndpointHelperGoogle extends AbstractEndpointHelper
{
    public function verifiedRequest($endpoint, $request) :bool
    {
        $data = json_decode($request->getContent());
        $googleKey = $data->google_key ?? null;

        if($googleKey !== $endpoint->verification_token)
        {
            return false;
        }

        return true;
    }

    public function createLogData($endpoint, $logMessage): int
    {
        $log = Logmodel::create([
            'endpoint_id' => $endpoint->id,
            'log_type' => Logmodel::LOG_TYPE_GOOGLE
        ]);

        $log_data_google = LogDataGoogle::create([
            'log_id' => $log->id,
            'data_received',
        ]);

        return (int) $log->id;
    }
}