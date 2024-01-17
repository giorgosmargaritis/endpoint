<?php

namespace App\Nova\Actions;

use App\Connector\Helpers\Endpoint\EndpointHelperFacebook;
use App\Models\LogDataFacebook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

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

        $endpoint = $connectionLog->connection->endpoint;
        $logID = $connectionLog->log->id;
        $dataReceived = LogDataFacebook::where('log_id', $logID)->first()->data_received;

        $requestedData = EndpointHelperFacebook::requestData($endpoint, $dataReceived);

        Log::info('data_requested: ' . $requestedData);

        return Action::message('Data requested successfully!');
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
