<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::prefix('usermanagement')->group(function() {
//    Route::get('/', 'UserManagementController@index');
//});

use Modules\UserManagement\Http\Controllers\UserManagementController;

Route::prefix('/')->group(function() {
    Route::view('/signin','usermanagement::auth.login' )->name('um-signin');
    Route::post('/signin',[UserManagementController::class,'userLogin'])->name('user-login');
    Route::view('/forgot-password','usermanagement::auth.forgot_password');
    Route::post('/forgot-password',[UserManagementController::class,'forgotPassword'])->name('forgot-password');
//    Route::view('/reset-password','usermanagement::auth.reset_password');
    Route::get('/reset-view/{email}/{key}',[UserManagementController::class,'resetView']);
    Route::post('/reset-password',[UserManagementController::class,'resetPassword'])->name('reset-password');
});
