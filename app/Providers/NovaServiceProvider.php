<?php

namespace App\Providers;

use App\Nova\Log;
use App\Nova\User;
use App\Nova\Endpoint;
use App\Nova\Receiver;
use Laravel\Nova\Nova;
use App\Nova\Connection;
use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuItem;
use App\Nova\AuthenticationMethod;
use Laravel\Nova\Menu\MenuSection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::withoutNotificationCenter();
        Nova::withoutGlobalSearch();

        Nova::mainMenu(function (Request $request) {
            return [
                MenuSection::make('Resources', [
                    MenuItem::resource(Connection::class),
                    MenuItem::resource(Receiver::class),
                    MenuItem::resource(Endpoint::class),
                    MenuItem::resource(AuthenticationMethod::class),
                    MenuItem::resource(User::class),
                    MenuItem::resource(Log::class),
                ])
            ];
        });

        Nova::footer(function ($request) {
            return Blade::render('<p style="text-align:center;">Connector Admin Panel</p>');
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            // new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Nova::initialPath('/resources/connections');
    }
    
}
