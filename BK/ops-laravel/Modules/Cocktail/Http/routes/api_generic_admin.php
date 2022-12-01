<?php

/**
 * Generic: These are the api which needs to be loosely coupled
 * These api's need to be decided to go for v1 controller or another version controller
 */

// kct generic api
Route::group(['middleware' => ['cors',], 'prefix' => 'kct-admin', 'namespace' => 'Modules\Cocktail\Http\Controllers\Generic\AdminSideControllers'], function () {
    // event space routes
    Route::group(['middleware' => ['cors', 'auth']], function () {
        Route::group(['prefix' => 'event/space'], function () {
            Route::post('/', 'SpaceController@store');
            Route::get('/', 'SpaceController@getSpace');
            Route::put('/', 'SpaceController@update');
            Route::get('all/{event_uuid}', 'SpaceController@getEventSpacesForAdmin')->middleware('eventAdmin:route');
        });
    });
});

// events module generic api
Route::group(['middleware' => ['web', 'cors', 'eventApi', 'auth'], 'prefix' => 'events', 'namespace' => 'Modules\Cocktail\Http\Controllers\Generic\AdminSideControllers'], function () {
    Route::post('events', 'EventController@store')->middleware('checkRole:admin');
    Route::put('events/{event_id}', 'EventController@update')->middleware('checkRole:sec');
    Route::get('events/{event_id}', 'EventController@show');
});
