@extends('layouts.master')
@section('content')
        <script>
  var hash_num =location.hash.substring(1,location.hash.length);
  var step = (hash_num > 0) ? hash_num :1;
  </script>
        <div class="app-body">
            <main class="main">
                <!-- ********** Step 1 Start ********** -->
                    <div class="container page-content">
                        <div class="col-xs-6 col-sm-6 col-sm-offset-3 text-center">
                            {{ Form::open(array('url'=>route('signup-email'),'class'=>"business-email-form")) }}
                                <h3>Pilotez votre organisation professionnelle comme vous en avez toujours révé</h3>
                                <div class="input-group">
                                    <input class="form-control" name="email" placeholder="Entrez votre email professionnel" type="text" value="" id="" required/>
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary btn-color1" type="submit" id="">Créez votre compte gratuitement</button>
                                    </span>
                                </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                <!-- ********** Step 1 End ********** -->
            </main>
        </div>

<script>
$(document).ready(function(){
    $( ".business-email-form" ).validate({
      rules: {
        email: {
            required: true,
            email: true
        }
      }
    });
});
</script>      
@endsection