{{--This is for the first step of registration with Email --}}
@extends('superadmin::layouts.master')
@component('superadmin::components.auth_header')@endcomponent
@component('superadmin::components.navigation_bar')@endcomponent
@section('content')
    @component("superadmin::components.messages_box") @endcomponent

    <div class="container page-content">
        <div class="row">
            <div class="col-xs-12 col-sm-12 pb-5">
                <h4 class="color1Txt mt-20 mb-30">
                    <strong>Domain Name</strong>
                </h4>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="text-center">S.No.</th>
                            <th class=" text-center">Name</th>
                            <th class="col-2 text-center">Org Name</th>
                            <th class="col-3 text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($hostnames as $k => $hostname)
                            <tr>
                                <td style="width: 60px" class=" text-center align-middle">{{ $k +1  }}</td>
                                <td class="align-middle">{{$hostname->fqdn}}</td>
                                <td class="align-middle text-center">
                                    {{ isset($hostname->organisation->fname)
                                        ? "{$hostname->organisation->fname} {$hostname->organisation->lname}"
                                         : null
                                    }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('su-account-access', ['hostnameId' => $hostname->id]) }}"
                                       class="btn btn-sm btn-primary btn-c2-h-o" target="_blank">Access</a>
                                    <a href="{{ route('su-account-setting', ['accountId' => $hostname->id]) }}"
                                       class="btn btn-sm btn-primary btn-c2-h-o">Settings</a>
                                    <a href=""
                                       class="btn btn-sm btn-primary btn-c2-h-o" onclick="showPopUp( '{{$hostname->fqdn}}','{{ route('su-delete-acc', ['accountId' => $hostname->id]) }}' ); return false;">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showPopUp(accountName, url) {
            const index = accountName.indexOf('.'); // getting the index first '.'
            const accName = accountName.slice(0, index) // extracting domain name from account name
            const promptValue = prompt("Enter your domain name");
            if (promptValue) {
                if (accName === promptValue) {
                    document.location.href = url;
                } else {
                    alert('Please enter correct domain');
                }
            }
        }
    </script>
@endsection
