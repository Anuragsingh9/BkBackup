@php
    $settings['data'] = getEmailSetting(['email_graphic','welcome_email_' . userLang()]);
    $member=workshopValidatorPresident($mail['workshop_data']);
    $workshopSetting=getWorkshopSettingData($mail['workshop_data']->id);
            if(isset($workshopSetting['email'])){
                    $settings['data'][0]->top_banner=env('AWS_PATH').$workshopSetting['email']['top_banner'];
                    $settings['data'][0]->bottom_banner=env('AWS_PATH').$workshopSetting['email']['bottom_banner'];
                    $settings['data'][0]->email_sign=$workshopSetting['email']['email_sign'];
            }
    $orgDetail=getOrgDetail();
@endphp

@include('email_template.header',$settings)
@php

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
    '[[MessageCategory]]',
    '[[OrgName]]',
    '[[OrgShortName]]',
    '[[UserFirstName]]',
    '[[UserLastName]]',
    '[[UserEmail]]',

    ];
    $values =[
    $mail['current_user_fn'],
    $mail['current_user_ln'],
    $mail['current_user_email'],
    $mail['workshop_data']->workshop_name,
    $mail['workshop_data']->code1,
    $member['p']['fullname'],
    $member['v']['fullname'],
    $mail['meeting_data']->name,
    dateConvert($mail['meeting_data']->date,'l d/m/Y'),
    timeConvert($mail['meeting_data']->date.' '.$mail['meeting_data']->start_time,' h\hi'),
    $mail['meeting_data']->place,
    $member['v']['email'],
    $member['p']['email'],
    $member['p']['phone'],
    '',
    $orgDetail->name_org,
    $orgDetail->acronym,
    '',
    '',
    '',
    ];
    if(isset($mail['token'])){
        $mail['url']=$mail['url'].'/'.$mail['token'][$mail['email']];
    }
@endphp
<tbody>
<tr>
    <td>{!! str_replace($keywords,$values,$settings['data'][1]->text_before_link) !!}</td>
</tr>
<tr>
    <td><a href="{{$mail['url']}}">Cliquez ici</a></td>
</tr>
<tr>
    <td>{!! (str_replace($keywords,$values,$settings['data'][1]->text_after_link)) !!}</td>
</tr>
</tbody>
@include('email_template.footer',$settings)