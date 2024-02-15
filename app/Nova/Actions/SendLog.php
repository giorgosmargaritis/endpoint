<?php

namespace App\Nova\Actions;

use App\Models\ConnectionLog;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\ConnectionLogAttempt;
use Illuminate\Support\Facades\Http;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\InteractsWithQueue;
use App\Connector\Helpers\ReceiverHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Nova\Http\Requests\NovaRequest;

class SendLog extends Action
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
        $connection = $connectionLog->connection;

        $sendConnectionLog = ReceiverHelper::sendConnectionLog($connectionLog, $connection->receiver);

        if($sendConnectionLog)
        {
            return Action::message('Log was sent successfully!');
        }
        
        return Action::danger('Log was not sent!');
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
