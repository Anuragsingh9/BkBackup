<?php

// @middleware eventApi => \Modules\Events\Http\Middleware\CheckEventsActive::class
Route::group(['middleware' => ['web', 'cors', 'eventApi', 'auth'], 'prefix' => 'events', 'namespace' => 'Modules\Events\Http\Controllers'], function () {
    
    /*EVENT DASHBOARD API*/
    Route::options('/events', function () {return true;});
    Route::options('/dates-occupied', function () {return true;});
    // EVENT
    Route::post('events', 'EventsController@store')->middleware('checkRole:admin');
    Route::put('events/{event_id}   ', 'EventsController@update')->middleware('checkRole:sec');
    Route::get('events/{event_id}', 'EventsController@show');
    Route::delete('events/{event_id}', 'EventsController@destroy')->middleware('checkRole:sec');
    Route::get('/dates-occupied', 'EventsController@occupiedDates')->middleware('checkRole:sec');
    Route::get('/getEventsList/{tense}/{itemPerPage?}', 'EventsController@getEventsListWithOrganiser');
    Route::get('/getEventWorkshops/{itemPerPage?}', 'EventsController@getEventWorkshops')->middleware('checkRole:sec');
    Route::get('/meeting/{workshopId}', 'EventsController@getEventWorkshopMeeting')->middleware('checkRole:sec');
    Route::get('/exportMembersList/{workshopId}', 'EventsController@exportMemberList')->middleware('checkRole:sec');
    
    // ORGANISER
    Route::resource('organiser', 'OrganiserController')->middleware('checkRole:admin');
    Route::get('/getOrganisersList/{itemPerPage?}', 'OrganiserController@getOrganiserList')->middleware('checkRole:admin');
    
    // ORG ADMIN
    Route::post('/updateOrgAdminSetting/{key}/{value}', 'EventsController@changeDefaultOrganiser')->middleware('checkRole:admin');
    Route::put('/admin/settings', 'EventsController@updateOrgAdminSetting')->middleware('checkRole:admin'); // to update event org admin setting
    
    // SEARCH API
    Route::get('/searchOrgAdmins/{key}', 'EventsController@searchOrgAdminList'); // for internal events
    Route::get('/searchEventOrganisers/{key}/{paginate?}', 'OrganiserController@searchOrganiserList')->middleware('checkRole:admin'); // for external events & search organiser
    Route::get('/getSecretoryList/{key}', 'EventsController@searchSecretoryList')->middleware('checkRole:admin'); // for default organiser page in org admin setting
    Route::get('/searchEvent/{tense}/{key}/{paginate?}', 'EventsController@searchEvent');
    Route::get('/searchEventWorkshop/{key}/{paginate?}', 'EventsController@searchEventWorkshop')->middleware('checkRole:sec');
});

Route::group(['middleware' => ['web', 'cors'], 'prefix' => 'events', 'namespace' => 'Modules\Events\Http\Controllers'], function () {
    Route::get('/get-company/{val}', 'EventsController@getCompanies');
    Route::post('/add-wp-member', 'EventsController@addMember');
});