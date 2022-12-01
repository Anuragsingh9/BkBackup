<!DOCTYPE html>
<html>
    <head>
        <title>OP Simplify</title>

        <link href="{{ URL::asset('public/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
    </head>

    <body>
        <table width="600px">
            <thead>
                <tr><td>{{ $mail['user'] }},</td></tr>
            <tr>
                <td>
                    <img src="{!!url('public/img/header-img.jpg') !!}">
                </td>
            </tr>
        </thead>
        <tbody>
             <br>
            <tr><td>
                    Felicitations. Votre nouveau outil de gestion <b>{{ $mail['organization'] }}</b> est pr&ecirc;t !
                </td>
            </tr>
        <br>
        <tr>
            <td>Vos adh&eacute;rents vont adorer.</td>
        </tr>
        <tr>
            <td>Le bouton bleu ci-dessous vous redirigera apr&eacute;s connection vers la page "D&eacute;marrez".
                En quelques clics, Opsimplify sera &aacute; votre image : vos couleurs, votre logo. 
                Vous serez guid&eacute;s de la personalisation de votre intranet jusqu'&aacute; la cr&eacute;ation de votre premi&eacute;re r&eacute;union
            </td>
        </tr>	

        <tr><td style="padding: 15px 0px;">
                <!--[if mso]>
<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $mail['path'] }}" style="display: inline-block;;border-radius:4px;v-text-anchor:middle;width:150px; height:45px; line-height:45px;" arcsize="15%" strokecolor="#0a8fc0" fillcolor="#0a8fc0">
<w:anchorlock/>
<center style="display: inline-block;color:#ffffff;font-family:'Lato',sans-serif;font-size:19px; border-radius:15px;outline: none;">@if(session()->has('lang') && session()->get('lang') == "EN")Let's start now @else D&eacute;marrez @endif</center>
</v:roundrect>
<![endif]-->
                <!--[if !mso]> <!-->
                <a href="{{ $mail['path'] }}" style="
                   color: #fff;
                   font-size: 19px;
                   text-decoration: none;
                   background-color: #0a8fc0;
                   border-color: #0a8fc0;
                   /*margin-top: 16px;*/
                   /*margin-bottom: 16px;*/
                   outline: none!important;
                   padding: 12px 20px !important;
                   border-radius: 4px;
                   moz-border-radius: 4px;
                   khtml-border-radius: 4px;
                   o-border-radius: 4px;
                   webkit-border-radius: 4px;
                   ms-border-radius: 4px;
                   display: inline-block;">@if(session()->has('lang') && session()->get('lang') == "EN")Let's start now @else D&eacute;marrez @endif</a>
                <!-- <![endif]-->
            </td></tr>
        <br>
        <tr><td>L'&eacute;quipe Opsimplify</td></tr>
        <tr><td>P.S.:</td></tr>
        <br>  
        <tr><td>Pour rappel, votre login est votre email.</td></tr>
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