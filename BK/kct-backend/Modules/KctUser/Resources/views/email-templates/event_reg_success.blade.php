@extends('kctuser::layouts.otp-main')
@section('content')
    <table style="width:600px">
        <tbody>
        <tr>
            <td><p>{{__('kctuser::message.hi')}}</p></td>
        </tr>
        <tr>
            <td>
                <p>{{$description}}</p>
            </td>
        </tr>
        <tr>
            <td>
                <p><b>Event name:</b> {{$event_details['name']}}</p>
            </td>
        </tr>
        <tr>
            <td>
                <p><b>Event start:</b> {{$event_details['start']}}</p>
            </td>
        </tr>
        <tr>
            <td>
                <p><b>Event end:</b> {{$event_details['end']}}</p>
            </td>
        </tr>
        <tr>
            <td>
                <a href="{{$link}}">Click Here</a> to move on the Event
            </td>
        </tr>
        </tbody>
    </table>
@endsection
