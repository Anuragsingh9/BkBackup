{{--This is for the first step of registration with Email --}}

@extends('superadmin::layouts.master')
@component('superadmin::components.auth_header')@endcomponent
@component('superadmin::components.navigation_bar')@endcomponent
@section('content')
    <div class="container page-content">
        <div class="row justify-content-center">
            <div class="col-12 mx-auto">
                <h4 class="color1Txt mt-20 mb-30">
                    <strong>{{ __('superadmin::labels.account_setting', ['fqdn' => implode(', ', $fqdns)]) }}</strong>
                </h4>
                <div class="my-4">
                    <form action="{{ route('su-account-setting-update', ['accountId'=> $accountId]) }}" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="accountId" value="{{ $accountId }}">
                        <div class="col-xs-12 col-sm-12">
                            <hr class="my-4"/>
                            <strong class="mb-0">{{ __('superadmin::labels.event_settings') }}</strong>

                            <div class="list-group mb-5 mt-3 shadow">
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <strong class="mb-0">{{ __('superadmin::labels.enable_event') }}</strong>
                                        </div>
                                        <div class=" col-auto form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="" value="1"
                                                   name="events_enabled" {{ $accountSettings['events_enabled'] ? "checked" : "" }}>
                                        </div>
                                    </div>
                                </div>
                                {{-- Keep Contact Settings --}}
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <strong class="mb-0">{{ __('superadmin::labels.enable_kct') }}</strong>
                                        </div>
                                        <div class=" col-auto form-check form-switch">
                                            <label for="kct_enabled"></label>
                                            <input class="form-check-input"
                                                   type="checkbox" id="kct_enabled"
                                                   value="1"
                                                   name="kct_enabled" {{ $accountSettings['kct_enabled'] ? "checked" : "" }}>
                                        </div>
                                    </div>
                                </div>
                                {{-- Conference Settings --}}
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <strong
                                                class="mb-0">{{ __('superadmin::labels.enable_conference') }}</strong>
                                        </div>
                                        <div class=" col-auto form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="" value="1"
                                                   name="conference_enabled" {{ $accountSettings['conference_enabled'] ? "checked" : "" }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4"/>
                            <strong class="mb-0">{{ __('superadmin::labels.conference_settings') }}</strong>
                            <div class="list-group mb-5 mt-3 shadow">
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <strong class="mb-0">{{ __('superadmin::labels.conference_type') }}</strong>
                                        </div>
                                        <div class=" col-auto form-check form-switch">
                                            <select name="event_conference_type" id="event_conference_type">
                                                <option
                                                    value="1" {{ $conferenceSettings['current_conference'] == 1 ? 'selected': '' }}>
                                                    {{ __('superadmin::words.zoom') }}
                                                </option>
                                                <option
                                                    value="2" {{ $conferenceSettings['current_conference'] == 2 ? 'selected': '' }}>
                                                    {{ __('superadmin::words.bluejeans') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="" id="bluejeans-form-section">
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <strong
                                                    class="mb-0">{{ __('superadmin::labels.bj_client_id') }}</strong>
                                            </div>
                                            <div class=" col-5 form-check form-switch">
                                                <input type="text"
                                                       class="col-12"
                                                       id="event_bluejeans_client_id"
                                                       name="event_bluejeans_client_id"
                                                       value="{{ $conferenceSettings['bluejeans']['app_key'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <strong
                                                    class="mb-0">{{ __('superadmin::labels.bj_client_secret') }}</strong>
                                            </div>
                                            <div class="col-5 form-check form-switch">
                                                <input type="text"
                                                       class="col-12"
                                                       id="event_bluejeans_client_secret"
                                                       name="event_bluejeans_client_secret"
                                                       value="{{ $conferenceSettings['bluejeans']['app_secret'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <strong
                                                    class="mb-0">{{ __('superadmin::labels.bj_client_email') }}</strong>
                                            </div>
                                            <div class="col-5 form-check form-switch">
                                                <input type="text"
                                                       class="col-12"
                                                       id="event_bluejeans_client_email"
                                                       name="event_bluejeans_client_email"
                                                       value="{{ $conferenceSettings['bluejeans']['app_email'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <strong
                                                    class="mb-0">{{ __('superadmin::labels.bj_client_license') }}</strong>
                                            </div>
                                            <div class="col-auto form-check form-switch">
                                                <input type="text"
                                                       id="event_bluejeans_licenses"
                                                       name="event_bluejeans_licenses"
                                                       value="{{ $conferenceSettings['bluejeans']['number_of_license'] }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="" id="zoom-form-section">
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <strong
                                                    class="mb-0">{{ __('superadmin::labels.zoom_client_key') }}</strong>
                                            </div>
                                            <div class="col-5 form-check form-switch">
                                                <input type="text"
                                                       class="col-12"
                                                       id="event_zoom_key"
                                                       name="event_zoom_key"
                                                       value="{{ $conferenceSettings['zoom']['app_key'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <strong
                                                    class="mb-0">{{ __('superadmin::labels.zoom_client_secret') }}</strong>
                                            </div>
                                            <div class="col-5 form-check form-switch">
                                                <input type="text"
                                                       class="col-12"
                                                       id="event_zoom_secret"
                                                       name="event_zoom_secret"
                                                       value="{{ $conferenceSettings['zoom']['app_secret'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <strong
                                                    class="mb-0">{{ __('superadmin::labels.zoom_client_email') }}</strong>
                                            </div>
                                            <div class="col-5 form-check form-switch">
                                                <input type="text"
                                                       class="col-12"
                                                       id="event_zoom_email"
                                                       name="event_zoom_email"
                                                       value="{{ $conferenceSettings['zoom']['app_email'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <strong
                                                    class="mb-0">{{ __('superadmin::labels.zoom_client_license') }}</strong>
                                            </div>
                                            <div class="col-auto form-check form-switch">
                                                <input type="text"
                                                       id="event_zoom_licenses"
                                                       name="event_zoom_licenses"
                                                       value="{{ $conferenceSettings['zoom']['number_of_license'] }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 col-9 mx-auto mt-5 mb-5">
                            <button class="btn btn-primary btn-c2-h-o text-white login-button"
                                    type="submit">Update Setting
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
