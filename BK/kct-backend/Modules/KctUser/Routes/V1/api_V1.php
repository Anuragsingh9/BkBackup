<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\KctUser\Http\Controllers\V1\ConversationController;
use Modules\KctUser\Http\Controllers\V1\EventController;
use Modules\KctUser\Http\Controllers\V1\CoreController;
use Modules\KctUser\Http\Controllers\V1\SpaceController;
use Modules\KctUser\Http\Controllers\V1\UserBadgeController;
use Modules\KctUser\Http\Controllers\V1\UserController;
use Modules\KctUser\Http\Controllers\V1\WebHookController;
use Modules\KctUser\Services\KctService;

//Route::middleware('auth:api')->get('/kctuser', function (Request $request) {
//    return $request->user();
//});

// new version 1 api here

Route::get('test1',function (){

});

Route::group(['middleware' => ['cors','setKctLocaleMiddleware'], 'prefix' => 'kct', 'namespace' => 'Modules\KctUser\Http\Controllers\V1'], function () {

    Route::group(['middleware' => ['api', 'auth:api', 'userEmailVerified', 'checkModuleEnable']], function () {

        //store ban user
        // user tag related api's
        Route::post('tag/add', [EventController::class,'getUserEvents']);
        Route::post('tag/delete', [EventController::class,'getUserEvents']);


    });





    Route::group(['middleware' => ['api', 'auth:api', 'checkModuleEnable']], function () {

    });
});


/*  - Kct Front Side Version 1 api's , now they can be accessed via v2 as there is no change in it */
Route::group(['middleware' => ['cors', 'setKctLocaleMiddleware'], 'prefix' => 'kct', 'namespace' => 'Modules\Cocktail\Http\Controllers\V1\UserSideControllers'], function () {
    // KCT Front side apis,
    // these all are based on api access token also the user must be have email verified account
    Route::group(['middleware' => ['api', 'auth:api', 'userEmailVerified', 'checkModuleEnable']], function () {
        // page initialization api
        Route::put('event/dnd', [EventController::class,'toggleDnd']);
        // space related api
        /** @deprecated */
        Route::get('event/space/{spaceUuid}', [SpaceController::class,'getSpaceWithConversation']);

        // user badge api
//        Route::get('badge', 'UserController@getBadge');
        Route::put('users/badges/socials', [UserBadgeController::class,'updateSocialLink']);
        Route::delete('users/badges/socials', [UserBadgeController::class,'deleteSocialLink']);
        // user profile api
        Route::put('users/password', [UserController::class,'updatePassword']);
        Route::get('users/profiles', [UserController::class,'getUserProfile']);
        Route::put('users/profiles', [UserController::class,'updateProfile']);

    });

    // Api's which don't require the email to be verified
    Route::group(['middleware' => ['api', 'auth:api', 'checkModuleEnable']], function () {
    });

    // Kct Front - Public Apis for login reg related tasks
    Route::group(['middleware' => ['checkModuleEnable'],], function () {
        Route::get('events/list', [CoreController::class,'getEventsList']);

    });
});


