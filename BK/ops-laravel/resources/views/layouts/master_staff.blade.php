<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">
<head>
    <title>OP Simplify</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <meta content="#000000" name="theme-color">
    <link href="http://localhost:3000/favicon.ico" rel="shortcut icon">
    <link href="{{ URL::asset('public/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('public/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('public/css/awesome-bootstrap-checkbox.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('public/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('public/css/normalize.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('public/css/style.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('public/css/media.css')}}" rel="stylesheet" type="text/css">
    <script src="{{ URL::asset('public/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    @yield('head')
    <style>
        .row > div label {
            color: #a94442 !important;
        }

        .form-group > .error {
            color: #a94442 !important;
        }
    </style>
</head>
<body>
<div class="ops_loader hide-elem"><img src="{{ url('public/img/loader.gif') }}" class=" " id=""/></div>
<div id="root">
    <div class="app" data-reactroot="">
        <header class="" id="header">
            <div id="logo-user-info">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" id="head-logo-sec">
                            <a aria-current="false" href="{{ url('accounts') }}">
                                <img alt="Logo" class="img-responsive" src="{{url('public/img/opsimplify-logo.jpg')}}"/>
                            </a>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                            <ul class="list-inline light-font text-uppercase">

                                <li>{{session()->get('staff')->name}}  </li>
                                <li><a href="{{route('stafflogout')}}">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div id="menu-setting-sec">
                <div class="container white-text">
                    <div class="row">
                        <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 nopadding" id="head-menu">

                        </div>
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right" id="setting-icons">

                        </div>
                    </div>
                </div>
            </div>
            <div id="main-menu">
                <div class="container">
                    <div class="row">
                        <ul class="nav navbar-nav tab-menu" style="height: 40px;">


                        </ul>
                        <div class="head-start-btn">

                        </div>
                    </div>
                </div>
            </div>
        </header>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                {{session('success')}}
            </div>
        @elseif(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                {{session('error')}}
            </div>
        @elseif(session('info'))
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                {{session('info')}}
            </div>
        @endif


        @yield('content')

        <div class="footer text-center site-bg-color white-text">
            Copyright 2017 Tous droits réservés
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    jQuery(document).ready(function () {
        $(document).on('click', '.browse', function () {
            var file = $(this).parent().parent().parent().find('.file');
            file.trigger('click');
        });
    });
    $(document).on('change', '.file', function () {
        $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
    });
</script>
<script src="{{ URL::asset('public/js/bootstrap.min.js') }}"></script>

</html>