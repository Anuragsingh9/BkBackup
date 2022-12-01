@extends('layouts.master_superadmin')
@php
    if(isset($settingData)){
        $video= collect($settingData)->firstWhere('setting_key','video_meeting_api_setting');
       if(isset($video->setting_value)){
        $videoSetting=json_decode($video->setting_value);
       }

    }

@endphp
@section('content')

    <style>
        .icontact-api-fileds label {
            font-size: 12px;
            margin-bottom: 5px;
            width: 100%;
            display: inline-block;
        }

        .icontact-api-fileds input {
            width: 100%;
            padding: 5px;
            border: 1px solid #dfdfdf;
        }
    </style>
    <div class="app-body">
        <main class="main">
            <div class="container page-content">
                <div class="col-xs-12 col-sm-12">
                    <div class="col-xs-12 col-sm-12">
                        <h5>Super Admin Settings</h5>
                        <h4 class="site-color mt-20 mb-30">
                            <strong>Configuration</strong> for <strong>{{@$host->fqdn}}</strong>
                        </h4>
                    </div>

                    <form class="" id="" action="{{ route('do-set-settings') }}" method="post"
                          onsubmit="return validate()">
                        {{-- Left side setting boxes --}}
                        <div class="col-xs-12 col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="col-xs-12">
                                        <h5 class="mb-20"><strong>Test Configuration</strong></h5>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="test_version"
                                                       value="1" {{ ($setting['test_version'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Is a Test version</label>
                                                <div class="">Until <strong>Mercredi 7 Décembre 2017</strong>
                                                    <i aria-hidden="true" class="fa fa-calendar site-color ml-10"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="col-xs-12">
                                        <h5 class="mb-20"><strong>Main configuration</strong></h5>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="mobile_enable"
                                                       value="1" {{ ($setting['mobile_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Mobile enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group mobile-multi">
                                            <label class="switch">
                                                <input type="checkbox" name="multiLoginEnabled"
                                                       value="1" {{ ($setting['multiLoginEnabled'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Mobile multi-Org enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="email_enabled"
                                                       value="1" {{ ($setting['email_enabled'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Email enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="travel_enable"
                                                       value="1" {{ ($setting['travel_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Travel enabled</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="col-xs-12">
                                        <h5 class="mb-20"><strong>Icontact configuration</strong></h5>
                                        <div class="form-group icontact-api-fileds">
                                            <label for="">ICONTACT API APP ID</label>
                                            <input type="text" name="ICONTACT_API_APP_ID"
                                                   value={{ (isset($setting->setting['ICONTACT_API_APP_ID']) && $setting->setting['ICONTACT_API_APP_ID'])?$setting->setting['ICONTACT_API_APP_ID']:'' }} >
                                        </div>

                                        <div class="form-group icontact-api-fileds">
                                            <label for="">ICONTACT API PASSWORD</label>
                                            <input type="text" name="ICONTACT_API_PASSWORD"
                                                   value={{ (isset($setting->setting['ICONTACT_API_PASSWORD']) && $setting->setting['ICONTACT_API_PASSWORD'])?$setting->setting['ICONTACT_API_PASSWORD']:'' }}>
                                        </div>

                                        <div class="form-group icontact-api-fileds">
                                            <label for="">ICONTACT API USERNAME</label>
                                            <input type="text" name="ICONTACT_API_USERNAME"
                                                   value={{ (isset($setting->setting['ICONTACT_API_USERNAME']) && $setting->setting['ICONTACT_API_USERNAME'])?$setting->setting['ICONTACT_API_USERNAME']:'' }}>

                                        </div>
                                        <div class="form-group icontact-api-fileds">
                                            <label for="">ICONTACT API CLIENT FOLDER ID</label>
                                            <input type="text" name="ICONTACT_CLIENT_FOLDER_ID"
                                                   value={{ (isset($setting->setting['ICONTACT_CLIENT_FOLDER_ID']) && $setting->setting['ICONTACT_CLIENT_FOLDER_ID'])?$setting->setting['ICONTACT_CLIENT_FOLDER_ID']:'' }}>

                                        </div>
                                        <div class="form-group icontact-api-fileds">
                                            <label for="">ICONTACT API ICONTACT ACCOUNT ID</label>
                                            <input type="text" name="ICONTACT_ACCOUNT_ID"
                                                   value={{ (isset($setting->setting['ICONTACT_ACCOUNT_ID']) && $setting->setting['ICONTACT_ACCOUNT_ID'])?$setting->setting['ICONTACT_ACCOUNT_ID']:'' }}>

                                        </div>
                                        {{--    <div class="form-group switch-group">
                                                <label class="switch">
                                                    <input type="text" name="light_version" value="1" {{ ($setting['light_version'])?'checked':'' }}>
                                                    <span class="slider"></span>
                                                </label>
                                                <div class="label-txt">
                                                    <label>Light restricted version</label>
                                                </div>
                                            </div>
                                            <div class="form-group switch-group">
                                                <label class="switch">
                                                    <input type="checkbox" name="mobile_enable" value="1" {{ ($setting['mobile_enable'])?'checked':'' }}>
                                                    <span class="slider"></span>
                                                </label>
                                                <div class="label-txt">
                                                    <label>Mobile enabled</label>
                                                </div>
                                            </div>
                                            <div class="form-group switch-group mobile-multi">
                                                <label class="switch">
                                                    <input type="checkbox" name="multiLoginEnabled"
                                                           value="1" {{ ($setting['multiLoginEnabled'])?'checked':'' }}>
                                                    <span class="slider"></span>
                                                </label>
                                                <div class="label-txt">
                                                    <label>Mobile multi-Org enabled</label>
                                                </div>
                                            </div>
                                            <div class="form-group switch-group">
                                                <label class="switch">
                                                    <input type="checkbox" name="email_enabled" value="1" {{ ($setting['email_enabled'])?'checked':'' }}>
                                                    <span class="slider"></span>
                                                </label>
                                                <div class="label-txt">
                                                    <label>Email enabled</label>
                                                </div>
                                            </div>
                                            <div class="form-group switch-group">
                                                <label class="switch">
                                                    <input type="checkbox" name="travel_enable" value="1" {{ ($setting['travel_enable'])?'checked':'' }}>
                                                    <span class="slider"></span>
                                                </label>
                                                <div class="label-txt">
                                                    <label>Travel enabled</label>
                                                </div>
                                            </div>
                                            <div class="form-group switch-group">
                                                <label class="switch">
                                                    <input type="checkbox" name="wvm_enable" value="1" {{ ($setting['wvm_enable'])?'checked':'' }}>
                                                    <span class="slider"></span>
                                                </label>
                                                <div class="label-txt">
                                                    <label>Workshop Video Meetings enable</label>
                                                </div>
                                            </div>
                                            <div class="form-group switch-group">
                                                <label class="switch">
                                                    <input type="checkbox" name="fvm_enable" value="1" {{ ($setting['fvm_enable'])?'checked':'' }}>
                                                    <span class="slider"></span>
                                                </label>
                                                <div class="label-txt">
                                                    <label>Flash Video Meetings enable</label>
                                                </div>
                                            </div>--}}
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">  {{-- ADOBE STOCK SETTING--}}
                                <div class="panel-body">
                                    <div class="col-xs-12">
                                        <h5 class="mb-20"><strong>Photo Stock Configuratution</strong></h5>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" id="stocksetting" onchange="toggleStockField()"
                                                       name="stock_setting_enabled"
                                                       value="1" {{ (isset($setting['setting']['stock_setting']['enabled']) && ($setting['setting']['stock_setting']['enabled']) ?'checked':'') }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Enabled</label>
                                            </div>
                                        </div>
                                        <div id="stock-field">

                                            <div class="form-group switch-group">
                                                <label class="">
                                                    <input type="text" class="col-md-2" name="stock_allowed_number"
                                                           value="{{ isset($setting['setting']['stock_setting']) ? $setting['setting']['stock_setting']['max_allowed'] : 0 }}">
                                                    <label>Monthly Allowed</label>
                                                </label>
                                            </div>
                                            <div class="form-group switch-group">
                                                <label class="">
                                                    <input type="text" class="col-md-2" name="stock_available_credit"
                                                           value="{{ isset($setting['setting']['stock_setting']) ? $setting['setting']['stock_setting']['available_credit'] : 0 }}">
                                                    <label>Available Credit</label>
                                                </label>
                                            </div>
                                            <div class="form-group switch-group">
                                                <label class="">
                                                    <input type="text" class="col-md-2" name="stock_renewal"
                                                           value="{{ isset($setting['setting']['stock_setting']) ? $setting['setting']['stock_setting']['renewal_date'] : 0 }}">
                                                    <label>Monthly Renewal Date</label>
                                                </label>
                                            </div>
                                            {{--                                        {{ dd($stock_data) }}--}}
                                            @isset($stock_data[0])
                                                <div class="form-group switch-group">
                                                    {{ isset($stock_data[0]->used_this_month) ? $stock_data[0]->used_this_month : 0 }}
                                                    Used This Month
                                                </div>
                                                <div class="form-group switch-group">
                                                    {{ isset($stock_data[0]->bought_this_month)?$stock_data[0]->bought_this_month:0 }}
                                                    Bought This Month
                                                </div>
                                                <div class="form-group switch-group">
                                                    {{ isset($stock_data[0]->used_total)?$stock_data[0]->used_total:0 }}
                                                    UsedTotal
                                                </div>
                                                <div class="form-group switch-group">
                                                    {{ isset($stock_data[0]->bought_total)?$stock_data[0]->bought_total:0 }}
                                                    Bought Total
                                                </div>
                                            @endisset
                                        </div>

                                        <h5 class="mb-20"><strong>ADOBE STOCK KEY </strong></h5>
                                        <div class="form-group icontact-api-fileds">
                                            <label for="">ADOBE STOCK ACCESS KEY</label>
                                            <input type="text" name="adobe_access_key"
                                                   value="{{ (isset($stockData->access_key) && !empty($stockData->access_key))?$stockData->access_key:'' }}">
                                        </div>

                                        <div class="form-group icontact-api-fileds">
                                            <label for="">APP NAME</label>
                                            <input type="text" name="adobe_app_name"
                                                   value="{{ (isset($stockData->app_name) && !empty($stockData->app_name))?$stockData->app_name:'' }}">
                                        </div>

                                    </div>
                                </div>
                            </div> {{-- END OF ADOBE STOCK SETTING --}}
                            {{--Start Of video Meeting api setting --}}
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="col-xs-12">
                                        <h5 class="mb-20"><strong>Blue Jeans Meeting configuration</strong></h5>
                                        <div class="form-group icontact-api-fileds">
                                            <label for="">Blue Jeans Client ID</label>
                                            <input type="text" name="client_id"

                                                   value={{ (isset($videoSetting->client_id) && !empty($videoSetting->client_id))?$videoSetting->client_id:'' }} >
                                        </div>

                                        <div class="form-group icontact-api-fileds">
                                            <label for="">Blue Jeans Client Secret</label>
                                            <input type="text" name="client_secret"
                                                   value={{ (isset($videoSetting->client_secret) && !empty($videoSetting->client_secret))?$videoSetting->client_secret:'' }}>
                                        </div>
                                        <div class="form-group icontact-api-fileds">
                                            <label for="" >Number of Licences</label>
                                            <input type="number"  class="col-xs-2"name="vm_bluejeans_licenses"
                                                   value="{{ (isset($videoSetting->number_of_license) && !empty($videoSetting->number_of_license))?$videoSetting->number_of_license: '1' }}">
                                        </div>
                                        <div class="form-group icontact-api-fileds">
                                            <label for="" ></label>
                                            <a type="number" class="col-xs-12" href="{{route('sync-bluejeans-users', ['id' => $id])}}">
                                                Sync BlueJeans Users
                                            </a>

                                            @if(isset($errors) && $errors->has('bjmSyncError'))
                                                <p class="col-xs-12" style="color:red">{{$errors->first('bjmSyncError')}}</p>
                                            @endif
                                        </div>
                                        <div class="form-group icontact-api-fileds">
                                            <label for="" ></label>
                                            <strong style="color:gray">** Please click on the <i>`Sync BlueJeans Users` </i>  button after saving credentials to sync the new bluejeans account users.</strong>
                                        </div>

                                    </div>


                                    {{--                                    <div class="col-xs-12">--}}
                                    {{--                                      --}}
                                    {{--                                    </div>--}}
                                </div>
                            </div>
                            {{--End  Of video Meeting api setting --}}
                            {{--                     YouTube   --}}
                            <div class="panel panel-default">
                                <div class="panel-body">
                                <h5 class="mb-20"><strong>YouTube Setting </strong></h5>
                                <div class="form-group icontact-api-fileds">
                                    <label for="">Client Id</label>
                                    <input type="text" name="clientid"
                                           value={{ (isset($youtubeData->clientid) && !empty($youtubeData->clientid))?$youtubeData->clientid:'' }} >
                                </div>

                                <div class="form-group icontact-api-fileds">
                                    <label for="">Client Secret</label>
                                    <input type="text" name="clientsecret"
                                           value="{{ (isset($youtubeData->clientsecret) && !empty($youtubeData->clientsecret))?$youtubeData->clientsecret:'' }}">
                                </div>

                                <div class="form-group icontact-api-fileds">
                                    <label for="">Youtube Api Key</label>
                                    <input type="text" name="youtube_api_key"
                                           value="{{ (isset($youtubeData->youtube_api_key) && !empty($youtubeData->youtube_api_key))?$youtubeData->youtube_api_key:'' }}">
                                </div>

                                <div class="form-group icontact-api-fileds">
                                    <label for="">Youtube Channel Id</label>
                                    <input type="text" name="youtube_channel_key"
                                           value="{{ (isset($youtubeData->youtube_channel_key) && !empty($youtubeData->youtube_channel_key))?$youtubeData->youtube_channel_key:'' }}">
                                </div>

                            </div>
                            </div>
                            {{--                     YouTube End   --}}
                            {{-- Event setting start --}}
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h5 class="mb-20"><strong>Event Settings</strong></h5>
                                    <div class="form-group switch-group">
                                        <label class="switch">
                                            <input type="checkbox" name="event_enable"
                                                   onchange="toggleEventSub(this)"
                                                   value="1" {{ (isset($setting->setting['event_enabled']) && $setting->setting['event_enabled'])?'checked':'' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <div class="label-txt">
                                            <label>Event Enabled</label>
                                        </div>
                                    </div>
                                    {{-- Event WP Setting --}}
                                    <div class="form-group switch-group mobile-multi event-sub">
                                        <label class="switch">
                                            <input type="checkbox"
                                                   name="event_wp_enabled"
                                                   onchange="toggleWPSetting()"
                                                   id="event_wp_enabled"
                                                   value="1"
                                                    {{(isset($setting->setting['event_settings']['wp_enabled'])
                                                        && $setting->setting['event_settings']['wp_enabled'])
                                                        ?'checked':'' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <div class="label-txt">
                                            <label>Event WP Enabled</label>
                                        </div>
                                        <div class="col-xs-12" id="event_wp_setting">
                                        {{-- <h5 class="mb-20"><strong>Event WP </strong></h5>--}}
                                            <div class="form-group icontact-api-fileds">
                                                <label for="">WP URL</label>
                                                <input type="text" name="event_wp_url"
                                                       value={{ (isset($eventData->event_wp_setting->wp_url) && !empty($eventData->event_wp_setting->wp_url))?$eventData->event_wp_setting->wp_url:''}}>
                                            </div>
                                            <div class="form-group icontact-api-fileds">
                                                <label for="">WP USER PASS</label>
                                                <input type="text" name="event_wp_pass"
                                                       value="{{ (isset($eventData->event_wp_setting->wp_pass) && !empty($eventData->event_wp_setting->wp_pass))?$eventData->event_wp_setting->wp_pass:'' }}">
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Event Blue jeans Setting --}}
                                    <div class="form-group switch-group mobile-multi event-sub">
                                        <label class="switch">
                                            <input type="checkbox"
                                                   name="event_conference_enabled"
                                                   onchange="toggleEventBlueJeansSetting()"
                                                    id="event_bluejeans_enabled"
                                                   value="1" {{ (isset($setting->setting['event_settings']['event_conference_enabled']) && $setting->setting['event_settings']['event_conference_enabled'])?'checked':'' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <div class="label-txt">
                                            <label>Event Webinars enabled</label>
                                        </div>

                                    {{---------------Zoom setting form---------------}}
                                        {{--
                                        <div class="col-xs-12" id="zoom_setting">
                                            <div class="form-group icontact-api-fileds">
                                                <label for="">Zoom ID</label>
                                                <input type="text" name="event_bluejeans_client_id"
                                                       value={{ (isset($eventData->event_bluejeans_setting->bluejeans_event_client_id) && !empty($eventData->event_bluejeans_setting->bluejeans_event_client_id))?$eventData->event_bluejeans_setting->bluejeans_event_client_id:'' }} >
                                            </div>
                                            <div class="form-group icontact-api-fileds">
                                                <label for="">Zoom Secret</label>
                                                <input type="text" name="event_bluejeans_client_secret"
                                                       value="{{ (isset($eventData->event_bluejeans_setting->bluejeans_event_client_secret) && !empty($eventData->event_bluejeans_setting->bluejeans_event_client_secret))?$eventData->event_bluejeans_setting->bluejeans_event_client_secret:'' }}">
                                            </div>
                                        </div>

--}}
                                {{---------------BlueJeans setting form---------------}}
                                    <div class="col-xs-12" id="event_bluejeans_setting">
                                        <select name="event_conference_type" id="event_conference_type" onchange="showConferenceFields()">
{{--                                                <option value="none" name="none" >Select type -- </option>--}}
                                                <option value="zoom" name="zoom" {{ (isset($eventData->event_current_conference) && $eventData->event_current_conference == 'zoom')?'selected':'' }}
                                                >Zoom</option>
                                                <option value="bj" name="blue" {{ (isset($eventData->event_current_conference) && $eventData->event_current_conference == 'bj')?'selected':'' }}>
                                                    Blue Jeans</option>
                                            </select>
                                        </div>

                                        <div class="col-xs-12" id="bluejeans-fields">
                                            <div class="form-group icontact-api-fileds">
                                                <label for="">Blue Jeans Event Client ID</label>
                                                <input type="text" name="event_bluejeans_client_id"
                                                       value={{ (isset($eventData->event_bluejeans_setting->bluejeans_event_client_id) && !empty($eventData->event_bluejeans_setting->bluejeans_event_client_id))?$eventData->event_bluejeans_setting->bluejeans_event_client_id:'' }} >
                                            </div>
                                            <div class="form-group icontact-api-fileds">
                                                <label for="">Blue Jeans Event Client Secret</label>
                                                <input type="text" name="event_bluejeans_client_secret"
                                                       value="{{ (isset($eventData->event_bluejeans_setting->bluejeans_event_client_secret) && !empty($eventData->event_bluejeans_setting->bluejeans_event_client_secret))?$eventData->event_bluejeans_setting->bluejeans_event_client_secret:'' }}">
                                            </div>
                                            <div class="form-group icontact-api-fileds">
                                                <label for="">Blue Jeans Event User Email</label>
                                                <input type="email" name="event_bluejeans_client_email"
                                                       value="{{ (isset($eventData->event_bluejeans_setting->bluejeans_event_client_email) && !empty($eventData->event_bluejeans_setting->bluejeans_event_client_email))?$eventData->event_bluejeans_setting->bluejeans_event_client_email:'' }}">
                                            </div>
                                            <div class="form-group icontact-api-fileds">
                                                <label for="" >Number of Licences</label>
                                                <input type="number"  class="col-xs-2"name="event_bluejeans_licenses"
                                                       value="{{ (isset($eventData->event_bluejeans_setting->number_of_license) && !empty($eventData->event_bluejeans_setting->number_of_license))?$eventData->event_bluejeans_setting->number_of_license: 0}}">
                                            </div>
                                        </div>

                                        <div class="col-xs-12" id="zoom-fields">
                                            <div class="form-group icontact-api-fileds">
                                                <label for="">API Key </label>
                                                <input type="text" name="event_zoom_key"
                                                       value={{ (isset($eventData->event_zoom_setting->event_zoom_key) && !empty($eventData->event_zoom_setting->event_zoom_key))?$eventData->event_zoom_setting->event_zoom_key:'' }} >
                                            </div>
                                            <div class="form-group icontact-api-fileds">
                                                <label for="">API Secret</label>
                                                <input type="text" name="event_zoom_secret"
                                                       value="{{ (isset($eventData->event_zoom_setting->event_zoom_secret) && !empty($eventData->event_zoom_setting->event_zoom_secret))?$eventData->event_zoom_setting->event_zoom_secret:'' }}">
                                            </div>
                                            <div class="form-group icontact-api-fileds">
                                                <label for="">Zoom User Email</label>
                                                <input type="email" name="event_zoom_email"
                                                       value="{{ (isset($eventData->event_zoom_setting->event_zoom_email) && !empty($eventData->event_zoom_setting->event_zoom_email))?$eventData->event_zoom_setting->event_zoom_email:'' }}">
                                            </div>
                                        </div>
                                        </div>

                                    {{-- Event Keep Contact Setting --}}
                                    <div class="form-group switch-group mobile-multi event-sub">
                                        <label class="switch">
                                            <input type="checkbox"
                                                   name="event_keep_contact_enabled"
                                                   id="event_kct_enabled"
                                                   onchange="toggleEventKctSetting()"
                                                   value="1" {{ (isset($setting->setting['event_settings']['keep_contact_enable']) && $setting->setting['event_settings']['keep_contact_enable'])?'checked':'' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <div class="label-txt">
                                            <label>Event Keep Contact Enabled</label>
                                        </div>
                                        <div class="col-xs-12" id="event_kct_setting">
                                            <div class="form-group icontact-api-fileds">
                                                <label for="stock_allowed_number">Max number of users in virtual events</label>
                                                <input type="number" min="1" class="col-md-2" name="event_kct_max_participant"
                                                       value="{{ (isset($eventData->event_kct_setting->kct_max_participants) && !empty($eventData->event_kct_setting->kct_max_participants))?$eventData->event_kct_setting->kct_max_participants:250}}">
                                            </div>
                                            <div class="form-group icontact-api-fileds">
                                                <label for="">KeepContact Keywords</label>
                                                <input type="text" name="event_kct_keywords"
                                                       value="{{ (isset($eventData->event_kct_setting->kct_keywords) && !empty($eventData->event_kct_setting->kct_keywords))?$eventData->event_kct_setting->kct_keywords:''}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{-- Right side setting boxes --}}
                        <div class="col-xs-12 col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="col-xs-12">
                                        <h5 class="mb-20"><strong>Modules configuration</strong></h5>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="user_group_enable"
                                                       value="1" {{ ($setting['user_group_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>User Groups enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="wiki_enable"
                                                       value="1" {{ ($setting['wiki_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Wiki Enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="reminder_enable"
                                                       value="1" {{ ($setting['reminder_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Reminders enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="zip_download"
                                                       value="1" {{ ($setting['zip_download'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Zip download enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="repd_connect_mode"
                                                       value="1" {{ ($setting['repd_connect_mode'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>REPD Access enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="prepd_repd_notes"
                                                       value="1" {{ ($setting['prepd_repd_notes'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>PREDP/REPD notes enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="project_enable"
                                                       value="1" {{ ($setting['project_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Projects enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="custom_profile_enable"
                                                       value="1" {{ ($setting['custom_profile_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Custom profile enabled</label>
                                            </div>
                                        </div>

                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="meeting_meal_enable"
                                                       value="1" {{ ($setting['meeting_meal_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Meeting Meal Registration</label>
                                            </div>
                                        </div>

                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="notes_to_secretary_enable"
                                                       value="1" {{ ($setting['notes_to_secretary_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Notes to Secretary and Deputy enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="import_enable"
                                                       value="1" {{ ($setting['import_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Import enabled</label>
                                            </div>
                                        </div>

                                        <div class="form-group switch-group">

                                            <label class="switch">
                                                <input type="checkbox" name="crm_enable"
                                                       value="1" {{ (isset($setting->setting['crm_enable']) && $setting->setting['crm_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Crm enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">

                                            <label class="switch">
                                                <input type="checkbox" name="instance_enable"
                                                       value="1" {{ (isset($setting->setting['instance_enable']) && $setting->setting['instance_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Instance enabled</label>
                                            </div>
                                        </div>

                                        <div class="form-group switch-group">

                                            <label class="switch">
                                                <input type="checkbox" name="press_enable"
                                                       value="1" {{ (isset($setting->setting['press_enable']) && $setting->setting['press_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Press enabled</label>
                                            </div>
                                        </div>

                                        <div class="form-group switch-group">

                                            <label class="switch">
                                                <input type="checkbox" name="news_letter_enable"
                                                       value="1" {{ (isset($setting->setting['news_letter_enable']) && $setting->setting['news_letter_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Newsletter enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group mobile-multi">

                                            <label class="switch">
                                                <input type="checkbox" name="manage_template"
                                                       value="1" {{ (isset($setting->setting['manage_template']) && $setting->setting['manage_template'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Manage Template enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="organiser_setting_enable"
                                                       value="1" {{ (isset($setting->setting['organiser_setting_enable']) && $setting->setting['organiser_setting_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Orgadmin Capabilities</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="messenger_enable"
                                                       value="1" {{ (isset($setting->setting['messenger_enable']) && $setting->setting['messenger_enable']) ?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Messenger enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">

                                            <label class="switch">
                                                <input type="checkbox" name="workshop_graphic_enable"
                                                       value="1" {{ (isset($setting->setting['workshop_graphic_enable']) && $setting->setting['workshop_graphic_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Workshop Graphic enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">

                                            <label class="switch">
                                                <input type="checkbox" name="qualification_module_enable"
                                                       value="1" {{ (isset($setting->setting['qualification_enable']) && $setting->setting['qualification_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Qualification Module Enabled</label>
                                            </div>
                                        </div>
                                        @if((isset($setting->setting['qualification_enable']) && $setting->setting['qualification_enable']))
                                            <div class="form-group switch-group mobile-multi">

                                                <label class="switch">
                                                    <input disabled type="checkbox" name=""
                                                           value="1" {{ (in_array($setting->account_id,[38]))?'checked':'' }}>
                                                    <span class="slider"></span>
                                                </label>
                                                <div class="label-txt">
                                                    <label>Has Custom Validation Process</label>
                                                </div>
                                            </div>
                                            <div class="form-group switch-group mobile-multi">

                                                <label class="switch">
                                                    <input disabled type="checkbox" name=""
                                                           value="1" {{ (in_array($setting->account_id,[38]))?'checked':'' }}>
                                                    <span class="slider"></span>
                                                </label>
                                                <div class="label-txt">
                                                    <label>Has Automatic Step Fields adding</label>
                                                </div>
                                            </div>
                                            <div class="form-group switch-group mobile-multi">
                                                <div class="label-txt">
                                                    <label><a href="{!! route('upload-template-setting',$setting->account_id) !!}">
                                                            Upload Qualification Template</a></label>
                                                </div>
                                            </div>


                                        @endif
                                    </div>
                                    <div class="form-group switch-group">
                                        <label class="switch">
                                            <input type="checkbox" name="video_meeting_enable"
                                                   value="1" {{ (isset($setting->setting['video_meeting_enable']) && $setting->setting['video_meeting_enable'])?'checked':'' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <div class="label-txt">
                                            <label>Video Meeting Enabled</label>
                                        </div>
                                    </div>
                                    <div class="form-group switch-group">
                                        <label class="switch">
                                            <input type="checkbox" name="direct_video_enable"
                                                   value="1" {{ (isset($setting->setting['direct_video_enable']) && $setting->setting['direct_video_enable'])?'checked':'' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <div class="label-txt">
                                            <label>Enable Direct Video Conversations</label>
                                        </div>
                                    </div>

                                    <div class="form-group switch-group">

                                        <label class="switch">
                                            <input   onchange="toggleConsultationSetting(this)"  type="checkbox" name="consultation_enable"
                                                   value="1" {{ (isset($setting->setting['consultation_enable']) && $setting->setting['consultation_enable'])?'checked':'' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <div class="label-txt">
                                            <label>Consultations Enabled</label>
                                        </div>
                                    </div>


                                        <div class="form-group switch-group mobile-multi consultation-sub">

                                            <label class="switch">
                                                <input  type="checkbox" name="reinvent_enable"
                                                       value="1" {{ (isset($setting->setting['reinvent_enable']) && $setting->setting['reinvent_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Reinvent Enable</label>
                                            </div>
                                        </div>



                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="col-xs-12">
                                        <h5 class="mb-20"><strong>Languages</strong></h5>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="langs[]"
                                                       {{-- if langauge not set or no language selected show this selected by default --}}
                                                       value="EN" {{(!isset($languages) || count($languages) == 0 ||  in_array('EN', $languages)) ?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>En English</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="langs[]"
                                                       {{-- if langauge not set or no language selected show this selected by default --}}
                                                       value="FR" {{(!isset($languages) || count($languages) == 0 ||  in_array('FR', $languages)) ?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Fr French</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- dddd--}}
                        <div class="col-xs-12 col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="col-xs-12">
                                        <h5 class="mb-20"><strong>Vertical Menus</strong></h5>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="vertical_bar_enable"
                                                       value="1" {{ (isset($setting->setting['vertical_bar_enable']) && $setting->setting['vertical_bar_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Display Vertical Bar</label>
                                            </div>
                                        </div>

                                        <div class="form-group switch-group mobile-multi">
                                            <label class="switch">
                                                <input type="checkbox" name="add_module_enable"
                                                       value="1" {{ (isset($setting->setting['add_module_enable']) && $setting->setting['add_module_enable']) ?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Add Module</label>
                                            </div>
                                        </div>

                                        <div class="form-group switch-group mobile-multi">
                                            <label class="switch">
                                                <input type="checkbox" name="vertical_messenger_enable"
                                                       value="1" {{ (isset($setting->setting['vertical_messenger_enable']) && $setting->setting['vertical_messenger_enable']) ?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Messenger enabled</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group mobile-multi">
                                            <label class="switch">
                                                <input type="checkbox" name="direct_video_enable"
                                                       value="1" {{ (isset($setting->setting['direct_video_enable']) && $setting->setting['direct_video_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Enable Direct Video Conversations</label>
                                            </div>
                                        </div>

                                        <div class="form-group switch-group mobile-multi">

                                            <label class="switch">
                                                <input type="checkbox" name="vertical_news_letter_enable"
                                                       value="1" {{ (isset($setting->setting['vertical_news_letter_enable']) && $setting->setting['vertical_news_letter_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Newsletter enabled</label>
                                            </div>
                                        </div>

{{--                                        <div class="form-group switch-group mobile-multi">--}}
{{--                                            <label class="switch">--}}
{{--                                                <input type="checkbox" name="event_enable"--}}
{{--                                                       value="1" {{ (isset($setting->setting['event_enabled']) && $setting->setting['event_enabled'])?'checked':'' }}>--}}
{{--                                                <span class="slider"></span>--}}
{{--                                            </label>--}}
{{--                                            <div class="label-txt">--}}
{{--                                                <label>Event Enabled</label>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
                                        {{-- Event module configurations in module settings --}}
                                        <div class="form-group switch-group mobile-multi">
                                            <label class="switch">
                                                <input type="checkbox" name="vertical_event_enabled"
                                                       value="1" {{ (isset($setting->setting['vertical_event_enabled']) && $setting->setting['vertical_event_enabled'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Event Enabled</label>
                                            </div>
                                        </div>
                                        {{-- End of event setting --}}

                                        <div class="form-group switch-group mobile-multi">
                                            <label class="switch">
                                                <input type="checkbox" name="feature_request_enable"
                                                       value="1" {{ (isset($setting->setting['feature_request_enable']) && $setting->setting['feature_request_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Feature Request</label>
                                            </div>
                                        </div>

                                        <div class="form-group switch-group mobile-multi">
                                            <label class="switch">
                                                <input type="checkbox" name="help_enable"
                                                       value="1" {{ (isset($setting->setting['help_enable']) && $setting->setting['help_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Help</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group mobile-multi">
                                            <label class="switch">
                                                <input type="checkbox" name="share_enable"
                                                       value="1" {{ (isset($setting->setting['share_enable']) && $setting->setting['share_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Share</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group mobile-multi">
                                            <label class="switch">
                                                <input type="checkbox" name="others_enable"
                                                       value="1" {{ (isset($setting->setting['others_enable']) && $setting->setting['others_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Others</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- dddd--}}
                        <div class="col-xs-12 col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="col-xs-12">
                                        <h5 class="mb-20"><strong>Future modules</strong></h5>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="workshops_enable"
                                                       value="1" {{ (isset($setting->setting['workshops_enable']) && $setting->setting['workshops_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Commissions et GTs</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="documents_enable"
                                                       value="1" {{ (isset($setting->setting['documents_enable']) && $setting->setting['documents_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Documents</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="survey_menu_enable"
                                                       value="1" {{ ($setting['survey_menu_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Enquêtes et Observatoires</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="elearning_menu_enabled"
                                                       value="1" {{ ($setting['elearning_menu_enabled'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Formation</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="crm_menu_enable"
                                                       value="1" {{ ($setting['crm_menu_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>CRM</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="reseau_menu_enable"
                                                       value="1" {{ ($setting['reseau_menu_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Réseau TESTORG</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="wiki_menu_enable"
                                                       value="1" {{ ($setting['wiki_menu_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Wikis</label>
                                            </div>
                                        </div>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" name="piloter_menu_enable"
                                                       value="1" {{ ($setting['piloter_menu_enable'])?'checked':'' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Piloter</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default"> {{-- TRANSCRIBE SETTING--}}
                                <div class="panel-body">
                                    <div class="col-xs-12">
                                        <h5 class="mb-20"><strong>Transcribe Setting</strong></h5>
                                        <div class="form-group switch-group">
                                            <label class="switch">
                                                <input type="checkbox" id="transcribesetting"
                                                       onchange="toggleTranscribeField()"
                                                       name="transcribe_setting_enabled"
                                                       value="1" {{ (isset($setting['setting']['transcribe_setting']['enabled']) && ($setting['setting']['transcribe_setting']['enabled']) ?'checked':'') }}>
                                                <span class="slider"></span>
                                            </label>
                                            <div class="label-txt">
                                                <label>Enabled</label>
                                            </div>
                                        </div>
                                        <div id="transcribe-field">
                                            <div class="form-group switch-group">
                                                <label class="">
                                                    <input type="number" class="col-md-2" name="transcribe_allowed_h"
                                                           placeholder="HH"
                                                           value="{{ isset($setting['setting']['transcribe_setting']) ? gmdate("H",$setting['setting']['transcribe_setting']['max_allowed']) : 0 }}">
                                                    <input type="number" class="col-md-2" name="transcribe_allowed_m"
                                                           placeholder="MM"
                                                           value="{{ isset($setting['setting']['transcribe_setting']) ? gmdate("i",$setting['setting']['transcribe_setting']['max_allowed']) : 0 }}">
                                                    <label>Maximum Allowed(HH:MM)</label>
                                                </label>
                                            </div>
                                            <div class="form-group switch-group">
                                                <label class="">
                                                    <input type="number" class="col-md-2" name="transcribe_available_h"
                                                           placeholder="HH"
                                                           value="{{ isset($setting['setting']['transcribe_setting']) ? gmdate("H",$setting['setting']['transcribe_setting']['available_credit']) : 0 }}">
                                                    <input type="number" class="col-md-2" name="transcribe_available_m"
                                                           placeholder="MM"
                                                           value="{{ isset($setting['setting']['transcribe_setting']) ? gmdate("i",$setting['setting']['transcribe_setting']['available_credit']) : 0 }}">
                                                    <label>Availalbe Now(HH:MM)</label>
                                                </label>
                                            </div>
                                            <div class="form-group switch-group">
                                                {{ (isset($transcribe_data['this_month'])) ? $transcribe_data['this_month'] : 0  }}
                                                Used This Month
                                            </div>
                                            <div class="form-group switch-group">
                                                {{ isset($transcribe_data['total_time']) ? $transcribe_data['total_time'] : 0 }}
                                                Used Total
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> {{--END OF TRANSCRIBE SETTING--}}
                        </div>

                        <div class="col-xs-12 col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group switch-group">
                                        <div class=" col-sm-3">
                                            <label class="mb-20">
                                                <strong>Database Name :</strong>
                                            </label>
                                        </div>
                                        <div class=" col-sm-9">
                                            <label>{{ @$dBname }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 text-center">
                            <input type="hidden" name="account_id" value="{{ $id }}">
                            <input type="submit" class="form-group btn btn-primary" value="Save Settings">
                        </div>
                    </form>
                    {{--@if($isNewsletterEnabled == 1)--}}
                    {{--@if(!empty($file))--}}
                    {{--<div>{{ @$file }}--}}
                    {{--<button type="button" name="del-file" id="del-file" onclick="return Delete();">X</button>--}}
                    {{--</div>--}}
                    {{--@endif--}}
                    {{--<form method="post" action="{{route('upload-newsletter')}}" enctype="multipart/form-data">--}}
                    {{--<div class="form-group file-group">--}}
                    {{--<input type="file" name="file" class="file"/>--}}
                    {{--<div class="input-group col-xs-12">--}}
                    {{--<input type="text" class="form-control" disabled placeholder=""/>--}}
                    {{--<span class="input-group-btn">--}}
                    {{--<button class="browse btn btn-default" type="button">Browse</button>--}}
                    {{--</span>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--<button class="btn btn-primary" type="submit">Submit</button>--}}
                    {{--</form>--}}
                    {{--@endif--}}
                </div>
            </div>
        </main>
    </div>

    <script>
        function showBlueJeansSettingFields(){
            document.getElementById("event_bluejeans_setting").style.display = (cb.checked ?  "block" : "none");
            document.getElementById("zoom_setting").style.display = (cb.checked ?  "none" : "none");
        }
        function showZoomSettingFields(){
            document.getElementById("zoom_setting").style.display = (cb.checked ?  "block" : "none");
            document.getElementById("event_bluejeans_setting").style.display = (cb.checked ?  "none" : "none");
        }
    </script>
    <script>
        function toggleStockField() {
            var s = document.getElementById('stocksetting');
            var sfield = document.getElementById("stock-field");
            if (!s.checked) {
                sfield.style.display = "none";
            } else {
                sfield.style.display = "block";
            }
        }

        function toggleTranscribeField() {
            var s = document.getElementById('transcribesetting');
            var sfield = document.getElementById("transcribe-field");
            console.log(sfield);
            if (!s.checked) {
                sfield.style.display = "none";
            } else {
                sfield.style.display = "block";
            }
        }

        toggleStockField();
        toggleTranscribeField();

        function languageCheckBoxValidation() {
        }

        function validate() {
            var selectedType = getConferenceType();
            var lang = document.getElementsByName('langs[]');
            result = false;
            lang.forEach(function (value) {
                if (value.checked)
                    result = true;
            });
            if (!result) {
                alert('Please select atleast one language');
            }if (selectedType === 'none'){
                alert('Please select atleast one conference type');
                result = false;
            }
            return result;
        }

        function toggleWPSetting() {
            cb = document.getElementById('event_wp_enabled');
            document.getElementById("event_wp_setting").style.display = (cb.checked ?  "block" : "none");
        }

        function toggleEventBlueJeansSetting() {
            hideAllFields();
            cb = document.getElementById('event_bluejeans_enabled');
            document.getElementById("event_bluejeans_setting").style.display = (cb.checked ? "block" : "none");
            if (cb.checked) {
                showConferenceFields();
            }
        }

        function getConferenceType() {
            var selectBox = document.getElementById("event_conference_type");
            var selectedValue = selectBox.options[selectBox.selectedIndex].value;
            return selectedValue;
        }

        function showConferenceFields() {
            var selectedOption = getConferenceType();
            if (selectedOption === 'bj') {
                showBluejeans();
            }
            if (selectedOption === 'zoom') {
                showZoom();
            }
            if (selectedOption === '') {
                showZoom();
            }
        }

        function showBluejeans() {
            document.getElementById("zoom-fields").style.display = "none";
            document.getElementById("bluejeans-fields").style.display = "block";
        }

        function showZoom(){
            document.getElementById("bluejeans-fields").style.display = "none";
            document.getElementById("zoom-fields").style.display = "block";
        }

        function hideAllFields(){
            document.getElementById("zoom-fields").style.display = "none";
            document.getElementById("bluejeans-fields").style.display = "none";
        }

        function toggleEventKctSetting() {
            cb = document.getElementById('event_kct_enabled');
            var kct_style = "block";
            const bluejeans = document.getElementById("event_bluejeans_enabled");
            if(!cb.checked) {
                kct_style = "none";
                bluejeans.checked = false;
                bluejeans.disabled = true;
                toggleEventBlueJeansSetting();
            } else {
                bluejeans.disabled = false;
            }
            document.getElementById("event_kct_setting").style.display = kct_style;
        }

        function toggleEventSub(cb) {
            if (cb.checked){
                document.getElementsByClassName("event-sub")[0].style.display = "block";
                document.getElementsByClassName("event-sub")[1].style.display = "block";
                document.getElementsByClassName("event-sub")[2].style.display = "block";
                toggleWPSetting();
                toggleEventBlueJeansSetting();
                toggleEventKctSetting();
            }
            else {
                document.getElementsByClassName("event-sub")[0].style.display = "none";
                document.getElementsByClassName("event-sub")[1].style.display = "none";
                document.getElementsByClassName("event-sub")[2].style.display = "none";
                document.getElementsByName('event_bluejeans_enabled')[0].checked = false;
                document.getElementsByName('event_wp_enabled')[0].checked = false;
                document.getElementsByName('event_keep_contact_enabled')[0].checked = false;
                toggleWPSetting();
                toggleEventBlueJeansSetting();
                toggleEventKctSetting();
            }

        }

        toggleEventSub(document.getElementsByName('event_enable')[0]);

        function toggleConsultationSetting(cb) {
            if (cb.checked){
                document.getElementsByClassName("consultation-sub")[0].style.display = "block";
            }
            else {
                document.getElementsByClassName("consultation-sub")[0].style.display = "none";
                document.getElementsByName('reinvent_enable')[0].checked = false;
                // document.getElementsByName('event_wp_enabled')[0].checked = false;
                // document.getElementsByName('event_keep_contact_enabled')[0].checked = false;
            }
        }

        toggleConsultationSetting(document.getElementsByName('consultation_enable')[0])
    </script>
@endsection
