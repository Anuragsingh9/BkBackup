<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'OpSimplify') }}</title>

    <!-- Styles -->
    
</head>

<style>
@font-face {
    font-family: 'MyriadProLight';
    src: url('public/fonts/MyriadProLight.eot');
    src: url('public/fonts/MyriadProLight.eot') format('embedded-opentype'),
         url('public/fonts/MyriadProLight.woff2') format('woff2'),
         url('public/fonts/MyriadProLight.woff') format('woff'),
         url('public/fonts/MyriadProLight.ttf') format('truetype'),
         url('public/fonts/MyriadProLight.svg#MyriadProLight') format('svg');
}
    .loginView .login-input{
        width: 105px;
    }
    .loginView input{
        border: 1px solid #d4d4d3;
        width: 95px;
        color: #9A9A9A;
        font-size: 12px;
        font-weight: 700;
        padding: 3px 5px;
        margin-bottom: 6px;
        font-weight: 400;
        font-family: 'MyriadProLight';
    }
    .loginView button[type="submit"] {
        width: 105px;
        color: #fff;
        padding: 4px 0;
        background-color: #0f87e1;
        font-weight: 700;
        border: none;
        cursor: pointer;
        font-family: 'MyriadProLight';
        font-size: 12px;
    }
    .forgot-pass a {
        display: block;
        text-align: center;
        padding-top: 5px;
        font-weight: 700;
        color: #8a8b8a;
        font-size: 11px;
        float: left;
        font-family: 'MyriadProLight';
        text-decoration: none;
    }
    .login-input #password-error,
    .login-input #email-error{
        display: none !important;
    }
    .loginView input.error{
        border-color: #b40000;
    }
</style>
<body>
        <div class="app-body">
            <main class="main">
                <div class="container page-content">
                    <div class="col-xs-12 col-sm-12 loginView">
                    {{ Form::open(array('url'=>route('adn-signin-process'),'class'=>'login-form text-center white-text'))}}
                       {{ csrf_field() }}
                            
                            <div class="login-email login-input">
                                {{ Form::text('email','',array('class'=>'form-control','placeholder'=>'Email Address','autocomplete'=>'false')) }}                                
                            </div>
                            <div class="login-pass login-input">
                               <input autocomplete="false" class="form-control" name="password" placeholder="Password" type="password" value=""/>
                            </div>
                            <button class="" type="submit">Se connecter</button>
                            <div class="forgot-pass">
                               <a aria-current="false" class="white-text" href="{{ route('adn-forgot-password') }}">
                                Mot de passe oublié ?
                                </a>
                               
                            </div>
                        {{ Form::close() }}
                        @if('https://opsimplify.com/signin' == url('/') || 'https://opsimplify.com' == url('/') )
                        <div class="text-center mt-60" id="dont-hv-login">
                            <!-- react-text: 154 -->
                            Don’t have a login?
                            <!-- /react-text -->
                            <a aria-current="false" class="site-color" href="{{ url('signup')}}">
                                Try OPS for free here
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
        <script src="{{ URL::asset('public/js/app.js') }}"></script>
        <script src="{{ URL::asset('public/js/bootstrap.min.js') }}"> </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"> </script>
     
<script>
$(document).ready(function(){
   

    $( ".login-form" ).validate({
      rules: {
        email: {
            required: true,
            email: true
        },
        password: {
          required: true
        }
      }
    });
});
</script>

    <!-- Scripts -->

</body>
</html>
