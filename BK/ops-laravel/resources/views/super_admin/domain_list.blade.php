@extends('layouts.master_superadmin')
@section('content')
<div class="app-body">
    <main class="main">
        <div class="container page-content">
           
            <div class="col-xs-12 col-sm-12">
                <h4 class="site-color mt-20 mb-30">
                    <strong>Domain Name</strong>
                </h4> 
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Name</th>
                                <th>Org Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $key=>$item)
                            <tr>
                                <td width="60px">{{ $key+1 }}.</td>
                                <td>{{ $item['fqdn'] }}</td>
                                <td>{{ $item->organisation['name_org'] }}</td>
                                <td width="156px">
                                    <a href="{{ route('access',$item['id']) }}" class="btn btn-primary actionButton" target="_blank">Access</a>
                                    <a href="{{ route('settings',$item['id']) }}" class="btn btn-primary actionButton">Settings</a>
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
@endsection