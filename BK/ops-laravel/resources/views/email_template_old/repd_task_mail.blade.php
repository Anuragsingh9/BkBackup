@php 
	$settings['data'] = getEmailSetting(['email_graphic','job_email_setting']);
	$member=workshopValidatorPresident($mail['workshop_data']);
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
	'[[orgadminFN]]',
	'[[orgadminLN]]',
	'[[orgadminEmail]]',
	
	];

	$values =[
$mail['current_user_fn'],
$mail['current_user_ln'],
$mail['current_user_email'],
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
$orgDetail->fname,
$orgDetail->lname,
$orgDetail->email,
];
@endphp
		<tbody>
			
			<tr><td>{!! utf8_encode(str_replace($keywords,$values,$settings['data'][1]->text_before_link)) !!}</td></tr>
                <tr><td><a href="{{$mail['url_task']}}">Acc&eacute;dez &aacute; vos t&acirc;ches</a></td></tr>
			<tr><td>{!! utf8_encode(str_replace($keywords,$values,$settings['data'][1]->text_after_link)) !!}</td></tr>
			<tr><td>{!! str_replace($keywords,$values,$settings['data'][0]->email_sign) !!}</td></tr>
		</tbody>
@include('email_template.footer',$settings)