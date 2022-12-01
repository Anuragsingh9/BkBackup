@extends('layouts.master')
@section('content')
        <div class="app-body">
            <main class="main">
                <div class="container page-content">
                    <div class="col-xs-12 col-sm-12 text-center text-warning mt-50" style="font-size:18px ">                    
                       <p>This system will redirect you in a moment.</p>
                       <img src="{{url('/public/img/loader_front.gif')}}" class="center-block"/>
                    </div>
                </div>
            </main>
        </div>
<script>
$(document).ready(function(){
    window.location = "{{$redirect_url}}";
});
</script>
@endsection