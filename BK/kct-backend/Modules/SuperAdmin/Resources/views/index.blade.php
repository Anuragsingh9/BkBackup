@extends('superadmin::layouts.master')
@section('content')
    <div class="container cmpnySignIn">
        <div class="row justify-content-center text-center pt-5">
            <img src="{{ $mainLogo??null }}" alt="{{ env("APP_NAME") }}" class="body_logo">
            <h1 class="text_gray font-weight-bold">{{__('superadmin::labels.signin_to_account')}}</h1>
            <div class="login-form">
                @component("superadmin::components.messages_box") @endcomponent
                <form action="{{ route('su-account-check') }}" class="text-center p-0 mb-0 rounded-3" method="POST" autocomplete="off" >
                    {{ csrf_field() }}

                    <div class="mt-2">
                        <label for="accountName" class="movedLabel  px-2">{{__('superadmin::labels.enter_your_eventspace')}} <b>HummanConnect</b> URL </label>
                        <div class="input-group">

                            <input autocomplete="false" class="form-control" name="accountName"
                                   placeholder="{{__('superadmin::labels.your_eventspace')}}" id="accountInput" type="text" required="">

                            <span class="input-group-text fw-bold text_gray" id="inputGroup-sizing-sm">
                                .{{env('APP_FRONT_DOMAIN')}}
                            </span>
                        </div>
                    </div>
                    <!-- <div class="text-lg-start pt-1">
                        <a aria-current="false" class="plainOnThemeAcr" href="https://seque.in/account/forget">
                            Forget Account Name ?
                        </a>
                    </div> -->

                    <div class="mt-4 mb-4">
                        <button class="btn btn-primary btn-c2-h-o text-white login-button bg_blue logInBtn" type="submit">
                            {{__('superadmin::labels.btn_login')}}</button>
                    </div>
                </form>
                <div class="text-center txt_12" id="dont-hv-login">
                    {{__('superadmin::labels.suggest_new_eventspace')}}
                    <a aria-current="false" class="text-decoration-none text_blue" href="{{route('su-account-create-1')}}">
                        {{__('superadmin::labels.create_new_eventspace')}}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            let nameInput = $("#accountInput");
            nameInput.keydown(function () {
                // on pressing hiding the error section as the value is updated
                $("#errorSection").hide();
            });
        });
    </script>
@endsection
