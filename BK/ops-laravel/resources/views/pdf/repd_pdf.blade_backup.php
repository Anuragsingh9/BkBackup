@extends('layouts.master_pdf')
@section('content')
@php
$month = array('1'=>'Janvier','2'=>'Février','3'=>'Mars','4'=>'Avril','5'=>'Mai','6'=>'Juin','7'=>'Juillet','8'=>'Août','9'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Décembre');
$days = array('Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche');
@endphp
<div class="container tab-section">
    <div class="col-xs-12 nopadding tab-menu-content">
        <div class="agenda-header"><img class="img-responsive pdf-logo" src="{{$settings_data['header_logo']}}"></div>
        <div class="header-line" style="{!! $settings_data['color1'] !!}"></div>
        <div class="agenda-heading mt-30 mb-20 clearfix">
            <div class="col-xs-6">
                <h4 class="pdf-doc-title text-uppercase">RELEVÉ DE Décision</h4>
            </div>
            <div class="col-xs-6 text-right">
                <h4 class="pdf-doc-name text-uppercase">{{ @$workshop_data->workshop_name }}</h4>

                <span class="pdf-small"> 
                    @php 
                    $exp=explode('-',$meeting_data->date);
                    if(count($exp)>0) { echo $exp[2].' '.$month[intval($exp[1])].' '.$exp[0];}
                    @endphp
                </span>
            </div>
        </div>
        <div class="agenda-content seprator site-color">
            <ul class="mt-10">
                <li>Président : {{ getWorkshopMember($workshop_data,1) }}</li>
                <li>Rédacteur : {{ $meeting_data->redacteur }}</li>
                <li>Date d'émission : {{ dateConvert(null,'d-m-Y') }}</li>
            </ul>
        </div>
        <div class="decision-agenda mb-20">
            <div class="table-style2">
                {{presence_list($presence_data,'P')}}
            </div>
        </div>
        <div class="decision-agenda mb-20">
            <div class="table-style2">
                {{presence_list($presence_data,'AE')}}
            </div>
        </div>
        <div class="decision-agenda mb-20">
            <div class="table-style2">
                {{presence_list($presence_data,'ANE')}}
            </div>
        </div>

        <div class="must-break"></div>
        <div class="agenda-inner-content mt-50 pb-10">           
            @if(count($topic) > 0)
            @foreach($topic as $parent_key=>$parent_val)
            @php 
            $parent_no = $parent_key+1;
            @endphp
            <ul class="mt-10">
                <li class="pt-10 mb-10">
                    <strong>{{ $parent_no }}. {{ $parent_val->topic_title }}</strong>
                    {{ documents($parent_val->docs) }}
                    {{ discussion($parent_val->discussion) }} 
                    <div class="decision">{{ decision($parent_val->decision) }}</div>
                    <ul class="child-block">
                        @if(count($parent_val->children) > 0)

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

                                        @if(count($child_val->children) > 0)
                                        @foreach($child_val->children as $grandchild_key=>$grandchild_val)
                                        @php 
                                        $grandchild_no = $child_no.'.'.($grandchild_key+1); 
                                        @endphp
                                        <li class="repd-topic">
                                            <div>
                                                {{ $grandchild_no }}. {{ $grandchild_val->topic_title }}
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

                                <div class="upcoming-meet pt-10">
                                    <h5 class="site-color">Prochaines réunions</h5>
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nom de la réunion</th>
                                                <th>Date</th>
                                                <th>Heure</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @if(count($next3Meetings)>0)
                                            @foreach($next3Meetings as $val)
                                            <tr>
                                                <td>{{ $val->name }}</td>
                                                <td>{{ dateConvert($val->created_at) }}</td>
                                                <td>

                                                    @php
                                                    if($val->start_time!=null){
                                                    $exp = explode(':',$val->start_time);
                                                    echo (count($exp)>0) ? $exp[0].'h'.$exp[1] : '';
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
                            @php 

                            function documents($docs){
                            $docs_urls=[];       
                            if(count($docs)>0){
                            foreach($docs as $val){
                            $docs_urls[]= '<a target="_blank" href="'.url('/').'/download-document?url='.$val->docs->document_file.'&docid='.$val->document_id.'">'.dateConvert($val->docs->created_at,'y').str_pad($val->docs->increment_number,3,"0",STR_PAD_LEFT).'</a>';
                            }
                            echo '<div class="doc-attach">'.join(', ',$docs_urls).'</div>';
                            }
                            }

                            function discussion($discussion){
                            if($discussion != ''){
                            echo '<p>'.$discussion.'</p>';
                            }
                            }

                            function decision($decision){
                            if($decision != ''){
                            echo '<div class="decision-box pt-5 pb-10">
                                <h5 class="site-color"><strong>Décision</strong></h5>
                                <span><strong>'.$decision.'</strong></span>
                            </div>';
                            }
                            }

                            function getWorkshopMember($workshop_data,$role){
                            $docs_urls=[];       
                            if($workshop_data){
                            foreach($workshop_data->meta as $val){
                            if($val->role==$role && $val->user){
                            echo $val->user->fname.' '.$val->user->lname;
                            }
                            }
                            }
                            }

                            function presence_list($presence_data,$type){
                            if(count($presence_data)>0){
                            $print_table=0;
                            foreach ($presence_data as $val){
                            if($val->presence_status==$type)
                            $print_table++;
                            }

                            if($print_table>0){
                            if($type=="P"){ 
                            echo '<h5 class="white-text member-table-heading">Participants</h5>';
                            } elseif($type == "AE") {
                            echo '<h5 class="white-text member-table-heading">Excusés</h5>';
                            }else{
                            echo '<h5 class="white-text member-table-heading">Absent Non Excusés</h5>';
                            }
                            echo "<table class='table table-hover member-table'><thead>
                                    <tr>
                                        <th>NOM</th>
                                        <th>PRÉNOM</th>
                                        <th>OP/SOCIÉTÉ</th>
                                        <th>OP D'APPARTENANCE</th>
                                    </tr>
                                </thead><tbody>";
                                    foreach ($presence_data as $val){
                                    if($val->presence_status==$type){
                                    echo "<tr>
                                        <td>".$val->user->lname."</td>
                                        <td>".$val->user->fname."</td>
                                        <td></td>
                                        <td></td>
                                    </tr>";
                                    }
                                    }
                                    echo "</tbody></table>";
                            }
                            }
                            }
                            @endphp


                            @endsection            