@extends('layouts.master_pdf')
@section('content')
    @php
        \App::setLocale($lang);
            $month = array('1'=>'Janvier','2'=>'Février','3'=>'Mars','4'=>'Avril','5'=>'Mai','6'=>'Juin','7'=>'Juillet','8'=>'Août','9'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Décembre');
            $days = array('Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche');
    @endphp
    <div class="container tab-section">
        <div class="col-xs-12 nopadding tab-menu-content">
            <div class="agenda-header"><img class="img-responsive pdf-logo" src="{{$settings_data['header_logo']}}">
            </div>
            <div class="header-line" style="{!! $settings_data['color1'] !!}"></div>
            <div class="agenda-heading mt-30 mb-20 clearfix">
                <div class="col-xs-6">
                    <h4 class="pdf-doc-title text-uppercase">@lang('message.ins_heading')</h4>
                </div>
                <div class="col-xs-6 text-right">
                    <h4 class="pdf-doc-name text-uppercase">{{ @$workshop_data->workshop_name }}</h4>
                    <span class="pdf-small">
                    @php

                            $exp=explode('-',$meeting_data->date);
                            if(count($exp)>1) { echo $exp[2].' '.$month[intval($exp[1])].' '.$exp[0];}
                    @endphp
                </span>
                </div>
            </div>
            <div class="agenda-content seprator site-color mb-20">
                <ul class="mt-10">
                    {{--<li style='font-size: 16px; line-height:27px;'>Président--}}
                    {{--: {{ getWorkshopMember($workshop_data,1) }}</li>--}}
                    <li style='font-size: 16px; line-height:27px;'>@lang('message.redacteur') : {{ $meeting_data->redacteur }}</li>
                    <li style='font-size: 16px; line-height:27px;'>@lang('message.issue_date')
                        : {{ dateConvert(null,'d-m-Y') }}</li>
                </ul>
            </div>
            <div class="decision-agenda agenda-value mb-20 mt-20">
                <div class="table-style1">
                    @if(count($presence_data)>0)
                        <table class='table table-hover table-style2 member-table'>
                            <thead>
                            <tr>
                                <th style='width:250px; padding-bottom: 15px; font-size: 16px;'>@lang('message.name')</th>
                                <th style='width:250px; padding-bottom: 15px; font-size: 16px;'>@lang('message.presence')</th>
                                @if(isset($meeting_data->meeting_type) && $meeting_data->meeting_type==3)
                                <th style='width:250px; padding-bottom: 15px; font-size: 16px;'>@lang('message.vid_presence')</th>
                                @endif
                                <th style='width:250px; padding-bottom: 15px; font-size: 16px;'>@lang('message.signature')</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($presence_data as $val)
                                <tr>
                                    <td style='padding: 22px 8px; font-size: 16px; color:#000;'>{{$val->user->lname or ''}}
                                        &nbsp;{{$val->user->fname or ''}}</td>
                                    <td style='padding: 22px 8px; font-size: 16px; color:#000;'> {{ presentStatus($val)}}</td>
                                    @if(isset($meeting_data->meeting_type) && $meeting_data->meeting_type==3) <td style='padding: 22px 8px; font-size: 16px; color:#000;'> {{ hybridPresentStatus($val)}}</td>@endif
                                    <td style='padding: 22px 8px; font-size: 16px; color:#000;'></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @php

            @endphp
    <style>
        .agenda-value tbody tr:last-child td {
            border-bottom: 1px solid #ddd;
        }
        .agenda-value thead{
            display: table-header-group;
            width: 100%;
            -webkit-print-color-adjust: exact !important;
            /*background-color: blue;*/
        }
        .agenda-value tr{page-break-inside: avoid }
        thead { display: table-header-group }
        tfoot { display: table-row-group }
    </style>
@endsection