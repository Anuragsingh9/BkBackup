@extends('kctuser::layouts.otp-main')
@section('content')
    <div style="width:600px">

        <div style="padding: 5px 20px;border-radius: 10px;margin-top: 20px;box-shadow: 1px 1px 10px 1px #ABB2B9;">
            <p style="text-align: justify;text-justify: inter-word;font-family: sans-serif; font-size: 0.9rem;">{{__('kctuser::message.hi')}}</p>
            <p style="text-align: justify;text-justify: inter-word;font-family: sans-serif; font-size: 0.9rem;">{!! __('kctuser::message.otp_first_description') !!}</p>
        </div>

        <div>
            <p style="font-family: sans-serif; text-align:center; font-size:36px;color:#0a8fc0">
                <strong>{!! $code !!}</strong></p>
        </div>

        <div style="padding: 5px 20px;border-radius: 10px;margin-top: 20px;box-shadow: 1px 1px 10px 1px #ABB2B9;">
            <p style="text-align: justify;text-justify: inter-word;font-family: sans-serif; font-size: 0.9rem;">{{__('kctuser::message.otp_second_description')}}</p>
            <p style="text-align: center">
                <a style="text-decoration: none;display: inline-block;background-color: #0a8fc0;color: #FFF;padding: 10px 20px;border-radius: 50px;font-family: sans-serif; font-size: 0.9rem"
                   href="{{ $url }}">{{__('kctuser::message.go_to_registration')}}</a>
            </p>
        </div>

        <div class="card mb-20"
             style="padding: 5px 20px;border-radius: 10px;margin-top: 20px;box-shadow: 1px 1px 10px 1px #ABB2B9;margin-bottom: 20px">
            <p style="text-align: justify;text-justify: inter-word;font-family: sans-serif; font-size: 0.9rem;">{{__('kctuser::message.magic_link_description')}}</p>
            <p style="text-align: center">
                <a style="text-decoration: none;display: inline-block;background-color: #0a8fc0;color: #FFF;padding: 10px 20px;border-radius: 50px;font-family: sans-serif; font-size: 0.9rem"
                   href="{{ $apiUrl }}">{{__('kctuser::message.validate_directly')}}</a>
            </p>
            <p style="text-align: justify;text-justify: inter-word;font-family: sans-serif; font-size: 0.9rem;">{{__('kctuser::message.the_humann_team')}}</p>
        </div>
    </div>
@endsection
