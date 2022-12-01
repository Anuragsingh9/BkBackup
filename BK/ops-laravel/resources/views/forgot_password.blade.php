@extends('layouts.master')
@php
    if (session('lang')=='FR')
     $add='Adresse email';
     else
     $add='Email Address';
@endphp
@section('content')
    <div class="app-body">
        <main class="main">
            <div class="container page-content">
                <div class="col-xs-12 col-sm-12">
                    {{ Form::open(array('url'=>route('forgot-password-process'),'class'=>'login-form text-center white-text'))}}
                    {{ csrf_field() }}
                    <h4 class="login-form-heading">
                        @if (session('lang')=='FR')Mot de passe oublié @else Forgot Password @endif

                    </h4>

                    <div class="login-email login-input">
                        <i aria-hidden="true" class="fa fa-envelope">
                        </i>
                        {{ Form::email('email','',array('class'=>'form-control','placeholder'=>"$add",'autocomplete'=>'false')) }}
                    </div>
                    <button class="btn btn-lg btn-primary btn-block" type="submit"> @if (session('lang')=='FR')
                            Soumettre @else Submit @endif</button>
                    {{ Form::close() }}
                    {{--<div class="text-center mt-60" id="dont-hv-login">--}}
                    {{--<!-- react-text: 154 -->--}}
                    {{--Don’t have a login?--}}
                    {{--<!-- /react-text -->--}}
                    {{--<a aria-current="false" class="site-color" href="{{ route('signup-email-form')}}">--}}
                    {{--Try OPS for free here--}}
                    {{--</a>--}}
                    {{--</div>--}}
                </div>
            </div>
        </main>
    </div>
@endsection