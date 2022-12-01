@extends('superadmin::layouts.master')
@component('superadmin::components.public_header')@endcomponent
@section('content')
    <div class="container">
        <div class="row justify-content-center pt-5">
            <div class="login-form">
                @component("superadmin::components.messages_box") @endcomponent
                <form action="{{ route('su-account-forget-sub') }}"
                      class="text-center p-0 mb-0 rounded-3"
                      method="POST">
                    {{ csrf_field() }}
                    {{-- Email Field --}}
                    <div class="mt-2">
                        <div class="input-group">
                        <span class="input-group-text" id="inputGroup-sizing-sm">
                            <i class="fas fa-envelope"></i>
                        </span>
                            <input autocomplete="false" class="form-control" name="email"
                                   placeholder="{{__('superadmin::words.email')}}"
                                   type="email" required/>
                        </div>
                    </div>
                    <div class="d-grid gap-2 col-9 mx-auto mt-5 mb-2">
                        <button class="btn btn-primary btn-c2-h-o text-white login-button"
                                type="submit">{{ __('superadmin::labels.send_account_name') }}</button>
                    </div>
                </form>
                @component('superadmin::components.ask_for_signup') @endcomponent
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
