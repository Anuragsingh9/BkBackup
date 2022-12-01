@extends('layouts.master_superadmin')
@section('content')
    <script src="https://unpkg.com/jquery@2.2.4/dist/jquery.js"></script>
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <link href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css"/>
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
                        <strong>{{(isset($user)? 'Edit Module Form' : 'Add Module Form')}}</strong>
                    </h4>
                </div>
                {{ Form::open(array('url'=>route('upload-module'),'class'=>"superadmin-form",'id'=>'superadmin-form')) }}
                <div class="col-xs-12 mb-50">
                    <div class="form-group required-field">
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 {{ $errors->has('label_en') ? ' has-error' : '' }}">
                                <label class="col-xs-12 col-sm-12 col-md-12 nopadding" for=""> LabelEn</label>
                                <input class="form-control ucfirst" id="label_en"  name="label_en" placeholder="label_en" type="text" value="{{(isset($user)?$user->label_en:'')}}"/>
                                @if ($errors->has('label_fr'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('label_fr') }}</strong>
                            </span>
                                @endif
                                @isset($user)
                                    <input type="hidden" name="id" value="{{$user->id}}">
                                @endisset
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 {{ $errors->has('label_fr') ? ' has-error' : '' }}">
                                <label class="col-xs-12 col-sm-12 col-md-12 nopadding" for=""> LabelFr</label>
                                <input class="form-control ucfirst" id="label_fr" name="label_fr" placeholder="label_fr" type="text"  value="{{(isset($user)?$user->label_fr:'')}}" />
                                @if ($errors->has('label_fr'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('label_fr') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group required-field">
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 {{ $errors->has('tooltip_en') ? ' has-error' : '' }}">
                                <label class="col-xs-12 col-sm-12 col-md-12 nopadding" for=""> Tool-TipEn</label>
                                <input class="form-control ucfirst" id="tooltip_en"  name="tooltip_en" placeholder="tooltip_en" type="text" value="{{(isset($user)?$user->tooltip_en:'')}}"/>
                                @if ($errors->has('tooltip_en'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('tooltip_en') }}</strong>
                            </span>
                                @endif
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 {{ $errors->has('tooltip_fr') ? ' has-error' : '' }}">
                                <label class="col-xs-12 col-sm-12 col-md-12 nopadding" for=""> Tool-TipFr</label>
                                <input class="form-control ucfirst" id="tooltip_fr" name="tooltip_fr" placeholder="tooltip_fr" type="text" value="{{(isset($user)?$user->tooltip_fr:'')}}"/>
                                @if ($errors->has('tooltip_fr'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('tooltip_fr') }}</strong>
                            </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button class="btn btn-primary text-center" data-id="save_details" type="submit"> Suivant ></button>
                    </div>
                </div>
                {{ Form::close() }}

                <div class="col-xs-12 col-sm-12">
                    <div class="clearfix">

                        <ul class="list-group dragable-list-group mb-0">
                            <li class="dragable-list-header list-group-item">
                                <span class="list-title">Name</span>
                                <span class="list-action">Acttion</span>
                            </li>
                        </ul>
                        <ul class="sort_menu list-group dragable-list-group">
                            @foreach ($modules as $row)

                                <li class="list-group-item" data-id="{{$row->id}}">
                                    <span class="handle"></span>
                                    <span class="list-title">
                                        {{$row->label_en}}
                                    </span>
                                    <span class="list-action">
                                        <a href="{{ route('edit-module',$row->id) }}"
                                           class="btn btn-primary actionButton">Modify</a>
                                        <button onclick="delete_me(this.id)" id="{{$row->id}}"
                                                class="btn btn-primary actionButton">Remove
                                        </button>
                                    </span>
                                </li>
                            @endforeach
                        </ul>

                    </div>
                    {{-- <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Name</th>

                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($modules as $key=>$item)
                                <tr class="sort_menu list-group">
                                    <td class="list-group-item" data-id="{{$row->id}}" width="15px">   <span class="handle"></span>{{$item->label_en}}</td>
                                    <td width="20px">
                                        <a href="{{ route('edit-module',$item['id']) }}"
                                           class="btn btn-primary actionButton">Modify</a>
                                        <button onclick="delete_me(this.id)" id="{{$item['id']}}"
                                                class="btn btn-primary actionButton">Remove
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div> --}}
                </div>
            </div>
            <!-- ********** Step 1 End ********** -->

        </main>
    </div>


    <script>
        function delete_me(id) {
            if (confirm("Are You Sure You want to delete")) {
                location.href = "{!!route('deltemodule')!!}/" + id;
            }
        }
    </script>
    <style>
        .list-group-item {
            display: flex;
            align-items: center;
        }

        .highlight {
            background: #f7e7d3;
            min-height: 30px;
            list-style-type: none;
        }

        .handle {
            min-width: 18px;
            background: #607D8B;
            height: 15px;
            display: inline-block;
            cursor: move;
            margin-right: 10px;
        }
    </style>
    <script>
        $(document).ready(function(){

            function updateToDatabase(idString){
                $.ajaxSetup({ headers: {'X-CSRF-TOKEN': '{{csrf_token()}}'}});

                $.ajax({
                    url:'{{url('/module/update-order')}}',
                    method:'POST',
                    data:{ids:idString},
                    success:function(){
                        alert('Successfully updated')
                        //do whatever after success
                    }
                })
            }

            var target = $('.sort_menu');
            target.sortable({
                handle: '.handle',
                placeholder: 'highlight',
                axis: "y",
                update: function (e, ui){
                    var sortData = target.sortable('toArray',{ attribute: 'data-id'})
                    updateToDatabase(sortData.join(','))
                }
            })

        })
    </script>
@endsection