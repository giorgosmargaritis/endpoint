<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\AuthenticationMethod;
use App\Models\LogReceiver;
use App\Policies\AuthenticationMethodPolicy;
use App\Policies\LogPolicy;
use App\Policies\LogReceiverPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Log::class => LogPolicy::class,
        LogReceiver::class => LogReceiverPolicy::class,
        AuthenticationMethod::class => AuthenticationMethodPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
