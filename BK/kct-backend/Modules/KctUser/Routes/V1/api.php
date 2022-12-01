<?php

use Illuminate\Support\Facades\Route;
use Modules\KctUser\Http\Controllers\V1\CoreController;
use Modules\KctUser\Http\Controllers\V1\UserBadgeController;
use Modules\KctUser\Http\Controllers\V1\ConversationController;
use Modules\KctUser\Http\Controllers\V1\EventController;
use Modules\KctUser\Http\Controllers\V1\SpaceController;
use Modules\KctUser\Http\Controllers\V1\UserController;
use Modules\KctUser\Http\Controllers\V1\WebHookController;
use Modules\KctUser\Http\Controllers\V4\LogController;

Route::get('test', function(\Illuminate\Http\Request  $request) {
    $service = app(\Modules\KctUser\Services\BusinessServices\factory\KctService::class);
    $service->checkOfflineUser();
});

// Public Apis
// user authorization api's
Route::group(['middleware' => ['cors']], function () {
    Route::get('init/data', [CoreController::class, 'initData'])->middleware("localisation");
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/users/password/forget', [CoreController::class, 'forgetPassword']);
    Route::get('/graphics/customization', [CoreController::class, 'getCustomGraphics'])->middleware("localisation");
    Route::get('/graphics/event/{eventUuid}', [EventController::class, 'getGroupSettingByEvent']);
    Route::get('event/{eventUuid}', [EventController::class, 'getEventBeforeRegister']);
    Route::post('/users/password/reset', [CoreController::class, 'resetPassword']);
    Route::get('/otp/page/data',[CoreController::class,'getOtpPageData']);
    Route::post('/verify/email', [CoreController::class, 'otpVerify']);
    Route::get('/verify/emailByLink', [CoreController::class, 'verifyUserByMagicLink']);
});

// User api without email verify check
Route::group(['middleware' => ['auth:api', 'cors']], function () {
//    Route::post('/verify/email', [CoreController::class, 'otpVerify']);
    Route::post('/resend/verify/email', [CoreController::class, 'resendVerificationLink']);
    Route::post('/logout', [CoreController::class, 'logout']);
    Route::post('/change/password',[CoreController::class,'changePassword']);
    Route::post('/logs', [CoreController::class, 'createLogs']);
});

Route::group(['middleware' => ['auth:api', 'cors']], function () {
    Route::group(['middleware' => ['userVerified']], function () {
        // application api
        Route::get('/users/settings', [UserController::class, 'getUserLevelData']);

        // events api
        Route::get('/events', [EventController::class, 'getEventsList']);
        Route::post('/events/join', [EventController::class, 'eventJoin']);
        Route::get('/event/kct-customization/{eventUuid}', [EventController::class, 'getEventsData']);
        Route::get('/event/embedded-url/{eventUuid}', [EventController::class, 'getEventEmbeddedUrl']);

        // space api
        Route::get('/event/space/all/{eventUuid}', [SpaceController::class, 'getSpacesAndConversation']);
        Route::post('/event/space/join', [SpaceController::class, 'spaceJoin']);

        // conversation api
        Route::post('/event/space/conversation/join', [ConversationController::class, 'conversationJoin']);
        Route::delete('/event/space/conversation/leave', [ConversationController::class, 'conversationLeave']);
        Route::get('/event/space/conversation/{eventUuid}', [ConversationController::class, 'getCurrentConversation']);
        Route::post('/change/conversion/type', [ConversationController::class, 'changeConversationType']);

        // badge api
        Route::get('/badge', [UserBadgeController::class, 'getBadge']);
        Route::put('/users/badges/visibility', [UserBadgeController::class, 'updateVisibility']);
        Route::put('/users/badges/profiles', [UserBadgeController::class, 'updateProfileField']);
        Route::put('/users/badges/entities', [UserBadgeController::class, 'updateEntity']);
        Route::delete('/users/badges/entities', [UserBadgeController::class, 'deleteEntity']);
        Route::get('/users/badges/entity/search/{val}/{type}', [UserBadgeController::class, 'searchEntity']);
        Route::delete('/users/profiles/pictures', [UserBadgeController::class, 'deleteProfilePicture']);
        Route::put('/users/info/update', [UserBadgeController::class, 'updatePersonalInfo']);

        // user tags api
        Route::post('/users/tag/create', [UserBadgeController::class, 'createUserTag']);
        Route::get('/users/tag/search', [UserBadgeController::class, 'searchTag']);
        Route::delete('/users/tag/delete', [UserBadgeController::class, 'removeTag']);
        Route::put('/users/tag/attach', [UserBadgeController::class, 'attachTag']);

        // organiser tags API
        Route::post('/add-tag', [UserBadgeController::class, 'addUserTag']);
        Route::post('/tag-delete', [UserBadgeController::class, 'deleteUserTag']);
        Route::get('/get-user-tags', [UserBadgeController::class, 'getUserTags']);

        // miscellaneous api
        Route::post('/event/user/ban', [EventController::class, 'storeUserBan']);

        // quick sign-in sign-up api's
        Route::get('/login-join-data', [CoreController::class, 'getDataForFirstLogin']);
        Route::post('/send/invite', [CoreController::class, 'sendInvitationEmail']);
        Route::post('/quick-join-event', [EventController::class, 'joinEvent']);

        // p-demo api's
        Route::post('/panel/current/section',[EventController::class,'updatePanelCurrentSection']);
    });
    // user profile api's
    Route::put('/users/lang', [UserController::class, 'updateLang']);
});

Route::group(['middleware' => ['cors']], function () {
    Route::post('/node/conversation/join', [ConversationController::class, 'addUserInConversation']); // earlier in NodeController
    Route::get('/node/event/dummies', [ConversationController::class, 'getEventDummyUsers']); // earlier in NodeController
    Route::post('/node/conversation/remove-dummy', [ConversationController::class, 'removeDummyUser']); // earlier in NodeController
    Route::post('/node/log/attendance', [LogController::class, 'createAttendanceLog']);
    Route::post('/node/log/pilot', [LogController::class, 'createPilotContentLog']);
});

// webhooks api
Route::group(['prefix' => 'webhook', 'middleware' => ['cors']], function () {
    Route::get('/zoom', [WebHookController::class, 'handleZoomWebinar']);
    Route::post('/zoom/webinar', [WebHookController::class, 'updateWebinarStatus']);
    Route::post('/zoom/during-meeting', [WebHookController::class, 'handleDuringMeetingUpdate']);
});

