<!DOCTYPE html>

<html lang="en-US">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="ThemeStarz">
    <meta name="description" content="OP CRM est le CRM pour organisations professionnelles développé par les dirigeants d’organisations professionnelles pour les dirigeants d’organisations professionnelles, en utilisant la méthode Design Thinking utilisée par Google pour rendre simple toutes ses applications.">
    <meta name="keywords" content="CRM, organisations professionnelles, CRM pour organisations professionnelles, logiciel pour organisations professionnelles, plateforme collaborative pour organisations professionnelles">

    <link href="{{ URL::asset('public/launch/assets/fonts/font-awesome.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('public/launch/assets/fonts/elegant-fonts.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="{{  URL::asset('public/launch/assets/bootstrap/css/bootstrap.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ URL::asset('public/launch/assets/css/owl.carousel.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ URL::asset('public/launch/assets/css/magnific-popup.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ URL::asset('public/launch/assets/css/trackpad-scroll-emulator.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ URL::asset('public/launch/assets/css/style.css') }}" type="text/css">

    <title>OP CRM est le CRM pour organisations professionnelles développé par les dirigeants d’organisations professionnelles pour les dirigeants d’organisations professionnelles.</title>

</head>

<body class=" frame">
       
@yield('content')


    
<script type="text/javascript" src="{{ URL::asset('public/launch/assets/js/jquery-2.2.1.min.js') }}"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js"></script>
<script type="text/javascript" src="{{ URL::asset('public/launch/assets/bootstrap/js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('public/launch/assets/js/jquery.validate.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('public/launch/assets/js/owl.carousel.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('public/launch/assets/js/jquery.magnific-popup.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('public/launch/assets/js/jquery.trackpad-scroll-emulator.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('public/launch/assets/js/custom.js') }}"></script>

<script type="text/javascript">
    var latitude = 34.038405;
    var longitude = -117.946944;
    var markerImage = "{{ URL::asset('public/launch/assets/img/map-marker-w.png') }}";
    var mapTheme = "dark";
    var mapElement = "map-contact";
    google.maps.event.addDomListener(window, 'load', simpleMap(latitude, longitude, markerImage, mapTheme, mapElement));
</script>


</body>
</html>