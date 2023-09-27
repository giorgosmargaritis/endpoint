<?php

namespace App\Http\Controllers;

use App\Models\Endpoint;
use App\Models\LogReceiver;
use Illuminate\Http\Request;
use App\Nova\Actions\SendLog;
use App\Models\Log as Logmodel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Endpoint\Verification\EndpointVerificationRules;
use App\Models\LogReceiverAttempt;

class LeadController extends Controller
{
    public function index()
    {
        // $verificationRules = EndpointVerificationRules::getInstance();
    }

    public function store(Endpoint $endpoint, Request $request)
    {
        
        $data = json_decode($request->getContent());
        $verification_token = $data->google_key ?? null;

        // Log::info($endpoint->receivers);
        $receivers = $endpoint->receivers;

        if(!$receivers)
        {
            return response('No receivers set', 403);
        }

        if($verification_token !== $endpoint->verification_token)
        {
            return response('Wrong verification token', 403);
        }

        $logMessage = print_r($data, true);

        $log = Logmodel::create([
            'data' => $logMessage,
            'endpoint_id' => $endpoint->id,
        ]);

        foreach($receivers as $receiver)
        {
            $logReceiver = LogReceiver::create([
                'log_id'      => $log->id,
                'receiver_id' => $receiver->id,
                'status'      => LogReceiver::STATUS_PENDING,
            ]);

            Log::info($logReceiver);

            $headerUsername = $receiver->authenticationmethod->data['Username'];
            $headerPassword = $receiver->authenticationmethod->data['Password'];
            
            $response = Http::withHeaders([
                'Username' => $headerUsername,
                'Password' => $headerPassword,
            ])->post($receiver->url, [
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
                "Regnum" => "YMH7945"
            ]);

            $logReceiverAttempt = LogReceiverAttempt::create([
                'logs_receivers_id' => $logReceiver->id,
                'status_code' => $response->status(),
                'response' => $response
            ]);

            Log::info($response->status());

            if($response->status() === LogReceiverAttempt::STATUS_SUCCESS)
            {
                $logReceiver->status = LogReceiver::STATUS_SUCCESS;
            }

            if($response->status() === LogReceiverAttempt::STATUS_SAMEID || $response->status() === LogReceiverAttempt::STATUS_EMPTYLEADID)
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
