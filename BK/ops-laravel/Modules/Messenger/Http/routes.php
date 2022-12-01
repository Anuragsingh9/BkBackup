<?php

Route::options('messenger/message/send', function () {
    die('here');
    return;
})->middleware(['cors']);
Route::group(['middleware' => ['cors', 'auth', 'api'],
              'prefix'     => 'messenger',
              'namespace'  => 'Modules\Messenger\Http\Controllers'], function () {
    
    Route::get('/', 'MessengerController@index');
    Route::post('/users-by-ids', 'MessengerController@getUserByIds');
    Route::post('/users-by-workshop-id', 'MessengerController@getUserByWorkshopId');
    Route::get('/user-personal-conversations', 'MessengerController@getPersonalConversations');
    Route::post('/user-conversations', 'MessengerController@getPersonalConversations');
    Route::post('/user-personal-message', 'MessengerController@getPersonalMessages');
    
    Route::post('/reply/workshop', 'MessengerController@replyToMessage');
    Route::post('/toggle/like', 'MessengerController@messageLikeToggle');
    Route::post('/toggle/star', 'MessengerController@messageStarToggle');
    Route::get('/search/direct-user/{workshopID}/{key}', 'MessengerController@searchUserForDirect');
    
    Route::post('/push/invoke', 'PushController@store');
    Route::get('/push/register', 'PushController@push');
    
    /* IM APIs Start from Here */
    Route::group(['prefix' => 'message'], function () {
        Route::options('send', function () {
            return;
        });
        Route::post('send', 'MessengerController@store');
        Route::put('update', 'MessengerController@update');
        Route::delete('delete', 'MessengerController@delete');
        Route::post('reply/create', 'MessengerController@reply');
        Route::put('reply/update', 'MessengerController@replyUpdate');
        Route::delete('reply/delete', 'MessengerController@replyDelete');
        Route::get('reply/all/{messageId}', 'MessengerController@getReplies')
            ->middleware('IsUserBelongToChannelOrWorkshop:messageId');
        Route::post('toggle/reaction', 'MessengerController@toggleReaction');
        Route::post('attach/single', 'MessengerController@uploadFile');
        Route::post('attach/multiple', 'MessengerController@uploadMultipleFile');
        Route::get('attach/download', 'MessengerController@downloadAttachment')
            ->middleware('IsUserBelongToChannelOrWorkshop:attachmentId');
    });
    
    Route::group(['prefix' => 'channel'], function () {
        Route::post('create', 'ChannelController@create');
        Route::put('update', 'ChannelController@update');
        Route::delete('delete', 'ChannelController@destroy');
        Route::get('files/{channelUuid}', 'ChannelController@getChannelFiles')
            ->middleware('IsUserBelongToChannelMiddleware:routeParam');
        Route::group(['prefix' => 'user'], function () {
            Route::post('add', 'ChannelController@addUserToChannel');
            Route::get('all/{channelUuid}', 'ChannelController@getUsers')
                ->middleware('IsUserBelongToChannelMiddleware:routeParam');
            Route::delete('remove', 'ChannelController@removeUserFromChannel');
        });
    });
    
    Route::group(['prefix' => 'topic'], function () {
        Route::post('create', 'TopicController@create');
        Route::put('update', 'TopicController@update');
    });
    Route::options('conversation/history/{channelUuid}', function () {
        return;
    });
    Route::get('conversation/history/{channelUuid}', 'ChannelController@getConversationHistory')
        ->middleware('IsUserBelongToChannelOrWorkshop:routeParam');
    Route::post('conversation/open', 'ChannelController@conversationOpen');
    Route::get('load/users', 'ChannelController@loadUsers');
    Route::get('load/panel', 'ChannelController@loadPanel');
    Route::get('workshop/users', 'ChannelController@getWorkshopUsers')->middleware('IsUserBelongToWorkshop');
    Route::post('user/panel/hide', 'ChannelController@hideUserFromPanel');
    Route::get('search/user', 'ChannelController@searchUser');
});


