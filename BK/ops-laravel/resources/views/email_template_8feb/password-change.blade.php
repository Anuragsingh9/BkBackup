<!DOCTYPE html>
<html>
<head>
	<title>OP Simplify</title>
	 <link href="{{ URL::asset('public/css/style.css')}}" rel="stylesheet" type="text/css">
</head>
<body>
@php
		$settings['data'] = getEmailSetting(['email_graphic','user_email_setting']);

	@endphp
	<table width="600px">
		<thead>
			<tr>
				<td>
					<img src="{{ $settings['data'][0]->top_banner }}">
				</td>
			</tr>
		</thead>
		
		<tbody>
			<tr><td>,</td></tr>
			<tr>
				<td>Vous venez de modifier votre mot de passe.</td>
			</tr>			
			<tr><td><a href="{{url($mail['url'])}}">Si vous n'êtes pas à l'origine de cette demande, veuillez nous contacter</a>.</td></tr>

			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td>
					<img src="{{ $settings['data'][0]->bottom_banner }}">
				</td>
			</tr>
		</tfoot>
	</table>
</body>
</html>