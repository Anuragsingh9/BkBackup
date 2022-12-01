@extends('qualification::layouts.master-ops')
@section('content')

    <style>
        .page-content {
            padding-top: 0px !important;
        }

        html, body, main.main, .registration-verifiy {
            height: 100%;
        }

        .app-body {
            height: calc(100% - 165px);
        }
        .btn-orange, .btn-orange:hover, .btn-orange:focus {
            background: #ffdd00;
            color: #fff;
            outline: none !important;
        }
    </style>

    <section class="banner" id="banner" style="display: none;">
        <div class="container">
            <div class="parent-card-block">
                <img src={{url('public/qualification/card-icon.png')}} class="center-block">
                <div class="card-block">
                    <div class="card-upper-block">
                        <div class="card-company-block">
                            @php
                                $i=0;
                            @endphp
                            @foreach($field->fields as $k=>$item)
                                @if(isset($item->skillFormat) && $item->skillFormat->name_en=='Text' && $i<1)
                                    @php
                                        $i++;
                                    @endphp
                                    @php
                                        $company='';
                                            $str=(isset($item->userSkill) && $item->userSkill!=NULL)?$item->userSkill->text_input:$item->name;
                                                $exp=explode(' ',$str);
                                              if(strlen($exp[0]) > 18){
                                              $trimstring = substr($str, 0, 18). '...';
                                                $company=$trimstring;
                                              }else{
                                                $company=$exp[0]. '...';
                                              }

                                    @endphp

                                    <span class="company-name">{{$company }}</span>
                                @endif
                            @endforeach
                            <span class="zip-code">{{ (strlen($user->postal) < 5)?str_pad($user->postal, 5, '0', STR_PAD_LEFT):$user->postal }}</span>
                        </div>
                        <div class="card-upper-logo">
                            <img src="{{ ($workshop!=NULL)?$workshop->workshop_logo:'' }}"
                                 class="center-block img-responsive">
                        </div>
                    </div>
                    <div class="card-lower-block">
                        <div class="card-bottom-logo">
                            <img src={{url('public/qualification/carte-pro.png')}} class="center-block
                                 img-responsive">
                        </div>
                        <div class="domians-name">
                            <ul>
                                @foreach($field->fields as $k=>$item)
                                    {{-- @if(isset($item->skillFormat) && $item->skillFormat->name_en=='Checkbox') --}}
                                    @if(isset($item->skillFormat) && (in_array($item->skillFormat->name_en,['Conditional CheckBox','Checkbox','Conditional CheckBox','Mandatory Checkbox'])))
                                        @if(isset($item->userSkill) && $item->userSkill!=NULL && $item->userSkill->checkbox_input==1)
                                            <li> {{ $item->name }}</li>
                                        @endif
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="double-border-line" style="display: none;"></div>

    <!-- ********** Step 2 Start ********** -->
    <div class="registration-verifiy grey-bg registration-step" id="step_2">
        <div class="container page-content">
            <div id="loader" style="display:none">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                    <path transform="translate(2)" d="M 0 8.15799 V 23.842 H 4 V 8.15799 Z">
                        <animate attributeName="d"
                                 values="M0 12 V20 H4 V12z; M0 4 V28 H4 V4z; M0 12 V20 H4 V12z; M0 12 V20 H4 V12z"
                                 dur="1.2s" repeatCount="indefinite" begin="0" keyTimes="0;.2;.5;1"
                                 keySplines="0.2 0.2 0.4 0.8;0.2 0.6 0.4 0.8;0.2 0.8 0.4 0.8"
                                 calcMode="spline"></animate>
                    </path>
                    <path transform="translate(8)" d="M 0 6.35564 V 25.6444 H 4 V 6.35564 Z">
                        <animate attributeName="d"
                                 values="M0 12 V20 H4 V12z; M0 4 V28 H4 V4z; M0 12 V20 H4 V12z; M0 12 V20 H4 V12z"
                                 dur="1.2s" repeatCount="indefinite" begin="0.2" keyTimes="0;.2;.5;1"
                                 keySplines="0.2 0.2 0.4 0.8;0.2 0.6 0.4 0.8;0.2 0.8 0.4 0.8"
                                 calcMode="spline"></animate>
                    </path>
                    <path transform="translate(14)" d="M 0 12 V 20 H 4 V 12 Z">
                        <animate attributeName="d"
                                 values="M0 12 V20 H4 V12z; M0 4 V28 H4 V4z; M0 12 V20 H4 V12z; M0 12 V20 H4 V12z"
                                 dur="1.2s" repeatCount="indefinite" begin="0.4" keyTimes="0;.2;.5;1"
                                 keySplines="0.2 0.2 0.4 0.8;0.2 0.6 0.4 0.8;0.2 0.8 0.4 0.8"
                                 calcMode="spline"></animate>
                    </path>
                    <path transform="translate(20)" d="M 0 12 V 20 H 4 V 12 Z">
                        <animate attributeName="d"
                                 values="M0 12 V20 H4 V12z; M0 4 V28 H4 V4z; M0 12 V20 H4 V12z; M0 12 V20 H4 V12z"
                                 dur="1.2s" repeatCount="indefinite" begin="0.6" keyTimes="0;.2;.5;1"
                                 keySplines="0.2 0.2 0.4 0.8;0.2 0.6 0.4 0.8;0.2 0.8 0.4 0.8"
                                 calcMode="spline"></animate>
                    </path>
                    <path transform="translate(26)" d="M 0 12 V 20 H 4 V 12 Z">
                        <animate attributeName="d"
                                 values="M0 12 V20 H4 V12z; M0 4 V28 H4 V4z; M0 12 V20 H4 V12z; M0 12 V20 H4 V12z"
                                 dur="1.2s" repeatCount="indefinite" begin="0.8" keyTimes="0;.2;.5;1"
                                 keySplines="0.2 0.2 0.4 0.8;0.2 0.6 0.4 0.8;0.2 0.8 0.4 0.8"
                                 calcMode="spline"></animate>
                    </path>
                </svg>
            </div>
            <div class="col-xs-12 col-sm-6 col-sm-offset-3 white-text text-center">
                <h2 class="">Vérifiez votre Email</h2>
                <h4 class="regi-verification-content">
                    Nous vous avons envoyé
                    <br>votre code de validation à 6 chiffres
                    <br>à
                    <strong><em>{{ $email }}</em></strong>
                    </br>
                    </br>
                </h4>
                <div class="para">
                    Entrez le code dans les cases ci-dessous<br>
                    pour valider votre accès à {{isset($org)?$org->name_org:'OPsimplify'}}</br>
                </div>
                <form class="verification-code-form" id="comment">
                    <input class="verify-code" maxlength="1" name="code_1" type="text"/>
                    <span>-</span>
                    <input class="verify-code" maxlength="1" name="code_2" type="text"/>
                    <span>-</span>
                    <input class="verify-code" maxlength="1" name="code_3" type="text"/>
                    <span>-</span>
                    <input class="verify-code" maxlength="1" name="code_4" type="text"/>
                    <span>-</span>
                    <input class="verify-code" maxlength="1" name="code_5" type="text"/>
                    <span>-</span>
                    <input class="verify-code" maxlength="1" name="code_6" type="text"/>
                    <div class="row">
                        <div class="col-xs-12 col-md-12 submit form-group mt-50">
                            <button type="submit" class="btn btn-orange">Validate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ********** Step 2 End ********** -->



    <script>
        /* $(document).on('keyup',".verify-code", function () {
             //alert('on keyup');
             if (this.value.length == this.maxLength) {
                 $(this).next('.verify-code').focus();
                 if ($(this).next()[0] != undefined && $(this).next()[0].nodeName == 'SPAN') {
                     $($(this).next()).next('.verify-code').focus();
                 }
             }
         });
         $(document).on('input',".verify-code", function () {
            // alert('on Input');
             var firstVal = $("input[name=code_1]").val();
             var secondVal = $("input[name=code_2]").val();
             var thirdVal = $("input[name=code_3]").val();
             var fourthVal = $("input[name=code_4]").val();
             var fivethVal = $("input[name=code_5]").val();
             var sixthVal = $("input[name=code_6]").val();


             if (firstVal != "" && secondVal != "" && thirdVal != "" && fourthVal != "" && fivethVal != "" && sixthVal != "") {
                 var email = "{{ $email }}"
                var data = {
                    code: firstVal + secondVal + thirdVal + fourthVal + fivethVal + sixthVal,
                    id:{!! $userid !!}, email: email
                }
                $.ajax({
                    url: "{{ url('registration-check-code') }}",
                    data: data,
                    type: 'post',
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    beforeSend: function (data) {
                        {{--  console.log(data)  --}}
        $('#loader').show()
    },
    success: function (data) {
        $('#loader').hide()
        if (data.status) {
            // console.log(data)
            // alert(data.msg)
            window.location.reload();
            window.location.replace(data.url);
{{-- window.location = 'https://stackoverflow.com/questions/4744751/how-do-i-redirect-with-javascript'; --}}
        } else {
            //4-6-7-7-2-8
            alert(data.msg)
        }
    },
    error: function (data) {
        console.log('error', data)
        $('#loader').hide()
    },
    complete: function (data) {
        console.log('complete')
    }
});
}
});*/
        function ajaxCall(){
            var firstVal = $("input[name=code_1]").val();
            var secondVal = $("input[name=code_2]").val();
            var thirdVal = $("input[name=code_3]").val();
            var fourthVal = $("input[name=code_4]").val();
            var fivethVal = $("input[name=code_5]").val();
            var sixthVal = $("input[name=code_6]").val();


            if (firstVal != "" && secondVal != "" && thirdVal != "" && fourthVal != "" && fivethVal != "" && sixthVal != "") {
                var email = "{{ $email }}"
                var data = {
                    code: firstVal + secondVal + thirdVal + fourthVal + fivethVal + sixthVal,
                    id:{!! $userid !!}, email: email
                }
                $.ajax({
                    url: "{{ url('registration-check-code') }}",
                    data: data,
                    type: 'post',
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    beforeSend: function (data) {
                        {{--  console.log(data)  --}}
                        $('#loader').show()
                    },
                    success: function (data) {
                        $('#loader').hide()
                        if (data.status) {

                            // console.log(data)
                            // alert(data.msg)
                            //window.location.reload();
                            console.log(data,encodeURIComponent(data.url))
                            window.location=((data.url));
                            {{-- window.location = 'https://stackoverflow.com/questions/4744751/how-do-i-redirect-with-javascript'; --}}
                        } else {
                            $("input[name=code_1]").val('');
                            $("input[name=code_2]").val('');
                            $("input[name=code_3]").val('');
                            $("input[name=code_4]").val('');
                            $("input[name=code_5]").val('');
                            $("input[name=code_6]").val('');
                            $("input[name=code_1]").focus();
                            alert(data.msg)
                            window.location.reload();
                        }
                    },
                    error: function (data) {
                        console.log('error', data)
                        $('#loader').hide()
                        window.location.reload();
                    },
                    complete: function (data) {
                        console.log('complete')
                    }
                });
            }
        }
        $(document).ready(function () {

            $("input[name=code_1]").focus();
            $("input[name=code_1]").val('');
            $("input[name=code_2]").val('');
            $("input[name=code_3]").val('');
            $("input[name=code_4]").val('');
            $("input[name=code_5]").val('');
            $("input[name=code_6]").val('');
            $("input[name=code_1]").focus();
        });
        $('input').on('paste', function (e) {
            // common browser -> e.originalEvent.clipboardData
            // uncommon browser -> window.clipboardData
            var clipboardData = e.clipboardData || e.originalEvent.clipboardData || window.clipboardData;
            var pastedData = clipboardData.getData('text').trim();
            if (pastedData.length == 11) {
                var arr = pastedData.split('-');
                if (arr.length == 6) {
                    $("input[name=code_1]").val(arr[0]);
                    $("input[name=code_2]").val(arr[1]);
                    $("input[name=code_3]").val(arr[2]);
                    $("input[name=code_4]").val(arr[3]);
                    $("input[name=code_5]").val(arr[4]);
                    $("input[name=code_6]").val(arr[5]);
                    console.log(navigator.userAgent.search("Firefox"), navigator.userAgent);
                    if (navigator.userAgent.search("Firefox") > 0) {
                        ajaxCall();
                    }
                } else {
                    alert('Invalid Code');
                }
            } else {
                alert('Invalid Code');
            }
        });

        $(document).on('keyup', ".verify-code", function () {
            if (this.value.length == this.maxLength) {
                $(this).next('.verify-code').focus();
                if ($(this).next()[0] != undefined && $(this).next()[0].nodeName == 'SPAN') {
                    $($(this).next()).next('.verify-code').focus();
                }
            }
        });

        $(".verify-code").one('input', function () {
            ajaxCall();
        });

        $('#comment').on('submit', function(e) {
            e.preventDefault();
            ajaxCall();
        });
    </script>
@stop
