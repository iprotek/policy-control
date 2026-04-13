<?php

namespace iProtek\PolicyControl;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use iProtek\Core\Helpers\BranchSelectionHelper;
use iProtek\Core\Helpers\PayHttp;
use iProtek\Xrac\Models\XuserRole;
use iProtek\Core\Helpers\UserMenuHelper;
use iProtek\Xrac\Helpers\XracHelper;

class XracPackageServiceProvider extends ServiceProvider
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

        //DEFINE GATES
        XracHelper::setGates();

        //DEFINE ROLES BASE ON XRAC



        // Bootstrap package services
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'iprotek_policy_control');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/iprotek.php', 'iprotek_policy_control'
        );
    }
}