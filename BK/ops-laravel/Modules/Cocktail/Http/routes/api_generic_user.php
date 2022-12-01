<?php

Route::group(['middleware' => ['cors'], 'prefix' => 'kct', 'namespace' => 'Modules\Cocktail\Http\Controllers\Generic\UserSideControllers'], function () {
    // public apis
    Route::get('init/data', 'KctController@initData');
    
    // event specific apis
    Route::group(['middleware' => ['api', 'auth:api', 'userEmailVerified', 'checkModuleEnable']], function () {
        // page initialization api
        Route::get('event/kct-customization/{eventUuid}', 'KctController@getEventsData')->middleware('isEventMember:route');
        Route::get('event/space/all/{eventUuid}', 'KctController@getSpacesAndConversation')->middleware('isEventMember:route');
        Route::post('event/space/join', 'KctController@spaceJoin');
        
    });
});