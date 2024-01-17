<?php

namespace App\Connector\Helpers\Endpoint;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\ConnectionLog;
use App\Models\Log as Logmodel;
use App\Models\LogDataFacebook;
use Illuminate\Support\Facades\Log;
use App\Models\ConnectionLogAttempt;
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
        
        $leadGenId = $data['entry'][0]['changes'][0]['value']['leadgen_id'];
        Log::info('LeagenId: ' . $leadGenId);
        $leadID = ConnectionLog::where('leadgen_id', $leadGenId)->get();

        // log model is not created when leadid already exists
        if($leadID->isNotEmpty())
        {
            Log::info('LeadgenId ' . $leadGenId . ' already exists.');
            return -1;
        }

        $log = Logmodel::create([
            'log_type' => Logmodel::LOG_TYPE_FACEBOOK,
        ]);

        $accessToken = $endpoint->page_access_token;
        Log::info('$accessToken: ' . $accessToken);
        $response = Http::get('https://graph.facebook.com/' . $leadGenId . '/', [
            'access_token' => $accessToken
        ]);

        Log::info('data_received: ' . $logMessage);
        Log::info('data_requested: ' . $response);

        $dataRequestedStatus = LogDataFacebook::DATA_REQUESTED_STATUS_SUCCESS;
        $dataRequested = json_decode($response, true);

        if(array_key_exists('error', $dataRequested))
        {
            $dataRequestedStatus = LogDataFacebook::DATA_REQUESTED_STATUS_FAIL;
        }

        $log_data_facebook = LogDataFacebook::create([
            'log_id' => $log->id,
            'data_received' => $logMessage,
            'data_requested' => $response,
            'data_requested_status' => $dataRequestedStatus,
        ]);

        Log::info('Log Data Facebook Saved: ' . $log_data_facebook);

        return (int) $log->id;
    }

    public function transformData($data, $logId)
    {
        $dataToSearch = [];
        $logDataFacebook = LogDataFacebook::where('log_id', '=', $logId)->first();
        $dataRequested = json_decode($logDataFacebook->data_requested, true);
        $dataReceived = json_decode($logDataFacebook->data_received);

        if(array_key_exists('field_data', $dataRequested))
        {
            $dataToSearch = $this->uppercaseNameValues($dataRequested['field_data']);
        }
        
        $leadDate = (string) Logmodel::find($logId)->created_at;
        $data['entry'][0]['changes'][0]['value']['leadgen_id'];
        if(array_key_exists('entry', $data) &&
            array_key_exists('changes', $data['entry'][0]) &&
            array_key_exists('value', $data['entry'][0]['changes'][0]) &&
            array_key_exists('ad_id', $data['entry'][0]['changes'][0]['value']))
        {
            $campaignID = $data['entry'][0]['changes'][0]['value']['ad_id'];
        }
        else
        {
            $campaignID = '';
            Log::info('Log with $logId: ' . $logId . ' has no ad_id');
        }

        if(array_key_exists('id', $dataRequested))
        {
            $leadID = $dataRequested['id'];
        }
        else
        {
            $leadID = '';
            Log::info('Log with $logId: ' . $logId . ' has no leadID');
        }

        if(array_key_exists('error', $dataRequested))
        {
            $leadID = $data['entry'][0]['changes'][0]['value']['leadgen_id'];
            $transformedData = [
                "Leadid" => (string)$leadID,
            ];

            return $transformedData;
        }

        $transformedData = [
            "Campaign_id"   => $this->map('Campaign', $dataToSearch),
            "Leadid"        => (string)$leadID,
            "FName"         => $this->map('FName', $dataToSearch),
            "LastName"      => $this->map('LastName', $dataToSearch),
            "LeadDate"      => $leadDate,
            "Email"         => $this->map('Email', $dataToSearch),
            "Mobile"        => substr($this->map('Mobile', $dataToSearch), 0, 18),
            "Brand"         => $this->map('Brand', $dataToSearch),
            "Model"         => $this->map('Model', $dataToSearch),
            "DealerCode"    => $this->map('DealerCode', $dataToSearch),
            "Engine"        => $this->map('Engine', $dataToSearch),
            "ContactReason" => $this->map('ContactReason', $dataToSearch),
            "Regnum"        => $this->map('Regnum', $dataToSearch),
        ];
        
        return $transformedData;
    }

    public function createConnectionLog($connection, $transformedData, $logId)
    {
        $leadGenId = $transformedData['Leadid'];
        $campaignID = array_key_exists('Campaign_id', $transformedData) ? $transformedData['Campaign_id'] : '';
        $connectionLogData = json_encode($transformedData, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        $status = ConnectionLog::STATUS_PENDING;
        if(count($transformedData) == 1)
        {
            $connectionLogData = 'NO DATA';
            $status = ConnectionLog::STATUS_FAIL_FROM_FACEBOOK;
        }

        $connectionLog = ConnectionLog::create([
            'connection_id' => $connection->id,
            'log_id' => $logId,
            'campaign_id' => $campaignID,
            'leadgen_id' => $leadGenId,
            'transformed_data' => $connectionLogData,
            'status' => $status,
        ]);

        return $connectionLog;
    }

    public function sendConnectionLog($connectionLog, $connection, $transformedData)
    {
        if($connectionLog->status === ConnectionLog::STATUS_FAIL_FROM_FACEBOOK)
        {
            return false;
        }
        
        $headerUsername = $connection->receiver->auth_data['Username'];
        $headerPassword = $connection->receiver->auth_data['Password'];
        
        $response = Http::withHeaders([
            'Username' => $headerUsername,
            'Password' => $headerPassword,
        ])->post($connection->receiver->url, $transformedData);

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

    public static function requestData($endpoint, $dataReceived)
    {
        $accessToken = $endpoint->page_access_token;
        $dataReceived = json_decode($dataReceived, true);
        $leadGenId = $dataReceived['entry'][0]['changes'][0]['value']['leadgen_id'];

        Log::info('$accessToken: ' . $accessToken);
        $response = Http::get('https://graph.facebook.com/' . $leadGenId . '/', [
            'access_token' => $accessToken
        ]);
        return $response;
        $dataRequested = json_decode($response, true);

        return $dataRequested;
    }

    private function map($key, $data): string
    {
        $map = [
            "Campaign" => "CAMPAIGN",
            "FName" => "FNAME",
            "LastName" => "LASTNAME",
            "Email" => "EMAIL",
            "Mobile" => "MOBILE",
            "Brand" => "BRAND",
            "Model" => "MODEL",
            "DealerCode" => "DEALERCODE",
            "ContactReason" => "CONTACTREASON",
            "Engine" => "ENGINE",
            "Regnum" => "REGNUM",
        ];

        foreach($data as $d)
        {
            if($map[$key] === $d['name'])
            {
                return $d['values'][0];
            }
        }

        return "";
    }

    private function uppercaseNameValues($data)
    {
        foreach($data as &$d)
        {
            $d['name'] = Str::upper($d['name']);
            $d['name'] = Str::replace(' ', '', $d['name']);
        }

        return $data;
    }
}