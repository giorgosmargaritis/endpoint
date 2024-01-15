<?php

namespace App\Http\Controllers;

use App\Models\Endpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Connector\Helpers\Endpoint\AbstractEndpointHelper;
use App\Models\ConnectionLog;
use App\Models\ConnectionLogAttempt;

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
            Log::info('No receivers');
            return response('No receivers set', 403);
        }

        if(!$endpointHelper->verifiedRequest($endpoint, $request))
        {
            Log::info('Not Verified Request');
            return response('Not Verified Request', 403);
        }
        
        Log::info('--- Procedure started ---');
        $data = json_decode($request->getContent(), true);

        $logId = $endpointHelper->createLogData($endpoint, $data);
        Log::info('$logId:' . $logId);
        if($logId === -1)
        {
            Log::info('--- Procedure finished ---');
            return response('LeadgenID exists', 200);
        }
        
        $connections = $endpoint->connections;

        Log::info('Connections: ' . $connections);

        foreach($connections as $connection)
        {
            $transformedData = $endpointHelper->transformData($data, $logId);
            
            $connectionLog = $endpointHelper->createConnectionLog($connection, $transformedData, $logId);

            $endpointHelper->sendConnectionLog($connectionLog, $connection, $transformedData);
        }

        Log::info('--- Procedure finished ---');

        return response()->json(['status' => 'success']);
    }

}
