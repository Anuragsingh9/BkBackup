@php 
	$settings['data'] = getEmailSetting(['email_graphic','job_email_setting']);
@endphp
<!DOCTYPE html>
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
					<img src="{{ $settings['data'][0]->top_banner }}" style="width: 600px;">
				</td>
			</tr>
		</thead>
		
		<tbody>
			<tr><td>{{$mail['user']->fname }},<br /><br /> 
			Vous avez demand&eacute; &agrave; r&eacute;initialiser votre mot de passe.<br />		
			<a href="{{url($mail['url'])}}">cliquant ici.</a>.<br /><br />
			</td>
			</tr>
						<tr><td>{!! nl2br($settings['data'][0]->email_sign) !!}</td></tr>

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