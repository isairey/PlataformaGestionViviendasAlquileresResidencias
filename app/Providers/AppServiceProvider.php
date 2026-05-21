<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    protected $policies = [
        MaintenanceRequest::class => MaintenanceRequestPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes();
        Model::preventAccessingMissingAttributes();
        Model::automaticallyEagerLoadRelationships();

        if (app()->isLocal()) {
            \Barryvdh\Debugbar\Facade::enable();
        } else {
            \Barryvdh\Debugbar\Facade::disable();
        }
    }
}
