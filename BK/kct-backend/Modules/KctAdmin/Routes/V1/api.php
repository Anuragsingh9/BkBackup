<?php

use Illuminate\Support\Facades\Route;
use Modules\KctAdmin\Http\Controllers\V1\EventController;
use Modules\KctAdmin\Http\Controllers\V1\GroupController;
use Modules\KctAdmin\Http\Controllers\V1\SettingController;
use Modules\KctAdmin\Http\Controllers\V1\SpaceController;
use Modules\KctAdmin\Http\Controllers\V1\TagController;
use Modules\KctAdmin\Http\Controllers\V1\UserController;
use Modules\KctAdmin\Http\Middleware\CheckUserLoginCount;
use Modules\UserManagement\Http\Controllers\UserManagementController;

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

Route::group(['middleware' => ['auth:api','checkUserLoginCount','canUserAccessGroup']], function () {
    Route::post('logout', [UserController::class, 'logout']);

    // users manage api's
    Route::group(['prefix' => '/users'], function () {
        Route::post('/multi', [UserController::class, 'addMultipleUser']);
        Route::delete('/multi', [UserController::class, 'removeMultipleUser']);
        Route::get('/', [UserController::class, 'getUserById'])->withoutMiddleware('checkUserLoginCount');
        Route::put('/', [UserController::class, 'updateUserProfile']);
        Route::post('/field', [UserController::class, 'updateProfileByField']);

        Route::post('/import/step1', [UserController::class, 'importStep1']);
        Route::post('/import/step2', [UserController::class, 'importStep2']);

        Route::delete('/entities', [UserController::class, 'detachEntity']);
        Route::get('/search/{groupKey?}', [UserController::class, 'searchUser']);

        Route::put('/roles', [UserController::class, 'updateUserRole']);
    });

    Route::prefix('entities')->group(function () {
        Route::get('/search', [UserController::class, 'searchEntity']);
    });

    // event api's
    Route::group(['prefix' => 'events'], function () {
        Route::post("/", [EventController::class, 'createEvent']);
        Route::put("/", [EventController::class, 'updateVirtualEvent']);
        Route::delete("/", [EventController::class, 'deleteEvent']);
        Route::get("/list/{groupKey}", [EventController::class, 'getEvents']);
        Route::get("/find", [EventController::class, 'find']);
        Route::get("/links", [EventController::class, 'getAccessLink']);
        Route::get("/users", [EventController::class, 'getParticipants']);
        Route::post('/moments', [EventController::class, 'updateMoments']);
        Route::get('/moments', [EventController::class, 'getMoments']);
        Route::put('/participants', [EventController::class, 'updateMultiUserRole']);
        Route::delete('/participants', [EventController::class, 'removeMultiUserRole']);
        Route::get('/draft/all',[EventController::class,'getDraftEvents']);
        Route::get('/draft/find',[EventController::class,'findDraftEvent']);
        Route::put('draft/update',[EventController::class,'updateDraftEvent']);
        Route::get('/min/list/{groupKey}', [EventController::class, 'getMinEvents']);
        Route::post('/live/settings',[EventController::class,'createLiveSettingData']);
        Route::get('/live/settings',[EventController::class,'getLiveSettingData']);
        Route::delete('/live/settings', [EventController::class,'deleteLiveSettingData']);
        Route::post('/invite/email',[EventController::class,'inviteEventUsers']);
        Route::post('/validate/join-code',[EventController::class,'validateJoinCode']);
    });

    // space api's
    Route::prefix('spaces')->group(function () {
        Route::post("/", [SpaceController::class, 'store']);
        Route::put("/", [SpaceController::class, 'update']);
        Route::get('/', [SpaceController::class, 'getEventSpaces']);
        Route::delete('/', [SpaceController::class, 'delete']);
        Route::post("/scenery",[SpaceController::class,'createSceneryData']);
    });

    // organiser tag api's
    Route::prefix('tags')->group(function () {
        Route::get('/all/{groupKey}', [TagController::class, 'getGroupTags']);
        Route::post('/', [TagController::class, 'store']);
        Route::put('/', [TagController::class, 'update']);
        Route::delete('/', [TagController::class, 'destroy']);
    });

    // group api's
    Route::prefix('groups')->group(function () {
        Route::post('/', [GroupController::class, 'createGroup']);
        Route::get('/single/{groupKey}', [GroupController::class, 'getGroup']);
        Route::put('/', [GroupController::class, 'updateGroup']);
        Route::delete('/', [GroupController::class, 'deleteGroup']);
        Route::get('/fetch/list', [GroupController::class, 'getAllGroups']);
        Route::get('/users/{groupKey}', [GroupController::class, 'getGroupUsers']);
        Route::post('/multi/organisers', [GroupController::class, 'addMultipleOrganiser']);
        Route::get('/settings/{groupKey}', [GroupController::class, 'getGroupSettings']);
        Route::put('/settings', [GroupController::class, 'updateSettings']);
        Route::get('/settings/technical/{groupKey}', [SettingController::class, 'getTechnicalSettings']);
        Route::put('/settings/technical', [SettingController::class, 'updateTechnicalSettings']);
    });


    Route::prefix('labels')->group(function () {
        Route::post('/', [SettingController::class, 'updateLabel']);
        Route::get('/{groupKey}', [SettingController::class, 'getLabels']);
    });
});

Route::get('handler/zoom/oAuth/{groupKey}', [GroupController::class, 'handleZoomOAuth'])
    ->name('zoomHandler');

// user's api related to login and password
Route::group([],function (){
    Route::post('/forgot-password', [UserManagementController::class, 'sendResetPassword']);
    Route::Post('/reset-password', [UserManagementController::class, 'resetPasswordApi']);
    Route::post('/login', [UserManagementController::class, 'adminLogin']);
    Route::post('/default/password', [UserManagementController::class, 'changeDefaultPassword']);
});
