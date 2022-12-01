@extends('qualification::layouts.master_Q')

@section('content')
    {{-- <section class="banner" id="main-banner">
                
        <figure>
            <img src={{url('public/qualification/card-1icon.png')}} class="center-block">
        </figure> 
        <div class="container banner-section">
            <div class="banner-inner-section">   
                <h3>DOMAINS</h3>
                <ul>
                    <li><span class="active">Aménagements, voiries et routes</span></li>
                    <li><span>Terrassement, fondations et reprise en sous-oeuvre</span></li>
                    <li><span>Ouvrages d’art et travaux spéciaux</span></li>
                    <li><span>Travaux d’électrisation et de télécommunications</span></li> 
                </ul>
            </div>
        </div>
    </section> --}}


    <div class="double-border-line"></div>
    <section class="banner" id="banner">
        @if(isset($referrerField))
            <div class="container">
                <div class="welcome-user">
                    <div class="welcome-user-inner">
                        <h5>
                            @if($lang=='FR')
                                Bonjour,
                            @else
                                Hi,
                            @endif
                        </h5>
                        <div class="wlcm-txt">{{ isset($referrerField->referrer->fname)?$referrerField->referrer->fname:'-' }} {{ isset($referrerField->referrer->lname)?$referrerField->referrer->lname:'-' }} {{-- {{ $referrerField->candidate->fname }} {{ $referrerField->candidate->lname }} --}}
                            @if($lang=='FR')
                                vous remercie
                                de bien vouloir l’aider à obtenir la
                                Carte TP Pro en confirmant
                                les travaux réalisés pour vous
                            @else
                                thanks you for helping
                                Getting the Carte TP Pro by confirming the works done for you.
                            @endif
                        </div>
                    </div>
                </div>
                <div class="parent-card-block">
                    @php
                        $image=isset($referrer1->setting['web']['header_logo'])?'https://s3-eu-west-2.amazonaws.com/ooionline.com/'.$referrer1->setting['web']['header_logo']:'https://s3-eu-west-2.amazonaws.com/ooionline.com/sharabh/uploads/3Pk1gtF0ivJwIyZvuRBYdTvzVeF2V6mtkSIKvjZJ.jpeg'
                    @endphp
                    <img src="http://sharabh.ooionline.com/public/qualification/card-icon.png" class="center-block">
                    <div class="card-block">
                        <div class="card-upper-block">
                            <div class="card-company-block">
                                <span class="company-name">
                                     @if(isset($referrerField->candidate->userSkillCompany->text_input))
                                      {{--  @php
                                            $company='';
                                                $str=$referrerField->candidate->userSkillCompany->text_input;
                                                    $exp=explode(' ',$str);
                                                  if(strlen($exp[0]) > 18){
                                                  $trimstring = substr($str, 0, 18). '...';
                                                    $company=$trimstring;
                                                  }else{
                                                    $company=$exp[0]. '...';
                                                  }

                                        @endphp--}}
                                       {{$referrerField->candidate->userSkillCompany->text_input }}
                                         @else
                                        {{ '-'}}
                                    @endif
                                    </span>

                            </div>
                            <div class="card-upper-logo">
                                <img src="{{$image}}"
                                     class="center-block img-responsive">
                            </div>
                        </div>

                        <div class="card-lower-block">
                            <div class="card-bottom-logo">
                                <img src="http://sharabh.ooionline.com/public/qualification/carte-pro.png"
                                     class="center-block img-responsive">
                            </div>
                            <div class="domians-name">
                                <ul>
                                    @php
                                        $i=0;
                                    @endphp
                                    @foreach($getAdminStepFields->fields as $k=>$item)
                                        @if(isset($item->skillFormat) && (in_array($item->skillFormat->name_en,['Conditional CheckBox','Checkbox','Conditional CheckBox','Mandatory Checkbox'])))
                                            {{--@if($i<=6)--}}
                                            @php
                                                $i=$i+1;
                                            @endphp
                                            @if(isset($item->userSkill) && $item->userSkill!=null && $item->userSkill->checkbox_input==1)
                                                <li>
                                                    <span class={{ ((isset($item->userSkill)&&$item->userSkill!=null&&$item->userSkill->checkbox_input==1))?"active":'' }}>{{ $item->name }}</span>
                                                </li>
                                            @endif
                                            {{--@endif--}}
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
    @if(isset($referrerField))
        <section class="data-view-block">
            <div class="container">
                <h4 class="heading-txt">
                    @if($lang=='FR')
                        Cette demande vous est envoyée par :
                    @else
                        This Request Comes From:
                    @endif
                </h4>
                <div class="row">
                    <div class="text-value-row">
                        <div class="col-md-3 text-label">
                            {{__('candidate_name')}} :

                        </div>
                        <div class="col-md-6 text-value">{{ isset($referrerField->candidate->fname)?$referrerField->candidate->fname:'-' }} {{ isset($referrerField->candidate->lname)?$referrerField->candidate->lname:'-' }}</div>
                    </div>
                </div>
                <div class="row">
                    <div class="text-value-row">
                        <div class="col-md-3 text-label">{{__('candidate_company')}} :</div>
                        <div class="col-md-6 text-value">  @if(isset($referrerField->candidate->userSkillCompany->text_input))
                                @php
                                    $company='';
                                        $str=$referrerField->candidate->userSkillCompany->text_input;
                                            $exp=explode(' ',$str);
                                          if(strlen($exp[0]) > 18){
                                          $trimstring = substr($str, 0, 18). '...';
                                            $company=$trimstring;
                                          }else{
                                            $company=$exp[0]. '...';
                                          }

                                @endphp
                                {{$company }}
                            @else
                                {{ '-'}}
                            @endif</div>
                    </div>
                </div>
                <div class="row">
                    <div class="text-value-row">
                        <div class="col-md-3 text-label">{{__('candidate_email')}} :</div>
                        <div class="col-md-6 text-value">{{isset($referrerField->candidate->email)?$referrerField->candidate->email:'-' }}</div>
                    </div>
                </div>
            </div>

            <div class="container form-candidate">
                <div class="clearfix mb-20">
                    <h4 class="heading-txt">
                        @if($lang=="FR")
                            1. Merci de confirmer que le demandeur a bien effectué pour vous des travaux
                            de {{ isset($referrerField->domain->step->name)?$referrerField->domain->step->name:'-' }} en
                            :
                        @else
                            1. Thanks to confirm that the requester has done
                            these {{ isset($referrerField->domain->step->name)?$referrerField->domain->step->name:'-' }}
                            works for
                            you, by
                            :
                        @endif
                    </h4>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-12 nopadding">
                            @if($lang=='FR')
                                téléchargeant ici
                            @else
                                Downloading here
                            @endif
                        </label>
                        <a target="_blank" class="btn btn-primary" href={{ asset('img/FFB-AttestationTP-PRO.pdf') }} >
                            @if($lang=='FR')
                                Télécharger
                            @else
                                Download
                            @endif
                        </a>
                    </div>
                </div>
                <h4 class="heading-txt">
                    @if($lang=='FR')
                        2. l’attestation que vous voudrez bien remplir, signer et nous renvoyer grâce au bouton
                        ci-dessous.
                    @else
                        2. The attestation that please you can fill, sign and send back using the button
                        below
                    @endif
                </h4>

                <form method="post" action="{{ url('qualification/referrer-upload-file') }}"
                      enctype='multipart/form-data'>
                    {{-- <input type="hidden" name="refreer_id" value=""/>
                    <input type="hidden" name="field_id" value=""/> --}}
                    {{-- <input type="hidden" name="id" value=""/> --}}
                    {{-- <input type="hidden" name="id" value="{{ $referrerField->refreer_id }}"/> --}}
                    <input type="hidden" name="refreer_id" value="{{ $referrerField->refreer_id }}"/>
                    <input type="hidden" name="field_id" value="{{ $referrerField->field_id }}"/>
                    <input type="hidden" name="id" value="{{ $referrerField->id }}"/>
                    <div class="form-group row">
                        <label for="exampleFormControlFile1" class="col-md-3">File Browse</label>
                        {{-- <input type="file" name="file" required class="form-control-file col-md-9"
                               id="exampleFormControlFile1">
 --}}
                        <div class="box">
                            <input type="file" name="file" id="exampleFormControlFile1" class="inputfile inputfile-6"/>
                            <label for="exampleFormControlFile1"><span></span> <strong>
                                    @if($lang=='FR')
                                        Choisissez votre fichier
                                    @else
                                        Choose a file
                                    @endif
                                </strong></label>
                        </div>
                    </div>
                    <div class="clearfix">
                        <button class="btn btn-primary">
                            @if($lang=='FR')
                                Envoyer l’attestation
                            @else
                                Send the attestation
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </section>
    @else
        @if(isset($error))
            <div class="container">
                <div class="error-block">
                    <h3>{{$error}}</h3>

                </div>
            </div>
        @else
            <div class="container">
                <div class="error-block">
                    <h3>Thanks For Upload!</h3>

                </div>
            </div>
        @endif
    @endif
@endsection