<?php

namespace App\Connector\Helpers\Endpoint;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\ConnectionLog;
use App\Models\Log as Logmodel;
use App\Models\LogDataFacebook;
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
        $dataToSearch = [];
        $dataRequested = json_decode(LogDataFacebook::where('log_id', '=', $logId)->first()->data_requested, true);
        if(array_key_exists('field_data', $dataRequested))
        {
            $dataToSearch = $this->uppercaseNameValues($dataRequested['field_data']);
        }
        
        $leadDate = (string) Logmodel::find($logId)->created_at;
        
        if(array_key_exists('entry', $data) &&
            array_key_exists('changes', $data['entry']) &&
            array_key_exists('value', $data['entry']['changes']) &&
            array_key_exists('ad_id', $data['entry']['changes']['value']))
        {
            $campaignID = $data['entry']['changes']['value']['ad_id'];
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