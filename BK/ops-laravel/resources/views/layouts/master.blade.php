@php
    $domain=(!empty($_SERVER['HTTP_HOST']))?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];
             $css_data = getDomainGraphicSetting($domain);
         @$headerLogo=$css_data['header_logo'];

@endphp
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
    <link href="{{ URL::asset('/style.css')}}" rel="stylesheet" type="text/css">

    <link href="{{ URL::asset('public/css/media.css')}}" rel="stylesheet" type="text/css">
    <script src="{{ URL::asset('public/js/jquery.min.js') }}"></script>
    <script type="text/javascript">
        var ajax_path = "{{ url('/api/') }}";
        var base_path = "{{ url('/') }}";
        var loader_path = "{{ url('/img/loader.gif') }}";
        var CSRF_TOKEN = "{!! csrf_token() !!}";
    </script>
    <script src="{{ URL::asset('public/js/ops.js') }}"></script>
    <script src="{{ URL::asset('public/js/step_forms.js') }}"></script>

    @yield('head')
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
                            <a aria-current="false" href="#">
                                @if(@$header && $header!=NULL)
                                    <img alt="Logo" class="img-responsive" src="{{ $header }}"/>
                                @else
                                    <img alt="Logo" class="img-responsive"
                                         src="{{$headerLogo}}"/>
                                    {{--                                         src="https://s3-eu-west-2.amazonaws.com/{{$_SERVER['AWS_BUCKET']}}/opsimplify-logo.jpg"/>--}}
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
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                {{session('success')}}
            </div>


        @elseif(session('error') || (Request::has('error')))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                {{session()->has('error')?session('error'):Request::get('error')}}
            </div>
        @elseif(session('info'))
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                {{session('info')}}
            </div>
        @endif


        @yield('content')

        {{-- <div class="footer text-center site-bg-color white-text">
            Copyright @php echo date("Y"); @endphp Tous droits réservés
        </div> --}}
        <div class="footer text-center white-text workshop-color1">
            @if(session()->has('lang'))
                @if(session()->get('lang')=='EN')
                    Copyright ( c ) {{date('Y')}} All rights reserved
                @else
                    Copyright ( c ) {{date('Y')}} Tous droits réservés
                @endif
            @else
                Copyright ( c ) {{date('Y')}} Tous droits réservés
            @endif

        </div>
        <div class="footer text-center site-bg-color white-text">
            <div class="container">
                <ul id="site-lang" class="list-inline mb-0 inline-block pull-left" name="lang">
                    <li data-lang="EN"  @if(session()->get('lang')=='EN') class="list active" @else class="list" @endif id="EN" value="EN">EN</li>
                    <li data-lang="FR"  @if(session()->get('lang')=='FR') class="list active" @else class="list" @endif id="FR" value="EN">FR</li>

                </ul>
                Copyright 2020 All rights reserved
                <!--ul id="site-lang" class="list-inline mb-0 inline-block pull-right">
                    <li data-lang="EN" data-url="1">Term of use</li>
                    <li data-lang="EN" data-url="2">Confidentiality Policy</li>
                </ul-->
            </div>
        </div>
    </div>

</div>
<div class="modal fade popsonresize" id="screenResizeIE" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="ie-msg">La plateforme n'est pas optimisée pour Internet Explorer dont Microsoft est en train
                    d'abandonner la mise à jour.</p>
                <p id="ie-msg">Veuillez utiliser Chrome ou Edge ou Firefox ou Safari pour un expérience optimale.</p>
                <div class="clearfix text-center">
                    <div class="clearfix mt-15 mb-15">
                        <img src={{URL::asset('public/img/upgradeIE11-image.png')}} class="center-block img-responsive"
                        />
                    </div>
                    <a href="https://www.microsoft.com/fr-fr/windows/microsoft-edge" class="underline-link"
                       target="_blank">Upgrade link here</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script src="{{ URL::asset('public/js/bootstrap.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {

        $( ".list" ).click(function(event) {
            event.stopImmediatePropagation();

            var list_id = $(this).attr('id');
            console.log('list_id : '+list_id);

            $.ajax({
                method: "POST",
                url: "{{route('change-lang')}}",
                data: { lang: list_id}
            }).done(function( response ) {
                window.location.reload();

            });
        });

    });
    navigator.browserSpecs = (function () {
        var ua = navigator.userAgent,
            tem,
            M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
        if (/trident/i.test(M[1])) {
            tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
            return {
                name: 'IE',
                version: (tem[1] || '')
            };
        }
        if (M[1] === 'Chrome') {
            tem = ua.match(/\b(OPR|Edge)\/(\d+)/);
            if (tem != null) return {
                name: tem[1].replace('OPR', 'Opera'),
                version: tem[2]
            };
        }
        M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
        if ((tem = ua.match(/version\/(\d+)/i)) != null)
            M.splice(1, 1, tem[1]);
        return {
            name: M[0],
            version: M[1]
        };
    })();
    if (navigator.browserSpecs.name == 'IE') {
        // Do something for Firefox.
        if (navigator.browserSpecs.version <= 11) {
            $('#screenResizeIE').modal('show');
            // Do something for Firefox versions greater than 42.
        }
    }
</script>
</html>