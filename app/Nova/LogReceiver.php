<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Connector\Helpers\LogReceiverHelper;
use App\Models\LogReceiver as ModelsLogReceiver;

class LogReceiver extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\LogReceiver>
     */
    public static $model = \App\Models\LogReceiver::class;

    /**
     * The click action to use when clicking on the resource in the table.
     *
     * Can be one of: 'detail' (default), 'edit', 'select', 'preview', or 'ignore'.
     *
     * @var string
     */
    public static $clickAction = 'ignore';

    /**
    * Get the value that should be displayed to represent the title of the resource.
    *
    * @return string
    */
    public static function label() {
        return 'Logs';
    }

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';
    

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            // BelongsTo::make('Connection', 'endpointreceiver', 'App\Nova\EndpointReceiver')
            //     ->filterable()
            //     ->exceptOnForms(),

            // Text::make('lead_id')
            // ->displayUsing(function ($data)),

            Select::make('Status', 'status')
                ->options(LogReceiverHelper::getStatuses())
                ->displayUsingLabels()
                ->filterable()
                ->exceptOnForms(),

            // Text::make('Campaign ID', 'transformed_data->Campaign_id')
            //     ->filterable()
            //     ->sortable(),

            DateTime::make('Created At')
                ->filterable()
                ->exceptOnForms()
                ->displayUsing(fn ($value) => $value ? $value->format(config('connector.datetime_format')) : ''),

            BelongsTo::make('Received data', 'log', 'App\Nova\Log')
                ->display(function ($log) {
                    return $log->data;
                })
                ->onlyOnDetail(),

            HasMany::make('Attempts', 'logsreceiversattempts', 'App\Nova\LogReceiverAttempt'),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            (new \App\Nova\Actions\SendLog)
                ->canSee(function ($request) {
                    if($this->model()->status !== ModelsLogReceiver::STATUS_SUCCESS)
                    {
                        return true;
                    }
                }),
        ];
    }
}
