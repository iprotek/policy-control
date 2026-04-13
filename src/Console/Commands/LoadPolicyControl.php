<?php

namespace iProtek\PolicyControl\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Route;
use iProtek\PolicyControl\Models\PolicyControl;

class LoadPolicyControl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'policy-control:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preparation for import batch file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    { 
        $allowedPrefixes = ['api.', 'manage.'];
        $routes = collect(Route::getRoutes())
            ->map(function ($route) {
                return [
                    'name' => $route->getName(),
                    'methods' => $route->methods(),
                    'defaults' => $route->defaults,
                ];
            })
            ->filter(fn ($route) => $route['name'] !== null)
            ->filter(function ($route) use ($allowedPrefixes) {
                foreach ($allowedPrefixes as $prefix) {
                    if (str_starts_with($route['name'], $prefix)) {
                        return true;
                    }
                }
                return false;
            })
            ->sortBy('name') 
            ->values();

        //dd($routes);
        $policy = PolicyControl::on();
        $policy->timestamps = false;
        $policy->update(["is_active"=>false]);
        
        foreach($routes as $route){
            
            if(!isset($route["name"]) || !$route["name"] ){
                continue;
            }
            $policy = PolicyControl::where('name', $route)->first();
            if($policy){
                $policy->update([
                    "description"=>$route['defaults']['description'] ?? null,
                    "is_active"=>true,
                    "is_visible"=>$route['defaults']['is_visible'] ?? true,
                    "default_is_allow"=>$route['defaults']['is_allow'] ?? true,
                ]);
            }
            else{
                PolicyControl::create([
                    "name"=>$route['name'],
                    "description"=>$route['defaults']['description'] ?? null,
                    "methods"=> implode(',', $route['methods']),
                    "is_visible"=>$route['defaults']['is_visible'] ?? true,
                    "default_is_allow"=>$route['defaults']['is_allow'] ?? true,
                ]);

            }
            usleep(10000);
        }

    }
}
