@auth
    @php
        $nav = [
            [
                'href' =>  route('su-account-list'),
                'title' => __("superadmin::labels.accounts"),
            ], [
                'href' =>  route('su-instant-account-create'),
                'title' => __("superadmin::labels.instant_account_creation"),
            ], [
                'href' =>  route('su-tag-moderation', ['tagType' => config('superadmin.models.userTag.tagType_Professional')]),
                'title' => __("superadmin::labels.tag_moderation"),
            ], [
                'href' =>  url('/api/documentation'),
                'title' => __("superadmin::labels.api_doc"),
            ], [
                'href' =>  route('su-general-settings'),
                'title' => __("superadmin::labels.general_settings"),
            ],
        ];
    @endphp
    <div class="color1BG text-white">
        <nav class="container navbar navbar-expand-sm p-0">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    @foreach($nav as $n)
                        <li class="nav-item">
                            <a class="nav-link plainAcr"
                               href="{{$n['href']}}">{{$n['title']}}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </nav>
    </div>
    <div class="color2BG">
        <div class="container">
            <div class="row">
                <ul class="nav navbar-nav tab-menu" style="height: 40px;">
                </ul>
                <div class="head-start-btn">
                </div>
            </div>
        </div>
    </div>
@endauth
