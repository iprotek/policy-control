<?php

use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Gate;

include(__DIR__.'/api.php');

Route::middleware(['web'])->group(function(){
 
    Route::middleware(['auth:admin'])->prefix('manage')->name('manage')->group(function(){
        
        Route::prefix('policy-control')->name('.xrac')->group(function(){

            //ROLE ACCESS
            include(__DIR__.'/manage/policy-control.php'); 

        });
    });
  
});