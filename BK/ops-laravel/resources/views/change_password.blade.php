@extends('layouts.master')
@section('content')
@php
    if(isset($workshop->setting) && $workshop->setting!=null && $workshop->setting['web']!=null){
        $colorData=$workshop->setting['web']['color1'];
        $color1=$colorData['r'].','.$colorData['g'].','.$colorData['b'].','.$colorData['a'];
    }
    else{
        $color1='';
    }
    if(isset($workshop->setting) && $workshop->setting!=null && $workshop->setting['web']!=null){
        $colorData=$workshop->setting['web']['color2'];
        $color2=$colorData['r'].','.$colorData['g'].','.$colorData['b'].','.$colorData['a'];
    }
    else{
        $color2='';
    }
    if(isset($workshop->setting) && $workshop->setting!=null && $workshop->setting['web']!=null){
        $header=env('AWS_PATH').$workshop->setting['web']['header_logo'];
    }
    else{
        $header=null;
    }
@endphp
<style>
    .workshop-color1, .site-bg-color{
        background-color: rgb({{ $color1 }})!important
    }
    .workshop-color2, #menu-setting-sec{
        background-color: rgb({{ $color2 }}) !important
    }
    .workshop-border-color2{
        border-color: rgb({{ $color2 }}) !important
    }
    #menu-setting-sec{
        border-color: rgb({{$color2}}) !important;
    }
</style>
        <div class="app-body">
            <main class="main">
                <div class="container page-content ">
                    <div class="col-xs-12 col-sm-12 ">
                    {{ Form::open(array('url'=>route('change-password-process'),'class'=>'login-form text-center white-text workshop-color1'))}}
                       {{ csrf_field() }}
                            <h4 class="login-form-heading">
                                {{$data->change_text}}
                            </h4>

                            <div class="login-pass login-input">
                                <i aria-hidden="true" class="fa fa-lock">
                                </i>
                                <input autocomplete="false" class="form-control" name="new_password" placeholder=  "{{$data->caption_text1}}" type="password" value="" id="new_password"/>
                                <div class="pass-toggle">
                                    <i aria-hidden="true" class="fa fa-eye show-pass" id="show-pass-new"></i>
                                    <i aria-hidden="true" class="fa fa-eye-slash hide-pass"id="hide-pass-new"></i>
                                </div>                              
                            </div>
                            <div class="login-pass login-input">
                                <i aria-hidden="true" class="fa fa-lock">
                                </i>
                             <input autocomplete="false" class="form-control" name="confirm_password" placeholder= " {{$data->caption_text2}}" type="password" value="" id="confirm_password"/>
                             <div class="pass-toggle">
                                    <i aria-hidden="true" class="fa fa-eye show-pass" id="show-pass"></i>
                                    <i aria-hidden="true" class="fa fa-eye-slash hide-pass" id="hide-pass"></i>
                                </div>
                            </div>
                            <button class="btn btn-lg btn-primary btn-block workshop-color2 workshop-border-color2" style="border-color: rgb({{ $color2 }})!important;background-color: rgb({{ $color2 }})!important" type="submit">  {{$data->button_text1}}</button>
                           
                        {{ Form::close() }}
                        
                    </div>
                </div>
            </main>
        </div>
        <script>
    $(document).on('click', '.show-pass', function(showpass) {
        showpass.preventDefault();
        $('#confirm_password').attr({
            type: 'text'
        });
        $(this).hide();
        $(this).siblings('#hide-pass').show();
    });
    $(document).on('click', '#hide-pass', function(hidepass) {
        hidepass.preventDefault();
        $('#confirm_password').attr({
            type: 'password'
        });
        $(this).hide();
        $(this).siblings('#show-pass').show();
    });
    $(document).on('click', '#show-pass-new', function(showpass) {
        showpass.preventDefault();
        $('#new_password').attr({
            type: 'text'
        });
        $(this).hide();
        $(this).siblings('#hide-pass-new').show();
    });
    $(document).on('click', '#hide-pass-new', function(hidepass) {
        hidepass.preventDefault();
        $('#new_password').attr({
            type: 'password'
        });
        $(this).hide();
        $(this).siblings('#show-pass-new').show();
    });
        </script>
@endsection