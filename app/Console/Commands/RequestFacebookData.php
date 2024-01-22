<?php

namespace App\Console\Commands;

use App\Models\LogDataFacebook;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Connector\Helpers\Endpoint\EndpointHelperFacebook;

class RequestFacebookData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:request-facebook-data';

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
        $logsForRequestingData = LogDataFacebook::where('data_requested_status', LogDataFacebook::DATA_REQUESTED_STATUS_FAIL)->get();
        $endpointHelperFacebook = new EndpointHelperFacebook();

        foreach($logsForRequestingData as $logForRequestingData)
        {
            Log::info('$connectionLog->connection: ' . $logForRequestingData->log);
            foreach($logForRequestingData->connectionlogs as $connectionLog)
            {
                Log::info('$connectionLog->connection: ' . $connectionLog->connection);
            }
            $endpoint = $logForRequestingData->connection->endpoint;
            $connection = $connectionLog->connection;
            $logID = $connectionLog->log->id;
            $logDataFacebook = LogDataFacebook::where('log_id', $logID)->first();
            $dataReceived = $logDataFacebook->data_received;
        }
    }
}
