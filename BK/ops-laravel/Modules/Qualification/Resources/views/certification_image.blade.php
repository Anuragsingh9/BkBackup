<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
        main section .container {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        body, html, main, section, .container {
            height: 100%;
        }

        @font-face {
            font-family: 'QanelasSemiBold';
            src: url('../public/fonts/qualification/QanelasSemiBold.eot') ;
            src: url('../public/fonts/qualification/QanelasSemiBold.eot') format('embedded-opentype'),
            url('../public/fonts/qualification/QanelasSemiBold.woff2') format('woff2'),
            url('../public/fonts/qualification/QanelasSemiBold.woff') format('woff'),
            url('../public/fonts/qualification/QanelasSemiBold.ttf') format('truetype'),
            url('../public/fonts/qualification/QanelasSemiBold.svg#QanelasSemiBold') format('svg');
        }

        @font-face {
            font-family: 'QanelasExtraBold';
            src: url('../public/fonts/qualification/QanelasExtraBold.eot');
            src: url('../public/fonts/qualification/QanelasExtraBold.eot') format('embedded-opentype'),
            url('../public/fonts/qualification/QanelasExtraBold.woff2') format('woff2'),
            url('../public/fonts/qualification/QanelasExtraBold.woff') format('woff'),
            url('../public/fonts/qualification/QanelasExtraBold.ttf') format('truetype'),
            url('../public/fonts/qualification/QanelasExtraBold.svg#QanelasExtraBold') format('svg');
        }

        @import url('https://fonts.googleapis.com/css?family=Open+Sans:400,600,700&display=swap');

        @page {
            size: A4;
            margin: 0;
        }

        * {
            margin: 0px;
            padding: 0px;
            border: none;
            outline: none;
            list-style: none;
        }

        body {
            overflow-x: hidden;
            width: 100%;
            font-family: 'Open Sans', sans-serif;
        }

        header {
            margin-top: 60px;
        }

        .container {
            max-width: 767px;
            margin: auto;
        }

        .cp-logo {
            width: 50%;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }

        .org-logo {
            width: 19%;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }

        .workshop-logo {
            width: 29%;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }

        .cp-logo h3 {
            font-family: 'QanelasSemiBold';
            color: #0f4d98;
            font-size: 22px;
            text-align: left;
            padding-left: 34px;
        }

        .cp-logo h1 {
            padding-left: 30px;
            text-align: left;
            color: #0f4d98;
            font-size: 46px;
            margin-bottom: 0;
            margin-top: 0;
            line-height: 35px;
            font-family: 'QanelasExtraBold';
        }

        .cp-logo p {
            font-size: 14px;
            margin-top: 10px;
            font-weight: 700;
            color: #0f4c97;
            margin-bottom: 20px;
        }

        img {
            max-width: 100%;
        }

        .org-logo img {
            width: 52px;
        }

        .cp-logo img {
            width: 316px;
        }

        .workshop-logo img {
            width: 146px;
        }

        .banner-section {
            background: url({{ url('public/qualification/images/sky.png') }}) no-repeat center;
            height: 300px;
            background-size: cover;
        }

        .banner-inner-section {
            max-width: 460px;
            margin: auto;
            color: #fff;
            padding-top: 60px;
        }

        .banner-inner-section h3 {
            padding-left: 22px;
            margin: 10px 0px 15px;
            font-size: 13px;
            font-weight: 400;
        }

        .banner-inner-section li {
            font-size: 17px;
            color: #a3aad5;
        }

        .banner-inner-section span {
            padding: 5px 22px;
            display: inline-block;
        }

        .banner-inner-section span.active {
            background: #fddc21;
            color: #0f4c97;
            font-weight: 700;
        }

        .detail {
            max-width: 460px;
            margin: auto;
            margin-top: 15px;
            margin-bottom: 20px;
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
            font-size: 14px;
            color: #0e4d98;
            display: block;
            margin-top: 1px;
            margin-bottom: 3px;
            font-weight: 400;
        }

        span.value, .qualification ul li {
            font-size: 14px;
            font-weight: 700;
            color: #0e4d98;
            word-break: break-word;
        }

        .qualification ul li {
            padding: 6px 0px;
        }

        .card-inner {
            width: 100%;
        }

        .card-detail {
            width: 52%;
        }

        .card-inner, .card-detail {
            display: inline-block;
            vertical-align: top;
        }

        .card-detail {
            margin-left: 25px;
        }

        .card {
            /*padding-left: 140px;*/
            /*padding-top: 30px;*/
            text-align: center;
        }


        .card-label {
            font-size: 14px;
            color: #0e4d98;
            margin-top: 1px;
            margin-bottom: 2px;
            font-weight: 400;
            display: inline-block;
        }

        .card-value {
            font-size: 14px;
            font-weight: 700;
            color: #00499a;
        }

        .card-detail img {
            width: 210px;
        }

        .card-detail ul li p {
            padding: 12px 0px 10px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            background: #0e4d98;
            width: 100%;
            left: 0px;
            right: 0px;
            height: 20px;
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
            width: 64%;
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
            padding-left: 24px;
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
            font-size: 20px;
            line-height: 26px;
            word-break: break-word;
        }
        .zip-code{
            margin-top: 5px;
        }
        .card-upper-logo {
            width: 35%;
            float: left;
            position: relative;
            min-height: 30px;
            padding: 0 3px;
            box-sizing: border-box;
        }

        .card-lower-block {
            height: 55%;
        }

        .card-bottom-logo {
            float: left;
            width: 27%;
            padding: 40px 0px 10px 0px;
        }

        .card-issue-date {
            font-size: 15px;
            color: #fff;
            margin-top: 15px;
        }

        .card-issue-date span {
            width: 100%;
            display: inline-block;
        }

        .domians-name {
            float: left;
            width: 73%;
            display: table;
            height: 100%;
            padding-top: 20px;
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
            width: 85px;
            padding-left: 7px;
        }

        .domians-name ul li {
            font-size: 16px;
            text-align: right;
            padding: 1px 0px;
            padding-right: 15px;
            position: relative;
            margin: 2px 0px;
        }

        .domians-name ul li:after {
            content: '';
            position: absolute;
            width: 9px;
            height: 5px;
            background: gold;
            top: 11px;
            right: 0;
        }

        .card-upper-logo img {
            /* max-width: 100%; */
            max-height: 105px;
            height: auto;
            width: auto;
        }
        .card-inner img.card-img {
            width: 640px;
            max-height: 422px;
            border: 1px solid rgba(0, 74, 152, 0.45);
            border-radius: 27px;
        }

        @media print {
            html, body {
                width: 210mm;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
@php
    $lang = session()->has('lang') ? session()->get('lang') : "FR";
@endphp

<main>

    <div class="card">
        <div class="card-inner">
            <div class="parent-card-block">
                <img src="{{url('public/qualification/images/card-icon.png') }}" class="center-block card-img">
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
                            <span class="company-name">    {{$company }}  </span>
                            @endif

                            <span class="zip-code">{{ $user->postal }}</span>
                        </div>
                        <div class="card-upper-logo" style="background:url('{{ str_replace('https://','http://',$workshop->workshop_logo) }})">
                        <img src="{{ str_replace('https://','http://',$workshop->workshop_logo) }}"
                             class="center-block img-responsive">
                        </div>

                    </div>
                    <div class="card-lower-block">
                        <div class="card-bottom-logo">
                            <img src="{{url('public/qualification/images/carte-pro.png') }}"
                                 class="center-block img-responsive">
                            <div class="card-issue-date">
                                <span>{{isset($date['deliverydate_orig'])?($date['deliverydate_orig']):(\Carbon\Carbon::now()->format('Y'))}}</span>
                                <span>{{isset($date['expdeliverydate_orig'])?($date['expdeliverydate_orig']):(\Carbon\Carbon::now()->addYear(1)->format('Y'))}}</span>
                            </div>
                        </div>
                        <div class="domians-name">
                            <ul>

                                @foreach($domain as $k=>$item)

{{--                                    @if($domain->where('id',$item->id)->first())--}}
{{--                                        <li style="background: #fddc21;color: #0f4c97;--}}
{{--    font-weight: 700;--}}
{{--    font-family: 'Qanelas-Bold';" class="active">{{ $item->name }}</li>--}}
{{--                                        @else--}}
                                        <li>{{ $item->name }}</li>
{{--                                    @endif--}}


                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</main>
<footer>

</footer>
</body>
</html>