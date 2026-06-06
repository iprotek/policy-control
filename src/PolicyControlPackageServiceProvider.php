<?php

namespace iProtek\PolicyControl;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use iProtek\PolicyControl\Console\Commands\LoadPolicyControl;
use iProtek\Core\Http\Kernel;
use iProtek\Core\Models\UserAdminPayAccount;

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

        
        //use Illuminate\Support\Facades\Gate;
        Gate::before(function ($user, string $ability, $branch_id = null) {
            
            if (str_starts_with($ability, 'api.')) {
                
                if($user->can('super-admin')) return true;

                // Example:
                // api.cms.save
                // api.cms.add
                $userAdminPayAccountId = UserAdminPayAccount::where('user_admin_id', $user->id)->orderBy('id', 'desc')->first()?->pay_app_user_account_id ?? 0;
                

                if(config('iprotek.disable_multi_branch') == 'yes' || $branch_id == null || $branch_id <= 0){
                    $branch_id = 1;
                }

                return \DB::select("SELECT isUserAllowPolicyControl(?, ?, ?) as is_allowed",[$userAdminPayAccountId, $branch_id, $ability])[0]->is_allowed == 1;
                //return $user->hasPermission($ability);
            }

            return null; // Continue normal gate checks
        });

        // Bootstrap package services
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'iprotek_policy_control');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/iprotek.php', 'iprotek_policy_control'
        );
    }
}