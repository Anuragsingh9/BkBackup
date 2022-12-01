{{--This is for the second step of registration with Email --}}

@extends('superadmin::layouts.signup_layout')
@section('signup-body')

    <div class="col-6  right_card">
        @component("superadmin::components.messages_box") @endcomponent
        <div class="text-left ">
            <h4>{{__('superadmin::labels.signup_description')}}</h4>
        </div>
        <form action="{{ route('su-signup-s2') }}" method="POST">
            {{ csrf_field()}}
            <div class="row name_div mb-3 mt-4">
                <div class="col-6 form-group">
                    <div class="input-group">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">
                                        <i class="fas fa-user"></i>
                                    </span>
                        <input name="first_name" type="text" placeholder="{{__("superadmin::words.first_name")}}" class="form-control">
                    </div>
                </div>
                <div class="col-6 form-group">
                    <div class="input-group">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">
                                        <i class="fas fa-user"></i>
                                    </span>
                        <input name="last_name" type="text" placeholder="{{__("superadmin::words.last_name")}}" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row pswd_div mb-5">
                <div class="col-12 form-group">
                    <div class="input-group">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">
                                        <i class="fa fa-lock"></i>
                                    </span>
                        <input name="password" type="password" placeholder="{{__("superadmin::labels.enter_your_password")}}"
                               class="form-control" required="" id="password">
                        <span class="input-group-text" onclick="passwordVisibility()">
                                        <i class="fas fa-eye" id="show-password"></i>
                                        <i class="fas fa-eye-slash" id="hide-password" style="display: none"></i>
                                    </span>
                    </div>
                </div>
            </div>
            <div class="row compny_div">
                <h4 class="mb-3">{{ __('superadmin::labels.enter_organisation_name') }}</h4>
                <div class="col-12 form-group">
                    <div class="input-group">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">
                                        <i class="fa fa-building"></i>
                                    </span>
                        <input name="organisation_name" type="text" placeholder="{{__("superadmin::labels.your_company_name")}}"
                               class="form-control" required="">
                    </div>
                </div>
            </div>
            <div class="row domain_div">
                <p class="mt-3">{{ __('superadmin::labels.choose_account_name') }}
                    {{ __('superadmin::labels.this_is_private_domain') }}</p>
                <div class="col-12 form-group">
                    <div class="input-group">
                        <input name="fqdn" type="text" placeholder="{{__("superadmin::labels.your_company_name")}}" class="form-control"
                               required="">
                        <div class="input-group-append">
                            <span class="input-group-text color1BG text-white pb-2 fixed_domain">.{{ env('APP_FRONT_NAME') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row terms_div">
                <div class="form-group my-3">
                    <div class=" text-left">
                        <input class="term_condition" type="checkbox" value="" id="flexCheckChecked"
                               required="">
                        <label class="form-check-label pl-2" for="flexCheckChecked">
                            &nbsp;&nbsp;{{ __('superadmin::labels.i_agree_t_p') }} <a href="#" class="text-decoration-none text_blue">Terms of Uses</a> and <a href="#" class="text-decoration-none text_blue">Privacy Policy</a>.
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="d-grid gap-2 col-9 submitBtn mx-auto mt-4">
                    <button disabled class="btn btn-primary text-white txt_14 btn-c1-h-o submit_btn" type="submit">{{ __('superadmin::labels.create_account') }}</button>
                </div>
            </div>
        </form>
    </div>













{{--    <div class="col-6 p-3 pt-5">--}}
{{--        @component("superadmin::components.messages_box") @endcomponent--}}
{{--        <div class="text-center pb-3">--}}
{{--            <h5>{{__('superadmin::labels.signup_description')}}</h5>--}}
{{--        </div>--}}
{{--        <form action="{{ route('su-signup-s2') }}" method="POST">--}}
{{--            {{ csrf_field() }}--}}
{{--            <div class="row">--}}
{{--                <div class="col-6 form-group">--}}
{{--                    <div class="input-group">--}}
{{--                        <span class="input-group-text" id="inputGroup-sizing-sm">--}}
{{--                            <i class="fas fa-user"></i>--}}
{{--                        </span>--}}
{{--                        <input name="first_name" type="text" placeholder="{{__("superadmin::words.first_name")}}"--}}
{{--                               class="form-control">--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-6 form-group">--}}
{{--                    <div class="input-group">--}}
{{--                        <span class="input-group-text" id="inputGroup-sizing-sm">--}}
{{--                            <i class="fas fa-user"></i>--}}
{{--                        </span>--}}
{{--                        <input name="last_name" type="text" placeholder="{{__("superadmin::words.last_name")}}"--}}
{{--                               class="form-control">--}}
{{--                    </div>--}}
{{--                    --}}{{--                                <div class="invalid-feedback d-block">--}}
{{--                    --}}{{--                                    Please choose a username.--}}
{{--                    --}}{{--                                </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="row">--}}
{{--                <div class="col-12 form-group">--}}
{{--                    <div class="input-group">--}}
{{--                        <span class="input-group-text" id="inputGroup-sizing-sm">--}}
{{--                            <i class="fas fa-key"></i>--}}
{{--                        </span>--}}
{{--                        <input name="password" type="password"--}}
{{--                               placeholder="{{__("superadmin::labels.enter_your_password")}}"--}}
{{--                               class="form-control" required id="password">--}}
{{--                        <span class="input-group-text" onclick="passwordVisibility()">--}}
{{--                            <i class="fas fa-eye" id="show-password" ></i>--}}
{{--                            <i class="fas fa-eye-slash" id="hide-password" style="display: none"></i>--}}
{{--                        </span>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="row">--}}
{{--                <p class="mb-0">{{ __('superadmin::labels.create_account_for_company') }}</p>--}}
{{--                <div class="col-12 form-group">--}}
{{--                    <div class="input-group">--}}
{{--                        <span class="input-group-text" id="inputGroup-sizing-sm">--}}
{{--                            <i class="fas fa-building"></i>--}}
{{--                        </span>--}}
{{--                        <input name="organisation_name" type="text"--}}
{{--                               placeholder="{{__("superadmin::labels.your_company_name")}}"--}}
{{--                               class="form-control" required>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="row">--}}
{{--                <p class="mb-0">{{ __('superadmin::labels.choose_account_name') }}--}}
{{--                    <br>{{ __('superadmin::labels.this_is_private_domain') }}</p>--}}
{{--                <div class="col-12 form-group">--}}
{{--                    <div class="input-group">--}}
{{--                        <input name="fqdn" type="text"--}}
{{--                               placeholder="{{__("superadmin::labels.choose_domain")}}"--}}
{{--                               class="form-control" required>--}}
{{--                        <div class="input-group-append">--}}
{{--                            <span class="input-group-text color1BG text-white">{{ env('APP_FRONT_HOST') }}</span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="row">--}}
{{--                <div class="col-12 form-group ">--}}
{{--                    <div class="form-check">--}}
{{--                        <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" required>--}}
{{--                        <label class="form-check-label" for="flexCheckChecked">--}}
{{--                            {{ __('superadmin::labels.i_agree_t_p') }}--}}
{{--                        </label>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="row">--}}
{{--                <div class="d-grid gap-2 col-9 mx-auto mt-4">--}}
{{--                    <button class="btn btn-primary text-white btn-c1-h-o"--}}
{{--                            type="submit">{{ __('superadmin::labels.create_free_account') }}</button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </form>--}}
{{--    </div>--}}
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


        $(document).ready(function (){
            var checkboxe = $(".term_condition"),
                submitButt = $(".submit_btn");
            checkboxe.click(function() {
                submitButt.attr("disabled", !checkboxe.is(":checked"));
            });
        })
    </script>
@endsection
