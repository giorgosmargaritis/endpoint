<?php

namespace App\Nova\Actions;

use App\Models\LogReceiver;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use App\Models\LogReceiverAttempt;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
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
        $logReceiver = $models->first();

        $headerUsername = $logReceiver->receiver->authenticationmethod->data['Username'];
        $headerPassword = $logReceiver->receiver->authenticationmethod->data['Password'];

        $response = Http::withHeaders([
            'Username' => $headerUsername,
            'Password' => $headerPassword,
        ])->post($logReceiver->receiver->url, [
            "Campaign_id" => "FB_45678_Taigo_01",
            "Leadid" => "56547ΓΔΦΓ1128",
            "FName" => "Anna",
            "LastName" => "Kotzamani",
            "LeadDate" => "12/9/2023",
            "Email" => "anakot@kosmocar.gr",
            "Mobile" => "6946325145",
            "Brand" => "vw",
            "Model" => "Taigo",
            "DealerCode" => "501",
            "ContactReason" => "Offer",
            "Regnum" => "YMH7945"
        ]);

        $logReceiverAttempt = LogReceiverAttempt::create([
            'logs_receivers_id' => $logReceiver->id,
            'status_code' => $response->status(),
            'response' => $response
        ]);

        Log::info($response->status());

        if($response->status() === LogReceiverAttempt::STATUS_SUCCESS)
        {
            $logReceiver->status = LogReceiver::STATUS_SUCCESS;
        }

        if($response->status() === LogReceiverAttempt::STATUS_SAMEID || $response->status() === LogReceiverAttempt::STATUS_EMPTYLEADID)
        {
            $logReceiver->status = LogReceiver::STATUS_FAIL;
        }

        $logReceiver->saveQuietly();

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
