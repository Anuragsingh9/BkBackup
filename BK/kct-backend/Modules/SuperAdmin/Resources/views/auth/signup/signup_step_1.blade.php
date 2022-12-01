{{--This is for the first step of registration with Email --}}
@extends('superadmin::layouts.signup_layout')
@section('signup-body')
    <div class="col-6  right_card">
        <div class="text-center">
            <img src="{{ $mainLogo??null }}" alt="{{ env("APP_NAME") }}" class="body_logo">
            <h1>{{__('superadmin::labels.signup_description')}}</h1>
            <p>{{__('superadmin::labels.enter_email')}}</p>
        </div>
        @component("superadmin::components.messages_box") @endcomponent
        <form action="{{ route('su-signup-s1') }}" method="POST">
            {{ csrf_field() }}
            <div class="col-12 form-group">
                <div class="input-group borderInput">
                <span class="input-group-text" id="inputGroup-sizing-sm">
                  <i class="fas fa-envelope"></i>
                </span>
                    <input name="email" type="email" placeholder="{{__("superadmin::words.email")}}" class="form-control @error('isEmailExists') is-invalid @enderror"
                           aria-describedby="validationServer03Feedback" id="emailInput" required>
                </div>
            </div>
            <div class="d-grid col-12 mt-4 mb-4">
                <button class="btn  text-white btn-c1-h-o" type="submit">{{__('superadmin::labels.signup')}}</button>
            </div>
            <div class="text-center">
                {{__('superadmin::labels.already_have_acc')}}
                <a aria-current="false" class="color1Txt" href="{{ route('index')}}">
                    {{__('superadmin::labels.manually_signin')}}
                </a>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function () {
            let emailInput = $("#emailInput");
            emailInput.keydown(function () {
                // on pressing hiding the error section as the value is updated
                emailInput.removeClass("is-invalid");
                $("#errorSection").hide();
            });
        });
    </script>
@endsection
