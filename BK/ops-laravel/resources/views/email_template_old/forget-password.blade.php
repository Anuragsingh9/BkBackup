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
					<img src="{{url('img/header-img.jpg')}}">
				</td>
			</tr>
		</thead>
		
		<tbody>
			<tr><td>Bonjour,</td></tr>
			<tr>
                            <td>Vous avez demand&eacute; &aacute; r&eacute;initialiser votre mot de passe.</td>
			</tr>			
			<tr><td><a href="{{url($mail['url'])}}">cliquant ici.</a>.</td></tr>

			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td>
					<img src="{{url('img/footer-img.jpg')}}">
				</td>
			</tr>
		</tfoot>
	</table>
</body>
</html>