<?php

namespace iProtek\PolicyControl\Console\Commands;

use Illuminate\Console\Command;
use DB;

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
        //CHECKING AND PROCESSING BATCH AND PREPARING DATA
        //FileImportHelper::startBatchProcessing();

        //echo "Batch import processed completed!";

    }
}
