<?php

namespace App\Nova;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\MultiSelect;
use App\Connector\Helpers\EndpointHelper;
use App\Models\Endpoint as ModelsEndpoint;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;

class Endpoint extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Endpoint>
     */
    public static $model = \App\Models\Endpoint::class;

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
        'name',
        'path',
        'verification_token',
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

            Text::make('Name', 'name')
            ->rules('required'),

            Text::make('Webhook Path', 'path')
            ->rules('required')
            ->creationRules('unique:endpoints,path')
            ->displayUsing(function ($value) {
                return '{{domain}}/api/webhook/' . $value;
            }),

            Text::make('Token', 'verification_token')
            ->readonly(function ($request) {
                return $request->isUpdateOrUpdateAttachedRequest();
            })
            ->default(Str::random(20))
            ->maxlength(20)
            ->enforceMaxlength()
            ->rules('required')
            ->creationRules('unique:endpoints,verification_token'),

            Select::make('Type', 'type')
            ->options(EndpointHelper::getTypes())
            ->displayUsingLabels()
            ->rules('required')
            ->filterable(),

            URL::make('Documentation', fn () => url('files/facebook/longliveaccesstoken.pdf'))
            ->onlyOnDetail()
            ->showOnDetail(function (NovaRequest $request, $resource) {
                return $this->type === ModelsEndpoint::SOCIAL_MEDIA_TYPE_FACEBOOK;
            })
            ->displayUsing(fn () => "Long-liveAccessToken.pdf"),

            Text::make('Page Access Token', 'page_access_token')
            ->hide()
            ->hideFromIndex()
            ->dependsOn(
                ['type'],
                function (Text $field, NovaRequest $request, FormData $formData) {
                    if ($formData->type === ModelsEndpoint::SOCIAL_MEDIA_TYPE_FACEBOOK) {
                        $field->show()->rules(['required'])->showOnDetail();
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
