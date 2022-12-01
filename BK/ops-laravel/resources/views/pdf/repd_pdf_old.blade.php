@extends('layouts.master_pdf')
@section('content')
@php
$month = array('1'=>'Janvier','2'=>'Février','3'=>'Mars','4'=>'Avril','5'=>'Mai','6'=>'Juin','7'=>'Juillet','8'=>'Août','9'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Décembre');
$days = array('Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche');
@endphp
<style>
    tr,.decision-box {page-break-inside: avoid; }
</style>
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
                <li>Secrétaire : {{ getWorkshopMember($workshop_data,1) }}</li>
                <li>Rédacteur : {{ $meeting_data->redacteur }}</li>
                <li>Date d'émission : {{ (!empty($meeting_data->repd_published_on) && $meeting_data->repd_published_on!='0000-00-00 00:00:00')?(dateConvertpdf($meeting_data->repd_published_on,'d-m-Y')):(dateConvertpdf(null,'d-m-Y')) }}</li>
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
        <div class="agenda-inner-content mt-50 pb-10 clearfix">           
            @if(count($topic) > 0)
            @foreach($topic as $parent_key=>$parent_val)
            @php 
            $parent_no = $parent_key+1;
            @endphp
            <ul class="mt-10">
                <li class="pt-10 mb-30">
                    <strong>{{ $parent_no }}. {{ $parent_val->topic_title }}</strong>
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

                                <div class="pt-10 upcoming-meet">
                                    <h5 class="site-color mt-30">Prochaines réunions</h5>
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
                                            @foreach($next3Meetings as $key=>$val)
                                            
                                            <tr>
                                                <td>{{ $val->name }}</td>
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

                          /*  function getWorkshopMember($workshop_data,$role){
                            $docs_urls=[];       
                            if($workshop_data){
                            foreach($workshop_data->meta as $val){
                            if($val->role==$role && $val->user){
                            echo $val->user->fname.' '.$val->user->lname;
                            }
                            }
                            }
                            }
*/
                            function presence_list($presence_data,$type){

                            if(count($presence_data)>0){
                            $print_table=0;
                            foreach ($presence_data as $val){
                            if($val['presence_status']==$type)
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
                                        <th style='width:250px;'>NOM</th>
                                        <th style='width:250px;'>PRENOM</th>
                                        <th style='width:200px;'>SOCIETE</th>
                                        <th style='width:200px;'>OP Représentée</th>
                                    </tr>
                                </thead><tbody>";

                                    foreach ($presence_data as $val){
                                    $union=(count($val['presence_user']['union'])>0)?$val['presence_user']['union'][0]['union_code']:'';
                                    if($val['presence_status']==$type){
                                    if(!empty($val['presence_user'])){
                                    echo "<tr>
                                        <td>".$val['presence_user']['lname']."</td>
                                        <td>".$val['presence_user']['fname']."</td>
                                         <td>".$val['presence_user']['society']."</td>
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