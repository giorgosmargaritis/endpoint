<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleLeadController extends Controller
{
    public function webhook(Request $request)
    {
        $data = json_decode($request->getContent());

        $logMessage = print_r($data, true);

        Log::info($logMessage);

        return response()->json(['status' => 'success']);
    }

}
