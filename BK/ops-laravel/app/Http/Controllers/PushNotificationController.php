<?php

namespace App\Http\Controllers;

use App\Notification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use App\Document;

class PushNotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello Word')
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' => ['yash' => 'no', 'sourabh' => 'pancharia'], 'click_action' => 'v1', 'sub_text' => 'its sub text']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        //$token = "cl5ZWv1TZ4A:APA91bEV-P6MbDhULTvy6OObzOZgM5DkSfq2R5nihn6iAqJVqF2C_uRk8CK9IlRZQXO_gdwKOEFwiVpx1cQP492ONqOH015v8OrGgxHrqNihXyG80YTGSV99ORFCglFgtqU0ISlZYg02";
        $token = ["fSdL51VlowA:APA91bGqsW4xMmk8KcPh726iFddq4Syq9ztqwJzRiNMbUoq2gWAOnG_P5DVApxf12QKP2NfR5w2Ry4ODRtCUbdzQ3Z0ayq4nIHQhAUsx7lcPigbsKfk4TMRWH8If2nhpx0t62GhsDKKl"];

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        dd($downstreamResponse->tokensToModify(), $downstreamResponse, $downstreamResponse->numberSuccess());
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

//return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

//return Array (key : oldToken, value : new token - you must change the token in your database )
        $downstreamResponse->tokensToModify();

//return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();
        //return view('home');
    }

    public function sendNotificationForAll($dataValue, $heading, $tokens = [], $msg)
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder($heading);
        $notificationBuilder->setBody($msg)
            ->setSound('default')->setColor('purple');

        $dataBuilder = new PayloadDataBuilder();
        $uId = rand(15, 15);
        $dataBuilder->addData(['data' => $dataValue]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        // You must change it to get your tokens
        if (is_array($tokens) && count($tokens) < 0)
            $tokens = User::whereNotNull('device_token')->pluck('fcm_token')->toArray();
        else {
            $tokens = $tokens;
        }
//var_dump($tokens,count($tokens));
        $downstreamResponse = FCM::sendTo(($tokens), $option, $notification, $data);
        $tokens=[];

        if ($downstreamResponse->numberSuccess() >= 1)
            return true;
        else
            return false;
    }

    protected function multipleNotification()
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

// You must change it to get your tokens
        $tokens = MYDATABASE::pluck('fcm_token')->toArray();

        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

//return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

//return Array (key : oldToken, value : new token - you must change the token in your database )
        $downstreamResponse->tokensToModify();

//return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

// return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array
        $downstreamResponse->tokensWithError();
    }


}
