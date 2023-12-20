<?php

namespace App\Connector\Helpers\Endpoint;

use App\Models\ConnectionLog;
use App\Models\Log as Logmodel;
use App\Models\LogDataGoogle;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EndpointHelperGoogle extends AbstractEndpointHelper
{
    public function verifiedRequest($endpoint, $request) :bool
    {
        $data = json_decode($request->getContent());
        
        $googleKey = $data->google_key ?? null;
        if($googleKey)
        {
            $explodedString = explode('_', $googleKey);
            $googleKey = $explodedString[0];
        }
        Log::info('$googleKey: ' . $googleKey);
        Log::info('$endpoint->verification_token' . $endpoint->verification_token);

        if($googleKey !== $endpoint->verification_token)
        {
            Log::info('Not verified in verified request.');
            return false;
        }

        return true;
    }

    public function createLogData($endpoint, $data): int
    {
        $logData = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

        Log::info('$logData Pretty: ' . $logData);

        $leadGenId = $data['lead_id'];

        $leadID = ConnectionLog::where('leadgen_id', $leadGenId)->get();

        // log model is not created when leadid already exists
        if($leadID->isNotEmpty())
        {
            Log::info('LeadgenId ' . $leadGenId . ' already exists.');
            return -1;
        }
        
        $log = Logmodel::create([
            'log_type' => Logmodel::LOG_TYPE_GOOGLE
        ]);

        $log_data_google = LogDataGoogle::create([
            'log_id' => $log->id,
            'data_received' => $logData,
        ]);

        Log::info('$logDataGoogle: ' . $log_data_google);

        return (int) $log->id;
    }

    public function transformData($data, $logId)
    {
        $leadDate = (string) Logmodel::find($logId)->created_at;
        $dataToSearch = $this->uppercaseColumnIdValues($data['user_column_data']);
        $explodedString = explode('_', $data['google_key']);
        $brand = array_key_exists(1, $explodedString) ? $explodedString[1] : $this->map('Brand', $dataToSearch);;
        $model = array_key_exists(2, $explodedString) ? $explodedString[2] : $this->map('Model', $dataToSearch);
        
        if(array_key_exists('campaign_id', $data))
        {
            $campaignID = $data['campaign_id'];
        }
        else
        {
            $campaignID = '';
            Log::info('Log with $logId: ' . $logId . ' has no campaign_id');
        }

        if(array_key_exists('lead_id', $data))
        {
            $leadID = $data['lead_id'];
        }
        else
        {
            $leadID = '';
            Log::info('Log with $logId: ' . $logId . ' has no lead_id');
        }

        $transformedData = [
            "Campaign_id" => (string)$campaignID,
            "Leadid" => (string)$leadID,
            "FName" => $this->map('FName', $dataToSearch),
            "LastName" => $this->map('LastName', $dataToSearch),
            "LeadDate" => $leadDate,
            "Email" => $this->map('Email', $dataToSearch),
            "Mobile" => $this->map('Mobile', $dataToSearch),
            "Brand" => $brand,
            "Model" => $model,
            "DealerCode" => $this->map('DealerCode', $dataToSearch),
            "ContactReason" => $this->map('ContactReason', $dataToSearch),
            "Regnum" => $this->map('Regnum', $dataToSearch),
            "Engine" => $this->map('Engine', $dataToSearch),
        ];

        return $transformedData;
    }

    public function createConnectionLog($connection, $transformedData, $logId)
    {
        $connectionLogData = json_encode($transformedData, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

        Log::info('Transformed data pretty:' . $connectionLogData);

        $connectionLog = ConnectionLog::create([
            'connection_id' => $connection->id,
            'log_id' => $logId,
            'campaign_id' => $transformedData['Campaign_id'],
            'leadgen_id' => $transformedData['Leadid'],
            'transformed_data' => $connectionLogData,
            'status' => ConnectionLog::STATUS_PENDING,
        ]);

        Log::info('$connectionLog:' . $connectionLog);

        return $connectionLog;
    }

    private function map($key, $data): string
    {
        $map = [
            "Campaign_id" => "campaign_id",
            "Leadid" => "lead_id",
            "FName" => "FIRST_NAME",
            "LastName" => "LAST_NAME",
            "Email" => "EMAIL",
            "Mobile" => "PHONE_NUMBER",
            "Brand" => "",
            "Model" => "ΠΟΙΟ_ΜΟΝΤΕΛΟ_ΣΑΣ_ΕΝΔΙΑΦΕΡΕΙ;",
            "DealerCode" => "ΠΟΙΑ_ΑΝΤΙΠΡΟΣΩΠΕΙΑ_ΠΡΟΤΙΜΑΤΕ;",
            "ContactReason" => "ΠΟΙΑ_ΥΠΗΡΕΣΙΑ_ΣΑΣ_ΕΝΔΙΑΦΕΡΕΙ;",
            "Regnum" => "REGNUM",
            "Engine" => "ΠΟΙΟΣ_ΤΥΠΟΣ_ΟΧΗΜΑΤΟΣ_ΣΑΣ_ΕΝΔΙΑΦΕΡΕΙ;",
        ];

        foreach($data as $d)
        {
            if($map[$key] === $d['column_id'])
            {
                return $d['string_value'];
            }
        }

        return "";
    }

    private function uppercaseColumnIdValues($data): array
    {
        $greek = array(
            'Ά', 'Ί', 'Ύ', 'Έ', 'Ό', 'Ή', 'Ώ', 'Ϊ', 'Ϋ',
        );
        $simpleGreek = array(
            'Α', 'Ι', 'Υ', 'Ε', 'Ο', 'Η', 'Ω', 'Ι', 'Υ',
        );
        foreach($data as &$d)
        {
            $d['column_id'] = Str::upper($d['column_id']);
            $d['column_id'] = Str::replace($greek, $simpleGreek, $d['column_id']);
        }

        return $data;
    }
}