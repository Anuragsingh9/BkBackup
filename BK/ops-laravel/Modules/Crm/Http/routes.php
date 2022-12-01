<?php

Route::group(['middleware' => ['web', 'cors'/*,'auth'*/], 'prefix' => 'crm', 'namespace' => 'Modules\Crm\Http\Controllers'], function () {

    Route::get('/ram/{id}', function () {
        dd(getGrantedDomain(594));
    });
    Route::options('/assistance/{id}', function () {
        return;
    });
    Route::options('/filter/show/preview', function () {
        return;
    });
    Route::options('/store-user-open-filter', function () {
        return;
    });
    //Route::group(['middleware' => ['web', 'cors', 'crmApi'], 'prefix' => 'crm', 'namespace' => 'Modules\Crm\Http\Controllers'], function () {
    Route::get('/dashboard-search/{keyword}', 'DashBoardController@getSearch');
    Route::get('entity-detail/{id}/{type}/', 'CategoryController@entityDetailFetch');
    Route::post('update-static-fields', 'CategoryController@updateStaticData');
    Route::post('fetch-timeline', 'TaskController@getTimeLine');
    Route::resource('/categories', 'CategoryController');
    Route::get('/get-category-skills/{id}/{userId}/{type}', 'DashBoardController@getCategorySkills');
    Route::post('/notes/{note}', 'NotesController@update');
    Route::get('/notes/{note}/{type}', 'NotesController@show');
    Route::resource('/notes', 'NotesController');
    Route::get('/comments/{comment}/{type}', 'CommentController@show');
    Route::resource('/comments', 'CommentController');
    Route::get('/files/{file}/{type}', 'FilesController@show')->middleware('FileTabView');
    Route::delete('/files/delete/{fileID}', 'FilesController@removeFile');
    Route::resource('/files', 'FilesController')->middleware('FileResource');
    Route::get('/', 'CrmController@index');
    route::get('assistance/{field}', 'AssistanceController@index');
    Route::resource('assistance', 'AssistanceController');
    Route::resource('assisteam', 'AssisTeamController');
    Route::resource('task', 'TaskController');
    Route::get('/task/{task}/{type}', 'TaskController@show');
    Route::get('/fetch-users-role/{val}/{role}', 'AssisTeamController@getFilteredUser');

    //reports
    Route::post('/reports/{report}', 'AssistanceReportController@update');
    Route::get('/reports/{id}/{type}/{assId?}', 'AssistanceReportController@show');
    Route::resource('/reports', 'AssistanceReportController')->middleware('AssistanceResource');


    // FILTER GET ROUTES
    // get Filters types, entity and it's respective fields
    Route::get('/filter/{id}/preview', 'CrmController@preview');     // filter preview
//    Route::get('/filter/type', 'CrmController@getFilterTypeList');
    Route::get('/filter/type/{id?}', 'CrmController@getFilterTypeList');
    Route::options('/filter', function () {return;});     // get filter list
//    Route::get('/filters', 'CrmController@index');
    Route::get('/filters/{filterTypeId}', 'UserOpenFilterController@responseFilter');
    Route::get('/filter/{id}', 'CrmController@show');    // get single filter
    Route::options('/filter/save/workshop', function () {return;});
    Route::post('/filter/save/workshop', 'CrmController@addFilterToWorkshop');
    Route::post('/filter/show/preview', 'CrmController@previewBeforeSave');    // filter preview before save
    Route::options('/filter/{id}', function () { return; });    // Edit filter by id
    Route::get('/filter/fields/{id}', 'CrmController@getCustomFieldList');     // get Custom Field List
    Route::get('/filter/custom-fields/{keyword}/{type}/{filterId?}', 'CrmController@getCustomFillable');     // get custom field for person , company,instance and union type

// FILTER EDIT ROUTES
    Route::group(['middleware' => 'FilterEdit'], function () {
        Route::options('/filter/{id}/fields/update', function () {return;});
        
        Route::post('/filter/{id}', 'CrmController@update');
        Route::options('/filter/{id}/field/add', function () {return;});
        Route::post('/filter/{id}/field/add', 'CrmController@addFilterField');
        Route::post('/filter/{id}/field/delete', 'CrmController@deleteFilterField');
        Route::post('/filter/{id}/fields/update', 'CrmController@updateFilterFieldsValue')->middleware('FilterEdit');     //bulk update custom/default fields
    });

    // FILTER ADD
    Route::group(['middleware' => 'FilterAdd'], function () {
        Route::post('/filter', 'CrmController@store');   // save filter
        Route::post('/filter/save/new', 'CrmController@saveAsNew');   // save existing filter as new
    });
    //    Route::get('/filterss', 'CrmController@getFilterTypeList');

    Route::group(['middleware' => 'FilterDelete'], function () {
        Route::options('/filter/{id}/delete', function () {return;});     // Delete single Filter
        Route::post('/filter/{id}/delete', 'CrmController@destroy');
        Route::options('/filter/{id}/field/delete', function () {return;});
    });

    // Transcribe Service Routes
    Route::group(['middleware' => 'TranscribeCheck'], function () {
        Route::get('/transcribe/getKey', 'TranscribeController@getKey');
        Route::get('/transcribe/getRegion', 'TranscribeController@getRegion');
        Route::get('/transcribe/getSignature', 'TranscribeController@getSignature');
    });
    Route::get('/transcribe/saveblob', 'TranscribeController@saveBlob');
    Route::get('/transcribe/generateLogs', 'TranscribeController@periodicLogGenerate');

    // User Social Media Account Link
    Route::post('social-links/create', 'UserSocialAccountLinkController@create');
    Route::post('social-links/update', 'UserSocialAccountLinkController@update');
    Route::get('social-links/get/{type}/{id}', 'UserSocialAccountLinkController@get_user_social_links');
    Route::get('social-links/remove/{id}', 'UserSocialAccountLinkController@remove_social_link'); // type = contact , user
    Route::get('social-links/test', 'UserSocialAccountLinkController@test');

    Route::get('get-person-belongs/{personType}/{personId}', 'CategoryController@getPersonBelongsTo');

    //fetch Users
    Route::post('/fetch-crm-users', 'CrmController@getFilteredUser');
        
        //user Last Saved Filter
        Route::post('/store-user-open-filter', 'UserOpenFilterController@store');
        
});
    Route::group(['middleware' => ['web', 'cors','auth'], 'prefix' => 'crm', 'namespace' => 'App\Http\Controllers'], function () {
        Route::get('/get-workshops-list/{id}', 'WorkshopController@getWorkshopList');
    });
    