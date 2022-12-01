@extends('layouts.master_superadmin')
@section('content')
    <script src="https://unpkg.com/jquery@2.2.4/dist/jquery.js"></script>
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <link href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css"/>
    <div class="app-body">
        <main class="main">
            <div class="container page-content">
                <div class="col-xs-12 col-sm-12">
                    <h4 class="site-color mt-20 mb-30">
                        <strong>List of Modules</strong>
                    </h4>
                    <h4 class="site-color mt-20 mb-30">
                        <strong> <a class="" href="{{ route('add-module-list') }}">Add Modules</a>
                        </strong>
                    </h4>
                </div>
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
