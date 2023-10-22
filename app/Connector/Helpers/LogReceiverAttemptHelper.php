<?php

namespace App\Connector\Helpers;

use App\Models\LogReceiverAttempt;

class LogReceiverAttemptHelper
{
    public static function getStatuses()
    {
        return [
            LogReceiverAttempt::STATUS_SUCCESS[0] => __('SUCCESS'),
            LogReceiverAttempt::STATUS_SUCCESS[1] => __('SUCCESS'),
            LogReceiverAttempt::STATUS_SAMEID => __('SAME_ID'),
            LogReceiverAttempt::STATUS_EMPTYLEADID => __('EMPTY LEAD ID'),
        ];
    }

    public static function getStatusesCodes()
    {
        return [
            LogReceiverAttempt::STATUS_SUCCESS[0],
            LogReceiverAttempt::STATUS_SUCCESS[1],
            LogReceiverAttempt::STATUS_SAMEID,
            LogReceiverAttempt::STATUS_EMPTYLEADID,
        ];
    }
}