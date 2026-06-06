<?php

use Illuminate\Support\Facades\Route; 
use iProtek\Core\Http\Controllers\Manage\FileUploadController; 
use iProtek\Core\Http\Controllers\AppVariableController;
use iProtek\SmsSender\Http\Controllers\MessageController;
use iProtek\SmsSender\Http\Controllers\SmsClientApiRequestLinkController;
use iProtek\PolicyControl\Http\Controllers\PolicyControlController;

//Route::prefix('sms-sender')->name('sms-sender')->group(function(){
  //  Route::get('/', [SmsController::class, 'index'])->name('.index');
//});
Route::prefix('api')->middleware('api')->name('api')->group(function(){  

      Route::prefix('group/{group_id}/policy-controls')->middleware(['pay.api', 'policy.control'])->name('.policy-controls')->group(function(){ 
      
        Route::get('/check-ability', [PolicyControlController::class, 'check_ability'])->name('.check-ability')
          ->defaults("_description", "Check user ability for policy control")
          ->defaults("_is_visible", false)
          ->defaults("_is_allow", true);
      
      }); 
  
  }); 
