<?php

namespace App\Nova\Actions;

use App\Models\ConnectionLog;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use App\Models\ConnectionLogAttempt;
use Illuminate\Support\Facades\Http;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\InteractsWithQueue;
use App\Connector\Helpers\ReceiverHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Nova\Http\Requests\NovaRequest;

class SendLogBatch extends Action
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

        return Action::message('Logs were sent successfully!');
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
