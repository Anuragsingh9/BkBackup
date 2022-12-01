<footer>
    <div class="container">
        <div class="row footer_main">
            <div class="col-4 left_footer">
                <!-- Light -->
                <div class="btn-group shadow-0">
                    <button
                        type="button"
                        class="btn footer_dropDown dropdown-toggle"
                        data-mdb-toggle="dropdown"
                        aria-expanded="false">
                        <span>{{session('locale') ?? 'EN'}}</span>
                    </button>
                    <ul class="dropdown-menu">
                        <?php
                        $langs = config('superadmin.moduleLanguages');
                        foreach ($langs as $key => $lang){
                        ?>
                        <li class="dropdown-item" ><a class="lang_option" style="text-decoration: none; color: #3b3b3b" href="{{route('change-lang',$lang)}}">{{$key}} </a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="col-4 middle_footer">
                <ul class="m-0">
                    <li><a href="#" class="header-text-color"> {{__('superadmin::labels.uses_terms')}}</a></li>
                    <li>|</li>
                    <li><a href="#" class="header-text-color">{{__('superadmin::labels.privacy_policy')}}</a></li>
                </ul>
            </div>
            <div class="col-4 right_footer">
                <?php $time = \Carbon\Carbon::now();?>
                <span class="text-right">{{__('superadmin::labels.copyright')}} {{$time->year}} </span>
            </div>
        </div>
    </div>
</footer>
