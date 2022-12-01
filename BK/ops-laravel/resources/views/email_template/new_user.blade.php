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
    if(isset($mail['workshop_data'])){
    $member=workshopValidatorPresident($mail['workshop_data']);
    }

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
    '[[PresidentPhone]]',
    ];
    $values =[
    '',
    '',
    '',
    isset($mail['workshop_data']->workshop_name)?$mail['workshop_data']->workshop_name:'',
    isset($mail['workshop_data']->code1)?$mail['workshop_data']->code1:'',
    isset($member['p']['fullname'])?$member['p']['fullname']:'',
    isset($member['v']['fullname'])?$member['v']['fullname']:'',
    '',
    '',
    '',
    '',
     isset($member['v']['email'])?$member['v']['email']:'',
     isset($member['p']['email'])?$member['p']['email']:'',
    $orgDetail->name_org,
    $orgDetail->acronym,
    isset($member['p']['phone'])?$member['p']['phone']:'',
    ];
@endphp
@include('email_template.header',$settings)
<tbody>
<tr>
    <td>{!! (str_replace($keywords,$values,$settings['data'][1]->text_before_link)) !!}</td>
</tr>
<tr>
    <td><a href="{{url($mail['url'])}}">cliquant ici.</a></td>
</tr>
<tr>
    <td>{!! (str_replace($keywords,$values,$settings['data'][1]->text_after_link)) !!}</td>
</tr>
<tr>
    <td>{!! nl2br(str_replace($keywords,$values,$settings['data'][0]->email_sign)) !!}</td>
</tr>
</tbody>
@include('email_template.footer',$settings)
</body>
</html>