<?php

namespace App\Http\Controllers;

use App\Endpoint\Verification\EndpointVerificationRules;
use App\Models\Endpoint;
use Illuminate\Http\Request;
use App\Models\Log as Logmodel;
use Illuminate\Support\Facades\Log;

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
            $receiver->logs()->attach($log->id, ['status' => 0]);
        }

        Log::info($logMessage);

        return response()->json(['status' => 'success']);
    }

}
