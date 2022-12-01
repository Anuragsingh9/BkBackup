<!DOCTYPE html>
<html>
<head>
    <title> {{ env("APP_NAME") }}</title>
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
</head>
<body>
<table width="600px">
    <thead>
    <tr>
        <td>{{ __('superadmin::messages.forgot_pwd_description') }},</td>
    </tr>
    </thead>

    <tbody>
    <tr>
        <a href="{{$link}}">Click Here</a>
    </tr>
    </tbody>

</table>
</body>
</html>

