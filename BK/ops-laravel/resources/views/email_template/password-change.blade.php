<!DOCTYPE html>
<html>
<head>
	<title>OP Simplify</title>
	 <link href="{{ URL::asset('public/css/style.css')}}" rel="stylesheet" type="text/css">
</head>
<body>
@php
		$settings['data'] = getEmailSetting(['email_graphic','user_email_setting']);
		$orgDetail=getOrgDetail();
	@endphp
	<table width="600px">
		<thead>
			<tr>
				<td>
					<img src="{{ $settings['data'][0]->top_banner }}" style="width: 600px;">
				</td>
			</tr>
		</thead>
		
		<tbody>
			<tr><td>{{Auth::user()->fname}},<br/><br/>Vous venez de modifier votre mot de passe.<br />
			<a href="{{url($mail['url'])}}">Si vous n'êtes pas à l'origine de cette demande, veuillez nous contacter</a>.<br /><br />
			
			{{$orgDetail->fname}} {{$orgDetail->lname}}<br/>
			{{$orgDetail->email}} <br/>{{$orgDetail->phone}}<br/><br/>
			
			</td></tr>
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