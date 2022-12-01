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
                           <h4 class="pdf-doc-title">Ordre du jour</h4>
                        </div>
                        <div class="col-xs-6 text-right">
                          <h4 class="text-uppercase pdf-doc-name">{{ @$workshop_data->workshop_name }}</h4>
                          <span class="pdf-small"> 
                            @php 

                            $startDay = date('l', strtotime($meeting_data->date));
                            $exp=explode('-',$meeting_data->date);
                            $expSTime=explode(':',$meeting_data->start_time);
                            if(count($exp)>0) { 
                            echo $days[$startDay].' '.$exp[2].' '.$month[intval($exp[1]) ].' '.$exp[0].' à '.$expSTime[0].':'.$expSTime[1];}

                            @endphp
                          </span>
                        </div>
                     </div>
                     <div class="agenda-content seprator site-color">
                        <ul class="mt-10">
                          <li>Président : {{ getWorkshopMember($workshop_data,1) }}</li>
                          <li>Rédacteur : {{ $meeting_data->redacteur }}</li>
                          <li>Date d'émission : {{ dateConvert(null,'d/m/Y') }}</li>
                       </ul>
                     </div>
                     <div class="agenda-inner-content">
                           @if(count($topic) > 0)
                              @foreach($topic as $parent_key=>$parent_val)
                                 @php 
                                    $parent_no = $parent_key+1;
                                 @endphp
                                 <ul class="mt-10">
                                    <li class="seprator pt-10 mb-10">
                                       <strong>{{ $parent_no }}. {{ $parent_val->topic_title }}</strong>
                                       {{ documents($parent_val->docs) }}
                                       <ul>
                                          @if(count($parent_val->children) > 0)
                                             @foreach($parent_val->children as $child_key=>$child_val)
                                                 @php 
                                                   $child_no = $parent_no.'.'.($child_key+1); 
                                                 @endphp
                                                   <li>
                                                     {{ $child_no }}. {{ $child_val->topic_title }}
                                                     {{ documents($child_val->docs) }}                                            
                                                     <ul>
                                                         @if(count($child_val->children) > 0)
                                                             @foreach($child_val->children as $grandchild_key=>$grandchild_val)
                                                                 @php 
                                                                   $grandchild_no = $child_no.'.'.($grandchild_key+1); 
                                                                 @endphp
                                                                 <li>
                                                                     {{ $grandchild_no }}. {{ $grandchild_val->topic_title }}
                                                                     {{ documents($grandchild_val->docs) }}                                  
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

   @endphp
@endsection           