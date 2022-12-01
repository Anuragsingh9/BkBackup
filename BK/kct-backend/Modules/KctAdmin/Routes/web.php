<?php

use Illuminate\Support\Facades\Route;
use Modules\KctAdmin\Http\Controllers\V1\EventController;

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
Route::group(['middleware' => ['cors']], function () {
    Route::get('/j/{join_code}', [EventController::class, 'redirectUsingJoinCode'])->name('event-join');
});
