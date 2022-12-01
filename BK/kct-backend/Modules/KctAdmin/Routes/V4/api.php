<?php

use Illuminate\Support\Facades\Route;
use Modules\KctAdmin\Http\Controllers\V4\EventV4Controller;
use Modules\KctAdmin\Http\Controllers\V4\UserV4Controller;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['cors','auth:api']], function () {
    Route::get('/events', [EventV4Controller::class, 'getEvent']);
    Route::post('/events', [EventV4Controller::class, 'createV4Event']);
    Route::put('/events', [EventV4Controller::class, 'updateV4Event']);
    Route::get('/events-init',[EventV4Controller::class, 'getEventInitData']);
    Route::get('/events/analytics',[EventV4Controller::class,'getEventsAnalytics']);
    Route::get('/events/analytics/single',[EventV4Controller::class,'getSingleEventAnalytics']);

    Route::get('/users', [UserV4Controller::class, 'getEventUsers']);
});
