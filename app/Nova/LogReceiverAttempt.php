<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Connector\Helpers\LogReceiverHelper;
use App\Connector\Helpers\LogReceiverAttemptHelper;

class LogReceiverAttempt extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\LogReceiverAttempt>
     */
    public static $model = \App\Models\LogReceiverAttempt::class;

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
            Text::make('Response'),
            Select::make('HTTP Code', 'status_code')
                ->options(LogReceiverAttemptHelper::getStatusesCodes())
                ->filterable(),
            BelongsTo::make('Data', 'logsreceivers', 'App\Nova\LogReceiver')
            ->display(function ($logsreceivers) {
                return $logsreceivers->transformed_data;
            })
            ->onlyOnDetail(),
            DateTime::make('Created At')
                ->filterable()
                ->displayUsing(fn ($value) => $value ? $value->format(config('connector.datetime_format')) : ''),

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
        return [];
    }
}
