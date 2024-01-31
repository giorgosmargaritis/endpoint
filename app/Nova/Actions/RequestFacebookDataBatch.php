<?php

namespace App\Nova\Actions;

use App\Connector\Helpers\Endpoint\EndpointHelperFacebook;
use App\Models\ConnectionLog;
use App\Models\LogDataFacebook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class RequestFacebookDataBatch extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $connection = $models->first();
        $endpoint = $connection->endpoint;
        $connectionLogs = $connection->connectionslogs->where('status', ConnectionLog::STATUS_FAIL_FROM_FACEBOOK)->where('times_requested', '<', 10);
        $endpointHelperFacebook = new EndpointHelperFacebook();

        foreach($connectionLogs as $connectionLog)
        {
            // if(!$connectionLog->log->log_data_facebook->times_requested < 10)
            // {
            //     continue;
            // }

            $logID = $connectionLog->log->id;
            $logDataFacebook = $connectionLog->log->log_data_facebook;
            $dataReceived = $logDataFacebook->data_received;

            $requestedData = $endpointHelperFacebook->requestData($endpoint, $dataReceived);

            $requestedDataUpdated = $endpointHelperFacebook->updateRequestedData($requestedData, $logDataFacebook);

            $transformedData = $endpointHelperFacebook->transformData($requestedDataUpdated, $logID);

            $connectionLog = $endpointHelperFacebook->updateConnectionLog($connectionLog, $transformedData);

            $connectionSent = $endpointHelperFacebook->sendConnectionLog($connectionLog, $connection, $transformedData);

        }

        return Action::message('Data requested succesfully!');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
