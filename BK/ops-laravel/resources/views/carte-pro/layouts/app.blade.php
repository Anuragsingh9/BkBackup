<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">
    <head>
        <title>OP Simplify</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
        <meta content="#000000" name="theme-color">
        <link href="{{ url('public/img/favicon.png') }}" rel="shortcut icon">
        <link href="{{ URL::asset('public/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ URL::asset('public/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('public/css/awesome-bootstrap-checkbox.css')}}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('public/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('public/css/normalize.min.css')}}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('public/css/style.css')}}" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="{{ asset('public/carte_pro/css/app.css') }}" type="text/css">
        <link rel="stylesheet" href="{{ asset('public/carte_pro/css/media.css') }}" type="text/css">
        <link rel="stylesheet" href="{{ asset('public/carte_pro/css/style.css') }}" type="text/css">
       
        <link href="{{ URL::asset('public/css/media.css')}}" rel="stylesheet" type="text/css">
        <script src="{{ URL::asset('public/js/jquery.min.js') }}"> </script>
        <script type="text/javascript">
                var ajax_path= "{{ url('/api/') }}";
                var base_path = "{{ url('/') }}";
                var loader_path = "{{ url('/img/loader.gif') }}";
                var CSRF_TOKEN = "{!! csrf_token() !!}";
        </script>
        <script src="{{ URL::asset('public/js/ops.js') }}"> </script>
        <script src="{{ URL::asset('public/js/step_forms.js') }}"> </script>
            
        @yield('head')
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <style>
            #header{
                padding: 0px;
            }
        </style>
</head>
<body>
{{-- <header class="banner navbar navbar-default navbar-static-top " role="banner">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div id="logo">
                <a href="{{ route('carte-pro', $account) }}">
                    <img class="logo-main logo-reg"
                         src="{{ asset('public/carte_pro/images/logo.png') }}"
                         height='118' width='116' alt="Carte"/>
                </a>
            </div>
        </div>


        <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
            <ul id="menu-main-menu" class="nav navbar-nav">
                <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-398"><a
                        href="https://projectdevzone.com/carte-pro/les-avantages-de-la-carte/">Les avantages de la
                        carte</a></li>
                <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-397"><a
                        href="https://projectdevzone.com/carte-pro/enterprises/">Enterprises</a></li>
                <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-396"><a
                        href="https://projectdevzone.com/carte-pro/maatre-douvrage/">Maîtres d’ouvrages</a></li>
                <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-395"><a
                        href="https://projectdevzone.com/carte-pro/maitre-doeuvre/">Maitre d&#8217;oeuvre</a></li>
                <li class="menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-387 current_page_item menu-item-394">
                    <a href="{{ route('demandez-votre-carte', $account) }}" aria-current="page">Demandez votre carte</a></li>
                <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-399"><a
                        href="#">Contact</a></li>
                <li class="th-accent menu-item menu-item-type-post_type menu-item-object-page menu-item-393"><a
                        href="https://projectdevzone.com/carte-pro/mon-espace/">Mon espace</a></li>
            </ul>
        </nav>
    </div>
</header> --}}
<header class="" id="header">
    <div id="logo-user-info">
        <div class="container">
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" id="head-logo-sec">
                    <a aria-current="false" href="#">
                         @if(@$header && $header!=null)
                            <img alt="Logo" class="img-responsive" src="{{ $header }}"/>
                        @else
                             <img alt="Logo" class="img-responsive" src="https://s3-eu-west-2.amazonaws.com/{{$_SERVER['AWS_BUCKET']}}/opsimplify-logo.jpg"/>
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
</header>
@yield('content')
{{-- <footer class="footer" role="contentinfo">
    <div class="container">
        <div class="footer-widgets row th-widget-area">
            <div class="footer-area-1 col-md-3 col-sm-6">
                <section class="widget text-3 widget_text">
                    <div class="widget-inner"><h3 class="widget-title">Qui nous sommes</h3>
                        <div class="textwidget"><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                                eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                                quis nostrud exercitation ullamco</p>
                        </div>
                    </div>
                </section>
            </div>
            <div class="footer-area-2 col-md-3 col-sm-6">
                <section class="widget text-4 widget_text">
                    <div class="widget-inner"><h3 class="widget-title">Nous contacter</h3>
                        <div class="textwidget"><p>Contrary to popular belief, Lorem Ipsum is not simply random text. It
                                has roots in a piece of classical</p>
                            <p>Contrary to popular belief, Lorem Ipsum is not simply random text.</p>
                        </div>
                    </div>
                </section>
            </div>
            <div class="footer-area-3 col-md-3 col-sm-6">
                <section class="widget text-5 widget_text">
                    <div class="widget-inner"><h3 class="widget-title">La FFB</h3>
                        <div class="textwidget"><p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced
                                below for those interested.</p>
                        </div>
                    </div>
                </section>
            </div>
            <div class="footer-area-4 col-md-3 col-sm-6">
                <section class="widget media_image-2 widget_media_image">
                    <div class="widget-inner"><img width="104" height="77"
                                                   src="{{ asset('public/carte_pro/images/ffb-logo.jpg') }}"
                                                   class="image wp-image-373  attachment-full size-full" alt=""
                                                   style="max-width: 100%; height: auto;"/></div>
                </section>
                <section class="widget text-6 widget_text">
                    <div class="widget-inner"><h3 class="widget-title">Mention Légales</h3>
                        <div class="textwidget"><p>Copyright © FFB {{ date('Y') }}</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <div class="footer-btm-bar">
        <div class="container">
            <div class="footer-copyright row">
                <div class="col-xs-12">
                    <p></p>
                </div>
            </div>
        </div>
    </div>
</footer> --}}

<input type="hidden" value="{{ Request::root() }}" id="currentDomain">
<script src="{{ asset('public/carte_pro/js/jqueryWidgets.js') }}"></script>
<script src="{{ asset('public/carte_pro/js/custom-theme.js') }}"></script>
<script src="{{ asset('public/carte_pro/js/form-validation.js') }}"></script>
</body>
</html>
