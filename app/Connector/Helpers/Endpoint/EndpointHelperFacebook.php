<?php

namespace App\Connector\Helpers\Endpoint;
use App\Models\ConnectionLog;
use App\Models\Log as Logmodel;
use App\Models\LogDataFacebook;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EndpointHelperFacebook extends AbstractEndpointHelper
{
    public function verifiedRequest($endpoint, $request): bool
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

            return true;
        }
        

        // case of POST request method, check if the endpoint is already verified
        if(request()->isMethod('POST'))
        {
            return true;
        }
        
    }

    public function createLogData($endpoint, $data): int
    {
        $logMessage = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        
        $log = Logmodel::create([
            'log_type' => Logmodel::LOG_TYPE_FACEBOOK,
        ]);

        $leadGenId = $data['entry'][0]['changes'][0]['value']['leadgen_id'];
        Log::info('LeagenId: ' . $leadGenId);
        $accessToken = 'EAAJg0XJD27IBOZCnFvl9i7MkOWTOL2gfyFWk2XZCuRxlaNDvEJnod6aX3PwM3TT0uN4j4AuVgK1zSfLZCe9Kpr2NpFXSC2Vj1yn8Hn2PjcnjrrmznYJjo1T38f7aRHUaebUcOqq2kZBRe96kxnOh0UamxboSBBIGLXhhCCOdH2LEZAQWJoD8ZAbFpUEsuEZCK8ZD';
        $response = Http::get('https://graph.facebook.com/' . $leadGenId . '/', [
            'access_token' => $accessToken
        ]);

        Log::info('data_received: ' . $logMessage);
        Log::info('data_requested: ' . $response);

        $log_data_facebook = LogDataFacebook::create([
            'log_id' => $log->id,
            'data_received' => $logMessage,
            'data_requested' => $response,
            'data_requested_status' => 1,
        ]);

        Log::info('Log Data Facebook Saved: ' . $log_data_facebook);

        return (int) $log->id;
    }

    public function transformData($data, $logId)
    {
        $dataRequested = json_decode(LogDataFacebook::where('log_id', '=', $logId)->first()->data_requested, true);
        $leadDate = (string) Logmodel::find($logId)->created_at;
        Log::info('LeadDate: ' . $leadDate);
        $transformedData = [
            "Campaign_id" => "",
            "Leadid" => $dataRequested['id'],
            "FName" => $dataRequested['field_data'][6]['values'][0],
            "LastName" => $dataRequested['field_data'][7]['values'][0],
            "LeadDate" => $leadDate,
            "Email" => $dataRequested['field_data'][9]['values'][0],
            "Mobile" => "6970707070",
            "Brand" => $dataRequested['field_data'][1]['values'][0],
            "Model" => $dataRequested['field_data'][0]['values'][0],
            "DealerCode" => $dataRequested['field_data'][2]['values'][0],
            "ContactReason" => $dataRequested['field_data'][3]['values'][0],
            "Regnum" => $dataRequested['field_data'][5]['values'][0],
        ];
        
        return $transformedData;
    }

    public function createConnectionLog($connection, $transformedData, $logId)
    {
        $connectionLogData = json_encode($transformedData, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

        $connectionLog = ConnectionLog::create([
            'connection_id' => $connection->id,
            'log_id' => $logId,
            'campaign_id' => $transformedData['Campaign_id'],
            'leadgen_id' => $transformedData['Leadid'],
            'transformed_data' => $connectionLogData,
            'status' => ConnectionLog::STATUS_PENDING,
        ]);

        return $connectionLog;
    }
}