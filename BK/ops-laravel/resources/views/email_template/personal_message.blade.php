@php 
$settings['data']=getEmailSetting(['email_graphic','personal_email_setting']);
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
'[[UserSenderFirstName]]',
'[[UserSenderLastName]]',
'[[MessageSenderFname]]',
    '[[MessageSenderLname]]',
    '[[MessageSenderEmail]]',
];
$values =[
$mail['recieve_user_fname'],
$mail['recieve_user_lname'],
isset($mail['recieve_user_email'])?$mail['recieve_user_email']:'',
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
$mail['send_user_fname'],
$mail['send_user_lname'],
Auth::user()->fname,
	Auth::user()->lname,
	Auth::user()->email,
];

@endphp
<tbody>
    <tr><td>{!! str_replace($keywords,$values,$settings['data'][1]->text_before_link) !!}</td></tr>
    <tr><td><a href="{{$mail['msg_url']}}">Cliquez ici</a></td></tr>
    <tr><td>{!! $mail['message_text'] !!} </td></tr>
    <tr><td>{!! (str_replace($keywords,$values,$settings['data'][1]->text_after_link)) !!}</td></tr>
</tbody>
@include('email_template.footer',$settings)