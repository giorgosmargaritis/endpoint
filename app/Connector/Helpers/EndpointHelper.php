<?php

namespace App\Connector\Helpers;

use App\Models\Endpoint;

class EndpointHelper
{
    public static function getTypes($code = null)
    {
        $typeName = [
            Endpoint::SOCIAL_MEDIA_TYPE_FACEBOOK => 'Facebook',
            Endpoint::SOCIAL_MEDIA_TYPE_GOOGLE => 'Google',
        ];

        if($code !== null)
        {
            return $typeName[$code];
        }

        return $typeName;
    }
}