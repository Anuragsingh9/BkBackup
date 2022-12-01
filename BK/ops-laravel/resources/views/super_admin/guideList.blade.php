@extends('layouts.master_superadmin')
@section('content')
    <div class="app-body">
        <main class="main">
            @if (Session::has('message'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {{ Session::get('message') }}
                </div>
            @endif
            <div class="container page-content">
                <h4 class="site-color mt-20 mb-15"><strong>Guides Upload</strong></h4>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="setting-opt-menu">
                                    <h5 class="mb-20"><strong>Guides FR</strong></h5>
                                    <ul class="settings-menu-list pl-20 setting-icon pl-20">
                                        <ul class="pl-0">
                                            @foreach($guide as $row)
                                                {{-- as some of are added separated --}}
                                                @if(!$excludeGuideList->where('title_en', $row->title_en)->count())
                                                    <li class="mb-3">
                                                        <button data-toggle="modal" data-id="{{$row->id}}"
                                                                data-lang="fr"
                                                                data-target="#myModal"
                                                                class="btn-link link">{{$row->title_fr}}</button>
                                                        @if($row->upload_fr!='' || $row->upload_fr!=null)
                                                            <button data-lang="FR" data-url="{{$row->id}}"
                                                                    class="btn-link download"><i
                                                                        class="fa fa-download"></i>
                                                            </button>
                                                            <button data-lang="FR" data-url="{{$row->id}}"
                                                                    class="btn-link text-danger deleteGuide"><i
                                                                        class="fa fa-trash-o"></i></button>
                                                        @endif
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="setting-opt-menu">
                                    <h5 class="mb-20"><strong>Guides EN</strong></h5>
                                    <ul class="settings-menu-list pl-20 setting-icon">
                                        <ul class="pl-0">
                                            @foreach($guide as $row)
                                                {{-- as some of are added separated --}}
                                                @if(!$excludeGuideList->where('title_en', $row->title_en)->count())
                                                    <li class="mb-3">
                                                        <button data-toggle="modal" data-lang="en"
                                                                data-target="#myModal"
                                                                data-id="{{$row->id}}" data-lang="en"
                                                                class="btn-link link">{{$row->title_en}}</button>
                                                        @if($row->upload_en!='' || $row->upload_en!=null)
                                                            <button data-lang="EN" data-url="{{$row->id}}"
                                                                    class="btn-link download"><i
                                                                        class="fa fa-download"></i>
                                                            </button>
                                                            <button data-lang="EN" data-url="{{$row->id}}"
                                                                    class="btn-link text-danger deleteGuide"><i
                                                                        class="far fa-trash-alt"></i></button>
                                                        @endif
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <h4 class="site-color mt-20 mb-15"><strong>GRDP Upload</strong></h4>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="setting-opt-menu">
                                    <h5 class="mb-20"><strong>GRDP FR</strong></h5>
                                    <ul class="settings-menu-list pl-20">
                                        <ul class="pl-0">
                                            @foreach($grdp as $row)
                                                <li class="mb-3">
                                                    <button data-toggle="modal" data-id="{{$row->id}}" data-lang="fr"
                                                            data-target="#grdp"
                                                            class="btn-link grdp-link">{{$row->title_fr}}</button>
                                                    @if($row->upload_fr!='' || $row->upload_fr!=null)
                                                        <button data-lang="FR" data-url="{{$row->id}}"
                                                                class="btn-link grdp-download"><i
                                                                    class="fa fa-download"></i></button>
                                                        <button data-lang="FR" data-url="{{$row->id}}"
                                                                class="btn-link text-danger deleteGrdp"><i
                                                                    class="fa fa-trash-o"></i></button>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="setting-opt-menu">
                                    <h5 class="mb-20"><strong>GRDP EN</strong></h5>
                                    <ul class="settings-menu-list pl-20">
                                        <ul class="pl-0">
                                            @foreach($grdp as $row)
                                                <li class="mb-3">
                                                    <button data-toggle="modal" data-lang="en" data-id="{{$row->id}}"
                                                            data-target="#grdp" data-lang="en"
                                                            class="btn-link grdp-link">{{$row->title_en}}</button>
                                                    @if($row->upload_en!='' || $row->upload_en!=null)
                                                        <button data-lang="EN" data-url="{{$row->id}}"
                                                                class="btn-link grdp-download"><i
                                                                    class="fa fa-download"></i></button>
                                                        <button data-lang="EN" data-url="{{$row->id}}"
                                                                class="btn-link text-danger deleteGrdp"><i
                                                                    class="fa fa-trash-o"></i></button>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--upload project template--}}
                {{-- <h4 class="site-color mt-20 mb-15"><strong>Project Template Upload</strong></h4>
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="setting-opt-menu">
                                    <ul class="settings-menu-list pl-20">
                                        <ul class="pl-0">
                                            @php
                                                $found=$grdp->pluck('type')->search(1);
                                            @endphp

                                            @if($found!=false && ($row->upload_en!='' || $row->upload_en!=null))

                                                <button data-toggle="modal" data-lang="project" data-target="#grdp"
                                                        data-id="{{$grdp[$found]->id}}" data-lang="en"
                                                        class="btn-link grdp-link">{{$grdp[$found]->upload_en}}</button>

                                                <button data-lang="EN" data-url="{{$grdp[$found]->id}}"
                                                        class="btn-link text-danger deleteGrdp"><i
                                                            class="fa fa-trash-o"></i></button>

                                            @else
                                                <button data-toggle="modal" data-lang="project" data-target="#grdp"
                                                        data-id="{{$grdp[$found]->id}}" data-lang="en"
                                                        class="btn-link grdp-link">{{'Please Upload Project Template'}}</button>
                                            @endif


                                        </ul>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> --}}
                <h4 class="site-color mt-20 mb-15"><strong>Project Template Upload</strong></h4>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="setting-opt-menu">
                                    <h5 class="mb-20"><strong>Project Template Upload FR</strong></h5>
                                    <ul class="settings-menu-list pl-20">
                                        <ul class="pl-0">
                                            @php
                                                $found=$grdp->pluck('type')->search(1);
                                            @endphp

                                            @if($found!=false && ($grdp[$found]->upload_fr!='' || $grdp[$found]->upload_fr!=null))
                                                {{-- <button data-lang="FR" data-url="{{ $grdp[$found]->id }}"
                                                        data-id="{{$grdp[$found]->id}}" data-lang="FR"
                                                        class="btn-link grdp-download">{{($grdp[$found]->file_name_fr!=null)?$grdp[$found]->file_name_fr:$grdp[$found]->upload_fr}}</button> --}}
                                                    <button data-lang="FR"
                                                        data-id="{{$grdp[$found]->id}}" data-toggle="modal" data-lang="FR" data-target="#project"
                                                        class="btn-link project-link">{{($grdp[$found]->file_name_fr!=null)?$grdp[$found]->file_name_fr:$grdp[$found]->upload_fr}}</button>
                                                <button data-lang="FR" data-url="{{$grdp[$found]->id}}"
                                                    class="btn-link grdp-download"><i
                                                        class="fa fa-download"></i></button>
                                                <button data-lang="FR" data-url="{{$grdp[$found]->id}}"
                                                        class="btn-link text-danger deleteGrdp"><i
                                                            class="fa fa-trash-o"></i></button>
                                            @else
                                                <button data-toggle="modal" data-lang="FR" data-target="#project"
                                                data-id="{{$grdp[$found]->id}}" data-lang="FR"
                                                        class="btn-link project-link">{{'Please Upload Project Template'}}</button>
                                            @endif


                                        </ul>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="setting-opt-menu">
                                    <h5 class="mb-20"><strong>Project Template Upload EN</strong></h5>
                                    <ul class="settings-menu-list pl-20">
                                        <ul class="pl-0">
                                            @php
                                                $found=$grdp->pluck('type')->search(1);
                                            @endphp

                                            @if($found!=false && ($grdp[$found]->upload_en!='' || $grdp[$found]->upload_en!=null))

                                                {{-- <button data-id="{{$grdp[$found]->id}}"  data-url="{{ $grdp[$found]->id }}" data-lang="EN"
                                                        class="btn-link grdp-download">{{($grdp[$found]->file_name_en!=null)?$grdp[$found]->file_name_en:$grdp[$found]->upload_en}}</button> --}}
                                                <button  data-toggle="modal"  data-target="#project"data-id="{{$grdp[$found]->id}}"  data-lang="EN"
                                                         class="btn-link project-link">{{($grdp[$found]->file_name_en!=null)?$grdp[$found]->file_name_en:$grdp[$found]->upload_en}}</button>
                                                <button data-lang="EN" data-url="{{ $grdp[$found]->id }}"
                                                        class="btn-link grdp-download"><i
                                                            class="fa fa-download"></i></button>
                                                <button data-lang="EN" data-url="{{$grdp[$found]->id}}"
                                                        class="btn-link text-danger deleteGrdp"><i
                                                            class="fa fa-trash-o"></i></button>

                                            @else
                                                <button data-toggle="modal"  data-target="#project"
                                                        data-id="{{$grdp[$found]->id}}" data-lang="EN"
                                                        class="btn-link project-link">{{'Please Upload Project Template'}}</button>
                                            @endif


                                        </ul>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Browser Notification Enable Guide Section --}}
                <h4 class="site-color mt-20 mb-15"><strong>Browser Notification Enable Guide</strong></h4>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="setting-opt-menu">
                                    <h5 class="mb-20"><strong>Browser Notification Enable Guide Upload FR</strong></h5>
                                    <ul class="settings-menu-list pl-20">
                                        <ul class="pl-0">
                                            @php
                                              $bneGuide = $guide->where('title_en', config('constants.defaults.s3.notification-allow-guide-EN.name'))->first(); // browser notification enable guide
                                            @endphp
                                            @if(isset($bneGuide) && $bneGuide)
                                                <li class="mb-3">
                                                    <button data-toggle="modal" data-id="{{$bneGuide->id}}" data-lang="fr"
                                                            data-target="#myModal"
                                                            class="btn-link link">{{$bneGuide->title_fr}}</button>
                                                    @if($bneGuide->upload_fr!='' || $bneGuide->upload_fr!=null)
                                                        <button data-lang="FR" data-url="{{$bneGuide->id}}"
                                                                class="btn-link download"><i class="fa fa-download"></i>
                                                        </button>
                                                @endif
                                                </li>
                                            @endif
                                        </ul>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="setting-opt-menu">
                                    <h5 class="mb-20"><strong>Browser Notification Enable Guide Upload EN</strong></h5>
                                    <ul class="settings-menu-list pl-20">
                                        <ul class="pl-0">
                                            @php
                                                $bneGuide = $guide->where('title_en', config('constants.defaults.s3.notification-allow-guide-EN.name'))->first(); // browser notification enable guide
                                            @endphp
                                            @if(isset($bneGuide) && $bneGuide)
                                                <li class="mb-3">
                                                    <button data-toggle="modal" data-lang="en" data-target="#myModal"
                                                            data-id="{{$bneGuide->id}}" data-lang="en"
                                                            class="btn-link link">{{$bneGuide->title_en}}</button>
                                                    @if($bneGuide->upload_en!='' || $bneGuide->upload_en!=null)
                                                        <button data-lang="EN" data-url="{{$bneGuide->id}}"
                                                                class="btn-link download"><i class="fa fa-download"></i>
                                                        </button>
                                                        <button data-lang="EN" data-url="{{$bneGuide->id}}"
                                                                class="btn-link text-danger deleteGuide"><i
                                                                    class="far fa-trash-alt"></i></button>
                                                    @endif
                                                </li>
                                            @endif
                                        </ul>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </main>
    </div>
    <div id="grdp" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <form method="post" action="{{route('upload-grdp')}}" enctype="multipart/form-data">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">GRDP upload</h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-xs-12 col-sm-12">
                            <h5 id="grdp-title"></h5>
                            <div class="form-group file-group">
                                <input type="hidden" name="lang" id="grdp-lang"/>
                                <input type="hidden" name="title" id="grdp-title-data"/>
                                <input type="hidden" name="id" id="grdp-id"/>
                                <input type="file" name="file" class="file"/>
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control" disabled placeholder=""/>
                                    <span class="input-group-btn">
                      <button class="browse btn btn-default" type="button">Browse</button>
                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <div id="project" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <form method="post" action="{{route('upload-project')}}" enctype="multipart/form-data">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Project Template Upload</h4>
                        </div>
                        <div class="modal-body">
                            <div class="col-xs-12 col-sm-12">
                                <h5 id="grdp-title"></h5>
                                <div class="form-group file-group">
                                    <input type="hidden" name="lang" id="project-lang"/>
                                    <input type="hidden" name="title" id="project-title-data"/>
                                    <input type="hidden" name="id" id="project-id"/>
                                    <input type="file" name="file" class="file"/>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control" disabled placeholder=""/>
                                        <span class="input-group-btn">
                          <button class="browse btn btn-default" type="button">Browse</button>
                        </span>
                                    </div>
                                </div>
                            </div>
    
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
    
            </div>
        </div>
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <form method="post" id="file-input" action="{{route('upload-guide-list')}}"
                      enctype="multipart/form-data">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">File upload</h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-xs-12 col-sm-12">
                            <h5 id="title"></h5>
                            <div class="form-group file-group">
                                <input type="hidden" name="lang" id="lang"/>
                                <input type="hidden" name="title" id="title-data"/>
                                <input type="hidden" name="id" id="id"/>
                                <input type="file" name="file" class="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/>
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control" disabled placeholder=""/>
                                    <span class="input-group-btn">
                      <button class="browse btn btn-default" type="button">Browse</button>
                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <script type="text/javascript">
        $('.link').click(function (event) {
            $('#lang').val($(this).data('lang'))
            $('#id').val($(this).data('id'))
            $('#title').text($(this).text())
            $('#title-data').val($(this).text())
        })
        $('.grdp-link').click(function (event) {
            $('#grdp-lang').val($(this).data('lang'))
            $('#grdp-id').val($(this).data('id'))
            $('#grdp-title').text($(this).text())
            $('#grdp-title-data').val($(this).text())
        })
        $('.project-link').click(function (event) {
            $('#project-lang').val($(this).data('lang'))
            $('#project-id').val($(this).data('id'))
            $('#project-title').text($(this).text())
            $('#project-title-data').val($(this).text())
        })
        $('.download').click(function (event) {
            var id = $(this).data('url');
            window.location.href = "download-guide/" + $(this).data('lang') + "/" + id;
        })
        $('.grdp-download').click(function (event) {
            var id = $(this).data('url');
            window.location.href = "download-grdp/" + $(this).data('lang') + "/" + id;
        })
        $('.deleteGuide').click(function (event) {
            var id = $(this).data('url');
            window.location.href = "delete-guide/" + $(this).data('lang') + "/" + id;
        })
        $('.deleteGrdp').click(function (event) {
            var id = $(this).data('url');
            window.location.href = "delete-grdp/" + $(this).data('lang') + "/" + id;
        })
    </script>
@endsection