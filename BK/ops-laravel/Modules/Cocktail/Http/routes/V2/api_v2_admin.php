<?php

// version 2 new api's here
Route::group(['middleware' => ['cors'], 'prefix' => 'kct-admin', 'namespace' => 'Modules\Cocktail\Http\Controllers\V2\AdminSideControllers'], function () {
    // header allow routes
    Route::group(['middleware' => ['cors']], function () {
        $cors = function () {};
        Route::options('org/default-logo', $cors);
        Route::options('org/default-color', $cors);
        Route::options('event/space', $cors);
        Route::options('event-tag', $cors);
        Route::options('graphics/customisation', $cors);

    });

    // actual routes for v2 here
    Route::group(['middleware' => ['cors', 'auth']], function () {
        Route::post('org/default-logo', 'SettingController@uploadLogo');
        Route::delete('org/default-logo', 'SettingController@deleteDefaultLogo');
        Route::post('org/default-color', 'SettingController@updateDefaultColor');
        Route::resource('event-tag','EventTagController');
        Route::get('graphics/customisation','SettingController@graphicsCustomisation');
        Route::post('graphics/customisation','SettingController@updateGraphicsCustomisation');
    });
    
    Route::group(['middleware' => ['auth', 'checkModuleEnable'],], function () {
        Route::put('event/user', 'EventController@eventUserUpdateRole');
    });;
});

/*
 * V1: These are the api's which can be directly re-usable to latest version
 */
Route::group(['middleware' => ['cors'], 'prefix' => 'kct-admin', 'namespace' => 'Modules\Cocktail\Http\Controllers\V1\AdminSideControllers'], function () {
    // options routes for allowing header so cors resolved
    Route::group([], function () {
        $cors = function () {
        };
        /* Event Settings update */
        Route::group(['prefix' => 'event'], function () use ($cors) {
            Route::options('kct-customization', $cors);
            Route::options('kct-customization/{event_uuid}', $cors);
            Route::options('registration-details', $cors);
            Route::options('registration-details/{event_uuid}', $cors);
            Route::options('blj-settings/{event_uuid}', $cors);
        });
        
        /* Event Space APi's */
        Route::group(['prefix' => 'event/space'], function () use ($cors) {
            Route::options('all/{event_uuid}', $cors);
            Route::options('re-order', $cors);
        });
        Route::options('search/user/host', $cors);
        Route::options('stock/upload', $cors);
        
        /* Event User */
        Route::group(['prefix' => 'event/user'], function () use ($cors) {
            Route::options('', $cors);
            Route::options('/admin', $cors);
        });
        Route::options('search/event/user', $cors);
        
        /* Org Settings */
        Route::options('reminder/page', $cors);
        Route::options('reminder/page', $cors);
    });
    
    Route::group(['middleware' => ['auth', 'checkModuleEnable'],], function () {
        /* Event Settings update */
        Route::group(['prefix' => 'event'], function () {
            Route::put('kct-customization', 'KctEventController@updateKeepContactCustomization');
            Route::delete('kct-customization', 'KctEventController@deleteGraphicsLogo');
            Route::get('kct-customization/{event_uuid}', 'KctEventController@getEventGraphicsCustomization')->middleware('eventAdmin:route');
            Route::put('registration-details', 'KctEventController@updateRegistrationFormDetail');
            Route::get('registration-details/{event_uuid}', 'KctEventController@getEventRegistrationDetails')->middleware('eventAdmin:route');
            Route::get('blj-settings/{event_uuid}', 'KctEventController@getEventBlueJeansDetails')->middleware('eventAdmin:route');
        });
        
        /* Event Space APi's */
        Route::group(['prefix' => 'event/space'], function () {
            Route::delete('/', 'EventSpaceController@delete');
            Route::put('re-order', 'EventSpaceController@resortSpaceList');
        });
        Route::get('search/user/host', 'EventSpaceController@searchEventUser')->middleware('eventAdmin:req');
        Route::post('stock/upload', 'EventSpaceController@uploadStockImage');
        
        /* Event User */
        Route::group(['prefix' => 'event/user'], function () {
            Route::get('/', 'KctEventController@eventUsers')->middleware('eventAdmin:req');
            Route::delete('/', 'KctEventController@eventUserRemove');
            Route::put('/admin', 'KctEventController@updateAdminRole');
        });
        Route::get('search/event/user', 'KctEventController@eventUsersSearch')->middleware('eventAdmin:req');
        
        /* Org Settings */
        Route::get('reminder/page', 'ReminderController@get');
        Route::post('reminder/page', 'ReminderController@update');
    });
    
    // admin side routes but escape the kct enable check
    Route::group(['middleware' => ['api', 'auth'],], function () {
        Route::post('event/user', 'KctEventController@eventUserAdd');
    });
});
