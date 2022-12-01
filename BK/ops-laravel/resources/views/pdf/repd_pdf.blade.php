@extends('layouts.master_pdf')
@section('content')
<style>
    @media print {
        a[href]:after {
            display: none;
            visibility: hidden;
        }
    }
</style>
    @php
        \App::setLocale($lang);
            $month = array('1'=>'Janvier','2'=>'Février','3'=>'Mars','4'=>'Avril','5'=>'Mai','6'=>'Juin','7'=>'Juillet','8'=>'Août','9'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Décembre');
            $days = array('Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche');

    @endphp
    <style>
        tr, .decision-box {
            page-break-inside: avoid;
        }

        .pdfBtn svg {
            width: 30px;
            height: 30px;
        }

        .pdfBtn svg path {
            fill: {!! $settings_data['pdfcolor1'] !!};
        }

        .site-color {
            color: {!! $settings_data['pdfcolor1'] !!}   !important;
        }

        .header-line {
        {!! $settings_data['color1'] !!}


        }

        .seprator {
            border-top: 1px solid {!! $settings_data['pdfcolor1'] !!}   !important;
        }

        .upcoming-meet {
            border-top: 3px solid {!! $settings_data['pdfcolor1'] !!}   !important;
        }
    </style>
    <div class="container tab-section">
        <div class="col-xs-12 nopadding tab-menu-content">
            <div class="agenda-header"><img class="img-responsive pdf-logo" src="{{str_replace('https://','http://',$settings_data['header_logo'])}}">
            </div>
            <div class="header-line" style="{!! $settings_data['color1'] !!}"></div>
            <div class="row">
                <div class="agenda-heading mt-30 mb-20 clearfix">
                    <div class="col-xs-6">
                        <h4 class="pdf-doc-title text-uppercase">@lang('message.repd')</h4>
                    </div>
                    <div class="col-xs-6 text-right">
                        <h4 class="pdf-doc-name text-uppercase">{{ @$workshop_data->workshop_name }}</h4>

                        <span class="pdf-small">
                        @php
                            $exp=explode('-',$meeting_data->date);
                            if(count($exp)>0) {
                            $lang = session()->has('lang') ? session()->get('lang') : "FR";
                             if($lang == 'EN'){
                    echo \Carbon\Carbon::parse($meeting_data->date)->format('F d, Y');
                    }else{
                     echo $exp[2].' '.$month[intval($exp[1])].' '.$exp[0];
                            }
                            }
                        @endphp
                    </span>
                    </div>
                </div>
            </div>
            <div class="agenda-content seprator site-color">
                <ul class="mt-10">
                    {{--<li>Président : {{ getWorkshopMember($workshop_data,1) }}</li>--}}
                    <li>@lang('message.redacteur') : {{ $meeting_data->redacteur }}</li>
                    <li>@lang('message.issue_date')
                        : {{ (!empty($meeting_data->repd_published_on) && $meeting_data->repd_published_on!='0000-00-00 00:00:00')?(dateConvertpdf($meeting_data->repd_published_on,'d-m-Y')):(dateConvertpdf(null,'d-m-Y')) }}</li>
                </ul>
            </div>
            <div class="decision-agenda mb-20">
                <div class="table-style2">
                    {{presence_list($presence_data,'P',$companyLabel,$membershipLabel)}}
                </div>
            </div>
            <div class="decision-agenda mb-20">
                <div class="table-style2">
                    {{presence_list($presence_data,'AE',$companyLabel,$membershipLabel)}}
                </div>
            </div>
            <div class="decision-agenda mb-20">
                <div class="table-style2">
                    {{presence_list($presence_data,'ANE',$companyLabel,$membershipLabel)}}

                </div>
            </div>

            <div class="must-break"></div>
            <div class="agenda-inner-content mt-50 pb-10 clearfix">
                @if(count($topic) > 0)
                    @foreach($topic as $parent_key=>$parent_val)
                        @php
                            $parent_no = $parent_key+1;
                        @endphp
                        <ul class="mt-10">
                            <li class="pt-10 mb-30">
                                {{--this due to fake REPD from server side will change in future BCZ need to use localization(SP)--}}
                                @if (count($topic) == 1 && $meeting_data->is_import == 1 && $parent_no==1)

                                    <strong>{{ $parent_no }}. {{$parent_val->topic_title}} </strong>
                                    <br>
                                    <strong>@lang('message.no_dec_rec_uploaded')/strong>
                                @else
                                    <strong>{{ $parent_no }}. {{$parent_val->topic_title}} </strong>
                                @endif
                                {{ documents($parent_val->docs) }}

                                {{ discussion($parent_val->discussion) }}
                                <div class="decision">{{ decision($parent_val->decision) }}</div>
                                <ul class="child-block">
                                    @if(is_array($parent_val->children) && count($parent_val->children) > 0)

                                        @foreach($parent_val->children as $child_key=>$child_val)
                                            @php
                                                $child_no = $parent_no.'.'.($child_key+1);
                                            @endphp
                                            <li class="repd-topic">
                                                <div>
                                                    {{ $child_no }}. {{ $child_val->topic_title }}
                                                    {{ documents($child_val->docs) }}
                                                </div>
                                                <div>
                                                    {{ discussion($child_val->discussion) }}
                                                    <div class="decision">{{ decision($child_val->decision) }}</div>
                                                    <div>
                                                        <ul class="grand-child-block">

                                                            @if(is_array($child_val->children) && count($child_val->children) > 0)
                                                                @foreach($child_val->children as $grandchild_key=>$grandchild_val)
                                                                    @php
                                                                        $grandchild_no = $child_no.'.'.($grandchild_key+1);
                                                                    @endphp
                                                                    <li class="repd-topic">
                                                                        <div>
                                                                            {{ $grandchild_no }}
                                                                            . {{ $grandchild_val->topic_title }}
                                                                            {{ documents($grandchild_val->docs) }}
                                                                        </div>
                                                                        {{ discussion($grandchild_val->discussion) }}
                                                                        <div class="decision">{{ decision($grandchild_val->decision) }}</div>
                                                                    </li>
                                                                @endforeach
                                                            @endif
                                                        </ul>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </li>
                        </ul>
                    @endforeach
                @endif
            </div>
            @if($document_data->count()>0)
                <div class="pt-10 upcoming-meet">
                    <h5 class="site-color mt-30">@lang('message.meeting_doc')</h5>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>>@lang('message.doc_name')</th>
                            <th>>@lang('message.doc_type')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($document_data as $element)
                            @php
                                $path_info = pathinfo($element->document_file);
                            @endphp
                            @if(isset($path_info['extension']))
                                <tr>
                                    <td>{{$element->document_title}}</td>
                                    <td>{{$path_info['extension']}}</td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                    <div class="text-left">
                        <a href="{{(count($document_data)>0)?url('get-zip-download/'.$wid):'#'}}" target="_blank">
                            <i class="pdfBtn">
                                <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                                     width="297.000000pt" height="297.000000pt"
                                     viewBox="0 0 297.000000 297.000000"
                                     preserveAspectRatio="xMidYMid meet">
                                    <g transform="translate(0.000000,297.000000) scale(0.100000,-0.100000)"
                                       fill="#000000" stroke="none">
                                        <path d="M410 2890 c-7 -13 -10 -171 -10 -467 l1 -448 -176 -170 -176 -170 1
	                                                -612 c0 -595 1 -613 19 -623 11 -6 87 -10 175 -10 l156 0 0 -156 c0 -88 4
	                                                -164 10 -175 10 -19 33 -19 1075 -19 1042 0 1065 0 1075 19 6 11 10 87 10 175
	                                                l0 156 156 0 c88 0 164 4 175 10 18 10 19 28 19 623 l1 612 -177 170 -176 171
	                                                2 183 1 184 -283 283 -282 284 -793 0 c-778 0 -792 0 -803 -20z m1493 -347 c1
	                                                -191 5 -255 15 -265 10 -10 78 -14 270 -18 l257 -5 0 -300 0 -300 -960 0 -960
	                                                0 -3 560 c-1 308 0 566 3 573 3 10 147 12 690 10 l685 -3 3 -252z m439 -155
	                                                c-5 -5 -77 -7 -161 -6 l-152 3 0 153 c0 83 1 156 1 160 0 4 72 -62 160 -147
	                                                88 -85 157 -158 152 -163z m463 -1368 l0 -505 -1320 0 -1320 0 -3 495 c-1 272
	                                                0 501 3 508 3 10 273 12 1322 10 l1318 -3 0 -505z m-360 -750 l0 -115 -960 0
	                                                -960 0 -3 104 c-1 58 0 111 2 118 5 11 176 13 963 11 l958 -3 0 -115z"/>
                                        <path d="M1755 1352 l-50 -7 -3 -357 -2 -358 70 0 70 0 0 133 0 132 79 2 c169
	                                                4 271 93 271 239 0 88 -39 152 -117 190 -45 23 -69 27 -161 29 -59 2 -129 0
	                                                -157 -3z m218 -116 c52 -22 72 -51 72 -107 0 -41 -5 -53 -32 -81 -30 -29 -40
	                                                -33 -103 -36 l-70 -4 0 115 c0 83 4 117 13 120 24 10 88 7 120 -7z"/>
                                        <path d="M810 1290 l0 -60 158 -2 159 -3 -179 -259 c-155 -225 -178 -264 -178
	                                                -298 l0 -38 275 0 275 0 0 60 0 60 -175 0 c-96 0 -175 2 -175 5 0 3 79 119
	                                                175 259 156 227 175 259 175 295 l0 41 -255 0 -255 0 0 -60z"/>
                                        <path
                                                d="M1420 990 l0 -360 70 0 70 0 0 360 0 360 -70 0 -70 0 0 -360z"/>
                                    </g>
                                </svg>
                            </i>
                        </a>
                    </div>
                </div>
            @endif
            <div class="pt-10 upcoming-meet">
                <h5 class="site-color mt-30">@lang('message.next_meeting')</h5>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>@lang('message.meeting_name')</th>
                        <th>@lang('message.meeting_loc')</th>
                        <th>@lang('message.meeting_date')</th>
                        <th>@lang('message.meeting_hr')</th>
                    </tr>
                    </thead>
                    <tbody>

                    @if(count($next3Meetings)>0)
                        @foreach($next3Meetings as $key=>$val)

                            <tr>
                                <td>{{ $val->name }}</td>
                                <td>{{ chop($val->place,'France') }}</td>
                                @if($val->date!=null)
                                    <td>{{dateConvertpdf($val->date,'d/m/Y') }}</td>
                                @else
                                    <td>
                                        @foreach($val->doodleDates as $doodledate)
                                            <p>{{ dateConvertpdf($doodledate->date,'d/m/Y') }}</p>
                                        @endforeach
                                    </td>
                                @endif
                                <td>

                                    @php
                                        if($val->start_time!=null){
                                        $exp = explode(':',$val->start_time);
                                        echo (count($exp)>0) ? $exp[0].'h'.$exp[1] : '';
                                        }
                                        else{
                                           foreach($val->doodleDates as $doodledate)
                                           {
                                            $exp = explode(':',$doodledate->start_time);
                                    @endphp
                                    <p>{{ (count($exp)>0) ? $exp[0].'h'.$exp[1] : ''}}</p>
                                    @php }
                                                    }
                                    @endphp  </td>
                            </tr>

                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>




    </div>
    </div>

    @php

        function documents($docs){

            $docs_urls=[];
            if(count($docs)>0){
            foreach($docs as $val){

            if(isset($val->docs->is_active) && $val->docs->is_active){
            $docs_urls[]= '<a target="_blank" href="'.url('/').'/download-document?url='.$val->docs->document_file.'&docid='.$val->document_id.'">'.getDateYear($val->docs->created_at,'y').str_pad($val->docs->increment_number,3,"0",STR_PAD_LEFT).'</a>';
            }

            }
            echo '<div class="doc-attach">'.join(', ',$docs_urls).'</div>';

            }
        }

        function discussion($discussion){
            if($discussion != ''){
            echo '<p>'.str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$discussion).'</p>';
            }
        }

        function decision($decision){
            if($decision != ''){
            echo '<div class="decision-box pt-5 pb-10">
                <h5 class="site-color"><strong>'.__('message.decision').'</strong></h5>
                <span><strong>'.str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$decision).'</strong></span>
            </div>';
            }
        }


function presence_list($presence_data,$type,$companyLabel,$membershipLabel){

            if(count($presence_data)>0){
                $print_table=0;
                foreach ($presence_data as $val){
                    if($val['presence_status']==$type)
                    $print_table++;
                }

                if($print_table>0){
                    if($type=="P"){
                        echo '<h5 class="white-text member-table-heading">'.__('message.participants').'</h5>';
                    } elseif($type == "AE") {
                        echo '<h5 class="white-text member-table-heading">'.__('message.AE').'</h5>';
                    }else{
                        echo '<h5 class="white-text member-table-heading">'.__('message.ANE').'</h5>';
                    }
                    if(session()->get('lang')=='EN'){
$company_label=$companyLabel['en_name'];
$membership_label=$membershipLabel['en_name'];
}else{
$company_label=$companyLabel['fr_name'];
$membership_label=$membershipLabel['fr_name'];
}echo "<table class='table table-hover member-table'>
                        <thead>
                            <tr>
                                <th style='width:250px;'>".__('message.name')."</th>
                                <th style='width:250px;'>".__('message.pre_name')."</th>
                                <th style='width:200px;'>$company_label</th>
                                <th style='width:200px;'>$membership_label</th>
                            </tr>
                        </thead>
                        <tbody>";
                        foreach ($presence_data as $val){
                            if($val['presence_status']==$type){
                                if(!empty($val['presence_user'])){
                                $union=isset($val['presence_user']['union'][0])?$val['presence_user']['union'][0]['union_code']:'';
                                $entity=isset($val['presence_user']['entity'][0])?$val['presence_user']['entity'][0]['long_name']:'';

                                echo "<tr>
                                    <td>".$val['presence_user']['lname']."</td>
                                    <td>".$val['presence_user']['fname']."</td>
                                    <td>".$entity."</td>
                                     <td>".$union."</td>
                                </tr>";
                                }
                            }
                        }
                    echo "</tbody></table>";
                }
            }
        }
    @endphp

@endsection