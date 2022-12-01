@php 
	$settings['data'] = getEmailSetting(['email_graphic','job_email_setting']);
	$member=workshopValidatorPresident($mail['workshop_data']);
	$workshopSetting=getWorkshopSettingData($mail['workshop_data']->id);
        if(isset($workshopSetting['email'])){
            if(isset($workshopSetting['email']['top_banner']) && $workshopSetting['email']['top_banner']!=null){
                $settings['data'][0]->top_banner=env('AWS_PATH').$workshopSetting['email']['top_banner'];
            }
            if(isset($workshopSetting['email']['bottom_banner']) && $workshopSetting['email']['bottom_banner']!=null){
                $settings['data'][0]->bottom_banner=env('AWS_PATH').$workshopSetting['email']['bottom_banner'];
            }
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
$member['p']['fullname'],
$member['v']['fullname'],
(isset($mail['meeting_data']))?$mail['meeting_data']->name:'',
(isset($mail['meeting_data']))?dateConvert($mail['meeting_data']->date,'l d/m/Y'):'',
(isset($mail['meeting_data']))?timeConvert($mail['meeting_data']->date.' '.$mail['meeting_data']->start_time,' h\hi'):'',
(isset($mail['meeting_data']))?$mail['meeting_data']->place:'',
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
			
			<tr><td>{!! ((str_replace($keywords,$values,$settings['data'][1]->text_before_link))) !!}</td></tr>
			@if(is_array($mail['url_task']))

				@foreach($mail['url_task'] as $val)
					<tr><td><a href="{{$val}}">Cliquez ici</a></td></tr>
				@endforeach
			@else
				<tr><td><a href="{{$mail['url_task']}}">Cliquez ici</a></td></tr>
			@endif

			<tr><td>{!! (str_replace($keywords,$values,$settings['data'][1]->text_after_link)) !!}</td></tr>
			<tr><td>{!! nl2br(str_replace($keywords,$values,$settings['data'][0]->email_sign)) !!}</td></tr>
		</tbody>
@include('email_template.footer',$settings)