<?php

namespace App\Connector\Helpers;

use App\Models\ConnectionLog;
use App\Models\Log;
use Illuminate\Support\Facades\Log as FacadesLog;

class ConnectionLogHelper
{
    public static function getGoogleStatuses()
    {
        return [
            ConnectionLog::STATUS_FAIL => 'FAIL',
            ConnectionLog::STATUS_SUCCESS => 'SUCCESS',
        ];
    }
    public static function getFacebookStatuses()
    {
        return [
            ConnectionLog::STATUS_FAIL => 'FAIL',
            ConnectionLog::STATUS_SUCCESS => 'SUCCESS',
            ConnectionLog::STATUS_FAIL_FROM_FACEBOOK => 'FAIL FROM FACEBOOK',
        ];
    }

    public static function getStatuses($connectionLog)
    {
        // switch ($connectionLog->log->log_type) {
        //     case Log::LOG_TYPE_FACEBOOK:
        //         return self::getFacebookStatuses();
        //         break;
            
        //     case Log::LOG_TYPE_GOOGLE:
        //         return self::getGoogleStatuses();
        //         break;
        //     default:
        //         return [
        //             ConnectionLog::STATUS_FAIL => 'FAIL',
        //             ConnectionLog::STATUS_SUCCESS => 'SUCCESS',
        //             ConnectionLog::STATUS_FAIL_FROM_FACEBOOK => 'FAIL FROM FACEBOOK',
        //         ];
        //         break;
                
        // }
        return [
            ConnectionLog::STATUS_FAIL => 'FAIL',
            ConnectionLog::STATUS_SUCCESS => 'SUCCESS',
            ConnectionLog::STATUS_PENDING => 'PENDING',
            ConnectionLog::STATUS_FAIL_FROM_FACEBOOK => 'FAIL FROM FACEBOOK',
        ];
    }
}