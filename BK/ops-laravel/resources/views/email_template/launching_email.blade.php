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
					<img src="{{url('public/img/header-img.jpg')}}" style="width: 600px;">
				</td>
			</tr>
		</thead>
		
		<tbody>
			<tr><td>Nom : {{ $mail['name']}}</td></tr>
			<tr><td>Email : {{ $mail['formemail']}}</td></tr>
			<tr><td>OP : {{ $mail['op']}}</td></tr>
			@if(isset($_COOKIE['name']) && isset($_COOKIE['phone']))
			<tr><td>Referred by</td></tr>
			<tr><td>First name : {{ $mail['referedFName']}}</td></tr>
			<tr><td>Last name : {{ $mail['referedLName']}}</td></tr>
			<tr><td>Email : {{ $mail['referedEmail']}}</td></tr>
			@endif
		</tbody>
		<tfoot>
			<tr>
				<td>
					<img src="{{url('public/img/footer-img.jpg')}}" style="width: 600px;">
				</td>
			</tr>
		</tfoot>
	</table>
</body>
</html>