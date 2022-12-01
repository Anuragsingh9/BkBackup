@extends('layouts.master')
@section('content')

        <div class="app-body">
            <main class="main">
                <!-- ********** Step 2 Start ********** -->
                    <div class="registration-verifiy grey-bg" id="step_2">
                        <div class="container page-content">
                            <div class="col-xs-6 col-sm-6 col-sm-offset-3 white-text text-center">
                                <h2 class="">Vérifiez votre Email</h2>
                                <h4 class="regi-verification-content">
                                    Nous vous avons envoyé
                                    <br>votre code de validation à 6 chiffres
                                    <br>à
                                    <strong><em>{{isset(session()->get('user_email')['email'])?session()->get('user_email')['email']:$email}}</em></strong>
                                    </br>
                                    </br>
                                </h4>

                                <div class="para">
                                    Entrez le code dans les cases ci-dessous<br>
                                    pour valider votre accès à OPsimplify</br>
                                </div>
                                <form class="verification-code-form">
                                    <input class="verify-code" maxlength="1" name="code_1" type="text"/>
                                    <input class="verify-code" maxlength="1" name="code_2" type="text"/>
                                    <input class="verify-code" maxlength="1" name="code_3" type="text"/>
                                    <span>-</span>
                                    <input class="verify-code" maxlength="1" name="code_4" type="text"/>
                                    <input class="verify-code" maxlength="1" name="code_5" type="text"/>
                                    <input class="verify-code" maxlength="1" name="code_6" type="text"/>
                                    <p class="text-white">Vous n'avez pas reçu votre code?
                                        <a class="resend_mail" href="javascript:void(0)">ME LE RENVOYER A NOUVEAU</a>
                                    </p>                 
                                </form>
                            </div>
                        </div>
                    </div>
                <!-- ********** Step 2 End ********** -->

                
            </main>
        </div>
        
<script>
    global_var.email="{{request()->email}}";
    global_var.userId="{{request()->userid}}";
    global_var.nxtRoute="{{route('signup-steps')}}";
</script>
@endsection