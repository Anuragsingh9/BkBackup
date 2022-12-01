<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Option Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API Option routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['middleware' => ['cors'], 'prefix' => 'kct-admin'], function () {
    // header allow routes
    Route::group(['middleware' => ['cors']], function () {
        $cors = function () {
        };
    });

});
