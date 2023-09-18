<?php

namespace App\Http\Controllers;

use App\Models\Endpoint;
use Illuminate\Http\Request;
use App\Models\Log as Logmodel;
use Illuminate\Support\Facades\Log;

class GoogleLeadController extends Controller
{
    public function webhook(Request $request)
    {
        $data = json_decode($request->getContent());
        $verification_token = $data->google_key ?? null;

        $endpoint = Endpoint::where('verification_token', '=', $verification_token)->firstOrFail();

        $logMessage = print_r($data, true);

        $log = Logmodel::create([
            'data' => $logMessage,
            'endpoint_id' => $endpoint->id,
        ]);

        Log::info($logMessage);

        return response()->json(['status' => 'success']);
    }

}
