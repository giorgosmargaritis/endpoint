<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Connector\Helpers\ConnectionLogHelper;
use App\Models\ConnectionLog as ModelsConnectionLog;

class ConnectionLog extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\ConnectionLog>
     */
    public static $model = \App\Models\ConnectionLog::class;

    /**
     * The number of resources to show per page via relationships.
     *
     * @var int
     */
    public static $perPageViaRelationship = 15;

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
        'campaign_id',
        'leadgen_id',
        'status',
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

            Text::make('Campaign ID', 'campaign_id')
                ->sortable()
                ->filterable(function ($request, $query, $value, $attribute) {
                    $query->where($attribute, 'LIKE', "{$value}%");
                }),

            Text::make('Leadgen ID', 'leadgen_id')
                ->sortable()
                ->filterable(function ($request, $query, $value, $attribute) {
                    $query->where($attribute, 'LIKE', "{$value}%");
                }),

            Select::make('Status', 'status')
                ->options(ConnectionLogHelper::getStatuses($this->model()))
                ->displayUsingLabels()
                ->exceptOnForms()
                ->filterable(),

            BelongsTo::make('Original Data', 'log', 'App\Nova\Log')
                ->onlyOnDetail(),

            Text::make('Transformed Data', 'transformed_data')
                ->onlyOnDetail(),

            DateTime::make('Created At')
                ->exceptOnForms()
                ->filterable()
                ->displayUsing(fn ($value) => $value ? $value->format(config('connector.datetime_format')) : ''),

            DateTime::make('Updated At')
            ->exceptOnForms()
            ->filterable()
            ->displayUsing(fn ($value) => $value ? $value->format(config('connector.datetime_format')) : ''),

            HasMany::make('Attempts', 'connectionlogattempts', 'App\Nova\ConnectionLogAttempt'),
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
                ->onlyOnDetail()
                ->canSee(function ($request) {
                    if($this->model()->status !== ModelsConnectionLog::STATUS_SUCCESS && $this->model()->status !== ModelsConnectionLog::STATUS_FAIL_FROM_FACEBOOK)
                    {
                        return true;
                    }
                })
                ->canRun(function ($request) {
                    return true;
                }),
            (new \App\Nova\Actions\RequestFacebookData)
                ->onlyOnDetail()
                ->canSee(function ($request) {
                    if($this->model()->status !== ModelsConnectionLog::STATUS_SUCCESS
                    && $this->model()->status !== ModelsConnectionLog::STATUS_FAIL
                    && $this->model()->status !== ModelsConnectionLog::STATUS_PENDING)
                    {
                        return true;
                    }
                })
                ->canRun(function ($request) {
                    return true;
                }),
        ];
    }
}
