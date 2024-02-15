<?php

namespace App\Connector\Helpers;

use App\Models\AuthenticationMethod;
use App\Models\ConnectionLog;
use Illuminate\Support\Facades\Log;
use App\Models\ConnectionLogAttempt;
use Illuminate\Support\Facades\Http;

class ReceiverHelper
{
    public static function sendConnectionLog($connectionLog, $receiver)
    {
        if($connectionLog->status === ConnectionLog::STATUS_FAIL_FROM_FACEBOOK)
        {
            return false;
        }
        
        $httpHeaders = [];
        $transformedData = json_decode($connectionLog->transformed_data, true);

        switch ($receiver->authenticationmethod->type) {
            case AuthenticationMethod::TYPE_NOAUTH:
                $response = Http::post($receiver->url, $transformedData);
                break;
            
            case AuthenticationMethod::TYPE_HEADER:
                $httpHeaders = $receiver->auth_data;
                $response = Http::withHeaders($httpHeaders)->post($receiver->url, $transformedData);
                break;

            default:
                $response = Http::post($receiver->url, $transformedData);
                break;
        }
        
        

        $connectionLogAttempt = ConnectionLogAttempt::create([
            'connections_logs_id' => $connectionLog->id,
            'status_code' => $response->status(),
            'response' => $response,
        ]);

        Log::info('$connectionLogAttempt:' . $connectionLogAttempt);

        if(in_array($response->status(), ConnectionLogAttempt::STATUS_SUCCESS))
        {
            $connectionLog->status = ConnectionLog::STATUS_SUCCESS;
        }
        else
        {
            $connectionLog->status = ConnectionLog::STATUS_FAIL;
        }

        $connectionLog->saveQuietly();

        Log::info('$connectionLog FINAL:' . $connectionLog);

        return true;
    }
}