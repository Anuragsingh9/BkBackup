<?php

Route::group(['middleware' => 'web', 'prefix' => 'v1/superadmin', 'namespace' => 'Modules\SuperAdmin\Http\Controllers'], function () {

	Route::get('export-tags/{type}', 'SuperAdminController@exportTags')->middleware('SuperAdmin');
	Route::get('pro-tag','SuperAdminController@showProTags')->name('pro-tag')->middleware('SuperAdmin');
    Route::get('perso-tag','SuperAdminController@showPersoTags')->name('perso-tag')->middleware('SuperAdmin');
    Route::get('search/tag','SuperAdminController@searchTags')->middleware('SuperAdmin');
    Route::post('merge/tag','SuperAdminController@mergeTag')->name('merge')->middleware('SuperAdmin');

    Route::get('accept-tag', 'SuperAdminController@acceptTag')->name('accept-tag')->middleware('SuperAdmin');
    Route::get('reject-tag', 'SuperAdminController@rejectTag')->name('reject-tag')->middleware('SuperAdmin');

    Route::get('update-tag', 'SuperAdminController@updateTagName')->name('update-tag')->middleware('SuperAdmin');
    
});
