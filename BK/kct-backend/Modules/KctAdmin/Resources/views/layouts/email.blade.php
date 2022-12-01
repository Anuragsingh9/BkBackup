<!DOCTYPE html>
<html lang="{{ \Illuminate\Support\Facades\App::getLocale() }}">
<head>
    <title> {{ env("APP_NAME") }}</title>
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
</head>
<body>
@component('kctadmin::components.email.email-header')@endcomponent
@yield('content')
@component('kctadmin::components.email.email-footer')@endcomponent
</body>
</html>
