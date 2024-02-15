<?php

namespace App\Console\Commands;

use App\Models\Connection;
use App\Models\ConnectionLog;
use Illuminate\Console\Command;
use App\Models\ConnectionLogAttempt;
use Illuminate\Support\Facades\Http;
use App\Connector\Helpers\ReceiverHelper;

class SendConnectionlogData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-connectionlog-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connections = Connection::get();
        foreach($connections as $connection)
        {
            $connectionLogsFail = $connection->connectionslogs->where('status', ConnectionLog::STATUS_FAIL);
            $connectionLogsPending = $connection->connectionslogs->where('status', ConnectionLog::STATUS_PENDING);

            foreach($connectionLogsFail as $connectionLogFail)
            {
                ReceiverHelper::sendConnectionLog($connectionLogFail, $connection->receiver);
            }   
            
            foreach($connectionLogsPending as $connectionLogPending)
            {
                ReceiverHelper::sendConnectionLog($connectionLogPending, $connection->receiver);
            }
        }
        
    }
}
