<?php
    
    Route::group(['middleware' => ['web', 'auth', 'cors'], 'prefix' => 'messenger', 'namespace' => 'Modules\Messenger\Http\Controllers'], function () {
        Route::options('/user-personal-conversations', function () {
            return;
        });
        Route::options('/user-personal-message', function () {
            return;
        });
        Route::get('/', 'MessengerController@index');
        Route::post('/users-by-ids', 'MessengerController@getUserByIds');
        Route::post('/users-by-workshop-id', 'MessengerController@getUserByWorkshopId');
        Route::post('/store-personal-message', 'MessengerController@storePersonalMessage');
        Route::get('/user-personal-conversations', 'MessengerController@getPersonalConversations');
        Route::post('/user-personal-message', 'MessengerController@getPersonalMessages');
    
        Route::options('/reply/workshop', function(){
		return;
	});
        Route::post('/reply/workshop', 'MessengerController@replyToMessage');
        Route::post('/toggle/like', 'MessengerController@messageLikeToggle');
        Route::post('/toggle/star', 'MessengerController@messageStarToggle');
        Route::get('/search/direct-user/{workshopID}/{key}', 'MessengerController@searchUserForDirect');
        
        Route::post('/push/invoke', 'PushController@store');
        Route::get('/push/register', 'PushController@push');
        
        Route::group(['prefix' => 'channel'], function () {
            Route::post('create', 'ChannelController@create');
            Route::post('delete', 'ChannelController@delete');
            Route::post('user/add', 'ChannelController@addUserToChannel');
            Route::post('user/remove', 'ChannelController@removeUserFromChannel');
            Route::get('user/get', 'ChannelController@getUsers');
            Route::get('user/channel', 'ChannelController@getUserChannels');
        });
        
    });
