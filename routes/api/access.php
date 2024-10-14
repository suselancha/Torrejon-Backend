<?php

use App\Http\Controllers\UserAccessController;
use Illuminate\Support\Facades\Route;

Route::group([

  'middleware' => 'auth:api',
  /* 'prefix' => 'auth' */

], function($router){
  
    Route::get('/users', [UserAccessController::class,'index'])->name('users.index');
    
    Route::get('/users/config', [UserAccessController::class,'config'])->name('users.config');
    
    Route::get('/users/{id}', [UserAccessController::class,'show'])->name('users.show');    
        
    Route::post('/users', [UserAccessController::class,'store'])->name('users.store');
    
    Route::post('/users/{id}', [UserAccessController::class,'update'])->name('users.update');
    
    Route::delete('/users/{id}', [UserAccessController::class,'destroy'])->name('users.destroy');
    
    
});