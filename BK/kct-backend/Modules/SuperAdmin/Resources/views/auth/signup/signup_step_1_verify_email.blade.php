{{--This is for the email verify step of registration with Email --}}

@extends('superadmin::layouts.signup_layout')
@section('signup-body')
    <div class="col-6  right_card">
        <div class="text-center pb-3">
            <h5 class="text_blue">{{__('superadmin::labels.verify_your_email')}}</h5>
        </div>
        @component("superadmin::components.messages_box") @endcomponent
        <div class="text-center">
            <p class="text_blue txt_14 mb-0"> {!! __('superadmin::labels.email_sent_notify', ['email' => session('signup_email')]) !!}</p>
        </div>
        <div class="text-center">
            <p class="txt_12 mb-5">({{__('superadmin::labels.not_you')}}
                <a aria-current="false" class="text_blue text-decoration-none" href="{{ route('su-account-create-1')}}">
                    {{__('superadmin::labels.change_email')}}
                </a>)</p>
        </div>

        <div class="text-center">
            <p class="txt_14 text_blue">{!! __('superadmin::labels.email_sent_notify_2', ['email' => session('signup_email')]) !!}</p>
        </div>
        <div class="text-center">
            <form class="verification-code-form" action="{{ route('su-signup-s1-2') }}"
                  method="POST" id="otp-form">
                            <span class="flexDiv">
                                {{ csrf_field() }}
                                <input class="verify-code" maxlength="1" name="code_1" id="otp1" type="text">
                                <input class="verify-code" maxlength="1" name="code_2" id="otp2" type="text">
                                <input class="verify-code" maxlength="1" name="code_3" id="otp3" type="text">
                                <div class="lineBox"></div>
                                <input class="verify-code" maxlength="1" name="code_4" id="otp4" type="text">
                                <input class="verify-code" maxlength="1" name="code_5" id="otp5" type="text">
                                <input class="verify-code" maxlength="1" name="code_6" id="otp6" type="text">
                            </span>
            </form>
        </div>
        <div class="text-center inline-block">
            <form action="{{route('su-resend-otp')}}" method="POST" id="otp-resend-btn"
                  style="display: none;" class="form-inline">
                {{ csrf_field() }}
                <input type="submit" class="form-control-plaintext form-inline color1Txt p-0 text_blue"
                       value="{{__('superadmin::labels.resend_otp')}}">
            </form>
            <p class="my-4 text_gray " style="display:flex; justify-content: center;">{{__('superadmin::labels.did_not_receive_otp')}} <span class="text_blue resendLinkTxt" id="resend2"> <span id="otp_timer"></span></span></p>
        </div>
    </div>
    <script>
        $(document).ready(function () {

            let timerSpan = $("#otp_timer");
            let resendBtn = $("#otp-resend-btn");
            let resendLabel = "{{ __('superadmin::labels.resend_otp') }}";

            const allowResend = function () {
                timerSpan.hide();
                $("#otp-resend-btn").appendTo("#resend2");
                resendBtn.show();
            }

            const otpInput = $(".verify-code");

            let resendTimer = {{ $otp->updated_at
                                ? $otp->updated_at->diffInSeconds(\Carbon\Carbon::now())
                                : $otp->created_at->diffInSeconds(\Carbon\Carbon::now()) }};
            let allowedTime = {{ config('superadmin.constants.otp_resend_sec') }};


            if (resendTimer < allowedTime) {// user need to wait to resend the otp after allowed wait time
                let timer = setInterval(function () {
                    let timerLeft = allowedTime - resendTimer;
                    resendTimer++;
                    timerSpan.text(`${resendLabel} (${timerLeft})`);
                    if (timerLeft <= 0) {
                        clearInterval(timer);
                        allowResend();
                    }
                }, 1000);
            } else {  // enough time spent to
                allowResend();
            }

            let form = $("#otp-form");
            const submitOtpForm = function (item) {
                let flag = true;
                otpInput.each(function (i) {
                    if (otpInput.eq(i).val() === '') {
                        flag = false;
                    }
                })

                if(flag) {
                    let i = otpInput.index(item);
                    if(i === otpInput.length - 1) {
                        form.submit();
                    }
                }
            }


            otpInput.on({
                paste(ev) { // Handle Pasting
                    const clip = ev.originalEvent.clipboardData.getData('text').trim();
                    // Split string to Array or characters
                    const s = [...clip];
                    // Populate inputs. Focus last input.
                    otpInput.val(i => {
                        return s[i];
                    });

                    otpInput[s.length - 1 > otpInput.length - 1 ? otpInput.length - 1 : s.length - 1].focus();

                    if (s.length >= otpInput.length) {
                        submitOtpForm(this);
                    }
                },
                input(ev) { // Handle typing
                    const i = otpInput.index(this);
                    if (this.value) otpInput.eq(i + 1).focus();
                },
                keyup(ev) { // Handle Deleting
                    const i = otpInput.index(this);
                    if (!this.value && ev.key === "Backspace" && i)
                        otpInput.eq(i - 1).focus();
                    submitOtpForm(this);
                },
                click() {
                    let i = otpInput.index(this);
                    let c = otpInput.eq(i);
                    let tmp = c.val();
                    c.focus();
                    otpInput.eq(i).val("");
                    otpInput.eq(i).blur();
                    otpInput.eq(i).focus();
                    otpInput.eq(i).val(tmp);
                }
            });
        });

    </script>
@endsection
