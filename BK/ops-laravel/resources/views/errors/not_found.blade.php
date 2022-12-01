@extends('layouts.master')
@section('content')
    <div class="app-body">
        <main class="main">
           
                <div class="container page-content">
                    <div class="col-xs-6 col-sm-6 col-sm-offset-3 text-center">
                        <div class="" style="padding:32px 64px;color: #da2727;">
                            <h1>Sorry</h1>
                            <h1>404 - 
                                @if(isset($error))
                                    {!! $error !!}
                                @else
                                    page cannot be found
                                @endif
                            </h1>
                        </div>
                    </div>
                </div>
           
        </main>
    </div>
@endsection