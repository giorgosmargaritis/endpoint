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
            Log::info('$logForRequestingData->log: ' . $logForRequestingData->log);
            foreach($logForRequestingData->log->connectionlogs->toArray() as $connectionLog)
            {
                Log::info('$connectionLog->connection: ' . $connectionLog->connection);
            }
            exit;
            $endpoint = $logForRequestingData->log->connection->endpoint;
            $connection = $logForRequestingData->log->connection;
            $connectionLog = $logForRequestingData->log->connection->connectionslogs;
            $logID = $logForRequestingData->log->id;
            $logDataFacebook = $logForRequestingData;
            $dataReceived = $logDataFacebook->data_received;

            $requestedData = $endpointHelperFacebook->requestData($endpoint, $dataReceived);

            $requestedDataUpdated = $endpointHelperFacebook->updateRequestedData($requestedData, $logDataFacebook);

            $transformedData = $endpointHelperFacebook->transformData($requestedDataUpdated, $logID);

            $connectionLog = $endpointHelperFacebook->updateConnectionLog($connectionLog, $transformedData);

            $connectionSent = $endpointHelperFacebook->sendConnectionLog($connectionLog, $connection, $transformedData);
        }
    }
}
