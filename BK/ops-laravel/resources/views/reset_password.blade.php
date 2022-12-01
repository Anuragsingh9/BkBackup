@extends('layouts.master')
@section('content')
    <div class="app-body">
        <main class="main">
            <div class="container page-content">
                <div class="col-xs-12 col-sm-12">
                    {{ Form::open(array('url'=>route('reset-password-process'),'class'=>'login-form text-center white-text'))}}
                    <input autocomplete="false" class="form-control" name="identifier" placeholder="Enter New Password"
                           type="hidden" value="{{request()->token}}"/>
                    {{ csrf_field() }}
                    <h4 class="login-form-heading">
                        @if (session('lang')=='FR')
                            Redéfinir votre mot de passe
                        @else
                            Reset your Password
                        @endif
                        {{--                                Reset your Password--}}
                    </h4>

                    <div class="login-email login-input">
                        <i aria-hidden="true" class="fa fa-envelope">
                        </i>
                        <input autocomplete="false" class="form-control" name="new_password"
                               placeholder=" @if (session('lang')=='FR')Votre nouveau mot de passe @else Enter New Password @endif" type="password"
                               value=""/>
                    </div>
                    <div class="login-pass login-input">
                        <i aria-hidden="true" class="fa fa-lock">
                        </i>
                        <input autocomplete="false" class="form-control" name="confirm_password"
                               placeholder="@if (session('lang')=='FR')Confirmer votre nouveau mot de passe @else Confirm Password @endif{{--Confirm Password--}}"
                               type="password" value=""/>
                    </div>
                    <button class="btn btn-lg btn-primary btn-block" type="submit">@if (session('lang')=='FR')Se connecter @else Submit @endif</button>

                    {{ Form::close() }}
                    <div class="text-center mt-60" id="dont-hv-login">
                        <!-- react-text: 154 -->
                        @if (session('lang')=='FR')Vous n’avez pas d’identifiant? @else Don’t have a login? @endif

                    {{--                            Don’t have a login?--}}
                    <!-- /react-text -->
                        <a aria-current="false" class="site-color" href="{{ route('signup-email-form')}}">
                            @if (session('lang')=='FR')Essayez OPS ici. @else Try OPS for free here @endif

                            {{--                                Try OPS for free here--}}
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection