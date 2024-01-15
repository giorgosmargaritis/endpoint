<?php

namespace App\Connector\Helpers;

use App\Models\LogDataFacebook;

class LogDataFacebookHelper
{
    public static function getStatuses()
    {
        return [
            LogDataFacebook::DATA_REQUESTED_STATUS_FAIL => 'FAIL',
            LogDataFacebook::DATA_REQUESTED_STATUS_SUCCESS => 'SUCCESS',
        ];
    }

}