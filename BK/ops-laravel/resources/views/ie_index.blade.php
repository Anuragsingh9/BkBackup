{{--@if(Auth::check())--}}
        <!DOCTYPE html>
<html lang="en">

<head>
    <title>OP Simplify</title>
    <meta charset="UTF-8">
    <meta name="keywords" content="Reinvent,Reinvent technologies,reinventer,resilience technologies" />
    <meta name="description" content="Reinvent page description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <meta name="theme-color" content="#000000">
    <link rel="manifest" href="{{ URL::asset('public/app/manifest.json') }}">
    <link href="{{ url('public/img/favicon.png') }}" rel="shortcut icon">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="{{ URL::asset('public/css/medium-editor.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/css/default.css') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ URL::asset('public/app/css/awesome-bootstrap-checkbox.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ URL::asset('public/app/css/normalize.min.css') }}">

    <link rel="stylesheet" href="{{ URL::asset('public/app/css/react-select.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/app/css/day-picker.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('public/app/css/react-confirm-alert.css') }}">
<!-- <link rel="stylesheet" href="{{ URL::asset('public/app/css/jquery.mCustomScrollbar.min') }}"> -->
    <link rel="stylesheet" href="{{ URL::asset('public/app/css/jquery.scrolling-tabs.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{url('style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('newsletter-style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('crm-style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('survey-style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('qualification-style.css')}}">
    <link rel="stylesheet" href="{{ URL::asset('public/app/css/media.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>

    <script type="text/javascript"
            src="{{ URL::asset('public/app/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <script type="text/javascript" src="{{ URL::asset('public/app/js/autosize.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('public/app/js/main.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('public/app/ckeditor/ckeditor.js') }}"></script>


        @if(Auth::check())
        <script type="text/javascript" src="{{ url('api/init-data') }}"></script>
    @else
        <script type="text/javascript">

            window.auth = {};
        </script>
    @endif
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAsJyvW0kWmU3kDMdD0C6q2z00nbhSycv4&libraries=places"></script>
    {{--    <link href=" {{ URL::asset('public/app/static/css/24.d8dabe1a.chunk.css') }}" rel="stylesheet">--}}
<!-- <link href=" {{ URL::asset('public/app/static/css/main.6e426ac7.chunk.css') }}" rel="stylesheet">  -->
    {{-- qualification <link href="{{ URL::asset('public/app/static/css/8.6a74b79d.chunk.css') }}" rel="stylesheet">--}}


    <script type="text/javascript">
                @php
                    $hostname = $_SERVER['REQUEST_SCHEME'].
                    '://'.$_SERVER['HTTP_HOST'];
                @endphp
        var HOST_NAME = '{{$hostname}}';
    </script>
    @if(Auth::check())
        <script type="text/javascript">
            /* Chameleon - better user onboarding */
            !function (t, n, o) {
                var a = "chmln",
                    e = "adminPreview",
                    c = "setup identify alias track clear set show on off custom help _data".split(" ");
                if (n[a] || (n[a] = {}), n[a][e] && (n[a][e] = !1), !n[a].root) {
                    n[a].accountToken = o, n[a].location = n.location.href.toString(), n[a].now = new Date;
                    for (var s = 0; s < c.length; s++) !function () {
                        var t = n[a][c[s] + "_a"] = [];
                        n[a][c[s]] = function () {
                            t.push(arguments)
                        }
                    }();
                    var i = t.createElement("script");
                    i.src = "https://fast.trychameleon.com/messo/" + o + "/messo.min.js", i.async = !0, t.head.appendChild(i)
                }
            }(document, window, "SOv5NHCDr5HK4Se1OA7J84hEOzJH0MbrGCviu9n8gIp67P-1HbD3P-BhTdNXWIu2H3Tld0");
            // **This is an example script, don't forget to change the PLACEHOLDERS.**
            // Please confirm the user properties to be sent with your project owner.
            let color1 = window.auth.graphic_setting.color1
            let color2 = window.auth.graphic_setting.color2
            let rgbaColor1 = `rgba(${color1.r},${color1.g},${color1.b},${color1.a})`;
            let rgbaColor2 = `rgba(${color2.r},${color2.g},${color2.b},${color2.a})`;

            function rgb2hex(rgb) {
                rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
                return (rgb && rgb.length === 4) ? "#" +
                    ("0" + parseInt(rgb[1], 10).toString(16)).slice(-2) +
                    ("0" + parseInt(rgb[2], 10).toString(16)).slice(-2) +
                    ("0" + parseInt(rgb[3], 10).toString(16)).slice(-2) : '';
            }

            var hexColor1 = rgb2hex(rgbaColor1);
            var hexColor2 = rgb2hex(rgbaColor2);
            var workshopName = null;
            var presidentFName = null;
            var presidentLName = null;
            var presidentEmail = null;
            window.onhashchange = function () {
                if (window.location.href.indexOf("commissions") > -1) {
                    var url = window.location.href;
                    var subStr = url.match("commissions(.*)/");
                    var b = subStr[1];
                    var a = b.split('/');
                    var id = a[1];
                    var workshopArray = window.auth.workshop;

                    var resultObject = search("id", workshopArray, id);
                    if (typeof resultObject != "undefined") {
                        workshopName = resultObject.workshop_name;
                        presidentFName = resultObject.president_fname;
                        presidentLName = resultObject.president_lname;
                        presidentEmail = resultObject.president_email;

                    }


                    window.chmln.identify(window.auth.auth.id, { // Unique ID in your database
                        workshopname: workshopName,
                        presidentename: presidentFName,
                        presidentlname: presidentLName,
                        presidentemail: presidentEmail,
                        // Add other pertinent parameters here
                    })


                }
            }

            function search(nameKey, myArray, id) {
                for (var i = 0; i < myArray.length; i++) {
                    if (myArray[i].id == id) {
                        return myArray[i];
                    }
                }
            }


            // Required:
            chmln.identify(window.auth.auth.id, { // Unique ID of each user in your database (e.g. 23443 or "590b80e5f433ea81b96c9bf6")
                email: window.auth.auth.email, // Put quotes around text strings (e.g. "jim@example.com")

                // Optional - additional user properties:
                created: window.auth.auth.created_at, // Send dates in ISO or unix timestamp format (e.g. "2017-07-01T03:21:10Z" or 1431432000)
                fname: window.auth.auth.fname, // We will parse this to extra first and surnames (e.g. "James Doe")
                lname: window.auth.auth.lname, // We will parse this to extra first and surnames (e.g. "James Doe")
                role: window.auth.auth.role, // Send properties useful for targeting types of users (e.g. "Admin")
                color1: hexColor1, // Send properties useful for targeting types of users (e.g. "Admin")
                color2: hexColor2, // Send properties useful for targeting types of users (e.g. "Admin")
                orgfname: window.auth.org_data.fname,
                orglname: window.auth.org_data.lname,
                orgemail: window.auth.org_data.email,
            });
        </script>
    @endif
    <style id="dynamicCss"></style>
    <style id="reinventCss"></style>

</head>

<body>
@php

        @endphp
<noscript>You need to enable JavaScript to run this app.</noscript>
<div id="root"></div>
<!--<script type="text/javascript" src="{{ URL::asset('public/app/static/js/main.6735444a.js') }}"></script>-->

<div class="modal fade popsonresize" id="screenResize" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <p id="full-screen">Veuillez restaurer la pleine largeur de votre écran de navigateur pour profiter de toutes les fonctionnalités.</p>
                <div class="clearfix text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:cc="http://creativecommons.org/ns#"
                         xmlns:dc="http://purl.org/dc/elements/1.1/"
                         xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
                         xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                         xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
                         xmlns:svg="http://www.w3.org/2000/svg" enable-background="new 0 0 32 32" height="32px"
                         id="svg2" version="1.1" viewBox="0 0 32 32" width="32px" xml:space="preserve">
                            <g id="background">
                                <rect fill="none" height="32" width="32"/>
                            </g>
                        <g id="fullscreen">
                            <path d="M20,8l8,8V8H20z M4,24h8l-8-8V24z"/>
                            <path d="M32,28V4H0v24h14v2H8v2h16v-2h-6v-2H32z M2,26V6h28v20H2z"/>
                        </g>
                        </svg>
                </div>
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
                <p id="ie-msg">

                    @if(session()->has('lang') && session()->get('lang')=='EN')
                        Please upgrade old IE11 to Microsoft Edge for better experience.
                    @else
                        Veuillez mettre à niveau l'ancien IE11 vers Microsoft Edge pour une meilleure expérience.
                    @endif
                </p>
                <div class="clearfix text-center">
                    <div class="clearfix mt-15 mb-15">
                        <img src={{URL::asset('public/img/upgradeIE11-image.png')}} class="center-block img-responsive"
                        />
                    </div>
                    <a href="https://www.microsoft.com/fr-fr/windows/microsoft-edge" class="underline-link"
                       target="_blank">  @if(session()->has('lang') && session()->get('lang')=='EN')
                            Upgrade link here
                        @else
                            Mettre à jour ici
                        @endif</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade popsonresize" id="screenResizeEdge" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="ie-msg">
                    @if(session()->has('lang') && session()->get('lang')=='EN')
                        Please upgrade old Microsoft Edge to Latest Microsoft Edge for better experience.
                    @else
                        Merci de mettre à jour votre ancienne version du navigateur Microsoft Edge pour la dernière
                        version pour optimiser votre expérience.
                    @endif
                </p>
                <div class="clearfix text-center">
                    <div class="clearfix mt-15 mb-15">
                        <img src={{URL::asset('public/img/upgradeIE11-image.png')}} class="center-block img-responsive"
                        />
                    </div>
                    <a href="https://www.microsoft.com/fr-fr/windows/microsoft-edge" class="underline-link"
                       target="_blank">
                        @if(session()->has('lang') && session()->get('lang')=='EN')
                            Upgrade link here
                        @else
                            Mettre à jour ici
                        @endif

                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- <div class="modal fade popsonresize" id="screenResizeIE" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="ie-msg">Please upgrade old IE11 to Microsoft Edge for better experience.</p>
                    <div class="clearfix text-center">
                        <div class="clearfix mt-15 mb-15">
                            <img src={{URL::asset('public/img/upgradeIE11-image.png')}} class="center-block img-responsive" />
                        </div>
                        <a href="https://www.microsoft.com/fr-fr/windows/microsoft-edge" class="underline-link" target="_blank">Upgrade link here</a>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
@php
    require_once(base_path('react.php'));
@endphp

{{-- Old build for backup --}}
{{-- <script>!function (f) { function e(e) { for (var t, r, n = e[0], o = e[1], a = e[2], c = 0, u = []; c < n.length; c++)r = n[c], d[r] && u.push(d[r][0]), d[r] = 0; for (t in o) Object.prototype.hasOwnProperty.call(o, t) && (f[t] = o[t]); for (p && p(e); u.length;)u.shift()(); return l.push.apply(l, a || []), i() } function i() { for (var e, t = 0; t < l.length; t++) { for (var r = l[t], n = !0, o = 1; o < r.length; o++) { var a = r[o]; 0 !== d[a] && (n = !1) } n && (l.splice(t--, 1), e = s(s.s = r[0])) } return e } var r = {}, u = { 20: 0 }, d = { 20: 0 }, l = []; function s(e) { if (r[e]) return r[e].exports; var t = r[e] = { i: e, l: !1, exports: {} }; return f[e].call(t.exports, t, t.exports, s), t.l = !0, t.exports } s.e = function (l) { var e = []; u[l] ? e.push(u[l]) : 0 !== u[l] && { 2: 1, 4: 1, 8: 1, 9: 1, 18: 1 }[l] && e.push(u[l] = new Promise(function (e, n) { for (var t = "static/css/" + ({}[l] || l) + "." + { 0: "31d6cfe0", 1: "31d6cfe0", 2: "b8296fba", 3: "31d6cfe0", 4: "b8b16f95", 5: "31d6cfe0", 7: "31d6cfe0", 8: "62593c25", 9: "462033e7", 10: "31d6cfe0", 11: "31d6cfe0", 12: "31d6cfe0", 13: "31d6cfe0", 14: "31d6cfe0", 16: "31d6cfe0", 17: "31d6cfe0", 18: "e69dff53", 19: "31d6cfe0" }[l] + ".chunk.css", o = s.p + t, r = document.getElementsByTagName("link"), a = 0; a < r.length; a++) { var c = (f = r[a]).getAttribute("data-href") || f.getAttribute("href"); if ("stylesheet" === f.rel && (c === t || c === o)) return e() } var u = document.getElementsByTagName("style"); for (a = 0; a < u.length; a++) { var f; if ((c = (f = u[a]).getAttribute("data-href")) === t || c === o) return e() } var i = document.createElement("link"); i.rel = "stylesheet", i.type = "text/css", i.onload = e, i.onerror = function (e) { var t = e && e.target && e.target.src || o, r = new Error("Loading CSS chunk " + l + " failed.\n(" + t + ")"); r.request = t, n(r) }, i.href = o, document.getElementsByTagName("head")[0].appendChild(i) }).then(function () { u[l] = 0 })); var r = d[l]; if (0 !== r) if (r) e.push(r[2]); else { var t = new Promise(function (e, t) { r = d[l] = [e, t] }); e.push(r[2] = t); var n, o = document.getElementsByTagName("head")[0], a = document.createElement("script"); a.charset = "utf-8", a.timeout = 120, s.nc && a.setAttribute("nonce", s.nc), a.src = s.p + "static/js/" + ({}[l] || l) + "." + { 0: "5aa48db3", 1: "8b3c31c4", 2: "9fb1b4e6", 3: "84a543a8", 4: "30c7b984", 5: "716b959d", 7: "caa0e2b6", 8: "b8efe4ee", 9: "d61e2950", 10: "514d6875", 11: "c05eeae6", 12: "5cc53653", 13: "aa9839f2", 14: "9c4e29b7", 16: "7d91b8fb", 17: "a12c17b1", 18: "c40d5415", 19: "337be381" }[l] + ".chunk.js", n = function (e) { a.onerror = a.onload = null, clearTimeout(c); var t = d[l]; if (0 !== t) { if (t) { var r = e && ("load" === e.type ? "missing" : e.type), n = e && e.target && e.target.src, o = new Error("Loading chunk " + l + " failed.\n(" + r + ": " + n + ")"); o.type = r, o.request = n, t[1](o) } d[l] = void 0 } }; var c = setTimeout(function () { n({ type: "timeout", target: a }) }, 12e4); a.onerror = a.onload = n, o.appendChild(a) } return Promise.all(e) }, s.m = f, s.c = r, s.d = function (e, t, r) { s.o(e, t) || Object.defineProperty(e, t, { enumerable: !0, get: r }) }, s.r = function (e) { "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, { value: "Module" }), Object.defineProperty(e, "__esModule", { value: !0 }) }, s.t = function (t, e) { if (1 & e && (t = s(t)), 8 & e) return t; if (4 & e && "object" == typeof t && t && t.__esModule) return t; var r = Object.create(null); if (s.r(r), Object.defineProperty(r, "default", { enumerable: !0, value: t }), 2 & e && "string" != typeof t) for (var n in t) s.d(r, n, function (e) { return t[e] }.bind(null, n)); return r }, s.n = function (e) { var t = e && e.__esModule ? function () { return e.default } : function () { return e }; return s.d(t, "a", t), t }, s.o = function (e, t) { return Object.prototype.hasOwnProperty.call(e, t) }, s.p = "/public/app/", s.oe = function (e) { throw console.error(e), e }; var t = window.webpackJsonp = window.webpackJsonp || [], n = t.push.bind(t); t.push = e, t = t.slice(); for (var o = 0; o < t.length; o++)e(t[o]); var p = n; i() }([])</script>
<script src="/public/app/static/js/15.baea8fc9.chunk.js"></script>
<script src="/public/app/static/js/main.9bbb50eb.chunk.js"></script>--}}
<script>
    var _rollbarConfig = {
        // accessToken: "",
        accessToken: "d8d7ee3efc464ca1a32d106bbf510535",

        captureUncaught: true,
        captureUnhandledRejections: true,
        payload: {
            environment: '{{env("APP_ENV")}}'
        }
    };
    // Set the person data to be sent with all errors for this notifier.

    // Rollbar Snippet
    !function (r) {
        function e(n) {
            if (o[n]) return o[n].exports;
            var t = o[n] = {
                exports: {},
                id: n,
                loaded: !1
            };
            return r[n].call(t.exports, t, t.exports, e), t.loaded = !0, t.exports
        }

        var o = {};
        return e.m = r, e.c = o, e.p = "", e(0)
    }([function (r, e, o) {
        "use strict";
        var n = o(1),
            t = o(4);
        _rollbarConfig = _rollbarConfig || {}, _rollbarConfig.rollbarJsUrl = _rollbarConfig.rollbarJsUrl || "https://cdnjs.cloudflare.com/ajax/libs/rollbar.js/2.4.6/rollbar.min.js", _rollbarConfig.async = void 0 === _rollbarConfig.async || _rollbarConfig.async;
        var a = n.setupShim(window, _rollbarConfig),
            l = t(_rollbarConfig);
        window.rollbar = n.Rollbar, a.loadFull(window, document, !_rollbarConfig.async, _rollbarConfig, l)
    }, function (r, e, o) {
        "use strict";

        function n(r) {
            return function () {
                try {
                    return r.apply(this, arguments)
                } catch (r) {
                    try {
                        console.error("[Rollbar]: Internal error", r)
                    } catch (r) {
                    }
                }
            }
        }

        function t(r, e) {
            this.options = r, this._rollbarOldOnError = null;
            var o = s++;
            this.shimId = function () {
                return o
            }, "undefined" != typeof window && window._rollbarShims && (window._rollbarShims[o] = {
                handler: e,
                messages: []
            })
        }

        function a(r, e) {
            if (r) {
                var o = e.globalAlias || "Rollbar";
                if ("object" == typeof r[o]) return r[o];
                r._rollbarShims = {}, r._rollbarWrappedError = null;
                var t = new p(e);
                return n(function () {
                    e.captureUncaught && (t._rollbarOldOnError = r.onerror, i.captureUncaughtExceptions(r, t, !0), i.wrapGlobals(r, t, !0)), e.captureUnhandledRejections && i.captureUnhandledRejections(r, t, !0);
                    var n = e.autoInstrument;
                    return e.enabled !== !1 && (void 0 === n || n === !0 || "object" == typeof n && n.network) && r.addEventListener && (r.addEventListener("load", t.captureLoad.bind(t)), r.addEventListener("DOMContentLoaded", t.captureDomContentLoaded.bind(t))), r[o] = t, t
                })()
            }
        }

        function l(r) {
            return n(function () {
                var e = this,
                    o = Array.prototype.slice.call(arguments, 0),
                    n = {
                        shim: e,
                        method: r,
                        args: o,
                        ts: new Date
                    };
                window._rollbarShims[this.shimId()].messages.push(n)
            })
        }

        var i = o(2),
            s = 0,
            d = o(3),
            c = function (r, e) {
                return new t(r, e)
            },
            p = d.bind(null, c);
        t.prototype.loadFull = function (r, e, o, t, a) {
            var l = function () {
                    var e;
                    if (void 0 === r._rollbarDidLoad) {
                        e = new Error("rollbar.js did not load");
                        for (var o, n, t, l, i = 0; o = r._rollbarShims[i++];)
                            for (o = o.messages || []; n = o.shift();)
                                for (t = n.args || [], i = 0; i < t.length; ++i)
                                    if (l = t[i], "function" == typeof l) {
                                        l(e);
                                        break
                                    }
                    }
                    "function" == typeof a && a(e)
                },
                i = !1,
                s = e.createElement("script"),
                d = e.getElementsByTagName("script")[0],
                c = d.parentNode;
            s.crossOrigin = "", s.src = t.rollbarJsUrl, o || (s.async = !0), s.onload = s.onreadystatechange = n(function () {
                if (!(i || this.readyState && "loaded" !== this.readyState && "complete" !== this.readyState)) {
                    s.onload = s.onreadystatechange = null;
                    try {
                        c.removeChild(s)
                    } catch (r) {
                    }
                    i = !0, l()
                }
            }), c.insertBefore(s, d)
        }, t.prototype.wrap = function (r, e, o) {
            try {
                var n;
                if (n = "function" == typeof e ? e : function () {
                    return e || {}
                }, "function" != typeof r) return r;
                if (r._isWrap) return r;
                if (!r._rollbar_wrapped && (r._rollbar_wrapped = function () {
                    o && "function" == typeof o && o.apply(this, arguments);
                    try {
                        return r.apply(this, arguments)
                    } catch (o) {
                        var e = o;
                        throw e && ("string" == typeof e && (e = new String(e)), e._rollbarContext = n() || {}, e._rollbarContext._wrappedSource = r.toString(), window._rollbarWrappedError = e), e
                    }
                }, r._rollbar_wrapped._isWrap = !0, r.hasOwnProperty))
                    for (var t in r) r.hasOwnProperty(t) && (r._rollbar_wrapped[t] = r[t]);
                return r._rollbar_wrapped
            } catch (e) {
                return r
            }
        };
        for (var u = "log,debug,info,warn,warning,error,critical,global,configure,handleUncaughtException,handleUnhandledRejection,captureEvent,captureDomContentLoaded,captureLoad".split(","), f = 0; f < u.length; ++f) t.prototype[u[f]] = l(u[f]);
        r.exports = {
            setupShim: a,
            Rollbar: p
        }
    }, function (r, e) {
        "use strict";

        function o(r, e, o) {
            if (r) {
                var t;
                "function" == typeof e._rollbarOldOnError ? t = e._rollbarOldOnError : r.onerror && !r.onerror.belongsToShim && (t = r.onerror, e._rollbarOldOnError = t);
                var a = function () {
                    var o = Array.prototype.slice.call(arguments, 0);
                    n(r, e, t, o)
                };
                a.belongsToShim = o, r.onerror = a
            }
        }

        function n(r, e, o, n) {
            r._rollbarWrappedError && (n[4] || (n[4] = r._rollbarWrappedError), n[5] || (n[5] = r._rollbarWrappedError._rollbarContext), r._rollbarWrappedError = null), e.handleUncaughtException.apply(e, n), o && o.apply(r, n)
        }

        function t(r, e, o) {
            if (r) {
                "function" == typeof r._rollbarURH && r._rollbarURH.belongsToShim && r.removeEventListener("unhandledrejection", r._rollbarURH);
                var n = function (r) {
                    var o, n, t;
                    try {
                        o = r.reason
                    } catch (r) {
                        o = void 0
                    }
                    try {
                        n = r.promise
                    } catch (r) {
                        n = "[unhandledrejection] error getting `promise` from event"
                    }
                    try {
                        t = r.detail, !o && t && (o = t.reason, n = t.promise)
                    } catch (r) {
                        t = "[unhandledrejection] error getting `detail` from event"
                    }
                    o || (o = "[unhandledrejection] error getting `reason` from event"), e && e.handleUnhandledRejection && e.handleUnhandledRejection(o, n)
                };
                n.belongsToShim = o, r._rollbarURH = n, r.addEventListener("unhandledrejection", n)
            }
        }

        function a(r, e, o) {
            if (r) {
                var n, t,
                    a = "EventTarget,Window,Node,ApplicationCache,AudioTrackList,ChannelMergerNode,CryptoOperation,EventSource,FileReader,HTMLUnknownElement,IDBDatabase,IDBRequest,IDBTransaction,KeyOperation,MediaController,MessagePort,ModalWindow,Notification,SVGElementInstance,Screen,TextTrack,TextTrackCue,TextTrackList,WebSocket,WebSocketWorker,Worker,XMLHttpRequest,XMLHttpRequestEventTarget,XMLHttpRequestUpload".split(",");
                for (n = 0; n < a.length; ++n) t = a[n], r[t] && r[t].prototype && l(e, r[t].prototype, o)
            }
        }

        function l(r, e, o) {
            if (e.hasOwnProperty && e.hasOwnProperty("addEventListener")) {
                for (var n = e.addEventListener; n._rollbarOldAdd && n.belongsToShim;) n = n._rollbarOldAdd;
                var t = function (e, o, t) {
                    n.call(this, e, r.wrap(o), t)
                };
                t._rollbarOldAdd = n, t.belongsToShim = o, e.addEventListener = t;
                for (var a = e.removeEventListener; a._rollbarOldRemove && a.belongsToShim;) a = a._rollbarOldRemove;
                var l = function (r, e, o) {
                    a.call(this, r, e && e._rollbar_wrapped || e, o)
                };
                l._rollbarOldRemove = a, l.belongsToShim = o, e.removeEventListener = l
            }
        }

        r.exports = {
            captureUncaughtExceptions: o,
            captureUnhandledRejections: t,
            wrapGlobals: a
        }
    }, function (r, e) {
        "use strict";

        function o(r, e) {
            this.impl = r(e, this), this.options = e, n(o.prototype)
        }

        function n(r) {
            for (var e = function (r) {
                return function () {
                    var e = Array.prototype.slice.call(arguments, 0);
                    if (this.impl[r]) return this.impl[r].apply(this.impl, e)
                }
            }, o = "log,debug,info,warn,warning,error,critical,global,configure,handleUncaughtException,handleUnhandledRejection,_createItem,wrap,loadFull,shimId,captureEvent,captureDomContentLoaded,captureLoad".split(","), n = 0; n < o.length; n++) r[o[n]] = e(o[n])
        }

        o.prototype._swapAndProcessMessages = function (r, e) {
            this.impl = r(this.options);
            for (var o, n, t; o = e.shift();) n = o.method, t = o.args, this[n] && "function" == typeof this[n] && ("captureDomContentLoaded" === n || "captureLoad" === n ? this[n].apply(this, [t[0], o.ts]) : this[n].apply(this, t));
            return this
        }, r.exports = o
    }, function (r, e) {
        "use strict";
        r.exports = function (r) {
            return function (e) {
                if (!e && !window._rollbarInitialized) {
                    r = r || {};
                    for (var o, n, t = r.globalAlias || "Rollbar", a = window.rollbar, l = function (r) {
                        return new a(r)
                    }, i = 0; o = window._rollbarShims[i++];) n || (n = o.handler), o.handler._swapAndProcessMessages(l, o.messages);
                    window[t] = n, window._rollbarInitialized = !0
                }
            }
        }
    }]);
    // End Rollbar Snippet
</script>


</body>
<script type="text/javascript" src="{{ URL::asset('public/js/jquery.scrolling-tabs.min.js') }}"></script>

<script>
    function getMobileOperatingSystem() {
        var t = navigator.userAgent || navigator.vendor || window.opera;
        return /windows phone/i.test(t) ? "Windows Phone" : /android/i.test(t) ? "Android" : /iPad|iPhone|iPod/.test(t) && !window.MSStream ? "iOS" : "unknown"
    }

    $(window).load(function () {
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
        console.log(navigator.userAgent.search("Edg"), navigator.browserSpecs.version);

        if (navigator.browserSpecs.name == 'IE') {
            // Do something for Firefox.
            if (navigator.browserSpecs.version <= 11) {
                $('#screenResizeIE').modal('show');
                // Do something for Firefox versions greater than 42.
            }

        } else if (navigator.userAgent.search("Edg") > 0) {
            // Do something for Firefox.
            if (navigator.browserSpecs.version <= 80) {
                $('#screenResizeEdge').modal('show');
                // Do something for Firefox versions greater than 42.
            }
        }

        if (getMobileOperatingSystem() == 'unknown') {
            if ($(window).width() < 1200) {

                $('#screenResize').modal('show');
            }
        }

    })
    $(window).resize(function () {
        if (getMobileOperatingSystem() == 'unknown') {
            if ($(window).width() < 1200) {
                $('#screenResize').modal('show');
            } else {
                $('#screenResize').modal('hide');
            }
        }
    })
    $(".nav-tabs").scrollingTabs(), $('[data-tooltip-toggle="tooltip"]').tooltip("show"), $('[tooltip-toggle="tooltip"]').tooltip({
        placement: "bottom"
    }), $(document).on("mouseenter", ".milestone-heading", function () {
        this.offsetWidth < this.scrollWidth && !$(this).attr("title") && ($(this).tooltip({
            title: $(this).text(),
            placement: "bottom"
        }), $(e.target).tooltip("show"))
    }), $(".proj-card .left").on("click", function (t) {
        t.preventDefault(), $(this).parents(".proj-card").toggleClass("active")
    }), $(".comment-btn").on("click", function () {
        $(".hideshow-parentdiv").children("#comments-block").siblings().hide(), $("#comments-block").toggle()
    }), $(".selected-task-icon").on("click", function () {
        $(".hideshow-parentdiv").children("#task-change").siblings().hide(), $("#task-change").toggle()
    }), $(".attech-linkfile-btn").on("click", function () {
        $(".hideshow-parentdiv").children("#doc-link-block").siblings().hide(), $("#doc-link-block").toggle()
    }), $(".depend-on-task").on("click", function () {
        $(".hideshow-parentdiv").children("#task-dependon-task").siblings().hide(), $("#task-dependon-task").toggle()
    }), $(".depend-on-multitask").on("click", function () {
        $(".hideshow-parentdiv").children("#task-dependon-multitask").siblings().hide(), $("#task-dependon-multitask").toggle()
    }), $("#ownerchangebtn").on("click", function (t) {
        $(".hideshow-parentdiv").children("#ownerchange").siblings().hide(), $("#ownerchange").toggle()
    })
    @if(Auth::check())
    $(document).ready(function () {
        if (window.auth.auth.setting != undefined) {
            var lang = JSON.parse(window.auth.auth.setting)
            if (lang.lang == 'EN') {
                $("#full-screen").text("Please restore full width of your screen for better experience.");
                // $("#ie-msg").text("")

            }
        }
    })
    @endif
</script>

</html>
</body>

</html>
{{--
@endif--}}
