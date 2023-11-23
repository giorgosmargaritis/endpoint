<?php

namespace App\Connector\Helpers;

use App\Models\ConnectionLogAttempt;

class ConnectionLogAttemptHelper
{
    public static function getStatuses()
    {
        return [
            ConnectionLogAttempt::STATUS_SUCCESS[0] => __('SUCCESS'),
            ConnectionLogAttempt::STATUS_SUCCESS[1] => __('SUCCESS'),
            ConnectionLogAttempt::STATUS_SAMEID => __('SAME_ID'),
            ConnectionLogAttempt::STATUS_EMPTYLEADID => __('EMPTY LEAD ID'),
        ];
    }

    public static function getStatusesCodes()
    {
        return [
            ConnectionLogAttempt::STATUS_SUCCESS[0],
            ConnectionLogAttempt::STATUS_SUCCESS[1],
            ConnectionLogAttempt::STATUS_SAMEID,
            ConnectionLogAttempt::STATUS_EMPTYLEADID,
        ];
    }
}