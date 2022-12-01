@php 

	$settings['data'] = getEmailSetting(['email_graphic','msg_replies_email_setting']);
	$member=workshopValidatorPresident($mail['workshop_data']);
	$orgDetail=getOrgDetail();
	$getUserDetail=getUserInfoByEmail($mail['email']);
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
@endphp
@include('email_template.header',$settings)
@php 

use Illuminate\Support\Facades\Auth;
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
    '[[MessageSenderFname]]',
    '[[MessageSenderLname]]',
    '[[MessageSenderEmail]]',
];
$values =[
	$mail['workshop_data']->workshop_name,
	$mail['workshop_data']->code1,
	$member['p']['fullname'],
	$member['v']['fullname'],
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
	$getUserDetail->fname,
	$getUserDetail->lname,
	$mail['email'],
	Auth::user()->fname,
	Auth::user()->lname,
	Auth::user()->email,

];
@endphp
		<tbody>
			<tr><td>{!! (str_replace($keywords,$values,$settings['data'][1]->text_before_link)) !!}</td></tr>
			<tr><td><a href="{{$mail['msg_url']}}">cliquez ici</a></td></tr>
			<tr><td><br/><br/>{!! $mail['message_text'] !!}<br/><br/> </td></tr>
			<tr><td>{!! (str_replace($keywords,$values,$settings['data'][1]->text_after_link)) !!}</td></tr>
			{{--<tr><td>{!! ($settings['data'][0]->email_sign) !!}</td></tr>--}}
		</tbody>
@include('email_template.footer',$settings)