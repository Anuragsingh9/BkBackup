@extends('layouts.master_superadmin')
@section('content')
    <div class="app-body">
        <main class="main">
            <div class="container page-content">
                <div class="col-xs-12 col-sm-12">
                    <h4 class="site-color mt-20 mb-30">
                        <strong>Transcribe Tracking</strong>
                    </h4>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <div class="table-responsive">
                        @if(isset($data) && $data!=NULL)
                            <table class="table table-hover table-sm">
                                <thead>
                                <tr>
                                    <th>Account Id</th>
                                    <th>This Month</th>
                                    <th>Total Time</th>
                                    <th>Notes Time</th>
                                    <th>Assistance Time</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $raw)
                                    <tr>
                                        <td>{{ $raw->hostname->fqdn }}</td>
                                        <td>{{ gmdate("H:i:s", $raw->this_month) }}</td>
                                        <td>{{ gmdate("H:i:s", $raw->total_time) }}</td>
                                        <td>{{ gmdate("H:i:s", $raw->noted_time) }}</td>
                                        <td>{{ gmdate("H:i:s", $raw->assistance_time) }}</td>
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
