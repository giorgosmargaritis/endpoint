<?php

namespace App\Nova;

use App\Models\Log as ModelsLog;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Http\Requests\NovaRequest;

class Log extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Log>
     */
    public static $model = \App\Models\Log::class;

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

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

            HasOne::make('Data Facebook', 'log_data_facebook', 'App\Nova\LogDataFacebook')
            ->canSee(function ($request) {
                return $this->resource->log_type === ModelsLog::LOG_TYPE_FACEBOOK;
            }),

            HasOne::make('Data Google', 'log_data_google', 'App\Nova\LogDataGoogle')
            ->canSee(function ($request) {
                return ($this->resource->log_type === ModelsLog::LOG_TYPE_GOOGLE);
            }),

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
