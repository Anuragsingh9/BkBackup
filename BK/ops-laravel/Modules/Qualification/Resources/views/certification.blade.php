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
            /* size: A4; */
            margin: 0mm;
            margin-header: 0mm;
            margin-footer: 0mm;
        }

        div.page-layout {
            /* height: 295.5mm; */
            /* width: 209mm; */
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
            width: 100%;
        }
        main, #banner{
            width: 100%;
            max-width: 100%;
        }
        body {
            overflow-x: hidden;
            width: 100%;
            font-family: 'Open Sans', sans-serif;
            min-height: 100%;
            position: relative;
        }
        header {
            width : 100%;
            margin-top: 60px;
            padding: 30px 6% 0px 6%;
            max-width: 100%;
            box-sizing: border-box;
            margin: auto;
        }
        header .container {
            /*max-width: 88%;*/
            margin: auto;
        }
        .container {
            /* max-width: 900px; */
            width: 100%;
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
            font-size: 50px;
            margin-bottom: 0;
            margin-top: 0;
            line-height: 52px;
            font-family: "Qanelas-ExtraBold";
        }
        .cp-logo p {
            font-size: 21px;
            margin-top: 12px;
            color: #0e4d98;
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
            max-width: 70px;
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
            background: url({{ url('public/qualification/images/sky.png') }}) no-repeat center top;
            min-height: 300px;
            background-size: cover;
            padding-top: 3%;
            width : 100%;
        }
        .banner-inner-section {
            max-width: 83%;
            margin-left: 17%;
            color: #fff;
            padding-top: 40px;
        }
        .banner-inner-section h3 {
            padding-left: 29px;
            margin: 10px 0px 15px;
            font-size: 13px;
            font-weight: 400;
            text-transform: uppercase;
            opacity: 0.9;
        }

        .banner-inner-section li {
            font-size: 20px;
            color: #a3aad5;
            line-height: 20px;
            font-family: 'Qanelas-Regular';
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
            color: #0f4c97;
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
            color: #0e4d98;
            display: block;
            margin-top: 8px;
            margin-bottom: 3px;
            font-weight: 400;
        }

        span.value, .qualification ul li {
            font-size: 20px;
            font-weight: 700;
            color: #0e4d98;
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
            border-top: 1px dotted #0e4d98;
            padding: 60px 0px;
        }

        .card-label {
            font-size: 16px;
            color: #0e4d98;
            margin-top: 1px;
            margin-bottom: 2px;
            font-weight: 400;
            display: inline-block;
            font-family: 'Qanelas-Regular';
        }

        .card-value {
            font-size: 16px;
            font-weight: 600;
            color: #00499a;
            font-family: 'Qanelas-Bold';
        }

        .card-detail img {
            width: auto;
            max-height: 60px;
            height: auto;
        }

        .card-detail ul li p {
            padding: 9px 0px 5px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            background: #0e4d98;
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
        .zip-code{
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
            background: #0e4d98;
        }
        .card-issue-date {
            font-size: 8px;
            color: #fff;
        }
        .card-issue-date span {
            width: 100%;
            display: inline-block;
        }
        @media print {
            html, body {
                /* width: 210mm; */
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
@php
    $lang = session()->has('lang') ? session()->get('lang') : "FR";
@endphp
<header>
    <div class="container">
        <div class="org-logo">
            <figure>
                <img src="{{url('public/qualification/images/org-logo.jpg') }}" alt="Org Logo">
            </figure>
        </div>
        <div class="cp-logo">
            @if($lang=='FR')
                <h3>Certificat Professionnel</h3>
            @else
                <h3>Professional certificate</h3>
            @endif
            <h1>CARTE TP Pro-Artisan</h1>
            @if(@$date['deliverydate'] && @$date['expdeliverydate'] && $date['deliverydate']!=NULL && $date['expdeliverydate']!=NULL)
                <p>{{ (($lang='FR')?'Valable du' :'Valid from').' '.$date['deliverydate'].' '.( ($lang=='FR')?'au':'to').' '.$date['expdeliverydate']}}</p>
            @else
                <p>{{ (($lang='FR')?'Valable du' :'Valid from').' 1er février 2019 '.( ($lang=='FR')?'au':'to').' 31 janvier 2020'}}</p>
            @endif
        </div>
        <div class="workshop-logo">
            <figure>
                <img src="{{str_replace('https://','http://',$workshop->workshop_logo)}}" alt="Workshop Logo">
            </figure>
        </div>
    </div>
</header>
<main>
    <section id="banner">
        <div class="banner-section">
            <div class="container">
                <div class="banner-inner-section">
                    @if($lang=='FR')
                        <h3>Domaines</h3>
                    @else
                        <h3>DOMAINS</h3>
                    @endif
                    <ul>

                        @foreach($domain as $k=>$item)
                                <li><span style="background: #fddc21;color: #0f4c97;
                                    font-weight: 700;
                                    font-family: 'Qanelas-Bold';" class="active">{{ $item->name }}</span></li>

                            {{-- @if($domain->where('id',$item->id)->first()) --}}
                               {{-- <li><span style="background: #fddc21;color: #0f4c97; --}}
                                   {{-- font-weight: 700; --}}
                                   {{-- font-family: 'Qanelas-Bold';" class="active">{{ $item->name }}</span></li> --}}
                           {{-- @else --}}
                               {{-- <li>{{ $item->name }}</li> --}}
                           {{-- @endif --}}
                        @endforeach
                        {{--  <li><span class="active">Aménagements, voiries et routes</span></li>
                        <li><span>Terrassement, fondations et reprise en sous-oeuvre</span></li>
                        <li><span>Ouvrages d’art et travaux spéciaux</span></li>
                        <li><span>Travaux d’électrisation et de télécommunications</span></li>  --}}
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="detail">
                <div class="company-detail">
                    <ul>
                        <li><span class="label">@if($lang=='FR') Nom de l’entreprise @else Company name @endif:</span>
                            @if(isset($user->userSkillCompany->text_input))
                          {{--      @php
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
                        <li><span class="label">@if($lang=='FR')SIRET @else Company registration number @endif :</span>

                            @if(isset($user->userSkillSiret->numerical_input))
                                <span class="value">{{ $user->userSkillSiret->numerical_input }}</span>
                            @endif
                        </li>
                        <li><span class="label">@if($lang=='FR')Code Ape @else SIC activity code @endif : </span>
                            @if(isset($otherfield['CODEAPE']))
                                @if($otherfield['CODEAPE']->userSkill!=NULL)
                                    <span class="value">{{ $otherfield['CODEAPE']->userSkill->text_input }}</span>
                                @endif
                            @endif
                        </li>
                        <li><span class="label">@if($lang=='FR')Forme juridique  @else Legal form of business @endif
                                :</span>
                            @if(isset($otherfield['LEGALFORM']) && $otherfield['LEGALFORM']->skillFormat->name_en=='Select')
                                @foreach($otherfield['LEGALFORM']->skillSelect as $k=>$val )
                                    @if($otherfield['LEGALFORM']->userSkill!=NULL && $val->id==$otherfield['LEGALFORM']->userSkill->select_input)
                                        <span class="value">{{ $val->option_value }}</span>
                                    @endif
                                @endforeach
                            @endif
                        </li>
                        <li><span class="label">@if($lang=='FR')Adresse @else Address @endif:</span><span
                                    class="value">{{ ($user->address!=NULL)?$user->address:'' }}</span>
                        </li>
                        <li><span class="label">@if($lang=='FR')Code postal @else Zip code @endif :</span><span
                                    class="value">{{ $user->postal }}</span></li>
                        {{--<li><span class="label">@if($lang=='FR')Ville  @else City @endif:</span><span
                                    class="value">{{  ($user->city!=null)?$user->city:'' }}</span>
                        </li>--}}
                    </ul>
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

    <section>
        <div class="card">
            <div class="container">
                <div class="card-details-block">
                    <div class="card-inner">
                        <div class="parent-card-block">
                            <img src="{{url('public/qualification/images/card-icon.png') }}" class="center-block">
                            <div class="card-block">
                                <div class="card-upper-block">
                                    <div class="card-company-block">
                                        @if(isset($user->userSkillCompany->text_input))
                                            @php
                                                $company='';
                                                    $str=$user->userSkillCompany->text_input;
                                                        $exp=explode(' ',$str);
                                                      if(strlen($exp[0]) > 18){
                                                      $trimstring = substr($str, 0, 18). '...';
                                                        $company=$trimstring;
                                                      }else{
                                                        $company=$exp[0]. '...';
                                                      }

                                            @endphp
                                            <span class="company-name">{{$company }}</span>
                                        @endif
                                        <span class="zip-code">{{ $user->postal }}</span>
                                    </div>
                                    <div class="card-upper-logo">
                                        <img src="{{str_replace('https://','http://',$workshop->workshop_logo) }}"
                                             class="center-block img-responsive" alt="Workshop logo">
                                    </div>
                                </div>
                                <div class="card-lower-block">
                                    <div class="card-bottom-logo">
                                        <img src="{{url('public/qualification/images/carte-pro.png') }}"
                                             class="center-block img-responsive">
                                        <div class="card-issue-date">
                                            <span>{{isset($date['deliverydate_orig'])?($date['deliverydate_orig']):(\Carbon\Carbon::now()->format('Y'))}}</span>
                                            <span>{{isset($date['expdeliverydate_orig'])?($date['expdeliverydate_orig']):(\Carbon\Carbon::now()->addYear(1)->format('Y'))}}</span>
                                            {{-- <span>{{\Carbon\Carbon::now()->addYear(1)->format('Y')}}</span> --}}
                                        </div>
                                    </div>
                                    <div class="domians-name">
                                        <ul>
                                            @foreach($domain as $k=>$item)

                                               {{-- @if($domain->where('id',$item->id)->first()) --}}
                                                    {{-- <li><span style="background: #fddc21;color: #0f4c97; --}}
                                                    {{-- font-weight: 700; --}}
                                                    {{-- font-family: 'Qanelas-Bold';" class="active">{{ $item->name }}</span></li> --}}
                                               {{-- @else --}}
                                                    <li>{{ $item->name }}</li>
                                               {{-- @endif --}}
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-detail">
                        <ul>
                            <li><span class="card-label">Carte TP Pro-Artisan : </span><span
                                        class="card-value"> {{$cardNo}}</span>
                            </li>
                            <li><span class="card-label"> @if($lang=='FR') Émise le @else Delivered on @endif
                                    : </span><span class="card-value">
								@if(@$date['deliverydate'] && @$date['expdeliverydate'] && $date['deliverydate']!=NULL && $date['expdeliverydate']!=NULL)
                                        {{ $date['deliverydate'] }}
                                    @else
                                        24 janvier 2019
                                    @endif
								</span></li>
                            <li><span class="card-label"> @if($lang=='FR') Date de validité @else Expiration date @endif
                                    : </span><span class="card-value">
								@if(@$date['deliverydate'] && @$date['expdeliverydate'] && $date['deliverydate']!=NULL && $date['expdeliverydate']!=NULL)
                                        {{ $date['expdeliverydate'] }}
                                    @else
                                        31 janvier 2020
                                    @endif

								</span></li>
                            <li><span class="card-label"> @if($lang=='FR') Par  @else By @endif : </span><span
                                        class="card-value"> {{ $workshop->workshop_name }}</span></li>
                            <li><p><span class="card-value">{{ $workshop->signatory['signatory_fname']. ' ' .$workshop->signatory['signatory_lname'] }}
                                        </span> <span
                                            class="card-label"> {{ $workshop->signatory['signatory_possition'] }}</span>
                                </p></li>
                            <li>
                                <figure>
                                    @if(!empty($workshop->signatory['signatory_signature']))
                                        <img src="{{str_replace('https://','http://',env('AWS_PATH').$workshop->signatory['signatory_signature'])}}"
                                             alt="Signatory Signature">
                                    @endif
                                </figure>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
    </section>
</main>
<footer>

</footer>
</body>
</html>