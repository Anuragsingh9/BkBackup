<?php
    Route::group(['middleware' => ['cors'], 'prefix' => 'consultation/user'], function () {
        Route::options('/', function () {
            return;
        });

        Route::options('/{consultationId?}', function () {
            return;
        });
        Route::options('/update-union', function () {
            return;
        });
        Route::options('sprint/{sprintId}/steps', function () {
            return;
        });
        Route::options('/step/{stepId}/questions', function () {
            return;
        });

        Route::options('/question-friend-mail', function () {
            return;
        });
        Route::options('/checkCount', function () {
            return;
        });
        Route::options('/change-language', function () {
            return;
        });
    });
    Route::group(['middleware' => ['cors', 'auth:api'/* 'CheckValidRequest'*/], 'prefix' => 'consultation/user'], function () {
        Route::post('question/mail', 'ResilienceFrontController@sendMail');
        Route::get('/{consultationId?}', 'ResilienceFrontController@show');
        Route::get('token/{user}', 'ResilienceFrontController@createUserToken');
        Route::get('/step/{stepId}/questions', 'ResilienceFrontController@getQuestionById');
        Route::post('/save-answer', 'ResilienceFrontController@answer');
        Route::post('/update-answer', 'ResilienceFrontController@updateAnswer');
        Route::get('sprint/{sprintId}/steps', 'ResilienceFrontController@stepsBySprintId');
        Route::post('/update-union', 'ResilienceFrontController@updateUnionPosition');
        //feature mail route
        Route::post('/feature-mail', '\App\Http\Controllers\FeatureController@featureMails');
        Route::post('/question-friend-mail', 'QuestionController@sendQuestionMail');
        Route::post('/checkCount', 'ResilienceFrontController@checkCount');
        /*----language route----*/
        Route::post('/change-language', 'ResilienceFrontController@languageChange');
    });

    Route::get('consultation/auth-token', 'ResilienceController@getAuthToken');
    Route::get('consultation-sqlToCheckMeetingDel/{id}', 'ResilienceFrontController@sqlToCheckMeetingDel');
    Route::get('/{consultationId?}', 'ResilienceFrontController@show')->middleware('auth');

    Route::group(['middleware' => ['cors'], 'prefix' => 'consultation'], function () {
        Route::options('/step/{consultationStep}/question', function () {
            return;
        });
        Route::options('/auth-token', function () {
            return;
        });
        Route::options('/question/{consultationQuestionId}', function () {
            return;
        });

        Route::options('/change-password', function () {
            return;
        });
        Route::options('get-consultation/{key}/{workshopId}', function () {
            return;
        });

        Route::options('/reinvent/page/logo', function () {
            return;
        });
        Route::options('/get-meeting-step/{consultationStepId}/{sprintId}', function () {
            return;
        });
        Route::options('delete-step-meeting/{id}', function () {
            return;
        });
        Route::options('/reminder/page', function () {
            return;
        });
        Route::options('login', function () {
            return;
        });
        Route::options('/reinvent-forgot-password', function () {
            return;
        });
        Route::options('/reinvent-set-password', function () {
            return;
        });
        Route::options('/sprint-meeting-step/{sprintId}', function () {
            return;
        });
        Route::options('class', function () {
            return;
        });
        Route::options('class/{class}', function () {
            return;
        });
        Route::options('class/get-classes/{type}', function () {
            return;
        });
        Route::options('class/get-classes', function () {
            return;
        });
        Route::options('position', function () {
            return;
        });
        Route::options('position/{position}', function () {
            return;
        });
        Route::options('drag-order', function () {
            return;
        });
        Route::get('/send-reminder', 'ResilienceController@checkReminder');

        Route::post('login', 'ResilienceFrontController@apiLogin');
        Route::post('register', 'ResilienceFrontController@register');
        Route::post('verify-code', 'ResilienceFrontController@checkCode');
        Route::post('/change-password', 'ResilienceFrontController@changePassword')->middleware('auth:api');
        Route::post('/reinvent-forgot-password', 'ResilienceFrontController@forgotPassword');
        Route::post('/reinvent-set-password', 'ResilienceFrontController@setPassword');

        // Route::post('/step/asset/add', 'ResilienceController@addAsset');
        // Route::post('/step/asset/update', 'ResilienceController@updateAssetStep');
        // Route::get('/step/{consultationStepId}/question/{consultationQuestionId}', 'QuestionController@getQuestion');
        // Route::get('access/user', 'ResilienceFrontController@loggedin')->middleware('auth:api');
        // Route::get('access/{user}', 'ResilienceFrontController@login');
        Route::post('question-mail', 'QuestionController@sendMail');
        Route::get('/reinvent/page', 'ReinventController@getReinventPage');
        Route::put('/reinvent/page/signin', 'ReinventController@updateSignIn');
        Route::get('/reinvent/page/signin', 'ReinventController@getSignInPage');
        Route::put('/reinvent/page/signup', 'ReinventController@updateSignUp');
        Route::get('/reinvent/page/signup', 'ReinventController@getSignUpPage');

        Route::put('/reinvent/page/forgot', 'ReinventController@updateForgotPage');
        Route::get('/reinvent/page/forgot', 'ReinventController@getForgotSetting');
        Route::put('/reinvent/page/verification', 'ReinventController@updateVerificationPage');
        Route::get('/reinvent/page/verification', 'ReinventController@getVerificationSetting');

        Route::get('sprint/{consultationSprintId}/question-step/pending', 'QuestionController@getPendingSteps');
        Route::delete('/reinvent/page/logo', 'ReinventController@removeReinventPageLogo');
        Route::put('/reinvent/page', 'ReinventController@update');
        Route::put('/reminder/page', 'ReminderController@update');
        Route::get('/reminder/page', 'ReminderController@index');
        Route::get('/{consultationId}/sidebar', 'ResilienceController@sidebar');
        Route::post('/sidebar/step', 'ResilienceController@sidebarStep');
        Route::delete('/step/{consultationStepId}', 'ResilienceController@deleteStep');
        Route::get('/by-workshop/{workshop}/{itemPerPage?}', 'ResilienceController@index');
        Route::post('/create', 'ResilienceController@create');
        Route::post('/{consultationId}/update', 'ResilienceController@update');
        Route::get('/{consultationId}', 'ResilienceController@show');
        Route::delete('/{consultationId}', 'ResilienceController@destroy');
        Route::post('{consultationSprintId}/step/welcome', 'ResilienceController@addWelcomeStep');
        Route::post('/step/{consultationStepId}/welcome/update', 'ResilienceController@updateWelcomeStep');
        Route::post('/step/{consultationStepId}/update', 'ResilienceController@updateStep');
        Route::post('{consultationSprintId}/step/add', 'ResilienceController@addMainStep');
        Route::post('{consultationSprintId}/step/thank-you', 'ResilienceController@addThankYouStep');
        Route::post('step/{consultationStepId}/thank-you/update', 'ResilienceController@updateThankYouStep');
        Route::get('/question/{consultationQuestionId}', 'QuestionController@index');
        Route::get('/question/types/all', 'QuestionController@getQuestionTypes');
        Route::post('/step/{consultationStepId}/question', 'QuestionController@store');
        Route::put('/question/{consultationQuestionId}', 'QuestionController@edit');
        Route::delete('/question/{consultationQuestionId}', 'QuestionController@destroy');
        Route::get('/asset/{consultationAssetId}', 'QuestionController@getAsset');
        Route::get('/step/{stepId}/answer/collect', 'ResilienceController@collect');
        Route::post('/sprints/{id}', 'SprintController@update');
        //sprint routes
        Route::get('/sprint-meeting-step/{sprintId}', 'SprintController@meetingStep');
        Route::get('/sprint-by-id/{sprintId}', 'SprintController@edit');
        Route::resource('sprints', 'SprintController');
        Route::get('/step/{stepId}/questions', 'ResilienceController@getStepById');
        // Route::post('video', 'ResilienceController@video');
        //get-meeting
        Route::get('get-meeting/{key}/{consultationId}/{sprintId}', 'ResilienceController@getWorkshopMeeting');
        Route::post('create-meeting-step', 'ResilienceController@addMeetingStep');
        Route::get('get-meeting-step/{consultationStepId}', 'ResilienceController@getMeetingStep');
        Route::delete('delete-step-meeting/{id}', 'ResilienceController@removeMeetingStep');
        Route::get('get-consultation/{key}/{workshopId}', 'ResilienceController@getWorkshopConsultation');
        Route::get('gen-reinvent-link/{consultationUuid}', 'ResilienceFrontController@genReinventLink');
        Route::post('/add-member', 'ResilienceFrontController@addMember');

        Route::get('get-private-file/{consultationStepId}', 'ResilienceController@getPrivateFile');
        Route::get('/auth-token', 'ResilienceController@getAuthToken');
        Route::post('/testMail', 'ResilienceController@testMail');
        Route::post('/answer-count', 'ResilienceFrontController@checkCount');
        //signUp Class Routes
        Route::get('class/get-classes/{type?}', 'SignUpClassController@index');
        Route::get('class/get-label/{class}', 'SignUpClassController@getLabelSetting');
        Route::get('class/get-signup-data/{class}', 'SignUpClassController@getClassSignUpData');
        Route::put('class/update-label/{class}', 'SignUpClassController@updateLabel');
        Route::resource('class', 'SignUpClassController');
        Route::get('/get-positions/{classUuid}', 'SignUpClassPositionController@index');
        Route::post('position-drag-order', 'SignUpClassPositionController@classPositionDragOrder');
        Route::resource('position', 'SignUpClassPositionController');
        Route::post('drag-order', 'ResilienceController@dragOrder');
    });
