<?php

use Illuminate\Support\Facades\Route; 
use iProtek\Core\Http\Controllers\Manage\FileUploadController; 
use iProtek\Core\Http\Controllers\AppVariableController;
use iProtek\SmsSender\Http\Controllers\MessageController;
use iProtek\SmsSender\Http\Controllers\SmsClientApiRequestLinkController;

//Route::prefix('sms-sender')->name('sms-sender')->group(function(){
  //  Route::get('/', [SmsController::class, 'index'])->name('.index');
//});
Route::prefix('api')->middleware('api')->name('api')->group(function(){ 

    Route::prefix('xrac')->name('.xrac')->group(function(){

      Route::prefix('group/{group_id}')->middleware(['pay.api'])->name('api')->group(function(){ 
      
        //BRANCH
        //include(__DIR__.'/manage/branch.php'); 
        
        //CONTROL ACCESS
        //include(__DIR__.'/manage/control-access.php'); 

        //ROLE
        //include(__DIR__.'/manage/role.php'); 

        //USER ROLE
        //include(__DIR__.'/manage/user-role.php'); 

        //XROLE
        //include(__DIR__.'/manage/xrole.php'); 
      
      });

    });
  
  }); 
