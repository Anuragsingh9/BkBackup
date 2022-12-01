@php 
	 $settings['data'] = getEmailSetting(['email_graphic','doodle_email_setting']);
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
	$member['p']['fullname'],
	$member['v']['fullname'],
	$mail['meeting_data']->name,
	'',
	'',
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
			<tr><td>{!! (str_replace($keywords,$values,$settings['data'][1]->text_before_link)) !!}</td></tr>
			<tr><td><a href="{{$mail['url']}}">Cliquez ici</a></td></tr>
			<tr><td>{!! (str_replace($keywords,$values,$settings['data'][1]->text_after_link)) !!}</td></tr>
		</tbody>
@include('email_template.footer',$settings)