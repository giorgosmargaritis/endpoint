<?php

namespace App\Nova;

use App\Models\AuthenticationMethod;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\BelongsTo;
use Outl1ne\MultiselectField\Multiselect;
use Laravel\Nova\Http\Requests\NovaRequest;

class Receiver extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Receiver>
     */
    public static $model = \App\Models\Receiver::class;

    /**
     * The click action to use when clicking on the resource in the table.
     *
     * Can be one of: 'detail' (default), 'edit', 'select', 'preview', or 'ignore'.
     *
     * @var string
     */
    public static $clickAction = 'ignore';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'name',
        'url',
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

            Text::make('Name', 'name')->rules('required'),

            Text::make('URL', 'url')->hideFromIndex()->rules('required'),

            BelongsTo::make('Authentication Method', 'authenticationmethod'),

            KeyValue::make('Auth Data', 'auth_data')
                ->hide()
                ->rules('json')
                ->dependsOn(
                    ['authenticationmethod'],
                    function (KeyValue $field, NovaRequest $request, FormData $formData) {
                        if($formData->authenticationmethod)
                        {
                            $authMethodType = AuthenticationMethod::find($formData->authenticationmethod)->type;
                            if ($authMethodType !== AuthenticationMethod::TYPE_NOAUTH) {
                                $field->show()->rules(['required']);
                            }
                        }
                    }
                ),
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
