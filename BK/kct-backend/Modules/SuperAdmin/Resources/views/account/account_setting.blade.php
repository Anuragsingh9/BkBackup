{{--This is for the first step of registration with Email --}}

@extends('superadmin::layouts.master')
@component('superadmin::components.auth_header')@endcomponent
@component('superadmin::components.navigation_bar')@endcomponent
@section('content')

    <div class="container group_setting_main_div">
        <h4 class="text_dark_groupSetting">{{ __('superadmin::labels.account_settings',['fqdn' => implode(', ', $fqdns)]) }}</h4>
        <div class="group_setting_wrap">
            <form action="{{ route('su-account-setting-update', ['accountId'=> $accountId]) }}" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="accountId" value="{{ $accountId }}">
                <div class="setting_block_groupSetting">
                    <div class="row col-md-12 block_header_groupSetting">
                        <div class="pr-2 icon-col"><img src="{{asset('icon/system.svg')}}"
                                                        class="text-dark_groupSetting"></div>
                        <div class="setting_bock_heading">
                            <div class="col-md-12">
                                {{ __('superadmin::labels.manage_groups') }}
                                <p class="text-small_groupSetting">how to benefit the most from the interface ... why
                                    complete the profile... how to use search, my list, ..</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 ">
                        <div class="sub_setting_groupSetting">
                            <div class="row single_groupSetting py-3_custom">
                                <div class="col-sm-4 padding-left-setting">
                                    <p class="strogg_txt m-0">{{ __('superadmin::labels.domain') }}:</p>
                                </div>
                                <div class="col-sm-8 text_dark_groupSetting">
                                    {{implode(', ', $fqdns)}}
                                </div>
                            </div>
                            <div class="row single_groupSetting py-3_custom">
                                <div class="col-sm-4 padding-left-setting">
                                    <p class="strogg_txt m-0">{{ __('superadmin::labels.super_group') }}:</p>
                                </div>
                                <div class="col-sm-8 text_dark_groupSetting">
                                    <a href="{{ route('su-account-access', ['hostnameId' => $accountId]) }}">Main {{ $subDomainUrl }}
                                        Group</a>
                                </div>
                            </div>
                            <div class="row single_groupSetting py-3_custom">
                                <div class="col-sm-4 padding-left-setting">
                                    <p class="strogg_txt m-0">{{ __('superadmin::labels.group_creation') }}:</p>
                                </div>
                                <div class="col-sm-8 text_dark_groupSetting">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" value="1"
                                               name="allow_multi_group" {{$accountSettings['allow_multi_group'] ? "checked" : ""}}/>
                                    </div>
                                </div>
                            </div>
                            <div class="row single_groupSetting py-3_custom">
                                <div class="col-sm-4 padding-left-setting">
                                    <p class="strogg_txt m-0">{{__('superadmin::labels.max_groups')}}:</p>
                                </div>
                                <div class="col-sm-8 text_dark_groupSetting">
                                    <select name="max_group_allowed" id="dropdown-value"
                                            class="form-control form-control-md select_custom">
                                        <option id="removeValue" value="{{$accountSettings['max_group_limit']}}"
                                                disabled
                                                selected>{{$accountSettings['max_group_limit']}}</option>
                                        @for($i=1;$i<=100;$i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="row single_groupSetting py-3_custom">
                                <div class="col-sm-4 padding-left-setting">
                                    <p class="strogg_txt m-0">{{__('superadmin::labels.allow_user')}}:</p>
                                </div>
                                <div class="col-sm-8 text_dark_groupSetting">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" value="1"
                                               name="allow_user_to_create_group" {{$accountSettings['allow_user_to_group_creation'] ? "checked" : ""}}/>
                                    </div>
                                </div>
                            </div>
                            <div class="row single_groupSetting py-3_custom">
                                <div class="col-sm-4 padding-left-setting">
                                    <button type="submit"
                                            class="btn btn-primary btn-primary_custom">{{__('superadmin::labels.btn_save')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Domain Setting -->
                <div class="setting_block_groupSetting">
                    <div class="row col-md-12 block_header_groupSetting">
                        <div class="pr-2 icon-col"><img src="{{asset('icon/system.svg')}}"
                                                        class="text-dark_groupSetting"></div>
                        <div class="setting_bock_heading">
                            <div class="col-md-12">
                                {{__('superadmin::labels.domain_settings')}}
                                <p class="text-small_groupSetting">Lorium Ipsum is Dummy Text</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 ">
                        <div class="sub_setting_groupSetting">
                            <div class="row single_groupSetting py-3_custom">
                                <p class="strogg_txt m-0 padding-left-setting">{{__('superadmin::labels.technical_settings')}}
                                    :</p>
                                <div class="col-sm-4 padding-left-setting">
                                    <p class="">{{__('superadmin::labels.technical_settings_config')}}:</p>
                                </div>
                                <div class="col-sm-8 text_dark_groupSetting">
                                    <a href=" {{ $technicalSettingUrl }}">{{ $technicalSettingUrl }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Analytics Settings -->
                <div class="setting_block_groupSetting">
                    <div class="row col-md-12 block_header_groupSetting">
                        <div class="pr-2 icon-col"><img src="{{asset('icon/system.svg')}}"
                                                        class="text-dark_groupSetting"></div>
                        <div class="setting_bock_heading">
                            <div class="col-md-12">
                                {{__('superadmin::labels.analytics_settings')}}
                                <p class="text-small_groupSetting">Lorium Ipsum is Dummy Text</p>
                            </div>
                        </div>
                        <div class="form-check form-switch analytics-switch">
                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                   name="acc_analytics" {{$accountSettings['acc_analytics'] ? "checked" : ""}} />
                        </div>
                    </div>
                    <div class="row single_groupSetting py-3_custom">
                        <div class="col-sm-4 padding-left-setting">
                            <button type="submit"
                                    class="btn btn-primary btn-primary_custom">{{__('superadmin::labels.btn_save')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $("#dropdown-value").click(function () {
            $("#removeValue").css("display", "none");
        });
    </script>
@endsection
