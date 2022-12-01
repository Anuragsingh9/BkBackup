<!DOCTYPE html>
<html>
<head>
	<title>OP Simplify</title>
	
	 <link href="{{ URL::asset('public/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
</head>

<body>
	<table width="600px">
		<thead>
			<tr>
				<td>
					<img src="{!!url('public/img/header-img.jpg') !!}">
				</td>
			</tr>
		</thead>
		<tbody>

			<tr>{{ $mail['user'] }}</tr>
			<tr><td>
				Felicitations. Votre nouveau outil de gestion <b>{{ $mail['organization'] }}</b> est prêt !
				</td>
			</tr>
			<br>	
			<tr>
				<td>Vos adhérents vont adorer.</td>
			</tr>
			<br>
			<tr>
				<td>Le bouton bleu ci-dessous vous redirigera après connection vers la page “Démarrez”.
				En quelques clics, Opsimplify sera à votre image : vos couleurs, votre logo. 
				Vous serez guidés de la personalisation de votre intranet jusqu’à la création de votre première réunion
				</td>
			</tr>		
			<tr><td><a href="{{ $mail['path'] }}" style="
			color: #fff;
		    font-size: 19px;
		    text-decoration: none;
		    background-color: #0a8fc0;
		    border-color: #0a8fc0;
		    margin-top: 16px;
		    margin-bottom: 16px;
		    outline: none!important;
		    padding: 12px 20px;
		    border-radius: 4px;
		    display: inline-block;">Let’s start now</a></td></tr>
			<tr><td>L’équipe Opsimplify</td></tr>
			<tr><td>P.S.:</td></tr>
			<tr><td>Pour rappel, votre login est votre email.</td></tr>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td>
					<img src="{{url('public/img/footer-img.jpg')}}">
				</td>
			</tr>
		</tfoot>
	</table>
</body>
</html>