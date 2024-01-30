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
        $connectionLogs = $connection->connectionlogs->where('status', ConnectionLog::STATUS_PENDING);

        $headerUsername = $connection->receiver->auth_data['Username'];
        $headerPassword = $connection->receiver->auth_data['Password'];

        foreach($connectionLogs as $connectionLog)
        {
            $response = Http::withHeaders([
                'Username' => $headerUsername,
                'Password' => $headerPassword,
            ])->post($connection->receiver->url,
                json_decode($connectionLog->transformed_data, true)
            );

            $connectionLogAttempt = ConnectionLogAttempt::create([
                'connections_logs_id' => $connectionLog->id,
                'status_code' => $response->status(),
                'response' => $response
            ]);
            
            if(in_array($response->status(), ConnectionLogAttempt::STATUS_SUCCESS))
            {
                $connectionLog->status = ConnectionLog::STATUS_SUCCESS;
            }
            else
            {
                $connectionLog->status = ConnectionLog::STATUS_FAIL;
            }

            $connectionLog->saveQuietly();
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
