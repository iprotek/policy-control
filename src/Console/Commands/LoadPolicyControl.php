<?php

namespace iProtek\PolicyControl\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Route;

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
        $routes = collect(Route::getRoutes())
            ->map(function ($route) {
                return [
                    'name' => $route->getName(),
                    'methods' => $route->methods(),
                    'defaults' => $route->defaults,
                ];
            })
            ->filter(fn ($route) => $route['name'] !== null)
            ->values();

        //dd($routes);
        foreach($routes as $route){
            dd($route);
        }

    }
}
