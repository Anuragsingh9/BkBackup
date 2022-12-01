@extends('layouts.master_staff')
@section('content')

    <div class="app-body">
            <main class="main">
                <div class="container page-content">
                    <div class="col-xs-12 col-sm-12">
                    <h2 class="text-center mb-30">You are connected as Support Staff.</h2>
                    <h2 class="text-center mb-30">!! Whatever you do is real !!</h2>
                    {{ Form::open(array('url'=>route('get-account'),'class'=>'staff-login-form login-form white-text'))}}
                       {{ csrf_field() }}
                            <div class="form-group">
                                <label class="form-label white-text">Choose account:</label>
                                <div class="login-input">
                                    <i aria-hidden="true" class="fa fa-user"></i>
                                    {{ Form::text('text','',array('class'=>'form-control','placeholder'=>'','autocomplete'=>'false','id'=>'search')) }}
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label white-text">Choose user:</label>
                                <div class="login-input">
                                    <i aria-hidden="true" class="fa fa-user"></i>
                                    {{ Form::text('password','',array('class'=>'form-control','placeholder'=>'','autocomplete'=>'false','id'=>'searchUser')) }}
                                </div>
                            </div>
                        <button id="sub" class="btn btn-lg btn-primary btn-block" type="submit"> Connect <span id="name"></span>   <span id="acc"></span></button>
                        {{ Form::hidden('account_id','',array('class'=>'form-control')) }}
                        {{ Form::hidden('user_id','',array('class'=>'form-control')) }}
                        {{ Form::close() }}

                    </div>
                </div>
            </main>
        </div>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(function() {
            function log( message ) {
            if(parseInt(message)){
                $( "input[name='account_id']" ).val(message);
            }
            }

            function mobileUser( message ) {
            if(parseInt(message)){
                $( "input[name='user_id']" ).val(message);
            }
            }

            $( "#search" ).autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "search",
                        dataType: "json",
                        data: {
                            q: request.term
                        },
                        success: function( data ) {
                            response($.map(data, function (value, key) {
                                return {
                                    label: value.fqdn,
                                    value: value.fqdn,
                                    id: value.id,
                                };
                            }));
                        }
                    });
                },
                minLength: 3,
                select: function( event, ui ) {
            $('#acc').text('');
            $('#acc').text(ui.item.label.substring(0, ui.item.label.indexOf('.')));
                    $("#sub").css("width", "100%");

                    log( ui.item ?
                        ui.item.id :
                        "Nothing selected, input was " + this.value);
                },
                open: function() {
                    $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
                },
                close: function() {
                    $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
                }
            });


            $( "#searchUser" ).autocomplete({
                source: function( request, response ) {

                    $.ajax({
                        type: 'POST',
                        url: "search-user",
                        dataType: "json",
                        data: {
                            q: request.term,
                            a:$( "input[name='account_id']" ).val() ,
                        },
                        success: function( data ) {
                            response($.map(data, function (value, key) {
                                return {
                                    label: value.fname +' '+value.lname,
                                    value: value.fname +' '+value.lname,
                                    id: value.id,
                                };
                            }));
                        }
                    });
                },
                minLength: 3,
                select: function( event, ui ) {
                    $('#name').text('');
                    $('#name').text(ui.item.label);
                    mobileUser( ui.item ?
                        ui.item.id :
                        "Nothing selected, input was " + this.value);
                },
                open: function() {
                    $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
                },
                close: function() {
                    $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
                }
            });
        });
    </script>
@endsection
