<?php
/*  - Kct Front Side Api's for Version 1 */
Route::group(['middleware' => ['cors'], 'prefix' => 'kct', 'namespace' => 'Modules\Cocktail\Http\Controllers\V1\UserSideControllers'], function () {
    // options routes for allowing header so cors resolved
    Route::group([], function () {
        $cors = function () {
        };
        Route::options('event/kct-customization/{event_uuid}', $cors);
        Route::options('event/embedded-url/{eventUuid}', $cors);
        Route::options('event/space/all/{eventUuid}', $cors);
        Route::options('event/space/{spaceUuid}', $cors);
        Route::options('event/space/join', $cors);
        Route::options('event/space/conversation/join', $cors);
        Route::options('event/space/conversation/leave', $cors);
        Route::options('event/space/conversation/{event_uuid}', $cors);
        Route::options('badge', $cors);
        Route::options('users/badges/profiles', $cors);
        Route::options('users/badges/socials', $cors);
        Route::options('users/badges/entities', $cors);
        Route::options('users/badges/entity/search/{val}/{type}', $cors);
        Route::options('users/password', $cors);
        Route::options('users/profiles', $cors);
        Route::options('events', $cors);
        Route::options('users/profiles/pictures', $cors);
        Route::options('users/lang', $cors);
        Route::options('users/settings', $cors);
        
        Route::options('login', $cors);
        Route::options('register', $cors);
        Route::options('events/list', $cors);
        Route::options('event/{eventUuid}', $cors);
        Route::options('verify/email', $cors);
        Route::options('resend/verify/email', $cors);
        Route::options('events/join', $cors);
        Route::options('logout', $cors);
        Route::options('users/password/forget', $cors);
        Route::options('users/password/reset', $cors);
        Route::options('init/data', $cors);
    });
    
    // KCT Front side apis,
    // these all are based on api access token also the user must be have email verified account
    Route::group(['middleware' => ['api', 'auth:api', 'userEmailVerified', 'checkModuleEnable']], function () {
        // page initialization api
        Route::get('event/kct-customization/{eventUuid}', 'KctEventController@getEventGraphicsDetails')->middleware('isEventMember:route');
        Route::get('event/embedded-url/{eventUuid}', 'KctEventController@getEventEmbeddedUrl')->middleware('isEventMember:route');
        Route::put('event/dnd', 'KctEventController@toggleDnd');
        // space related api
        Route::get('event/space/all/{eventUuid}', 'EventSpaceController@getEventSpacesForUser')->middleware('isEventMember:route');
        /** @deprecated */
        Route::get('event/space/{spaceUuid}', 'EventSpaceController@getSpaceWithConversation');
        Route::post('event/space/join', 'EventSpaceController@spaceJoin');
        // conversation related api
        Route::post('event/space/conversation/join', 'EventSpaceController@conversationJoin');
        Route::delete('event/space/conversation/leave', 'EventSpaceController@conversationLeave');
        Route::get('event/space/conversation/{eventUuid}', 'EventSpaceController@getCurrentConversation')->middleware('isEventMember:route');
        // user badge api
        Route::get('badge', 'UserController@getBadge');
        Route::put('users/badges/profiles', 'UserController@updateProfileField');
        Route::put('users/badges/socials', 'UserController@updateSocialLink');
        Route::delete('users/badges/socials', 'UserController@deleteSocialLink');
        Route::put('users/badges/entities', 'UserController@updateEntity');
        Route::delete('users/badges/entities', 'UserController@deleteEntity');
        Route::get('users/badges/entity/search/{val}/{type}', 'UserController@searchEntity');
        // user profile api
        Route::put('users/password', 'UserController@updatePassword');
        Route::get('users/profiles', 'UserController@getUserProfile');
        Route::put('users/profiles', 'UserController@updateProfile');
        Route::get('events', 'KctEventController@getUserEvents');
        Route::delete('users/profiles/pictures', 'UserController@deleteProfilePicture');
        Route::put('users/lang', 'UserController@updateLang');
        Route::get('users/settings', 'UserController@getUserLevelData');
        
    });
    
    // Kct Front - Public Apis for login reg related tasks
    Route::group(['middleware' => ['checkModuleEnable'],], function () {
        Route::post('login', 'KctController@login');
        Route::post('register', 'KctController@register');
        Route::get('events/list', 'KctController@getEventsList');
        Route::get('event/{eventUuid}', 'KctController@getEventBeforeRegister');
        Route::post('verify/email', 'KctController@otpVerify')->middleware('auth:api');
        Route::post('resend/verify/email', 'KctController@resendVerificationLink')->middleware('auth:api');
        Route::post('events/join', 'KctEventController@joinEvent')->middleware('auth:api');
        Route::post('logout', 'KctController@logout')->middleware('auth:api');
        Route::post('users/password/forget', 'KctController@forgetPassword');
        Route::post('users/password/reset', 'KctController@resetPassword');
    });
    
    // These apis are independent of module enable
    Route::group([], function () {
        Route::get('init/data', 'KctController@initData');
    });
});
