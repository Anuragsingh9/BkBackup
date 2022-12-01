@php
    $domain=(!empty($_SERVER['HTTP_HOST']))?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];
             $css_data = getDomainGraphicSetting($domain);
         $host_name=@$css_data['host_name'];
@endphp

@extends('layouts.master')
@section('content')
    <div class="app-body">
        <main class="main">
            <div class="container page-content">
                <div class="col-xs-12 col-sm-12">
                    {{ Form::open(array('url'=>route('signin-process'),'class'=>'login-form text-center white-text'))}}
                    {{ csrf_field() }}
                    <h4 class="login-form-heading">
                        @if(getLabel(35)==NULL)
                            Bienvenue  to {{$host_name}}
                        @else
                            {{getLabel(35)}}
                        @endif
                    </h4>

                    <div class="login-email login-input">
                        <i aria-hidden="true" class="fa fa-envelope"></i>
                        {{ Form::text('email','',array('class'=>'form-control','placeholder'=>__(('message.email_add')),'autocomplete'=>'false')) }}
                    </div>
                    <div class="login-pass login-input">
                        <i aria-hidden="true" class="fa fa-lock"></i>
                        <input autocomplete="false" class="form-control" name="password" placeholder="@lang('message.password_text')"
                               type="password" value=""/>
                        <div class="pass-toggle">
                            <i aria-hidden="true" class="fa fa-eye show-pass"></i>
                            <i aria-hidden="true" class="fa fa-eye-slash hide-pass"></i>
                        </div>
                    </div>
                    <button class="btn btn-lg btn-primary btn-block" type="submit"> @lang('message.submit_btn')</button>
                    <div class="forgot-pass">
                        <a aria-current="false" class="white-text" href="{{ route('forgot-password') }}">
                            @lang('message.FORGOT_PASSWORD')?
                        </a>

                    </div>
                    {{ Form::close() }}
                    @if('https://opsimplify.com/signin' == url('/') || 'https://opsimplify.com' == url('/') )
                        {{--<div class="text-center mt-60" id="dont-hv-login">--}}
                        {{--<!-- react-text: 154 -->--}}
                        {{--Don’t have a login?--}}
                        {{--<!-- /react-text -->--}}
                        {{--<a aria-current="false" class="site-color" href="{{ url('signup')}}">--}}
                        {{--Try OPS for free here--}}
                        {{--</a>--}}
                        {{--</div>--}}
                    @endif
                </div>
            </div>
        </main>
    </div>
    <script>
        var message = {};
                @if(session()->has('lang') && session()->get('lang')=='FR')
        var message = {
                email: {
                    email: "Veuillez entrer un email valide.",
                    required: "Ce champ est requis.",
                },
                password: {
                    required: "Ce champ est requis.",
                }
            };
        @endif
        $(document).ready(function () {
            $(".login-form").validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true
                    }
                }, messages: message
            });

            $(document).on('click', '.show-pass', function (showpass) {
                showpass.preventDefault();
                $('.login-pass input').attr({
                    type: 'text'
                });
                $(this).hide();
                $(this).siblings('.hide-pass').show();
            });
            $(document).on('click', '.hide-pass', function (hidepass) {
                hidepass.preventDefault();
                $('.login-pass input').attr({
                    type: 'password'
                });
                $(this).hide();
                $(this).siblings('.show-pass').show();
            });
        });
    </script>
@endsection