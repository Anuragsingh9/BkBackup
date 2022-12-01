    @php 

    $settings['data'] = getEmailSetting(['email_graphic','msg_replies_email_setting']);
   
@endphp
    @include('email_template.header',$settings)
    <tbody>
        <tr><td>{{$mail['message_text']}}</td></tr>
    </tbody>
    @include('email_template.footer',$settings)
