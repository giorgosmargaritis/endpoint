<?php

namespace App\Http\Controllers;

use App\Models\Endpoint;
use App\Models\LogReceiver;
use Illuminate\Http\Request;
use App\Models\Log as Logmodel;
use App\Models\LogReceiverAttempt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Connector\Helpers\Endpoint\AbstractEndpointHelper;

class LeadController extends Controller
{
    public function index(Endpoint $endpoint, Request $request)
    {
        $endpointHelper = AbstractEndpointHelper::getInstance($endpoint->type);

        if(!$endpointHelper->hasReceivers($endpoint))
        {
            return response('No receivers set', 403);
        }

        if(!$endpointHelper->verifiedRequest($endpoint, $request))
        {
            return response('Not Verified Request', 403);
        }
        return response($request->input('hub_challenge'), 200);
    }

    public function store(Endpoint $endpoint, Request $request)
    {
        $endpointHelper = AbstractEndpointHelper::getInstance($endpoint->type);

        if(!$endpointHelper->hasReceivers($endpoint))
        {
            return response('No receivers set', 403);
        }

        if(!$endpointHelper->verifiedRequest($endpoint, $request))
        {
            return response('Not Verified Request', 403);
        }
        
        $data = json_decode($request->getContent());

        $endpointsReceivers = $endpoint->receiversendpoints;

        $logMessage = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

        $log_id = $endpointHelper->createLogData($endpoint, $logMessage);

        foreach($endpointsReceivers as $endpointReceiver)
        {
            $logReceiverMessage = print_r($data, true);
            $sendData = [
                "Campaign_id" => "FB_45678_Taigo_01",
                "Leadid" => "56547ΓΔΦΓ1128",
                "FName" => "Anna",
                "LastName" => "Kotzamani",
                "LeadDate" => "12/9/2023",
                "Email" => "anakot@kosmocar.gr",
                "Mobile" => "6946325145",
                "Brand" => "vw",
                "Model" => "Taigo",
                "DealerCode" => "501",
                "ContactReason" => "Offer",
                "Regnum" => "YMH7945",
                // "Campaign_id" => $data->campaign_id,
                // "Leadid" => $data->lead_id,
                // "FName" => $data->user_column_data->FULL_NAME,
                // "LastName" => $data->user_column_data->FULL_NAME,
                // "LeadDate" => "12/9/2023",
                // "Email" => $data->user_column_data->FULL_NAME,
                // "Mobile" => $data->user_column_data->FULL_NAME,
                // "Model" => $data->user_column_data->FULL_NAME,
                // "DealerCode" => "501",
                // "ContactReason" => "Offer",
                // "Regnum" => "YMH7945"
            ];

            $sendDataSaved = json_encode($sendData, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

            $logReceiver = LogReceiver::create([
                'log_id'                 => $log_id,
                'endpoints_receivers_id' => $endpointReceiver->id,
                'status'                 => LogReceiver::STATUS_PENDING,
                'transformed_data'       => $sendDataSaved,
            ]);

            Log::info($logReceiver);

            $headerUsername = $endpointReceiver->receiver->auth_data['Username'];
            $headerPassword = $endpointReceiver->receiver->auth_data['Password'];

            $response = Http::withHeaders([
                'Username' => $headerUsername,
                'Password' => $headerPassword,
            ])->post($endpointReceiver->receiver->url, $sendData);

            $logReceiverAttempt = LogReceiverAttempt::create([
                'logs_receivers_id' => $logReceiver->id,
                'status_code' => $response->status(),
                'response' => $response
            ]);

            Log::info($response->status());

            if(in_array($response->status(), LogReceiverAttempt::STATUS_SUCCESS))
            {
                $logReceiver->status = LogReceiver::STATUS_SUCCESS;
            }
            else
            {
                $logReceiver->status = LogReceiver::STATUS_FAIL;
            }

            $logReceiver->saveQuietly();

            // $log_receiver = LogReceiver::find($log_receiver_id);
            // SendLog::dispatch($log_receiver);
        }

        Log::info($logMessage);

        return response()->json(['status' => 'success']);
    }

}
