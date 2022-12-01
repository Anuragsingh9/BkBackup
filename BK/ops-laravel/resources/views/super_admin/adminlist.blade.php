@extends('layouts.master_superadmin')
@section('content')
<div class="app-body">
    <main class="main">
        <div class="container page-content">
            <div class="col-xs-12 col-sm-12">
                <h4 class="site-color mt-20 mb-30">
                    <strong>List of Super Admin</strong>
                </h4> 
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Create Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $key=>$item)
                            <tr>
                                <td width="10px">{{$key+1 }}.</td>
                                <td width="15px">{{$item->name}}</td>
                                <td width="20px">{{$item->email}}</td>
                                <td width="25px">{{$item->created_at}}</td>
                                <td width="20px">
                                    <a href="{{ route('newsuperadmin',$item['id']) }}" class="btn btn-primary actionButton">Modify</a>
                                    <button onclick="delete_me(this.id)" id="{{$item['id']}}"  class="btn btn-primary actionButton">Remove</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
<script>
    function delete_me(id){
    if (confirm("Are You Sure You want to delete")){
    location.href = "{!!route('deltesuperadmin')!!}/" + id;
    }
    }
</script>
@endsection