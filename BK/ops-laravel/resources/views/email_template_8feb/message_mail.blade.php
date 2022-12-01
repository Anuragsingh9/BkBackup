@php 

	$settings['data'] = getEmailSetting(['email_graphic','msg_email_setting']);
	$member=workshopValidatorPresident($mail['workshop_data']);
	$orgDetail=getOrgDetail();
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
	'',
	'',
	'',
	'',
	'',
	'',
	$member['v']['email'],
	$member['p']['email'],
	$member['p']['phone'],
	$mail['message_catagory'],
	$orgDetail->name_org,
	$orgDetail->acronym,
	$mail['current_user_fn'],
	$mail['current_user_ln'],
	$mail['current_user_email'],

];
@endphp
		<tbody>
		
			<tr><td>{!! str_replace($keywords,$values,$settings['data'][1]->text_before_link) !!}</td></tr>
			<tr><td><a href="{{$mail['msg_url']}}">cliquez ici</a></td></tr>
			<tr><td>{!! $mail['message_text'] !!} </td></tr>
			<tr><td>{!! str_replace($keywords,$values,$settings['data'][1]->text_after_link) !!}</td></tr>
			<tr><td>{!! utf8_encode($settings['data'][0]->email_sign) !!}</td></tr>
		</tbody>
@include('email_template.footer',$settings)