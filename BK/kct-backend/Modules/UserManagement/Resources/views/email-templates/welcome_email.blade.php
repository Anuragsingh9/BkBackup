@extends('usermanagement::layouts.otp')
@section('content')
    <table style="width:600px">
        <tbody>
        <tr>
            <td><p>{{__('kctuser::message.hi')}}</p></td>
        </tr>
        <tr>
            <td>
                <p>{!! __('usermanagement::messages.welcome_email',['group' => "$group",'role' => "$role"]) !!}</p>
            </td>
        </tr>
        <tr>
            <td>
                <p><b>{!! __('usermanagement::messages.signin_link') !!}</b>
                    <a href="{{$signInLink}}">{{$signInLink}}</a>
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <p><b>{!! __('usermanagement::messages.otp_email') !!}</b> {{$email}}</p>
            </td>
        </tr>
        <tr>
            <td>
                <p><b>{!! __('usermanagement::messages.otp_password') !!}</b> {{$password}}</p>
            </td>
        </tr>

        <tr style="margin-bottom:15px">
            <td><p>{{__('kctuser::message.the_humann_team')}} </p></td>
        </tr>

        </tbody>
    </table>
@endsection
