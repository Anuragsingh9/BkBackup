<!DOCTYPE html>
<html>
<head>
  <title>OP Simplify</title>

   <link href="{{ URL::asset('public/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
</head>
@php
    $settings['data'] = getEmailSetting(['email_graphic']);
    $css_data = dynamicCss();
    $color1 = $css_data['color1'];
    $color2= $css_data['color2'];
@endphp
<body>
  <table width="600px">
    <thead>
      <tr>
        <td>
            <img src="{{ $settings['data'][0]->top_banner }}" style="width: 600px;">
          <br><br>
        </td>
      </tr>
    </thead>
    <tbody>

      <tr>{!! ($mail['user']) !!},<br><br></tr>
      <tr><td>
                                F&eacute;licitations,<br><br> Votre nouvel outil de cr&eacute;ation <b>{!! ($mail['organization']) !!}</b> est pr&ecirc;t.<br><br>
        </td>
      </tr>

      <tr>
                            <td>Vos adh&eacute;rents vont adorer.<br></td>
      </tr>

      <tr>
                            <td>Le bouton bleu ci-dessous vous redirigera apr&egrave;s connexion vers la page "D&eacute;marrez".
                                En quelques clics, Opsimplify sera &agrave; votre image : vos couleurs, votre logo.
                                Vous serez guid&eacute;s de la personnalisation de votre intranet jusqu'&agrave; la cr&eacute;ation de votre premi&egrave;re r&eacute;union. <br><br>
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
                   background-color: {{$color2}};
                   border-color: {{$color2}};
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
                        <tr><td>L'&eacute;quipe OPsimplify<br><br></td></tr>
      <tr><td>P.S.:</td></tr>
      <tr><td>Pour rappel, votre login est votre email.<br><br></td></tr>
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