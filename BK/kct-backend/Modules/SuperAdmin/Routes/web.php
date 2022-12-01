<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Modules\SuperAdmin\Http\Controllers\AccountController;
use Modules\SuperAdmin\Http\Controllers\TagController;
use Modules\SuperAdmin\Http\Controllers\SuperAdminController;

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

Route::get('/', [SuperAdminController::class, 'index'])->name('index')->middleware('localisation');
Route::get('/lang/{lang}', [AccountController::class, 'setLang'])->name('change-lang');

// account level api's (below superadmin)
Route::group(['middleware' => ['localisation']], function () {
    // signup step 1 - enter email
    Route::view('/signup', 'superadmin::auth.signup.signup_step_1')->name('su-account-create-1');
    Route::post('/signup/s1', [AccountController::class, 'signupStep1'])->name('su-signup-s1');

    Route::group(['middleware' => 'inSignupProcess'], function () {
        // signup step 1-b - Verify the email
        Route::get('/signup/verify_email', [AccountController::class, 'getOtpPage'])->name('su-account-create-1-2');
        Route::post('/signup/s1/resend-otp', [AccountController::class, 'otpResend'])->name('su-resend-otp');
        Route::post('/signup/s1/verify-email', [AccountController::class, 'verifyEmail'])->name('su-signup-s1-2');

        // signup step 2 - Create account
        Route::view('/signup/account', 'superadmin::auth.signup.signup_step_2')->name('su-account-create-2');
        Route::post('/signup/s2', [AccountController::class, 'signupStep2'])->name('su-signup-s2');
    });

    // To check the account name to redirect that account login
    Route::post('/account/check', [AccountController::class, 'accountNameCheck'])->name('su-account-check');

    // Account password forget
    Route::view('/account/forget', 'superadmin::auth.account_forget')->name('su-account-forget');
    Route::post('/account/forget', [AccountController::class, 'accountNameForget'])->name('su-account-forget-sub');
});

// public routes without authentication
Route::group(['middleware' => ['localisation', 'onlyNonLogin'], 'prefix' => '/superadmin'], function () {
    // To submit signup form step wise
    Route::view('/signin', 'superadmin::auth.signin.signin')->name('su-signin');
    Route::post('/signin', [SuperAdminController::class, 'superAdminSignin'])->name('su-do-signin');
    Route::view('/forgot-password', 'superadmin::auth.forgot_password')->name('su-forgot-password');
    Route::post('forgot-password', [SuperAdminController::class, 'forgotPassword'])->name('forgot-pwd-email');
    Route::get('/reset-password/{email}/{key}', [SuperAdminController::class, 'resetView']);
    Route::post('/reset-password', [SuperAdminController::class, 'resetPassword'])->name('reset-password');
});

// super admin access routes only
Route::group(['middleware' => ['suAuth', 'localisation'], 'prefix' => '/superadmin'], function () {
    Route::get('/account/list', [AccountController::class, 'accountList'])->name('su-account-list');
    Route::get('/account/setting/{accountId}', [AccountController::class, 'getAccountSetting'])->name('su-account-setting');
    Route::post('/account/setting/{accountId}', [AccountController::class, 'updateAccountSetting'])->name('su-account-setting-update');
    Route::get('/logout', [SuperAdminController::class, 'logout'])->name('su-logout');

    Route::get('/access/{hostnameId}/{redirectUrl?}', [AccountController::class, 'access'])->name('su-account-access');

    // Instant (Bulk) Account Creation
    Route::view('/instant-acc', 'superadmin::instant-account.create')->name('su-instant-account-create');
    Route::post('/instant-acc', [AccountController::class, 'instantAccountCreate'])->name('su-instant-acc-store');

    // Tag Moderation
    Route::get('/tag/moderate/index/{tagType}', [TagController::class, 'index'])->name('su-tag-moderation');
    Route::post('/tag/moderate/import/{tagType}', [TagController::class, 'import'])->name('su-tag-import');
    Route::get('/tag/moderate/export/{tagType}', [TagController::class, 'export'])->name('su-tag-export');
    Route::post('/tag/moderate/update', [TagController::class, 'updateTag'])->name('su-tag-update');
    Route::post('/tag/moderate/accept', [TagController::class, 'acceptTag'])->name('su-tag-accept');
    Route::post('/tag/moderate/reject', [TagController::class, 'rejectTag'])->name('su-tag-reject');
    Route::get('/tag/moderate/search', [TagController::class, 'searchTag'])->name('su-tag-search');
    Route::post('/tag/moderate/merge', [TagController::class, 'rejectTag'])->name('su-tag-merge');

    Route::get('/settings', [SuperAdminController::class, 'getGeneralSettings'])->name('su-general-settings');
    Route::post('/settings', [SuperAdminController::class, 'setGeneralSettings'])->name('su-save-settings');

    // Account delete
    Route::get('/account/delete/{accountId}',[SuperAdminController::class,'deleteAccount'])->name('su-delete-acc');
});

