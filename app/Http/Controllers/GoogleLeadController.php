<?php

namespace App\Http\Controllers;

use App\Models\Endpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleLeadController extends Controller
{
    public function webhook(Request $request)
    {
        $data = json_decode($request->getContent());
        $verification_token = $data->google_key ?? null;

        $verification_token = Endpoint::where('verification_token', '=', $verification_token)->firstOrFail();

        $logMessage = print_r($data, true);

        Log::info($logMessage);

        return response()->json(['status' => 'success']);
    }

}
