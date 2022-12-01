<!DOCTYPE html>
<html lang="{{ \Illuminate\Support\Facades\App::getLocale() }}">
<head>
    <title> {{ env("APP_NAME") }}</title>
</head>
<body style="width: 600px; background-color: #F8F9F9;font-family: sans-serif;font-size: 0.9rem;">
@component('kctuser::components.otp-header')@endcomponent
@yield('content')
@component('kctuser::components.otp-footer')@endcomponent
</body>
</html>
