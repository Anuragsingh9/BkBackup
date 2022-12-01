@php 
	$settings['data'] = getEmailSetting(['email_graphic','doodle_email_setting']);

	$member=workshopValidatorPresident($mail['workshop_data']);
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
			<tr><td>{!! str_replace($keywords,$values,$settings['data'][1]->text_before_link) !!}</td></tr>
			<tr><td>Veuillez confirmer vos dates de disponibilités pour la prochaine réunion {{$mail['workshop_data']->workshop_name}}</td></tr>
			<tr><td><a href="{{$mail['url']}}">Cliquez ici</a></td></tr>
			<tr><td>{!! str_replace($keywords,$values,$settings['data'][1]->text_after_link) !!}</td></tr>
		</tbody>
@include('email_template.footer',$settings)