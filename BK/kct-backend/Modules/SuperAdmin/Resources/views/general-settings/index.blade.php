@extends('superadmin::layouts.master')
@component('superadmin::components.auth_header')@endcomponent
@component('superadmin::components.navigation_bar')@endcomponent
@section('content')
    <div class="container page-content">
        <div class="row justify-content-center">
            <div class="col-12 mx-auto">
                <div class="my-4">
                    <div class="col-xs-12 col-sm-12">
                        <strong class="mb-0">{{ __('superadmin::labels.video_explainer') }}</strong>
                        <div class="list-group mb-5 mt-3 shadow">
                            {{-- Enabled section --}}
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <strong class="mb-0">{{ __('superadmin::labels.enabled') }}</strong>
                                    </div>
                                    <div class=" col-auto mb-4 form-check form-switch">
                                        <input class="form-check-input" type="checkbox" value=1
                                               id="video_explainer_enabled"
                                               name="video_explainer_enabled" {{ ($settings['video_explainer_enabled'] ?? 0) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            {{-- Video Url for EN input section --}}
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <strong
                                            class="mb-0">{{ __('superadmin::labels.event_url_en') }}</strong>
                                    </div>
                                    <div class=" col-5 form-check form-switch">
                                        <input type="text"
                                               class="col-12"
                                               id="public_video_en"
                                               name="public_video_en"
                                               value="{{ $settings['public_video_en'] ?? "" }}">
                                    </div>
                                </div>
                            </div>
                            {{-- Video Url for FR input section --}}
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <strong
                                            class="mb-0">{{ __('superadmin::labels.event_url_fr') }}</strong>
                                    </div>
                                    <div class=" col-5 form-check form-switch">
                                        <input type="text"
                                               class="col-12"
                                               id="public_video_fr"
                                               name="public_video_fr"
                                               value="{{ $settings['public_video_fr'] ?? "" }}">
                                    </div>
                                </div>
                            </div>
                            {{-- Enable on registration page section --}}
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <strong class="mb-0">{{ __('superadmin::labels.display_on_registration') }}
                                            :</strong>
                                    </div>
                                    <div class=" col-auto mb-4 form-check form-switch">
                                        <input class="form-check-input" type="checkbox" value=1
                                               id="display_on_reg"
                                               name="display_on_reg" {{ ($settings['display_on_reg'] ?? 0) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            {{-- Enable on live page section --}}
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <strong class="mb-0">{{ __('superadmin::labels.display_on_live') }}
                                            :</strong>
                                    </div>
                                    <div class="col-auto mb-4 form-check form-switch">
                                        <input class="form-check-input" type="checkbox" value=1
                                               id="display_on_live"
                                               name="display_on_live" {{ ($settings['display_on_live'] ?? 0) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            {{-- Image for video explainer --}}
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <strong
                                            class="mb-0">
                                            <p data-toggle="tooltip"
                                               title="Default Image when Video Explainer is turned OFF from OIT">{{ __('superadmin::labels.image') }}
                                                <i class="fa fa-info-circle" aria-hidden="true"></i></p>
                                        </strong>
                                    </div>
                                    <div class=" col-5 form-check form-switch">
                                        <input type="file"
                                               class="col-3"
                                               id="image_path"
                                               name="image_path">
                                            <img class="col-3" src="{{ $settings['image_path'] ?? "" }}" id="image_preview" width="50" height="50">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('document').ready(function () {
            var fd = new FormData();
            let video_explainer_enabled = $('#video_explainer_enabled');
            let display_on_reg = $('#display_on_reg');
            let display_on_live = $('#display_on_live');
            let public_video_en = $('#public_video_en');
            let public_video_fr = $('#public_video_fr');
            let image_path = $('#image_path');

            let updateSettings = function (e) {
                fd.append('_token', "{{ csrf_token() }}",);
                fd.append('public_video[video_explainer_enabled]', video_explainer_enabled.is(":checked") ? 1 : 0);
                fd.append('public_video[display_on_reg]', video_explainer_enabled.is(":checked") ? 1 : 0);
                fd.append('public_video[display_on_live]', video_explainer_enabled.is(":checked") ? 1 : 0);
                fd.append('public_video[public_video_en]', public_video_en.val());
                fd.append('public_video[public_video_fr]', public_video_fr.val());
                if(image_path[0].files[0] !== undefined)
                fd.append('public_video[image_path]',image_path[0].files[0]);

                $.ajax({
                    url: "{{ route('su-save-settings') }}",
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        $('#image_preview').attr("src",response.data.setting_value.image_path);
                    }
                });
            };
            video_explainer_enabled.on('change', updateSettings);
            display_on_reg.on('change', updateSettings);
            display_on_live.on('change', updateSettings);
            public_video_en.on('change', updateSettings);
            public_video_fr.on('change', updateSettings);
            image_path.on('change', updateSettings);
        })
    </script>
@endsection
