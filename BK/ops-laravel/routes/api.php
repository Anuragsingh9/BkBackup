<?php
date_default_timezone_set('Europe/Paris');
    Route::options('/change-password', function () {
        return;
    });
    Route::group(['middleware' => ['cors'], 'prefix' => 'consultation'], function () {
        Route::options('/change-password', function () {
            return;
        });
        Route::post('/change-password', '\Modules\Resilience\Http\Controllers\ResilienceFrontController@changePassword');
    });
    
//use Illuminate\Http\Request;
//    Route::group(['middleware' => ['cors'/*, 'CheckValidRequest'*/,'auth:api'], 'prefix' => 'consultation/user'], function () {
//
//        Route::post('question/mail', '\Modules\Resilience\Http\Controllers\ResilienceFrontController@sendMail');
//        Route::get('/{consultationId}', '\Modules\Resilience\Http\Controllers\ResilienceFrontController@show')/*->middleware('auth:api')*/;
//        Route::get('/step/{stepId}/questions', '\Modules\Resilience\Http\Controllers\ResilienceFrontController@getQuestionById');
//        Route::post('/save-answer', '\Modules\Resilience\Http\Controllers\ResilienceFrontController@answer');
//        Route::post('/update-answer', '\Modules\Resilience\Http\Controllers\ResilienceFrontController@updateAnswer');
//        Route::get('sprint/{sprintId}/steps', '\Modules\Resilience\Http\Controllers\ResilienceFrontController@stepsBySprintId');
//    });
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

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */
Route::group(['middleware' => 'cors', 'prefix' => '', 'namespace' => 'Universal'], function () {
    Route::get('/get-custom-fillable/{keyword}/{type?}', 'ListImportController@getCustomFillable');
    Route::post('import-step-2', 'ListImportController@importStepTwo');
    Route::post('import-step-3', 'ListImportController@importStepThree');
    Route::resource('import-lists', 'ListImportController');
    Route::post('lists/person-list', 'ListsController@personList');
    Route::post('lists/addlist', 'ListsController@addList');
    Route::get('get-typo', 'ListsController@getTypology');
    Route::get('list-filter/{searchKeyword}','ListsController@searchList');
    Route::resource('lists', 'ListsController',['as'=>'a']);
});
    
Route::group(['middleware' => ['cors']], function () {
    Route::options('/get-searched-unions/{val}', function () {
        return;
    });
    Route::get('/get-searched-unions/{val}', 'UserController@getAutoUnions');
    Route::get('/get-swagger', function () {
        return public_path() . 'documentation_api_ops.json';
    });
    Route::options('/feature-mail', function () {
        return;
    });
    //module-api
    Route::get('get-module-list', 'SuperAdminModuleController@getModules');
    //feature mail route
    Route::post('/feature-mail', 'FeatureController@featureMails');
    
    //api for video Meeting
    Route::post('/fetch-meeting-dates', 'MeetingController@fetchMeetingDates');
    Route::post('/update-language-web', 'InitController@languageChange');
    Route::post('/create-project', 'InitController@createProject');
    Route::post('/create-milestone', 'InitController@createMilestone');
    Route::get('/fetch-milestone/{data}/{wid}', 'InitController@fetchMilestone');
    Route::get('/init-data', 'InitController@initData')->middleware('auth');
    Route::post('/pulse', 'ActionLogController@logging');
    Route::post('/check-login', 'InitController@checkLogin');
    Route::post('/forget-password-call', 'InitController@forgetPassword');
    Route::post('/reset-password', 'InitController@resetPassword');
    Route::post('/send-email', 'InitController@send_email');
    //Route::post('/check-otp', 'InitController@check_otp');
    Route::post('/user-update', 'InitController@UserUpdate');
    Route::post('/org-detail-save', 'InitController@orgSave');
    Route::post('/domain-save', 'InitController@domainSave');
    Route::get('/fetch-roles', 'InitController@getRoleList');
    //digital presense
    Route::get('/redirect-video-meeting/{id}', 'MeetingController@digitalMeetingPresense')->middleware('auth');
    Route::post('/check-otp', 'UserController@check_otp');
    Route::post('/reg-step', 'UserController@regStep');
    Route::post('/final-step', 'UserController@saveRegSteps');
    Route::post('/resend-email', 'UserController@resendEmail');
    Route::post('/signout', 'UserController@signOut');
    // Route::options('/skills/{skill}', function () {
    //     return;
    // });
    Route::options('/pulse', function () {
        return;
    });

    Route::options('/project-delete/{project}', function () {
        return;
    });

    Route::options('/skills/{skill}', function () {
        return;
    });
    Route::options('/user-skills/{skill}', function () {
        return;
    });
   
    Route::options('/get-user-by-id/{id}', function () {
        return;
    });
    Route::options('/get-user', function () {
        return;
    });
    Route::options('/get-user-roles', function () {
        return;
    });
    Route::options('/get-unions', function () {
        return;
    });
    Route::options('/get-society/{val}', function () {
        return;
    });
    Route::options('/get-user-by-id/{id}', function () {
        return;
    });
    Route::options('/edit-user', function () {
        return;
    });
    Route::options('/change-password', function () {
        return;
    });
    Route::options('/add-doc-type', function () {
        return;
    });
    Route::options('/edit-doc-type', function () {
        return;
    });
    Route::options('/get-doc-types', function () {
        return;
    });
    Route::options('/delete-doc-type/{id}', function () {
        return;
    });
    Route::options('/add-issuer', function () {
        return;
    });
    Route::options('/get-issuers', function () {
        return;
    });
    Route::options('/delete-issuers/{id}', function () {
        return;
    });

    Route::options('/add-group', function () {
        return;
    });
    Route::options('/get-group', function () {
        return;
    });
    Route::options('/delete-group/{id}', function () {
        return;
    });
    Route::options('/add-group',function(){return;});
    Route::options('/get-group',function(){return;});
    Route::options('/delete-group/{id}',function(){return;});
    Route::options('import-family',function(){return;});
    Route::options('import-industries',function(){return;});
    Route::options('import-user',function(){return;});
    Route::options('import-member',function(){return;});
    Route::options('import-union',function(){return;});
    Route::options('import-document',function(){return;});
    Route::options('import-workshop',function(){return;});
    Route::options('import-past-meeting',function(){return;});
    Route::options('import-project-task',function(){return;});
    Route::options('get-temp-user',function(){return;});
    Route::options('/add-workshop',function(){return;});
    Route::options('/update-workshop',function(){return;});
    Route::options('/get-workshops',function(){return;});
    Route::options('/get-workshop-by-id/{id}',function(){return;});
    Route::options('/get-president',function(){return;});
    Route::options('/get-non-workshop-users/{wid}',function(){return;});
    Route::options('/get-workshop-members/{wid}',function(){return;});
    Route::options('/update-member-status',function(){return;});
    Route::options('/delete-member',function(){return ;});
    Route::options('/fetch-filterd-workshop/{val}',function(){return ;});
    Route::options('/get-dashboard-workshop',function(){return;});
    Route::options('/user-workshops',function(){return;});
    Route::options('/delete-workshop/{id}',function(){return;});
    Route::options('/get-tags', function () {
        return;
    });
    Route::options('/add-workshop', function () {
        return;
    });
    Route::options('/update-workshop', function () {
        return;
    });
    Route::options('/get-workshops', function () {
        return;
    });
    Route::options('/get-workshop-by-id/{id}', function () {
        return;
    });
    Route::options('/get-president', function () {
        return;
    });
    Route::options('/get-non-workshop-users/{wid}', function () {
        return;
    });
    Route::options('/get-workshop-members/{wid}', function () {
        return;
    });
    Route::options('/update-member-status', function () {
        return;
    });
    Route::options('/delete-member', function () {
        return;
    });
    Route::options('/fetch-filterd-workshop/{val}', function () {
        return;
    });
    Route::options('/get-dashboard-workshop', function () {
        return;
    });
    Route::options('/user-workshops', function () {
        return;
    });
    Route::options('/delete-workshop/{id}', function () {
        return;
    });

    Route::options('/timeline/{wid}', function () {
        return;
    });

    Route::options('/add-member', function () {
        return;
    });
    Route::options('/delete-member', function () {
        return;
    });

    Route::options('/add-meeting', function () {
        return;
    });
    Route::options('/edit-meeting', function () {
        return;
    });
    Route::options('/get-future-meetings/{wid}', function () {
        return;
    });
    Route::options('/get-past-meetings/{wid}', function () {
        return;
    });
    Route::options('/get-presence/{wid}/{mid}', function () {
        return;
    });
    Route::options('/update-register', function () {
        return;
    });
    Route::options('/update-presence', function () {
        return;
    });
    Route::options('/view-meeting/{workshopid}/{meetingid}', function () {
        return;
    });
    Route::options('/update-meeting-status', function () {
        return;
    });
    Route::options('/delete-meeting/{id}', function () {
        return;
    });
    Route::options('/del-topic/{id}', function () {
        return;
    });
    Route::options('/reuse-topic/{id}', function () {
        return;
    });
    Route::options('/reuse-all', function () {
        return;
    });
    Route::options('/add-docs-topic', function () {
        return;
    });
    Route::options('/remove-docs-topic', function () {
        return;
    });
    Route::options('/save-topic-text', function () {
        return;
    });
    Route::options('/save-redacteur', function () {
        return;
    });
    Route::options('/save-topic-note', function () {
        return;
    });
    Route::options('/update-user-presence', function () {
        return;
    });
    Route::options('/get-user-presence', function () {
        return;
    });
    Route::options('/validate-prepd', function () {
        return;
    });
    Route::options('/validate-repd', function () {
        return;
    });
    Route::options('/add-inscription-user', function () {
        return;
    });
    Route::options('/repd-offline', function () {
        return;
    });
    Route::options('/get-redacteur/{wid}', function () {
        return;
    });
    Route::options('/add-message', function () {
        return;
    });
    Route::options('/get-message/{wid}', function () {
        return;
    });
    Route::options('/add-reply', function () {
        return;
    });
    Route::options('/delete-message/{id}', function () {
        return;
    });
    Route::options('/delete-message-reply/{id}', function () {
        return;
    });
    Route::options('/get-message-category/{wid}', function () {
        return;
    });
    Route::options('/add-message-category', function () {
        return;
    });
    Route::options('/update-message-category', function () {
        return;
    });
    Route::options('/delete-message-category/{id}', function () {
        return;
    });
    Route::options('/message-like-unlike', function () {
        return;
    });
    Route::options('/update-message', function () {
        return;
    });
    //Route::options('/get-message-by-category',function(){return ;});
    //Route::options('/get-like-unliked-message',function(){return ;});


    Route::options('/add-resources-category', function () {
        return;
    });
    Route::options('/get-resources-category/{id}', function () {
        return;
    });
    Route::options('/delete-resources/{id}', function () {
        return;
    });
    Route::options('/delete-resources-category/{id}', function () {
        return;
    });
    Route::options('/add-user-master', function () {
        return;
    });
    Route::options('/get-resources/{cat_id?}', function () {
        return;
    });
    Route::options('/add-resources', function () {
        return;
    });
    Route::options('/edit-resources', function () {
        return;
    });
    Route::options('/get-resources/{id}', function () {
        return;
    });


    Route::options('/add-wiki-category', function () {
        return;
    });
    Route::options('/get-wiki-category/{id}', function () {
        return;
    });
    Route::options('/wiki', function () {
        return;
    });
    Route::options('/wiki/{id}', function () {
        return;
    });
    Route::options('/get-wikis', function () {
        return;
    });
    Route::options('/get-wikis-list', function () {
        return;
    });
    Route::options('/get-wiki/{id}', function () {
        return;
    });
    Route::options('/update-status', function () {
        return;
    });
    Route::options('/wiki/{id}', function () {
        return;
    });
    Route::options('/invite-editor', function () {
        return;
    });
    Route::options('/delete-wiki-category/{id}', function () {
        return;
    });
    Route::options('/get-wiki-editor/{wiki_id}', function () {
        return;
    });
    Route::options('/delete-editor/{id}', function () {
        return;
    });
    Route::options('/get-wiki-admin', function () {
        return;
    });
    Route::options('/wiki-privilege/{wid}', function () {
        return;
    });

    Route::options('/add-industry', function () {
        return;
    });
    Route::options('/get-industry', function () {
        return;
    });
    Route::options('/get-family', function () {
        return;
    });
    Route::options('/add-family', function () {
        return;
    });
    Route::options('/delete-industry/{id}', function () {
        return;
    });
    Route::options('/fetch-industries', function () {
        return;
    });

    Route::options('/add-union', function () {
        return;
    });
    Route::options('/get-union', function () {
        return;
    });
    Route::options('/delete-union/{id}', function () {
        return;
    });
    Route::options('/get-union-by-id/{id}', function () {
        return;
    });

    Route::options('/add-document', function () {
        return;
    });
    Route::options('/del-document', function () {
        return;
    });
    Route::options('/search-document', function () {
        return;
    });
    Route::options('/update-doc-title', function () {
        return;
    });
    Route::options('/cote-document', function () {
        return;
    });
    Route::options('/download-document-count', function () {
        return;
    });
    Route::options('/add-files', function () {
        return;
    });
    Route::post('/add-topic-files', function () {
        return;
    });
    Route::options('/get-setting', function () {
        return;
    });
    Route::options('/update-setting', function () {
        return;
    });
    Route::options('/update-graphic-setting', function () {
        return;
    });
    Route::options('/update-email-graphic', function () {
        return;
    });
    Route::options('/remove-email-graphic', function () {
        return;
    });
    Route::options('/update-pdf-graphic', function () {
        return;
    });
    Route::options('/update-password-graphic', function () {
        return;
    });
    Route::options('/get-resources-by-id/{id}', function () {
        return;
    });
    Route::get('/get-doodle-dates/{id}', function () {
        return;
    });
    Route::post('/update-meeting-finaldate', function () {
        return;
    });
    Route::post('/send-meeting-invitation', function () {
        return;
    });

    Route::options('/get-superadmin-setting', function () {
        return;
    });
    Route::options('/update-superadmin-graphic', function () {
        return;
    });

    Route::options('/update-org-setting', function () {
        return;
    });
    Route::options('/get-orgsetting', function () {
        return;
    });

    Route::options('/get-all-task/', function () {
        return;
    });
    Route::options('/delete-task/{task}', function () {
        return;
    });
    Route::options('/update-task-status', function () {
        return;
    });
    Route::options('/get-meeting-by-id', function () {
        return;
    });
    Route::options('/get-meeting-by-date', function () {
        return;
    });
    Route::options('/add-topic', function () {
        return;
    });
    Route::options('/fetch-filterd-users/{val}', function () {
        return;
    });
    Route::options('/fetch-filterd-validator', function () {
        return;
    });
    Route::options('/fetch-filterd-president', function () {
        return;
    });
    Route::options('/invite-meeting', function () {
        return;
    });
    Route::options('/download-document-count', function () {
        return;
    });
    Route::options('/search-action-logs', function () {
        return;
    });
    Route::options('/search-user', function () {
        return;
    });
    Route::options('/delete-user/{id}', function () {
        return;
    });
    Route::options('/get-all-group/{id}', function () {
        return;
    });

    Route::options('/get-start-list', function () {
        return;
    });
    Route::options('/get-page-id', function () {
        return;
    });
    Route::options('/get-workshop-user-role/{wid}', function () {
        return;
    });
    Route::options('/update-topic-order', function () {
        return;
    });
    Route::options('/change-language', function () {
        return;
    });
    Route::options('/delete-file', function () {
        return;
    });
    Route::options('user-task-permission', function () {
        return;
    });
    /* added by vijay for current date*/
    Route::options('/get-current-date', function () {
        return;
    });
    Route::options('/delete-dependancy', function () {
        return;
    });
    Route::options('/check-signature-pdf', function () {
        return;
    });
    Route::options('/check-workshop-admin', function () {
        return;
    });
    Route::options('/get-companies', function () {
        return;
    });
    Route::options('/create-entity', function () {
        return;
    });
    Route::options('/update-admin-status', function () {
        return;
    });
    
    Route::group(['middleware' => ['auth', 'action_log', 'timeline_log','cors']], function () {
        Route::options('/update-project-milestone/{id}', function () {
            return;
        });
        Route::put('/update-project-milestone/{id}', 'Project\ProjectController@update');
        Route::post('update-labels', 'ImproveMentFour\LabelCustomizationController@updateLabel');
//workshopTopics
        Route::get('/workshop-topics', 'WorkshopController@getWorkshopTopics')/*->middleware('eventApiKey')*/;

        Route::post('update-admin-status', 'SuperAdminController@updateAdminStatus');
        Route::get('/get-companies/{val}', 'UserController@getCompanies');
        Route::post('/create-entity', 'UserController@createCompanies');
       
        Route::get('/get-user', 'UserController@getUser');
        Route::get('/get-user-by-id/{id}', 'UserController@getUserById');
        Route::post('/edit-user', 'UserController@editUser');
        Route::get('/get-user-roles', 'UserController@getUserRoles');
        Route::get('/get-unions', 'UserController@getUnions');
        Route::get('/get-society/{val}', 'UserController@getSociety');
        Route::post('/change-password', 'UserController@changePassword');
        Route::get('/fetch-filterd-users/{val}', 'UserController@getFilterdUser');
        Route::get('/search-user', 'UserController@searchUser');
        Route::post('/search-user', 'UserController@searchUser');
        Route::get('/delete-user/{id}', 'UserController@delUser');
        Route::get('/fetch-filterd-workshop/{val}', 'WorkshopController@getFilterdWorkshop');
        Route::post('/fetch-filterd-task', 'TaskController@getFilterdTask');
        Route::post('/fetch-filterd-validator', 'UserController@getFilterdValidator');
        Route::post('/fetch-filterd-president', 'UserController@getFilterdPresident');
        Route::post('/get-workshop-users', 'UserController@getWorkshopUsers');
        Route::post('/download-document-count', 'DocumentController@downloadDocument');
        Route::post('/update-skill-option-drag', 'ImproveMentFour\SkillController@skillOptionDrag');


        Route::post('/add-doc-type', 'DocumentTypeController@addDocType');
        Route::post('/edit-doc-type', 'DocumentTypeController@editDocType');
        Route::get('/get-doc-types', 'DocumentTypeController@getDocTypes');
        Route::get('/delete-doc-type/{id}', 'DocumentTypeController@DeleteDocTypes');
        Route::post('/add-issuer', 'IssuerController@addIssuer');
        Route::get('/get-issuers', 'IssuerController@getIssuers');
        Route::get('/delete-issuers/{id}', 'IssuerController@DeleteIssuers');
        Route::get('get-workshop-by-user/{id}', 'WorkshopController@getWorkshopByUser');
        
        Route::post('/add-group', 'GroupController@addGroup');
        Route::get('/get-group', 'GroupController@getGroup');
        Route::get('/delete-group/{id}', 'GroupController@DeleteGroup');

        Route::post('/add-workshop', 'WorkshopController@addWorkshop');
        Route::post('/update-workshop', 'WorkshopController@updateWorkshop');
        Route::get('/get-workshops', 'WorkshopController@getWorkshops');
        Route::get('/get-workshops-for-docs', 'WorkshopController@getWorkshopsForDocs');

        Route::get('/get-workshops-roles/{id}', 'CoreController@getRolesForWorkshop');
        Route::get('/get-workshop-user-role/{wid}', 'CoreController@getWorkshopUserRole');
    Route::get('/get-workshops-list', 'WorkshopController@getWorkshopList');
        Route::get('/get-workshop-by-id/{id}', 'WorkshopController@getWorkshopById');
        Route::get('/get-president', 'WorkshopController@getPresident');
        Route::get('/get-non-workshop-users/{wid}', 'WorkshopController@getNonWorkshopUsers');
        Route::get('/get-workshop-members/{wid}', 'WorkshopController@getWorkshopMembers');
        Route::post('/update-member-status', 'WorkshopController@updateMemberStatus');
        Route::post('/delete-member', 'WorkshopController@deleteMember');
        Route::get('/fetch-filterd-workshop/{val}', 'WorkshopController@getFilterdWorkshop');
        Route::get('/get-workshop-data', 'WorkshopController@getWorkshopData');
        Route::get('/get-dashboard-workshop', 'DashboardController@getDashboardWorkshop');
        Route::get('/user-workshops', 'WorkshopController@getUserWorkshops');
        Route::get('/user-profile-workshops', 'WorkshopController@getUserProfileWorkshops');
        Route::get('/delete-workshop/{id}', 'WorkshopController@deleteWorkshop');
        Route::get('/timeline/{wid}', 'WorkshopController@getTimeline');
        Route::post('/add-member', 'WorkshopController@addMember');
        Route::post('/delete-member', 'WorkshopController@deleteMember');
        Route::post('/add-meeting', 'MeetingController@addMeeting');
        Route::post('/edit-meeting', 'MeetingController@editMeeting');
        Route::get('/get-future-meetings/{wid}', 'MeetingController@getFutureMeetings');
        Route::get('/get-past-meetings/{wid}', 'MeetingController@getPastMeetings');
        Route::get('/get-presence/{wid}/{mid}', 'MeetingController@getPresence');
        Route::post('/update-register', 'MeetingController@updateRegister');
        Route::post('/update-presence', 'MeetingController@updatePresence');
        Route::post('/update-meal-presence', 'ImprovementMeetingController@updateMealPresence');
        Route::get('/view-meeting/{workshopid}/{meetingid}', 'MeetingController@viewMeeting');
        Route::post('/update-meeting-status', 'MeetingController@updateStatus');
        Route::post('/add-topic', 'MeetingController@addTopic');
        Route::get('/delete-meeting/{id}', 'MeetingController@deleteMeeting');
        Route::get('/get-topics/{mid}/{type}', 'MeetingController@getTopics')->middleware(['validMember','validated_R_Prepd']);
        ;
        Route::get('/del-topic/{id}', 'MeetingController@delTopics');
        Route::get('/reuse-topic/{id}', 'MeetingController@reuseTopics');
        Route::post('/reuse-all', 'MeetingController@reuseAll');
        Route::post('/add-docs-topic', 'MeetingController@addDocsToTopic');
        Route::post('/remove-docs-topic', 'MeetingController@removeTopicDocs');
        Route::post('/save-topic-text', 'MeetingController@saveTopicText');
        Route::post('/save-redacteur', 'MeetingController@saveRedacteur');
        Route::post('/save-topic-note', 'MeetingController@saveTopicNote');
        Route::post('/save-topic-discussion', 'MeetingController@saveTopicDiscussion');
        Route::post('/save-task', 'MeetingController@saveTask');
        Route::post('/update-user-presence', 'MeetingController@updateUserPresence');
      
        Route::post('/update-video-status', 'MeetingController@updateVideoPresence');
        Route::post('/validate-prepd', 'MeetingController@validatePREPD');
        Route::post('/re-validate-prepd', 'ImprovementMeetingController@reValidatePREPD');
        Route::post('/validate-repd', 'MeetingController@validateREPD');
        Route::post('/re-validate-repd', 'ImprovementMeetingController@reValidateREPD');
        Route::post('/re-validate-repd', 'ImprovementMeetingController@reValidateREPD');
        Route::post('/add-inscription-user', 'MeetingController@addInscriptionUser');
        Route::post('/get-user-presence', 'MeetingController@getUserPresence');
        Route::post('/repd-offline', 'MeetingController@repdOffline');
        Route::post('/update-topic-order', 'MeetingController@updateTopicOrder');
        Route::get('/get-redacteur/{wid}', 'MeetingController@redacteur');


        Route::post('/add-message', 'MessageController@addMessage');
        Route::post('/get-message', 'MessageController@getMessage');
        Route::post('/add-reply', 'MessageController@addReply');
        Route::get('/delete-message/{id}', 'MessageController@deleteMessage');
        Route::get('/delete-message-reply/{id}', 'MessageController@deleteMessageReply');
        Route::get('/get-message-category/{wid}', 'MessageController@getMessageCategory');
        Route::post('/add-message-category', 'MessageController@addMessageCategory');
        Route::post('/update-message-category', 'MessageController@updateMessageCategory');
        Route::get('/delete-message-category/{id}', 'MessageController@deleteMessageCategory');
        Route::post('/message-like-unlike', 'MessageController@messageLikeUnlike');
        Route::post('/update-message', 'MessageController@updateMessage');
        //Route::post('/get-message-by-category','MessageController@getMessageByCategory');
        //Route::post('/get-like-unliked-message','MessageController@getLikeUnlikedMessage');

        Route::get('/get-start-list', 'StartController@GetStartList');
        Route::post('/get-page-id', 'StartController@GetPageId');

        Route::post('/add-resources-category', 'ResourcesController@addResourcesCategory');
        Route::get('/get-resources-category/{id}', 'ResourcesController@getResourcesCategory');
        Route::get('/delete-resources/{id}', 'ResourcesController@DeleteResources');
        Route::get('/delete-resources-category/{id}', 'ResourcesController@DeleteResourcesCategory');
        Route::post('/add-user-master', 'UserController@addUser');
        Route::get('/get-resources/{cat_id?}', 'ResourcesController@getResources');
        Route::post('/add-resources', 'ResourcesController@addResources');
        Route::post('/edit-resources', 'ResourcesController@editResources');
        Route::get('/get-resources-by-id/{id}', 'ResourcesController@getResourcesById');
        Route::get('/get-all-group/{id}', 'ResourcesController@getAllGroup');


        Route::post('/update-org-setting', 'OrganisationController@updateOrgSetting');
        Route::get('/get-orgsetting', 'OrganisationController@getOrgSetting');
        Route::post('/get-dashboardSetting', 'SettingController@getDashboardSetting');
        Route::post('/update-dashboardSetting', 'SettingController@dashboardSetting');
        Route::post('/update-dashboardCheckedSetting', 'SettingController@updateDashboardCheckedSetting');

        Route::post('/add-wiki-category', 'WikiController@addWikiCategory');
        Route::get('/get-wiki-category/{id}', 'WikiController@getWikiCategory');
        Route::post('/wiki', 'WikiController@addWiki');
        Route::put('/wiki/{id}', 'WikiController@editWiki');
        Route::get('/get-wikis', 'WikiController@getWiki');
        Route::get('/get-wikis-list', 'WikiController@getWikiList');
        Route::get('/get-wiki/{id}', 'WikiController@getWikiById');
        Route::post('/update-status', 'WikiController@updateStatus');
        Route::get('/delete-wiki/{id}', 'WikiController@DeleteWiki');
        Route::post('/invite-editor', 'WikiController@inviteEditor');
        Route::get('/delete-wiki-category/{id}', 'WikiController@DeleteWikiCategory');
        Route::get('/get-wiki-editor/{wiki_id}', 'WikiController@getWikiEditor');
        Route::get('/delete-editor/{id}', 'WikiController@DeleteEditor');
        Route::get('/get-wiki-admin', 'WikiController@getWikiAdmin');
        Route::get('/wiki-privilege/{wid}', 'WikiController@getWikiPrivilege');


        Route::post('/add-union', 'UnionController@addUnion');
        Route::get('/get-union', 'UnionController@getUnion');
        Route::get('/delete-union/{id}', 'UnionController@deleteUnion');
        Route::get('/get-union-by-id/{id}', 'UnionController@getUnionById');


        Route::post('/add-document', 'DocumentController@addDocument');
        Route::post('/del-document', 'DocumentController@deleteDocument');
        Route::post('/search-document', 'DocumentController@searchDocument');
        Route::post('/update-doc-title', 'DocumentController@updateDocTitle');
        Route::post('/cote-document', 'DocumentController@coteDocument');
        Route::post('/download-document-count', 'DocumentController@downloadDocument');
        Route::post('/check-document-exits', 'DocumentController@checkDocumentExits');
        Route::post('/add-files', 'DocumentController@addFiles');
        Route::post('/add-topic-files', 'DocumentController@addTopicFiles');

        Route::post('/search-action-logs', 'ActionLogController@searchActionLog');

        Route::post('/get-setting', 'SettingController@getSetting');
        Route::post('/update-setting', 'SettingController@updateSetting');
        Route::post('/update-graphic-setting', 'SettingController@updateGraphicSetting');
        Route::post('/update-email-graphic', 'SettingController@updateEmailGraphic');
        Route::post('/remove-email-graphic', 'SettingController@removeEmailGraphic');
        Route::post('/update-pdf-graphic', 'SettingController@updatePdfGraphic');
        Route::post('/update-password-graphic', 'SettingController@updatePasswordGraphicSetting');

        Route::post('/get-superadmin-setting', 'SuperadminSettingController@getSetting');
        Route::post('/update-superadmin-graphic', 'SuperadminSettingController@updateGraphicSetting');

        //qualification module working
        Route::post('/update-workshop-graphic', 'WorkshopController@updateWorkshopGraphic');
        Route::post('/update-workshop-pdf-graphic', 'WorkshopController@updateWorkshopPdfGraphic');
        Route::post('/update-workshop-email-graphic', 'WorkshopController@updateWorkshopEmailGraphic');
        Route::post('/remove-workshop-email-graphic', 'WorkshopController@removeWorkshopEmailGraphic');
        Route::post('/update-workshop-customized-fields', 'WorkshopController@updateWorkshopCustomizedFields');
        Route::get('/get-qualification-workshops', 'WorkshopController@getQualificationWorkshops');
        Route::get('get-conditional', 'ImproveMentFour\SkillController@getConditionalCheckBox');

        Route::post('/add-industry', 'IndustryController@addIndustry');
        Route::post('/add-family', 'IndustryController@addFamily');
        Route::get('/get-industry', 'IndustryController@getIndustry');
        Route::get('/get-family', 'IndustryController@getFamily');
        Route::get('/delete-industry/{id}', 'IndustryController@DeleteIndustry');
        Route::get('/fetch-industries', 'CoreController@getIndustries');

        Route::post('/update-meeting-finaldate', 'MeetingController@updateMeetingFinalDate');
        Route::post('/send-meeting-invitation', 'MeetingController@sendMeetingInvitation');
        Route::post('/invite-member-final-date', 'MeetingController@inviteMemberFinalMeeting');
        Route::post('/invite-member-presence-list', 'MeetingController@inviteMemberPresence');
        //Route::get('/get-doodle-dates/{id}','MeetingController@getDoodleDates');
        Route::post('/user-response-save', 'MeetingController@saveUserResponse');
        Route::post('/doodle-response-list/{id}/{wid}', 'MeetingController@doodleResponseList');

        Route::post('/get-all-task', 'TaskController@getAllTask');
        Route::post('/get-milestone-task', 'TaskController@getTaskByMilestone');
        Route::get('/delete-task/{task}', 'TaskController@deleteTask');
        Route::post('/update-task-status', 'TaskController@updateTaskStatus');
        Route::post('/get-meeting-by-id', 'MeetingController@getMeetingById');
        Route::post('/get-meeting-by-date', 'DashboardController@getMeetingByDate');
        Route::post('/invite-meeting', 'MeetingController@inviteMeeting');
        /*----language route----*/
        Route::post('/change-language', 'InitController@languageChange');
        Route::get('/delete-file/{id}', 'UserController@fileDelete');
        /*-------end- language route---*/
       
        /*-----pdf upload inscription page ---------*/
        Route::post('/upload-inscription-pdf', 'PdfController@uploadInscriptionPdf');
        Route::get('/config-logo-delete', 'SuperadminSettingController@logoDelete');
        /*-----end pdf upload inscription page ---------*/
        /*-----get meeting document ---------*/
        Route::post('/get-meeting-inscription-doc', 'MeetingController@getMeetingDocument');
        Route::post('/check-signature-doc', 'DocumentController@checkSignaturePdf');

        Route::post('/get-meeting-inscription-doc', 'MeetingController@getMeetingDocument');
        /*-----end get meeting document ---------*/
        /*repd Controller*/

        //Route::post('/save-task','RepdOfflineController@saveTask');
        //get dashboard task
        Route::post('/get-dashboard-task', 'DashboardController@getDashboardTask');
        Route::get('/get-dashboard-doc', 'DashboardController@getDashboardDoc');
        //end get dashboard task
        //Guid pdf
        Route::post('/get-guides', 'SuperAdminController@getGuide');
        Route::get('/get-reminder-status', 'SuperAdminController@getReminderStatus');
        Route::post('/update-reminder-status', 'SuperAdminController@updateReminderStatus');

        /* added by vijay for current date*/
        Route::post('/get-current-date', 'InitController@getCurrentDate');
        //end guide pdf
        //mobile api
        /*personal message*/
        Route::post('/check-workshop-admin', 'WorkshopController@checkWorkShopAdmin');
        Route::post('/get-admin-workshop', 'WorkshopController@getAdminWorkshop');
        Route::post('/add-personal-message', 'MessageController@personalMessageAdd');
        Route::post('/get-unread-msg-count', 'MessageController@getUnreadMessageCount');
        Route::get('/get-unread-msg', 'MessageController@getUnreadMessage');
        Route::get('/get-sent-personal-msg', 'MessageController@getSentPersonalMessage');
        Route::post('/delete-personal-msg', 'MessageController@deletePersonalMessage');
		// added by Ravindra
        // for search workshop for list
        Route::get('/search-workshops/{searchKeyword}', 'WorkshopController@searchWorkshop');

        //project resources
        Route::resource('activity-type', 'Project\ActivityTypeController');
        Route::get('/get-projects/{id}', 'Project\ProjectController@index');
        Route::get('/get-projects-overview/{id?}', 'Project\ProjectController@getWorkshopProjectOverview');

        Route::post('update-activity-status-order', 'Project\ActivityStatusController@updateActivtyStatusOrder');
        Route::post('delete-activity-status', 'Project\ActivityStatusController@destroy');
        Route::resource('activity-status', 'Project\ActivityStatusController');
        Route::post('/get-project-by-id', 'Project\ProjectController@show');
        Route::get('/get-projects-by-wid/{wid}', 'Project\ProjectController@projectByWid');
        Route::get('/get-color', 'Project\ProjectController@getColor');
        Route::get('/get-milestone-date/{milestone_id}', 'Project\ProjectController@getMileStoneDate');
        Route::post('update-milestone-date', 'Project\ProjectController@updateMilestoneDate');
        Route::post('delete-milestone', 'Project\ProjectController@deleteMilestone');
        Route::post('update-task-activity', 'TaskController@updateTaskActiveType');
        Route::post('update-task-date', 'TaskController@updateTaskDate');
        Route::post('add-project-task', 'TaskController@saveTask');
        //set project email-task
        Route::post('project-email-update', 'Project\ProjectController@projectMailUpdate');
        Route::post('update-milestone-date', 'Project\ProjectController@updateMilestoneDate');
        Route::post('delete-milestone', 'Project\ProjectController@deleteMilestone');
        Route::post('update-task-milestone', 'Project\ProjectController@updateTaskMilestone');

        Route::post('update-task-activity', 'TaskController@updateTaskActiveType');

        //Task Dependency
        Route::post('add-dependent', 'Project\TaskDependencyController@addDependent');
        Route::get('get-dependent/{task}', 'Project\TaskDependencyController@getChildTask');
        Route::post('add-dependency', 'Project\TaskDependencyController@addDependency');
        Route::get('get-dependency/{task}', 'Project\TaskDependencyController@getParentTask');
        Route::post('delete-dependency', 'Project\TaskDependencyController@deleteDependancy');


        //userTaskPermissions
        Route::get('user-task-permission/{id}/{pid}', 'Project\TaskPermissionController@index');
        Route::post('user-task-permission/{id}', 'Project\TaskPermissionController@update');

        Route::resource('user-task-permission', 'Project\TaskPermissionController');

        // Task comment in task
        Route::post('add-comment', 'Project\CommentController@create');
        Route::get('get-comment/{task_id}', 'Project\CommentController@index');
        Route::post('add-comment-file', 'Project\CommentController@addCommentFile');

        //Task document
        Route::post('add-task-doc', 'Project\TaskDocumentController@store');
        Route::get('get-task-doc/{task_id}', 'Project\TaskDocumentController@index');
        Route::post('delete-comment', 'Project\CommentController@destroy');
        //task Description
        Route::post('update-description', 'Project\CommentController@UpdateDescription');
        //Task Link
        Route::post('add-task-link', 'Project\CommentController@addTaskLink');
        // activity Type
        Route::post('add-activity-type', 'Project\ActivityTypeController@create');
        Route::get('get-activity-type', 'Project\ActivityTypeController@index');
        Route::post('update-activity-type', 'Project\ActivityTypeController@update');
        //Project Task Status
        Route::post('/update-project-task-status', 'TaskController@updateProjectTaskStatus');
        Route::post('/update-task-color', 'TaskController@updateTaskColor');
        //Add Task User
        Route::post('/add-task-user', 'TaskController@addTaskUser');
        //remove Task User
        Route::post('/remove-task-user', 'TaskController@removeTaskUser');
        // Project timeline
        Route::post('/create-project-milestone', 'Project\ProjectController@store');
       
        //Project Task tranformation
        Route::post('/task-transformation', 'Project\TaskTransformationController@projectTaskTransformation');
        Route::get('get-project-timeline/{workshop_id}', 'Project\ProjectTimelineController@index');
        Route::post('update-project-timeline-sort', 'Project\ProjectTimelineController@update');
        Route::post('update-project-name', 'Project\ProjectTimelineController@updateProjectLabel');
        //Updata Task title
        Route::post('update-task-title', 'TaskController@updateTaskTitle');
        //Task tags
        Route::post('/get-filter-tags', 'Project\TaskTagController@getTagsByFilter');
        Route::post('/add-tags', 'Project\TaskTagController@addTags');
        Route::post('/add-task-tags', 'Project\TaskTagController@addTaskTags');
        Route::get('/delete-tags/{id}', 'Project\TaskTagController@deleteTag');
        Route::post('/delete-task-tags', 'Project\TaskTagController@taskTagDelete');
        Route::post('/get-tags-data', 'Project\TaskTagController@getTags');
        //improvements 3
        //improvements 3
        Route::get('/get-agenda-doc/{mid}', 'ImprovementMeetingController@getTopicsDocument');
        Route::post('get-zip', 'ImprovementMeetingController@zipTopicsDocument');

        //Improvement4 API
        Route::get('get-labels', 'ImproveMentFour\LabelCustomizationController@index');
        Route::post('update-labels', 'ImproveMentFour\LabelCustomizationController@updateLabel');
        Route::post('update-admin-status', 'SuperAdminController@updateAdminStatus');
        Route::get('get-skill-format', 'ImproveMentFour\SkillTabsController@getSkillTypes');
        //Get skill tabs
         Route::get('/get-skill-tab/{tab_type}/{wid?}', 'ImproveMentFour\SkillTabsController@index');

        Route::get('/get-skills/{id}/{userId?}/{type?}', 'ImproveMentFour\SkillTabsController@getSkills');
        Route::get('/get-mandatory/{userId?}', 'ImproveMentFour\SkillTabsController@getAllMandatory');
        Route::post('/add-skill-tab', 'ImproveMentFour\SkillTabsController@add');
        Route::post('/delete-skill-tab', 'ImproveMentFour\SkillTabsController@deleteTab');
        Route::post('/update-skill-tab-visibilty', 'ImproveMentFour\SkillTabsController@updateVisibilty');
        Route::post('/update-skill-tab-lock', 'ImproveMentFour\SkillTabsController@updateLock');
        //api/skills/create
        Route::post('/delete-skill-select-option', 'ImproveMentFour\SkillController@deleteSkillSelectOption');
        Route::post('/update-skill-drag', 'ImproveMentFour\SkillController@skillDrag');
        Route::post('/update-skilltab-drag', 'ImproveMentFour\SkillTabsController@skillDrag');
        //api for deligence
        Route::get('/get-diligence/{id}', 'WorkshopController@getDiligence');
        Route::get('/get-diligence-workshop/{userId}', 'WorkshopController@getDiligenceByWorkshops');
        //api for default project
        Route::get('/get-default-project/{id}', 'Project\ProjectController@getDefaultProject');
        //api for userSkills
        Route::resource('user-skills', 'ImproveMentFour\UserSkillController');
        //admin notes
        Route::post('get-admin-notes', 'ImproveMentFour\AdminNotesController@index');
        Route::resource('admin-notes', 'ImproveMentFour\AdminNotesController');
        Route::get('user-file-del/{id}', 'ImproveMentFour\UserSkillController@destroyFle');
        Route::post('/get-users-skills', 'ImproveMentFour\UserSkillController@getUserSkill');
        Route::post('update-presence-visible-tab', 'ImproveMentFour\SkillTabsController@updateVisibleToPresence');
        Route::post('/get-presence-users-skills', 'ImproveMentFour\UserSkillController@getPresenceUserSkill');
        //Admin notes Save

        Route::delete('/project-delete/{project}', 'Project\ProjectController@destroy');
        
        
        Route::post('/need-help', 'StyleController@needHelp');
        Route::post('/send-code', 'StyleController@sendCode');
        Route::post('/feature-help', 'StyleController@newFeature');
        Route::post('/get-tags', 'Project\TaskTagController@getTags');
        //Route::post('/get-filter-tags', 'Project\TaskTagController@getTagsByFilter');
        Route::post('/add-tags', 'Project\TaskTagController@addTags');
        Route::post('/add-task-tags', 'Project\TaskTagController@addTaskTags');
        Route::get('/delete-tags/{id}', 'Project\TaskTagController@deleteTag');
        Route::post('/delete-task-tags', 'Project\TaskTagController@taskTagDelete');

        //Entity Routes
        Route::get('crm/search-entity/{val}/{type}/{belongId?}', 'EntityController@getEntity');
        Route::post('crm/person-belongs', 'EntityController@addPersonBelongsTo');
        Route::get('crm/update-person-belongs/{id}/{entityId}', 'EntityController@updatePersonEntity');
        Route::get('crm/remove-person-belongs/{id}', 'EntityController@removePersonEntity');
        Route::post('crm/company-dependency', 'EntityController@addDependency');
        Route::get('crm/company-dependency/{id}', 'EntityController@removeDependency');
        Route::get('crm/company-persons/{id}', 'EntityController@getPersonsOfCompany');
        Route::get('crm/search-persons/{val}/{belongId}', 'EntityController@getPersons');
        Route::post('crm/user-permission-update', 'UserController@updatePermission');
//                Route::post('crm/create-entity-new', 'UserController@createCompaniesNew');
        Route::post('crm/create-entity-new', 'EntityController@createEntity');
        Route::post('crm/add-user-master', 'UserController@addUser');

       
        Route::get('/crm/fetch-list', 'Universal\ListsController@crmFetchList');
                Route::post('/crm/add-existing-list', 'Universal\ListsController@crmAddResultExistingList');

        Route::post('/crm/add-new-list', 'Universal\ListsController@crmAddResultNewList');


    });
    //Get Admin user
    Route::get('get-admin-user/{val}', 'ImproveMentFour\NewMemberAlert@getOrgAdminUser');
    Route::get('get-alerted-admin-user', 'ImproveMentFour\NewMemberAlert@getOrgAdminAlertUser');
    Route::post('update-alerted-admin-user', 'ImproveMentFour\NewMemberAlert@updateAdminAlert');
    //end Admin
    Route::get('skills?id={id}', 'ImproveMentFour\SkillController@index');
    Route::resource('skills', 'ImproveMentFour\SkillController');
    Route::resource('admin-notes', 'ImproveMentFour\AdminNotesController');
        //import routes
        Route::post('import-industries','Import\ImportController@importIndustries');
        Route::post('import-union','Import\ImportController@importUnion');
        Route::post('import-user','Import\ImportController@importUsers');
        Route::get('get-temp-user','Import\ImportController@getTempUser');
        Route::get('get-user-email-import','Import\ImportController@getImportEmailAlertUser');
         Route::get('get-workshop-temp-user/{id?}','Import\WorkshopTempImport@index');
        // Route::get('sent-workshop-temp-mail','Import\WorkshopTempImport@index');
        Route::post('import-family','Import\ImportController@importIndustriesFamily');
        Route::post('import-temp-user','Import\ImportController@importTempUser');
        Route::post('delete-email-user','Import\ImportController@delImportUser');
        Route::post('delete-email-user','Import\WorkshopTempImport@delImportUser');
        Route::post('send-temp-user-email','Import\ImportController@sendEmailToTempUser');
        Route::post('send-wuser-temp-mail','Import\WorkshopTempImport@sendEmailToTempUser');
        Route::post('delete-temp-user','Import\ImportController@deleteTempUsers');
        Route::post('import-workshop','Import\ImportController@ImportWorkshop');
        Route::post('import-member','Import\ImportController@importMember');
        Route::post('import-past-meeting','Import\ImportController@importPastMeeting');
        Route::post('import-project-task','Import\ImportController@importProjectTask');
        Route::post('import-document','Import\ImportController@importDocument');
        Route::post('get-imported-meetings','Import\ImportController@getImportMeetings');
        Route::post('import-external-doc','Import\ImportController@uploadExternalDoc');
        Route::post('import-internal-doc','Import\ImportController@uploadInternalDoc');

     });


    
    /*repd Offline Controller*/
    Route::options('/get-data', function () {
        return;
    });
    Route::options('/prepd-online', function () {
        return;
    });
    Route::group(['middleware' => ['cors']], function () {
        Route::post('/get-repd-data', 'RepdOfflineController@getrepdData');
        Route::post('/save-task-offline', 'RepdOfflineController@saveTask');
        Route::post('/chrome-app-login', 'RepdOfflineController@login');
    });

    Route::group(['middleware' => ['chrome_app_token']], function () {
        Route::post('/get-data', 'RepdOfflineController@getOfflineMeetings');
        Route::post('/update-meeting', 'RepdOfflineController@updateMeetingData');
        Route::post('/prepd-online', 'RepdOfflineController@uploadPrepdData');
    });


    Route::post('/get-repd-data', 'RepdOfflineController@getrepdData');
    Route::post('/save-task-offline', 'RepdOfflineController@saveTask');
    Route::post('/chrome-app-login', 'RepdOfflineController@login');
    Route::group(['middleware' => ['chrome_app_token']], function () {
        Route::post('/get-data', 'RepdOfflineController@getOfflineMeetings');
        Route::post('/update-meeting', 'RepdOfflineController@updateMeetingData');



});


// mobile Api
Route::group(['middleware' => 'auth:api'], function () {

    Route::get('/logout', 'Api\ApiController@logout');
    Route::post('/send-doodle-reminder', 'Api\ApiController@sendDoodleReminder');
    Route::post('/update-language', 'Api\ApiController@languageChange');
    Route::post('/get-future-metting-list', 'Api\ApiController@getFutureMettingList');
    Route::get('/get-workshop-list', 'Api\ApiController@getCommissionList');
    Route::post('/get-past-metting-list', 'Api\ApiController@getPastMettingList');
    //message tab detail
    Route::post('/get-message-tab', 'Api\ApiController@getMessageTab');
    Route::post('/add-message-tab', 'Api\ApiController@addMessageTab');
    Route::post('/message-delete', 'Api\ApiController@deleteMessage');
    Route::post('/delete-reply-message', 'Api\ApiController@deleteMessageReply');
    Route::post('/edit-message', 'Api\ApiController@updateMessage');
    Route::post('/delete-message-tab', 'Api\ApiController@deleteMessageTab');
    Route::post('/update-message-tab', 'Api\ApiController@updateMessageTab');
    Route::post('/add-message-text', 'Api\ApiController@addMessage');
    Route::post('/get-message-list', 'Api\ApiController@getMessagelist');
    Route::post('/add-exteral-docs', 'Api\ApiController@addDocs');
    Route::post('/add-reply-message', 'Api\ApiController@addReplyMesssage');
    Route::post('/member-list', 'Api\ApiController@memberList');
    Route::post('/save-vote', 'Api\ApiController@saveVote');
    Route::post('/get-metting-detail', 'Api\ApiController@getMettingDetail');
    Route::post('/metting-date-confirm', 'Api\ApiController@dateChoiceDoodle');
    Route::post('/like-unlike', 'Api\ApiController@likeUnlikeMsg');
    Route::post('/agenda', 'Api\ApiController@agendaList');
    Route::post('/get-participant-list', 'Api\ApiController@getParticipantList');
    Route::post('/update-presence-status', 'Api\ApiController@updatePresentStatus');
    Route::post('/update-register-status', 'Api\ApiController@updateRegister');
    Route::post('/update-user-status', 'Api\ApiController@updateUserStatus');
    Route::get('/get-workshop-secretory', 'Api\ApiController@getAdminWorkshop');
    Route::get('/download-document', 'Api\ApiController@downloadDocument');
    Route::get('/get-notification', 'Api\ApiController@getNotifications');
    Route::get('/get-unread-count', 'Api\ApiController@getUnreadCount');
    Route::post('/mark-notification-read', 'Api\ApiController@markNotificationRead');
    Route::post('/delete-notification', 'Api\ApiController@destroyNotification');
    Route::post('/get-personal-message', 'Api\ApiController@getPersonalMessage');
    Route::post('/add-personal-reply', 'Api\ApiController@addPersonalMessageReply');
    Route::post('/meeting-final-date', 'Api\ApiController@finalDateMeeting');

});
Route::post('/get-future-metting-list', 'Api\ApiController@getFutureMettingList');
Route::post('/update-user-status', 'Api\ApiController@updateUserStatus');
Route::post('/mobile-login', 'Api\ApiController@login');
Route::post('/check-hostname', 'Api\ApiController@checkHostName');

