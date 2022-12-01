<html lang="{{app()->getLocale()}}" class="h-100">
<head>
    <title>HumannConnect - Sign Up</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" id="mainIcon" href="{{asset("img/favicon.ico")}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset("img/favicon32x32.png")}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset("img/favicon16x16.png")}}">

    <link href="//cdn.muicss.com/mui-0.10.3/css/mui.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.0/mdb.dark.min.css"
          integrity="sha512-NxaAbVXIFKVQRbsaSlg55ZKsdja0n2qKUkjMHZp752MLYfCAsJJ9u7krsiAH0M633Sca/Z8avwHZuTDaBBw/Eg=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.0/mdb.dark.rtl.min.css"
          integrity="sha512-eGSj5YG9KXREpUrMHYgLfpDkL3HIAz2/elKatlGQ94v0hUjlIp04dG/7YyS96QEmyZ4T3SJln2zuMB5nKntM9w=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.0/mdb.min.css"
          integrity="sha512-1w5xHVkcpTU02l7gesIqNxruTgWn3RtxbFUfgFARVYNgNMx17zBrOusNzVhuC/GoXFTMk7x2dXLP1FBye2qBfA=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.0/mdb.rtl.min.css"
          integrity="sha512-X+Fk/tn01CsBDsZEz8IIqXePQXvN76NDQDSGya/DGbj2PTpnvAPDovASSE4FQIVWhsBjGB+s22o1ykfEc2L9ZQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.0/mdb.min.js"
            integrity="sha512-4liRR9IojCqXKpXTVTt/Yot3/ijsi9DbVde39JpMx1WuZhTHjHCgQNQL24BHpTw3BYahpAp0y+Q9K+E08Ja7qA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="stylesheet" href="{{asset('fontawesome/all.css')}}">
    {{--    <link href="{{asset('css/all.css')}}" rel="stylesheet">--}}
{{--    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"--}}
{{--          integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>--}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"
            integrity="sha512-37T7leoNS06R80c8Ulq7cdCDU5MNQBwlYoy1TX/WUsLFC2eYNqtKlV0QjH7r8JpG/S0GUMZwebnVFLPd6SU5yg=="
            crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>
</head>
<body class="d-flex flex-column h-100">
@component('superadmin::components.header')@endcomponent
@yield('content')
@component('superadmin::components.footer')@endcomponent
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>
</html>
