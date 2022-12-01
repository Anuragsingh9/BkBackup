<?php
  
  Route::get('newsletter/message/report/export', 'Modules\Newsletter\Http\Controllers\NewsletterController@export');
Route::group(['middleware' =>  ['cors','web'], 'prefix' => 'newsletter', 'namespace' => 'Modules\Newsletter\Http\Controllers'], function () {

    // Newsletter sender routes
    Route::options('sender', function () {
        return false;
    });
    Route::options('imagesearch', function () {
        return;
    });
    Route::resource('sender', 'SenderController');
Route::get('template/getAllTemplates', 'TemplateController@getAllTemplates');
    // Template routes
    // route for edit templateBlock
    Route::get('template/block/{id}/edit', 'TemplateController@editTemplateBlock');
    // rote for short_order templateblocks
    Route::post('template/block/sortorder', 'TemplateController@shortOrder');
    // route for fetch templateblock data with template_id and update template_id in newsletter
    Route::get('template/block/{id}/{newsletter_id?}', 'TemplateController@getTemplateBlock');
    Route::put('/template/header-and-footer/{id}', 'TemplateController@updateHeaderFooter');
    // route for add templateblock
    Route::post('/template/{id}/block', 'TemplateController@addTemplateBlock');
    // route for update templateblock
    Route::post('/template/{tempId}/block/{blockId}', 'TemplateController@updateTemplateBlock');
    // route for delete templateblock
    Route::delete('/template/{tempId}/block/{blockId}', 'TemplateController@deleteTemplateBlock');
    Route::resource('template', 'TemplateController');


    // Newsletter routes
    Route::get('message/past/fetch/{id}', 'NewsletterController@FetchPastNewsletter');
    
    Route::get('message/past/sendTo/{id}', 'NewsletterController@sendToList');
    Route::get('message/past/list', 'NewsletterController@getPastNewsletterList');
    Route::get('message/past', 'NewsletterController@getPastNewsletter');
    Route::post('message/spamtest', 'NewsletterController@spamTest');
    Route::get('message/preview/{id}', 'NewsletterController@getNewsletterPreview');
    Route::get('message/{id}/fetchsending', 'NewsletterController@fetechNewsLetterSending');
    Route::post('message/save-newsletter-schedule', 'NewsletterController@newsletterScheduleSave');
    Route::post('message/cancel-schedule', 'NewsletterController@newsletterScheduleCancel');
    Route::post('message/send-test-email', 'NewsletterController@sendTestEmail');
    Route::get('weburl/{id}', 'NewsletterController@webUrl');
    // Route::get('message/report/export', 'NewsletterController@export');
    Route::get('message/report/{id}', 'NewsletterController@newsletterReport');
    Route::post('message/add-list-in-newsletter', 'NewsletterController@addListInNewsletter');
    Route::post('message/remove-list-in-newsletter', 'NewsletterController@removeListInNewsletter');
    Route::resource('message', 'NewsletterController');



    //Contact Routes
    Route::post('contact/{id}', 'ContactController@update');
    Route::resource('contact', 'ContactController');

    //Subscription form
    Route::get('subscription/getdata/', 'SubscriptionController@getData');
    Route::resource('subscription-form', 'SubscriptionController');




    Route::get('/future', 'NewsletterController@getFutureNewsletterList');
    Route::get('/Custom', 'NewsletterController@createCustomField');


    // Newsletter block routes
    //! route for fetch NewsletterBlocks data with newsletter_id
    Route::get('/block/{id}/getblocks', 'NewsletterBlockController@getNewsletterBlock');
    //! rote for short_order NewsletterBlocks
    Route::post('/block/shortorder', 'NewsletterBlockController@shortOrder');
     Route::post('/block-image-upload', 'NewsletterBlockController@blockImageUpload');
     Route::post('/block/update-order', 'NewsletterBlockController@updateOrder');
    Route::resource('/block', 'NewsletterBlockController');
    // Route::post('/{block', 'NewsletterController@addNewsletterBlock');
    // Route::post('/{newsletterId}/block/{blockId}', 'NewsletterController@updateNewsletterBlock');
    // Route::delete('/{newsletterId}/block/{blockId}', 'NewsletterController@deleteNewsletterBlock');

    // Route::get('/future', 'NewsletterController@getFutureNewsletterList');
    
    // api for adobe stock search in newsletter
    Route::post('imagesearch', 'AdobeStockController@imagesearch');
    Route::POST('/image_upload', 'AdobeStockController@save_searched_image_to_amazon');
    Route::post('/image_edit', 'AdobeStockController@resize_image');
    
    Route::get('search/{tense}', 'NewsletterController@searchNewsLetter');
    
    // NEWS MODERATION API
    Route::group(['middleware' => ['newsmoderation'], 'prefix' => 'news'], function () {
        Route::post('add', 'NewsController@store');// add news
        Route::get('getnews/bystatus', 'NewsController@getNews');// get all news of a specific  status
        Route::get('getnews/status', 'NewsController@newsStatusCount');// get count of all news by status
        Route::post('update', 'NewsController@update');// update the news
        Route::post('transition', 'NewsController@applyTransition');// apply Transition
        Route::post('delete', 'NewsController@deleteNews');// delete news
        Route::post('news/stock/upload', 'NewsController@stockImageUpload');// stock image upload
        Route::post('news/newsletter', 'NewsController@newsToNewsLetter');// create relation between news to newsletter
        Route::post('delete/newsletter', 'NewsController@deleteNewsLetter');// delete newsletter
        
        Route::group(['prefix' => 'review'], function () {
            Route::post('review/create', 'ReviewController@store'); // create review
            Route::get('getnews/review/{newsId}', 'ReviewController@getNewsReviews');// get review of a news
            Route::get('searchNews', 'ReviewController@searchNews');// Search news by Title
            Route::put('review/update/send', 'ReviewController@send');// update review
            Route::get('review/count/visible', 'ReviewController@countReviewBySent');// get  count of review with reactions where is_visible=1
        });
    });
    // END OF NEWS MODERATION API
});
