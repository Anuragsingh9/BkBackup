@extends('layouts.master')
@section('content')
<div class="app-body">
    <main class="main">
        <div class="container page-content">
           @foreach($data as $k=>$val)
            @php $res=''; @endphp
                @foreach($cols as $v)
                    @if(!in_array($v,['id','updated_at','created_at','setting_value']))
                        @php                     
                            $res.='"'.$v.'"=>"'.$val->$v.'" , ';
                        @endphp
                    @endif
                @endforeach
                @php 
                    $res.='"setting_value"=>"" , ';
                    $trimVal = substr($res,0,strlen($res)-2);
                    echo '['.$trimVal.'],<br/>'; 
                @endphp                        
            @endforeach
        </div>
    </main>
</div>
@endsection
