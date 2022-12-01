@extends('kctuser::layouts.otp-main')
@section('content')
    <table style="width:600px">

        <tbody>
        <tr>
            <td>{{ __('superadmin::words.welcome') }},</td>
        </tr>
        <tr>
            <td> {{ __('superadmin::messages.validation_code_description') }}</td>
        </tr>
        <tr>
            <td>
                <p style="font-size:36px;color:#0a8fc0"><strong>{!! $otp !!}</strong></p>
            </td>
        </tr>
        <tr>
            <td>{{ __("superadmin::messages.you_need_to_enter_this_code") }}</td>
        </tr>

        </tbody>

    </table>
@endsection

