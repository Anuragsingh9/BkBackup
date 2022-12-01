<!DOCTYPE html>
<html>
    <head>
        <title>OP Simplify</title>
        <link href="{{ URL::asset('public/css/style.css')}}" rel="stylesheet" type="text/css">
    </head>
    <body>
        @php
        $settings['data'] = getEmailSetting(['email_graphic','user_email_setting']);
        $orgDetail=getOrgDetail();
        $keywords =[
        '[[UserFirstName]]',
        '[[UserLastName]]',
        '[[UserEmail]]',
        '[[WorkshopLongName]]',
        '[[WorkshopShortName]]',
        '[[WorkshopPresidentFullName]]',
        '[[WorkshopvalidatorFullName]]',
        '[[WorkshopMeetingName]]',
        '[[WorkshopMeetingDate]]',
        '[[WorkshopMeetingTime]]',
        '[[WorkshopMeetingAddress]]',
        '[[ValidatorEmail]]',
        '[[PresidentEmail]]',
        '[[OrgName]]',
        '[[OrgShortName]]',
        '[[ValidatorEmail]]',
        '[[PresidentEmail]]',
        '[[PresidentPhone]]',
        ];
        $values =[
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        $orgDetail->name_org,
        $orgDetail->acronym,
        '',
        '',
        '',
        ];
        @endphp
        @include('email_template.header',$settings)
    <tbody>
        <tr><td>{!! (str_replace($keywords,$values,$settings['data'][1]->text_before_link)) !!}</td></tr>
        <tr><td>Vous pouvez vous connecter en <a href="{{url($mail['url'])}}">cliquant ici.</a></td></tr>
        <tr><td>{!! str_replace($keywords,$values,$settings['data'][1]->text_after_link) !!}</td></tr>
        <tr><td>{!! nl2br(str_replace($keywords,$values,$settings['data'][0]->email_sign)) !!}</td></tr>
    </tbody>
    @include('email_template.footer',$settings)
</body>
</html>