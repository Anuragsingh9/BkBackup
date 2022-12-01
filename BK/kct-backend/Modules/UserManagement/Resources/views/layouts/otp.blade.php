<!DOCTYPE html>
<html lang="{{ \Illuminate\Support\Facades\App::getLocale() }}">
<head>
    <title> {{ env("APP_NAME") }}</title>
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
</head>
<body>
@component('usermanagement::components.email.otp-header')@endcomponent
@yield('content')
@component('usermanagement::components.email.otp-footer')@endcomponent
</body>
</html>
