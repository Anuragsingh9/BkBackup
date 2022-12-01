@extends('carte-pro.layouts.app')

@section('content')
    <main id="main-content">
        {{-- <section class="banner" id="banner">
            <div class="container">
                <div class="parent-card-block">
                    <img src="{{ asset('public/carte_pro/images/card-icon.png') }}" class="center-block">
                    <div class="card-block">
                        <div class="card-upper-block">
                            <div class="card-company-block">
                                <span class="company-name"></span>
                                <span class="zip-code"></span>
                            </div>
                            <div class="card-upper-logo">
                                <!-- Image will load from Js  -->
                            </div>
                        </div>
                        <div class="card-lower-block">
                            <div class="card-bottom-logo">
                                <img src="{{ asset("public/carte_pro/images/carte-pro.png") }}"
                                     class="center-block img-responsive">
                            </div>
                            <div class="domians-name">
                                <ul>
                                    <!-- li will load from js -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> --}}

        {{-- <div class="double-border-line"></div> --}}
        @if(config('constants.QUALIFICATION'))
            <section id="step-form">
                <div class="container">
                    <div class="loader-cover"><span class="loader"></span></div>
                    <div class="step-counter">
                        <ul>
                            <li class="active"><a href=""><span>&#202;tape 1</span><h4>Pr&#233;-remplissez votre
                                        carte</h4></a>
                            </li>
                            <!--<li><a href=""><span>&#202;tape 2</span><h4>Pr&#233;-remplissez votre carte-pro</h4></a></li>-->
                            <!--<li><a href=""><span>&#202;tape 3</span><h4>Pr&#233;-remplissez votre carte-pro</h4></a></li>-->
                            <!--<li><a href=""><span>&#202;tape 4</span><h4>Pr&#233;-remplissez votre carte-pro</h4></a></li>-->
                        </ul>
                    </div>
                    <div class="form">
                        <form class="step-one-form" workshopid="">

                            <div class="form-group form-inner form-active reg-step1" modify="0" finalZip="">
                                <label class="step-label" id="step1-title">Choisissez votre Fédération</label>
                                <div class="modify-zipcode field-block">
                                    <div class="zip-field">
                                        <input id="zip-input" data-code="" name="zip" type="text" class="form-control usraff-input"
                                               autocomplete="off">
                                        <div class="loader-07"></div>
                                        <button type="button" class="btn-green continue-zip">Continuez</button>
                                        <button class="btn transparent-btn save-zip hide">Sauver</button>
                                        <p class="error"></p>
                                    </div>
                                    <div class="hide zip-val">
                                        <div class="zipcode-value"><span></span></div>
                                        <button class="btn transparent-btn modify-zip">Modifiez</button>
                                    </div>
                                </div>
                            </div>

                            <div class="hide form-group border-dash form-active form-inner reg-step2">
                                <legend class="step-label">Êtes-vous adhérent(e) de la <span>FFB 33 Gironde</span> ?
                                </legend>
                                <div class="field-block">
                                    <div class="radio-style1">
                                        <label for="check-yes">
                                            oui
                                            <input type="radio" value="1" name="check-member" checked="checked"
                                                   id="check-yes"/>
                                            <span class="radio-icon"></span>
                                        </label>
                                    </div>
                                    <div class="radio-style1">
                                        <label for="check-no">
                                            non
                                            <input type="radio" value="0" name="check-member" id="check-no"/>
                                            <span class="radio-icon"></span>
                                        </label>
                                    </div>
                                    <button type="button" class="btn-green continue-check-memb">Continuez</button>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="hide reg-step3">
                                <!-- <div class="form-group form-inner border-dash form-active" id="company-name-block">
                                    <label class="step-label">Dénomination </br> de votre entreprise :</label>
                                    <div class="field-block">
                                      <input name="company-name" type="text" class="form-control" id="company-name" value="">
                                      <p class="error"></p>
                                    </div>
                                </div> -->

                                <div id="rendered-fields-block">
                                    <!-- Loop of Input Fields from Js -->
                                </div>

                                <div class="form-group radio-inner border-dash" id="domain-list">
                                    <fieldset>
                                        <legend>Domaines demandés :</legend>
                                        <div id="input-domains">
                                            <!-- Input data from API -->
                                        </div>
                                    </fieldset>
                                    {{-- <p class="error"></p> --}}
                                </div>

                                <div class="border-dash bio-data-details">
                                    <div class="form-group form-inner" id="user-prenom">
                                        <label class="detail-label" for="prename">Votre prénom :</label>
                                        <div>
                                            <input id="prename" name="prename" type="text" class="form-control"/>
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                    <div class="form-group form-inner" id="user-name">
                                        <label class="detail-label" for="name">Votre nom :</label>
                                        <div>
                                            <input id="name" name="name" type="text" class="form-control"/>
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                    <div class="form-group form-inner" id="user-tel">
                                        <label class="detail-label" for="phone">Votre téléphone :</label>
                                        <div>
                                            <input id="phone" name="phone" type="text" class="form-control"/>
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                    <div class="form-group form-inner" id="user-mobile">
                                        <label class="detail-label" for="mobile">Votre mobile :</label>
                                        <div>
                                            <input id="mobile" name="mobile" type="text" class="form-control"/>
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                    <div class="form-group form-inner" id="user-email">
                                        <label class="detail-label" for="email">Votre email :</label>
                                        <div>
                                            <input id="email" name="email" type="text" class="form-control"/>
                                            <p class="error"></p>
                                        </div>
                                    </div>

                                    <!--<div class="form-group form-inner w-100">-->
                                    <!--    <label class="detail-label" for="comment">Commentaire :</label>-->
                                    <!--    <div>-->
                                    <!--      <textarea id="comment" name="" class="form-control" ></textarea>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <div class="form-group form-inner">
                                        <div class="btn-outer">
                                            <input id="save-form" type="submit" value="Continuez"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        @else
            <div class="container text-center mt-70">
                <h1>Qualification Module is Not Enabled For Your Account </h1>
            </div>

        @endif
    </main>
    <style>
.ui-helper-hidden-accessible{
    display: none;
}
    </style>
@endsection
