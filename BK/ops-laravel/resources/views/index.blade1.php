@if(Auth::check())
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>OP Simplify</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
        <meta name="theme-color" content="#000000">
        <link rel="manifest" href="{{ URL::asset('public/app/manifest.json') }}">
        <link href="{{ url('public/img/favicon.png') }}" rel="shortcut icon">
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="{{ URL::asset('public/app/css/awesome-bootstrap-checkbox.css') }}">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ URL::asset('public/app/css/normalize.min.css') }}">
        <!-- <link rel="stylesheet" href="{{ URL::asset('public/app/css/jquery.mCustomScrollbar.min') }}"> -->
        <link rel="stylesheet" href="{{ URL::asset('public/app/css/jquery.scrolling-tabs.min.css') }}">
        
        <link rel="stylesheet" type="text/css" href="style.css"> 
        <link rel="stylesheet" href="{{ URL::asset('public/app/css/media.css') }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
        <script type="text/javascript" src="{{ URL::asset('public/app/js/jquery.min.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('public/app/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script type="text/javascript" src="{{ URL::asset('public/app/js/bootstrap.min.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('public/app/js/autosize.min.js') }}"></script>
        
        <script type="text/javascript" src="{{ URL::asset('public/app/js/main.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('public/app/ckeditor/ckeditor.js') }}"></script>
        <script type="text/javascript" src="api/init-data"></script>
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAsJyvW0kWmU3kDMdD0C6q2z00nbhSycv4&libraries=places"></script>
         <link href="{{ URL::asset('public/app/static/css/4.37f9cf0b.chunk.css') }}" rel="stylesheet">
      
       <script type="text/javascript">
           @php
            $hostname = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
           @endphp
           var HOST_NAME = '{{$hostname}}';
           
       </script>
    
    </head>
    <body>
        
        <noscript>You need to enable JavaScript to run this app.</noscript>
        <div id="root"></div>
        <!--<script type="text/javascript" src="{{ URL::asset('public/app/static/js/main.6735444a.js') }}"></script>-->
        <div class="modal fade" id="screenResize" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button></div>
                    <div class="modal-body">
                        <p>Please restore full width of your screen for better experience.</p>
                        <div class="clearfix text-center"><svg xmlns="http://www.w3.org/2000/svg" xmlns:cc="http://creativecommons.org/ns#"
                                xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
                                xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
                                xmlns:svg="http://www.w3.org/2000/svg" enable-background="new 0 0 32 32" height="32px" id="svg2"
                                version="1.1" viewBox="0 0 32 32" width="32px" xml:space="preserve">
                                <g id="background">
                                    <rect fill="none" height="32" width="32" />
                                </g>
                                <g id="fullscreen">
                                    <path d="M20,8l8,8V8H20z M4,24h8l-8-8V24z" />
                                    <path d="M32,28V4H0v24h14v2H8v2h16v-2h-6v-2H32z M2,26V6h28v20H2z" />
                                </g>
                            </svg></div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var cssPath="{{ URL::asset('public/app/static/css') }}"
            var jsPath="{{ URL::asset('public/app/static/js') }}"

        </script>
                                                         <script>!function (i) { function e(e) { for (var t, r, n = e[0], o = e[1], a = e[2], u = 0, c = []; u < n.length; u++)r = n[u], s[r] && c.push(s[r][0]), s[r] = 0; for (t in o) Object.prototype.hasOwnProperty.call(o, t) && (i[t] = o[t]); for (p && p(e); c.length;)c.shift()(); return l.push.apply(l, a || []), f() } function f() { for (var e, t = 0; t < l.length; t++) { for (var r = l[t], n = !0, o = 1; o < r.length; o++) { var a = r[o]; 0 !== s[a] && (n = !1) } n && (l.splice(t--, 1), e = d(d.s = r[0])) } return e } var r = {}, c = { 7: 0 }, s = { 7: 0 }, l = []; function d(e) { if (r[e]) return r[e].exports; var t = r[e] = { i: e, l: !1, exports: {} }; return i[e].call(t.exports, t, t.exports, d), t.l = !0, t.exports } d.e = function (l) { var e = []; c[l] ? e.push(c[l]) : 0 !== c[l] && { 5: 1, 6: 1 }[l] && e.push(c[l] = new Promise(function (e, n) { for (var t = "public/app/static/css/" + ({}[l] || l) + "." + { 1: "31d6cfe0", 2: "31d6cfe0", 3: "31d6cfe0", 5: "b45cec81", 6: "71dc1f8f" }[l] + ".chunk.css", o = d.p + t, r = document.getElementsByTagName("link"), a = 0; a < r.length; a++) { var u = (i = r[a]).getAttribute("data-href") || i.getAttribute("href"); if ("stylesheet" === i.rel && (u === t || u === o)) return e() } var c = document.getElementsByTagName("style"); for (a = 0; a < c.length; a++) { var i; if ((u = (i = c[a]).getAttribute("data-href")) === t || u === o) return e() } var f = document.createElement("link"); f.rel = "stylesheet", f.type = "text/css", f.onload = e, f.onerror = function (e) { var t = e && e.target && e.target.src || o, r = new Error("Loading CSS chunk " + l + " failed.\n(" + t + ")"); r.request = t, n(r) }, f.href = o, document.getElementsByTagName("head")[0].appendChild(f) }).then(function () { c[l] = 0 })); var r = s[l]; if (0 !== r) if (r) e.push(r[2]); else { var t = new Promise(function (e, t) { r = s[l] = [e, t] }); e.push(r[2] = t); var n, o = document.getElementsByTagName("head")[0], a = document.createElement("script"); a.charset = "utf-8", a.timeout = 120, d.nc && a.setAttribute("nonce", d.nc), a.src = d.p + "public/app/static/js/" + ({}[l] || l) + "." + { 1: "cfa6ccc9", 2: "1f5f18a0", 3: "e707cc44", 5: "ac40a30a", 6: "36f76215" }[l] + ".chunk.js", n = function (e) { a.onerror = a.onload = null, clearTimeout(u); var t = s[l]; if (0 !== t) { if (t) { var r = e && ("load" === e.type ? "missing" : e.type), n = e && e.target && e.target.src, o = new Error("Loading chunk " + l + " failed.\n(" + r + ": " + n + ")"); o.type = r, o.request = n, t[1](o) } s[l] = void 0 } }; var u = setTimeout(function () { n({ type: "timeout", target: a }) }, 12e4); a.onerror = a.onload = n, o.appendChild(a) } return Promise.all(e) }, d.m = i, d.c = r, d.d = function (e, t, r) { d.o(e, t) || Object.defineProperty(e, t, { enumerable: !0, get: r }) }, d.r = function (e) { "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, { value: "Module" }), Object.defineProperty(e, "__esModule", { value: !0 }) }, d.t = function (t, e) { if (1 & e && (t = d(t)), 8 & e) return t; if (4 & e && "object" == typeof t && t && t.__esModule) return t; var r = Object.create(null); if (d.r(r), Object.defineProperty(r, "default", { enumerable: !0, value: t }), 2 & e && "string" != typeof t) for (var n in t) d.d(r, n, function (e) { return t[e] }.bind(null, n)); return r }, d.n = function (e) { var t = e && e.__esModule ? function () { return e.default } : function () { return e }; return d.d(t, "a", t), t }, d.o = function (e, t) { return Object.prototype.hasOwnProperty.call(e, t) }, d.p = "/", d.oe = function (e) { throw console.error(e), e }; var t = window.webpackJsonp = window.webpackJsonp || [], n = t.push.bind(t); t.push = e, t = t.slice(); for (var o = 0; o < t.length; o++)e(t[o]); var p = n; f() }([])</script>


<script src="{{ URL::asset('public/app/static/js/4.3eb38524.chunk.js') }}"></script>
            <script src="{{ URL::asset('public/app/static/js/main.1cd09cdf.chunk.js') }}"></script>
</body>
         <script type="text/javascript" src="{{ URL::asset('public/js/jquery.scrolling-tabs.min.js') }}"></script>

<script>function getMobileOperatingSystem() { var t = navigator.userAgent || navigator.vendor || window.opera; return /windows phone/i.test(t) ? "Windows Phone" : /android/i.test(t) ? "Android" : /iPad|iPhone|iPod/.test(t) && !window.MSStream ? "iOS" : "unknown" } $(window).load(function () { $(window).width() < 1200 && "unknown" == getMobileOperatingSystem() && $("#screenResize").modal("show") }), $(window).resize(function () { $(window).width() < 1200 ? "unknown" == getMobileOperatingSystem() && $("#screenResize").modal("show") : $("#screenResize").modal("hide") }), $(".nav-tabs").scrollingTabs(), $('[data-tooltip-toggle="tooltip"]').tooltip("show"), $('[tooltip-toggle="tooltip"]').tooltip({ placement: "bottom" }), $(document).on("mouseenter", ".milestone-heading", function () { this.offsetWidth < this.scrollWidth && !$(this).attr("title") && ($(this).tooltip({ title: $(this).text(), placement: "bottom" }), $(e.target).tooltip("show")) }), $(".proj-card .left").on("click", function (t) { t.preventDefault(), $(this).parents(".proj-card").toggleClass("active") }), $(".comment-btn").on("click", function () { $(".hideshow-parentdiv").children("#comments-block").siblings().hide(), $("#comments-block").toggle() }), $(".selected-task-icon").on("click", function () { $(".hideshow-parentdiv").children("#task-change").siblings().hide(), $("#task-change").toggle() }), $(".attech-linkfile-btn").on("click", function () { $(".hideshow-parentdiv").children("#doc-link-block").siblings().hide(), $("#doc-link-block").toggle() }), $(".depend-on-task").on("click", function () { $(".hideshow-parentdiv").children("#task-dependon-task").siblings().hide(), $("#task-dependon-task").toggle() }), $(".depend-on-multitask").on("click", function () { $(".hideshow-parentdiv").children("#task-dependon-multitask").siblings().hide(), $("#task-dependon-multitask").toggle() }), $("#ownerchangebtn").on("click", function (t) { $(".hideshow-parentdiv").children("#ownerchange").siblings().hide(), $("#ownerchange").toggle() })</script>

</html>
    </body>
    </html>
@endif

    
    
