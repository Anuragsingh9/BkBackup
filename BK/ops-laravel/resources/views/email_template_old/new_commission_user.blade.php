<!DOCTYPE html>
<html>
<head>
    <title>OP Simplify</title>
     <link href="{{ URL::asset('public/css/style.css')}}" rel="stylesheet" type="text/css">
</head>
<body>
    @php
        $settings['data'] = getEmailSetting(['email_graphic','commission_new_user']);

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
        '[[PresidentPhone]]',
        '[[OrgName]]',
        '[[OrgShortName]]'
        ];
    $values =[
    '',
    '',
    '',
    $mail['workshop_data']->workshop_name,
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    $orgDetail->org_name,
    $orgDetail->acronym
    ];
    @endphp
    @include('email_template.header',$settings)
        <tbody>
        <tr><td>{!! utf8_decode(str_replace($keywords,$values,$settings['data'][1]->text_before_link)) !!}</td></tr>
        <tr><td><a href="{{url($mail['url'])}}">Cliquez ici.</a></td></tr>
        <tr><td>{!! str_replace($keywords,$values,$settings['data'][1]->text_after_link) !!}</td></tr>
        </tbody>
    @include('email_template.footer',$settings)
</body>
</html>