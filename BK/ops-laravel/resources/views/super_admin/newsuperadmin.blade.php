@extends('layouts.master_superadmin')
@section('content')

<!--<script>
    var hash_num = location.hash.substring(1, location.hash.length);
    var step = (hash_num > 0) ? hash_num : 1;
</script>-->
<div class="app-body">

    <main class="main">
        <!-- ********** Step 1 Start ********** -->
        <div class="container page-content">
            <div class="col-xs-12 col-sm-12">
                <h4 class="site-color mt-20 mb-30">
                    <strong>{{(isset($user)? 'Edit super admin' : 'Add new super admin')}}</strong>
                </h4> 
            </div>
            {{ Form::open(array('url'=>route('savesuperadmin'),'class'=>"superadmin-form",'id'=>'superadmin-form')) }}
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 form-group required-field {{ $errors->has('name') ? ' has-error' : '' }}">
                        <label class="col-xs-12 col-md-12 nopadding" for=""> Name</label>
                        <div class="clearfix">
                            <input class="form-control ucfirst" id="fname"  name="name" placeholder="Name" type="text" value="{{(isset($user)?$user->name:'')}}"/>
                            @isset($user)
                            <input type="hidden" name="id" value="{{$user->id}}">
                            @endisset
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 form-group required-field {{ $errors->has('email') ? ' has-error' : '' }}">
                        <label class="col-xs-12 col-md-12 nopadding" for=""> Email</label>
                        <div class="clearfix">
                            <input class="form-control ucfirst" id="email" name="email" placeholder="Email" type="Email" @if(isset($user->id)) disabled @else false  @endif" value="{{(isset($user)?$user->email:'')}}" />
                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 form-group required-field {{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label class="col-xs-12 col-md-12 nopadding" for=""> Phone</label>
                        <div class="clearfix">
                            <input class="form-control ucfirst" id="phone"  name="phone" placeholder="Phone" type="text" value="{{(isset($user)?$user->phone:'')}}"/>
                            @if ($errors->has('phone'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 form-group required-field {{ $errors->has('mobile') ? ' has-error' : '' }}">
                        <label class="col-xs-12 col-md-12 nopadding" for=""> Mobile</label>
                        <div class="clearfix">
                            <input class="form-control ucfirst" id="mobile" name="mobile" placeholder="Mobile" type="text" value="{{(isset($user)?$user->mobile:'')}}"/>
                            @if ($errors->has('mobile'))
                            <span class="help-block">
                                <strong>{{ $errors->first('mobile') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group required-field {{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="">Votre Mot de passe</label>
                    <input class="form-control" id="u_pass" name="password" placeholder="Entrez Votre Mot de passe" type="password" />
                     @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif
                </div>

                <div class="form-group required-field {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <label for=""> Mot de passe (Confirmation)</label>
                    <input class="form-control" id="" name="password_confirmation" placeholder="Entrez Votre Mot de passe" type="password"/>
                    @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="text-right">
                    <button class="btn btn-primary text-center" data-id="save_details" type="submit"> Suivant ></button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
        <!-- ********** Step 1 End ********** -->
    </main>
</div>

<script>
    function validation_all(){
        $( ".superadmin-form" ).validate({
            rules: {
                email: {
                    required: true,
                    email: true
            },
            name:{
                required:true
            },
            mobile:{

                required:true,
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            phone:{
                required:true,
                digits: true,
            },
            password: {
               required: true,
               minlength: 8
           },
           password_confirmation:{
            required: true,
            minlength: 8,
            equalTo: "#u_pass"
                }
            }
    }) 
}
function validation_pass(){
        $( ".superadmin-form" ).validate({
            rules: {
            email: {
                required: true,
                email: true
            },
            name:{
                required:true
            },
            mobile:{
                required:true,
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            phone:{
                required:true,
                digits: true,
            },
        }
     }) 
    }
    $(document).ready(function(){
        @if(isset($user))
            validation_pass()
            $("#u_pass").on('change',function(){
                if($(this).val().length>0){
                    validation_all()   
                }
        });
        @else   
            validation_all()
        @endif
    });
</script>      
@endsection