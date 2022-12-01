@extends('layouts.master')
@section('content')
  <script>
  var hash_num =location.hash.substring(1,location.hash.length);
  var step = (hash_num > 0) ? hash_num :1;

  function preventBack(){window.history.forward();}
  setTimeout("preventBack()", 0);
  window.onunload=function(){null};
  </script>
  @php

    @$session_email = session()->get('user_email')['email'];
    @$email =explode('.', explode('@',$session_email)[1], 2)[0];
  @endphp
        <div class="app-body">
            <main class="main" id="reg-step-form"> 
                <!-- ********** Step 1 Start ********** -->
                    <div class="container page-content hide-elem" id="step_1">
                        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                            <form class="default-form" role="form" id="personal_info">
                                <input name="step" value="one" placeholder="Entrez votre Prénom" type="hidden"/>
                                <h4 class="form-sec-title site-color">Vous</h4>
                                <section class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group required-field">
                                            <label for=""> Nom</label>
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                                    <input class="form-control ucfirst" id="fname"  name="u_fname" placeholder="Entrez votre Prénom" type="text"/>
                                                </div>
                                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                                    <input class="form-control ucfirst" id="lname" name="u_lname" placeholder="Entrez votre Nom" type="text"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group required-field">
                                            <label for="">Votre Mot de passe</label>
                                            <input class="form-control" id="u_pass" name="u_pass" placeholder="Entrez Votre Mot de passe" type="password"/>
                                        </div>
                                        <div class="form-group required-field">
                                            <label for=""> Mot de passe (Confirmation)</label>
                                            <input class="form-control" id="" name="u_cpass" placeholder="Entrez Votre Mot de passe" type="password"/>
                                        </div>
                                        <div class="text-right">
                                            <button class="btn btn-primary text-center nxt-form" data-id="personal_info" type="button"> Suivant ></button>
                                        </div>
                                    </div>
                                </section>
                            </form>
                        </div>
                    </div>
                <!-- ********** Step 1 End ********** -->

                <!-- ********** Step 2 Start ********** -->
                    <div class="container page-content hide-elem" id="step_2">
                        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                            <form class="default-form" role="form" id="organization_info">
                                <input name="step" value="two" placeholder="Entrez votre Prénom" type="hidden"/>
                                <h4 class="form-sec-title site-color"> Votre organisation </h4>
                                <section class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group required-field">
                                            <label for="">Le nom de votre organisation</label>
                                            <input class="form-control ucfirst" id="name_org" name="name_org" placeholder="Exemple: Association des maires" type="text"/>
                                        </div>
                                        <div class="form-group required-field">
                                            <label for="">Sigle de votre organisation</label>
                                            <input class="form-control capsletter" id="acronym" name="acronym" placeholder="Exemple : ADM" type="text"/>
                                        </div>
                                        <div class="form-group required-field">
                                            <label for="">Votre secteur</label>
                                            <input class="form-control ucfirst" id="" name="sector" placeholder="Exemple : Automobile" type="text"/>
                                        </div>
                                        <div class="form-group required-field">
                                            <label for="">Combien de permanents avez-vous ?</label>
                                            <div class="select-cover">
                                                <i aria-hidden="true" class="fa fa-chevron-down site-color"></i>
                                                <select class="form-control" id="" name="permanent_member">
                                                    <option value="">--Select Option--</option>
                                                    <option value="1-3">1-3</option>
                                                    <option value="4-9">4-9</option>
                                                    <option value="10-25">10-25</option>
                                                    <option value="26-100">26-100</option>
                                                    <option value="101-1000">101-1000</option>
                                                    <option value="1000+">1000+</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Combien d’adhérents avez-vous ?</label>
                                            <div class="btn-group radio-btns" data-toggle="buttons">
                                                <label class="btn active">
                                                    <input autocomplete="off" id="radiovalue" name="members_count" type="radio" value="1-9"/>1-9
                                                </label>
                                                <label class="btn">
                                                    <input autocomplete="off" id="radiovalue" name="members_count" type="radio" value="10-99"/>10-99
                                                </label>
                                                <label class="btn">
                                                    <input autocomplete="off" id="radiovalue" name="members_count" type="radio" value="100-999"/>100-999
                                                </label>
                                                <label class="btn">
                                                    <input autocomplete="off" id="radiovalue" name="members_count" type="radio" value="1000+"/>1000+
                                                </label>
                                                <label class="btn">
                                                    <input autocomplete="off" id="radiovalue" name="members_count" type="radio" value="5000+"/>5000+
                                                </label>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button class="btn btn-primary text-center nxt-form" data-id="organization_info" type="button">Suivant ></button>
                                        </div>
                                    </div>
                                </section>
                            </form>
                        </div>
                    </div>
                <!-- ********** Step 2 End ********** -->

                <!-- ********** Step 3 Start ********** -->
                    <div class="container page-content hide-elem" id="step_3">
                        <div class="col-xs-6 col-sm-6 col-sm-offset-3 text-center">
                            <form class="business-email-form your-email-account" onsubmit="event.preventDefault()" role="form" id="account_info">
                                <input name="step" value="three" placeholder="Entrez votre Prénom" type="hidden"/>
                                <h3>Votre compte</h3>
                                <p>Ceci est l’adresse internet qui vous permettra de gérer votre organisation :</p>
                                <div class="input-group">
                                    <input name="domain" class="form-control text-right" placeholder="your domain name" type="text" value="{{$email}}"/>
                                    <span class="input-group-btn">
                                        <span class="btn btn-primary btn-color1 ">.pasimplify.com</span>
                                    </span>                                       
                                </div>
                                <button class="btn btn-primary text-center your-account-submit nxt-form" data-id="account_info" type="button"> Enregistrer</button>
                            </form>
                        </div>
                    </div>
                <!-- ********** Step 3 End ********** -->

                <!-- ********** Step 4 Start ********** -->
                    <div class="container page-content hide-elem" id="step_4">
                        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                            <form class="default-form" role="form" id="group_info">
                                <input name="step" value="four" placeholder="" type="hidden"/>
                                <h4 class="form-sec-title site-color">Vos commissions ou groupes de travail</h4>
                                <section class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group required-field">
                                            <label for="">Combien de commissions avez vous ?</label>
                                            <input name="commissions" class="form-control" min="0" id="" placeholder="" type="number"/>
                                            <p>Vous pourrez changer ces chiffres plus tard si vous le désirez</p>
                                        </div>
                                        <div class="form-group required-field">
                                            <label for="">Combien de groupes de travail avez vous ?</label>
                                            <input name="groups" class="form-control" id="" min="0" placeholder="" type="number"/>
                                            <p>Vous pourrez changer ces chiffres plus tard si vous le désirez</p>
                                        </div>
                                    </div>
                                </section>
                                <button class="btn btn-primary text-center your-account-submit final-form" data-id="group_info" type="button"> Démarrez</button>
                            </form>
                        </div>
                    </div>
                <!-- ********** Step 4 End ********** -->

                <!-- ********** Step 5 Start ********** -->
                    <div class="container page-content hide-elem" id="step_5">
                        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                            <form class="default-form text-center" role="form">
                                <h4 class="form-sec-title site-color">Votre première réunion</h4>
                                <section class="row form-footer">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label class="site-color mt-10">Voulez-vous créer votre première réunion ?</label>
                                            <div class="row mt-25">
                                                <div class="col-xs-6">
                                                    <a aria-current="false" class="btn btn-primary text-center" href="http://localhost:3000/#/login">Oui</a>
                                                </div>
                                                <div class="col-xs-6">
                                                    <a aria-current="false" class="btn btn-default text-center" href="http://localhost:3000/#/login">Plus Tard </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </form>
                        </div>
                    </div>
                <!-- ********** Step 5 End ********** -->
            </main>
        </div>
        


<script>
$(document).ready(function(){
   
    globStepFunc.initForm();
    $("input").on("click", function(){

        $("button").attr("disabled",false);
    });
    
});
$('.capsletter').keyup(function(event) {

        $(this).val($(this).val().toUpperCase());
});

$('.ucfirst').keyup(function(){
    if(this.value.match(/^[a-z]/)){
        // replace the first letter
        this.value = this.value.replace(/^./,function(letter){
            // with the uppercase version
            return letter.toUpperCase();
        });
    }
})
</script>
@endsection