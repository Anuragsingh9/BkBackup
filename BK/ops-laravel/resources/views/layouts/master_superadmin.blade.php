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
    </head>
    <body>
        <div class="ops_loader hide-elem"><img src="{{ url('public/img/loader.gif') }}"  class=" " id=""/></div> 
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
                                    <ul class="list-inline pt-20">

                                        <li class="mr-10">{{session()->get('superadmin')->name}} </li>
                                        <li>
                                            <a href="{{route('savesuperadminlogout')}}">
                                                <i class="fa fa-power-off mr-3" aria-hidden="true"></i>
                                                Logout
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="menu-setting-sec">
                        <div class="container white-text">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nopadding">
                                    <nav class="navbar mb-0">
                                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#head-menu" aria-expanded="false" aria-controls="navbar">
                                            <span class="sr-only">Toggle navigation</span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                        </button>
                                    <div class="collapse navbar-collapse" id="head-menu">
                                    <ul class="navbar-nav mr-auto list-inline light-font">
                                        <li><a class="white-text" href="{{ url('accounts') }}">Accounts</a></li>
                                        <li class="dropdrown">
                                            <span class="dropdown-toggle" id="super-admin-menu" data-toggle="dropdown">
                                                Super Admin
                                                <i class="fa fa-chevron-down"></i>
                                            </span>
                                            <ul class="dropdown-menu" aria-labelledby="super-admin-menu">
                                                <li>
                                                    <a href="{{ route('adminList') }}">List of Super Admin</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('newsuperadmin') }}">Create New Super Admin</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="dropdrown">
                                            <span class="dropdown-toggle" id="support-staff-menu" data-toggle="dropdown">
                                                Support Staff
                                                <i class="fa fa-chevron-down"></i>
                                            </span>
                                            <ul class="dropdown-menu" aria-labelledby="support-staff-menu">
                                                <li>
                                                    <a href="{{ route('staffList') }}">List of Support Staff</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('newstafflogin') }}">Create New Support Staff</a></li>
                                            </ul>
                                        </li>
                                        <li><a class="white-text" href="{{ route('guide-list') }}">Admin Settings</a></li>
                                        <li><a class="white-text" href="{{ route('adobe-stock-tracking') }}">Adobe Stock Tracking</a></li>
                                        <li><a class="white-text" href="{{ route('transcribe-tracking') }}">Transcribe Tracking</a></li>
                                        <li><a class="white-text" href="{{ route('add-module-list') }}">Add Modules</a></li>
{{--                                        <li><a class="white-text" href="{{ route('moduleList') }}">Modules List</a></li>--}}
                                        <li><a class="white-text" href="{{ route('bulk-acc-creation') }}">Instant Account Creation</a>  </li>
                                        <li><a class="white-text" href="{{ route('pro-tag') }}">Tag Moderation</a></li>

                                    </ul>
                                    </div>
                                    </nav>
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
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                    {{session('success')}}
                </div>
                @elseif(session('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                    {{session('error')}}
                </div>
                @elseif(session('info'))
                <div class="alert alert-warning alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
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