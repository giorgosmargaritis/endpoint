<?php

namespace App\Connector\Helpers;

use App\Models\ConnectionLog;

class ConnectionLogHelper
{
    public static function getStatuses()
    {
        return [
            ConnectionLog::STATUS_FAIL => 'FAIL',
            ConnectionLog::STATUS_SUCCESS => 'SUCCESS',
            ConnectionLog::STATUS_PENDING => 'PENDING',
            ConnectionLog::STATUS_FAIL_FROM_FACEBOOK => 'FAIL FROM FACEBOOK',
        ];
    }
}