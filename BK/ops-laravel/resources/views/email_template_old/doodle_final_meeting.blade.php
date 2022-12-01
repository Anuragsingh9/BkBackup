@php 
$settings['data'] = getEmailSetting(['email_graphic','doodle_final_date']);
$orgDetail=getOrgDetail();
$member=workshopValidatorPresident($mail['workshop_data']);
@endphp

@include('email_template.header',$settings)
@php 
$keywords =[
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
$mail['workshop_data']->workshop_name,
$mail['workshop_data']->code1,
$member['v']['fullname'],
$member['p']['fullname'],
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
    <tr><td>{!! utf8_encode(utf8_decode(str_replace($keywords,$values,$settings['data'][1]->text_before_link))) !!}</td></tr>
    <tr><td><a href="{{$mail['url']}}">Cliquez ici</a></td></tr>
    <tr><td>{!! utf8_encode(str_replace($keywords,$values,$settings['data'][1]->text_after_link)) !!}</td></tr>
</tbody>
@include('email_template.footer',$settings)