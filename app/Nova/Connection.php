<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use App\Models\ConnectionLog;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\BelongsTo;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Http\Requests\NovaRequest;

class Connection extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Connection>
     */
    public static $model = \App\Models\Connection::class;

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
        'endpoint',
        'receiver',
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

            BelongsTo::make('Endpoint', 'endpoint', 'App\Nova\Endpoint'),

            BelongsTo::make('Receiver', 'receiver', 'App\Nova\Receiver'),
            
            HasMany::make('Connection Logs', 'connectionslogs', 'App\Nova\ConnectionLog'),
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
            (new \App\Nova\Actions\RequestFacebookDataBatch)
            ->onlyOnDetail()
            ->canSee(function ($request) {
                $connectionLog = $this->model()->connectionslogs->where('status', ConnectionLog::STATUS_FAIL_FROM_FACEBOOK)->first();
                Log::info('From facebook data batch, $connectionLog: ' . $connectionLog);
                if($connectionLog)
                {
                    Log::info("I can run RequestFacebookDataBatch.");
                    return true;
                }

                if(!$this->model())
                {
                    Log::info("I can run RequestFacebookDataBatch from exception.");
                    return true;
                }

                return false;
            })
            ->canRun(function ($request) {
                return true;
            }),
            (new \App\Nova\Actions\SendLogBatch)
            ->onlyOnDetail()
            ->canSee(function ($request) {
                $connectionLog = $this->model()->connectionslogs->where('status', ConnectionLog::STATUS_FAIL)->first();
                Log::info('From send log batch, $connectionLog: ' . $connectionLog);
                if($connectionLog)
                {
                    Log::info("I can run SendLogBatch.");
                    return true;
                }
                Log::info("I can't run SendLogBatch.");
            })
            ->canRun(function ($request) {
                Log::info("I can run SendLogBatch.");
                return true;
            }),
            (new \App\Nova\Actions\SendEmail),
        ];
    }
}
