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
                        <strong>{{(isset($user)? 'Edit Template' : 'Add Template')}}</strong>
                    </h4>
                </div>
                {{ Form::open(array('url'=>route('updatetemplate',[$id,$acc_id]),'class'=>"superadmin-form",'id'=>'superadmin-form')) }}
                <div class="col-xs-12">
                    <div class="form-group required-field">
                        <label class="col-xs-6 col-sm-6 col-md-6 col-lg-6" for=""> Name</label>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 {{ $errors->has('name') ? ' has-error' : '' }}">
                                <input class="form-control ucfirst" id="fname" name="name" placeholder="name"
                                       type="text" value="{{(isset($data->title)?$data->title:'')}}"/>

                            </div>


                        </div>
                        <label class="col-xs-6 col-sm-6 col-md-6 col-lg-6" for=""> Language</label>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 {{ $errors->has('name') ? ' has-error' : '' }}">
                                <select class="form-control" name="language">
                                    <option value="">Please Select Language</option>
                                    @foreach($lang as $k=>$l)
                                        <option {{(isset($data->language)?'selected':'')}} value="{{$k}}">{{$l}}</option>
                                    @endforeach
                                </select>
                            </div>


                        </div>
                    </div>
                    <div class="text-right">
                        <button class="btn btn-primary text-center" data-id="save_details" type="submit"> Suivant >
                        </button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
            <!-- ********** Step 1 End ********** -->
        </main>
    </div>

    <script>
        function validation_all() {
            $(".superadmin-form").validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    name: {
                        required: true
                    }, language: {
                        required: true
                    },
                    mobile: {

                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    phone: {
                        required: true,
                        digits: true,
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    password_confirmation: {
                        required: true,
                        minlength: 8,
                        equalTo: "#u_pass"
                    }
                }
            })
        }
        function validation_pass() {
            $(".superadmin-form").validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    name: {
                        required: true
                    }, language: {
                        required: true
                    },
                    mobile: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    phone: {
                        required: true,
                        digits: true,
                    },
                }
            })
        }
        $(document).ready(function () {
            @if(isset($user))
                validation_pass()
            $("#u_pass").on('change', function () {
                if ($(this).val().length > 0) {
                    validation_all()
                }
            });
            @else
                validation_all()
            @endif
        });
    </script>
@endsection