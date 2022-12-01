@php

    $settings['data'] = getEmailSetting(['email_graphic','msg_email_setting']);

@endphp
    @include('email_template.header',$settings)
        <tbody>
        <tr><td><strong>{{$mail['name']}}</strong></td></tr>
        <tr><td>{{$mail['role']}}</td></tr>
        <tr><td>{{$mail['msg']}}</td></tr>

        </tbody>
    @include('email_template.footer',$settings)
