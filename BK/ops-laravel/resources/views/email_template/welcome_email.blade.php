<!DOCTYPE html>
<html>
<head>
	<title>OP Simplify</title>
	 <link href="{{ URL::asset('public/css/style.css')}}" rel="stylesheet" type="text/css">
</head>
@php
	$settings['data'] = getEmailSetting(['email_graphic']);
@endphp
<body>
	<table width="600px">
		<thead>
			<tr>
				<td>
					<img src="{{ $settings['data'][0]->top_banner }}" style="width: 600px;">
				</td>
			</tr>
		</thead>
		
		<tbody>
			<tr><td>Bienvenue,</td></tr>
			<tr>
				<td>Avant de cr&eacute;er votre intranet, et afin de nous assurer que nous avons votre bon email, voici votre code de validation.</td>
			</tr>			
			<tr><td><p style="font-size:36px;color:#0a8fc0"><strong>{!! implode('-',str_split($mail['otp'])) !!}</strong></p></td></tr>
			<tr><td>Il vous suffit de saisir ce code sur l'&eacute;cran de validation.</td></tr>
			<tr><td>L'&eacute;quipe Opsimplify</td></tr>
			<tr><td>Si vous avez ferm&eacute; votre navigateur, veuillez <a href="{{$mail['path']}}">cliquer ici</a></td></tr>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td>
					<img src="{{ $settings['data'][0]->bottom_banner }}" style="width: 600px;">
				</td>
			</tr>
		</tfoot>
	</table>
</body>
</html>