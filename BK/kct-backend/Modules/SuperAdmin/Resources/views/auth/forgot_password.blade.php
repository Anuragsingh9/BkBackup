@extends('superadmin::layouts.master')
@component('superadmin::components.public_header')@endcomponent
@section('content')
    <div class="container">
        <div class="row justify-content-center pt-5">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible text-center" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @elseif(session('msg'))
                <div class="alert alert-primary text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                    <ul>
                        {{session('msg')}}
                    </ul>
                </div>
            @endif
            <div class="login-form">
                <form action="{{ route('forgot-pwd-email') }}"
                      class="color1BG text-white text-center p-2 mb-5 rounded-3"
                      method="POST">
                    {{ csrf_field() }}
                    {{-- Welcome Label --}}
                    <h4 class="login-form-heading mb-5 mt-5">
                        {{__('superadmin::labels.forgot_pwd_label')}}
                    </h4>
                    {{-- Email Field --}}
                    <div class="login-email login-input">
                        <div class="input-group">
                        <span class="input-group-text" id="inputGroup-sizing-sm">
                            <i class="fas fa-envelope"></i>
                        </span>
                            <input autocomplete="false" class="form-control" name="email"
                                   placeholder="{{__('superadmin::words.email')}}"
                                   type="email" value=""/>
                        </div>
                    </div>
                    <div class="d-grid gap-2 col-9 mx-auto mt-5 mb-5">
                        <button class="btn btn-primary btn-c2-h-o text-white login-button"
                                type="submit">{{ __('superadmin::words.login') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
