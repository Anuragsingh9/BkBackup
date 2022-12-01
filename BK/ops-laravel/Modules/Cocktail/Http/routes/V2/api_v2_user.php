<?php
// new version 2 api here
Route::group(['middleware' => ['cors'], 'prefix' => 'kct', 'namespace' => 'Modules\Cocktail\Http\Controllers\V2\UserSideControllers'], function () {

    Route::group(['middleware' => ['api', 'auth:api', 'userEmailVerified', 'checkModuleEnable']], function () {
        Route::get('badge', 'UserController@getBadge');
        Route::post('tag-delete', 'UserController@deleteUserTag');
        Route::post('add-tag', 'UserController@addUserTag');
        Route::get('get-user-tags', 'UserController@getUserTags');
        Route::put('users/badges/visibility', 'UserController@updateVisibility');
        Route::put('users/badges/profiles', 'UserController@updateProfileField');
        Route::put('users/badges/entities', 'UserController@updateEntity');
        Route::delete('users/badges/entities', 'UserController@deleteEntity');
        // conversation related api
        Route::post('event/space/conversation/join', 'KctController@conversationJoin');
        Route::delete('event/space/conversation/leave', 'KctController@conversationLeave');
        Route::get('event/space/conversation/{eventUuid}', 'KctController@getCurrentConversation')->middleware('isEventMember:route');
        Route::delete('users/profiles/pictures', 'UserController@deleteProfilePicture');
        Route::post('tag/add', 'KctController@getUserEvents');
        Route::post('tag/delete', 'KctController@getUserEvents');
        Route::get('events', 'KctController@getUserEvents');

        Route::post('users/tag/create', 'UserController@createUserTag');
        Route::get('users/tag/search', 'UserController@searchTag');
        Route::delete('users/tag/delete', 'UserController@removeTag');
        Route::put('users/tag/attach', 'UserController@attachTag');

        Route::put('users/info/update', 'UserController@updatePersonalInfo');
        // que-system related apis
        Route::post('queue/logs','QueueController@storeUserQueLogs');
        Route::get('queue/missed/calls','QueueController@getMissedCalls');
        Route::get('queue/search', 'QueueController@search');
        Route::get('queue/rejected/calls','QueueController@getRejectedCalls');

    });

    Route::group(['middleware' => ['cors', 'api', 'kct-node']], function () {
        Route::post('/node/conversation/join', 'NodeController@addUserInConversation');
        Route::get('/node/event/dummies', 'NodeController@getEventDummyUsers');
    });
    
    Route::group(['middleware' => ['checkModuleEnable'],], function () {
        Route::post('login', 'UserController@login');
        Route::get('/graphics/customization', 'KctController@getCustomGraphics');
        Route::get('event/{eventUuid}', 'KctController@getEventBeforeRegister');
    });
    
    Route::group(['middleware' => ['api', 'auth:api', 'checkModuleEnable']], function () {
        Route::get('/login-join-data', 'KctController@getDataForFirstLogin');
        Route::post('send/invite','KctController@sendInvitationEmail');
        Route::post('quick-join-event','KctController@joinEvent');
    });
});

/*  - Kct Front Side Version 1 api's , now they can be accessed via v2 as there is no change in it */
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
        Route::options('tag-delete', $cors);
        Route::options('add-tag', $cors);
        Route::options('get-user-tags', $cors);
        Route::options('users/badges/profiles', $cors);
        Route::options('users/badges/socials', $cors);
        Route::options('users/badges/entities', $cors);
        Route::options('users/badges/visibility', $cors);
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
        Route::options('/login-join-data', $cors);
        Route::options('send/invite', $cors);
        Route::options('quick-join-event', $cors);

        Route::options('users/tag/create', $cors);
        Route::options('users/tag/search', $cors);
        Route::options('users/tag/delete', $cors);
        Route::options('users/tag/attach', $cors);

        Route::options('users/info/update', $cors);
    });
    
    // KCT Front side apis,
    // these all are based on api access token also the user must be have email verified account
    Route::group(['middleware' => ['api', 'auth:api', 'userEmailVerified', 'checkModuleEnable']], function () {
        // page initialization api
        Route::get('event/embedded-url/{eventUuid}', 'KctEventController@getEventEmbeddedUrl')->middleware('isEventMember:route');
        Route::put('event/dnd', 'KctEventController@toggleDnd');
        // space related api
        /** @deprecated */
        Route::get('event/space/{spaceUuid}', 'EventSpaceController@getSpaceWithConversation');
        
        // user badge api
//        Route::get('badge', 'UserController@getBadge');
        Route::put('users/badges/socials', 'UserController@updateSocialLink');
        Route::delete('users/badges/socials', 'UserController@deleteSocialLink');
        Route::get('users/badges/entity/search/{val}/{type}', 'UserController@searchEntity');
        // user profile api
        Route::put('users/password', 'UserController@updatePassword');
        Route::get('users/profiles', 'UserController@getUserProfile');
        Route::put('users/profiles', 'UserController@updateProfile');
        Route::get('users/settings', 'UserController@getUserLevelData');
        
    });
    
    // Api's which don't require the email to be verified
    Route::group(['middleware' => ['api', 'auth:api', 'checkModuleEnable']], function () {
        Route::put('users/lang', 'UserController@updateLang');
    });
    
    // Kct Front - Public Apis for login reg related tasks
    Route::group(['middleware' => ['checkModuleEnable'],], function () {
        Route::post('register', 'KctController@register');
        Route::get('events/list', 'KctController@getEventsList');
        Route::post('verify/email', 'KctController@otpVerify')->middleware('auth:api');
        Route::post('resend/verify/email', 'KctController@resendVerificationLink')->middleware('auth:api');
        Route::post('events/join', 'KctEventController@joinEvent')->middleware('auth:api');
        Route::post('logout', 'KctController@logout')->middleware('auth:api');
        Route::post('users/password/forget', 'KctController@forgetPassword');
        Route::post('users/password/reset', 'KctController@resetPassword');
    });
});

