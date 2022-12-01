@extends('superadmin::layouts.master')
@component('superadmin::components.public_header')@endcomponent
@section('content')
    <div class="container">
        <div class="row justify-content-center pt-5">
            <div class="login-form">
                @component("superadmin::components.messages_box") @endcomponent
                <form action="{{route('forgot-password')}}"
                      class="color1BG text-white text-center p-2 mb-5 rounded-3"
                      method="POST">
                    {{ csrf_field() }}
                    <div class="text-white pt-5 pb-5 text-center">
                        <label><h5>Forgot Password</h5></label>
                    </div>
                    <div class="login-email login-input">
                        <div class="input-group">
                        <span class="input-group-text" id="inputGroup-sizing-sm">
                            <i class="fas fa-envelope"></i>
                        </span>
                            <input autocomplete="false" class="form-control" name="email"
                                   placeholder="{{__('superadmin::words.email')}}"
                                   type="email" aria-describedby="passwordHelpInline"/>
                        </div>
                    </div>
                    <div class="d-grid gap-2 col-9 mx-auto mt-5 mb-5">
                        <button class="btn btn-primary btn-c2-h-o text-white login-button"
                                type="submit">Submit</button>
                    </div>
                    <input type="hidden" value="{{ session('token') }}">
                </form>
            </div>
            @component('superadmin::components.ask_for_signup') @endcomponent
        </div>
    </div>
@endsection
