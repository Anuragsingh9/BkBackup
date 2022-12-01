<?php
// Kct V1 Admin Side Apis
Route::group([
    'middleware' => ['cors'],
    'prefix'     => 'kct-admin',
    'namespace'  => 'Modules\Cocktail\Http\Controllers\V1\AdminSideControllers'
], function () {
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
            Route::options('/', $cors);
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
    
    // the namespace for these routes is decided from the RouteServiceProvider in app/providers
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
            Route::post('/', 'EventSpaceController@store');
            Route::get('/', 'EventSpaceController@getSpace');
            Route::put('/', 'EventSpaceController@update');
            Route::delete('/', 'EventSpaceController@delete');
            Route::get('all/{event_uuid}', 'EventSpaceController@getEventSpacesForAdmin')->middleware('eventAdmin:route');
            Route::put('re-order', 'EventSpaceController@resortSpaceList');
        });
        Route::get('search/user/host', 'EventSpaceController@searchEventUser')->middleware('eventAdmin:req');
        Route::post('stock/upload', 'EventSpaceController@uploadStockImage');
        
        /* Event User */
        Route::group(['prefix' => 'event/user'], function () {
            Route::get('/', 'KctEventController@eventUsers')->middleware('eventAdmin:req');
            Route::put('/', 'KctEventController@eventUserUpdateRole');
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
