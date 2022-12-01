@extends('qualification::layouts.layoutattest')
@section('content')
    @php
        $skill_tab_formats = array(
          array('id' => '1','short_name' => 'checkbox_input'),
          array('id' => '2','short_name' => 'yes_no_input'),
          array('id' => '3','short_name' => 'percentage_input'),
          array('id' => '4','short_name' => 'scale_10_input'),
          array('id' => '5','short_name' => 'scale_5_input'),
          array('id' => '6','short_name' => 'text_input'),
          array('id' => '7','short_name' => 'file_input'),
          array('id' => '8','short_name' => 'select_input'),
          array('id' => '9','short_name' => 'numerical_input'),
          array('id' => '10','short_name' => 'long_text_input'),
          array('id' => '11','short_name' => 'date_input'),
          array('id' => '12','short_name' => 'mandatory_checkbox_input'),
          array('id' => '13','short_name' => 'mandatory_file_input'),
          array('id' => '14','short_name' => 'comment_text_input'),
          array('id' => '15','short_name' => 'address_text_input'),
          array('id' => '16','short_name' => 'blank_line'),
          array('id' => '17','short_name' => 'mandatory_acceptance_input'),
          array('id' => '18','short_name' => 'conditional_checkbox_input'),
          array('id' => '19','short_name' => 'radio_input'),
          array('id' => '20','short_name' => 'referrer_input'),
          array('id' => '21','short_name' => 'file_input'),
          array('id' => '22','short_name' => 'file_input')
        );
    //dd($custom);
    $arr = [];
        foreach ($custom as $item) {
            $key = array_search($item['skill_format_id'],array_column($skill_tab_formats,'id'));
            // dd($key);
            if($key == 17){
                $key = 0;
                $arr[] = array('short_name' => $item['short_name'], 'value'=> $item['user_skill'][$skill_tab_formats[$key]['short_name']]);
            }elseif(in_array($key,[13])){
                $arr[] = array('short_name' => $item['short_name'], 'value'=> ($item['skill_meta']['value']));
            }elseif($key == 18){
                $key = 7;
                $arr[] = array('short_name' => $item['short_name'], 'value'=> $item['user_skill'][$skill_tab_formats[$key]['short_name']],'select'=>$item['skill_select']);
            }elseif($key == 7){
                $arr[] = array('short_name' => $item['short_name'], 'value'=> $item['user_skill'][$skill_tab_formats[$key]['short_name']],'select'=>$item['skill_select']);
            }elseif($key == 14){
                $arr[] = ['short_name' => $item['short_name'], 'value'=> $item['user_skill'][$skill_tab_formats[$key]['short_name']],'original'=>$item['user_skill']['original_address_text_input']];
            }else{
                $arr[] = array('short_name' => $item['short_name'], 'value'=> $item['user_skill'][$skill_tab_formats[$key]['short_name']]);
                //rint_r($arr);
            }
        }
    //dd($arr);
    @endphp
    <header id="header" class="clearfix">
        <div class="inner-wrap flexbox">
            <div class="header-col-left">
                <img src="{{url('public/qualification/attest/carte_tppro_artisan.jpg')}}" class=""/>
            </div>
            <div class="header-col-center">
                <div class="">
                    <h1><strong>Attestation</strong>De Travaux{{-- {{ now()->year }}--}}</h1>
                </div>
            </div>
            <div class="header-col-right">
                {{-- <img src="{{url('public/qualification/attest/ffb-logo.png')}}" class=""/> --}}
            </div>
        </div>
    </header>
    {{-- {{dd($result)}} --}}
    {{-- {{dd($custom[10]['user_skill']['text_input'])}} --}}

    <table style="width: 100%;">
        <tbody>
        <tr>
            <td>
                <main class="clearfix">
                    <section id="" class="clearfix">
                        <div class="inner-wrap clearfix">
                            <div class="sec-first-detail">
                                <div class="sec1-left-col">
                                    J’atteste que l’entreprise
                                </div>
                                <div class="sec1-right-col">
                                    <div class="sec1-right-first-col">
                                        <div class="label-detail">
                                            <ul>
                                                <li><span class="label"></span>
                                                    @if(isset($basic['candidate']['user_skill_company']['text_input']))
                                                        {{--    @php
                                                                    $company='';
                                                                        $str=$basic['candidate']['user_skill_company']['text_input'];
    $company    =$str;                                                           $exp=explode(' ',$str);
                                                                          if(strlen($exp[0]) > 18){
                                                                          $trimstring = substr($str, 0, 18). '...';
                                                                            $company=$trimstring;
                                                                          }else{
                                                                            $company=$exp[0]. '...';
                                                                          }

                                                            @endphp--}}
                                                        <span class="value">{{$basic['candidate']['user_skill_company']['text_input']?? 'Martin & Fils'}}</span>
                                                    @endif
                                                </li>
                                                <li><span class="label">SIRET :</span>
                                                    @if(isset($basic['candidate']['user_skill_siret']['numerical_input']))
                                                        <span class="value">{{$basic['candidate']['user_skill_siret']['numerical_input']}}</span>
                                                    @endif
                                                </li>

                                                <li><span class="label">Adresse : </span>
                                                    @if(isset($basic['candidate']['address']))
                                                        <span class="value">{{$basic['candidate']['address'] ?? ''}}</span>
                                                    @endif
                                                </li>

                                                <li><span class="label">Code postal:</span>
                                                    @if(isset($basic['candidate']['postal'] ))
                                                        <span class="value">{{$basic['candidate']['postal'] ?? ''}}</span>
                                                </li>
                                                @endif
                                                {{-- <li>
                                                    <span class="label">Ville :</span>
                                                    @if(($basic['candidate']['postal'] ))
                                                        <span class="value">{{$basic['candidate']['city'] ?? ''}}</span>
                                                    @endif
                                                </li> --}}

                                            </ul>
                                        </div>
                                    </div>

                                    <div class="sec1-right-second-col">
                                        <h3>a bien réalisé pour moi des travaux dans le domaine suivant :</h3>
                                        <ul>
                                            @if(isset($domain['name']) && ($domain['name']))
                                                <li class="active">{{(isset($domain['name']))? $domain['name']: ''}}</li>
                                                <!-- <li>Aménagements, voiries et routes</li> -->

                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="building-img-bg" class="clearfix">
                        <img src="{{url('public/qualification/attest/building-icons-img.jpg')}}" width="100%"/>
                    </section>

                    <section id="works-details" class="clearfix">
                        <div class="inner-wrap clearfix">
                            <div class="work-detail-top-sec clearfix flexbox">
                                <div class="works-details-left">
                                    Détails des travaux
                                </div>
                                <div class="work-place text-right">
                                    <h5>Lieu des travaux</h5>
                                    {{-- {{dd(array_search('Lieudestravaux', array_column($arr, 'short_name')))}} --}}

                                    <div class="address text-right txt-value-field">
                                        <span class="text-label">Adresse:</span>
                                        @php
                                            $searchKey=array_search('Lieudestravaux', array_column($arr, 'short_name'));
                                            // dd($arr[$searchKey]['value']);
                                           if(isset($searchKey) && $searchKey!==FALSE){
                                                echo  $arr[$searchKey]['value'];
                                            }
                                        @endphp
                                        {{-- Adresse:{{(isset($arr[0]['value'])) ? $arr[0]['value'] :''}} --}}

                                        {{-- dd($arr[$searchKey]['value']); --}}
                                    </div>
                                    {{-- {{dd(array_search('Lieudestravaux', array_column($arr, 'short_name')))}} --}}
                                    {{-- @php
                                    array_walk($arr,function($val,$k){
                                    return ($val);
                                    })
                                    @endphp --}}
                                    {{-- {{ dd(array_search("Lieudestravaux",$arr))}} --}}
                                    <div class="postal-code text-right txt-value-field">
                                        <span class="text-label">Code postal:</span>
                                        @php
                                            $searchKey=array_search('Lieudestravaux', array_column($arr, 'short_name'));
                                            // dd($arr[$searchKey]['value']);
                                           if(isset($searchKey) && $searchKey!==FALSE){
                                                if(isJson($arr[$searchKey]['original'])){
                                                 $decode = json_decode($arr[$searchKey]['original'], TRUE);
                                                 if(isset($decode['zip_code']))
                                                   {
                                                    echo $decode['zip_code'];
                                                   }
                                                }
                                            }
                                        @endphp
                                        {{--  @if(isset($basic['referrer']['zip_code']))
                                              {{isset($basic['referrer']['zip_code']) ? $basic['referrer']['zip_code'] : ''}}
                                          @endif--}}
                                    </div>
                                    {{-- <div class="ville text-right txt-value-field">
                                        <span class="text-label">Ville:</span>
                                        @php
                                            $searchKey=array_search('City', array_column($arr, 'short_name'));
                                            // dd($arr[$searchKey]['value']);
                                            if(isset($arr)){
                                                echo  $arr[$searchKey]['value'];
                                            }
                                        @endphp
                                    </div> --}}
                                </div>
                                <div class="work-dates">
                                    <h5>Début des travaux</h5>
                                    <div class="date-start">
                                        @php
                                            $searchKey=array_search('Débutdestravaux', array_column($arr, 'short_name'));
                                            // dd($arr[$searchKey]['value']);
                                            if(isset($searchKey) && $searchKey!==FALSE){
                                                echo  getCreatedAtAttribute($arr[$searchKey]['value']);
                                            }
                                        @endphp
                                    </div>
                                    <h5>Fin des travaux</h5>
                                    <div class="date-end">
                                        @php
                                            $searchKey=array_search('Findestravaux', array_column($arr, 'short_name'));
                                            // dd($arr[$searchKey]['value']);
                                            if(isset($searchKey) && $searchKey!==FALSE){
                                                echo  getCreatedAtAttribute($arr[$searchKey]['value']);
                                            }
                                        @endphp
                                    </div>
                                </div>
                                {{-- {{dd($arr[7]['value'])}} --}}
                                <div class="service-review">
                                    <h5>Appréciation des prestations</h5>
                                    <div class="service-rank">
                                        @php
                                            $searchKey=array_search('Appréciationdesprestations', array_column($arr, 'short_name'));
                                            $selectKey=array_search($arr[$searchKey]['value'],array_column($arr[$searchKey]['select'],'id'));
                                        @endphp
                                        @if(isset($arr[$searchKey]['select']) && (!empty($arr[$searchKey]['select'])))
                                            @foreach($arr[$searchKey]['select'] as $k=>$val)
                                                <div class="radio-style1">
                                                    <label>
                                                        <input type="radio" name="prestations" id="" value='0'
                                                                {{(isset($selectKey) && $selectKey==$k) ? 'class=active checked'  : '' }} />
                                                        {{-- <input type="radio" name="service_rank" id="" value=1 class=""  @if(($arr[7]['value']['select_input']) == 1) ? 'checked="checked"'  : 'hello' @endif /> --}}
                                                        <span class="radio-icon"></span>
                                                        {{$val['option_value']}}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="work-detail-bottom-sec clearfix flexbox">
                                <div class="work-detail-btm-lsec text-right">
                                    <div class="txt-value-field">
                                        <span class="text-label">Montant des travaux TTC (en €) :</span>
                                        @php
                                            $searchKey=array_search('Montantdestravaux', array_column($arr, 'short_name'));
                                            // dd($arr[$searchKey]['value']);
                                            if(isset($searchKey) && $searchKey!==FALSE){
                                                echo  $arr[$searchKey]['value'];
                                            }

                                        @endphp
                                    </div>
                                    <div class="txt-value-field">
                                        <span class="text-label">Des travaux ont-ils été sous-traités ?</span>

                                        <div class="radio-inline">
                                            @php
                                                $searchKey=array_search('Travauxsoustraités', array_column($arr, 'short_name'));

                                            @endphp
                                            <div class="radio-style1">
                                                @if(isset($arr[$searchKey]['value']) && (!empty($arr[$searchKey]['value'])))


                                                    <label>

                                                        <input type="radio" name="service_rank" id="" class=""
                                                               value='1' {{(isset($arr) && ($arr[$searchKey]['value'])) ? "checked" : ""  }} {{($arr[$searchKey]['value']==0 && ($val['option_value']=='Non' || $val['option_value']=='No'))? "checked" : "" }}/>
                                                        <span class="radio-icon"></span>
                                                        OUI
                                                    </label>
                                                    <label>

                                                        <input type="radio" name="service_rank" id="" class=""
                                                               value='1' {{(isset($arr) && ($arr[$searchKey]['value']==0)) ? "checked" : ""  }}/>
                                                        <span class="radio-icon"></span>
                                                        Non
                                                    </label>

                                                @else
                                                    <label>

                                                        <input type="radio" name="service_rank" id="" class=""
                                                               value='1'/>
                                                        <span class="radio-icon"></span>
                                                        Yes
                                                    </label>
                                                    <label>

                                                        <input type="radio" name="service_rank" id="" class=""
                                                               value='1' checked/>
                                                        <span class="radio-icon"></span>
                                                        Non
                                                    </label>
                                                @endif
                                            </div>
                                            {{-- @if(isset($arr[$searchKey]['select']) && (!empty($arr[$searchKey]['select'])))
                                                 @foreach($arr[$searchKey]['select'] as $val)

                                              <div class="radio-style1">
                                                  <label>

                                                      <input type="radio" name="service_rank" id="" class=""
                                                          value='1' {{(isset($arr) && ($arr[$searchKey]['value'] == $val['id'])) ? "checked" : ""  }} {{($arr[$searchKey]['value']==0 && ($val['option_value']=='Non' || $val['option_value']=='No'))? "checked" : "" }}/>
                                                      <span class="radio-icon"></span>
                                                   {{$val['option_value']}}
                                                  </label>
                                              </div>
                                                  @endforeach
                                              @endif--}}
                                            {{--                                                <div class="radio-style1">--}}
                                            {{--                                                    <label>--}}
                                            {{--                                                        @php--}}
                                            {{--                                                            $searchKey=array_search('Travauxsoustraités', array_column($arr, 'short_name'));--}}
                                            {{--                                                            // dd($arr[$searchKey]['value']);--}}

                                            {{--                                                        @endphp--}}
                                            {{--                                                        --}}{{-- <input type="radio" name="service_rank" id="" class="" value='0' {{ ($arr[3]['value']['select_input']) ?? "checked" }} /> --}}
                                            {{--                                                        <input type="radio" name="service_rank" id="" class=""--}}
                                            {{--                                                            value='0' {{(isset($arr) && ($arr[$searchKey]['value'] == '0' || empty($arr[$searchKey]['value']) )) ? "checked" : ""  }} />--}}
                                            {{--                                                        <span class="radio-icon"></span>--}}
                                            {{--                                                        NON--}}
                                            {{--                                                    </label>--}}
                                            {{--                                                </div>--}}
                                        </div>
                                    </div>
                                    {{-- {{dd($arr)}} --}}
                                    <div class="txt-value-field">
                                        <span class="text-label">Si oui, montant des travaux sous-traités (en €) :</span>
                                        @php
                                            $searchKey=array_search('Montantdestravauxsoustraités', array_column($arr, 'short_name'));
                                            // dd($arr[$searchKey]['value']);
                                             if(isset($searchKey) && $searchKey!==FALSE){
                                                echo  $arr[$searchKey]['value'];
                                            }
                                        @endphp
                                    </div>
                                    <div class="txt-value-field">
                                        <span class="text-label">Nature des travaux sous-traités :</span>
                                        @php
                                            $searchKey=array_search('Naturedestravauxsoustraités', array_column($arr, 'short_name'));
                                            // dd($arr[$searchKey]['value']);
                                          if(isset($searchKey) && $searchKey!==FALSE){
                                                echo  $arr[$searchKey]['value'];
                                            }
                                        @endphp

                                    </div>
                                </div>
                                <div class="work-detail-btm-rsec">
                                    <label>Commentaires : </label>
                                    @php
                                        $searchKey=array_search('Commentaires', array_column($arr, 'short_name'));
                                        //dd($arr[$searchKey]['value']);
                                    @endphp
                                    <div class="textarea">{{isset($arr[$searchKey]['value']) ? strip_tags($arr[$searchKey]['value']):''}}</div>
                                </div>
                            </div>
                        </div>
                    </section>
                    {{-- {{dd($custom[8]['user_skill']['long_text_input'])}} --}}
                    <section id="" class="clearfix">
                        <div class="inner-wrap clearfix">
                            <div class="main-detail-sec clearfix flexbox">
                                <div class="main-detail-sec-lsec text-right">
                                    <div class="txt-value-field">
                                        <span class="text-label">Vous êtes :</span>
                                        <div class="radio-inline">
                                            <div class="radio-style1">
                                                <label>
                                                    <input type="radio" name="you_are" id="" class=""
                                                           value='1' {{(isset($basic['referrer']['referrer_type']) && $basic['referrer']['referrer_type'] == '1')? "checked" : "" }} />
                                                    <span class="radio-icon"></span>
                                                    Maître d'ouvrage
                                                </label>
                                            </div>
                                            <div class="radio-style1">
                                                <label>
                                                    <input type="radio" name="you_are" id="" class=""
                                                           value='2' {{(isset($basic['referrer']['referrer_type']) && $basic['referrer']['referrer_type'] == '2') ? "checked" : "" }} />
                                                    <span class="radio-icon"></span>
                                                    Maître d'oeuvre
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- {{dd($custom[7]['user_skill']['select_input'])}} --}}
                                    <div class="txt-value-field">
                                        <span class="text-label">Nom de la Structure:</span>
                                        @if(isset($basic['referrer']['company']))
                                            <span class="text-value">{{isset($basic['referrer']['company']) ? $basic['referrer']['company'] : ''}}</span>
                                        @endif
                                    </div>
                                    <div class="txt-value-field address-block">
                                        <span class="text-label">Adresse :</span>
                                        @if(isset($basic['referrer']['address']))
                                            <span class="text-value">{{isset($basic['referrer']['address']) ? $basic['referrer']['address'] : ''}}</span>
                                        @endif
                                    </div>
                                    {{-- <div class="txt-value-field">
                                        <span class="text-label">Ville :</span>
                                        @if(isset($basic['referrer']['city']))
                                            <span class="text-value">{{isset($basic['referrer']['city']) ? $basic['referrer']['city'] : ''}}</span>
                                        @endif
                                    </div> --}}
                                    <div class="txt-value-field">
                                        <span class="text-label">Téléphone :</span>
                                        @if(isset($basic['referrer']['phone']))
                                            <span class="text-value">{{isset($basic['referrer']['phone']) ? $basic['referrer']['phone'] : ''}}</span>
                                        @endif
                                    </div>
                                    <div class="txt-value-field">
                                        <span class="text-label">Email :</span>
                                        @if(isset($basic['referrer']['email']))
                                            <span class="text-value">{{isset($basic['referrer']['email']) ? $basic['referrer']['email'] : ''}}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="main-detail-sec-rsec">
                                    <div class="txt-value-field">
                                        <span class="text-label">Prénom :</span>
                                        @if(isset($basic['referrer']['fname']))
                                            <span class="text-value">{{isset($basic['referrer']['fname'])  ? $basic['referrer']['fname'] :''}}</span>
                                        @endif
                                    </div>
                                    <div class="txt-value-field">
                                        <span class="text-label">Nom :</span>
                                        @if(isset($basic['referrer']['lname']))
                                            <span class="text-value">{{isset($basic['referrer']['lname']) ? $basic['referrer']['lname'] : ''}}</span>
                                        @endif
                                    </div>
                                    {{-- {{dd($basic['referrer']['lname'])}} --}}
                                    <div class="txt-value-field">
                                        <span class="text-label">Fonction :</span>
                                        @if(isset($basic['referrer']['position']))
                                            <span class="text-value">{{isset($basic['referrer']['position']) ? $basic['referrer']['position'] : ''}}</span>
                                        @endif
                                    </div>
                                    <div class="txt-value-field">
                                        <span class="text-label">Date:</span>
                                        @if(isset($basic['referrer']['date']))
                                            <span class="text-value">{{isset($basic['referrer']['date'])  ? $basic['referrer']['date'] : getCreatedAtAttribute(\Carbon\Carbon::now()->format('Y-m-d'))}}</span>
                                        @else
                                            <span class="text-value">{{ getCreatedAtAttribute(\Carbon\Carbon::now()->format('Y-m-d'))}}</span>
                                        @endif
                                    </div>
                                    {{-- <div class="txt-value-field">
                                        <span class="text-label">Signature :</span>
                                        <div class="signature-block">
                                            <div class="txt-value-field">
                                                @if(isset($basic['referrer']['fname']))
                                                    <span class="text-value">{{isset($basic['referrer']['fname']) ? $basic['referrer']['fname'] :''}}</span>
                                                @endif
                                                @if(isset($basic['referrer']['lname']))
                                                    <span class="text-value">{{isset($basic['referrer']['lname']) ? $basic['referrer']['lname'] : ''}}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div> --}}
                                    {{-- {{dd($custom[7]['user_skill']['select_input'])}} --}}
                                </div>
                            </div>
                        </div>
                    </section>
                </main>
            </td>
        </tr>
        </tbody>
    </table>
@endsection

{{-- <div class="work-place text-right">
     <h5>Lieu des travaux</h5>
     <div class="address text-right">
         @if(($custom[0]['short_name'] && $custom[0]['short_name'] == 'Work_Address' ))
             Adresse:{{($custom[0]['user_skill']['address_text_input']) ?? ''}}
         @endif
     </div>

     <div class="postal-code text-right">
         @if(($custom[9]['short_name'] && $custom[9]['short_name'] == 'Postal_code' ))
             Code postal:{{([$custom][9]['user_skill']['numerical_input']) ?? ''}}
         @endif
     </div>
     <div class="ville text-right">
         @if(($custom[10]['short_name'] && $custom[10]['short_name'] == 'City' ))
             Ville:{{($custom[10]['user_skill']['text_input']) ?? ''}}
         @endif
     </div>

 </div>
 <div class="work-dates">
     <h5>Début des travaux</h5>
     <div class="date-start">
         @if(($custom[1]['short_name'] && $custom[1]['short_name'] == 'Start_Date' ))
             {{($custom[1]['user_skill']['date_input']) ?? ''}}
         @endif
     </div>
     <h5>Fin des travaux</h5>
     <div class="date-end">
         @if(($custom[2]['short_name'] && $custom[2]['short_name'] == 'End_Date'))
             {{($custom[2]['user_skill']['date_input']) ?? ''}}
         @endif
     </div>
 </div>
 <div class="service-review">
     <h5>Appréciation des prestations</h5>
     <div class="service-rank">
         <div class="radio-style1">
             <label>
                 --}}{{-- {{ dd($custom[7]['user_skill'])}} --}}{{--
                 <input type="radio" name="service_rank" id="" value=1
                        class="" {{($custom[7]['user_skill']['select_input'] == '1') ?? 'checked' }} />
                 --}}{{-- <input type="radio" name="service_rank" id="" value=1 class=""  @if(($custom[7]['user_skill']['select_input']) == 1) ? 'checked' : 'hello' @endif /> --}}{{--
                 <span class="radio-icon"></span>
                 Excellent
             </label>
         </div>
         <div class="radio-style1">
             <label>
                 <input type="radio" name="service_rank" id="" value=2
                        class="" {{($custom[7]['user_skill']['select_input'] == '2') ?? 'checked'}} />
                 --}}{{-- <input type="radio" name="service_rank" id="" value=1 class=""  @if(($custom[7]['user_skill']['select_input']) == 1) ? 'checked' : 'hello' @endif /> --}}{{--
                 <span class="radio-icon"></span>
                 Bien
             </label>
         </div>
         <div class="radio-style1">
             <label>
                 --}}{{-- <input type="radio" name="service_rank" id="" value=3 class=""  @if(($custom[7]['user_skill']['select_input']) == 3) ?? 'checked' @endif /> --}}{{--
                 <input type="radio" name="service_rank" id="" value=3
                        class="" {{($custom[7]['user_skill']['select_input'] == '3') ? 'checked' : ''}} />
                 <span class="radio-icon"></span>
                 Moyen
             </label>
         </div>
         <div class="radio-style1">
             <label>
                 --}}{{-- <input type="radio" name="service_rank" id="" value=4 class=""  @if(($custom[7]['user_skill']['select_input']) == 4) ?? 'checked'  @endif /> --}}{{--
                 <input type="radio" name="service_rank" id="" value=4
                        class="" {{($custom[7]['user_skill']['select_input'] == '4') ? 'checked' : ''}} />
                 <span class="radio-icon"></span>
                 Médiocre
             </label>
         </div>
     </div>
 </div>
</div>


<div class="work-detail-bottom-sec clearfix flexbox">
 <div class="work-detail-btm-lsec text-right">
     <div class="txt-value-field">
         <span class="text-label">Montant des travaux TTC</span>
         @if(($custom[6]['short_name'] && $custom[6]['short_name'] == 'Cost_of_work' ))
             <span class="text-value">{{$custom[6]['user_skill']['numerical_input'] ?? ''}}</span>
         @endif
     </div>

     <div class="txt-value-field">
         <span class="text-label">Des travaux ont-ils été sous-traités ?</span>

         <div class="radio-inline">
             <div class="radio-style1">
                 <label>
                     --}}{{-- <input type="radio" name="service_rank" id="" class="" value='1' {{ ($custom[3]['user_skill']['select_input']) ?? "checked"}} /> --}}{{--
                     <input type="radio" name="service_rank" id="" class=""
                            value='1' {{ ($custom[3]['user_skill']['select_input'] == "1")? "checked" : "" }} />
                     <span class="radio-icon"></span>
                     OUI
                 </label>
             </div>
             <div class="radio-style1">
                 <label>
                     --}}{{-- <input type="radio" name="service_rank" id="" class="" value='0' {{ ($custom[3]['user_skill']['select_input']) ?? "checked" }} /> --}}{{--
                     <input type="radio" name="service_rank" id="" class=""
                            value='0' {{ ($custom[3]['user_skill']['select_input'] == "0")? "checked" : "" }} />
                     <span class="radio-icon"></span>
                     NON
                 </label>
             </div>
         </div>
     </div>

     <div class="txt-value-field">
         <span class="text-label">Si oui, montant des travaux sous-traités :</span>
         @if(($custom[5]['short_name'] && $custom[5]['short_name'] == 'Cost_if_yes' ))
             <span class="text-value">{{$custom[5]['user_skill']['numerical_input']?? ''}}</span>
         @endif
     </div>

     <div class="txt-value-field">
         <span class="text-label">Nature des travaux sous-traités :</span>
         @if(($custom[4]['short_name'] && $custom[4]['short_name'] == 'Work' ))
             <span class="text-value">{{$custom[4]['user_skill']['text_input']?? ''}}</span>
         @endif
     </div>

 </div>
 <div class="work-detail-btm-rsec">
     @if(($custom[8]['short_name'] && $custom[8]['short_name'] == 'Comments'  ))
         <label>Commentaires : </label>
         <textarea>{{$custom[8]['user_skill']['long_text_input']}}</textarea>
     @endif
 </div>
</div>
</div>
</section>
--}}{{-- {{dd($custom[8]['user_skill']['long_text_input'])}} --}}{{--
<section id="" class="clearfix">
<div class="inner-wrap clearfix">
<div class="main-detail-sec clearfix flexbox">
 <div class="main-detail-sec-lsec text-right">
     <div class="txt-value-field">
         <span class="text-label">Vous êtes :</span>
         <div class="radio-inline">
             <div class="radio-style1">
                 <label>

                     <input type="radio" name="you_are" id="" class=""
                            value='1' {{ ($basic['referrer']['referrer_type'] == '1')? "checked" : "" }} />
                     <span class="radio-icon"></span>
                     Maître d’ouvrage
                 </label>
             </div>
             <div class="radio-style1">
                 <label>
                     <input type="radio" name="you_are" id="" class=""
                            value='2' {{ ($basic['referrer']['referrer_type'] == '2')? "checked" : "" }} />
                     <span class="radio-icon"></span>
                     Maître d’oeuvre
                 </label>
             </div>
         </div>
     </div>
     <div class="txt-value-field">
         <span class="text-label">Entreprise :</span>
         @if(($basic['referrer']['company'] ?? ''))
             <span class="text-value">{{$basic['referrer']['company'] ?? ''}}</span>
         @endif
     </div>

     <div class="txt-value-field address-block">
         <span class="text-label">Adresse :</span>
         @if(isset($basic['referrer']['address']))
             <span class="text-value">{{$basic['referrer']['address'] ?? ''}}</span>
         @endif
     </div>
     <div class="txt-value-field">
         <span class="text-label">Ville :</span>
         @if(isset($basic['referrer']['city']))
             <span class="text-value">{{$basic['referrer']['city'] ?? ''}}</span>
         @endif
     </div>
     <div class="txt-value-field">
         <span class="text-label">Téléphone :</span>
         @if(isset($basic['referrer']['telephone']))
             <span class="text-value">{{$basic['referrer']['telephone'] ?? ''}}</span>
         @endif
     </div>
     <div class="txt-value-field">
         <span class="text-label">Email :</span>
         @if($basic['referrer']['email'])
             <span class="text-value">{{$basic['referrer']['email'] ?? ''}}</span>
         @endif
     </div>
 </div>
 <div class="main-detail-sec-rsec">
     <div class="txt-value-field">
         <span class="text-label">Prénom :</span>
         @if($basic['referrer']['fname'])
             <span class="text-value">{{$basic['referrer']['fname'] ?? ''}}</span>
         @endif
     </div>
     <div class="txt-value-field">
         <span class="text-label">Nom :</span>
         @if($basic['referrer']['lname'])
             <span class="text-value">{{$basic['referrer']['lname'] ?? ''}}</span>
         @endif
     </div>
     <div class="txt-value-field">
         <span class="text-label">Fonction :</span>
         @if($basic['referrer']['position'])
             <span class="text-value">{{$basic['referrer']['position'] ?? ''}}</span>
         @endif
     </div>
     <div class="txt-value-field">
         <span class="text-label">Date:</span>
         @if(isset($basic['referrer']['date']))
             <span class="text-value">{{$basic['referrer']['date']  ?? ''}}</span>
         @endif
     </div>
     <div class="txt-value-field">
         <span class="text-label">Signature :</span>
         <div class="signature-block">
             <div class="txt-value-field">
                 @if($basic['referrer']['fname'])
                     <span class="text-value">{{$basic['referrer']['fname'] ?? ''}}</span>
                 @endif
                 @if($basic['referrer']['lname'])
                     <span class="text-value">{{$basic['referrer']['lname'] ?? ''}}</span>
                 @endif
             </div>

         </div>
     </div>
     --}}{{-- {{dd($custom[7]['user_skill']['select_input'])}} --}}{{--
 </div>
</div>
</div>
</section>
</main>
@endsection--}}