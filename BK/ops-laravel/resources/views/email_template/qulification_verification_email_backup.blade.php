{{--<!DOCTYPE html>
<html>
<head>
	<title>OP Simplify</title>
	 <link href="{{ URL::asset('public/css/style.css')}}" rel="stylesheet" type="text/css">
</head>
<body>
	<table width="600px">
		<thead>
			<tr>
				<td>
					<img src="{{url('public/img/header-img.jpg')}}">
				</td>
			</tr>
		</thead>
		--}}
@php
    $settings['data'] = getEmailSetting(['email_graphic','qulification_welcome_workshop']);
    $orgDetail=getOrgDetail();
    $member=workshopValidatorPresident($mail['workshop_data']);
    $workshopSetting=getWorkshopSettingData($mail['workshop_data']->id);

    if(isset($workshopSetting['email'])){
    // var_dump($settings);
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
<tbody>
<tr>
    <td>Cher adhérent,</td>
</tr>
<tr>
    <td>Voici votre code d'authentification.</td>
</tr>
<tr>
    <td><p style="font-size:36px;color:#0a8fc0"><strong>{!! implode('-',str_split($mail['otp'])) !!}</strong></p></td>
</tr>
<tr>
    <td>Vous pouvez le rentrer dans votre navigateur actuellement.</td>
</tr>
<tr>
    <td>Sinon, voici le lien de la page :</td>
</tr>
<tr>
    <td><a href="{{$mail['path']}}">cliquer ici</a></td>
</tr>
<tr>
    <td>L'équipe Carte TP-Pro</td>
</tr>
</tr>
</tbody>
@include('email_template.footer',$settings)
{{--
<tfoot>
<tr>
    <td>
        <img src="{{url('public/img/footer-img.jpg')}}">
    </td>
</tr>
</tfoot>
</table>
</body>
</html>--}}
