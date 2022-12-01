@extends('superadmin::layouts.master')
{{--@component('superadmin::components.public_header')@endcomponent--}}
@section('content')
    <div class="container">
        <div class="row justify-content-center pt-5">
            <div class="login-form">
                @component("superadmin::components.messages_box") @endcomponent
                <form action="{{ route('su-do-signin') }}"
                      class="color1BG text-white text-center p-2 mb-5 rounded-3"
                      method="POST">
                    {{ csrf_field() }}
                    {{-- Welcome Label --}}
                    <h4 class="login-form-heading mb-5 mt-5">
                        {{__('superadmin::labels.welcome_to_keepcontact')}}
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
                    {{-- Password Field --}}
                    <div class="login-pass login-input">
                        <div class="input-group">
                            <span class="input-group-text" id="inputGroup-sizing-sm">
                            <i class="fas fa-key"></i>
                        </span>
                            <input autocomplete="false" class="form-control" name="password"
                                   placeholder="{{__('superadmin::words.password')}}"
                                   type="password" id="password" value=""/>
                            <span class="input-group-text" onclick="passwordVisibility()">
                            <i class="fas fa-eye" id="show-password" ></i>
                            <i class="fas fa-eye-slash" id="hide-password" style="display: none"></i>
                        </span>
                        </div>
                    </div>
                    <div class="d-grid gap-2 col-9 mx-auto mt-5 mb-5">
                        <button class="btn btn-primary btn-c2-h-o text-white login-button"
                                type="submit">{{ __('superadmin::words.login') }}</button>
                    </div>
                    <div class="forgot-pass">
                        <a aria-current="false" class="plainAcr" href="{{route('su-forgot-password')}}">
                            {{__('superadmin::labels.forgot_password')}}
                        </a>
                    </div>
                </form>
            </div>
            @component('superadmin::components.ask_for_signup') @endcomponent

        </div>
    </div>
    <script>
        function passwordVisibility() {
            const visibility = document.getElementById("password");
            if (visibility.type === "password") {
                visibility.type = "text";
                const x = document.getElementById("show-password");
                x.style.display = "none";
                const y = document.getElementById("hide-password");
                y.style.display = "block";
            } else {
                visibility.type = "password";
                const y = document.getElementById("hide-password");
                y.style.display = "none";
                const x = document.getElementById("show-password");
                x.style.display = "block";
            }
        }
    </script>
@endsection
