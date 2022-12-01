@extends('superadmin::layouts.master')
@component('superadmin::components.public_header')@endcomponent
@section('content')
    <div class="container">
        <div class="row justify-content-center pt-5">
            <div class="login-form">
                @component("superadmin::components.messages_box") @endcomponent
                <form action="{{route('reset-password')}}"
                      class="color1BG text-white text-center p-2 mb-5 rounded-3"
                      method="POST">
                    {{ csrf_field() }}
                    <div class="text-white pt-5 pb-5 text-center">
                        <label><h5>{{__('usermanagement::labels.reset_password')}}</h5></label>
                    </div>
                    {{-- New Password Field --}}
                    <div class="login-pass login-input">
                        <div class="input-group">
                            <span class="input-group-text" id="inputGroup-sizing-sm">
                            <i class="fas fa-key"></i>
                        </span>
                            <input class="form-control" type="password" name="password"
                                   placeholder="{{__('usermanagement::labels.new_password')}}"
                                   aria-describedby="passwordHelpInline"/>
                        </div>
                    </div>
                    {{-- Repeat Field --}}
                    <div class="login-pass login-input">
                        <div class="input-group">
                            <span class="input-group-text" id="inputGroup-sizing-sm">
                            <i class="fas fa-key"></i>
                        </span>
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="{{__('usermanagement::labels.repeat_password')}}"
                                   aria-describedby="passwordHelpInline" id="conf_password">
                            <span class="input-group-text" onclick="passwordVisibility()">
                            <i class="fas fa-eye" id="show-password" ></i>
                            <i class="fas fa-eye-slash" id="hide-password" style="display: none"></i>
                        </span>
                        </div>
                    </div>
                    <input type="hidden" name="email" value="{{$email}}">
                    <input type="hidden" name="identifier" value="{{$key}}">
                    <div class="d-grid gap-2 col-9 mx-auto mt-5 mb-5">
                        <button class="btn btn-primary btn-c2-h-o text-white login-button"
                                type="submit">Submit
                        </button>
                    </div>
                    {{--                access token --}}
                    <input type="hidden" value="{{ session('token') }}">
                </form>
            </div>
            @component('superadmin::components.ask_for_signup') @endcomponent
        </div>
    </div>
    <script>
        function passwordVisibility() {
            const visibility = document.getElementById("conf_password");
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
