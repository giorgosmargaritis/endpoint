<?php

namespace App\Http\Controllers;

use App\Models\Endpoint;
use Illuminate\Http\Request;
use App\Models\Log as Logmodel;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    public function store(Endpoint $endpoint, Request $request)
    {
        $data = json_decode($request->getContent());
        $verification_token = $data->google_key ?? null;

        if($verification_token !== $endpoint->verification_token)
        {
            return response('Wrong verification token', 403);
        }

        $logMessage = print_r($data, true);

        $log = Logmodel::create([
            'data' => $logMessage,
            'endpoint_id' => $endpoint->id,
        ]);

        Log::info($logMessage);

        return response()->json(['status' => 'success']);
    }

}
