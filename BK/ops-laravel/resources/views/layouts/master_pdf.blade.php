<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">
    <head>
        <title>OP Simplify</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
        <meta content="#000000" name="theme-color">
        <link href="{{ URL::asset('public/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>  
        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        @php
            $css_data = dynamicCss();
            $color1 = $css_data['color1'];
            $color2= $css_data['color2'];
            $color3= $css_data['color3'];
            $transprancy7=$css_data['transprancy7'];
            $transprancy1=$css_data['transprancy1'];
            $transprancy2=$css_data['transprancy2'];
        @endphp
        <style type="text/css">
            .agenda-heading h4 {
                /*letter-spacing: 1px;*/
            }
            .pdf-logo{
                margin-bottom: 10px;
                max-height: 62px;
            }
            .site-color {
                color: {{$color1}} !important;
            }
            .text-uppercase {
                text-transform: uppercase;
            }
            .mt-10 {margin-top: 10px!important;}
            .mt-30 {margin-top: 30px!important;}
            .mt-50 {margin-top: 50px!important;}
            
            .mb-10 {margin-bottom: 10px!important;}
            .mb-20 {margin-bottom: 20px!important;}
            
            .pt-5 {padding-top: 5px !important;}
            .pt-10 {padding-top: 10px !important;}
            
            .pb-10 {padding-bottom: 10px !important;}
            .agenda-header{
                max-height: 62px;
            }
            .text-center {
                text-align: center;
            }
            .agenda-content ul, .agenda-inner-content>ul {
                -webkit-padding-start: 0;
                list-style-type: none;
            }
            #ViewDecisionPDF
            {
                font-family: 'Open Sans', sans-serif;
            }
             .agenda-inner-content p {
                        white-space: pre-wrap;
                        padding-right: 10px;
                        padding-left: 10px;
                    } 
            .decision-box span{
                        white-space: pre-wrap;
                    }
            .seprator {
                border-top: 1px solid #000;
            }
            .header-line {
                height: 30px;
                width: 100%;
                background: {{$color1}};
            }
            .agenda-content ul {
                color: #000;
            }
            .agenda-content ul li{
                color: #000;
                font-size: 11.5px;
                line-height: 22px;
            }
            .table-style2 h5 {
                padding: 4px 0 4px 9px;
                margin: 0;
                background: rgba(0,0,0,0.39);
            }
            .white-text {
                color: #ffffff !important;
            }
            h5 {
                font-size: 16px;
            }
            .table-style2>.table>thead>tr>th, .table-style2>.table>tbody>tr>td {
                padding: 0px 8px;
                border-bottom: 2px solid #000;
            }
            .table-style2>.table>thead>tr>th, .table-style2>.table>tbody>tr>td {
                padding: 0px 8px;
                border-bottom: 2px solid #000;
            }
            .table-style2>.table>tbody>tr>td {
                background: rgba(0,0,0,0.04);
                color: #000;
            }
            .decision-box {
                /* border: 1px solid #4f81bd; */
                border: 1px solid #000;
                padding-left: 10px;
            }
            .decision-box span {
                font-style: italic;
            }
            .decision-box h5 strong {
                /* border-bottom: 2px solid #4e74b0; */
                border-bottom: 2px solid #000;
                padding-bottom: 1px;
                /* color: #4e74b0; */
                color: #000;
                font-size: 10pt;
                margin-bottom: 10px;
                display: inline-block;
            }
            .agenda-inner-content ul li {
                list-style-type: none;
            }
            .doc-attach {
                float: right;
            }
            .doc-attach a {
                text-decoration: underline;
                padding-right: 3px;
                font-size: 12px;
                color: #4e74b0;
            }
            .upcoming-meet {
                border-top: 3px solid {{$color1}};
            }
            .table>tbody>tr>td {
                padding: 8px 8px;
                color: #000;
                font-size: 12px;
            }

            .pdf-doc-title {
                color: #000;
                font-size: 18px;
                margin: 0;
                text-transform: uppercase;
                font-weight: bold;
                float: left;
                font-family: 'Open Sans', sans-serif;
            }
            .pdf-doc-name
            {
                color: #000;
                font-size: 18px;
                margin: 0;
                font-weight: bold;
            }
            .pdf-small {
                color: #000;
                font-size: 11.5px;
            }
            .agenda-inner-content ul li{
                font-size: 16px;
            }
            .member-table-heading{
                /* background: #4e74b0; */
                background: #000;
                color: #fff;
                padding: 2px 16px;
                font-weight: bold;
                font-size: 11.5px;
            }
            .member-table thead tr th {
                color: #000;
                font-size: 11px;
                font-weight: bold;
                padding: 0;
            }
            .member-table thead tr th, .member-table tr td {
                text-align: left;
                padding-left: 16px !important;
            }
            .member-table thead tr th, .member-table tr td,
            .upcoming-meet thead tr th, .upcoming-meet tr td {
                border-bottom: 1px solid #000;
            }
            .member-table tr td {
                font-size: 12px;
                /* color: #7f7f7f; */
                color: #000;
                padding: 6px 0 6px 0;
            }
            .upcoming-meet h5 {
                /* color: #4e74b0; */
                color: #000;
                font-size: 14px;
                font-weight: bold;
            }
            .child-block{
                padding-left: 0 !important;
            }
            .repd-topic{
                padding-left: 28px;
                margin-top: 20px;
            }
            .decision{
                color: #000;
                font-weight: bold;
                font-size: 15px;
                font-style: italic;
                margin-bottom: 1px;
            }
            .pdfBtn svg path{
                fill: {{$color1}};
            }
            @media print{
                .container .must-break {
                    page-break-before: always;
                }
            }
        </style>                        
    </head>
    <body>
        <div class="app-body">
            <main class="main">
                <div id="ViewDecisionPDF">               
                    @yield('content')
                </div>
            </main>
        </div>
    </body>
</html>