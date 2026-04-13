<?php

namespace iProtek\PolicyControl;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use iProtek\PolicyControl\Console\Commands\LoadPolicyControl;

class PolicyControlPackageServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register package services
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        //DEFINE ROLES BASE ON XRAC

        //COMMANDS REGISTRATIONS PREPARATIONS 
        if ($this->app->runningInConsole()) {
            $this->commands([
                LoadPolicyControl::class,
            ]);
        }


        // Bootstrap package services
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'iprotek_policy_control');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/iprotek.php', 'iprotek_policy_control'
        );
    }
}