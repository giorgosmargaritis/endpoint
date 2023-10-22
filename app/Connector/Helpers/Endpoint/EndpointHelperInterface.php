<?php

namespace App\Connector\Helpers\Endpoint;

use App\Models\Endpoint;
use Illuminate\Http\Request;

interface EndpointHelperInterface
{
    public function verifiedRequest(Endpoint $endpoint, Request $request);
    public function createLogData(Endpoint $endpoint, $logData): int;
}