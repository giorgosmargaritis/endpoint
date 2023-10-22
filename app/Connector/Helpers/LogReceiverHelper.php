<?php

namespace App\Connector\Helpers;

use App\Models\LogReceiver;

class LogReceiverHelper
{
    public static function getStatuses()
    {
        return [
            LogReceiver::STATUS_FAIL => 'FAIL',
            LogReceiver::STATUS_SUCCESS => 'SUCCESS',
            LogReceiver::STATUS_PENDING => 'PENDING',
        ];
    }
}