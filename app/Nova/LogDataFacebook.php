<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Connector\Helpers\LogDataFacebookHelper;
use Laravel\Nova\Fields\Number;

class LogDataFacebook extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\LogDataFacebook>
     */
    public static $model = \App\Models\LogDataFacebook::class;

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
            Text::make('Data Received','data_received'),

            Text::make('Data Requested','data_requested'),

            Select::make('Data Requested Status', 'data_requested_status')
                ->options(LogDataFacebookHelper::getStatuses())
                ->displayUsingLabels(),

            Number::make('Times Requested', 'times_requested'),

            DateTime::make('Created At')
            ->displayUsing(fn ($value) => $value ? $value->format(config('connector.datetime_format')) : ''),

            DateTime::make('Updated At')
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
