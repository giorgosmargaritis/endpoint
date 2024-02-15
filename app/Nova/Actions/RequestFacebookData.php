<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use App\Models\LogDataFacebook;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\InteractsWithQueue;
use App\Connector\Helpers\ReceiverHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Connector\Helpers\Endpoint\EndpointHelperFacebook;

class RequestFacebookData extends Action
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
        $connectionLog = $models->first();

        $endpointHelperFacebook = new EndpointHelperFacebook();

        $endpoint = $connectionLog->connection->endpoint;
        $connection = $connectionLog->connection;
        $logID = $connectionLog->log->id;
        $logDataFacebook = LogDataFacebook::where('log_id', $logID)->first();
        $dataReceived = $logDataFacebook->data_received;

        $requestedData = $endpointHelperFacebook->requestData($endpoint, $dataReceived);

        $requestedDataUpdated = $endpointHelperFacebook->updateRequestedData($requestedData, $logDataFacebook);

        $transformedData = $endpointHelperFacebook->transformData($requestedDataUpdated, $logID);

        $connectionLog = $endpointHelperFacebook->updateConnectionLog($connectionLog, $transformedData);

        $sendConnectionLog = ReceiverHelper::sendConnectionLog($connectionLog, $connection->receiver);

        if($sendConnectionLog)
        {
            return Action::message('Data requested and sent successfully!');
        }
        
        return Action::danger('Data requested successfully but was not sent!');
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
