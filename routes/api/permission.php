<?php

use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;

Route::group([

  'middleware' => 'auth:api',
  /* 'prefix' => 'auth' */

], function($router){

    Route::get('/roles', [RolePermissionController::class,'index'])->name('roles.index');

    Route::get('/roles/{id}', [RolePermissionController::class,'show'])->name('roles.show');

    Route::post('/roles', [RolePermissionController::class,'store'])->name('roles.store');

    Route::put('/roles/{id}', [RolePermissionController::class,'update'])->name('roles.update');

    Route::delete('/roles/{id}', [RolePermissionController::class,'destroy'])->name('roles.destroy');

    Route::post('/roles/assign', [RolePermissionController::class,'assign'])->name('roles.assign');

    Route::post('/roles/remove', [RolePermissionController::class,'remove'])->name('roles.remove');
    
});