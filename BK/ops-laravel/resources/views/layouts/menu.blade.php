 <div id="menu-setting-sec">
                <div class="container white-text">
                    <div class="row">
                        <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 nopadding" id="head-menu">
                        @if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']!="/change-password")
                                <ul class="list-inline light-font text-uppercase">
                                    <li>ORGANISER</li>
                                    <li>Produire</li>
                                    <li>Influencer</li>
                                    <li>Accélérer</li>
                                    <li>Piloter</li>
                                </ul>
                            @endif



                        </div>
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right" id="setting-icons">
                           
                        </div>
                    </div>
                </div>
            </div>
            <div id="main-menu" class="workshop-color1">
                <div class="container">
                    <div class="row">
                        <div class="white-text" style="text-align: center; padding: 15px 0px;"> {{--{{@$data->header_bar}}--}}</div>
                        <ul class="nav navbar-nav tab-menu" {{-- style="height: 40px;" --}}>


                        </ul>
                        <div class="head-start-btn">

                        </div>
                    </div>
                </div>
            </div>