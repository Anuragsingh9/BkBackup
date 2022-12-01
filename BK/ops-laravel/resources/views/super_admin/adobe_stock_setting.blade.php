@extends('layouts.master_superadmin')
@section('content')
    <div class="app-body">
        <main class="main">
            <div class="container page-content">
                <div class="col-xs-12 col-sm-12">
                    <h4 class="site-color mt-20 mb-30">
                        <strong>Adobe Stock Tracking</strong>
                    </h4>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <div class="table-responsive">
                        @if(isset($data) && $data!=NULL)
                            <table class="table table-hover table-sm">
                                <thead>
                                <tr>
                                    <th>Account Id</th>
                                    <th>Items Used This Month</th>
                                    <th>Items Used Total</th>
                                    <th>Items Bought This Month</th>
                                    <th>Items Bought Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $raw)
                                    <tr>
                                        <td>{{ $raw->hostname->fqdn }}</td>
                                        <td>{{ $raw->used_this_month }}</td>
                                        <td>{{ $raw->used_total }}</td>
                                        <td>{{ $raw->bought_this_month }}</td>
                                        <td>{{ $raw->bought_total }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            Nothing to show
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>

@endsection
