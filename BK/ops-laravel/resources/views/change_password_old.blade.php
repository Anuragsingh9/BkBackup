@extends('layouts.master')
@section('content')
        <div class="app-body">
            <main class="main">
                <div class="container page-content">
                    <div class="col-xs-12 col-sm-12 ">
                    {{ Form::open(array('url'=>route('change-password-process'),'class'=>'login-form text-center white-text'))}}
                       {{ csrf_field() }}
                            <h4 class="login-form-heading">
                                Change Password
                            </h4>
                            
                            <div class="login-pass login-input">
                                <i aria-hidden="true" class="fa fa-lock">
                                </i>
                                <input autocomplete="false" class="form-control" name="new_password" placeholder="Enter New Password" type="password" value="" id="new_password"/> 
                                <div class="pass-toggle">
                                    <i aria-hidden="true" class="fa fa-eye show-pass" id="show-pass-new"></i>
                                    <i aria-hidden="true" class="fa fa-eye-slash hide-pass"id="hide-pass-new"></i>
                                </div>                              
                            </div>
                            <div class="login-pass login-input">
                                <i aria-hidden="true" class="fa fa-lock">
                                </i>
                             <input autocomplete="false" class="form-control" name="confirm_password" placeholder="Re-Enter Password" type="password" value="" id="confirm_password"/> 
                             <div class="pass-toggle">
                                    <i aria-hidden="true" class="fa fa-eye show-pass" id="show-pass"></i>
                                    <i aria-hidden="true" class="fa fa-eye-slash hide-pass" id="hide-pass"></i>
                                </div>
                            </div>
                            <button class="btn btn-lg btn-primary btn-block" type="submit"> mettre à jour</button>
                           
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