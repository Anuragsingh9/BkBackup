@php 
	$settings['data'] = getEmailSetting(['email_graphic','doodle_email_setting']);

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
@endphp

@include('email_template.header',$settings)
@php 

$keywords =[
	'[[WorkshopLongName]]',
	'[[WorkshopShortName]]',
	'[[ValidatorEmail]]',
	'[[PresidentEmail]]'
];
$values =[
	$mail['workshop_data']->workshop_name,
	$mail['workshop_data']->code1,
	$member['v']['email'],
	$member['p']['email']
];
@endphp
		<tbody>
			{{settings['data'][1]->text_after_link}}
			<tr><td>Bonjour,</td></tr>
			<tr><td>{!! ((str_replace($keywords,$values,$settings['data'][1]->text_before_link))) !!}</td></tr>
			
			<tr><td><a href="{{$mail['url']}}">Cliquez ici</a></td></tr>
			<tr><td>{!! str_replace($keywords,$values,$settings['data'][1]->text_after_link) !!}</td></tr>
		</tbody>
@include('email_template.footer',$settings)