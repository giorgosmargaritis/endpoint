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

        $headerUsername = $connectionLog->connection->receiver->auth_data['Username'];
        $headerPassword = $connectionLog->connection->receiver->auth_data['Password'];

        $response = Http::withHeaders([
            'Username' => $headerUsername,
            'Password' => $headerPassword,
        ])->post($connectionLog->connection->receiver->url,
            json_decode($connectionLog->transformed_data, true)
        );

        $connectionLogAttempt = ConnectionLogAttempt::create([
            'connections_logs_id' => $connectionLog->id,
            'status_code' => $response->status(),
            'response' => $response
        ]);

        Log::info($response->status());

        if(in_array($response->status(), ConnectionLogAttempt::STATUS_SUCCESS))
        {
            $connectionLog->status = ConnectionLog::STATUS_SUCCESS;
        }
        else
        {
            $connectionLog->status = ConnectionLog::STATUS_FAIL;
        }

        $connectionLog->saveQuietly();

        return Action::message('Log was sent successfully!');
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
