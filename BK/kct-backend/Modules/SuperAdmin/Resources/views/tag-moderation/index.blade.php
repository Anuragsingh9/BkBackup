@extends('superadmin::layouts.master')
@component('superadmin::components.auth_header')@endcomponent
@component('superadmin::components.navigation_bar')@endcomponent
@section('content')
    <div class="container page-content">
        <div class="col-lg-12 col-sm-12">
            @if ($errors->any())
                <div class="row mb-20">
                    <div class="col-lg-12">
                        <div class="alert alert-danger text-left">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li class="text-left">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            {{-- Export Button Section --}}
            <div class="mb-lg-5">
                <div class="col-lg-12">
                    <a
                        href=" {{ route('su-tag-export', ['tagType' => $tagType_Professional]) }}"
                        class="btn btn-primary btn-c2-h-o me-lg-3" target="_blank" download>
                        {{ __('superadmin::labels.excel_export_professional') }}
                    </a>
                    <a href="{{ route('su-tag-export', ['tagType' => $tagType_Personal]) }}"
                       class="btn btn-primary btn-c2-h-o" target="_blank" download>
                        {{ __('superadmin::labels.excel_export_personal') }}
                    </a>
                </div>
            </div>
            {{-- Tag Type Button Section --}}
            <nav class="navbar navbar-expand-lg navbar-light bg-light rounded-3 shadow-sm mb-lg-5">
                <div class="container-fluid">
                    <div class="navbar-nav">
                        <a class="{{$tagType ==  $tagType_Professional ? "navbar-brand h1 pe-3" : "nav-link"}} m-0"
                           aria-current="page"
                           href="{{route('su-tag-moderation', ['tagType' => $tagType_Professional])}}">
                            {{ __("superadmin::labels.professional_tags") . ($unModeratedProfessionalCount ? "($unModeratedProfessionalCount)" : "")}}

                        </a>
                        <a class="{{$tagType ==  $tagType_Personal ? "navbar-brand h1 ps-3" : "nav-link"}} m-0"
                           href="{{route('su-tag-moderation', ['tagType' => $tagType_Personal])}}">
                            {{__("superadmin::labels.personal_tags"). ($unModeratedPersonalCount ? "($unModeratedPersonalCount)" : "")}}
                        </a>
                    </div>
                </div>
            </nav>
            {{-- Tags List Section --}}
            <div class="col-lg-12 mb-lg-5">

                <table class="table">
                    <thead>
                    <tr>
                        @foreach(config('superadmin.moduleLanguages') as $lang)
                            <th scope="col">{{ strtoupper($lang) }}</th>
                        @endforeach
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tags as $tag)
                        <tr>
                            {{-- Cell for each language --}}
                            @foreach(config('superadmin.moduleLanguages') as $lang)
                                <td class="col-lg-2 align-middle">
                                    <div id="{{"label_{$lang}_$tag->id"}}"
                                         onclick="toggleEdit({{$tag->id}},'{{$lang}}')">
                                        {{ ($value = $tag->locales->where('locale', $lang)->first()) ? $value->value : null}}
                                    </div>
                                    <input type="hidden"
                                           id="{{"input_{$lang}_$tag->id"}}"
                                           name="editedTag"
                                           class="form-control shadow-none clr-2-brdr"
                                           value="{{ ($value = $tag->locales->where('locale', $lang)->first()) ? $value->value : null}}"
                                           onblur="toggleEdit({{$tag->id}},'{{ $lang }}')">
                                </td>
                            @endforeach
                            {{-- Action Buttons Cell--}}
                            <td class="col-lg-4">
                                <div class="row">
                                    <div class="col-auto text-left pr-0 pl-0">
                                        <button class="btn btn-primary btn-c2-h-o accept-tag"
                                                id="{{$tag->id}} ">
                                            {{ __('superadmin::words.accept') }}
                                        </button>
                                    </div>
                                    <div class="col-auto text-left pr-0 pl-0">
                                        <button class="btn btn-primary btn-c2-h-o reject-tag"
                                                id="{{$tag->id}}">
                                            {{ __('superadmin::words.reject') }}
                                        </button>
                                    </div>
                                    <div class="col-lg-5 text-left pr-0 pl-0">
                                        <div>
                                            <div id="searchBox_{{$tag->id}}"></div>
                                            <div id="searchResult_{{$tag->id}}"></div>
                                        </div>
                                        <div id="res"></div>
                                        <button class="btn btn-primary btn-c2-h-o" id="merge_{{$tag->id}}"
                                                onclick="showSearchBox({{$tag->id}})">
                                            {{ __('superadmin::words.merge') }}
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Pagination Buttons Section --}}
            <div class="d-flex justify-content-center clr-2-pgntn">
                {{$tags->links()}}
            </div>

            {{-- Import Section --}}
            <div class="col-lg-12 mb-lg-5">
                <br/>
                <button data-toggle="modal" data-id="" data-lang="fr"
                        data-target="#tag-import"
                        class="btn btn-primary btn-c2-h-o">
                    {{ __('superadmin::labels.import_tags') }}
                </button>
            </div>
        </div>
    </div>
    <script>
        function emitUpdateTagApi(id, value, locale) {
            const data = {
                id,
                value,
                locale,
                _token: '{{csrf_token()}}'
            };
            $.ajax({
                url: "{{ route('su-tag-update') }}",
                data,
                dataType: "json",
                type: 'POST',
                error: function (xhr) {
                    alert("Error: " + (xhr && xhr.hasOwnProperty('responseJSON') ? xhr.responseJSON.msg : ""));
                },
            });
        }

        function toggleEdit(id, lang) {
            const labelElement = document.getElementById(`label_${lang}_${id}`);
            const inputElement = document.getElementById(`input_${lang}_${id}`);

            if (labelElement.style.display === "none") { // currently label is showing -> changing to input mode
                let inputValue = inputElement.value;
                if ((typeof inputValue === 'string' || inputValue instanceof String) && inputValue.length > 0) {
                    labelElement.style.display = "block";
                    inputElement.type = "hidden";
                    labelElement.innerText = inputValue;
                }
                emitUpdateTagApi(id, inputElement.value, lang);
            } else { // currently input mode is showing -> changing to label mode
                labelElement.style.display = "none";
                inputElement.type = "text";
                inputElement.focus();
            }
        }

        function showSearchBox(id) {
            $(document).ready(function () {
                $('#searchBox_' + id).append('<input id="key" type="text" name="search" class="merge_input form-control">');
                $('#key').attr('id', 'key' + id);
            });
            $("#merge_" + id).hide();
            searchTag(id);
        }

        function searchTag(id) {
            $(document).ready(function () {
                $("#key" + id).keyup(function () {
                    var key = $("#key" + id).val();
                    var count = key.length;
                    if (count >= 3) {
                        $.get('{{ route('su-tag-search') }}',
                            {key: key, tagType: {{ $tagType }}}, function (data) {
                                if (data.data.length === 0) {
                                    if (document.getElementById('no_result') === null) {
                                        $('#searchBox_' + id).append('<div id="no_result" class="listDiv"></div>');
                                        $('#no_result').html('No Record Found');
                                    }
                                    return false;
                                }
                                $('#searchResult_' + id).empty();
                                $.each(data.data, function (key, value) {
                                    console.log(key, value);
                                    for (let i = 0; i <= data.data.length; i++) {
                                        if (i === data.data.length) {
                                            return false;
                                        }
                                        {{--$('#searchResult_' + id)--}}
                                        {{--    .append(`--}}
                                        {{--        <form action="{{ route("su-tag-merge") }}" method="POST">--}}
                                        {{--        {{ csrf_field() }}--}}
                                        {{--            <div>EN: ${data.data[i].tag_EN}</div>--}}
                                        {{--            <div>FR: ${data.data[i].tag_FR}</div>--}}
                                        {{--            <input type="hidden" name="id" value="${id}"/>--}}
                                        {{--            <input type="submit" id="result" value="{{ __('superadmin::words.merge') }}" class="listDiv"/>--}}
                                        {{--        </form>--}}
                                        {{--    `);--}}
                                        $('#searchResult_' + id)
                                            .append(`
                                                    <div>EN: ${data.data[i].tag_EN}</div>
                                                    <div>FR: ${data.data[i].tag_FR}</div>
                                                    <button class="btn btn-primary btn-c2-h-o merge-tag" id="${id}">
                                                            {{ __('superadmin::words.merge') }}
                                            </button>
`);
                                        $("#res").show();
                                        $('#result').attr('id', 'result' + i).html(data.data[i].tag_EN);
                                        $('#result' + i).on('click', function () {
                                            let mergingId = data.data[i].tag_id;
                                            mergeTag(mergingId, id);
                                        });
                                    }
                                });
                            });
                    } else {
                        $('.listDiv').remove();
                    }
                });
            });
        }

        $(document).on('click', '.accept-tag', function (event) {
            const tag_id = event.target.id;
            const tagLabel = $(`#label_en_${tag_id}`).text().trim();

            if (confirm(`{{__('superadmin::labels.accept_confirm')}}: ${tagLabel} ? `)) {
                $.ajax({
                    url: "{{ route('su-tag-accept') }}",
                    data: {
                        id: tag_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    type: 'POST',
                    success: function (response) {
                        let type = response.data[0];
                        if (type === undefined)
                            location.reload();
                        else
                            window.location.href = type['tag_type'];
                    },
                    error: function (xhr) {

                    },
                    complete: function () {
                    }
                });
            }
        });

        $(document).on('click', '.reject-tag', function (event) {
            const tag_id = event.target.id;
            const tagLabel = $(`#label_en_${tag_id}`).text().trim();

            if (confirm(`{{__('superadmin::labels.reject_confirm')}}: ${tagLabel} ? `)) {
                $.ajax({
                    url: "{{ route('su-tag-reject') }}",
                    data: {
                        id: tag_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    type: 'POST',
                    success: function (response) {
                        let type = response.data[0];
                        if (type === undefined)
                            location.reload();
                        else
                            window.location.href = type['tag_type'];
                    },
                    error: function (xhr) {

                    },
                    complete: function () {
                    }
                });
            }
        });

        $(document).on('click', '.merge-tag', function (event) {
            const tag_id = event.target.id;
            $.ajax({
                url: "{{ route('su-tag-reject') }}",
                data: {
                    id: tag_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                type: 'POST',
                success: function (response) {
                    let type = response.data[0];
                    if (type === undefined)
                        location.reload();
                    else
                        window.location.href = type['tag_type'];
                },
                error: function (xhr) {

                },
                complete: function () {
                }
            });
        });
    </script>
@endsection
