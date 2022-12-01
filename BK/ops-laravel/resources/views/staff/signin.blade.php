@extends('layouts.master')
@section('content')

    <div class="app-body">
            <main class="main">
                <div class="container page-content">
                    <div class="col-xs-12 col-sm-12">
                    {{ Form::open(array('url'=>route('staff-signin'),'class'=>'login-form text-center white-text'))}}
                       {{ csrf_field() }}
                            <h4 class="login-form-heading">
                                Welcome to OP simplify
                            </h4>
                            
                            <div class="login-email login-input">
                                <i aria-hidden="true" class="fa fa-envelope">
                                </i>
                                {{ Form::text('email','',array('class'=>'form-control','placeholder'=>'Email Address','autocomplete'=>'false')) }}                                
                            </div>
                            <div class="login-pass login-input">
                                <i aria-hidden="true" class="fa fa-lock">
                                </i>
                               <input autocomplete="false" class="form-control" name="password" placeholder="Password" type="password" value=""/>
                            </div>
                            <button class="btn btn-lg btn-primary btn-block" type="submit"> Se connecter</button>

                        {{ Form::close() }}

                    </div>
                </div>
            </main>
        </div>
@endsection