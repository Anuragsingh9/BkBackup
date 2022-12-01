<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Module Qualification</title>
    <link href="{{ URL::asset('public/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('public/css/normalize.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link href="{{ URL::asset('public/css/style.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('public/css/media.css')}}" rel="stylesheet" type="text/css">
    <script src="{{ URL::asset('public/js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/bootstrap.min.js') }}"></script>
</head>
<style type="text/css">
    @font-face {
        font-family: 'QanelasMedium';
        src: url('../public/fonts/qualification/QanelasMedium.eot');
        src: url('../public/fonts/qualification/QanelasMedium.eot') format('embedded-opentype'),
        url('../public/fonts/qualification/QanelasMedium.woff2') format('woff2'),
        url('../public/fonts/qualification/QanelasMedium.woff') format('woff'),
        url('../public/fonts/qualification/QanelasMedium.ttf') format('truetype'),
        url('../public/fonts/qualification/QanelasMedium.svg#QanelasMedium') format('svg');
    }

    @font-face {
        font-family: 'QanelasExtraBold';
        src: url('../public/fonts/qualification/QanelasExtraBold.eot');
        src: url('../public/fonts/qualification/QanelasExtraBold.eot') format('embedded-opentype'),
        url('../public/fonts/qualification/QanelasExtraBold.woff2') format('woff2'),
        url('../public/fonts/qualification/QanelasExtraBold.woff') format('woff'),
        url('../public/fonts/qualification/QanelasExtraBold.ttf') format('truetype'),
        url('../public/fonts/qualification/QanelasExtraBold.svg#QanelasExtraBold') format('svg');
    }

    @font-face {
        font-family: 'QanelasSemiBold';
        src: url('../public/fonts/qualification/QanelasSemiBold.eot');
        src: url('../public/fonts/qualification/QanelasSemiBold.eot') format('embedded-opentype'),
        url('../public/fonts/qualification/QanelasSemiBold.woff2') format('woff2'),
        url('../public/fonts/qualification/QanelasSemiBold.woff') format('woff'),
        url('../public/fonts/qualification/QanelasSemiBold.ttf') format('truetype'),
        url('../public/fonts/qualification/QanelasSemiBold.svg#QanelasSemiBold') format('svg');
    }

    /* Header */
    #header {
        padding: 35px 0px;
    }

    #logo {
        position: absolute;
        max-width: 100%;
    }

    .menu-block-outer nav {
        display: inline-block;
        vertical-align: bottom;
        margin-bottom: 0;
        min-height: auto;
        float: right;
    }

    .navbar-collapse.collapse {
        padding-left: 0;
        padding-right: 0;
    }

    .navbar-nav > li > a:hover,
    .navbar-nav > li > a:focus {
        color: #2aa464;
        background: transparent;
    }

    .navbar-nav > li > a {
        font-size: 14px;
        color: #07378a;
        font-family: 'QanelasMedium';
    }

    .navbar-nav > li.active > a {
        color: #2aa464;
        font-family: 'QanelasExtraBold';
    }

    .header-right-menu ul li:not(:last-child) {
        margin-bottom: 14px;
        display: inline-block;
        width: 100%;
    }

    .nav > li > a {
        position: relative;
        display: block;
        padding: 3px 10px;
    }

    .navbar-nav > li:last-child a {
        background: #07378a;
        color: #fff;
        text-transform: uppercase;
        font-family: 'QanelasExtraBold';
        font-size: 13px;
        letter-spacing: 1px;
    }

    .header-right-menu ul li a {
        background-color: #2aa464;
        color: #ffffff;
        font-size: 16px;
        line-height: 16px;
        padding: 5px 10px;
    }

    .header-right-menu ul li:last-child a {
        background-color: #07378b;
    }

    .double-border-line {
        width: 100%;
        border-top: 14px solid #07378b;
        border-bottom: 14px solid #4472c0;
    }

    .navbar-toggle {
        background-color: #fdd530;
    }

    .navbar-toggle .icon-bar {
        background-color: #07378b;
    }

    .menu-block-outer nav.navbar {
        margin-top: 32px;
    }

    .menu-block-outer nav ul {
        margin-bottom: 0;
    }

    /* Header */

    #banner {
        background: url('../public/qualification/banner-pattern.png') no-repeat center bottom;
        background-size: 100% auto;
        padding: 60px 0px;
        min-height: 300px;
    }

    .parent-card-block {
        position: relative;
        display: block;
        max-width: 340px;
        margin: 0 auto;
    }

    .card-block {
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        right: 0;
        top: 0;
        margin: auto;
    }

    .card-upper-block {
        height: 45%;
        padding: 15px 0px 5px;
    }

    .card-lower-block {
        height: 55%;
    }

    .card-company-block {
        width: 70%;
        float: left;
    }

    .card-upper-logo {
        width: 27%;
        float: left;
        position: relative;
        min-height: 30px;
        padding: 0 3px;
    }

    .card-upper-logo:after {
        content: '';
        position: absolute;
        top: 10px;
        bottom: 0;
        background: url(../public/qualification/arrowright.jpg);
        width: 15px;
        height: 16px;
        right: -28px;
        /*transform: rotate(180deg);*/
    }

    .card-upper-logo img {
        max-width: 100%;
        max-height: 64px;
    }

    .card-bottom-logo {
        float: left;
        width: 30%;
        padding: 10px 0px;
    }

    .domians-name {
        float: left;
        width: 70%;
        display: table;
        height: 100%;
    }

    .card-company-block span {
        display: block;
        /*min-height: 20px;*/
        position: relative;
        color: #22498e;
        text-align: left;
        padding-left: 25px;
        font-family: 'Open Sans', sans-serif;
        font-weight: 600;
        font-size: 14px;
        line-height: 15px;
    }

    .card-company-block span:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        background: url(../public/qualification/arrow.jpg);
        width: 15px;
        height: 15px;
        left: -16px;
    }

    .card-company-block span.arrow-disable:before,
    .card-upper-logo.arrow-disable:after {
        display: none;
    }

    .domians-name ul {
        display: table-cell;
        vertical-align: middle;
        margin: 0;
        color: #fff;
        padding: 0;
        list-style: none;
    }

    .domians-name ul li {
        font-size: 9px;
        text-align: right;
        padding: 2px 0px;
        padding-right: 26px;
        position: relative;
    }

    .domians-name ul li:after {
        content: '';
        position: absolute;
        width: 15px;
        height: 5px;
        background: gold;
        top: 6px;
        right: 0;
    }

    .registration-step {
        min-height: 550px !important;
        border-bottom: 14px solid #07378b;
        position: relative;
    }

    #loader {
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        background-color: rgba(255, 255, 255, 0.8);
        z-index: 9;
    }

    #loader svg {
        width: 60px;
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        margin: auto;
    }

    #loader svg path {
        fill: #07378b;
    }

    .ops-header#header {
        padding: 0px 0px;
    }

</style>
<body>
@php
    $domain=(isset($hostname->id))?$hostname->id:$_SERVER['SERVER_NAME'];
@endphp

@if(in_array($domain,[2,'qualifelec.ooionline.com']))
    @include('qualification::layouts.qualiflec_menu')
@else
    <header class="ops-header" id="header">
        <div id="logo-user-info">
            <div class="container">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" id="head-logo-sec">
                        <a aria-current="false" href="#">
                            @if(@$header && $header!=NULL)
                                <img alt="Logo" class="img-responsive" src="{{ $header }}"/>
                            @else
                                <img alt="Logo" class="img-responsive"
                                     src="https://s3-eu-west-2.amazonaws.com/{{$_SERVER['AWS_BUCKET']}}/opsimplify-logo.jpg"/>
                            @endif
                        </a>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right" id="head-user-info">

                        {{--  <div class="company-icon inline-block">
                             <img alt="Logo" class="" src="{{ url('public/img/logo-icon.png') }}"/>
                         </div> --}}
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.menu')
        @endif
    </header>

    {{-- <!--> Qualifelec Header <!--> --}}

    <style>
        header.header-qualifelec-top.banner {
            background-color: #6ec6f1;
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: scroll;
            background-position: center center;
            background-image: url('../public/carte_pro/images/header.jpg');
        }

        .sf-menu li {
            text-align: center;
            color: #fff;
        }

        .sf-menu li {
            border-left: 1px solid #008ac9;
            padding-top: 10px;
            padding-bottom: 10px;
            -moz-box-shadow: inset 0 -10px 10px -10px #028ac9;
            -webkit-box-shadow: inset 0 -10px 10px -10px #028ac9;
            box-shadow: inset 0 -10px 20px -10px #028ac9;
        }

        .sf-menu li:hover {
            background-color: #008ac9;
        }

        .sf-menu li a:hover {
            color: #ffffff;
        }

        .sf-menu ul {
            background-color: #028ac9;
        }

        .sf-menu li li a, .sf-menu li li:hover > a, .sf-menu li.current-menu-item li a {
            color: #ffffff;
        }

        .sf-menu li li {
            box-shadow: none;
        }

        .sf-menu li.current-menu-item, .current_page_parent {
            background-color: #028ac9 !important;
            color: #ffffff;
        }

        .sf-menu li.current-menu-item a, .sf-menu li.current-menu-ancestor > a, .current_page_parent {
            color: #ffffff !important;
        }

        .sf-menu li.current-menu-item a:hover {
            color: #ffffff !important;
        }

        .sub-menu li {
            border-left: none;
            background-color: #008ac9;
        }

        .menu-item-24447 {
            border-right: 1px solid #028ac9;
        }

        .menu_circle {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            background-color: #028ac9;
            text-align: center;
            font-size: 20px;
            padding-top: 0px;
            line-height: 40px;
            float: left;
            margin-left: 20px;
        }

        .menu_circle a {
            color: #ffffff !important;
            cursor: pointer;
        }

        .menu_circle a:hover {
            color: #6ec7f1 !important;
        }

        .sf-menu-top {
            float: right;
            margin-top: 15px;
        }

        .menu-bkg {
            background-color: #6ec7f1;
        }

        .sf-menu, .sf-menu * {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .sf-menu li {
            position: relative;
            display: table-cell;
        }

        .sf-menu ul {
            position: absolute;
            display: none;
            top: 100%;
            left: 0;
            z-index: 9999;
        }

        .sf-menu li:hover > ul, .sf-menu li.sfHover > ul {
            display: block;
        }

        .sf-menu a {
            display: block;
            position: relative;
        }

        .sf-menu ul ul {
            top: 0;
            left: 100%;
        }

        /*-- Main styles --*/
        .sf-menu {
            margin: 0;
            background-color: transparent;
            width: 100%;
            display: table;
        }

        .sf-menu li {
            -webkit-transition: background .2s;
            transition: background .2s;
            background-color: transparent;
        }

        .sf-menu ul li {
            position: relative;
            padding: 0 15px;
        }

        .sf-menu ul {
            margin-left: -5px;
        }

        .sf-menu .menu-arrow {
            position: absolute;
            right: 0;
            top: 50%;
            font-size: 16px;
            margin-top: -8px;
        }

        .sf-menu a {
            padding: 10px;
            font-size: 14px;
            font-weight: normal;
            text-decoration: none;
            zoom: 1; /* IE7 */
            margin: 0 1px;
            color: #fff;
            font-family: 'QanelasSemiBold';
            cursor: pointer;
        }

        .sf-menu ul li a {
            padding: 10px 0;
        }

        .sf-menu ul li:last-child a {
            border: none !important;
        }
    </style>


    <div class="app-body">
        <main class="main">
            @yield('content')

            @if(in_array($domain,[2,'qualifelec.ooionline.com']))
                <div class="prefooter" id="secondary-menu">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="secondary-menu">
                                    <ul id="menu-menu-principal-bas" class="menu">
                                        <li class="menu-item"><a>Boutique</a></li>
                                        <li class="menu-item"><a>Mon Panier</a></li>
                                        <li class="menu-item"><a>Retour vers Devenir-qualifelec.fr</a></li>
                                        <li class="menu-item"><a>Retour vers Qualifelec.fr</a></li>
                                        <li class="menu-item"><a>Contactez-nous</a></li>
                                        <li class="menu-item"><a>Se déconnecter</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <footer id="footer" class="qualifelec-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div id="text-2" class="widget widget_text">
                                    <div class="textwidget">
                                        <div style="text-align:center;color:#6DBCFC;">Mon Portail Qualifelec v2.0</div>
                                        <div style="text-align:center;color:#6DBCFC;">
                                            <span style="color:#ffffff;">Qualifelec</span> - 109 rue Lemercier 75017
                                            PARIS
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            @endif

        </main>

    </div>
    <style>
        #footer.qualifelec-footer {
            padding: 60px 0 30px 0;
            background-position: center top;
            display: inline-block;
            width: 100%;
            background-color: #1b75bd;
            color: #727272;
            font-size: 14px;
            line-height: 22px;
            float: left;
        }

        #secondary-menu {
            padding: 30px 0;
            text-transform: uppercase;
            background-color: #6ec6f1;
            display: inline-block;
            width: 100%;
            float: left;
        }

        .secondary-menu .menu {
            margin: 0;
        }

        .secondary-menu ul {
            text-align: center;
        }

        .secondary-menu ul li {
            padding: 0 10px;
            list-style-type: none;
            display: inline;
            float: none;
        }

        #secondary-menu ul {
            line-height: 14px;
            font-weight: 700;
            font-style: normal;
            font-size: 14px;
        }

        #secondary-menu a,
        #secondary-menu li.current-menu-item a {
            color: #fff;
            cursor: pointer;
        }

        #secondary-menu a:hover,
        #secondary-menu li.current-menu-item a {
            color: #e0e0e0;
        }

        #secondary-menu a:active,
        #secondary-menu li.current-menu-item a {
            color: #e0e0e0;
        }
    </style>


</body>
</html>
