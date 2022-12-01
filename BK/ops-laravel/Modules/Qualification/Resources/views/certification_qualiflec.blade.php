<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
        @font-face {
            font-family: 'Qanelas-Light';
            src: url('/public/fonts/qualification/Qanelas-Light.eot');
            src: url('/public/fonts/qualification/Qanelas-Light.eot?#iefix') format('embedded-opentype'),
            url('/public/fonts/qualification/Qanelas-Light.svg#Qanelas-Light') format('svg'),
            url('/public/fonts/qualification/Qanelas-Light.ttf') format('truetype'),
            url('/public/fonts/qualification/Qanelas-Light.woff') format('woff'),
            url('/public/fonts/qualification/Qanelas-Light.woff2') format('woff2');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'QanelasSemiBold';
            src: url('/public/fonts/qualification/QanelasSemiBold.eot') ;
            src: url('/public/fonts/qualification/QanelasSemiBold.eot') format('embedded-opentype'),
            url('/public/fonts/qualification/QanelasSemiBold.woff2') format('woff2'),
            url('/public/fonts/qualification/QanelasSemiBold.woff') format('woff'),
            url('/public/fonts/qualification/QanelasSemiBold.ttf') format('truetype'),
            url('/public/fonts/qualification/QanelasSemiBold.svg#QanelasSemiBold') format('svg');
        }

        @font-face {
            font-family: 'QanelasMedium';
            src: url('/public/fonts/qualification/QanelasMedium.eot');
            src: url('/public/fonts/qualification/QanelasMedium.eot') format('embedded-opentype'),
            url('/public/fonts/qualification/QanelasMedium.woff2') format('woff2'),
            url('/public/fonts/qualification/QanelasMedium.woff') format('woff'),
            url('/public/fonts/qualification/QanelasMedium.ttf') format('truetype'),
            url('/public/fonts/qualification/QanelasMedium.svg#QanelasMedium') format('svg');
        }

        @font-face {
            font-family: "Qanelas-ExtraBold";
            src: url("/public/fonts/qualification/Qanelas-ExtraBold.eot"); /* IE9 Compat Modes */
            src: url("/public/fonts/qualification/Qanelas-ExtraBold.eot?#iefix") format("embedded-opentype"), /* IE6-IE8 */ url("/public/fonts/qualification/Qanelas-ExtraBold.otf") format("opentype"), /* Open Type Font */ url("/public/fonts/qualification/Qanelas-ExtraBold.svg") format("svg"), /* Legacy iOS */ url("/public/fonts/qualification/Qanelas-ExtraBold.ttf") format("truetype"), /* Safari, Android, iOS */ url("/public/fonts/qualification/Qanelas-ExtraBold.woff") format("woff"), /* Modern Browsers */ url("/public/fonts/qualification/Qanelas-ExtraBold.woff2") format("woff2"); /* Modern Browsers */
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: "Qanelas-Heavy";
            src: url("/public/fonts/qualification/Qanelas-Heavy.eot"); /* IE9 Compat Modes */
            src: url("/public/fonts/qualification/Qanelas-Heavy.eot?#iefix") format("embedded-opentype"), /* IE6-IE8 */ url("/public/fonts/qualification/Qanelas-Heavy.otf") format("opentype"), /* Open Type Font */ url("/public/fonts/qualification/Qanelas-Heavy.svg") format("svg"), /* Legacy iOS */ url("/public/fonts/qualification/Qanelas-Heavy.ttf") format("truetype"), /* Safari, Android, iOS */ url("/public/fonts/qualification/Qanelas-Heavy.woff") format("woff"), /* Modern Browsers */ url("/public/fonts/qualification/Qanelas-Heavy.woff2") format("woff2"); /* Modern Browsers */
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: "Qanelas-Bold";
            src: url("/public/fonts/qualification/Qanelas-Bold.eot"); /* IE9 Compat Modes */
            src: url("/public/fonts/qualification/Qanelas-Bold.eot?#iefix") format("embedded-opentype"), /* IE6-IE8 */ url("/public/fonts/qualification/Qanelas-Bold.otf") format("opentype"), /* Open Type Font */ url("/public/fonts/qualification/Qanelas-Bold.svg") format("svg"), /* Legacy iOS */ url("/public/fonts/qualification/Qanelas-Bold.ttf") format("truetype"), /* Safari, Android, iOS */ url("/public/fonts/qualification/Qanelas-Bold.woff") format("woff"), /* Modern Browsers */ url("/public/fonts/qualification/Qanelas-Bold.woff2") format("woff2"); /* Modern Browsers */
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: "Qanelas-Regular";
            src: url("/public/fonts/qualification/Qanelas-Regular.eot"); /* IE9 Compat Modes */
            src: url("/public/fonts/qualification/Qanelas-Regular.eot?#iefix") format("embedded-opentype"), /* IE6-IE8 */ url("/public/fonts/qualification/Qanelas-Regular.otf") format("opentype"), /* Open Type Font */ url("/public/fonts/qualification/Qanelas-Regular.svg") format("svg"), /* Legacy iOS */ url("/public/fonts/qualification/Qanelas-Regular.ttf") format("truetype"), /* Safari, Android, iOS */ url("/public/fonts/qualification/Qanelas-Regular.woff") format("woff"), /* Modern Browsers */ url("/public/fonts/qualification/Qanelas-Regular.woff2") format("woff2"); /* Modern Browsers */
            font-weight: normal;
            font-style: normal;
        }

        @import url('https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800&display=swap');

        @page {
            size: A4;
            margin: 0mm;
            margin-header: 0mm;
            margin-footer: 0mm;
        }

        div.page-layout {
            height: 295.5mm;
            width: 209mm;
        }

        * {
            margin: 0px;
            padding: 0px;
            border: none;
            outline: none;
            list-style: none;
        }

        html {
            height: 100%;
        }

        body {
            overflow-x: hidden;
            width: 100%;
            /* font-family: 'Open Sans', sans-serif; */
            font-family: "Qanelas-Regular";
            height: 100%;
            position: relative;
            max-width: 1100px;
            margin: auto;
            color: #073e62;
        }

        .main-heading-wrap {
            width: 100%;
            text-align: center;
            display: inline-block;
            margin: 50px 0px 15px 0px;
        }

        .main-heading, .main-heading2 {
            text-align: center;
            text-transform: uppercase;
            font-size: 30px;
            margin: 0px;
            padding: 0px 50px;
            display: inline-block;
            position: relative;
            color: #073e62;
            font-weight: normal;
            box-sizing: border-box;
        }

        .main-heading2 {
            width: 100%;
        }

        .main-heading:before, .main-heading:after {
            content: '';
            position: absolute;
            width: 40px;
            height: 2px;
            background: #e86f67;
            display: inline-block;
            vertical-align: middle;
            top: 0;
            bottom: 0;
            margin: auto;
        }

        .main-heading:before {
            left: 0;
        }

        .main-heading:after {
            right: 0;
        }

        .certificate-bg-wrap {
            background: url({{ url('public/qualification/images/qualification-certificate-bg2.jpg') }}) no-repeat center top;
            background-size: cover;
            /* min-height: 100%; */
            min-height: 1415px;
            width: 100%;
        }

        header {
            padding: 35px 6% 0px 6%;
            text-align: center;
        }

        header .container {
            /*max-width: 88%;*/
            margin: auto;
            display: inline-block;
            width: 100%;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .cp-logo {
            width: 60%;
            display: inline-block;
            vertical-align: top;
            /*text-align: center;*/
        }

        .org-logo {
            width: 19.75%;
            display: inline-block;
            vertical-align: top;
            /*text-align: center;*/
            float: left;
        }

        .workshop-logo {
            width: 19%;
            display: inline-block;
            vertical-align: top;
            text-align: center;
            float: right;
            padding-left: 15px;
            box-sizing: border-box;
        }

        .cp-logo h3 {
            /*font-family: 'QanelasSemiBold';*/
            font-family: 'Qanelas-Light';
            color: #0f4c97;
            font-size: 28px;
            text-align: left;
            padding-left: 5px;
            font-weight: 300;
        }

        .cp-logo h1 {
            text-align: left;
            color: #0f4c97;
            font-size: 60px;
            margin-bottom: 0;
            margin-top: 0;
            line-height: 52px;
            font-family: "Qanelas-ExtraBold";
        }

        .cp-logo p {
            font-size: 21px;
            margin-top: 12px;
            color: #073e62;
            margin-bottom: 20px;
            padding-left: 5px;
            font-weight: 600;
            font-family: 'QanelasSemiBold';
            /*font-family: "Qanelas-Regular";*/
        }

        img {
            max-width: 100%;
        }

        .org-logo img {
            max-height: 150px;
        }

        .cp-logo img {
            width: 316px;
            min-height: 100px;
            max-height: 100px;
        }

        .workshop-logo img {
            width: auto;
            max-height: 90px;
        }

        .banner-section {
            /* background: url(




        {{ url('public/qualification/images/sky.png') }}     ) no-repeat center top;
            min-height: 300px;
            background-size: cover;
            padding-top: 3%; */
            padding-bottom: 180px;
        }

        .banner-inner-section {
            max-width: 100%;
            color: #073e62;
            padding: 20px 6% 25px 6%;
            min-height: 350px;
        }

        .banner-inner-section h3 {
            /* padding-left: 29px; */
            margin: 10px 0px 15px;
            font-size: 16px;
            /* font-weight: 400; */
            text-transform: uppercase;
            /* opacity: 0.9; */
            font-family: "Qanelas-Bold";
        }

        .banner-inner-section li {
            font-size: 20px;
            color: #073e62;
            line-height: 20px;
            font-family: 'Qanelas-Regular';
        }

        .card-detail {
            min-height: 106px;
        }

        .card-detail li {
            margin: 2px 0px;
        }

        .banner-inner-section span {
            padding: 6px 29px;
            display: inline-block;
            margin: 3px 0px;
        }

        .banner-inner-section span.active {
            background: #fddc21;
            color: #073e62;
            font-weight: 700;
            font-family: 'Qanelas-Bold';
        }

        .detail {
            /*max-width: 460px;*/
            width: 60%;
            margin: auto;
            margin-top: 30px;
            margin-bottom: 40px;
        }

        .company-detail {
            width: 55%;
        }

        .qualification {
            width: 44%;
        }

        .company-detail, .qualification {
            display: inline-block;
            vertical-align: top;
        }

        span.label, .qualification h3 {
            font-size: 16px;
            color: #073e62;
            display: block;
            margin-top: 8px;
            margin-bottom: 3px;
            font-weight: 400;
        }

        span.value, .qualification ul li {
            font-size: 20px;
            font-weight: 700;
            color: #073e62;
            /*font-family: 'Qanelas-Bold';*/
        }

        .qualification ul li {
            padding: 6px 0px;
        }

        .card-inner {
            width: 38%;
        }

        .card-inner img {
            width: 296px;
        }

        .card-detail {
            width: 59%;
            padding-left: 4%;
            box-sizing: border-box;
        }

        .card-inner, .card-detail {
            display: inline-block;
            vertical-align: top;
        }

        .card {
            /*padding-left: 140px;*/
            border-top: 1px dotted #073e62;
            padding: 60px 0px;
        }

        .card-label {
            font-size: 16px;
            color: #073e62;
            margin-top: 1px;
            margin-bottom: 2px;
            font-weight: 400;
            display: inline-block;
            font-family: 'Qanelas-Regular';
        }

        .card-value {
            font-size: 16px;
            font-weight: 600;
            color: #073e62;
            font-family: 'Qanelas-Bold';
        }

        .card-detail img {
            width: auto;
            max-height: 60px;
        }

        .card-detail ul li p {
            padding: 9px 0px 5px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            background: #073e62;
            width: 100%;
            left: 0px;
            right: 0px;
            height: 10px;
        }

        /*****card*****/
        .card-block {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            right: 0;
            top: 0;
            margin: auto;
        }

        .card-upper-block {
            height: 32%;
            padding: 15px 0px 5px;
        }

        .card-company-block {
            width: 70%;
            float: left;
            padding-right: 17px;
            box-sizing: border-box;
        }

        .card-company-block span {
            display: block;
            min-height: 14px;
            position: relative;
            color: #22498e;
            text-align: left;
            padding-left: 14px;
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
            font-size: 10px;
            line-height: 10px;
        }

        .zip-code {
            margin-top: 3px;
        }

        .card-upper-logo {
            width: 27%;
            float: left;
            position: relative;
            min-height: 30px;
            padding: 0 3px;
            max-height: 30px;
            box-sizing: border-box;
        }

        .card-lower-block {
            height: 55%;
        }

        .card-bottom-logo {
            float: left;
            width: 30%;
            padding: 10px 0px;
        }

        .domians-name {
            float: left;
            width: 70%;
            display: table;
            height: 100%;
        }

        .domians-name ul {
            display: table-cell;
            vertical-align: middle;
            margin: 0;
            color: #fff;
        }

        .parent-card-block {
            position: relative;
            display: inline-block;
        }

        .card-bottom-logo {
            text-align: center;
        }

        .card-bottom-logo img {
            width: 38px;
        }

        .domians-name ul li {
            font-size: 6px;
            text-align: right;
            padding: 2px 0px;
            padding-right: 15px;
            position: relative;
        }

        .domians-name ul li:after {
            content: '';
            position: absolute;
            width: 10px;
            height: 4px;
            background: gold;
            top: 4px;
            right: 0;
        }

        .card-upper-logo img {
            max-width: 100%;
            max-height: 35px;
            width: auto;
            float: right;
        }

        .company-detail li {
            margin: 8px 0px;
        }

        .card-details-block {
            padding-left: 19%;
            box-sizing: border-box;
            width: 100%;
        }

        footer {
            height: 50px;
            width: 100%;
            position: fixed;
            bottom: 0;
            left: 0;
            background: #073e62;
        }

        .card-issue-date {
            font-size: 8px;
            color: #fff;
        }

        .card-issue-date span {
            width: 100%;
            display: inline-block;
        }

        .two-col-section {
            width: 100% !important;
            display: inline-block;
            padding: 0px 6%;
        }

        .two-col-section .company-detail {
            width: 100%;
        }

        .left-block-secion, .right-block-secion {
            width: 50%;
            float: left;
        }

        .card-singature {
            width: 100%;
        }

        .card-singature ul {
            width: 30%;
            float: right;
        }

        .card-singature li {
            font-size: 12px;
            margin: 1px 0px;
        }

        /* @media print {
            html, body {
                width: 210mm;
                -webkit-print-color-adjust: exact;
            }
        } */

        @page {
            margin: 0.75cm 0cm 0cm 0cm
        }

        @media print {
            @page {
                size: A4;
            }
        }
    </style>
</head>
<body>
<div class="certificate-bg-wrap">
    @php
        $lang = session()->has('lang') ? session()->get('lang') : "FR";
    @endphp
    <header>
        <div class="container">
            <div class="org-logo">
                <figure>
                    <img src="{{url('public/qualification/images/logo_qualifelec.png') }}" alt="Org Logo">
                </figure>
            </div>
            {{-- <div class="cp-logo">
                @if($lang=='FR')
                    <h3>Certificat Professionnel</h3>
                @else
                    <h3>Professional certificate</h3>
                @endif
                <h1>CARTE TP-PRO</h1>
                @if(@$date['deliverydate'] && @$date['expdeliverydate'] && $date['deliverydate']!=NULL && $date['expdeliverydate']!=NULL)
                    <p>{{ (($lang='FR')?'Valable du' :'Valid from').' '.$date['deliverydate'].' '.( ($lang=='FR')?'au':'to').' '.$date['expdeliverydate']}}</p>
                @else
                    <p>{{ (($lang='FR')?'Valable du' :'Valid from').' 1er février 2019 '.( ($lang=='FR')?'au':'to').' 31 janvier 2020'}}</p>
                @endif
            </div>--}}
            <div class="workshop-logo">
                <figure>
                    <img src="{{str_replace('https://','http://',$workshop->workshop_logo)}}" alt="Workshop Logo">
                </figure>
            </div>
        </div>
    </header>
    <main>
        <section>

            <div class="container">
                <div class="clearfix main-heading-wrap">
                    <h1 class="main-heading">Certificate de qualification</h1>
                    <h1 class="main-heading2">Professionnelle</h1>
                </div>

                <div class="detail two-col-section">
                    <div class="company-detail">
                        <div class="left-block-secion">
                            <ul>

                                <li>
                                    @if(array_key_exists('Raison sociale',$otherfield))
                                        <span class="label">
                                  {{$otherfield['Raison sociale']->name}}
                                    :</span>
                                    @else
                                        <span class="label">
                                    Company Name
                                    :</span>

                                    @endif
                                    @if(isset($user->userSkillCompany->text_input))
                            {{--            @php
                                            $company='';
                                                $str=$user->userSkillCompany->text_input;
                                                    $exp=explode(' ',$str);
                                                  if(strlen($exp[0]) > 18){
                                                  $trimstring = substr($str, 0, 18). '...';
                                                    $company=$trimstring;
                                                  }else{
                                                    $company=$exp[0]. '...';
                                                  }

                                        @endphp--}}
                                        <span class="value">{{$user->userSkillCompany->text_input }}</span>
                                    @endif
                                </li>
                                @php
                                    $i=0;
                                @endphp
                                <li><span class="label">@if($lang=='FR')Dossier N°  @else Certificat
                                        Numéro  @endif :</span>

                                    <span class="value">  {{$cardNo}}</span>
                                </li>
                                <li>
                                    @if(array_key_exists('Forme juridique',$otherfield))
                                        <span class="label">
                                  {{$otherfield['Forme juridique']->name}}
                                    :</span>
                                    @else
                                        <span class="label">
                                 Forme juridique
                                    :</span>
                                    @endif
                                    @if(isset($otherfield['Forme juridique']) && $otherfield['Forme juridique']->skillFormat->name_en=='Select')
                                        @foreach($otherfield['Forme juridique']->skillSelect as $k=>$val )
                                            @if($otherfield['Forme juridique']->userSkill!=NULL && $val->id==$otherfield['Forme juridique']->userSkill->select_input)
                                                <span class="value">{{ $val->option_value }}</span>
                                            @endif
                                        @endforeach
                                    @endif
                                </li>
                                <li><span class="label">
                                        @if(array_key_exists('SIRET',$otherfield))
                                            <span class="label">
                                  {{$otherfield['SIRET']->name}}
                                    :</span>
                                        @else
                                            <span class="label">
                                   SIRET
                                    :</span>
                                        @endif

                                        @if(isset($otherfield['SIRET']) && $otherfield['SIRET']->skillFormat->name_en=='Siret')
                                            @if(isset($otherfield['SIRET']->userSkill) && !empty($otherfield['SIRET']->userSkill))
                                                <span class="value">{{ @$otherfield['SIRET']->userSkill->numerical_input }}</span>
                                    @endif

                                    @endif
                                </li>
                                <li><span class="label">@if($lang=='FR') Téléphone @else Téléphone @endif:</span><span
                                            class="value">{{ ($user->mobile!=NULL)?$user->mobile:'' }}</span>
                                </li>
                                <li><span class="label">@if($lang=='FR') Couriel @else Couriel @endif:</span><span
                                            class="value">{{ ($user->email!=NULL)?$user->email:'' }}</span>
                                </li>
                                <li><span class="label">@if($lang=='FR') Responsable legal @else Responsable
                                        legal  @endif:</span><span
                                            class="value">{{ ($user->fname!=NULL)?$user->fname.' '.$user->lname:'' }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="right-block-secion">
                            <ul>
                                <li><span class="label">@if($lang=='FR')Certificat Numéro  @else Certificat
                                        Numéro  @endif:</span><span
                                            class="value">{{$cardNo}}</span>
                                </li>
                                <li><span class="label"> @if($lang=='FR') Valable du  @else Valable du @endif
                                    : </span><span class="value">
								@if(@$date['deliverydate'] && @$date['expdeliverydate'] && $date['deliverydate']!=NULL && $date['expdeliverydate']!=NULL)
                                            {{ $date['deliverydate'].' au '. $date['expdeliverydate']}}
                                        @else

                                        @endif
								</span></li>
                                <li><span class="label"> @if($lang=='FR') Édité le @else Édité le @endif
                                    : </span><span class="value">
								@if(@$date['deliverydate'] && @$date['expdeliverydate'] && $date['deliverydate']!=NULL && $date['deliverydate']!=NULL)
                                            {{ $date['deliverydate'] }}
                                        @else

                                        @endif

								</span></li>
                                {{--<li><span class="label">@if($lang=='FR')Ville  @else City @endif:</span><span
                                            class="value">{{  ($user->city!=null)?$user->city:'' }}</span>
                                </li>--}}
                            </ul>
                        </div>
                    </div>
                    {{--  <div class="qualification">
                          @if($lang=='FR')
                              <h3>Signes de qualités déclarés :</h3>
                          @else
                              <h3>Other qualifications :</h3>
                          @endif
                          <ul>
                              --}}{{-- <li>Signe 1</li>
                              <li>Signe 2</li>
                              <li>Signe 3</li>
                              <li>Signe 4</li>
                              <li>Signe 5</li> --}}{{--
                              @if(isset($otherfield['QUALITYONE']) && $otherfield['QUALITYONE']->skillFormat->name_en=='Select')
                                  @foreach($otherfield['QUALITYONE']->skillSelect as $k=>$val )
                                      @if($otherfield['QUALITYONE']->userSkill!=null && $val->id==$otherfield['QUALITYONE']->userSkill->select_input)
                                          <li>{{ $val->option_value }}</li>
                                      @endif
                                  @endforeach
                              @endif
                              @if(isset($otherfield['QUALITYTWO']) && $otherfield['QUALITYTWO']->skillFormat->name_en=='Select')
                                  @foreach($otherfield['QUALITYTWO']->skillSelect as $k=>$val )
                                      @if($otherfield['QUALITYTWO']->userSkill!=null && $val->id==$otherfield['QUALITYTWO']->userSkill->select_input)
                                          <li>{{ $val->option_value }}</li>
                                      @endif
                                  @endforeach
                              @endif
                              @if(isset($otherfield['QUALITYTHREE']) && $otherfield['QUALITYTHREE']->skillFormat->name_en=='Select')
                                  @foreach($otherfield['QUALITYTHREE']->skillSelect as $k=>$val )
                                      @if($otherfield['QUALITYTHREE']->userSkill!=null && $val->id==$otherfield['QUALITYTHREE']->userSkill->select_input)
                                          <li>{{ $val->option_value }}</li>
                                      @endif
                                  @endforeach
                              @endif
                              @if(isset($otherfield['QUALITYFOUR']) && $otherfield['QUALITYFOUR']->skillFormat->name_en=='Select')
                                  @foreach($otherfield['QUALITYFOUR']->skillSelect as $k=>$val )
                                      @if($otherfield['QUALITYFOUR']->userSkill!=null && $val->id==$otherfield['QUALITYFOUR']->userSkill->select_input)
                                          <li>{{ $val->option_value }}</li>
                                      @endif
                                  @endforeach
                              @endif
                              @if(isset($otherfield['QUALITYFIVE']) && $otherfield['QUALITYFIVE']->skillFormat->name_en=='Select')
                                  @foreach($otherfield['QUALITYFIVE']->skillSelect as $k=>$val )
                                      @if($otherfield['QUALITYFIVE']->userSkill!=null && $val->id==$otherfield['QUALITYFIVE']->userSkill->select_input)
                                          <li>{{ $val->option_value }}</li>
                                      @endif
                                  @endforeach
                              @endif
                          </ul>
                      </div>--}}
                </div>
            </div>
        </section>

        <section id="banner">
            <div class="banner-section">
                <div class="container">
                    <div class="banner-inner-section">
                        @if($lang=='FR')
                            <h3>Qualification professionnelle</h3>
                        @else
                            <h3>Qualification professionnelle</h3>
                        @endif
                        <ul>

                            @foreach($domain as $k=>$item)
                                <li><span style="background: #fddc21;color: #073e62;
                                    font-weight: 700;
                                    font-family: 'Qanelas-Bold';" class="active">{{ $item->name }}</span></li>

                                {{--                            @if($domain->where('id',$item->id)->first())--}}
                                {{--                                <li><span style="background: #fddc21;color: #073e62;--}}
                                {{--                                    font-weight: 700;--}}
                                {{--                                    font-family: 'Qanelas-Bold';" class="active">{{ $item->name }}</span></li>--}}
                                {{--                            @else--}}
                                {{--                                <li>{{ $item->name }}</li>--}}
                                {{--                            @endif--}}
                            @endforeach
                            {{-- <li><span class="active">Aménagements, voiries et routes</span></li>
                            <li><span>Terrassement, fondations et reprise en sous-oeuvre</span></li>
                            <li><span>Ouvrages d’art et travaux spéciaux</span></li>
                            <li><span>Travaux d’électrisation et de télécommunications</span></li>  --}}
                        </ul>
                    </div>


                    <div class="card-detail card-singature">
                        <ul>
                            <li>
                                <figure>
                                    @if(!empty($workshop->signatory['signatory_signature']))
                                        <img src="{{str_replace('https://','http://',env('AWS_PATH').$workshop->signatory['signatory_signature'])}}"
                                             alt="Signatory Signature">
                                    @endif
                                </figure>
                            </li>
                            <li>
                                {{ $workshop->signatory['signatory_fname']. ' ' .$workshop->signatory['signatory_lname'] }}
                            </li>
                            <li>
                                {{ $workshop->signatory['signatory_possition'] }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

    </main>
    {{-- <footer>

    </footer> --}}
</div>
</body>
</html>