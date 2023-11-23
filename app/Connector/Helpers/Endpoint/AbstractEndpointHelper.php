<?php

namespace App\Connector\Helpers\Endpoint;

use App\Models\Endpoint;
use App\Connector\Helpers\EndpointHelper;
use App\Connector\Helpers\Endpoint\EndpointHelperInterface;
use Illuminate\Support\Facades\Log;

abstract class AbstractEndpointHelper implements EndpointHelperInterface
{
    public static function getInstance(int $endpointType)
    {
        $endpointTypeName = EndpointHelper::getTypes($endpointType);
        $endpointClass = 'App\Connector\Helpers\Endpoint\EndpointHelper' . $endpointTypeName;

        return new $endpointClass();
    }

    public function hasReceivers(Endpoint $endpoint)
    {
        if(empty($endpoint->connections))
        {
            return false;
        }
        
        return true;
    }
}