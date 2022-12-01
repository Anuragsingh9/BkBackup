@php
    $domain=(!empty($_SERVER['HTTP_HOST']))?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];
        $css_data = getDomainGraphicSetting($domain);

        // $css_data = dynamicCss();
        $color1 = $css_data['color1'];
        $headerColor1 = $css_data['headerColor1'];
        $color2= $css_data['color2'];
        $headerColor2= $css_data['headerColor2'];
        $color3= $css_data['color3'];
        $transprancy7=$css_data['transprancy7'];
        $transprancy1=$css_data['transprancy1'];
        $transprancy2=$css_data['transprancy2'];
        $color2_transprancy65=$css_data['color2_transprancy65'];
        $color1_transprancy1=isset($css_data['color1_transprancy1'])?$css_data['color1_transprancy1']:'';
        $color2_transprancy25=isset($css_data['color2_transprancy25'])?$css_data['color2_transprancy25']:'';
        $color2_transprancy65=isset($css_data['color2_transprancy65'])?$css_data['color2_transprancy65']:'';
        $headerLogo=$css_data['header_logo'];
        /* dd($css_data); */
@endphp

@import url('https://fonts.googleapis.com/css?family=Lato:300,400,700');
html, body, #root{
    height: 100%;
}
form.default-form button, form.default-form button:hover, form.default-form button:active, form.default-form button:focus{
    outline: none;
}
body *{
/*cursor: default;*/
}
.app {
    min-height: 100%;
    position: relative;
    padding-bottom: 66px;
}
body {
    width: 100%;
    font-family: 'Lato', sans-serif;
    font-weight: 400;
    font-size: 14px;
    color: #333333;
}
h5{ font-size: 16px;}
.cal-event-list{
    max-height:250px;
    overflow:auto;
}
.nopadding {
    padding-left: 0;
    padding-right: 0;
}
.nomargin{
    margin-top: 0;
    margin-bottom: 0;
}
.border-top{ border-top: 1px solid #ddd; }
.flex-box,.input-value-inner, .flexbox, .addnew-mileston, .rdo-icon label, .agendaDocsListMenu li, .auto-scroll-popup .modal-dialog,
.mobViewHeader, .mobViewOverlayDiv {
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
}
.cursor-pointer, .cursor{ cursor: pointer;}
.white-text{ color: #ffffff !important;}
.site-color{ color: {{$color2}} !important;}
.site-color2{color: {{$color1}} !important;}
.site-color3{ color: #898888 !important;}
.site-bg-color{ background-color: {{$color2}} !important;}
.site-border-color{ border-color: {{$color2}} !important;}
.site-bg-color2 { background: #f6f6f6;}
.site-bg-color3{ background-color: {{$color1}} !important;}
.primary-border-color{ border-color: {{$color1}} !important;}
.secondry-border-transparency1{ border-color: {{$transprancy7}} !important;}
.parimary-border-transparency1{ border-color: {{$transprancy1}} !important;}
.secondry-border-transparency2{ border-color: {{$transprancy2}} !important;}
.light-font{ font-weight: 300;}
.label-black{ color:#171717 !important;}
.transprancy1{ background-color: {{$transprancy1}} !important;}
.transprancy2{ background-color: {{$transprancy2}} !important;}
.checkbox label::before { border: 1px solid #909090;}
.mt-0{ margin-top: 0px !important;}
.mt-3{ margin-top: 3px !important;}
.mt-5{ margin-top: 5px!important;}
.mt-8{ margin-top: 8px!important;}
.mt-10{ margin-top: 10px!important;}
.mt-15{ margin-top: 15px!important;}
.mt-20{ margin-top: 20px!important;}
.mt-25{ margin-top: 25px!important;}
.mt-30{ margin-top: 30px!important;}
.mt-40{ margin-top: 40px!important;}
.mt-50{ margin-top: 50px!important;}
.mt-60{ margin-top: 60px!important;}
.mt-70{ margin-top: 70px!important;}

.mb-0{ margin-bottom: 0px !important;}
.mb-3{ margin-bottom: 3px!important;}
.mb-5{ margin-bottom: 5px!important;}
.mb-10{ margin-bottom: 10px!important;}
.mb-15{ margin-bottom: 15px!important;}
.mb-20{ margin-bottom: 20px!important;}
.mb-25{ margin-bottom: 25px!important;}
.mb-30{ margin-bottom: 30px!important;}
.mb-40{ margin-bottom: 40px!important;}
.mb-50{ margin-bottom: 50px!important;}
.mb-60{ margin-bottom: 60px!important;}

.ml-0{margin-left: 0px !important;}
.ml-3{margin-left: 3px !important;}
.ml-5{margin-left: 5px !important;}
.ml-10{margin-left: 10px !important;}
.ml-15{margin-left: 15px !important;}

.mr-0{margin-right: 0px !important;}
.mr-3{margin-right: 3px !important;}
.mr-5{margin-right: 5px !important;}
.mr-10{margin-right: 10px !important;}
.mr-15{margin-right: 15px !important;}

.pt-0{ padding-top: 0 !important;}
.pt-5{ padding-top: 5px !important;}
.pt-10{ padding-top: 10px !important;}
.pt-20{ padding-top: 20px !important;}
.pt-30{ padding-top: 30px !important;}
.pt-40{ padding-top: 40px !important;}
.pt-50{ padding-top: 50px !important;}
.pt-60{ padding-top: 60px !important;}

.pb-0{ padding-bottom: 0 !important;}
.pb-5{ padding-bottom: 5px !important;}
.pb-10{ padding-bottom: 10px !important;}
.pb-20{ padding-bottom: 20px !important;}
.pb-30{ padding-bottom: 30px !important;}
.pb-40{ padding-bottom: 40px !important;}
.pb-50{ padding-bottom: 50px !important;}
.pb-60{ padding-bottom: 60px !important;}

.pl-0{ padding-left: 0px !important;}
.pl-5{ padding-left: 5px !important;}
.pl-10{ padding-left: 10px !important;}
.pl-15{ padding-left: 15px !important;}
.pl-20{ padding-left: 20px !important;}
.pl-30{ padding-left: 30px !important;}
.pl-40{ padding-left: 40px !important;}
.pl-50{ padding-left: 50px !important;}
.pl-60{ padding-left: 60px !important;}

.pr-0{ padding-right: 0px !important;}
.pr-5{ padding-right: 5px !important;}
.pr-10{ padding-right: 10px !important;}
.pr-15{ padding-right: 15px !important;}
.pr-20{ padding-right: 20px !important;}
.pr-30{ padding-right: 30px !important;}
.pr-40{ padding-right: 40px !important;}
.pr-50{ padding-right: 50px !important;}
.pr-60{ padding-right: 60px !important;}

.font-wt-nrml{ font-weight: 400 !important;}
.btn-inline, .inline-block {display: inline-block;}
#upgrade-info{ background: #002d59;}
#user-info-inner {
    display: inline-block;
    max-width: 200px;
    float: right;
}
#upgrade-info {
    background: #002d59;
    padding: 11px 0;
}
#upgrade-info a {
    margin-left: 16px;
    width: 136px;
    border-radius: 2px;
    box-shadow: none;
    padding: 4px 10px;
}
.btn-primary {
    color: #fff;
    background-color: {{$color2}};
    border-color: transparent;
}
#logo-user-info {
    padding: 5px 0;
    background: {{$headerColor1}};
}
#user-img {
    width: 80px;
    height: 80px;
    display: inline-block;
    background-size: cover;
    border-radius: 50%;
    vertical-align: middle;
}
.pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover {
    background-color: {{$color2}};
}
#head-user-info .dropdown {
    padding-left: 40px;
    min-height: 36px;
}
#user-info-inner ul.dropdown-menu { margin-top: 10px;}
#head-user-info button.dropdown-toggle {
    background: none;
    border: none;
    padding-left: 11px;
    height: 35px;
    text-align:left;
}
span#h_username {
    word-break: break-all;
    text-align: left !important;
    color:{{$headerColor2}};
}
#head-user-info button.dropdown-toggle i {
    font-size: 12px;
    color: {{$headerColor2}};
    margin-left: 8px;
    vertical-align: top;
    margin-top: 2px;
    right: 0;
}
#head-logo-sec img{ max-height: 62px;}
#head-user-info {padding-top: 12px;}
#menu-setting-sec {
    background: {{$color1}};
    border-bottom: 1px solid {{$color1}};
}
#menu-setting-sec ul{
    margin: 0px;
}
#main-menu ul{
    text-align: right;
    margin: 0px;
    left: inherit;
    right: 0;
}
#setting-icons ul {display: inline-block;}
#head-menu>ul>li{
    padding-left: 0;
    padding-right: 0;
}
#head-menu>ul>li,#main-menu ul>li>a,
#main-menu ul>.list-group>li>a{
    color: #fff;
    padding: 8px 8px;
    margin-right: 1px;
}
#main-menu ul>li>a{
    display: block;
    cursor: pointer;
}
#head-menu ul li a:hover,#main-menu ul li a{text-decoration: none;}
#setting-icons ul li a,#head-menu ul li.icon a {
    font-size: 20px;
    padding: 4px 5px;
    color: #fff;
    display: block;
}
#head-menu ul li.icon{padding: 0px 5px;}
.setting-left-right-row{
    margin-left: -40px;
    margin-right: -40px;
}
.setting-left, .setting-right {
    padding-left: 40px;
    padding-right: 40px;
}
form.default-form section {
    border-top: 1px solid #d1d1d1;
    padding-top: 15px;
    padding-bottom: 15px;
}
form.default-form{padding-top: 24px;}
.switch {
    position: relative;
    display: inline-block;
    width: 32px;
    height: 13px;
    margin: 4px 0;
    margin-right: 20px;
    vertical-align: middle;
    padding-left: 32px;
}
.switch input {display:none;}
.slider, .slider:before{
    position: absolute;
    left: 0;
    bottom: 0;
    -webkit-transition: .4s;
    transition: .4s;
}
.slider {
    top: 0;
    right: 0;
    cursor: pointer;
    background-color: #ecebeb;
}
.slider:before {
    content: "";
    height: 13px;
    width: 13px;
    background-color: #828282;
}
input:checked + .slider:before {
    background-color: {{$color2}};
}
input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
}
input:checked + .slider:before {
    -webkit-transform: translateX(19px);
    -ms-transform: translateX(19px);
    transform: translateX(19px);
}
.checkbox.checkbox-inline label,
.radio.radio-inline label{
    color: #7b7b7b;
}
.checkbox.checkbox-inline,
.radio.radio-inline{
    margin-right: 40px;
    padding-top: 3px;
}
.switch-style2 .slider {
    top: 6.5px;
    height: 2px;
}
.switch-style2 .slider:before {
    bottom: -5.6px;
    border-radius: 50%;
}
.ui-widget-content { z-index: 999 !important;}
.resources h4{
    margin-bottom: 30px;
    margin-top: 0px;
}
.file {
    visibility: hidden;
    position: absolute;
}
.file-group .btn,
.txt-btn-group .btn {
    min-width: 90px;
}
.file-group .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control{
    background-color: #fff;
}
.help{
    position: relative;
    padding-right: 36px;
}
.help .fa-question-circle{
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto;
    font-size: 20px;
    height: 22px;
    cursor: pointer;
    color: #8a8a8a;
}
.setting-opt-menu ul{-webkit-padding-start: 34px;}
.setting-opt-menu ul li {
    list-style-type: none;
    position: relative;
    margin-bottom: 15px;
}
.setting-opt-menu ul li a:hover{ text-decoration: none;}
.setting-opt-menu ul li a small{  color: #a9a0b0; }
.setting-opt-menu h4 {
    border-bottom: 1px solid #dcdcdc;
    padding-bottom: 16px;
}
.setting-opt-menu ul li span {
    background: url(../../ops-rest-tenancy/public/img/caution.png) no-repeat;
    position: absolute;
    left: -40px;
    height: 21px;
    width: 25px;
    top: 0px;
}
.setting-opt-menu ul li.imp-opt span{ background-position: right; }
.vert-menu{
    padding: 20px 20px 20px 30px;
    overflow: auto;
    height: 505px;
    margin-top: 30px;
}
.inner-content{
    border: 1px solid #eee;
    padding-left: 11px;
    position: relative;
}
.inner-content h4{ margin-bottom: 0; }
.comment-date{ color: #999999; }
.inner-vert-menu{
    padding: 10px 0 10px 60px;
    border-left: 6px solid {{$color2}};
    position: relative;
}
.inner-content:after,
.inner-content:before {
    content:"";
    border-style:solid;
    border-width:10px;
    width:0;
    height:0;
    position:absolute;
    top:100%;
    left:-30px
}
.inner-content:before {
    border-color: transparent #eee transparent transparent;
    border-width: 14px;
    top: 29px;
}
.inner-content:after {
    margin-top: -2px;
    border-color: #fff transparent transparent;
}
.inner-content:after {
    border-color: transparent #fff transparent transparent;
    border-width: 15px;
    top: 30px;
}
.inner-vert-menu:before {
    content: "";
    font-family: FontAwesome;
    position: absolute;
    left: -20px;
    font-size: 22px;
    line-height: 30px;
    top: 35px;
    height: 35px;
    width: 35px;
    color: #fff;
    background: {{$color1}};
    border-radius: 50%;
    text-align: center;
    border: 2px solid #dadada;
}
.inner-vert-menu.icon-message:before,
.inner-vert-menu.icon-task:before,
.inner-vert-menu.icon-meeting:before{
    font-size: 16px;
    padding: 9px 8px;
    line-height: 15px;
}
.inner-vert-menu.icon-message:before{ content: "\f086"; }
.inner-vert-menu.icon-task:before{ content: "\f0ae"; }
.inner-vert-menu.icon-meeting:before{ content: "\f0c0"; }
.inner-vert-menu.icon-file:before {
    content: "\f15b";
    font-size: 14px;
    padding: 9px 11px;
    line-height: 15px;
}
#main-menu{ background: {{$color2}}; }
#main-menu ul>li.menu-has-child>a>i {
    font-size: 10px;
    margin-left: 4px;
    position: relative;
    top: -1px;
}
#main-menu ul>li ul.dropdown-menu{ padding: 0; }
#main-menu .nav>li>a:hover, #main-menu .nav>li>a:focus, #main-menu .nav>li.active>a{
    background-color: {{$color2}};
}
#main-menu ul>li ul.dropdown-menu,
#main-menu ul>li ul.dropdown-menu li{
    -webkit-transition: all 0.4s ease-in-out;
    -moz-transition: all 0.4s ease-in-out;
    -o-transition: all 0.4s ease-in-out;
    transition: all 0.4s ease-in-out;
}
#main-menu ul>li ul.dropdown-menu li{
    /*background: {{$color1}}; */
    background: {{$color2_transprancy65}};
    padding: 0 10px;
    list-style: none;
}
#main-menu ul>li ul.dropdown-menu li:hover{
    background :{{$color2}};
}
#main-menu ul>li ul.dropdown-menu>li:hover,
#main-menu ul>li ul.dropdown-menu>li.active,
#main-menu ul>li ul.dropdown-menu>.list-group>li.active
#main-menu ul>li ul.dropdown-menu>.list-group>li.active{
    background: {{$color1}};
}
#main-menu ul>li ul.dropdown-menu>li>a,
#main-menu ul>li ul.dropdown-menu>.list-group>li>a {
    display: block;
    padding: 5px 5px;
    background: none;
    /*border-bottom: 1px solid {{$color2}};*/
}
#main-menu .dropdown-menu,
#main-menu ul>li ul.dropdown-menu li:last-child>a{
    border: none;
}
.inline-form-sec { padding: 14px 0; }
.inline-form-sec form {
    background: #fff;
    padding-top: 26px;
    padding-bottom: 30px;
    border: 1px solid #cecece;
}
.inline-form-sec .form-group{ margin-bottom: 0px; }
.inline-form-sec label {
    margin-bottom: 14px;
    font-weight: normal;
    font-size: 16px;
}
.inline-form-sec .form-control, .form-control {
    color: #333333;
    border: 1px solid #dfdfdf;
    -webkit-box-shadow: none;
    box-shadow: none;
    padding: 4px 24px 4px 12px;
}
form label,.form-group label{
    font-size: 14px;
    color: #171717;
    font-weight: 400;
}
form .form-sec-title {
    font-size: 20px;
    margin-bottom: 15px;
}
label.checkbox-inline,
label.radio-inline{
    font-size: 14px;
}
.form-control {
    /*height: 42px;*/
    padding: 5px 12px;
}
textarea.form-control { height: 100px; }
.inline-form-sec button.btn[type="submit"] {
    margin-top: 22px;
    background: none;
    border: none !important;
    outline: none !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    padding: 0;
    font-size: 22px;
    min-width: inherit;
}
.inline-form-sec .dsearch-form button.btn[type="submit"] { min-width: inherit; }
.inline-form-sec button.btn[type="submit"]:hover i { color: {{$color1}}; }
.title-sec{
    padding-top: 26px;
    padding-bottom: 8px;
}
.select-cover{
    position: relative;
    z-index: 9;
    background: #fff;
}
.select-cover i{
    position: absolute;
    font-size: 11px;
    height: 11px;
    right: 10px;
    top: 0;
    bottom: 0;
    margin: auto;
    z-index: -1;
}
.select-cover select{
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    text-overflow: '';
    background-color: transparent;
    padding-right: 30px;
}
.select-cover select::-ms-expand { display: none; }
.no-style{
    background: none;
    border: none;
}
.drop-style1 .dropdown-toggle {
    font-size: 16px;
    padding: 0;
    text-align: left;
}
.drop-style1 i {
    margin-left: 5px;
    margin-top: 5px;
    font-size: 11px;
    vertical-align: top;
}
.btn-color1.btn-primary {
    color: #fff;
    background-color: {{$color1}};
    outline: none !important;
}
.btn-color1.btn-primary:hover,
.btn-color1.btn-primary:focus{
    background-color: {{$color1}};
    opacity: 0.8;
}
.btn-primary:hover,
.btn-primary:focus,
.btn-primary.focus,
.btn-primary:active,
.btn-primary.active,
.open>.dropdown-toggle.btn-primary {
    background-color: {{$color2}};
    opacity: 0.8;
    border-color: transparent;
}
.dropdown-toggle.btn-primary i {
    margin-left: 7px;
    font-size: 11px;
    position: relative;
    top: -1.5px;
}
.btn-color1.btn-primary i{ margin-right: 10px; }
.table-style1 table th, .table-style1 table td{ text-align: center; }
.table-style1 {
    border: 1px solid #cccccc;
    border-top: 2px solid {{$color2}};
    background: #fff;
    position: relative;
}
.react-bs-table-tool-bar {
    position: absolute;
    bottom: 100%;
    width: 100%;
    padding: 8px 0;
}
.table-style1 .table thead tr { background: #f7f6f7; }
.table-style1 .table>thead>tr>th {
    border-bottom: 1px solid #d0d0d0;
    position: relative;
}
.table-style1 .table>thead>tr>th::before {
    content: "";
    border-right: 1px solid #d0d0d0;
    height: 63%;
    position: absolute;
    top: 0;
    bottom: 0;
    right: 0;
    margin: auto;
}
.table-style1 .table>thead>tr>th:last-child::before,
.table-style1 .table>thead>tr>th.no-vertical-line::before,
.table-style1 .table>thead>tr>th.no-sorting::after,
.table-style1 .table>thead>tr>th.no-border-line::before{
    display: none;
}
.table>thead>tr>th,
.table>tbody>tr>th,
.table>tfoot>tr>th,
.table>thead>tr>td,
.table>tbody>tr>td,
.table>tfoot>tr>td {
    border-top: 1px solid #d0d0d0;
    vertical-align: middle;
}
.table>tbody>tr>td {
    word-wrap: break-word;
    overflow:visible;
}
.download-btn, .delete-btn {
    padding: 0px 10px;
    font-size: 18px;
    display: inline-block;
}
a.download-btn::after, a.delete-btn::after{
    content: "";
    display: block;
    clear: both;
}

.tab-section { padding-top: 20px; }
.nav-tabs { border-bottom: 1px solid #bababb; }
.nav-tabs>li { margin-bottom: 0; }
.nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus,
.tab-menu>li.current-menu-item>a, .nav-tabs>li>a:hover, .nav-tabs>li>a:focus {
    color: #fff;
    background-color: {{$transprancy7}};
    border: 1px solid transparent;
}
.nav .open>a, .nav .open>a:hover, .nav .open>a:focus {
    background-color: {{$color1}};
    border-color: {{$color1}};
    color: #fff;
}
.nav .open>a i, .nav .open>a:hover i, .nav .open>a:focus i,
.nav-tabs>li.active>a i, .nav-tabs>li.active>a:hover i, .nav-tabs>li.active>a:focus i{
    color: #fff;
}
.nav-tabs>li>a {
    color: #333333;
    padding: 2px 14px;
    cursor: -webkit-pointer;
    cursor:pointer;
}
.nav-tabs>li>a i {
    font-size: 11px;
    margin-left: 4px;
    color: {{$color2}};
    position: relative;
    top: -1px;
}
#setting-icons i.fa.fa-mobile {
    font-size: 26px;
    position: relative;
    top: 2px;
}
.nav-tabs .dropdown-menu {
    margin-top: 0px;
    background: {{$color2}};
    padding: 0px;
    border: none;
    width: 100%;
}
.nav-tabs .dropdown-menu>li{ padding: 0 6px; }
.nav-tabs .dropdown-menu>li>a {
    padding: 5px 12px;
    color: #fff !important;
    /*border-bottom: 1px solid {{$color3}};*/
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.nav-tabs .dropdown-menu>li:last-child>a{ border: none; }
.nav-tabs .dropdown-menu>li>a:hover, .nav-tabs .dropdown-menu>li>a:focus {
    background-color: transparent;
    outline: none !important;
}
.nav-tabs .dropdown-menu>li:hover, .nav-tabs .dropdown-menu>li:focus,
.dropdown-menu>.active, .dropdown-menu>.active:hover, .dropdown-menu>.active:focus{
    background-color: {{$color1}};
}
.dropdown-menu>.active>a, .dropdown-menu>.active>a:hover, .dropdown-menu>.active>a:focus {
    background-color: transparent;
}
.dropdown.drop-style2 {
    display: inline-block;
}
.drop-style2 button.dropdown-toggle {
    border: 1px solid #cecece;
    padding: 7px 25px 7px 15px;
    border-radius: 5px;
    line-height: 20px;
    position: relative;
}
.drop-style2 button.dropdown-toggle i {
    font-size: 11px;
    margin-left: 7px;
    vertical-align: top;
    margin-top: 5px;
    position: absolute;
    right: 8px;
}
.drop-style2 .dropdown-menu { min-width: 100%; }
.radio label { padding-left: 5px; }
.checkbox label{ padding-left: 10px; }
.radio label,.radio label::before,.checkbox label,.checkbox label::before{
    outline: none !important;
    line-height: 15px;
}
.radio label::before {
    content: "";
    display: inline-block;
    position: absolute;
    width: 15px;
    height: 15px;
    left: 0;
    margin-left: -20px;
    border: 1px solid #cccccc;
    border-radius: 50%;
    background-color: #fff;
    -webkit-transition: border 0.15s ease-in-out;
    -o-transition: border 0.15s ease-in-out;
    transition: border 0.15s ease-in-out;
}
.radio label::after,
.required-field .radio label::after {
    background-color: {{$color2}};
    position: absolute;
    content: " ";
    width: 9px;
    height: 9px;
    left: 3px;
    top: 3px;
    margin-left: -20px;
    display: inline-block;
    border-radius: 50%;
}
.checkbox label::after { color: {{$color2}}; }
.checkbox label::before { border-radius: 0px; }
form.default-form section {
    border-top: 1px solid #d1d1d1;
    padding-top: 15px;
    padding-bottom: 15px;
}
form.default-form {
    padding-top: 0;
    padding-bottom: 15px;
}
.date-field {
    position: relative;
    background: #fff;
}
.date-field input {
    background: transparent;
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}
.date-field i {
    position: absolute;
    right: 12px;
    top: 0;
    bottom: 0;
    margin: auto;
    font-size: 19px;
    height: 20px;
}
.btn {
    min-width: 132px; 
    outline: none !important;
}
.btn.small{ padding: 4px 10px; }
.btn.normal{ min-width: auto; }
.noselect,.noselect *{
    -moz-user-select: none;
    -khtml-user-select: none;
    -webkit-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
.page-content{
    /*padding-top: 35px;*/
    padding-bottom: 35px;
    /* min-height: 684px; */
}
.business-email-form h3 {
    line-height: 34px;
    margin-bottom: 20px;
    color: #000;
}
.business-email-form p{ font-size: 16px; }
.business-email-form .input-group .form-control { border: 1px solid {{$color1}}; }
.business-email-form .input-group { padding-top: 12px; }
.business-email-form .input-group-btn button{ height: 42px; }
.business-email-form .input-group-btn:last-child>.btn{ margin-left: 0px; }
.grey-bg{ background: #717171; }
.registration-verifiy {
    padding: 50px 0;
    min-height: 735px;
}
.registration-verifiy h2{ font-size: 28px; }
.regi-verification-content {
    margin-bottom: 40px;
    margin-top: 32px;
    line-height: 24px;
}
.regi-verification-content span{
    font-style: italic;
    margin-left: 6px;
}
.registration-verifiy .para {
    margin-bottom: 0px;
    font-size: 15px;
}
input.verify-code {
    width: 46px;
    height: 67px;
    background: #79797a;
    border: 2px solid #e2e2e2;
    border-radius: 8px;
    font-size: 28px;
    color: #fff;
    text-align: center;
    margin: 0px 2px;
    outline: none !important;
}
.verification-code-form span {
    color: #fff;
    font-size: 58px;
    font-weight: 100;
    vertical-align: bottom;
}
.verification-code-form p {
    font-size: 12px;
    margin-top: 80px;
}
.business-email-form.your-email-account .input-group {
    max-width: 350px;
    margin: 0 auto;
}
.business-email-form.your-email-account .input-group-btn .btn.disabled{ opacity: 1; }
.your-account-submit { margin-top: 30px; }

/* Login Form CSS Start */
.login-form {
    max-width: 338px;
    padding: 15px 8px 15px;
    margin: 0 auto;
    background-color: {{$color2}};
    border-radius: 6px;
}
.login-form .login-form-heading {
    margin-bottom: 46px;
    margin-top: 32px;
}
.login-input{ position: relative; }
.login-input i {
    position: absolute;
    left: 14px;
    top: 20px;
    margin: auto;
    height: 14px;
    color: #c7c7c7;
    z-index: 9;
}
.login-pass.login-input i {
    font-size: 18px;
    height: 17px;
}
.login-form .form-control {
    position: relative;
    font-size: 14px;
    height: auto;
    padding: 15px 36px 15px 38px;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
.login-form  .form-control:focus { z-index: 2; }
.login-form .login-email input{
    margin-bottom: -1px;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}
.login-form .login-pass input{
    margin-bottom: 20px;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
}
.login-form button[type="submit"].btn-primary {
    color: #fff;
    background-color: {{$color1}} !important;
    border-color:  {{$color1}} !important;
    width: 80%;
    margin: 72px auto 50px;
    outline: none !important;
    padding: 12px 20px;
    border-radius: 4px;
    -webkit-box-shadow: 1px 2px 7px rgba(0, 0, 0, 0.43);
    -moz-box-shadow: 1px 2px 7px rgba(0, 0, 0, 0.43);
    box-shadow: 1px 2px 7px rgba(0, 0, 0, 0.43);
    white-space: normal;
    font-size: 16px;
    min-height: 50px;
}
/* Login Form CSS end */

.table thead tr th i.fa-sort {
margin-left: 8px;
color: #6f6f6f;
font-size: 15px;
cursor: pointer;
}

/****************************/
/* Bootstrap MENU CSS start */
/***************************/

.tab-menu { width: 100%; }
.tab-menu .dropdown-submenu { position: relative; }
.tab-menu .dropdown-submenu>.dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: 0px;
    margin-left: 0px;
    -webkit-border-radius: 0 6px 6px 6px;
    -moz-border-radius: 0 6px 6px;
    border-radius: 0 6px 6px 6px;
}
.tab-menu .dropdown-submenu:hover>.dropdown-menu { display: block; }
.tab-menu .dropdown-submenu>a:after {
    display: block;
    content: " ";
    float: right;
    width: 0;
    height: 0;
    border-color: transparent;
    border-style: solid;
    border-width: 5px 0 5px 5px;
    border-left-color: #ccc;
    margin-top: 5px;
    margin-right: -10px;
}
.tab-menu .dropdown-submenu:hover>a:after { border-left-color: #fff; }
.tab-menu .dropdown-submenu.pull-left { float: none; }
.tab-menu .dropdown-submenu.pull-left>.dropdown-menu {
    left: -100%;
    margin-left: 10px;
    -webkit-border-radius: 6px 0 6px 6px;
    -moz-border-radius: 6px 0 6px 6px;
    border-radius: 6px 0 6px 6px;
}
.tab-menu-content { padding-top: 18px; }

table.dataTable {
    margin-top: 0px !important;
    margin-bottom: 0px !important;
}
div.dataTables_wrapper div.dataTables_info { padding: 24px 8px 24px 30px; }
div.dataTables_wrapper div.dataTables_paginate { padding: 15px 30px 10px 12px; }
.table-style1 table tbody tr:last-child td { border-bottom: 1px solid #d0d0d0; }
.table-style1 *{ outline: none !important; }
table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after, table.dataTable thead .sorting_asc_disabled:after, table.dataTable thead .sorting_desc_disabled:after {
    position: relative;
    top: 0px;
    left: 12px;
    bottom: initial;
    right: initial;
    display: inline;
}
.pagination>.disabled>span, .pagination>.disabled>span:hover, .pagination>.disabled>span:focus, .pagination>.disabled>a, .pagination>.disabled>a:hover, .pagination>.disabled>a:focus {
    display: none;
}
.fa-ellipsis-v {
    font-size: 28px;
    vertical-align: middle;
    margin-right: 5px;
}
.fa-ellipsis-v.green { color: #00af5f; }
.fa-ellipsis-v.blue{ color: #00a2d0; }
.fa-ellipsis-v.red{ color: #e64156; }
.fa-ellipsis-v.orange{ color: orange; }
table .fa-pencil{
    margin-left: 18px;
    cursor: pointer;
}
table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after{
    font-family: FontAwesome;
}
table.dataTable thead .sorting:after { content: "\f0dc"; }
table.dataTable thead .sorting_asc:after{ content: "\f0de"; }
table.dataTable thead .sorting_desc:after{ content: "\f0dd"; }
.footer {
    padding: 5px 0;
    position: absolute;
    bottom: 0px;
    width: 100%;
}
.btn-group.radio-btns{ display: block; }
.btn-group.radio-btns .btn {
    box-shadow: none;
    border: 1px solid #cecece;
    min-width: auto;
    padding: 9px 18px;
}
.btn-group.radio-btns .btn.active {
    background: {{$color1}};
    border: 1px solid {{$color1}};
    color: #fff;
}
.table { margin-bottom: 0; }
.role {
    color: #fff;
    padding: 1px 5px;
    display: inline-block;
    margin: 3px 0;
    border-radius: 2px;
    margin-right: 4px;
}
.resend_mail, .resend_mail:hover { color: #33c9ff;}
.tooltip.top .tooltip-arrow { border-top-color: {{$color1}} !important; }
.agenda-title {
    border-radius: 3px;
    color: #fff !important;
    background: {{$color2}};
    font-size: 22px;
    border-radius: 0;
    padding: 10px;
    text-align: center;
}
.all-agenda-topics li::after {
    content: "";
    display: block;
    clear: both;
}
.all-agenda-topics {
    padding: 0px 0px 5px 0;
    display: inline-block;
    width: 100%;
    border-bottom: 1px solid {{$color1_transprancy1}};
}
.all-agenda-topics .rc-draggable-list-draggableRow>li, .all-agenda-topics>li {
    border-top: 1px solid {{$color1_transprancy1}};
    padding: 2px 0 2px 10px;
    padding-right: 80px;
}
.rc-draggable-list-draggableRow{
    font-size: 13px;
    line-height: 22px;
    position: relative;
}
/* .all-agenda-topics>li:last-child{ border-bottom: 1px solid #e5e4e5; } */
.all-agenda-topics li {
    list-style: none;
    position: relative;
    padding: 5px 0px;
}
.all-agenda-topics li i.fa.fa-pencil {
    margin-left: 10px;
    color: #bbb;
    cursor: pointer;
}
ol.all-agenda-topics>li a{ color: #333333; }
.all-agenda-topics .order-no { margin-right: 5px;}
.all-agenda-topics .text{
    display: table-cell;
    padding-right: 10px;
}
.agenda-heading-action {
    border-bottom: 1px solid {{$color1_transprancy1}};
    position: relative;
}
ul.agenda-actions {
    position: absolute;
    right: 0px;
    top: 1px;
    margin: 0;
    width: 90px;
    text-align: right;
    }
ol.all-agenda-topics>li>ul.agenda-actions{
    /*top: 10px;*/
    top: 4px;
}
ul.agenda-actions>li{
    width: 33%;
    float: right;
    padding: 0px;
    margin: 4px;
    text-align: center;
    width: auto;
    cursor: pointer !important;
}
ul.agenda-actions>li i{
    cursor: pointer;
    font-size: 14px;
}
i.inactive, .agenda-actions li.collapsed i{ color: #adadad }
i.inactive svg path{ fill: #adadad !important; }
i.active svg path,i[aria-expanded="true"] svg path,
.agenda-actions li i.active svg path{ fill: {{$color2}}; }
i.fa-gavel.active,i.fa-gavel[aria-expanded="true"], .agenda-actions li i.active, .selectUserBtn i { color: {{$color2}}; }
.agenda-actions li.collapsed i svg path{ fill: #adadad; }
i.fa-recycle {
    position: relative;
    top: 1px;
}
.dec-discuss-sec {
    padding-left: 14px;
    margin-top: 5px;
}
.discuss-dec-text {
    font-size: 18px;
    color: #000;
    margin-bottom: 5px;
}
.grandTitle{ font-weight: normal; }
.dtext strong{ font-size: 12px; }
.dtext p{
    font-size: 16px;
    white-space: pre-wrap;
}
.discussion-block{
    margin-left: 40px;
    break-inside: avoid;
    page-break-inside: avoid;
    -webkit-column-break-inside: avoid;
    -moz-column-break-inside: avoid;
}
.discussion-form label,.task-list label,.task-list table{ font-size: 12px; }
/** Agenda PDF Css Start**/
.agenda-header {border-bottom: 30px solid #0a6cb3;}
.seprator{border-top: 1px solid #0a6cb3;}
.seprator-grey hr{border-top: 1px solid #d1d1d1;}
.agenda-content ul{color: #17375e;}
.agenda-content ul, .agenda-inner-content>ul{
    -webkit-padding-start: 0;
    list-style-type: none;
}
.topics-level1,.topics-level2{padding-left: 40px;}
.agenda-inner-content{font-size: 16px;}
.agenda-inner-content ul li{list-style-type: none;}
.doc-attach{float: right;}
.doc-attach a{
    text-decoration: underline;
    padding-right: 3px;
    font-size: 12px;
    color: #4e74b0;
}
.agenda-heading h4{letter-spacing: 2px;}
/** Agenda PDF Css End**/

.all-agenda-topics textarea.form-control {height: 90px;}
.all-agenda-topics hr {
    margin-top: 0;
    border-top: 1px solid {{$color2_transprancy25}};
}
.single-msg-cover {
    padding: 10px 20px;
    /* border-bottom: 1px solid #e4e4e4; */
    position: relative;
    /*background: #fff;*/
}
.single-msg-cover:last-child{border: none;}
.single-msg {
    position: relative;
    padding-left: 50px;
}
.single-msg .user-img {
    height: 40px;
    width: 40px;
    display: block;
    position: absolute;
    top: 4px;
    left: 0;
    border-radius: 50%;
    border: 1px solid {{$color2}};
}
.time-sec-inner {font-size: 12px; color: #b7b7b7;}
button.reply-btn {
    background-color: {{$color2}};
    color: #fff;
    border: none;
    padding: 4px 10px;
    border-radius: 2px;
    font-size: 13px;
}
.msg-action-btns button {
    color: {{$color2}};
    border: 0px solid {{$color2}} !important;
    background: transparent;
    margin-left: 2px;
    font-size: 14px;
    padding: 1px 3px;
}
.single-reply-msg {
    position: relative;
    padding: 10px 10px 10px 60px;
    margin-top: 10px;
    background: #fff;
    border: 1px solid #dcdcdc;
}
.single-msg .single-reply-msg .user-img, .single-msg .single-reply-msg .userPicName{
    left: 12px;
    top: 12px;
}
.btn.btn-primary.collapsable-btn {padding: 5px 14px;}
.btn.btn-primary.collapsable-btn i{
    font-size: 11px;
    position: relative;
    top: -1px;
}
.task-form-title{
    color: #3d4041;
    padding: 8px 5px 5px 10px;
    text-transform: uppercase;
    font-size: 15px;
    display: none;
}
.task-form-title span {text-transform: none;}
.title-block{
    text-align: center;
    padding: 30px 0;
}
/** Decision PDF Css Start**/
.table-style2 h5{
    padding: 4px 0 4px 9px;
    margin: 0;
    background: #4e74b0;
}
.table-style2>.table>thead>tr>th{
    font-size: 12px;
    color: #4e74b0;
}
.table-style2>.table>tbody>tr>td{
    background: #dce6f2;
    color: #7f8196;
}
.table-style2>.table>thead>tr>th, .table-style2>.table>tbody>tr>td{
    padding: 0px 8px;
    border-bottom: 2px solid #4e74b0;
}
#ViewDecisionPDF .agenda-content{ padding-left: 5px; }
.decision-box{
    border: 1px solid #4f81bd;
    padding-left: 10px;
    break-inside: avoid;
    page-break-inside: avoid;
    -webkit-column-break-inside: avoid;
    -moz-column-break-inside: avoid;
}
.decision-box h5 strong{
    border-bottom: 2px solid #4e74b0;
    padding-bottom: 1px;
}
.decision-box span{font-style: italic;}
.upcoming-meet{border-top: 3px solid #4e74b0;}
.status-btn{
    padding: 0px 3px 2px;
    background: #f0ad4e;
    display: initial;
}
#membertasks-page .table-style1 i.fa.fa-trash{
    background: transparent;
    color: #d9534f;
    padding: 2px 3px;
}
.empty-task h2{background: #f2f2f2;}
/** Decision PDF Css End**/

.company-icon {
    border: 1px solid {{$color1}};
    padding: 3px 4px;
    -webkit-box-shadow: 0px 3px 7px -3px rgba(0, 0, 0, 0.5);
    -moz-box-shadow: 0px 3px 7px -3px rgba(0, 0, 0, 0.5);
    box-shadow: 0px 3px 7px -3px rgba(0, 0, 0, 0.5);
    margin-left: 10px;
    float: right;
}
.company-icon img {width: 26px;}
#setting-icons ul li:last-child, #setting-icons ul li:last-child a { padding-right: 0; }
#main-menu .container { position: relative; }
.head-start-btn {
    display: inline-block;
    margin-left: 15px;
    vertical-align: top;
    margin-top: 0px;
}
.head-start-btn .btn-color1.btn-primary {
    padding: 3px 17px 3px;
    border-radius: 14px;
    margin: 5px 0 3px;
}
.panel{
    border-radius: 2px;
    margin-bottom: 30px;
}
.panel-heading {
    border-top-left-radius: 1px;
    border-top-right-radius: 1px;
}
.panel-primary .panel-heading{ background: {{$color2}}; }
.panel-primary .panel-heading .panel-title{
    font-size: 14px;
    text-transform: uppercase;
}
.btn-space{ margin: 10px 10px; }
.panel-body ul{
    padding-left: 0px;
    list-style: none
}
.panel-body ul.panel-list{
    padding-left: 20px;
    list-style: none
}
.panel-body ul.panel-list li{
    padding-left: 10px;
    position: relative;
    font-size: 12px;
}
.panel-body ul.panel-list li:before{
    content: '';
    position: absolute;
    left: 0;
    width: 5px;
    height: 2px;
    background: #777;
    top: 0;
    bottom: 0;
    margin: auto;
}
.title { padding: 5px 15px; }
.eventHints{ margin-top: 46px; }
.eventHints li{
    font-size: 12px;
    margin: 12px 0px;
}
.eventHints li span{
    display: inline-block;
    height: 10px;
    margin-right: 5px;
    width: 15px;
}
.hintBlue{ background: #446a9e; }
.hintGreen{ background: #a5c74d; }
.hintPink{ background: #ce89af; }
.hintBlack{ background: #343f18; }
.hintPurple{ background: #662a4b; }
.hintTeal{ background: #006560; }
.form-left-right-row .form-left-sec, .form-left-right-row .form-right-sec{
    padding-left: 40px;
    padding-right: 40px;
}
.row.form-left-right-row {
    margin-right: -25px;
    margin-left: -25px;
}
.welcome-video.row{
    position: relative;
    margin-top: -15px;
    padding: 5px;
}
.welcome-video{ background: #002d59; }
.list-title {
    position: relative;
    padding-right: 40px;
}
.list-title .btn{
    position: absolute;
    right: 0;
    top: 0;
    padding: 0px 10px;
    border-radius: 2px;
}
.start-listing { padding: 20px; }
.start-listing ul{ padding-left: 20px; }
.start-listing ul li{
    list-style: none;
    padding: 7px 0;
}
.panel-body .setting-opt-menu ul{ padding-left: 10px; }
.setting-opt-menu ul li .switch {
    position: absolute;
    left: 0;
    top: 3px;
    margin: 0px 5px -1px 0px;
}
.date-confie small .fa{ font-size: 16px; }
.btn-xs {
    font-size: 14px !important;
    padding: 5px 15px !important;
}
.clearfix {
    display: block;
    clear: both;
}
.table>tbody>tr>td .dropdown-menu>li>span, .table>tbody>tr>td .dropdown-menu>li>a, .dropdown-menu>li>span {
    display: block;
    padding: 3px 20px !important;
    clear: both;
    font-weight: 400;
    line-height: 1.42857143;
    color: #333 !important;
    white-space: nowrap;
    cursor: pointer;
}
.table tbody tr td .dropdown-menu > li > span > a{
    padding: 0px 0px !important;
    color: #333 !important;
}
.table-style1 .btn.dropdown-toggle {
    min-width: initial;
    padding: 6px 12px;
}
.react-bs-table-pagination .row {
    margin-left: 0;
    margin-right: 0;
}
.react-bs-table table td, .react-bs-table table th {
    overflow: initial !important;
    white-space: normal !important;
}
body .DayPicker {
    display: inline-block;
    width: 100%;
    max-width: 300px;
    margin: 0px;
    z-index: 999;
}
body .DayPicker-wrapper {
    border: 1px solid #cecece;
    outline: none;
}
body .DayPicker-Day { position: relative; }
body .DayPicker-Caption { 
    height: 35px;
    color: #333;
    margin: -2px -2px 0px -2px;
    line-height: 35px;
    text-align: center !important;
    padding: 0px 35px;
}
body  .DayPicker-Month {
    margin: 0;
    width: 100%;
}
.form-inline .select-cover { display: inline-block; }
.date-list{ padding-left: 0; }
.date-list li{ list-style: none; }
button.btn.bordered {
    background: #ffffff;
    border: 1px solid #9a9a9a;
    outline:none;
}
span.order {
    position: relative;
    width: 16px;
    height: 16px;
    display: inline-block;
    vertical-align: middle;
    margin-top: -3px;
    margin-left: 2px;
}
span.order> .caret {
    position: relative;
    top: -3px;
}
.order span.dropup,
.order span.dropdown{
    width: 8px;
    height: 8px;
    display: inline-block;
    left: 0;
    right: 0;
    margin: auto;
}
.order span.dropdown {
    position: absolute;
    bottom: 0;
}
.order span.dropup {
    position: absolute;
    top: 0;
}
.order span.dropdown span.caret,
.order span.dropup span.caret{
    margin: 2px 0px !important;
    display: block;
}
span.caret{ margin: 5px 2px !important; }
.edit-block {
    padding-right: 50px;
    position: relative;
}
.edit-block-btn {
    position: absolute;
    right: 0px;
    top: 0px;
    font-size: 18px;
    padding: 5px 7px;
    cursor: pointer;
    border: 1px solid;
    background: rgba(0, 106, 176, 0.79);
    border-radius: 4px;
    color: #ffffff !important;
}
.edit-map-btn {
    position: absolute;
    right: 0;
    top: 2px;
    width: 28px;
    cursor: pointer;
}
.btn.btn-primary i{ font-size: 10px; }
.head-start-btn .btn.btn-primary i {
    font-size: 14px;
    margin-right: 3px;
}
.date-view {
    display: inline-block;
    border-radius: 3px;
    position: relative;
    margin-top: 6px;
}
.approve-date, .reject-date, .pending-date{
    border-radius: 50px;
    height: 24px;
    line-height: 26px;
    margin-top: 2px;
    margin-right: 10px;
    width: 24px;
    display: none;
}
.approve-date{ color: {{$color2}}; }
.date-view i.approve-date{ font-size:16px; }
.reject-date{
    background: #e64156;
    color: #fff;
}
.date-view i {
    font-size: 16px;
    vertical-align: top;
}
.pending-date{ color: {{$color2}}; }
.date-of-meeting {
    margin: 5px 0px;
    float: left;
    width: 100%;
    position: static;
}
.date-view i:last-child {
    margin-right: 0px;
    font-size: 28px;
}
.meeting-timedetail .date-view i:last-child {     font-size: 23px; }
.meeting-timedetail span {
    color: #7b7b7b;
    font-size: 12px;
}
.meeting-timedetail span.month{ font-size: 14px; }
.meeting-mail-icon{
    position: absolute;
    left: 100%;
    top: 13px;
    cursor: pointer;
    width: 24px;
    height: 24px;
}
.fill-check:checked + label:after {
    content: '';
    position: absolute;
    width: 12px;
    height: 6px;
    border-left: 2px solid #fff;
    border-bottom: 2px solid #fff;
    -moz-transform: rotate(-50deg);
    -webkit-transform: rotate(-50deg);
    -ms-transform: rotate(-50deg);
    transform: rotate(-50deg);
    top: 4px;
    left: 3px;
}
.fill-check{
    position: absolute;
    opacity: 0;
}
.fill-check + label {
    position: relative;
    margin-right: 10px !important;
    margin-top: 1px !important;
    width: 26px;
    height: 26px;
}
.meeting-timedetail .fill-check + label{
    width: 20px;
    height: 20px;
}
.fill-check + label:before {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border: 1px solid #ccc;
    background: #fff;
    border-radius: 3px;
    left: -1px;
    top: -1px;
}
.fill-check:checked + label:before {
    content: '';
    background: {{$color2}};
    border-color: {{$color2}};
}
.fill-check:checked + label:after {
    content: '';
    position: absolute;
    width: 12px;
    height: 6px;
    border-left: 2px solid #fff;
    border-bottom: 2px solid #fff;
    -moz-transform: rotate(-50deg);
    -webkit-transform: rotate(-50deg);
    -ms-transform: rotate(-50deg);
    transform: rotate(-50deg);
    top: 4px;
    left: 3px;
}
.radio label::before { border: 1px solid #9a9a9a; }
.table-style1 table .form-control {
    height: 34px;
    max-width: 240px;
    margin: 0 auto;
}
.table-style1 table .form-control[type="file"] {
    padding: 3px 0;
    border: none;
}
.addunion-table .table-style1 table tbody tr:last-child td { border-bottom: none; }
.table-style1 table .remover { font-size: 18px; }
.settings-menu-list li ul li{ position: relative; }
.settings-menu-list li ul li::before {
    content: '';
    position: absolute;
    left: -10px;
    font-size: 15px;
    top: 7px;
    color: #959595;
    border-left: 5px solid #c1c1c1;
    border-bottom: 3px solid transparent;
    border-top: 3px solid transparent;
}
.action_btn{ cursor: pointer; }
.dropdown-list li{
    cursor: pointer;
    display: block;
    padding: 3px 20px;
    clear: both;
    font-weight: 400;
    line-height: 1.42857143;
    color: #333;
    white-space: nowrap;
    cursor: pointer;
}
.dropdown-list li:hover{
    background-color: rgb(245, 245, 245);
    text-decoration: none;
}
.profile-img{ border-radius: 50% }
.modal-backdrop{ z-index: 0 !important; }
.pointer{ cursor: pointer; }
.text-field-block{
    padding: 10px 0px;
    border-radius: 5px;
}
.text-field-parent{
    display: inline-block;
    width: 100%;
    margin-bottom: 20px;
}
.text-field-label{
    margin: 10px 0px;
    font-weight: 700;
    position: relative;
}
.doodle-meeting-form .text-field-label { margin-bottom: 5px; }
.text-field-label::after {
    content: ':';
    position: absolute;
    margin-left: 5px;
}
.view-radio .radio label::before{ display: none; }
.task-table .label{
    font-weight: 400;
    font-size: 90%;
    border-radius: 0;
}
.upload_img{
    margin-top:10px;
    max-width: 400px;
}
.faq-accordion .card-header {
    background: {{$color1}};
    padding: 0px 15px;
    display: inline-block;
    width: 100%;
}
.faq-accordion .card-header a{
    color: #fff;
    position: relative;
    display: inline-block;
    width: 100%;
    padding-right: 20px;
}
.faq-accordion .card-block {
    padding: 15px;
    background: #f3f1f1;
}
.collapse{
    position: relative;
    display:none;
}
.faq-accordion .card-header a::after {
    content: '\f107';
    font-family: FontAwesome;
    color: #fff;
    font-size: 22px;
    position: absolute;
    bottom: 0px;
    right: 0px;
    line-height: 18px;
}
.all-agenda-topics > li .text{ display: inline-block; }
.all-agenda-topics > li .text input{
    border: none;
    border-bottom: 1px dashed #ccc;
    margin-right: 5px;
    font-size: 14px;
    font-weight: normal;
}
.editTxt {
    position: relative;
    margin-top: 5px;
    display: none;
}
.editTxt textarea{
    width: 100%;
    border: 1px solid #e5e4e5;
    padding: 10px;
    height:100px;
    position: relative;
    float: left;
    margin-bottom: 10px;
    border-radius: 3px;
}
.editTxt .btn-group{
    position: absolute;
    right:0px;
    bottom: 1px;
}
.editTxt .btn-group button{
    padding: 2px 10px;
    font-size: 13px;
    min-width: inherit;
    border-radius: 0px;
}
.all-agenda-topics button.save{
    padding: 1px 10px;
    font-size: 12px;
    min-width: inherit;
    border-radius: 0px;
    border-radius: 3px;
    display: none;
    height: 24px;
    line-height: 20px;
}
.download-btn{ cursor: pointer; }
.table-style1{ overflow: inherit !important; }
.blue{ color: {{$color2}}; }
.action_alert{
    display: block;
    color: red;
}
.tabs-cover {
    position: relative;
    border-bottom: 2px solid #bababb;
    min-height: 36px;
}
.tabs-cover ul {
    margin: 0;
    position: relative;
    margin-bottom: -2px;
}
.tabs-cover ul li,
.tabs-cover ul li.add-tab:hover {
    padding: 3px 20px 9px;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    font-size: 16px;
}
.tabs-cover ul li.active-tab,
.tabs-cover ul li:hover {
    border-bottom: 2px solid #0b8ec0;
    color: #0b8ec0;
}
.tabs-cover ul li.add-tab, .tabs-cover ul li.add-tab:hover { padding: 0px 10px 0px; }
button.like-star {
    position: absolute;
    top: 0;
    right: 0;
    background: #9c9c9c;
    border: none;
    color: #fff;
    padding: 7px 9px;
    font-size: 18px;
}
button.all-like-btn.like-star:hover,
button.all-like-btn.like-star.active,
.single-msg-cover.active>.like-star,
.single-reply-msg.active>.like-star{
    color: #ffffff;
    background: #16568e;
}
button.like-star:hover,
button.like-star:focus{
    outline: none;
}
button.single-like-btn.like-star {
    left: 100%;
    padding: 7px 10px 7px 5px;
    border-radius: 0px 17px 18px 0px;
    font-size: 10px;
}
.single-msg-cover.active { background: #e2f3ff; }
.single-reply-msg.active {
    background: #b6dbf9;
    border: none;
    -moz-box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.22);
    -webkit-box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.22);
    box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.22);
}
.add-tab-btn {
    background: none;
    border: none;
    outline: none;
    color: #7d7d7d;
    padding: 6px 6px;
}
.add-tab-btn:hover{ color: #0b8ec0; }
.modal {
    text-align: center;
    padding: 0!important;
}
.modal:before {
    content: '';
    display: inline-block;
    height: 100%;
    vertical-align: middle;
    margin-right: -4px;
}
.modal-dialog {
    display: inline-block;
    text-align: left;
    vertical-align: middle;
}
.modal-header {
    padding: 10px 15px;
    border-bottom: 1px solid #e5e5e5;
    background: {{$color2}};
    color: #fff;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
}
.close {
    font-size: 27px;
    font-weight: normal;
    color: #fff;
    text-shadow: none;
    filter: alpha(opacity=70);
    opacity: 0.7;
}
.close:focus, .close:hover {
    color: #fff;
    filter: alpha(opacity=100);
    opacity: 1;
    outline: none;
}
.modal .btn-default:hover {
    color: #fff;
    background-color: {{$color2}};
    border-color: {{$color2}};
}
.modal-body { padding: 40px 35px; }
.modal-body p{ word-break: break-all; }
.modal-body p.mail-signature{ word-break: unset; }
.modal:not(#addBlockModal) .modal-body img,
.container.tab-section .container {
    width: 100%;
}
.single-msg .collapse::after { display: none; }
.form-group span.text-danger {
    margin-top: 5px;
    display: block;
    font-size: 12px;
}
.checkbox-inline+.checkbox-inline, .radio-inline+.radio-inline { margin-left: 0; }
.checkbox.checkbox-inline label font,
.radio.radio-inline label font,
.btn font{
    vertical-align: unset !important;
}
.start_row{
    -moz-column-width: 30em;
    -webkit-column-width: 30em;
    -moz-column-gap: 1em;
    -webkit-column-gap: 1em;
    column-count: 2;
    -webkit-column-count: 2;
    -moz-column-count: 2;
}
.start_col{
    display: grid;
    margin:  0;
    width:  100%;
    break-inside: avoid;
    page-break-inside: avoid;
    -webkit-column-break-inside: avoid;
    -moz-column-break-inside: avoid;
    overflow: hidden;
}
.linkBtn a{
    margin-right: 10px;
    margin-top: 5px;
    display: inline-block;
}
.linkBtn a:last-child{ margin-right: 0px; }
.page404{ margin-top: 10%; }
#pg-loader { 
    display: none;
    position: fixed;
    z-index: 99;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.62);
}
#pg-loader img{
    width:60px;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    margin:auto;
}
.redacteur{
    margin-top: 6px;
    padding-left: 0;
}
.redacteur label{ padding-left: 0; }
.redacteur input[type=text]{
    border: 1px solid #cecece;
    -webkit-box-shadow: none;
    box-shadow: none;
    padding: 6px;
    border-radius: 0px;
}
span.edit-del-tab button {
    background: none;
    border: none;
    outline: none;
}
span.edit-del-tab { position: absolute; }
.tabs-cover ul li{ position: relative; }
span.edit-del-tab {
    display: none;
    position: absolute;
    left: 100%;
    right: 0;
    border: 1px solid #d4d4d4;
    color: #9c9c9c;
    bottom: 4px;
    top: 0;
    height: 26px;
    width: 57px;
    margin: auto;
    text-align: center;
    padding: 2px 0px;
    font-size: 14px;
    border-radius: 4px;
    background: #fff;
    z-index: 2;
}
span.edit-del-tab::after, span.edit-del-tab::before {
    content: "";
    position: absolute;
    bottom: 0;
    left: -5px;
    top: 0;
    margin: auto;
    height: 10px;
    border-right: 5px solid #bdbdbd;
    border-top: 5px solid transparent;
    border-bottom: 5px solid transparent;
}
span.edit-del-tab::before {
    left: -4px;
    z-index: 9;
    border-right: 5px solid #fff;
}
.tabs-cover ul li:hover span.edit-del-tab { display: block; }
.tabs-cover ul li:first-child span.edit-del-tab{ display: none !important; }
.spanBtn{
    color: #337ab7;
    display: inline-block;
    margin-right: 8px;
    cursor: pointer;
}
.spanBtn:hover{
    color: #23527c;
    text-decoration: underline;
}
.input-value-inner {
    width: 100%;
    background: #fff;
    border: 1px solid #cecece;
}
.input-value-inner span { padding: 5px 0px; }
.input-value-inner input {
    width: 100%;
    height: 30px;
    border: none;
    box-shadow: none;
    padding: 0 6px;
    outline: none;
}
.table-striped>tbody>tr:nth-of-type(odd) { background-color: #fff; }
.table-striped>tbody>tr:nth-of-type(even) { background-color: #f9f9f9; }
.modal-large .modal-dialog {
    max-width: 1000px;
    width: 94%;
}

/* Progress Button CSS Start */
.pb-container {
    display: inline-block;
    text-align: center;
    min-width: 142px;
}
.pb-container .pb-button {
    background: {{$color2}};
    border: 0px solid;
    border-radius: 3px;
    color: #fff;
    cursor: pointer;
    padding: 0.6em 1em;
    text-decoration: none;
    text-align: center;
    height: 34px;
    width: 100%;
    -webkit-tap-highlight-color: transparent;
    outline: none;
    transition: background-color 0.3s, width 0.3s, border-width 0.3s, border-color 0.3s, border-radius 0.3s;
}
.pb-container .pb-button span {
    display: inherit;
    transition: opacity 0.3s 0.1s;
    font-size: 14px;
    font-weight: 400;
}
.pb-container .pb-button svg {
    height: 34px;
    width: 34px;
    position: absolute;
    transform: translate(-50%, -50%);
    pointer-events: none;
    left: 11px;
    top: 11px;
}
.pb-container .pb-button svg path {
    opacity: 0;
    fill: none;
}
.pb-container .pb-button svg.pb-progress-circle {
    animation: spin 0.9s infinite cubic-bezier(0.085, 0.260, 0.935, 0.710);
}
.pb-container .pb-button svg.pb-progress-circle path {
    stroke: currentColor;
    stroke-width: 5;
}
.pb-container .pb-button svg.pb-checkmark path,
.pb-container .pb-button svg.pb-cross path {
    stroke: #fff;
    stroke-linecap: round;
    stroke-width: 4;
}
.pb-container.disabled .pb-button { cursor: not-allowed; }
.pb-container.loading .pb-button {
    width: 34px;
    height: 34px;
    border-width: 6px;
    border-color: {{$color2}};
    cursor: wait;
    border-radius: 50%;
    background-color: transparent;
    padding: 0;
    position: relative;
}
.pb-container.loading .pb-button:hover,
.pb-container.loading .pb-button:focus{
    border-color: {{$color2}} !important;
}
.pb-container.loading .pb-button span {
    transition: all 0.15s;
    opacity: 0;
    display: none;
}
.pb-container.loading .pb-button .pb-progress-circle > path {
    transition: opacity 0.15s 0.3s;
    opacity: 1;
}
.pb-container.success .pb-button {
    border-color: #A0D468;
    background-color: #A0D468;
}
.pb-container.success .pb-button span {
    transition: all 0.15s;
    opacity: 0;
    display: none;
}
.pb-container.success .pb-button .pb-checkmark > path { opacity: 1; }
.pb-container.error .pb-button {
    border-color: {{$color2}};
    background-color: {{$color2}};
}
.pb-container.error .pb-button span { transition: all 0.15s; }

@keyframes spin {
    from {
        transform: translate(-50%, -50%) rotate(0deg);
        transform-origin: center center;
    }
    to {
        transform: translate(-50%, -50%) rotate(360deg);
        transform-origin: center center;
    }
}
/* Progress Button CSS End */

.pg-content-loader{
    margin-top: 150px;
    margin-bottom: 150px;
}
.pb-container.progress-btn-full { width: 100%; }
.autocomplete-form > div,.autocomplete-form > section > div { display: block !important; }
.autocomplete-form > div > div > div,.autocomplete-form > section > div > div > div{ padding: 0px; }
.autocomplete-form > div > div,.autocomplete-form > section > div > div{ position: inherit !important; }
.inline-form-sec .autocomplete-form input{ height:31px; }
.autocomplete-form input,
.autocomplete-input input{
    display: block;
    width: 100%;
    /*height: 42px;*/
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}
.autocomplete-input div{
    width: 100%;
    position: relative;
}
.autocomplete-input div>div {
    width: 100%;
    position: inherit !important;
    left: 0 !important;
    top: 0 !important;
    padding: 2px 10px;
}
.autocomplete-form input:focus {
    box-shadow: rgba(0, 0, 0, 0.075) 0px 1px 1px inset, rgba(102, 175, 233, 0.6) 0px 0px 8px;
    border-color: rgb(102, 175, 233);
    outline: 0px;
}
.required-field label::after {
    content: " *";
    color: #af0000;
    font-size: 16px;
    line-height: 3px;
    display: inline-block;
    position: relative;
    top: 0px;
    margin-left: 4px;
}
.mail-signature{
    display: table-caption;
    white-space: pre;
    word-wrap: normal;
    line-height: 14px
}
.notification-preview span{
    display: inline-block;
    margin-left: 10px;
}
.my-autocomplete-container{
    border-bottom: honeydew;
    border-left: honeydew;
    border-right: honeydew;
    border-top: 1px solid #e6e6e6;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    border-radius: 0 0 2px 2px;
}
#site-lang li {
    position: relative;
    cursor: pointer;
    padding: 0 10px;
}
#site-lang li::after {
    content: "";
    height: 13px;
    display: inline-block;
    border-right: 2px solid #fff;
    position: absolute;
    top: 0;
    bottom: 0;
    right: -1px;
    margin: auto;
}
#site-lang li:last-child::after{ display: none; }
#site-lang li.active { color: #acebfd; }
#site-lang li.active span {
    border-bottom: 2px solid #1e83d0;
    padding: 2px 0;
}
.no-msgs {
    padding: 30px;
    text-align: center;
    font-size: 15px;
    font-weight: 700;
    color: #636363;
}
.all-msgs-container .fa-thumbs-up {
    margin-right: 2px;
    margin-left: 4px;
}
#select_user{ text-align: left; }
.file-icon {
    background-repeat: no-repeat;
    background-position: center left;
    padding: 7px 0px 7px 30px;
    background-size: 25px;
    display: inline-block;
}
#messages { overflow: hidden; }
#messages .container { padding-right: 30px; }
#messages .container .container {
    padding-right: 0;
    padding-left: 0;
}
#messages .inline-form-sec .container { padding-right: 15px; }
.container .inline-form-sec .container {
    padding-right: 15px !important;
    padding-left: 15px !important;
}
.main .container .container {
    width: 100%;
    padding-left: 0;
    padding-right: 0;
}
.docs-png{background-image: url(../../public/img/file_icon/png.png);}
.docs-pdf{background-image: url(../../public/img/file_icon/pdf.png);}
.docs-doc{background-image: url(../../public/img/file_icon/doc.png);}
.docs-ai{background-image: url(../../public/img/file_icon/ai.png);}
.docs-avi{background-image: url(../../public/img/file_icon/avi.png);}
.docs-css{background-image: url(../../public/img/file_icon/css.png);}
.docs-csv{background-image: url(../../public/img/file_icon/csv.png);}
.docs-dbf{background-image: url(../../public/img/file_icon/dbf.png);}
.docs-exe{background-image: url(../../public/img/file_icon/exe.png);}
.docs-dwg{background-image: url(../../public/img/file_icon/dwg.png);}
.docs-file{background-image: url(../../public/img/file_icon/file.png);}
.docs-fla{background-image: url(../../public/img/file_icon/fla.png);}
.docs-html{background-image: url(../../public/img/file_icon/html.png);}
.docs-iso{background-image: url(../../public/img/file_icon/iso.png);}
.docs-javascript{background-image: url(../../public/img/file_icon/javascript.png);}
.docs-jpg{background-image: url(../../public/img/file_icon/jpg.png);}
.docs-json{background-image: url(../../public/img/file_icon/json.png);}
.docs-mp3{background-image: url(../../public/img/file_icon/mp3.png);}
.docs-mp4{background-image: url(../../public/img/file_icon/mp4.png);}
.docs-ppt{background-image: url(../../public/img/file_icon/ppt.png);}
.docs-psd{background-image: url(../../public/img/file_icon/psd.png);}
.docs-rtf{background-image: url(../../public/img/file_icon/rtf.png);}
.docs-search{background-image: url(../../public/img/file_icon/search.png);}
.docs-svg{background-image: url(../../public/img/file_icon/svg.png);}
.docs-txt{background-image: url(../../public/img/file_icon/txt.png);}
.docs-xls{background-image: url(../../public/img/file_icon/xls.png);}
.docs-xml{background-image: url(../../public/img/file_icon/xml.png);}
.docs-zip{background-image: url(../../public/img/file_icon/zip.png);}
.docs-zip-1{background-image: url(../../public/img/file_icon/zip-1.png);}
#commissions-page .table-style1,
#commissions-page .table-style1 .react-bs-container-body,
.table-style1.search-table,
.table-style1.search-table .react-bs-container-body{
    /*overflow: auto !important;*/
}
.select-cover .DayPickerInput { display: block; }
.select-cover .DayPickerInput input{ background: transparent; }
.table td .btn-color1.btn-primary {
    padding: 5px 12px;
    min-width: inherit;
}
.react-bs-table-tool-bar{ overflow:hidden; }
body .DayPicker-wrapper { padding: 2px 2px; }
.error-txt span {
    font-size: 90px;
    color: {{$color2}};
    display: block;
    padding-bottom: 28px;
    line-height: 50px;
}
.dashboard-event-cal{ max-width: 100% !important; }
.event-list{
    margin-left: 9px;
    min-height: 48px;
    margin: 12px 0;
    cursor: pointer;
    padding: 4px 12px;
    display: block;
    color: #333 !important;
    border-left: 10px solid {{$color1}}
}
.event-list span {
    font-size: 14px;
    color: #BEBEBE;
}
.feature_meeting, .past_meeting{
    color: #fff;
    border-radius: 50%;
    position: absolute;
    width: 18px;
    top: -3px;
    font-size: 10px;
    right: -1px;
    height: 18px;
    line-height: 18px;
}
.feature_meeting{ background: #e43838; }
.past_meeting{ background: #6d737e; }
.dlink{
    color: #464746;
    cursor: pointer;
    font-size: 12px;
}
.btn-primary.active.focus,.btn-primary[disabled], .btn-primary.active:focus, .btn-primary.active:hover, .btn-primary:active.focus, .btn-primary:active:focus, .btn-primary:active:hover, .open>.dropdown-toggle.btn-primary.focus, .open>.dropdown-toggle.btn-primary:focus, .open>.dropdown-toggle.btn-primary:hover {
    color: #fff;
    background-color: {{$color3}};
    border-color: {{$color3}};
}
.email-preview img{ width:100%; }
.img-preview{ max-height: 62px; }
.opentip-container,.opentip-container.ot-show-effect-appear.ot-visible{
    background: #9b922f !important;
    display: block !important;
}
.cke_combopanel{ width:200px !important; }
.cke_button__createplaceholder, .hide{ display:none !important; }
.panel-primary{ border-color: #cccccc; }
.prepd-topic-input{ text-transform: capitalize; }
#Dashboard .react-bs-table-no-data{ text-align:left; }
.buttonlink{
    background: transparent;
    border: none;
    color: #333;
    text-decoration: underline;
}
.react-confirm-alert > h3,.react-confirm-alert > h1{ font-size:13px !important; }
.agenda-actions li i, .all-agenda-topics li i{ pointer-events:none; }
.agenda-actions li, .all-agenda-topics li span{ cursor:pointer; }
.scroll-block{ overflow: auto; }
.flex-block{ display:flex; }
.fix-column{
    float: left;
    display: inline-block;
    min-width: 172px;
}

/******* Video Meeting Css *******/
.videoBlock .topbar, .videoBlock .bottombar{
    background: #23487c;
    height: 35px;
    padding: 6px 10px;
}
.videoBlock .topbar img {
    display: inline-block;
    margin: 0px 5px;
    cursor: pointer;
}
.videobox {
    height: 250px;
    background: #f5f5f5;
    border-right: 1px solid #ccc;
}
.dsearch-form label{
    font-size:13px;
}
#videoControllerSec {
    width: 350px;
    position: fixed;
    right: -350px;
    top: 0;
    z-index: 9999;
    bottom: 0;
    margin: auto;
    max-height: 555px;
    height: 440px;
}
#videoControllerSec::-webkit-scrollbar {
    width: 0px;
    background: transparent;
}
#videoControllerSec::-webkit-scrollbar-thumb {
    background: #FF0000;
}
#videoMeetingControlsBtns {
    background: #23487c;
    float: left;
    position: absolute;
    right: 100%;
    top: 0;
    bottom: 0;
    margin: auto;
    z-index: 999;
    width: 62px;
    height: 440px;
    border-left: 10px solid #0f366c;
    border-top-left-radius: 40px;
    border-bottom-left-radius: 40px;
    padding: 30px 0px;
}
#videoMeetingControlsBtns span {
    text-align: center;
    padding: 7px 5px;
    margin: 5px 0px;
    width: 100%;
    float: left;
    cursor: pointer;
}
#videoControlDiv {
    width: 100%;
    height: 100%;
    float: right;
    box-shadow: 0px 0px 12px rgba(0, 0, 0, 0.12);
    -moz-box-shadow: 0px 0px 12px rgba(0, 0, 0, 0.12);
    -webkit-box-shadow: 0px 0px 12px rgba(0, 0, 0, 0.12);
    background: #fff;
    position: relative;
    overflow: auto;
}
.videoControlAccordion .card-header {
    background: #0f366c;
    padding: 0px 15px;
    display: inline-block;
    width: 100%;
    border-bottom: 1px solid rgba(255, 255, 255, 0.50);
}
.videoControlAccordion .card-header a {
    color: #fff;
    position: relative;
    display: inline-block;
    width: 100%;
    padding-right: 20px;
}
.videoControlAccordion .card-header a::after {
    content: '\f107';
    font-family: FontAwesome;
    color: #fff;
    font-size: 22px;
    position: absolute;
    bottom: 0px;
    right: 0px;
    line-height: 18px;
}
#videoControlDiv ul {
    padding-left: 0px;
    list-style: none;
    width: 100%;
    display: inline-block;
    margin-bottom: 0px;
    float: left;
}
#videoControlDiv ul li{
    float: left;
    width: 100%;
    border-bottom: 1px solid #cdcdcd;
}
#videoControlDiv ul .count {
    background: #0f366c;
    color: #fff;
    width: 35px;
    height: 35px;
    text-align: center;
    display: inline-block;
    float: left;
    padding: 5px;
}
#videoControlDiv ul .count span {
    border: 1px solid #fff;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    display: inline-block;
    padding: 1px 1px;
}
.controlIcons {
    height: 35px;
    float: left;
    width: 120px;
}
.controlIcons img {
    padding: 5px 4px;
}
.userControlList {
    float: left;
    width: 174px !important;
    padding-left: 10px;
}
.userControlList select{
    width: 100%;
    border: none;
    height: 35px;
}
.attendanceList {
    width: 115px;
    padding: 0px 5px;
    float: left;
}
.attendanceList span {
    border: 1px solid #000;
    border-radius: 25px;
    width: 25px;
    height: 25px;
    display: inline-block;
    text-align: center;
    padding: 2px;
    margin: 5px 5px;
    color: #000;
}
.attendancUser {
    float: left;
    font-size: 16px;
    padding: 5px;
    width: 210px;
}
.chatBox{
    padding: 10px 15px;
    border-bottom: 1px solid #ccc;
}
.chatNameTime .chatName{
    display: inline-block;
    color: #0f366c;
}
.chatNameTime .chatTime{
    float: right;
    margin-left: 10px;
    color: #a4a4a4;
}
.chatArea {
    height: 180px;
    overflow: auto;
    border-bottom: 1px solid #ccc;
}
.chatNameTime{
    margin-bottom: 5px;
}
.chatForm textarea {
    width: 100%;
    padding: 5px;
    height: 50px;
    resize: none;
    border: 1px solid #d4d4d4;
    border-radius: 3px;
}
.chatForm {
    padding: 10px;
}
.sendTo select {
    width: 200px;
    margin-left: 5px;
    border: 1px solid #ccc;
    padding: 2px;
}
.sendTo button{
    margin-left: 10px;
    background: transparent url(../../public/img/send.png) no-repeat center left;
    border: none;
    padding: 5px 5px 5px 20px;
}
.userControlList select:focus, .sendTo select:focus,
.sendTo button:focus{
    outline: none;
}
.sendTo {
    display: inline-block;
    width: 100%;
    margin-top: 5px;
}
.videoControllDivBtn{
    cursor: pointer;
}
.meetingMember {
    height: 34px;
    width: 100%;
    margin: 5px 0px;
    float: left;
    padding-right: 35px;
    position: relative;
}
.meetingDates{
    position: static;
    overflow: auto;
}
.DayPicker-Day--disabled {
    pointer-events: none;
}
.app-body {
    padding-bottom: 80px;
}
.title-sec .dropdown-menu{
    max-height : 210px;
    overflow : auto;
}

/* Preparing Agenda*/
.btn-icon{
    background: transparent;
    font-size: 20px;
}
.btn-icon, .btn-icon:hover, .btn-icon:focus {
    background: #ffffff;
    font-size: 14px;
    min-width: inherit;
    margin: 0 5px;
    padding: 0px 10px;
    outline: none !important;
    box-shadow: none !important;
    margin-top: 5px;
    $color: {{$color2}}
    border: none;
}
.iconBtn{
    padding: 0px 5px;
    margin-right: 5px;
    visibility: hidden;
    display: inline-table;
    color: #9e9c9c;
    vertical-align: middle;
}
.list-drag{
    float: left;
    opacity: 0.5;
}
.list-drag, .fileUploadIcon{
    visibility: hidden;
    cursor: pointer;
    opacity: 0.5;
}
.fileUploadIcon{
    margin: 0px 5px;
}
ol.add-box-li{
    position: relative;
    padding-left: 0px;
}
ol.add-box-li li {
    padding: 7px 0px 7px 30px;
}
.new_topic_line input[type="text"] {
    padding: 3px;
    max-width: 300px;
    width: 100%;
    height: 24px;
}
input.edit_button {
    width: 350px;
    padding: 1px 5px;
    height: 24px;
    border: 1px solid #dfdfdf;
}
.new_topic_line button {
    background: {{$color2}};
    color: #fff;
    border: none;
    padding: 2px 12px;
    height: 24px;
    line-height: 18px;
}
.all-agenda-topics li .agenda_list_li:hover .iconBtn,
.all-agenda-topics li .agenda_list_li:hover .list-drag,
.allAgendaList .all-agenda-topics li .agenda_list_li:hover .fileUploadIcon{
    visibility: visible;
}
.all-agenda-topics li .agenda_list_li .iconBtn,
.all-agenda-topics li .agenda_list_li .list-drag,
.allAgendaList .all-agenda-topics li .agenda_list_li .fileUploadIcon{
    display: table-cell;
}
.allAgendaList .all-agenda-topics .list-drag {
    position: relative;
    left: 0px;
    margin-right: 5px;
    padding: 0px 5px;
}
.allAgendaList .all-agenda-topics .order-no {
    position: relative;
    left: 0;
    top: 0;
    z-index: 1;
    padding-right: 10px;
    display: table-cell;
    vertical-align: top;
}
.allAgendaList .all-agenda-topics .list_number{
    padding-right: 10px;
    margin-top: 0px;
    display: table-cell;
    vertical-align: top;
    float: left;
}
.new_topic_line { padding-left: 28px; }
.fa-3x{ font-size: 20px !important; }
.toggleTask {
    padding: 5px 0px 15px 40px;
    margin: 0px 0px 0px 0px;
}
.toggleTask::after,
.listing-open::before{
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0%;
}
/* .listing-open::before{
    -webkit-box-shadow: 0px -5px 8px -6px rgba(0, 0, 0, 0.8);
    -moz-box-shadow: 0px -5px 8px -6px rgba(0, 0, 0, 0.8);
    box-shadow: 0px -5px 8px -6px rgba(0, 0, 0, 0.8);
}
.toggleTask::after {
    -webkit-box-shadow: 0px 11px 11px -13px rgb(0, 0, 0);
    -moz-box-shadow: 0px 11px 11px -13px rgb(0, 0, 0);
    box-shadow: 0px 11px 11px -13px rgb(0, 0, 0);
} */
.toggleTaskInner{
    position: relative;
    z-index: 1;
}
.right-zero{ right:0px }
.show-add, .show-add.new{ display: inline-block; }
.show-add.new span{
    margin: 0px 5px;
    display: inline-block;
}
.show-add.new span img { height: 35px; }
.add_singleMulti_task {
    margin-right: 5px;
    display: inline-block;
    margin-top: 7px;
    vertical-align: top;
}
.add_singleMulti_task i{ font-size: 25px; }
.pagination>li{ display:inline-block !important; }
.first-level-li>li{ padding-left:10px !important }
.first-level-li .new_topic_line{ padding-left:0px !important; }
.note-blue{ color:#4F71D0 !important; }
.note-blue svg path{ fill:{{$color2}} !important; }
ol.first-level-addbox li { padding: 7px 0px 7px 10px; }
.first-level-addbox .new_topic_line{ padding-left:10px; }

.allAgendaList .all-agenda-topics li .agenda_list_li {
    padding: 5px 0px 5px 0px;
    position: relative;
}
.allAgendaList .all-agenda-topics li .topics-level1 .agenda_list_li,
.allAgendaList .all-agenda-topics li .topics-level2 .agenda_list_li,
.allAgendaList .all-agenda-topics .rc-draggable-list-draggableRow>li,
.documentAddList .topics-level1,
.listCheckdrog{
    padding-left: 0px;
}
.level_add_doc, .repd_list_level3{
    padding-left:40px;
}
.level_add_doc li span.file-icon{
    display: inline-block;
    margin-right: 5px;
}
.level_add_doc li ul.agenda-actions {
    position: relative;
    display: inline-block;
    width: 20px;
    top: 0px;
    padding: 0px 4px;
    height: 20px;
    margin-top: 8px;
}
.level_add_doc li ul.agenda-actions li{
    width: 100%;
}
.icon::before{
    font-size: 20px;
    font-weight: 600;
    color: {{$color2}};
}
.icon{
    height: 20px;
    display: inline-block;
    margin-top: 0 !important;
    cursor: pointer;
}
.iconBtn svg{
    height: 20px;
    width: 20px;
    vertical-align: middle;
    display: table-cell;
}
.agenda_list_li>strong,
.agenda_list_li>li>strong {
    display: table-cell;
    vertical-align: middle;
}
.add_ver_div svg{
    height: 20px;
    margin-bottom: 0px;
    vertical-align: middle;
}
.all-agenda-topics li span {
    display: inline-block;
    vertical-align: middle;
}
.all-agenda-topics li span.cke_top{
    width: 100%;
    padding: 5px 0px 0px 0px;
}
.all-agenda-topics li span.cke_button_label,
.all-agenda-topics li span.cke_voice_label {
    display:none !important;
}
.list-drag i {
    font-size: 20px;
    margin-left: 5px;
}
.list-drag i::before{ font-weight: normal; }
.simpleIcon svg, .pdfBtn svg{
    height: 35px;
    width: 35px;
}
.repdDoc_sign svg {
    height: 30px;
    width: 30px;
}
.add_plus_doc.repdDoc_sign svg {
    height: 26px;
    width: 26px;
}
.leftDivIcon svg,
.reunion_add svg{
    height: 20px;
    width: 20px;
}
.reunion_add {
    display: inline-block;
    margin: 0 !important;
}
.pg-content-loader svg path,  .simpleIcon svg path,
.leftDivIcon svg path, .iconBtn svg path,
.pdfBtn svg path, .repdDoc_sign svg path,
.reunion_add svg path, .cal-add svg path, .svg path,
.labelIcon svg path {
    fill: {{$color2}};
}
.repdAgenda_topic.all-agenda-topics li label span.lable-icon-pb{
    vertical-align: inherit;
    margin-right: 5px;
}
.topic-project .sub-topic>div{ display: block !important; }
.new-topic-project .form-group:first-child div{ float: left !important; }
.new-topic-project .form-group:last-child button{ float: right !important; }
.new-topic-project .form-group:nth-child(2){
    border: 1px solid #ddd !important;
    padding: 8px 12px;
}
.new-topic-project .form-group:first-child, .new-topic-project .form-group:last-child,
.new-topic-project .dropdown{
    padding-left: 0px;
    padding-right: 0px;
}
.milestoneNameBox{
    padding-left: 10px;
    padding-right: 10px;
}
.topic-project .sub-topic>div>div{
    position: inherit !important;
    cursor: pointer;
}
.topic-project .sub-topic>div>div>div{ padding: 6px; }
.colorPickerBox {
    padding: 7px;
    border-radius: 3px;
    margin-top: 8px;
}
.colorPickerBox::before,
.colorPickerBox::after{
    content: '';
    position: absolute;
    width: 8px;
    height: 8px;
    top: -8px;
}
.colorPickerBox::before {
    border-bottom: 8px solid #fdfdfd;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    z-index: 1;
    left: 11px;
}
.colorPickerBox::after {
    border-bottom: 8px solid #ccc;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    left: 10px;
}
.colorPickerBox li{
    width: 20px;
    height: 20px;
    float: left;
    margin: 2px;
    display: inline-block;
    cursor: pointer;
}
.colorPickerBox li:hover{
    -webkit-box-shadow: 1px 1px 4px rgba(154, 154, 154, 0.6);
    -moz-box-shadow: 1px 1px 4px rgba(154, 154, 154, 0.6);
    box-shadow: 1px 1px 4px rgba(154, 154, 154, 0.6);
}
.taskDataList>div div>div {
    width: 100%;
    text-align: left;
    padding: 5px 10px!important;
}
.taskDataList>div div>div:hover{
    background-color: #f5f5f5 !important;
}
.taskDataList>div div .btn:hover,
.taskDataList>div div .btn {
    padding: 0px !important;
    background-color: transparent !important;
}
.taskDataList>div>input + div {
    border: 1px solid #d8d6d6;
    border-top: none;
    max-height: 150px !important;
    overflow: auto;
}
.multiple_list_sec .Select--multi { padding: 0px; }
.all-agenda-topics li .multiple_list_sec .Select--multi span{ display: table-cell !important; }
.divLeft_space35 {
    padding-left: 35px;
    position: relative;
}
.toggleTaskInner .leftDivIcon{
    position: absolute !important;
    left: 0;
    top: 12px;
    display: inline-block;
}
ul.agenda-actions>li svg {
    width: 16px;
    height: 16px;
    display: inline-block;
    vertical-align: middle;
}

/* New CSS */
.dropdown.drop-style1.dropDown_plus {
    padding-left: 35px;
    position: relative;
}
.DayPicker-NavButton {
    width: 20px !important;
    height: 20px !important;
    background-position: center !important;
    background-size: 8px 12px !important;
    background-color: #fff;
    border-radius: 50%;
    margin-top: -3px;
    top: 5px !important;
    background: transparent !important;
    position: relative;
}
.default-form.discussion-form{ padding-bottom: 0px; }
.multiple_list_sec .Select--multi .Select-control .Select-input { width: 35% !important; }
.multiple_list_sec .Select--multi .Select-control .Select-input>input{
    width: 100% !important;
    height: 30px !important;
    padding: 2px !important;
}
.delIconBtn svg{
    height: 13px;
    width: 13px;
}
.dropdown.drop-style1.dropDown_plus{ padding-left: 35px; }
.dropdown.drop-style1.dropDown_plus>a, .drop-style1 .dropdown-toggle>i {
    position: absolute;
    top: 0;
}
.dropdown.drop-style1.dropDown_plus>a { left: 0; }
.drop-style1 .dropdown-toggle>i { right: 0; }
.drop-style1 .dropdown-toggle{
    text-align: left;
    padding-right: 15px;
    position: relative;
    overflow: hidden;
    max-width: 100%;
    vertical-align: middle;
    display: inline-block;
}
.taskDataList div > h5 .btn {
    padding: 0px !important;
    margin-left: 10px;
}
.autocomplete-form .divLeft_space35 .date-field div {
    padding: 0px;
}
.autocomplete-form .multiple_list_sec input:focus {
    box-shadow: none;
    border-color: transparent;
    outline: 0px;
}
.multiple_list_sec span.Select-clear-zone {
    position: absolute;
    right: 20px;
    margin-right: 0px !important;
    text-align: center !important;
    width: 20px !important;
    padding: 0px 5px 0px 5px;
    height: 20px;
    top: 8px;
}
.multiple_list_sec span.Select-arrow-zone {
    position: absolute;
    right: 0;
    width: 20px;
    margin: 0 !important;
    top: 15px;
    padding: 0px 4px;
}
.multiple_list_sec .Select--multi .Select-multi-value-wrapper {
    display: inline-block;
    padding-right: 40px;
    margin-right: 0px;
    width : 100%;
}
.level_add_doc span { vertical-align: top !important; }
.agendaHeadDiv {
    padding-bottom: 10px;
    position: relative;
    padding-right: 70px;
}
.all-agenda-topics.repdAgenda_topic>li:first-child { border-top: none !important; }
.all-agenda-topics .rc-draggable-list-draggableRow:first-child>li{ border-top: none; }
.all-agenda-topics.repdAgenda_topic li.repd-li>strong {
    margin-top: 5px;
    display: inline-block;
    padding-left: 40px;
}
.agendaHeadDiv .text-left.redacteur { margin-top: 11px; }
.agendaHeadDiv .redacteur label { margin-top: 5px; }
.agenda-heading-action ul.agenda-actions {
    top: 14px;
    right: 0px;
}
.level_add_doc li ul.agenda-actions li, #project { padding: 0; }
.all-agenda-topics li .agenda_list_li.editModeOn:hover .iconBtn,
.allAgendaList .all-agenda-topics li .agenda_list_li.editModeOn:hover .fileUploadIcon,
.all-agenda-topics li .agenda_list_li.editModeOn .iconBtn{
    display: none;
}
.all-agenda-topics li .agenda_list_li.editModeOn:hover .list-drag{ visibility: hidden; }
.opacity-50{
    opacity: 0.5;
    filter: alpha(opacity=50);
}
.cursor-none{ pointer-events: none; }
.all-agenda-topics ol.topics-level1 span.all-agenda-topics {
    margin-top: 10px;
    margin-left:-40px;
}
.has-error .date-field input,
.has-error section.sub-topic.taskDataList input {
    border-color: #a94442;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
}
.all-agenda-topics.repdAgenda_topic li.repd-li>strong>span.order-no{
    position: absolute;
    left: 0;
    top: 10px;
}
.all-agenda-topics ol.topics-level1 span.all-agenda-topics+li { margin-left: -68px; }
.all-agenda-topics ol.topics-level1 span.all-agenda-topics+li span.list_number{ float: left; }
.all-agenda-topics ol.topics-level1 span.all-agenda-topics+li .addFirstTopic { display: list-item; }
.all-agenda-topics ol.topics-level1 span.all-agenda-topics+li .addFirstTopic input,
.all-agenda-topics ol.topics-level1 span.all-agenda-topics+li .addFirstTopic button{
    display: table-cell;
}
.all-agenda-topics ol.topics-level1 span.all-agenda-topics + li .new_topic_line .addFirstTopic input,
.all-agenda-topics ol.topics-level1 span.all-agenda-topics + li .new_topic_line .addFirstTopic button {
    display: inline-block;
    height: 26px;
    vertical-align: middle;
    line-height: normal;
}
.all-agenda-topics ol.topics-level1 span.all-agenda-topics + li .new_topic_line .addFirstTopic input,
.all-agenda-topics ol.topics-level1 span.all-agenda-topics + li .new_topic_line .addFirstTopic input:focus{
    border: 1px solid #eee !important;
}
.taskStatusBtn span.label {
    padding: 7px 20px 7px 8px;
    border-radius: 2px;
    width: 120px !important;
    display: inline-block;
    line-height: 14px;
    white-space: inherit;
    position: relative;
    text-align: left;
}
.taskStatusBtn span.label i {
    font-weight: 400;
    font-size: 10px;
    position: absolute;
    right: 5px;
    height: 10px;
    width: 10px;
    top: 0;
    bottom: 3px;
    margin: auto;
}
.taskStatusBtn ul.dropdown-menu {
    padding: 0px;
    background: #ffffff;
    box-shadow: 1px 3px 10px rgba(0, 0, 0, 0.06);
    top: 100%;
    max-width: 120px;
    min-width: 120px;
    left: 0 !important;
    margin: auto;
}
.taskStatusBtn ul.dropdown-menu li {
    border-bottom: 1px solid #e2e1e1;
    padding: 5px 10px;
    display: inline-block;
    width: 100%;
    font-size: 12px;
    cursor: pointer;
}
.taskStatusBtn ul.dropdown-menu li:hover { background: #f3f3f3; }
.listControlIcons{
    display: table-cell;
    width: 160px;
}
.addFirstTopic { display: list-item; }
.allAgendaList.prepdDateList ul.agenda-actions{ right: -76px; }
.repd-list-head{
    margin-bottom: 10px;
}
.toggleTaskInner h4.task-form-title { padding: 5px 0px; }
.allAgendaList.prepdDateList .level_add_doc ul.agenda-actions { right: 0; }
.all-agenda-topics.clearfix.repdAgenda_topic .repd-list-head { padding-right: 90px; }
.all-agenda-topics.repdAgenda_topic>li.repd-li span.order-no {
    position: absolute;
    left: 0;
    top: 5px;
}
.all-agenda-topics.clearfix.repdAgenda_topic .repd-list-head {
    padding-right: 80px;
    padding-left: 40px;
}
.uploadProfile-img {
    height: 120px;
    width: 120px;
    background-size: 95% !important;
    background-position: center !important;
    position: relative;
    background-position-x: center !important;
    background-repeat: no-repeat !important;
    border: 1px solid #e5e4e5;
}
.form-group span.text-danger.span-inline{
    display: inline-block;
    padding-left: 10px;
}
#editMsgModal .modal-footer,
#replyMsgModal .modal-footer{
    padding: 10px 35px;
}
.pb-container{ margin-top:20px; }
.form-group.daycal-time label { min-width: 70px; }
.editTxt.collapse.in {
    display: inline-block;
    width: 100%;
}
.footer-btns .pb-container { margin-top: 0px; }
.default-form .pb-container{ vertical-align: bottom; }
.uploadePicCrop {
    position: relative;
    display: inline-block;
    border-radius: 5px;
}
span.crossBtn {
    width: 20px;
    height: 20px;
    display: inline-block;
    text-align: center;
    line-height: 19px;
    background: {{$color2}};
    border-radius: 20px;
    font-size: 12px;
    position: absolute;
    top: -6px;
    right: -6px;
    cursor: pointer;
    color: #fff;
}
#main-menu ul>li ul.dropdown-menu.bg-70 li{ background-color: {{$transprancy7}}; }
.userPicName {
    background: {{$transprancy7}};
    border: 1px solid {{$color2}};
    text-align: center;
    color: #fff;
    text-transform: uppercase;
    font-weight: bold;
    font-size: 30px;
    padding: 5px 2px;
    display: table-cell !important;
    border-radius: 50%;
}
.userImgUploadBox{
    background: {{$transprancy7}};
    border: 1px solid {{$color2}};
    padding: 35px 15px 25px 15px;
    min-height: 150px;
    margin-bottom: 20px;
}
.userFullName{
    display: inline-block;
    width: 100%;
    padding: 0px;
}
.cal-add {
    display: inline-block;
    position: relative;
}
.cal-add .react-add-to-calendar__dropdown {
    z-index: 1;
    background-color: white;
    border: 1px solid rgba(215, 215, 215, 0.38);
    box-shadow: 2px 3px 6px -4px rgba(0, 0, 0, 0.34);
    -webkit-box-shadow: 2px 3px 6px -4px rgba(0, 0, 0, 0.34);
    -moz-box-shadow: 2px 3px 6px -4px rgba(0, 0, 0, 0.34);
    position: absolute;
    left: 0px;
    width: 140px;
    margin-top:10px;
}
.cal-add .react-add-to-calendar__dropdown::before,
.cal-add .react-add-to-calendar__dropdown::after{
    content: '';
    position: absolute;
    height: 8px;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    top: -8px;
}
.cal-add .react-add-to-calendar__dropdown::before {
    width: 10px;
    border-bottom: 8px solid rgb(215, 215, 215);
    left: 10px;
}
.cal-add .react-add-to-calendar__dropdown::after {
    width: 8px;
    border-bottom: 8px solid #fff;
    left: 11px;
}
.cal-add .react-add-to-calendar__dropdown ul {
    padding-left: 0;
    margin-bottom: 0;
    text-align: left;
}
.cal-add .react-add-to-calendar__dropdown ul li {
    list-style: none;
}
.cal-add .react-add-to-calendar__dropdown ul li a{
    padding: 4px 10px;
    display: block;
}
.cal-add .react-add-to-calendar__dropdown ul li:hover a{
    background: {{$transprancy7}};
    color: #fff;
    text-decoration: none;
}
.cal-add .react-add-to-calendar__dropdown ul li i{
    margin-right: 8px;
}
.cal-add a.react-add-to-calendar__button span i {
    font-size: 35px;
    color: {{$color2}};
}
.cal-add a.react-add-to-calendar__button, .cal-add a.react-add-to-calendar__button span {
    display: inline-block;
    height: 45px;
    vertical-align: middle;
    cursor: pointer;
    fill: {{$color2}};
}
.listCheckdrog li {
    list-style: none;
    position: relative;
    margin: 10px 0px;
    background: {{$transprancy7}};
    padding: 10px 10px 10px 55px;
    -webkit-box-shadow: 0px 7px 10px -8px rgba(0, 0, 0, 0.1);
    -moz-box-shadow: 0px 7px 10px -8px rgba(0, 0, 0, 0.1);
    box-shadow: 0px 7px 10px -8px rgba(0, 0, 0, 0.1);
    cursor: pointer;
}
.listCheckdrog li .checkbox {
    position: absolute;
    left: 30px;
    top: 10px;
    margin: 0;
}
.listCheckdrog li .checkbox input[type="checkbox"]{
    width: 16px;
    height: 16px;
}
.listCheckdrog li span {
    font-size: 16px;
    text-transform: uppercase;
}
.optionlist-drag {
    position: absolute;
    left: 10px;
    top: 10px;
}
.listCheckdrog li .checkbox label::before {
    background-color: rgba(255, 255, 255, 0.50);
}
.cal-add svg {
    width: 35px;
    height: 35px;
    vertical-align: middle;
}
.cal-add a.react-add-to-calendar__button, .cal-add a.react-add-to-calendar__button span {
    display: inline-block;
    height: 35px;
    width: 35px;
    vertical-align: middle;
    cursor: pointer;
    fill: #3c4757;
}
#user-info-inner #user-img {
    left: 0;
    top: 0;
    position: absolute;
    width: 35px;
    height: 35px;
    font-size: 12px;
    line-height: 22px;
    background-color: {{$transprancy7}};
    border: 1px solid #e1e1e1;
}
.DayPicker-Day.DayPicker-Day--selected.DayPicker-Day--disabled,
.DayPicker-Day--selected:not(.DayPicker-Day--disabled):not(.DayPicker-Day--outside){
    background: {{$color2}} !important;
    color: #fff;
}
.single-msg .userPicName, .single-msg .user-img,
.reply-item-box .user-img, .reply-item-box .userPicName {
    height: 30px !important;
    width: 30px !important;
    display: block;
    position: absolute;
    top: 4px;
    left: 0;
    border-radius: 50%;
    font-size: 10px;
    padding: 3px 3px;
    line-height: 24px;
    font-weight: 400;
    letter-spacing: 1px;
}
.decision{
    break-inside: avoid;
    page-break-inside: avoid;
    -webkit-column-break-inside: avoid;
    -moz-column-break-inside: avoid;
}
.searchBtn {
    border: none;
    width: 50px;
    font-size: 20px;
}
.dropdown-menu li button {
    width: 100%;
    text-align: left;
}
.verticalTxtTop table>tbody>tr>td,
.externalTopicDoc .pb-container .pb-button span,
.card-details-block .input-group-addon, .card-details-block .input-group-btn,
.input-btn-field .input-group-btn,
.input-group span.input-group-btn,
.input-group-btn{
    vertical-align: top;
}
.mobileEnableDiv {
    margin: 50px auto;
    max-width: 350px;
    border: 1px solid;
    background: #f6f6f6;
}
.mobileEnableDiv .innerDiv {
    background: {{$color1}};
    padding: 10px 20px;
    color: #fff;
}
.loginMailCode{
    font-size: 14px;
    margin: 5px 0px;
    display: inline-block;
    width: 100%;
}
.loginMailCode span{
    width: 90px;
    float: left;
}
.appStoreImg{
    text-align: center;
    margin: 5px 3px;
}
.mobileEnableDiv h4{
    font-size: 16px;
}
.mobileEnableDiv .innerDiv h4 {
    margin-bottom: 20px;
    margin-top: 10px;
}
.appStoreImg img{
    display: inline-block;
    margin: 0px 5px;
    width : 150px;
}
.table-childdiv .linkBtn {
    width: 45%;
    float: right;
    padding-left: 10px;
    position: relative;
    font-size: 12px;
    margin-top: -18px;
}
.table-childdiv .l_content{
    width: 60%;
    float: left;
}
.table-childdiv .linkBtn span i {
    font-size: 18px;
    position: absolute;
    left: 0;
    top: 0px;
}
.start-listing ul li {
    margin: 5px 0px;
}
.start-listing ul li button.btn-link:focus {
    outline: none !important;
    box-shadow: none !important;
    border-color: transparent !important;
}
.message-bar{
    background : {{$color1}};
    padding: 3px 0px;
    position: absolute;
    width: 100%;
    bottom: 30px;
    min-height: 36px;
}
span.chatBtn {
    background: #fff;
    width: 50px;
    display: inherit;
    border-radius: 15px;
    font-size: 20px;
    color: {{$color2}};
    border:1px solid {{$color2}};
    text-align: center;
    height: 30px;
    padding: 5px 5px;
    line-height: 12px;
    cursor: pointer;
    float: left;
}
.chat-form{
    display: inline-block;
    float: left;
    margin-left: 15px;
    width: 100%;
    max-width: 600px;
}
.chat-form input{
    margin: 0px;
    padding: 3px 5px;
    color: {{$color2}};
    width: 40%;
    height: 30px;
    border: 1px solid {{$color2}};
    float: left;
}
.chat-form input:focus,
.chat-form button:focus{
    outline: none;
    -webkit-outline: none;
}
.chat-form button{
    background: {{$color2}};
    border: none;
    padding: 5px 15px;
    border-radius: 0px !important;
    margin-left: 8px;
}
.chat-form .Select.is-clearable.is-searchable.Select--single {
    width: 40%;
    height: 30px;
    float: left;
}
.chat-form .Select.is-clearable.is-searchable.Select--single .Select-control {
    background-color: #fff;
    border-radius: 0px;
    border: 1px solid {{$color2}};
    color: {{$color2}};
    height: 30px;
}
.chat-form .Select-placeholder, .chat-form .Select--single > .Select-control .Select-value {
    line-height: 28px !important;
    height: 30px;
    font-size: 12px;
}
.chat-form .Select.is-clearable.is-searchable.Select--single .Select-control .Select-multi-value-wrapper .Select-input {
    height: 28px !important;
    padding-left: 10px;
    padding-right: 10px;
    vertical-align: middle;
}
.chat-form .Select-menu-outer{
    line-height: 14px !important;
    font-size: 12px;
    top: 2px;
}
.chat-form .Select.is-clearable.is-searchable.Select--single .Select-input > input{
    padding: 0px 0px 0px;
    font-size: 14px;
    height: 30px;
}
#setting-icons .badge {
    display: inline-block;
    min-width: 24px;
    padding: 3px 3px;
    font-size: 12px;
    font-weight: bold;
    color: #fff;
    line-height: 1;
    vertical-align: text-top;
    white-space: nowrap;
    text-align: center;
    background-color: #dd0031;
    border-radius: 20px;
    min-height: 24px;
    text-align: center;
    line-height: 16px;
}
.agenda-inner-content p {
    white-space: pre-wrap;
}
#inscreption #upload .modal-header{
    padding: 20px 15px;
    border-bottom: 1px solid #e5e5e5;
    background: #4272a5;
}
#inscreption #upload .modal-header .close{
    margin-top: -10px;
}
.reminderTimer{
    display: inline-block;
    margin-left:40px;
    width: 100%;
}
.reminderTimer select:not(:last-child) {
    width: 88px;
}
.reminderTimer select{
    font-size: 12px;
    margin: 0px 3px;
    padding: 3px 0px;
}
.chat-form .pb-container{
    padding: 0px;
    width: auto;
    min-width: inherit;
    max-width: inherit;
    margin-top: 0px;
    border: none;
}
.chat-form .pb-container .pb-button {
    padding: 5px 10px;
    height: inherit;
    margin: 0;
    height: 30px;
}
.chat-form .pb-container.loading .pb-button {
    width: 26px;
    height: 26px;
    border-width: 3px;
}
.chat-form .pb-container .pb-button svg {
    height: 26px;
    width: 26px;
    left: 10px;
    top: 10px;
}
.chat-form .pb-container .pb-button svg.pb-progress-circle path {
    stroke-width: 3px;
    white-space: pre-wrap;
}
.table-childdiv>div>span {
    width: 55%;
    float: left;
}
.mobileEnableDiv {
    margin: 50px auto;
    max-width: 450px;
}
.mobileEnableDiv .innerDiv {
    background: {{$color1}};
    padding: 10px 25px;
    color: #fff;
    border-radius: 5px;
}
.loginMailCode{
    font-size: 18px;
    margin: 5px 0px;
    display: inline-block;
    width: 100%;
}
.loginMailCode span{
    width: 99px;
    float: left;
}
.appStoreImg{
    text-align: center;
    margin-top: 25px;
}
.mobileEnableDiv h2{ font-size: 24px; }
.mobileEnableDiv .innerDiv h3 {
    margin-bottom: 20px;
    margin-top: 10px;
}
.appStoreImg img{
    display: inline-block;
    margin: 0px 10px;
    width : 150px;
}
.footer-btns span.report-view{
    padding: 5px 0px;
    display: block;
    text-align: left;
}
.footer-btns .btn-icon.wifi-view,.footer-btns .btn-icon.wifi-view:hover,.footer-btns .btn-icon.wifi-view:focus{
    margin: 0 0px;
    padding: 0px 7px 0px 0px;
    margin-top: 0px;
}
.row>.pb-container{ padding: 7px 0px; }
.pdfGrayBtn svg path{
    fill: grey !important;
}
.cke_chrome .cke_bottom{ display:none !important; }

/*project style module*/
/*========= Start Select Tast Radio Buttons ==========*/
.task-type-select {
    max-width: 100%;
    white-space: nowrap;
    overflow-x: auto;
    padding: 0px 0px;
}
.tast-radiobox {
    display: inline-block;
    padding :0px !important;
}
.task-type-select label span{
    display: inline-block;
}
.radio-icons{
    padding: 5px;
    border: 1px solid #e9e9e9;
    cursor: pointer;
}
.radio-icons svg{
    width: 20px !important;
    height: 20px !important;
    vertical-align: middle;
}
.radio-icons svg path { fill: #6b6b6b !important; }
.task-type-select label input[type="radio"] { display: none; }
.task-type-select input[type="radio"]:checked + .radio-icons,
.task-type-select .tast-radiobox.active input[type="radio"] + .radio-icons{
    border-color: {{$color2}};
}
.task-type-select input[type="radio"]:checked + .radio-icons svg path,
.task-type-select .tast-radiobox.active input[type="radio"] + .radio-icons svg path{
    fill: {{$color2}};
}
/*========= End Select Tast Radio Buttons ==========*/

/*========= Start Project Milestones ==========*/
.milestone-titles {
    width: 100%;
    overflow: hidden;
    white-space: nowrap;
    position: relative;
    margin: 15px 0px;
}
.milestone-title-box {
    display: table-cell;
    padding: 0px 3px;
    position: relative;
}
.milestone-title-inner{
    width: 211px;
    padding: 9px 10px;
    border-bottom: 2px solid #ccc;
    background-color: #fff;
    border-radius: 3px 3px 0px 0px;
}
.milestone-title-dots{
    width: 100%;
    display: inline-block;
    padding-right: 22px;
    position: relative;
}
.milestone-heading{
    position: relative;
    color: #000;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: pointer;
}
.milestone-heading h4 {
    font-size: 14px;
    margin: 0;
    line-height: 20px;
    display: inline-block;
}
.milestone-heading span{
    line-height: 20px;
}
.milestone-heading span.proname{
    font-weight: bold;
}
.milestone-title-box .info{
    position: absolute;
    right: -10px;
    top: 0px;
    opacity: 0;
    visibility: hidden;
    -webkit-transition: all ease-in-out 200ms;
    -moz-transition: all ease-in-out 200ms;
    -ms-transition: all ease-in-out 200ms;
    -o-transition: all ease-in-out 200ms;
    transition: all ease-in-out 200ms;
    background-color: transparent;
}
.milestone-title-box .info button{
    padding: 0px 10px;
    background: transparent;
    border: none;
    position: absolute;
    right: 0px;
    top: 8px;
}
.milestone-title-box .info img{ height: 15px; }
.milestone-title-box .info .dropdown-menu {
    right: 0;
    left: auto;
    width: 100%;
    min-width: initial;
    white-space: normal;
    border-radius: 0;
    padding: 2px 2px;
    font-size: 10px;
    text-align: center;
    position: relative;
    border-color: #f4f4f4;
    margin-top: 0;
    box-shadow: none;
}
.milestone-title-box .info .dropdown-menu::before,
.milestone-title-box .info .dropdown-menu::after {
    content: '';
    position: absolute;
    width: 6px;
    height: 6px;
    border-left: 3px solid transparent;
    border-right: 3px solid transparent;
    right: 7px;
}
.milestone-title-box .info .dropdown-menu::before {
    border-bottom: 6px solid #d6d6d6;
    top: -7px;
}
.milestone-title-box .info .dropdown-menu::after {
    border-bottom: 6px solid #fff;
    top: -6px;
}
.milestone-title-box .info .dropdown-menu .svgicon{
    margin: 5px 5px;
    cursor: pointer;
}
.milestone-title-box .info { position: static; }
.milestone-donedue-status { min-height: 16px; }
.milestone-donedue-status .status {
    border: 1px solid #c7c7c7;
    float: left;
    width: 12px;
    height: 12px;
    margin: 2px;
}
.milestone-donedue-status .done{ background-color: #ccc; }
.milestone-donedue-status .due{ background-color: #fff; }
.milestone-title-box.active .milestone-heading span.proname,
.milestone-title-box.active .milestone-heading{
    color: {{$color2}};
}
.milestone-title-box.active,
.milestone-title-box.active .milestone-title-inner,
.milestone-title-box.active .milestone-donedue-status .status{
    border-color: {{$color2}};
}
.milestone-title-box.active .milestone-donedue-status .done{ background-color: {{$color2}} }
.milestone-title-box.active .info{
    opacity: 1;
    visibility: visible;
}
.addnew-mileston,.addnew-mileston:hover,.addnew-mileston:focus {
    cursor: pointer;
    vertical-align: top;
    margin-top: 15px;
    width: 26px;
    height: 26px;
    position: absolute;
    right: 0;
    top: 20px;
    border: 2px solid #c7c7c7;
    border-radius: 50%;
    align-items: center;
}
.addnew-mileston-btn {
    margin: auto;
    align-self: center;
    align-content: center;
}
.addnew-mileston i {
    color: #adadad;
    font-size: 14px;
}
.milestone-content {
    background-color: #f7f7f7;
    padding: 20px;
}
.milestone-content .row{
    font-size: 0;
    white-space: nowrap;
    overflow: auto;
}
.milestone-content-child-box{
    min-height: 150px;
    display: table-cell;
    font-size: 14px;
    float: none;
    min-width: 376px !important;
    max-width: 376px !important;
    width: 376px !important;
    white-space: normal;
    background-color: transparent !important;
    max-height: 100% !important;
    height: 100% !important;
    margin: 0px !important;
    border-radius: 0 !important;
    padding: 0px !important;
}
.smooth-dnd-container span:not(:last-child) .milestone-content-child-box,
.milestone-content-child-box:not(:last-child) {
    border-right: 1px solid #ffffff;
}
.milestone-content-child-box header {
    border-bottom: 0px solid #ccc;
}
.milestone-content-child-box header h4{
    font-size: 16px;
    font-weight: bold;
}
.cards-outer { padding: 10px 0px; }
.card-box {
    background: #fff;
    padding: 10px;
    min-height: 50px;
    float: left;
    width: 100%;
}
.card-box.flexbox{
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
}
.card-box.proj-card{ padding: 0px; }
.proj-card .left {
    max-width: 65px;
    min-width: 65px;
    background-color: #f9f9f9;
    border-right: 0;
    position: relative;
}
.proj-card .left .left-inner {
    width: 45px;
    height: 100%;
    padding: 8px 4px;
    float: left;
}
.proj-card.active {
    border-left-color: #ce0606;
}
.proj-card .right {
    background: #fff;
    width: 100%;
    width: -webkit-calc(100% - 55px);
    width: -moz-calc(100% - 55px);
    width: calc(100% - 55px);
    padding: 10px 0px;
    position: relative;
    border-radius: 0px 3px 3px 0px;
}
.cardbox-inner{
    border-width: 1px 1px 1px 0px;
    border-style: solid;
    border-color: #e8e8e8;
    border-radius: 3px;
}
.selected-task-icon {
    margin-left: 5px;
    margin-top: 5px;
    display: inline-block;
    cursor: pointer;
}
.selected-task-icon svg,
.task-type-select .svgicon svg,
.comment-file-btns .svgicon svg {
    width: 25px;
    height: 25px;
}
.svgicon {
    display: inline-block;
}
.svgicon svg{
    width: 24px;
    height: 24px;
    vertical-align: middle;
}
.proj-card .left .user_icon {
    width: 35px;
    height: 35px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 25px;
    margin-bottom: 10px;
    background-repeat: no-repeat;
    background-position: center top;
    background-size: cover;
    padding:3px;
    float: left;
    cursor: pointer;
}
.task-name {
    width: 100%;
    display: inline-block;
    padding-right: 30px;
    position: relative;
    padding-left: 10px;
}
.task-name h5{
    margin: 5px 0px;
    min-height: 20px;
    font-size: 14px;
}
.date-notification {
    display: inline-block;
    width: 100%;
    margin: 10px 0px;
    text-align: right;
    padding: 0px 10px;
}
.date-notification .calendar,
.date-notification .date {
    margin-right: 7px;
    display: inline-block;
    vertical-align: middle;
    font-size: 13px;
    cursor:pointer;
}
.comment-file-btns{
    display: inline-block;
    cursor: pointer;
}
.rounded{ border-radius: 100%; }
.comment-file-btns .btn-type1{
    color: #fff;
    font-size: 14px;
    width: 30px;
    height: 30px;
    display: inline-block;
    text-align: center;
    margin-left: 5px;
    margin-right: 7px;
    position: relative;
    vertical-align: middle;
}
.comment-file-btns .btn-type1 span {
    padding: 1px 2px;
    position: absolute;
    min-width: 18px;
    min-height: 18px;
    text-align: center;
    border: 1px solid #9d9d9d;
    border-radius: 20px;
    color: #9d9d9d;
    bottom: -2px;
    right: -7px;
    line-height: 14px;
    background: rgb(255, 255, 255);
    font-size: 11px;
}
.card-box .cardinnerheader .activeTab .comment-file-btns .btn-type1 span {
    border-color: {{$color1}};
    color: {{$color1}};
}
.depend-task-btn {
    display: inline-block;
    width: 100%;
}
.task-dependdiv {
    padding: 3px 0;
    border-radius: 5px;
    display: inline-block;
    float: left;
    cursor: pointer;
}
.depend-lbtn{
    margin-left: 5px;
    float: left;
}
.depend-rbtn{
    margin-right: 5px;
    float: right;
}
.delete-workshop {
    background: transparent;
    font-size: 16px;
    color: green;
    position: absolute;
    right: 10px;
    top: 10px;
    width: 18px;
    height: 18px;
    line-height: 20px;
}
.delete-workshop img { height: 20px; }
#task-change {
    margin-top: 5px;
    display: none;
}

/*Comment Form*/
#comments-block, #doc-link-block{
    display: none;
}
.comment-box-inner {
    position: relative;
    min-height: 50px;
}
#task-comment-form textarea {
    border: 1px solid #eaeaea;
    width: 100%;
    resize: none;
    height: 55px;
    padding: 5px;
}
#task-comment-form button {
    background: {{$color2}};
    color: #fff;
    font-size: 13px;
    padding: 3px 15px;
    float: right;
    border-radius: 2px;
    border: none;
}
.comments-outer {
    max-height: 230px;
    overflow: auto;
}
.comment-box-inner .comment-no {
    position: absolute;
    left: 0;
    top: 0;
}
.commenter-img, .avtar-img {
    width: 35px;
    height: 35px;
    background-size: contain;
    border-radius: 25px;
    border: 1px solid #ccc;
    background-repeat: no-repeat;
    background-position: top center;
}
#task-comment-form {
    display: inline-block;
    width: 100%;
    margin-bottom: 15px;
    padding: 0px !important;
}
.single-comment{
    border-top: 1px solid #f6f6f6;
    padding: 5px 0px;
}
.commenter-img-sec { min-width: 50px !important; }
.comment-data{ width: 100%; }
.comment-data .commenter, .comment-data .date {
    color: #b3b3b3;
    font-size: 14px;
    width: 50%;
    float: left;
}
.comment-data .text{
    margin-top: 2px;
    width: 100%;
}
.atteched-files-block{
    width: 100%;
    float: left;
    margin: 5px 0px;
}
.file-icon-box {
    width: 40px;
    margin-right: 5px;
}
.file-name-link {
    width: 100%;
    margin: auto;
    color: #888888;
    font-size: 13px;
    width: 100%;
    display: inline-block;
}
.default-form-style {
    display: inline-block;
    width: 100%;
    margin: 10px 0px;
    padding: 0px 10px;
}
.default-form-style input.form-control,
.default-form-style textarea {
    box-shadow: none;
    border-radius: 2px;
    height: 34px;
    padding: 2px 10px;
}
.default-form-style textarea{
    height: 55px;
    border: 1px solid #ccc;
    width: 100%;
    resize: none;
}
.default-form-style label{
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 0px;
}
.default-form-style .form-group{
    margin-bottom: 5px;
}
.default-form-style button[type="submit"]{
    background: {{$color2}};
    color: #fff;
    font-size: 13px;
    padding: 3px 15px;
    float: right;
    border-radius: 2px;
    border: 1px solid {{$color2}};
    outline: none;
}

/* Modal CSS */
.modal-style1 .modal-header {
    padding: 8px 15px;
    background: {{$color2}};
    color: #fff;
}
.modal-style1 .modal-title { font-size: 18px; }
.modal-style1 .modal-body{ min-height: 250px; }
.modal-style1 .modal-header{
    position: relative;
    padding-right: 50px;
}
.modal-style1 .modal-header .close {
    margin-top: 0;
    position: absolute;
    top: 7px;
    right: 10px;
    opacity: 1;
    color: #fff;
    font-weight: normal;
    font-size: 24px;
    width: 24px;
    height: 24px;
    line-height: 24px;
}
.mCSB_horizontal.mCSB_inside>.mCSB_container {
    margin-right: 0;
    margin-bottom: 15px;
}
#task-dependon-task,#task-dependon-multitask,#ownerchange{ display: none; }
.comment-box-inner h5.title {
    font-weight: 400;
    font-size: 13px;
    color: #888888;
    margin-bottom: 0px;
    padding-bottom: 8px;
}
#task-dependon-task .comments-outer,
#task-dependon-multitask .comments-outer{ margin-bottom: 15px; }
.mCSB_scrollTools {
    opacity: .30;
    filter: "alpha(opacity=30)";
    -ms-filter: "alpha(opacity=30)";
}
#ownerchange h5 {
    font-size: 16px;
    font-weight: 700;
}
.taskowners ul li {
    font-size: 14px;
    width: 100%;
    padding:  3px 25px 3px 0px;
    position: relative;
    list-style: none;
}
.taskowners ul li span.remove {
    cursor: pointer;
    font-weight: 700;
    line-height: 20px;
    height: 20px;
    position: absolute;
    right: 0;
    text-align: center;
    top: 3px;
    width: 20px;
}
.txt-and-icons{
    font-size: 0px;
    margin: 10px 0px;
    display: inline-block;
    width: 100%;
    cursor: pointer;
}
.txt-and-icons p {
    font-size: 14px;
    font-weight: 700;
    display: inline-block;
    padding-right: 10px;
    width: 55%;
    vertical-align: middle;
    margin-bottom: 0px;
}
.txt-and-icons .icons{
    width: 45%;
    display: inline-block;
    vertical-align: middle;
}
.txt-and-icons .svgicon {
    display: inline-block;
    vertical-align: middle;
    margin: 0px 2px;
}
#ownerchangebtn{ cursor: pointer; }
.add-task-type{ margin: 10px 0px; }
.remove-exiting-box {
    padding-right: 30px;
    position: relative;
}
.remove-exiting-box .removeBtn{
    font-size: 16px;
    position: absolute;
    font-weight: 400;
    right: 0;
    top: 0px;
    width: 25px;
    height: 30px;
    background: transparent;
    color: {{$color1}};
    border: none;
    text-transform: lowercase;
}
.remove-exiting-box .removeBtn img{ height: 20px; }
.multiusericon{
    -webkit-box-shadow: 3px 0px 0px rgba(22, 86, 142, 0.6);
    -moz-box-shadow: 3px 0px 0px rgba(22, 86, 142, 0.6);
    box-shadow: 3px 0px 0px rgba(22, 86, 142, 0.6);
    position: relative;
}
.multiusericon-outer {
    position: absolute;
    max-width: 300px;
    z-index: 1;
    left: 30px;
    top: -2px;
    padding-left: 10px;
    width: 300px;
    max-height: 100px;
    overflow: auto;
    display: none;
}
.multiusericon:hover .multiusericon-outer{ display: block; }
.multipleuser_imgs {
    background: #fff;
    overflow: hidden;
    font-size: 0px;
    width: 100%;
}
.multipleuser_imgs .user_icon {
    display: inline-block;
    margin: 3px;
}
.modal.fade { background: rgba(0, 0, 0, 0.5); }
.card-info-tabs { padding: 10px; }
.cardinnerheader {
    background: #fff;
    display: table;
    width: 100%;
}
.cardinnerheader>div.cardsTab{
    display: table-cell;
    vertical-align: middle;
    text-align: center;
    cursor: pointer;
    padding: 5px 5px;
    border-bottom: 2px solid #e1e1e1;
}
.cardinnerheader>div.activeTab.cardsTab {
    background: #fff;
    border-color: {{$color1}};
}
.card-box .cardinnerheader .activeTab svg path{ fill: {{$color1}}; }
.cardinnerheader .comment-file-btns{ float: none; }
.cardinnerheader .comment-file-btns .btn-type1 { margin-left: 0; }
.proj-card .right .add-task-type { max-width: 95%; }
.card-info-btn {
    font-size: 16px;
    position: absolute;
    font-weight: 400;
    right: 10px;
    top: 4px;
    width: 18px;
    height: 20px;
    background: transparent;
    border: none;
    padding: 0px;
}
.card-info-btn img{ height: 15px; }
.comment-box-inner .comment-file-btns .btn-type1 { margin-left: 0; }
/* Tooltip */
.tooltip{ position: fixed; }
.tooltip .tooltip-inner{
    max-width:250px;
    padding:3px 8px;
    color:#fff;
    text-align:center;
    background-color:{{$color1}};
    border-radius:5px;
    white-space: normal;
}
.tooltip.bottom .tooltip-arrow,
.tooltip.top .tooltip-arrow{
    border-bottom-color:{{$color1}} !important;
}

@media(max-width: 1199px){
    .milestone-content-child-box {
        min-width: 310px !important;
        max-width: 310px !important;
        width: 310px !important;
    }
    .radio-icons svg {
        width: 20px !important;
        height: 20px !important;
    }
}
@media (max-width: 991px){
    .milestone-content-child-box {
        min-width: 356px !important;
        max-width: 356px !important;
        width: 256px !important;
    }
}

/*******/
.smooth-dnd-container.horizontal {
    white-space: nowrap;
    height: 100%;
    width: 100%;
    overflow: auto;
    display: flex;
}
.smooth-dnd-container.horizontal > .smooth-dnd-draggable-wrapper {
    height: 100%;
    display: inline-block;
    vertical-align: top;
    white-space: normal;
    border-right: 2px solid #fff;
    padding: 0 !important;
}
.milestone-content-child-box header {
    padding: 0px 10px;
    color: #000;
    position: relative;
}
.milestone-content-child-box header span.sc-bZQynM{
    text-align: center;
    display: block;
    padding: 8px 0px;
}
.smooth-dnd-container.horizontal span:not(:last-child) .milestone-content-child-box header::after {
    content: '';
    position: absolute;
    right: -1px;
    border-right: 1px solid #f6f6f6;
    height: 100%;
    top: 0;
}
.milestone-content-child-box .sc-EHOje,
.milestone-content-child-box .sc-EHOje .mCustomScrollBox {
    width: 100%;
    height: 100%;
}
.milestone-content-child-box .sc-EHOje .mCustomScrollBox .mCSB_container {
    height: 100%;
    display: flex;
    margin-right: 5px !important;
    top: 0px !important;
}
.milestone-content-child-box .sc-EHOje{
    padding-bottom: 0px !important;
    max-height: 100% !important;
}
.milestone-content-child-box .smooth-dnd-container.vertical{
    min-height: 100%;
    width: 100%;
    padding: 0px 10px;
    min-height: 100%;
}
.milestone-content-child-box .sc-EHOje{
    padding-bottom: 0px !important;
    max-height: 100% !important;
}
.milestone-content-child-box .smooth-dnd-container.vertical{
    min-height: 100%;
    width: 100%;
    padding: 0px 10px;
}
.smooth-dnd-container.vertical > .smooth-dnd-draggable-wrapper {
    overflow: hidden;
    display: block;
}
.milestone-content-child-box article {
    width: 100% !important;
    max-width: inherit;
    border: none !important;
    background: transparent !important;
    display: inline-block;
}
ul.dropdown-menu { z-index: 1001; }
.task-addbar {
    border: 1px dashed #dddddd;
    min-height: 45px;
    margin-top: 10px;
    margin-bottom: 0;
    text-align: left;
    padding-left: 50px;
}
.project-milestones {
    display: inline-block;
    width: 100%;
    clear: both;
    padding-right: 25px;
    position: relative;
}
.user_pic_txt {
    margin: auto;
    line-height: 10px;
    font-size: 10px;
    text-align: center;
    word-break: break-all;
    align-self: center;
}
button:focus{ outline: none; }
.tast-radiobox label { margin: 4px; }
.tast-radiobox:first-child{ margin-left: 0px !important; }
.tast-radiobox:last-child{ margin-right: 0px !important; }
.modal-small-view .modal-dialog {
    max-width: 420px;
    width: inherit;
}
.modal-small-view .modal-body { padding: 20px 15px; }
.modal form label, .form-group label{
    color: #888888;
    font-size: 13px;
    font-weight: 400;
    margin-bottom: 5px;
}
.width-100{ max-width: 100% !important; }
.modal-small-view .milestoneNameBox{ margin-top: 22px; }
.milestone-content-child-box>div.ejtCpj{ width: 100%; }
.attach-file-group .input-group-btn .open-file{
    padding: 5px;
    min-width: 85px;
    font-size: 13px;
}
.card-info-tabs .pb-container.progress-btn {
    min-width: 62px;
    margin-top: 0px;
    float: right;
}
.card-info-tabs .pb-container.loading .pb-button {
    width: 26px !important;
    height: 26px !important;
    border-width: 3px !important;
    border-color: {{$color2}} !important;
}
.card-info-tabs .pb-container .pb-button { margin: 0; }
.card-info-tabs .pb-container .pb-button span{ font-size: 13px; }
.card-info-tabs .pb-container .pb-button svg {
    height: 26px;
    width: 26px;
    position: absolute;
    transform: translate(-50%, -50%);
    pointer-events: none;
    left: 10px;
    top: 10px;
}
.comment-data .text,
.comment-data .text a {
    word-break: break-all;
    overflow: hidden;
}
.file-group .btn.open-file {
    padding: 7px 5px;
    min-width: 85px;
    font-size: 12px;
    height: 34px;
}
.taskColorBorder {
    width: 18px;
    height: 100%;
    float: left;
    background: {{$color2}};
    cursor:pointer;
    border-radius: 3px 0px 0px 3px;
    align-items: center;
}
.taskcolorPickerBox {
    padding: 3px;
    border-radius: 3px;
    margin-top: 8px;
    position: absolute;
    top: 0;
    left: 10px;
    width: 103px;
    min-width: inherit;
    min-height: 30px;
    cursor: default;
}
.taskcolorPickerBox::before,
.taskcolorPickerBox::after{
    content: '';
    position:absolute;
    top: 3px;
    width: 6px;
    height: 6px;
    border-top: 6px solid transparent;
    border-bottom: 6px solid transparent;
}
.taskcolorPickerBox::before{
    left: -6px;
    border-right: 6px solid #d6d6d6;
}
.taskcolorPickerBox::after{
    left: -5px;
    border-right: 6px solid #fff;
    font-size: 0px;
}
.taskcolorPickerBox li{
    width: 15px;
    height: 15px;
    display: inline-block;
    margin: 2px;
    cursor: pointer;
}
.jHtXmx{ cursor: move !important; }
.Select--multi .Select-value {
    position: relative;
    padding-right: 20px;
    float: left;
}
.Select--multi .Select-value-icon {
    position: absolute;
    right: 0;
    border: none !important;
}
.task-type-select .svgicon{ cursor: pointer; }
.Select-placeholder, .Select--single > .Select-control .Select-value{ line-height: 32px !important; }
.colorChoose-block {
    max-width: 300px;
    margin: 0 auto;
}
.colorChoose-block .pb-container {
    margin-top: 0px;
    margin-left: 10px;
    vertical-align: middle;
}
.Select.is-clearable.is-searchable.Select--single .Select-menu-outer{ position: relative; }
.DayPickerInput{ width: 100%; }
.DayPickerInput-OverlayWrapper{
    z-index:999999;
}
button.removeBtn {
    background: transparent;
    border: none;
    font-size: 20px;
    height: 20px;
    width: 20px;
    line-height: 15px;
    padding: 0px 5px 5px;
    color: #9d9d9d;
}
button.removeBtn:hover{ color: {{$color1}}; }
.example-enter { opacity: 0.01; }
.example-enter.example-enter-active {
    opacity: 1;
    transition: opacity 500ms ease-in;
}
.example-leave { opacity: 1; }
.example-leave.example-leave-active {
    opacity: 0.01;
    transition: opacity 300ms ease-in;
}
.text-data {
    word-break: break-all;
    cursor: pointer;
}
.comment-box-inner .title{
    padding-left: 0px;
    padding-right: 0px;
}
.pro-milestone-infobtn-name {
    padding: 2px 10px 2px 30px;
    margin-top: 8px;
}
.pro-mileston-name{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle;
    font-size: 14px;
    line-height: 20px;
    padding-right: 35px;
    position : relative;
}
.pro-milestone-infobtn-name .info {
    display: block;
    position: absolute;
    left: 15px;
    top: 2px;
}
.pro-milestone-infobtn-name .info button{
    background: transparent;
    border: none;
    display: block;
}
.milestoneRenameForm {
    position: absolute;
    top: -2px;
    left: 35px;
    width: 220px;
}
.pro-milestone-infobtn-name .info img {
    height: 18px;
}
.milestoneRenameForm .title-field {
    width: 168px;
    float: left;
}
.milestoneRenameForm .title-field input {
    width: 100%;
    border-radius: 2px;
    border: 1px solid #ccc;
    padding: 2px 5px;
}
.milestoneRenameForm .pb-container.progress-btn {
    min-width: 50px;
    width: 50px;
    float: right;
    margin-top: 0;
}
.milestoneRenameForm .pb-container button.pb-button {
    padding: 5px;
    height: 28px;
    line-height: 18px;
}
.milestoneRenameForm .pb-container.loading .pb-button {
    width: 28px;
    height: 28px;
    border-width: 3px;
}
.milestoneRenameForm .pb-container .pb-button svg {
    height: 29px;
    width: 29px;
    left: 11px;
    top: 11px;
}
.milestoneRenameForm .pb-container .pb-button svg.pb-progress-circle path {
    stroke: currentColor;
    stroke-width: 3px;
}
.content-singleline{
    white-spacing: nowrap;
    overflow: auto;
}
.content-singleline .milestone-title-box { min-width: 150px; }
.pro-mileston-name .editProjectName {
    position: absolute;
    right: 10px;
    top: 0;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
}
.pro-mileston-name:hover .editProjectName {
    opacity: 1;
    visibility: visible;
}
.editProjectName svg{
    width: 20px;
    height: 20px;
}
.content-singleline .milestone-heading{
    margin: 0px;
}
.project-list-view {
    padding: 5px 5px;
    margin: 4px 0px;
    width: 100%;
    border-bottom: 1px solid #e5e5e5;
}
.svgpreview svg{
    width: 72px;
    height: 72px;
    vertical-align: middle;
}
.dropList li{
    display: block;
    padding: 3px 20px;
    clear: both;
    font-weight: 400;
    line-height: 1.42857143;
    color: #333;
    white-space: nowrap;
    cursor: pointer;
}
.dropList li:hover,.dropList li:focus{
    text-decoration: none;
    color: #262626;
    background-color: #f5f5f5;
}
.pageLoader {
    position: fixed;
    background: rgba(0, 0, 0, 0.65);
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: 999;
}
.pageLoader .pg-content-loader {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto;
}
.userText-imgIcon{
    width: 35px;
    height: 35px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 25px;
    margin-bottom: 10px;
    background-repeat: no-repeat;
    background-position: center top;
    background-size: cover;
    padding: 3px;
    float: left;
    cursor: pointer;
}
.multipleuser_imgs .user_icon.flex-box{
    display: -webkit-box !important;
    display: -moz-box !important;
    display: -ms-flexbox !important;
    display: -webkit-flex !important;
    display: flex !important;
}
.commenter-img-sec .userPicName#user-img{
    width: 35px;
    height: 35px;
    font-size: 14px;
}
.mid-datepicker {
    display: inline-block;
    max-width: 400px;
    margin-left:10px;
    display: none;
}
.mid-datepicker .DayPicker{
    max-width: 400px;
    margin: auto;
    display: inline-block;
}
.calBtn-space{
    vertical-align: top;
    margin-top: 25px;
}
.DayPicker-Weekday,
.mid-datepicker .DayPicker-Weekday,
.mid-datepicker .DayPicker-Week{
    font-size: 14px !important;
}
.mid-datepicker .DayPicker-Caption { font-size: 16px; }
.mid-datepicker .DayPicker-NavButton {
    width: 18px !important;
    height: 18px !important;
}
.task-assign span { float: left; }
.task-assign{ margin: 2px 0px; }
.d-inline{ display: inline-block !important; }
.default-pic-bg{ background-color:gray !important; }
.pro-milestone-parent {
    background: #f6f6f6;
    margin-top: 20px;
}
.milestone-content{
    background-color: #f6f6f6 !important;
    border: 0px !important;
    height: 750px !important;
}
/*.addNew-taskBtn,
.milestone-title-box .info .dropdown-menu,*/
.milestone-title-box .info .dropdown-menu .svgicon svg,
.milestone-inner-dropdown .icons-li svg{
    width: 20px;
    height: 20px;
}
.addNew-taskBtn i{
    font-size: 16px;
    margin-right: 5px;
}
.card-box svg path{ fill: #9d9d9d; }
.card-box .task-depend-btn2 svg path{ fill: {{$color1}}; }
.calendarDate {
    float: left;
    margin-left: 2px;
    padding-top: 3px;
}
.cardinnerheader .cardsTab .task-dependdiv{ float: none !important; }
.card-description h5{
    color: #505050;
    font-weight: 700;
    font-size: 16px;
    margin-top: 0px;
    padding-right: 60px;
}
.depend-task-description .taskname {
    font-size: 15px;
    color: #505050;
    font-weight: 700;
    position: relative;
    padding-right: 20px;
}
.depend-task-description .taskname span {
    position: absolute;
    right: 0;
    top: 1px;
    color: #9d9d9d;
    cursor: pointer;
}
.depend-task-description .task-assign,
.depend-task-description .task-status {
    color: #888888;
    font-size: 13px;
}
.assignNameList{
    width: 66%;
    float: left;
}
.card-description {
    position: relative;
    min-height: 50px;
}
.cardDelEdit{
    position: absolute;
    right: 0px;
    top: 0px;
    text-align: center;
}
.default-pic-bg{ background: #e6e6e6 !important; }
.card-description .text-data, .grayTxt {
    width: 100%;
    color: #444;
    font-size: 14px;
    line-height: 14px;
    margin-bottom: 5px;
}
.addTaskUser {
    border-top: 1px solid #f6f6f6;
    padding-top: 20px;
}
span.Select-arrow, span.Select-arrow:hover {
    width: 9px;
    height: 9px;
    border-color: transparent #888 #888 transparent !important;
    border-width: 1px !important;
    border-style: solid;
    transform: rotate(45deg);
    -webkit-border-radius: 0px !important;
    -moz-border-radius: 0px !important;
    -ms-border-radius: 0px !important;
    -o-border-radius: 0px !important;
    border-radius: 0px !important;
    top: -4px;
    display: none;
}
.cardbox-inner .Select-control { border: 1px solid #eaeaea; }

/* Start Dependancy Icons Colors */
.card-box .depPink svg path { fill: #9d9d9d; }
.card-box .depRed svg path { fill: #c00000 !important; }
/* End Dependancy Icons Colors */
.milestone-inner-dropdown{
    width: 100%;
    display: table;
}
.milestone-inner-dropdown .icons-li {
    margin: auto auto 0 auto;
    padding: 0px 1px;
    display: table-cell;
    vertical-align: bottom;
}
.editBtnSvg {
    background: transparent;
    border: none;
    padding: 2px;
    display: block;
}
.editBtnSvg svg {
    width: 18px;
    height: 18px;
    vertical-align: middle;
}
.editBtnSvg svg path{ fill: #9d9d9d; }
.Select--multi .Select-value {
    background-color: #fdfdfd !important;
    color: #353535 !important;
}
.project-tasks-details,
.project-milestone-details,
.project-due-details{
    border-radius: 5px;
    display:inline-block;
    width: 100%;
    min-height: 45px;
}
.project-tasks-details,
.project-milestone-details{
    padding: 10px 10px;
}
.project-due-details{ padding: 6px 10px; }
.project-tasks-details .totalTask, .project-tasks-details .taskText, .project-tasks-details .taskCount,
.project-milestone-details .totalmilestone, .project-milestone-details .milestoneText, .project-milestone-details .milestoneCount {
    display: inline-block;
    margin: 0px 3px;
}
.transparent-btn{
    background: none !important;
    border: none !important;
}
.redProject-btn{
    background: red;
    color: #fff;
}
.orangeProject-btn{
    background: orange;
    color: #fff;
}
.greenProject-btn{
    background: green;
    color: #fff;
}
.taskAssignedUserImg {
    float: left;
    width: 100%;
    margin-top: 10px;
    margin-bottom: 10px;
}
.taskAssignedUserImg .multiusericon-outer{ width: 260px; }
.taskAssignedUserImg .userText-imgIcon{ margin: 2px; }
.comment-box-inner h5.title {
    font-weight: 400;
    font-size: 13px;
    color: #888888;
    margin-bottom: 0px;
    padding-bottom: 8px;
}
.title-w-border{ border-bottom: 1px solid #f6f6f6; }
.border-bottom-dashed { border-bottom-style: dashed; }
.milestoneDate-calendar{
    width: 100%;
    display: inline-block;
}
.milestoneDate-calendar .date{
    font-size: 14px;
    color: #888888;
    float: left;
}
.milestoneDate-calendar .calendarIcon{
    float: right;
    cursor:pointer;
}
.statusAssignBtns {
    border-top: 1px solid #e5e5e5;
    border-bottom: 1px solid #e5e5e5;
    padding: 8px 0px;
    margin: 10px 0px 0px 0px;
}
.statusAssignBtns button{ color: {{$transprancy2}};}
.statusAssignBtns button.active{ color: {{$color2}} }
.svgRed svg g path{ fill:red !important; }
.card-description .text-data { min-height: 25px; }
.tastWcount span{
    vertical-align: text-top;
    font-size: 14px;
    line-height: 14px;
    margin-left: 3px;
    float: left;
}
.default-selectbox {
    padding: 5px 20px 5px 10px;
    max-width: 200px;
    border: 1px solid #e9e9e9;
    width: 100%;
    min-width: 220px;
}
span.scrollLeftBtn,
span.scrollRightBtn{
    position: absolute;
    height: 25px;
    width: 25px;
    margin: auto;
    top: 36px;
    cursor: pointer;
    background-color: #fff !important;
    border-radius: 25px;
    background-size: 15px !important;
    -webkit-box-shadow: 0px 1px 9px -2px rgba(0, 0, 0, 0.20);
    box-shadow: 0px 1px 9px -2px rgba(0, 0, 0, 0.20);
}
span.scrollLeftBtn{
    left: -25px;
    background: url('public/img/left-arrow.png') no-repeat center;
}
span.scrollRightBtn{
    right: -5px;
    background: url('public/img/right-arrow.png') no-repeat center;
}
.proj-milestones-outer {
    position: relative;
    padding-right: 35px;
}
.Button__root___1gz0c {
    padding: 1px 3px !important;
    height: 25px !important;
}
.EditorToolbar__root___3_Aqz {
    margin: 0 0px !important;
    padding: 8px 10px 0px !important;
}
.DraftEditor-editorContainer {
    max-height: 200px;
    overflow: auto;
}
.RichTextEditor__editor___1QqIU .RichTextEditor__paragraph___3NTf9, .RichTextEditor__editor___1QqIU pre {
    margin: 5px 0 !important;
    font-size: 12px;
}
/* Custom dropdown */
.custom-select {
    position: relative;
    display: inline-block;
    vertical-align: middle;
    width: 100%;
}
.custom-select select {
    background-color: #fff;
    padding-right: 25px;
    border-radius: 3px;
    text-indent: 0.01px;
    text-overflow: '';
    -moz-appearance: none;
    -webkit-appearance:none;
    appearance: none;
}
.custom-select select::-ms-expand {
    display: none;
}
.custom-select::before,
.custom-select::after {
    content: "";
    position: absolute;
    pointer-events: none;
}
.custom-select::after {
    content: "\25BC";
    height: 1em;
    font-size: .625em;
    line-height: 1;
    right: 1.2em;
    top: 20px;
    margin-top: -.5em;
    z-index: 3;
}
.custom-select::before {
    width: 25px;
    height: 100%;
    display: inline-block;
    right: 0;
    top: 0;
    bottom: 0;
    border-radius: 0 3px 3px 0;
    background-color: #fff;
    border-width: 1px 1px 1px 0px;
    border-style: solid;
    border-color: #e9e9e9;
}
.custom-select::after {
    color: rgba(0,0,0,.6);
}
.custom-select select[disabled] {
    color: rgba(0,0,0,.25);
}
.form-control:focus, :focus{
    border-color: #dfdfdf !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    outline: none !important;
}
.card-info-tabs .DayPicker{
    margin-bottom: 5px;
}

@media (max-width: 1199px){
    .cardsTab .svgicon svg {
        width: 20px;
        height: 20px;
    }
}

.dateSelectBox{
    border-left: 1px dashed #e9e9e9;
    position: relative;
}
.dateSelectBox::after {
    content: '';
    border-left: 10px solid #e9e9e9;
    border-top: 7px solid transparent;
    border-bottom: 7px solid transparent;
    width: 10px;
    height: 14px;
    position: absolute;
    left: 0;
    top: 16px;
}
.iconRadioBtn label span{
    border: 2px solid #c7c7c7;
    display: inline-block;
    padding: 2px 25px;
}
.iconRadioBtn label input[type="radio"]{
    width: 0px;
    height: 0px;
    visibility: hidden;
    opacity: 0;
}
.iconRadioBtn label input[type="radio"]:checked + span{
    border-color: {{$color2}};
}
.iconRadioBtn label span svg {
    width: 50px;
    height: 50px;
    cursor: pointer;
}
.iconRadioBtn label span svg path,
.rdo-icon label svg path{
    fill: #e1e1e1 !important;
}
.iconRadioBtn label input[type="radio"]:checked + span svg path,
.rdo-icon input[type="radio"]:checked + label svg path{
    fill: {{$color2}};
}
.cardsTab .svgicon svg {
    width: 20px;
    height: 20px;
}
.DraftEditor-editorContainer, .DraftEditor-editorContainer * { font-family: 'Lato', sans-serif; }

/*Project Improvement 3 Style*/
.menu-space {
    float: left;
    width: 5px;
    display: block;
    height: 1px;
}
.rdo-icon label {
    border: 2px solid #e1e1e1;
    width: 44px;
    height: 44px;
    padding: 5px;
    cursor: pointer;
}
.rdo-icon svg {
    width: 30px;
    height: 30px;
    cursor: pointer;
    margin: auto;
}
.rdo-icon input[type="radio"],
.rdo-icon input[type="radio"]:after,
.rdo-icon input[type="radio"]:before{
    width: 0px;
    height: 0px;
    visibility: hidden;
    opacity: 0;
    filter: alpha(opacity=0);
}
.rdo-icon input[type="radio"]:checked + label{ border-color: {{$color2}}; }
.discussion-form .cke_contents { min-height: 100px !important; }
.cke_top, .cke_contents, .cke_bottom {
    display: block;
    overflow: hidden;
}
.cke_contents{ height: 120px }
.discussion-form .cke_contents body.cke_editable { margin: 5px 10px; }
.discussion-form .cke_contents body.cke_editable p { margin: 2px 0px; }
.selectedTxt{
    color: {{$color2}};
    margin-top: 5px;
}
.mr-n80{ margin-right: -80px; }
.externalTopicDoc{ margin-top: 20px; }
.externalTopicDoc form.doc-attach-form span.input-group-btn{
    display: table-cell !important;
    vertical-align: middle;
}
.externalTopicDoc form .form-sec-title {
    font-size: 16px;
    margin-bottom: -1px;
    display: inline-block;
    background: #f7f7f7;
    padding: 10px 10px 10px 10px;
    border-radius: 5px 5px 0px 0px;
    border: 1px solid #e5e4e5;
    border-bottom: none;
}
.externaldocsBg{
    background-color: #f7f7f7;
    padding: 15px 0px;
    border-radius: 0px 5px 5px 5px;
    border: 1px solid #e5e4e5;
}
.externalTopicDoc .pb-container{ margin-top: 0px; }
.externalTopicDoc .pb-container .pb-button {
    line-height: 14px;
    padding: 10px;
    height: 42px;
}
.externalTopicDoc .pb-container.loading .pb-button{ height: 36px; }
.externalTopicDoc form .form-sec-title { font-size: 16px; margin-bottom: -1px; }
.project-milestone-details .milestoneCount,
.project-tasks-details .taskCount {
    font-size: 18px;
    font-weight: 600;
    float: right;
    margin-right: 0px;
}
.project-milestone-details .milestoneCount small,
.project-tasks-details .taskCount small{ font-size: 13px; }
.project-milestone-details .milestoneText,
.project-tasks-details .taskText {
    vertical-align: top;
    margin-right: 15px;
    font-size: 12px;
    float: left;
    text-align: left;
}
.project-due-details .dueText{
    vertical-align: bottom;
    float: left;
    display: inline-block;
    text-align: left;
    font-size: 12px;
}
.project-due-details{ text-align: left; }
.taskDueDateDays {
    font-size: 18px;
    vertical-align: bottom;
    display: inline-block;
    line-height: 24px;
    float: right;
    margin-top: 10px;
}
.needFeature{ text-align: right; }
.needFeature .lbox{ margin-right: 25px; }
.needFeature .lbox,
.needFeature .rbox{
    display: inline-block;
    cursor: pointer;
}
.needFeature .lbox span svg,
.needFeature .rbox span svg{
    width: 22px;
    height: 22px;
}
.needFeature .lbox span svg path,
.needFeature .rbox span svg path{
    fill: #fff;
}
.needFeature .lbox span{
    padding-right: 5px;
    display: inline-block;
    vertical-align: middle;
}
.needFeature .rbox span{
    padding-left: 5px;
    display: inline-block;
    vertical-align: middle;
}
.resize-none{ resize: none; }
.projectAccess {
    min-height: 34px;
    padding: 6px 10px !important;
}
.userPermissionIcon svg,
svg {
    width: 35px;
    height: 35px;
}
.userPermissionIcon .count{
    background-color: {{$color1}};
    display: inline-block;
    min-width: 20px;
    height: 20px;
    border-radius: 25%;
}
.agendaDocsListMenu {
    padding-left: 0px;
    border-bottom: 1px solid #f7f7f7;
    float: left;
    width: 100%;
    padding-bottom: 20px;
    margin-bottom: 10px;
}
.agendaDocsListMenu li {
    position: relative;
    margin: 10px 0px;
    list-style: none;
    width: 50%;
    float: left;
}
.agendaDocsListMenu li span.file-icon {
    width: 35px;
    height: 35px;
    background-position: center;
    position: relative;
    z-index: 1;
    background-color: #fff;
    background-size: 35px;
    padding: 0px !important;
}
.agendaDocsListMenu li span { margin: auto 0; }
.agendaDocsListMenu li span.dlink { font-size: 14px; color: #888888; }
.btn-transparent{
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
}
.iconTypeBtn{
    height: 50px;
    height: 50px;
    background-repeat: no-repeat;
    background-position: center left;
    padding: 10px 0px 10px 40px;
    background-size: 35px;
    display: inline-block;
}
.userPermissionIconBtn{ position: relative; }
.userPermissionIconBtn span.count {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: red;
    display: inline-block;
    color: #fff;
    position: absolute;
    right: -4px;
    bottom: -4px;
    font-size: 12px;
    line-height: 20px;
}
.mobileEnableView {
    background-image: url(../../public/img/iphone-mockup.png);
    width: 320px;
    height: 650px;
    background-size: cover;
    background-repeat: no-repeat;
    margin: 0 auto;
    position: relative;
    padding: 44px 18px 19px 22px;
    border-radius: 50px;
    overflow: hidden;
}
.mobileEnableInnerView {
    width: 100%;
    height: 100%;
    background: #fff;
    border-radius: 3px 3px 30px 30px;
    overflow: hidden;
}
.mobViewHeader,
.mobViewOverlayDiv{
    background: {{$headerColor1}};
}
.mobViewHeader ._leftIcon,
.mobViewHeader ._rightIcon{
    width: 20%;
    float: left;
    display: inherit;
}
.mobViewHeader ._logoIcon{
    width: 60%;
    float: left;
}
.mobViewHeader ._logoIcon img {
    max-width: 120px;
    margin: auto;
    display: block;
    max-height:75px;
}
.notificationIcon{
    position: relative;
    margin: auto 10px;
}
.mobViewHeader ._leftIcon .notificationIcon i{
    color: {{$color2}};
    font-size: 20px;
}
.notificationIcon .notifiCount{
    position: absolute;
    display: inline-block;
    width: 15px;
    height: 15px;
    color: #fff;
    font-size: 10px;
    text-align: center;
    line-height: 15px;
    border-radius: 50%;
    background: red;
    right: -5px;
    top: -4px;
}
.mobViewHeader ._rightIcon img {
    margin: auto;
    max-width: 25px;
    max-height: 25px;
}
#mobViewHeader_bottom1{
    background-color: {{$color1}};
    min-height: 25px;
    width: 100%;
    float: left;
    color: #fff;
    text-align: center;
    padding: 5px;
}
#mobViewHeader_bottom2{
    background-color: {{$color2}};
    min-height: 25px;
    width: 100%;
    float: left;
    color: #fff;
    text-align: center;
    padding: 5px;
}
#mobViewHeader_bottom1 i {
    font-size: 10px;
    font-weight: 400;
    margin-left: 2px;
}
.mobViewBody {
    float: left;
    width: 100%;
    height: 514px;
    padding: 5px 0px;
}
.mobViewListRow {
    position: relative;
    padding: 0px 10px;
    border-bottom: 1px solid #f4f4f4;
}
.mobViewListRow .icon-right {
    position: absolute;
    right: 10px;
    top: 8px;
}
.mobViewListRow .icon-right i{ font-size: 18px; }
.mobViewListRow .icon-c-red i{ color: red; }
.mobViewListRow .icon-c-green i{ color: green; }
.mobViewListRow .listTitle{
    padding: 5px 20px;
    background-image: url('../../public/img/vertical-dots.png');
    background-repeat: no-repeat;
    background-position: left 10px;
    background-size: auto 16px;
    font-size: 12px;
    color: #888888;
}
.mobViewListRow .listTitle .show {
    font-size: 13px;
    color: #444;
    font-weight: 600;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
}
.mobViewOverlayDiv {
    position: absolute;
    width: 315px;
    height: 648px;
    background: rgba(0,0,0,0.25);
    top: 3px;
    left: 5px;
    border-radius: 50px;
    padding: 25px;
}
.mobViewOverlay_popup {
    max-width: 250px;
    margin: auto;
    width: 100%;
    overflow: hidden;
    border-radius: 5px;
    align-self: center;
    align-content: center;
}
.mobViewOverlay_popup .popup_head{
    padding: 10px;
    background: {{$color2}};
    color: #fff;
    font-size: 16px;
    text-align: center;
}
.mobViewOverlay_popup .popup_head ._date,
.mobViewOverlay_popup .popup_head ._time{
    font-size: 14px;
}
.mobViewOverlay_popup .popup_body{ background: #fff; }
.mobViewOverlay_popup .popup_body ul{
    padding: 0px;
    margin-bottom: 0px;
}
.mobViewOverlay_popup .popup_body ul li {
    padding: 8px;
    font-size: 13px;
    color: #444;
    border-bottom: 1px solid #e1e1e1;
    text-align: center;
}
.mobViewOverlay_popup .popup_body ul li.exitBtn{
    color: {{$color2}};
    font-size: 16px;
}
.appStoreImgLinks{
    margin-top: 15px;
    width: 100%;
    display: inline-block;
}
.appStoreImgLinks img:not(:last-child){
    margin-right: 15px;
    cursor: pointer;
}
.mobLoginMailCode{
    width: 100%;
    display: inline-block;
    font-size: 17px;
}
.mobLoginMailCode strong{
    margin-right: 10px;
    font-size: 15px
}
.mobLoginMailCode ._code {
    color: {{$color1}};
    font-weight: 600;
    font-size: xx-large;
}
.prepdDateList .externalTopicDoc form {
    margin-right: -80px;
    margin-left: 20px;
}
.picUpload_parent{
    width: 180px;
    height: 180px;
    position: relative;
    border-radius: 100%;
    background-color: #eeeeee !important;
    border: 1px solid #e1e1e1;
    background-position: center !important;
}
.picUpload_parent avatar-image.picUpload_div {
    background-color: transparent !important;
}
.picUpload_parent avatar-image.picUpload_div>div:not(:last-child),
.picUpload_parent avatar-image.picUpload_div>div input[type="file"]{
    border-radius: 100%;
}
.picUpload_parent avatar-image.picUpload_div>div:not(:first-child) {
    top: inherit !important;
    padding: 0px 20px;
    left: 0 !important;
    right: 0 !important;
    margin: auto;
    position: relative !important;
    background-color: transparent !important;
}
.milestone-content-child-box .sc-EHOje:focus, .milestone-content-child-box .sc-EHOje *:focus {
    outline: none !important;
    border: none;
}
.staff-login-form {
    max-width: 338px;
    padding: 25px;
}
.agendaDocsListView {
    border-top: 1px solid #e1e1e1;
    margin-top: 5px;
    padding: 10px 0px;
    display: inline-block;
    width: 100%;
}
.agendaDocsListView .agendaDocsListMenu .not-found {
    width: 100%;
    padding: 30px;
    text-align: center !important;
    display: inline-block;
    font-size: 20px;
    background: #bd10e0;
    color: #fff;
    border-radius: 3px;
    opacity: 0.5;
}
.imgUploadeStyle1 .picUpload_parent avatar-image.picUpload_div>div:not(:first-child) div[name="action-con"] {
    min-width: 50px !important;
    margin: 0px !important;
    display: inline-block !important;
    background: {{$color1}};
    padding: 5px;
    width: inherit !important;
}
a.imgCropUploadClose, a.imgCropUploadDone {
    color: #8a8a8a;
    font-weight: 300;
    margin: 0px 1%;
    cursor: pointer;
    width: 48%;
    display: inline-block;
    float: left;
}
.picUpload_parent avatar-image.picUpload_div>div:not(:first-child)>div>div>div {
    background: #8a8a8a !important;
    height: 4px !important;
}
.picUpload_parent avatar-image.picUpload_div>div:not(:first-child)>div>div>div span {
    background: #8a8a8a !important;
    border: 2px solid white !important;
    top: -6px !important;
}
.picUpload_parent avatar-image.picUpload_div>div:not(:last-child):hover {
    background: #fff;
}
.picUpload_parent avatar-image.picUpload_div>div:first-child div:nth-last-child(2){
    opacity: 0;
    filter: alpha(opacity=0);
    visibility: hidden;
    -webkit-transition: all ease-in-out 200ms;
    -moz-transition: all ease-in-out 200ms;
    -ms-transition: all ease-in-out 200ms;
    -o-transition: all ease-in-out 200ms;
    transition: all ease-in-out 200ms;
    z-index: 3;
}
.picUpload_parent avatar-image.picUpload_div>div:first-child:hover div:nth-last-child(2){
    opacity: 100;
    filter: alpha(opacity=100);
    visibility: visible;
}
.picUpload_parent avatar-image.picUpload_div>div:first-child div:first-child p{
    color: #333 !important;
}
.picUpload_parent avatar-image.picUpload_div>div:not(:last-child) > div:nth-last-child(1) div {
    background-color: #fff !important;
}
.popsonresize .modal-header {
    background: #fff !important;
    border: none;
}
.popsonresize .close {
    color: #000;
    z-index: 999;
    height: 24px !important;
    position: absolute;
    right: 15px;
    top: 10px;
}
.popsonresize .modal-body {
    text-align: center;
    font-size: 18px;
    color: #444;
}
.popsonresize .modal-dialog{ width: 400px; }
.popsonresize .modal-body p{ word-break: normal; }
.popsonresize .modal-body svg{
    width: 50px;
    height: 50px;
}
.table-caption{
    width: 67%;
    position: relative;
    top: 54px;
    text-align: right;
}
.ui-autocomplete {
    max-height: 190px;
    overflow-y: auto;
}
.ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active, a.ui-button:active, .ui-button:active, .ui-button.ui-state-active:hover {
    border: none !important;
}
.ui-menu .ui-state-focus, .ui-menu .ui-state-active { margin: 0px !important; }
.meetingMembername { margin-top: 108px; }
.meetingMemberNameText { margin: auto 0; }
.meetingMember img {
    margin: auto;
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    width: 20px;
    cursor: pointer;
}
.pb-container.success.undefined .pb-button {
    border-color: {{$color2}};
    background-color: {{$color2}};
}
.divTextArea{
    padding: 10px;
    border: 1px solid #e1e1e1;
    max-height: 120px;
    overflow: auto;
}
.react-confirm-alert-overlay{
    z-index:999999 !important;
}
/***********/
.labelIcon svg {
    width: 25px;
    height: 25px;
    margin-right: 10px;
    vertical-align: middle;
}
.drag-dots-icon{
    margin: auto;
    align-self: center;
}
.drag-dots-icon svg{
    width: 14px;
    height: 14px;
}
.drag-dots-icon svg path{ fill: #fff; }
.drop-style1 .dropdown-toggle:has(span){ padding-left: 30px; }
.border-top-light { border-top: 1px solid #e1e1e1; }
.slide-pane__header {
    -ms-flex: 0 0 50px !important;
    flex: 0 0 50px !important;
    background: #ffffff !important;
    height: 50px !important;
}
.slide-pane__close {
    margin-left: 24px;
    padding: 5px 5px !important;
    opacity: 1 !impotant;
    cursor: pointer;
    height: 30px;
    width: 30px;
    text-align: center;
}
.slide-pane__content { padding: 5px 0px 20px 0px !important; }
.card-info-tabs { padding: 30px; }
.cardDelEdit button.removeBtn, .cardDelEdit button.editBtnSvg {float: left; }
.IconButton__isActive___2Ey8p { background: transparent !important; }
.card-info-tabs .ButtonGroup__root___3lEAn { margin: 0 0px 0px 0 !important; }
.card-info-tabs .Button__root___1gz0c {
    border-width: 0px 1px 0px 0px !important;
    border-style: solid !important;
    border-color: #dddddd !important;
    padding: 5px !important;
    height: 30px !important;
}
.card-info-tabs .EditorToolbar__root___3_Aqz {
    margin: 0 0px !important;
    padding: 0px !important;
}
.card-info-tabs .input-group .pb-container button[type="submit"] {
    height: 36px;
    text-align: center;
    min-width: 85px;
    padding: 7px 5px;
}
.card-info-tabs .input-group .pb-container.loading button[type="submit"] {
    height: 26px;
    float: none;
    width: 26px !important;
    min-width: 26px;
    margin: 5px;
}
.status-change-block, .assign-change-block{ background-color: #F3f3f3; }
.addNewLink{
    padding: 10px 20px 0px 6px;
    position: relative;
}
.addNewLink a{ color: {{$color2}}; }
.toggle-form-bg{
    padding: 10px;
    background-color: #f3f3f3;
}
.input-btn-inline,
.input-btn-inline:hover,
.input-btn-inline:focus {
    background: {{$color2}};
    color: #fff !important;
    height: 36px;
    min-width: inherit;
}
.custom-select .Select-control {
    border-radius: 0px;
    border-color: #e1e1e1;
}
.task-status span, .task-assign span {
    font-weight: 600;
    color: #505050;
    margin-right: 5px;
}
.addNewTag { margin-top: 20px; }
.task-status span{
    margin-right: 5px;
    display: inline-block;
}
.tag-label {
    display: inline-block;
    color: #fff;
    font-size: 14px;
    padding: 3px 10px;
    margin: 3px;
    border-radius: 3px;
    position: relative;
}
.proj-card .right .tag-label {
    font-size: 12px;
    padding: 2px 10px;
    border-radius: 2px;
}
.tag-label.removeTag{
    padding-right: 30px;
}
.tag-label .close {
    font-size: 12px;
    opacity: 1;
    margin-left: 5px;
    position: absolute;
    right: 0;
    height: 100%;
    top: 0;
    background: #333;
    padding: 5px 5px;
    border-radius: 0px 3px 3px 0px;
}
.card-info-tabs .form-control { height: 36px; }
.input-group-btn button.pb-button { border-radius: 0px 3px 3px 0px; }
.default-form-style.task-doc { background-color: #f3f3f3; padding: 10px; }
.comment-box-inner .cke_contents { height: 120px !important; }
.text-link{
    color: {{$color2}};
    font-size: 16px;
    cursor: pointer;
}
.text-link i.icon{ margin-right: 5px; }
.text-link i.icon::before{ font-size: 14px; }
.assignUserName {
    margin: 2px 0px;
    display: inline-block;
    width: 100%;
}
.sideTaskPanel{
    width: 100% !important;
    max-width: 600px !important;
}
.circle-report .CircularProgressbar .CircularProgressbar-trail { stroke: #e7e6e5; }
.circle-report .CircularProgressbar .CircularProgressbar-path { stroke: {{$color2}}; }
.circle-report {
    margin: 0 auto;
    height: 70px;
    width: 70px;
}
.circle-report-count span{ color: {{$color1}}; }
.taskDueCalendar { margin-top: 9px; }
.redProject .__inner{ border: 2px solid #fa4643; }
.orangeProject .__inner{ border: 2px solid #faa643; }
.greenProject .__inner{ border: 2px solid #7fd86f; }
.taskDueCalendar .__inner {
    width: 80px;
    display: inline-block;
    position: relative;
}
.taskDueCalendar .__inner .project-due-details{ float: left; }
.taskDueCalendar .taskDueDateDays span.dueDays {
    font-size: 20px;
    font-weight: 600;
    width: 100%;
    display: inline-block;
}
.taskDueCalendar .__inner::before, .taskDueCalendar .__inner::after {
    content: '';
    position: absolute;
    top: -9px;
    width: 5px;
    height: 9px;
    background: #faa643;
    border-radius: 3px 3px 0px 0px;
}
.taskDueCalendar .__inner::before{ left: 10px; }
.taskDueCalendar .__inner::after{ right: 10px; }
.redProject .__inner::before, .redProject .__inner::after{ background-color: #fa4643; }
.orangeProject .__inner::before, .orangeProject .__inner::after{ background-color: #faa643; }
.greenProject .__inner::before, .greenProject .__inner::after{ background-color: #7fd86f; }
.sideTaskPanelInner .slide-pane__title-wrapper { margin-right: 30px; }
.sideTaskPanelInner .slide-pane .slide-pane__title{ max-width: 100%; }
.taskNameField span {
    width: 100%;
    display: inline-block;
    position: relative;
    padding: 5px 35px 5px 0px;
}
.taskNameField span, .task-name h5, .text-ellipsis{
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.taskNameField span button.editBtnSvg {
    position: absolute;
    right: 0;
    top: 0;
}
.sideTaskPanelInner .slide-pane__title-wrapper {
    margin-right: 30px;
    border-left: 1px solid #e1e1e1;
    padding-left: 20px;
    margin-left: 10px;
}
.taskNameUpdateform .form-group { width: 100%; }
.taskNameUpdateform form {
    position: relative;
    padding-right: 125px;
}
.taskNameUpdateform .form-group .form-control {
    width: 100%;
    height: 36px;
    border-radius: 5px 0px 0px 5px;
}
.form-actionBtns {
    position: absolute;
    right: 0;
    top: 0;
}
.form-actionBtns button.btn.btn-default {
    min-width: inherit;
    box-shadow: none;
    margin-left: 2px;
    border-radius: 0px;
    border-color: #e5e5e5;
}
.taskname .task-link-connection{ display: inline-block; }
.taskname .task-link-connection svg {
    width: 25px;
    height: 25px;
    vertical-align: middle;
    margin: 0px 5px;
}
.comment-box-inner h5.title {
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}
.color1Transparent41 table tr th{
    background-color: {{$transprancy1}};
    color: #fff;
}
.color1Transparent41{ border-top-color: {{$color1}}; }
.addNewTag h5 {
    color: #505050;
    font-weight: 700;
    font-size: 16px;
}
.redProject .taskDueHead{ background: #fa4643; color: #fff; }
.orangeProject .taskDueHead{ background: #faa643; color: #fff;}
.greenProject .taskDueHead{ background: #7fd86f; color: #fff; }
.taskDueHead{ height: 10px; }
.taskDueCalendar .taskDueDateDays {
    font-size: 14px;
    display: inline-block;
    line-height: 16px;
    width: 100%;
    padding: 2px 5px 10px 5px;
}
.threeColTable .table-div-head { border-bottom: 1px solid #cccccc; }
.threeColTable .table-div-head .third-part {
    background: #f7f6f7;
    color: #333;
    font-weight: bold;
    position: relative;
}
.threeColTable .table-div-head:not(:last-child) .third-part::after{
    content: "";
    border-right: 1px solid #d0d0d0;
    height: 63%;
    position: absolute;
    top: 0;
    bottom: 0;
    right: 0;
    margin: auto;
}
.threeColTable .table-div-head .third-part,
.threeColTable .table-div-body .third-part {
    padding-top: 11px;
    padding-bottom: 11px;
}
.threeColTable .table-div-body .rc-draggable-list-draggableRow:not(:last-child){ border-bottom: 1px solid #cccccc; }
.threeColTable .table-div-body .third-part{ display: inherit; }
.threeColTable .table-div-body .third-part .dropdown,
.threeColTable .table-div-body .third-part .table_text{
    margin: auto;
    align-items: center;
    justify-content: center;
}
.drag-list-row{
    position: relative;
    padding-left: 25px;
}
.threeColTable .table-div-body .third-part.drag-list-row:hover .list-drag {
    display: block;
    visibility: visible;
}
.threeColTable .table-div-body .third-part.drag-list-row .list-drag {
    position: absolute;
    top: 0;
    left: 15px;
    margin: auto;
    bottom: 0;
    width: 5px;
    height: 22px;
}
.threeColTable .drop-style2 button.dropdown-toggle i {
    vertical-align: middle;
    margin-top: 0;
}
.mileston-content-outer{ position: relative; }
.mileston-content-outer span.scrollLeftBtn,
.mileston-content-outer span.scrollRightBtn {
    top: 15px;
}
.mileston-content-outer span.scrollRightBtn { right: -25px; }
.smooth-dnd-container.horizontal{ overflow: hidden; }
.card-info-tabs #task-comment-form .pb-container.loading .pb-button {
    border-width: 3px !important;
    background: transparent !important;
    border-color: {{$color2}} !important;
    border-style: solid;
    border-radius: 50%;
    width: 26px !important;
    height: 26px !important;
    padding: 0px !important;
}
.milestone-content-child-box header{ background-color: {{$transprancy1}}; }
.Select-input {
    width: 100% !important;
    height: 32px !important;
    float: left;
}
.Select-input > input{ width: 100% !important; }
.comment-data .commenter span { color: #333; font-weight: 600; }
.comment-data .date{
    text-align: right;
    font-size: 12px;
}
body.ReactModal__Body--open {
    overflow: hidden;
    padding-right: 17px;
}
.mCS-3d-dark.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar,
.mCS-3d-dark.mCSB_scrollTools .mCSB_dragger.mCSB_dragger_onDrag .mCSB_dragger_bar,
.mCS-3d-dark.mCSB_scrollTools .mCSB_dragger:active .mCSB_dragger_bar,
.mCS-3d-dark.mCSB_scrollTools .mCSB_dragger:hover .mCSB_dragger_bar,
.mCS-3d.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar,
.mCS-3d.mCSB_scrollTools .mCSB_dragger.mCSB_dragger_onDrag .mCSB_dragger_bar,
.mCS-3d.mCSB_scrollTools .mCSB_dragger:active .mCSB_dragger_bar,
.mCS-3d.mCSB_scrollTools .mCSB_dragger:hover .mCSB_dragger_bar {
    background-color: #a9a9a9 !important;
}
.card-info-btn svg {
    width: 18px;
    height: 18px;
}
.DayPicker-Day { color: #333; }
.tag-label-outer {
    margin-bottom: 10px;
    width: 100%;
}
.task-name h5 a,
.table>tbody>tr>td a {
    color: {{$color2}};
    cursor: pointer;
    padding: 0px 5px;
    display: inline-block;
    word-break: break-all;
}
.slide-pane__close {
    background: url(../../public/img/close-icon.png) no-repeat !important;
    background-size: 15px 15px !important;
    background-position: center !important;
    margin-left: 10px !important;
    -webkit-transition: all ease-in-out 400ms;
    -moz-transition: all ease-in-out 400ms;
    -ms-transition: all ease-in-out 400ms;
    -o-transition: all ease-in-out 400ms;
    transition: all ease-in-out 400ms;
}
.slide-pane__close:hover {
    -webkit-transform: rotate(180deg);
    -moz-transform: rotate(180deg);
    -ms-transform: rotate(180deg);
    -o-transform: rotate(180deg);
    transform: rotate(180deg);
}
.slide-pane__close svg{ display: none; }
.Select-arrow-zone{
    z-index: 1;
    background-color: #fff;
}
.custom-select .Select-placeholder, .custom-select .Select--single > .Select-control .Select-value {
    line-height: 34px !important;
    height: 34px;
}
.close-icon {
    background: url(../../public/img/close-icon.png) no-repeat;
    height: 12px;
    width: 12px;
    display: block;
    background-size: 10px;
}
.addNewLink span.closebtn {
    position: absolute;
    right: 0;
    top: 12px;
    cursor: pointer;
}
.statusAssignBtns .svgicon svg path { fill: #9d9d9d !important; }
.userImgUploadBox .picUpload_parent {
    width: 80px;
    height: 80px;
}
.userImgUploadBox .picUpload_parent avatar-image.picUpload_div > div:first-child div:first-child p{ font-size: 10px !important; }
.userImgUploadBox .picUpload_parent avatar-image.picUpload_div>div:not(:first-child) {
    padding: 0px 0px;
    width: 125px;
    margin-left: -17px;
}
.userImgUploadBox .picUpload_parent avatar-image.picUpload_div>div:not(:first-child) div[name="action-con"] {
    min-width: 80px !important;
    margin-left: 0px;
    width: 100% !important;
    text-align: center;
}
.horizontal-scroll{
    max-height:180px;
    overflow: auto;
}
.input-group-btn .pb-container .pb-button{ padding: 5px; }
.picUpload_parent avatar-image.picUpload_div>div:first-child div:first-child p { font-size: 14px !important; }
.picUpload_parent avatar-image.picUpload_div>div:first-child div:nth-last-child(2) svg{
    width:26px !important;
    height: 26px !important;
}
.userFnamePost .designation { margin: 5px 0px; }
.userFnamePost {
    margin: 10px 0px;
    width: 100%;
    padding-left: 20px;
}
.d-inherit{ display: inherit; }
.midHeadingTxt{ margin: auto; }
.midHeadingTxt .text-para{ font-size: 16px; }
.noUserPicTxt {
    position: absolute;
    top: 0;
    left: 0px;
    z-index: 1;
}
.noUserPicTxt #user-img {
    width: 80px;
    height: 80px;
}
.imgUploadeStyle1 .picUpload_parent avatar-image.picUpload_div>div:not(:first-child) {
    background: {{$color2}} !important;
    bottom: -10px;
    margin: 0px -10px;
    padding: 0px;
}
.imgUploadeStyle1 .picUpload_parent avatar-image.picUpload_div>div:not(:first-child)>div>div>div { background: #fff !important; }
.imgUploadeStyle1 a.imgCropUploadClose, .imgUploadeStyle1 a.imgCropUploadDone { color: #fff; }
.imgUploadeStyle1 .picUpload_parent {
    background-color: {{$transprancy2}} !important;
    background-size: cover !important;
}
.imgUploadeStyle1{
    background-color: {{$transprancy2}} !important;
    padding: 10px;
}
.imgUploadeStyle1 .picUpload_parent avatar-image.picUpload_div>div:not(:first-child)>div>div>div span {
    border: 2px solid {{$color2}} !important;
    background-color: #fff !important;
}
.imgUploadeStyle1 .noUserPicTxt #user-img {
    width: 180px;
    height: 180px;
    margin: -1px 0px 0px -1px;
}
.userImgUploadBox .picUpload_parent avatar-image.picUpload_div {
    width: 80px;
    height: 80px !important;
    margin: 0px 0px 0px -1px;
}

/* Project Module Task Filter */
.filter-add-button {
    position: relative;
    width: 100%;
    float: left;
}
.taskProFilter {
    float: left;
    margin-right: 10px;
    position: absolute;
    top: 21px;
    left: 10px;
}
.taskFlterBtn {
    height: 31px;
    width: 31px;
    background: #fff;
    border: 1px solid #f5f5f5;
    border-radius: 100%;
    padding: 5px;
    text-align: center;
    color: #000;
    cursor: pointer;
    position: relative;
    z-index: 2;
    -webkit-box-shadow: 0px 1px 9px -2px rgba(0, 0, 0, 0.20);
    box-shadow: 0px 1px 9px -2px rgba(0, 0, 0, 0.20);
}
.taskFlterBtn svg {
    width: 17px;
    height: 17px;
}
.taskFlterBtn.active svg path{ fill: {{$color2}}; }
.task-filter-popup {
    color: #444;
    padding-top: 10px;
    width: 356px;
    position: absolute;
    top: -11px;
    background: #fff;
    border: 1px solid #f1f1f1;
    left: -11px;
    z-index: 1;
}
.task-filter-head {
    padding: 5px 50px 13px 60px;
    border-bottom: 1px solid #f1f1f1;
    font-size: 16px;
    font-weight: 600;
    position: relative;
}
.task-filter-footer {
    padding: 10px 30px 10px 30px;
    border-top: 1px solid #f1f1f1;
    font-size: 16px;
    font-weight: 600;
    position: relative;
}
.taskProFilter .task-filter-footer .form-actionBtns {
    position: relative;
    text-align: right;
}
.task-filter-footer .form-actionBtns span.btn {
    min-width: auto;
    padding: 5px 15px;
    margin-right: 10px;
}
.task-filter-head .close-icon {
    position: absolute;
    right: 10px;
    top: -4px;
    bottom: 0;
    margin: auto;
    border-left: 1px solid #e1e1e1;
    padding: 13px 10px 13px 25px;
    width: 30px;
    height: 30px;
    background-size: 12px;
    background-position: center;
    cursor: pointer;
}
.task-filter-body {
    padding: 10px 0px;
    max-height: 500px;
    overflow: auto;
}
.filter-dates-list,
.filter-block {
    border-bottom: 1px solid #f1f1f1;
    padding: 10px 15px;
}
.filter-block:last-child{ border-bottom: none; }
.task-filter-body .mCSB_inside>.mCSB_container { margin-right: 15px; }
.heading-txt{
    font-size: 14px;
    color: #444;
    font-weight: 600;
    margin-bottom: 5px;
    padding-right: 25px;
    position: relative;
    text-transform: uppercase;
}
.filter-dates .cal-icon svg {
    width: 20px;
    height: 20px;
}
.filter-dates span.cal-icon {
    display: inline-block;
    vertical-align: middle;
    margin-right: 10px;
    cursor: pointer;
}
.filter-dates {
    margin: 2px 0px;
    float: left;
    width: 100%;
}
.filter-block .heading-txt .close-icon,
.filter-dates-list .heading-txt .close-icon {
    float: right;
    width: 18px;
    height: 18px;
    opacity: 0.5;
    cursor: pointer;
    background-size: 10px;
    background-position: center;
    margin-top: 3px;
    position: absolute;
    right: 0;
    top: 0;
}
.filter-tags {
    padding-left: 0;
    width: 100%;
    margin-bottom: 0;
}
.filter-tags li {
    margin: 2px 0px;
    display: inline-block;
    min-width: 31.33%;
    padding: 0px 1%;
    float: left;
}
.filter-tags li input[type="checkbox"]{
    position: relative;
    margin-right: 10px;
}
.filter-tags li input[type="checkbox"]:before{
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 16px;
    height: 16px;
    background: #fff;
    border: 1px solid #e1e1e1;
}
.filter-tags li input[type="checkbox"]:checked:after{
    content: '';
    position: absolute;
    left: 3px;
    top: 4px;
    width: 10px;
    height: 5px;
    border-width: 0px 0px 1px 1px;
    border-style: solid;
    border-color: {{$color2}};
    -webkit-transform: rotate(-45deg);
    -moz-transform: rotate(-45deg);
    -ms-transform: rotate(-45deg);
    -o-transform: rotate(-45deg);
    transform: rotate(-45deg);
}
.filter-tags li span.tagName {
    background-color: #124e61;
    color: #fff;
    padding: 2px 8px;
    font-size: 12px;
    border-radius: 2px;
    max-width: 60px;
    width: 100%;
    display: inline-block;
    vertical-align: text-top;
    word-break: break-word;
}
.setting-opt-menu ul li span.switch-li {
    position: relative;
    left: 0;
    width: 100%;
    padding-left: 40px;
    height: auto;
    background: transparent;
    float: left;
}
.colorCode{
    width: 14px;
    height: 14px;
    background: #124e61;
    display: inline-block;
    margin-right: 5px;
    border-radius: 2px;
    margin-top: -3px;
    vertical-align: middle;
}
.slide-pane_from_right {
    -webkit-transform: translateX(100%);
    -moz-transform: translateX(100%);
    -ms-transform: translateX(100%);
    -o-transform: translateX(100%);
    transform: translateX(100%);
}
.slide-pane {
    -webkit-transition: -webkit-transform 0.5s !important;
    -moz-transition: -moz-transform 0.5s !important;
    -ms-transition: -ms-transform 0.5s !important;
    transition: -webkit-transform 0.5s;
    -o-transition: transform 0.5s;
    transition: transform 0.5s !important;
    transition: transform 0.5s, -webkit-transform 0.5s, -moz-transform 0.5s, -ms-transform 0.5s, -o-transform 0.5s !important;
    will-change: transform !important;
    float: right !important !important;
}
.ReactModal__Content,
.ReactModal__Content--after-open {
    -webkit-transform: scale(1) rotateX(0)  !important;
    -moz-transform: scale(1) rotateX(0)  !important;
    -ms-transform: scale(1) rotateX(0)  !important;
    -o-transform: scale(1) rotateX(0)  !important;
    transform: scale(1) rotateX(0)  !important;
    -webkit-transition: all 250ms ease-in;
    -moz-transition: all 250ms ease-in;
    -ms-transition: all 250ms ease-in;
    -o-transition: all 250ms ease-in;
    transition: all 250ms ease-in;
}
.slide-pane__overlay{ width: 100%; }
.dropdown ul.dropdown-menu {
    max-height: 200px;
    overflow: auto;
}
.mileston-content-outer .DayPicker-Day { font-size: 12px; }
#addTabModal .modal-dialog { width: 450px; }
#addTabModal  .btn-default {
    color: #fff;
    background-color: {{$color2}};
    border-color: {{$color2}};
}
.validation-message { font-size: 12px; }
.drop-style1.dropdown a.site-color.no-style{ word-break: break-word; }
.repdAgenda_topic.all-agenda-topics li span.cke_reset { width: 100%; }
.DayPicker-Day:focus, .DayPicker-Day:hover {
    outline: none;
    background-color: {{$color2}} !important;
    color: #fff;
}
.past{ color:#E1E1E1 }
.dashboard-commission .inline-form-sec.site-bg-color2{ background: #fff !important; }
.all-msgs-container .doc-attach-form section.row {
    margin-left: 0;
    margin-right: 0;
}
.all-msgs-container .doc-attach-form section.row>.col-xs-12{
    padding-left: 0px;
    padding-right: 0px;
}
.uploadProImgDiv .uploadProfile-img {
    border: 1px solid #e5e4e5;
    background-size: contain !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
}
.DayPicker{ font-size: 14px !important; }
.DayPicker-NavButton.DayPicker-NavButton--prev { left: 10px; }
.DayPicker-NavButton.DayPicker-NavButton--next { right: 10px; }
.DayPicker .DayPicker-Day {
    border-radius: 0;
    text-align: center;
    font-size: 13px;
}
.select-cover .DayPickerInput input {
    background: transparent;
    width: 100%;
    border-radius: 4px;
    color: #333333;
    border: 1px solid #e9e9e9;
    -webkit-box-shadow: none;
    box-shadow: none;
    height: 31px;
    padding: 4px 24px 4px 12px;
}
.select-cover .DayPickerInput .DayPickerInput-OverlayWrapper{ z-index: 1; }
.css-glk0q1, [data-css-glk0q1]{
    background: url('../../public/img/multiply.png') no-repeat center !important;
    background-size: 18px !important;
}
select:focus::-ms-value {
    background: transparent !important;
    color: #000;
}
.mCSB_container { top: 0px !important; }
.css-1vm9g5e, [data-css-1vm9g5e]{ height: 50px; }
.login-pass input::-ms-expand{ display: none; }
input::-ms-clear,
input::-ms-reveal,
select::-ms-expand { display: none !important;}
select{
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}
default-selectbox{
    border: none !important;
    color: {{$color1}};
}
.default-selectbox option{ color: #444; }
.modal-body img[src=''] { display: none; }
.has-error + .text-danger { font-size: 12px; }
.default-selectbox-outer {
    display: inline-block;
    position: relative;
}
.default-selectbox-outer i {
    color: {{$color2}} !important;
    position: absolute;
    right: 5px;
    top: 11px;
    font-size: 11px;
}
.chat-form .Select-control .Select-arrow-zone{ display: none; }
.start_col .panel{ margin-bottom: 15px; }
.meeting-timedetail .date-view label { cursor: pointer; }
.select-cover .DayPickerInput .DayPickerInput-OverlayWrapper >div{
    box-shadow: none !important;
    margin-bottom: 0px !important;
    paddin-bottom: 0px !important;
}
.select-cover .DayPickerInput .DayPickerInput-OverlayWrapper .DayPicker-Day, .DayPicker-WeekNumber {
    padding: 3px;
    font-size: 12px;
}
.select-cover .DayPickerInput .DayPickerInput-OverlayWrapper .DayPicker-Caption {
    height: 30px;
    line-height: 30px;
    padding: 0px 25px;
    font-size: 12px;
}
.popsonresize .modal-body img{ width: auto !important; }
.underline-link{
    color: #0f5c56 !important;
    text-decoration: underline;
}
.select-cover .DayPicker-NavButton {
    width: 16px !important;
    height: 16px !important;
    background-size: 6px 8px !important;
}
.all-agenda-topics.clearfix.repdAgenda_topic textarea.form-control {
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    border: 1px solid #ccc;
}
textarea{ resize: none; }
.agenda_list_li .search-commission-outer {
    margin: 0px -80px 0px 20px;
    display: inline-block;
}
.agenda_list_li .s earch-commission-outer .site-bg-color2 { background: #fff; }
.agenda_list_li .search-c ommission-outer .container.dsearch-form { padding: 0px !important; }
.agenda_list_li .search-commission-outer .container.dsearch-form form {
    background: #f7f7f7;
    border: 1px solid #e5e4e5;
    border-radius: 5px;
}
.editTxt-textarea { margin: 5px -80px 5px 20px; }
.status-box { float: left; }
.imgUploadeStyle1 .picUpload_parent avatar-image.picUpload_div>div:not(:first-child) > div > div {
    width: 100% !important;
    text-align: center;
    margin: 5px 0px !important;
    padding: 0px 25%;
}
.userImgUploadBox .picUpload_parent avatar-image.picUpload_div>div:not(:first-child) > div > div {
    padding: 0px !important;
    width: 80px !important;
}
.userImgUploadBox .picUpload_parent avatar-image.picUpload_div>div:not(:first-child)>div>div>div { margin-left: -12px; }
.userImgUploadBox .picUpload_parent avatar-image.picUpload_div>div:not(:first-child)>div>div>div {
    background: {{$color2}} !important;
    height: 3px !important;
}
.userImgUploadBox .picUpload_parent avatar-image.picUpload_div>div:not(:first-child)>div>div>div span {
    background: {{$color2}} !important;
    border: 1px solid white !important;
    top: -5px !important;
    width: 12px !important;
    height: 12px !important;
}
.userImgUploadBox a.imgCropUploadClose, .userImgUploadBox a.imgCropUploadDone {
    color: {{$color2}};
    font-size: 13px;
    font-weight: 400;
}
#setting-icons{ padding-right: 0px; }

/** Imporvent v4 **/
.next-meeting-div .linkBtn {
    position: relative;
    padding-left: 20px;
}
.next-meeting-div .linkBtn span.linkBtn-icon {
    position: absolute;
    left: 0;
    top: 0;
}
.next-meeting-div .linkBtn span.linkBtn-icon i { padding: 0px 5px; }
.border-top-none{ border-top: none !important; }
.note-textarea{ background-color: #e5e4e5; }
.discussion-form .lable-icon-pb{
    margin: 0px 2px;
    vertical-align: middle !important;
}
.lable-icon-pb svg{
    width: 25px;
    height: 25px;
    float: left;
}
.lable-icon-pb svg path{ fill: #9d9d9d; }
.lable-icon-pb.active svg path{ fill: {{$color2}}; }
.lable-icon-pb.blue svg path{ fill: #4F71D0; }
.icon-20 svg{
    width: 20px;
    height: 20px;
    float: left;
}
.discussion-form .lable-icon-pb.icon-20 svg{
    width: 17px;
    height: 17px;
}
.discussion-form .lable-icon-pb svg {
    width: 22px;
    height: 22px;
}
.reverse {
    -webkit-transform: rotate(180deg);
    -moz-transform: rotate(180deg);
    -ms-transform: rotate(180deg);
    -o-transform: rotate(180deg);
    transform: rotate(180deg);
}
.txt-count{
    margin-left: 5px;
    font-size: 18px;
    line-height: 18px;
    vertical-align: middle;
    font-weight: normal;
}
.notes-wrapper {
    position: relative;
    z-index: 1;
    width: 100%;
    border: 1px solid #e5e4e4;
}
.notes-boxdiv{
    width: 100%;
    margin: 5px 0px;
}
.notes-box-inner {
    border: 1px solid #e5e4e5;
    -webkit-box-shadow: 3px 3px 6px -4px rgba(0, 0, 0, 0.28);
    -moz-box-shadow: 3px 3px 6px -4px rgba(0, 0, 0, 0.28);
    box-shadow: 3px 3px 6px -4px rgba(0, 0, 0, 0.28);
}
.notes-boxdiv .left-box {
    border-right: 1px solid #e5e4e5;
    min-width: 25px;
    background-color: #f9f9f9;
}
.notes-boxdiv .right-box {
    padding: 10px;
    width: -webkit-calc(100% - 25px);
    width: -moz-calc(100% - 25px);
    width: calc(100% - 25px);
}
.notes-boxdiv .left-box .inner{ width: 50px }
.note-text-block {
    background: #e5e4e5;
    border: 1px solid #dfdfdf !important;
    box-shadow: none !important;
}
a.delete-box-btn {
    float: right;
    padding: 5px;
    border: 1px dashed #ccc;
    display: inline-block;
    width: 42px;
    height: 42px;
    text-align: center;
    border-radius: 2px;
    cursor: pointer;
}
a.delete-box-btn i {
    font-size: 30px;
    color: #adadad;
}
.notes-wrapper-left { border-right: 1px solid #e5e4e4; }
.notes-wrapper-left, .notes-wrapper-right { padding: 10px 15px; }
.notes-boxdiv .left-box span.drag-dots-icon { margin: 5px; }
.notes-boxdiv .left-box svg path { fill: {{$color2}}; }
.notes-boxdiv .right-box .note-by, .notes-boxdiv .right-box .note-datetime {
    font-size: 13px;
    color: #888888;
}
.notes-boxdiv .right-box .note-datetime { text-align: right; }
.note-control-header {
    width: 100%;
    vertical-align: middle;
    float: left;
    margin-bottom: 5px;
}
.close-section-btn {
    background: url(../../public/img/close-icon.png) no-repeat !important;
    background-size: 12px 12px !important;
    background-position: center !important;
    display: inline-block;
    width: 30px;
    height: 20px;
    vertical-align: middle;
    border-right: 1px solid #727272;
    margin-right: 10px;
    cursor: pointer;
    opacity: 0.5;
    filter: alpha(opacity=50);
}
.tab-menu-section ul.nav.nav-pills { border-bottom: 1px solid #f1f1f1; }
.tab-menu-section ul.nav.nav-pills li a {
    background: #f1f1f1;
    color: #333;
    min-width: 120px;
    text-align: center;
    border-radius: 3px 3px 0px 0px;
}
.tab-menu-section ul.nav.nav-pills li.active a {
    background-color: {{$color2}};
    color: #fff;
}
.scroll-menu-outer {
    display: inline-block;
    width: 100%;
    overflow: auto;
}
.custom-scroll-menu {
    border-bottom: 1px solid #e5e4e5;
    min-width: 100%;
}
.custom-scroll-menu li span { cursor: pointer; }
.custom-scroll-menu li span.svgicon { padding: 5px 10px; }
.custom-scroll-menu li span.svgicon svg{ width: 22px; height: 22px; }
.custom-scroll-menu li span.texttab,
.custom-scroll-menu li a {
    white-space: nowrap;
    color: #444;
    font-size: 14px;
    background: #f9f9f9;
    margin: 0px 1px;
    border-radius: 3px 3px 0px 0px;
    display: inline-block;
}
.custom-scroll-menu li span.texttab{ padding: 6px 15px; }
.custom-scroll-menu li a { padding: 6px 15px; }
.custom-scroll-menu li span.svgicon svg path { fill: #444 !important; }
.custom-scroll-menu li span.texttab.active,
.custom-scroll-menu li a.active,
.custom-scroll-menu li a:hover {
    color: #fff;
    background: {{$color2}};
}
.custom-scroll-menu li span.svgicon.active svg path{ fill: {{$color2}} !important; }
.no-req-label::after{
    content: '';
    display: none !important;
}
.agenda-actions li .left-icon-35 svg, .all-agenda-topics li .left-icon-35 svg{
    width: 35px;
    height: 35px;
    display: inline-block;
    vertical-align: middle;
}
.p-relative{ position: relative; }
.p-absolute{ position: absolute; }
.p-absolute-tl{
    position: absolute;
    left: 0;
    top: 0;
}
.custon-selectbox span.Select-arrow {
    position: absolute;
    right: 8px;
    top: 0;
    bottom: 0;
    margin: auto;
    align-self: center;
    align-content: center;
}
.or-separate{
    text-transform: uppercase;
    color: #888888;
    font-size: 14px;
    padding: 0px 12px;
    position: relative;
    margin-right: 5px;
}
.txt-btn-color2{ color: {{$color2}}; }
span.or-separate:before, .or-separate:after {
    content: '';
    position: absolute;
    width: 8px;
    height: 1px;
    background: #ccc;
    top: 0;
    bottom: 0;
    margin: auto;
    align-items: center;
}
span.or-separate:before{ left: 0; }
span.or-separate:after{ right: 0; }
.all-agenda-topics li span.text-success {
    position: absolute;
    right: 10px;
    top: 10px;
}
.notes-boxdiv .right-box .note-datetime span.time { vertical-align: inherit; }
.DayPicker .DayPicker-Day { padding: 5px !important; }
.task-list .table>tbody>tr:first-child>td { border-top: none; }
.trainee-view { 
    margin-right: 10px;
    display: inline-block;
}
.trainee-view svg {
    width: 42px;
    height: 42px;
    vertical-align: middle;
}
.div-table-col {
    width: 16.66%;
    float: left;
    padding: 5px 8px;
    display: table;
    text-align: center;
    word-break: break-word;
}
.div-thead{ background-color: #f7f6f7;}
.div-thead .div-th{
    font-weight: 600;
    padding: 11px 14px;
    position: relative;
    min-height: 42px;
}
.div-thead .div-table-col.div-th:not(:last-child):after {
    content: '';
    position: absolute;
    right: 0;
    width: 1px;
    height: 70%;
    background: #ccc;
    top: 0;
    bottom: 0;
    margin: auto;
    align-items: center;
}
.div-table-col.div-td{
    min-height: 50px;
    border-top: 1px solid #d0d0d0;
}
.div-table{
    border-width: 2px 1px 1px 1px;
    border-style: solid;
    border-color: #d0d0d0;
    border-top-color: {{$color2}}
}
.div-table-col.div-td span{
    display: table-cell;
    vertical-align: middle;
}
.table-default-icon tr td .svgicon svg path { fill: #888888; }
.table-default-icon tr td .svgicon.active svg path { fill: {{$color2}}; }
.div-table-col.div-td span.td-drag-icon {
    background-image: url(../../public/img/vertical-dots.png);
    background-repeat: no-repeat;
    background-position: center;
    background-size: auto 16px;
    position: absolute;
    left: 5px;
    width: 10px;
    height: 20px;
    top: 0;
    display: none;
    bottom: 0;
    margin: auto;
}
.div-tbody-tr:hover .div-table-col.div-td span.td-drag-icon{ display: block; }
.div-td.drag-icon-field { padding-left: 20px; }
/* improvement v4 scrum 2 */
/*.meeting-timedetail:first-child span.date.show.site-color .month {
    background: green;
    color: #fff !important;
}
.meeting-timedetail:first-child + .meeting-timedetail span.date.show.site-color .month {
    background: orange;
    color: #fff !important;
}*/
span.month.site-color {
    padding: 3px 5px;
    max-width: 132px;
    width: 100%;
    display: inherit;
    border-radius: 3px;
    margin: auto;
}
.btn-h42{ height: 42px; }
.selected-options-list a.list-group-item { padding: 5px 30px 5px 18px; }
.selected-options-list a.list-group-item button {
    background: transparent;
    border: none;
    border-left: 1px solid #ccc;
    float: right;
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto;
    height: 100%;
    line-height: 20px;
    font-size: 14px;
    color: {{$color2}};
}
.selected-options-list .rc-draggable-list-draggableRow:not(first-child) { margin-top: 2px; }
.full-label{ width: 100%; }
.skill-file-inputfiles .input-group input.form-control {
    width: 100%;
    max-width: inherit;
}
.skill-file-inputfiles {
    width: 370px;
    display: inline-block;
    vertical-align: middle;
}
.edit-rfield {
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto;
    height: 24px;
    width: 24px;
    line-height: 24px;
}
.skill-file-field + .edit-rfield { top: 20px; }
.rotate-icon {
    width: 13px;
    height: 13px;
    display: inline-block;
    background: red;
    -webkit-transform: rotate(45deg);
    -moz-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    -o-transform: rotate(45deg);
    transform: rotate(45deg);
    border-radius: 2px;
}
.onrotate-icon{ background-color: red; }
.offrotate-icon{ background-color: green; }
.custom-checkbox {
    display: inline-block;
    position: relative;
    width: 20px;
    height: 20px;
}
.custom-checkbox input[type="checkbox"] {
    z-index: 1;
    position: relative;
    opacity: 0;
    width: 20px;
    height: 20px;
    margin: 0;
    cursor: pointer;
}
.custom-checkbox input[type="checkbox"] + .custom-check-span{ cursor: pointer; }
.custom-checkbox input[type="checkbox"] + .custom-check-span:before {
    content:'';
    top: 0;
    left: 0;
    position: absolute;
    width: 20px;
    height: 20px;
    border: 1px solid #ccc;
    background-color: #fff;
    left: 0;
    top: 0;
}
.custom-checkbox input[type="checkbox"]:checked + .custom-check-span::after{
    content: '';
    position: absolute;
    width: 10px;
    height: 6px;
    border-width: 0px 0px 1px 1px;
    border-color: {{$color2}};
    -webkit-transform: rotate(-45deg);
    -moz-transform: rotate(-45deg);
    -ms-transform: rotate(-45deg);
    -o-transform: rotate(-45deg);
    transform: rotate(-45deg);
    top: 5px;
    left: 5px;
    border-style: solid;
}
.skill-action-btns {
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto;
    height: 24px;
}
.skill-action-btns .svgicon svg {
    width: 20px;
    height: 20px;
}
.deactive svg path{ fill: #888 !important; }
.scal-slider {
    max-width: 300px;
    margin: auto;
}
.skill-date{
    position: absolute;
    z-index: 3;
    background: #fff;
    top: 100%;
    right: 0;
}
.skill-table-textarea textarea {
    width: 100%;
    max-width: 100% !important;
    height: 70px !important;
}
.mandatoryBox{
    background: {{$transprancy7}};
    border: 1px solid {{$color2}};
    padding: 15px 15px 15px 125px;
    margin-bottom: 20px;
    position: relative;
    min-height: 150px;
    max-height: 150px;
    overflow: auto;
}
.mandatoryBox .input-group .form-control { background-color: #fff; }
.mandatoryBox .form-group label { color: #333; }
.mandatoryBox .skill-file-inputfiles {
    width: 500px;
    vertical-align: middle;
}
.mandatoryBox .skill-file-field .pb-container {
    margin-top: 0px;
    vertical-align: middle;
}
.skill-filename{ font-size: 13px; }
.skilltab-percentage .file-text-box {
    width: 100%;
    padding: 5px 5px 5px 10px;
    border-radius: 3px 0px 0px 3px;
    display: inline-block;
    vertical-align: middle;
    float: none;
    max-width: inherit !important;
}
.percentage-sign {
    padding: 6px 10px;
    display: inline-block;
    background: #fff;
    border-color: #e5e4e5;
    border-style: solid;
    border-width: 1px 1px 1px 1px;
    vertical-align: middle;
    position: absolute;
    right: 0px;
    z-index: 2;
}
.orange{ color: orange; }
.input-group-btn button {
    margin-left: -1px;
    position: relative;
    z-index: 1;
}
.skill-inputfield{
    width: 100%;
    min-height: 20px;
}
.skill-inputfield input {
    width: 100%;
    max-width: 100% !important;
}
.skill-inputfield button{ height: 34px; }
.input-group-btn>.btn{ z-index: 2; }
.skill-inputfield span.input-group-btn { padding-left: 10px; }
.skill-table-textarea span.input-group-btn button,
.skill-inputfield span.input-group-btn button,
.skilltab-percentage span.input-group-btn button{
    border-radius: 0px 3px 3px 0px !important;
}
.table-row-loader svg{
    width: 35px;
    height: auto;
}
.skill-tab-select .select-cover {
    max-width: 300px;
    width: 100%;
}
.skill-tab-select .select-cover select { max-width: 100%; }
.skill-action-btns .svgicon,
.skill-date-field .calendarIcon,
.skill-date-field span.svgicon {
    cursor: pointer;
}
select.default-selectbox:focus { outline: none; }
.skill-table-textarea,
.skill-inputfield,
.skilltab-percentage{
    width: 100%;
}
.custom-responsive-table .react-bs-table{ overflow: auto; }
.custom-responsive-table .react-bs-table table td,
.custom-responsive-table .react-bs-table table td .text-left {
    text-align: center !important;
}
.custom-responsive-table .react-bs-table table td .skill-file-inputfiles {
    width: inherit;
    padding: 0 !important;
}
.custom-responsive-table .react-bs-table table td .pr-50 {
    padding-right: 0 !important;
    padding-left: 0px !important;
}
.custom-responsive-table .react-bs-table table td .rc-slider-disabled{ background: transparent; }
.mandatoryBox span.mandatorySvgIcon {
    position: absolute;
    left: -120px;
    top: 10px;
}
.mandatoryBox span.mandatorySvgIcon svg{
    width: 110px !important;
    height: 110px !important;
}
.mandatoryBox span.mandatorySvgIcon svg path{ fill: #fff !important; }
.div-tbody li {
    display: block;
    width: 100%;
}
.react-bs-container-body{ overflow: inherit !important; }
.skill-table-textarea button.btn, .skill-inputfield button.btn, .skilltab-percentage button.btn { min-width: inherit; }
.custom-responsive-table .table>thead>tr>th, .custom-responsive-table .table>tbody>tr>td {
    font-size: 13px;
    word-break: break-word;
}
.v-middle{ vertical-align: middle; }
.table-data-shorting .form-group{ margin-bottom: 12px; }
.table-data-shorting .form-group .select-cover select{
    height: 30px;
    padding: 3px 12px;
}

/* OPS Migration CSS */
.countcircle {
    display: inline-block;
    width: 25px;
    height: 25px;
    background: #b7b7b7;
    text-align: center;
    border-radius: 25px;
    line-height: 25px;
    margin-right: 10px;
    font-size: 14px;
    color: #fff;
    position: absolute;
    left: 0;
    top: 0;
}
.progress-bar-info,.progress-bar-success { background-color: {{$transprancy7}} !important; }
.progress-bar-success { background-color: #3c763d !important; }
/*.btn-wrap-h40 button.pb-button, .btn-h40 { height: 40px; }*/
.border-rright-none{
    border-top-right-radius:0px;
    border-bottom-right-radius: : 0px;
}
.border-rleft-none{
    border-top-left-radius: 0px;
    border-bottom-left-radius: 0px;
}
.table-action select.select-box {
    height: 30px;
    padding: 3px 30px 3px 10px;
}
.table-action .select-cover + .input-group-btn button.btn-primary {
    height: 30px;
    padding: 2px 5px;
    min-width: 50px;
}
.disable-line {
    opacity: 0.50;
    filter: alpha(opacity=50);
}
@media (min-width: 768px){
    .table-action {
        margin-bottom: -39px;
        max-width: 50%;
    }
    .table-group-action{ margin-bottom: -64px; }
}
.countcircle-wrap {
    position: relative;
    padding: 3px 5px 3px 40px;
    display: inline-block;
    width: 100%;
}
.meeting-report-table .svgicon {
    display: inline-block;
    margin-right: 5px;
    vertical-align: middle;
    cursor: pointer;
}
.meeting-report-table .svgicon svg{
    width: 28px;
    height: 28px;
}
.meeting-report-table button.btn { min-width: inherit; }
/* End OPS Migration CSS */

.drop-style1 ul.dropdown-menu li a { white-space: normal; }
.customize-label-fields .help .svgicon { margin-left: 10px; }
.customize-label-fields .help .svgicon svg {
    width: 24px;
    height: 20px;
    padding: 2px 4px;
}
.customize-label-data { margin-bottom: 10px; }
.customize-label-data p {
    margin-bottom: 5px;
    font-size: 13px;
    color: #444;
}
#addTabModal { background: rgba(0,0,0,0.5); }
.new_topic_line input[type="text"],
.new_topic_line button{ vertical-align: middle; }
.cal-add.green svg path{ fill: green; }
.lock-icon.svgicon svg {
    width: 18px;
    height: 18px;
    vertical-align: middle;
    display: block;
}
.no-cursor, .no-cursor * { cursor: default !important; }
.dynamicSkill-blockname {
    font-size: 14px;
    line-height: 14px;
    margin-top: 5px;
}
.customSkillField-block{
    margin: 10px 0px;
    float: left;
    width: 100%;
}
.dynamicSkill-blockdata { padding-right: 80px; }
.dynamicSkill-blockdata .scal-slider {
    max-width: 500px;
    margin: 10px 0px;
}
.dynamicSkill-blockdata .skill-inputfield input { height: 36px; }
.meeting-timedetail span.month.green{
    background-color: green !important;
    color: #fff !important;
}
.meeting-timedetail span.month.orange{
    background-color: orange !important;
    color: #fff !important;
}
.countcircle-wrap {
    position: relative;
    padding: 3px 5px 3px 40px;
    display: inline-block;
    width: 100%;
}
.meeting-report-table .svgicon {
    display: inline-block;
    margin-right: 5px;
    vertical-align: middle;
    cursor: pointer;
}
.meeting-report-table .svgicon svg{
    width: 28px;
    height: 28px;
}
.meeting-report-table button.btn { min-width: inherit; }
.mobile-multi{ margin-left: 50px; }
span.count {
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto;
    height: 20px;
    min-width: 23px;
}
span.count i { font-size: 22px; }
span.pointer.r otate-icon.onrotate-icon.disable { cursor: none; }
.rc-slider-tooltip-placement-top { z-index: 999 !important; }
.scrtabs-tab-scroll-arrow {
    border: 1px solid #e5e4e5 !important;
    background-color: transparent;
    color: {{$color2}};
    padding: 0;
    height: 20px;
    text-align: center;
    width: 20px;
    border-radius: 20px;
    top: 0;
    bottom: 0;
    margin-top: 6px;
}
.customSkillField-blockdata .rc-slider {
    max-width: 100%;
}
.rc-slider-dot-active,
.rc-slider-handle,.rc-slider-handle:hover{
    border-color: {{$color2}} !important;
}
.rc-slider-track{ background-color: {{$color2}} !important; }
.rc-slider-tooltip-placement-top .rc-slider-tooltip-arrow{ border-top-color: {{$color2}} !important; }
.rc-slider-tooltip-inner {
    background-color: {{$color2}} !important;
    padding-left: 5px !important;
    padding-right: 5px !important;
}
.rc-slider-mark-text::before {
    content: '';
    position: absolute;
    width: 1px;
    height: 7px;
    background: #d9d9d9;
    top: -5px;
    left: 0;
    right: 0;
    margin: auto;
}
.rc-slider-handle {
    z-index: 1;
    background-color: {{$color2}} !important;
}
.rc-slider-handle {
    margin-top: -4px !important;
    width: 12px !important;
    height: 12px !important;
}
.rc-slider-mark-text {
    color: #888 !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    white-space: nowrap;
}
.rc-slider-mark-text-active { color: {{$color2}} !important; }
form#add-skill .form-field-wrap:nth-child(3n+1),
form#add-skill .form-field-wrap :nth-child(3n+1) {
    clear: left;
}
#add-skill .form-group.required-field .select-cover i {
    top: 12px;
    bottom: inherit;
}
.map-control-btns {
    position: absolute;
    right: 0;
    top: 0;
}
.map-control-btns a.edit-map{
    margin-right: 10px;
    display: inline-block;
}
.map-control-btns a.edit-map img { width: 24px; }
.file-icon{ min-height: 35px; }
.cke_top { padding: 1px 0px 2px !important; }
.cke_toolgroup { margin: 1px 3px 0px 3px !important; }
@media (min-width: 768px){
    .scrollmodal-headbody .modal-content {  max-height: 90vh;}
    .scrollmodal-headbody .modal-body{
        max-height: calc(90vh - 48px);
        overflow: auto;
    }
}
.table-progressive-btn {
    min-width: 80px;
    vertical-align: middle;
    margin-top: 0;
}
.table-progressive-btn.loading .pb-button {
    width: 30px;
    height: 30px;
    border-width: 3px;
}
.table-progressive-btn .pb-container .pb-button svg {
    height: 32px;
    width: 32px;
    left: 12px;
    top: 12px;
}
.pb-container.disabled .pb-button {
    cursor: not-allowed;
    background-color: #dddddd;
    color: #444;
}
.skilltab-percentage .form-control {
    width: 120px;
}
.map-control-btns span.svgicon svg {
    width: 20px;
    height: 20px;
}
.task-list.autocomplete-form .divLeft_space35 { position: relative !important; }
.alert-member-datalist span.input-group-btn button {
    min-width: auto;
    border-radius: 3px !important;
}
.right.card-info-tabs {
    max-height: 400px;
    overflow: auto;
}
.first2-col-fixed-table thead tr th:nth-child(1),
.first2-col-fixed-table tbody tr td:nth-child(1){
    position: sticky;
    left: 0;
    z-index: 1;
}
.first2-col-fixed-table thead tr th:nth-child(2),
.first2-col-fixed-table tbody tr td:nth-child(2){
    position: sticky;
    left: 150px;
    z-index: 2;
}
.first2-col-fixed-table thead tr th:nth-child(1),
.first2-col-fixed-table thead tr th:nth-child(2){
    background-color: #f7f7f7;
}
.first2-col-fixed-table tbody tr td:nth-child(1),
.first2-col-fixed-table tbody tr td:nth-child(2){
    background-color: #fff;
}
.table-hover>tbody>tr:hover td{ background-color: #f5f5f5; }
.custom-responsive-table .skill-file-field .file-icon {
    padding: 30px 0px 0px 0px;
    background-position: top center;
    background-size: 25px;
    padding-left: 0;
}
.custom-responsive-table .rc-slider {
    height: 6px;
    padding: 2px 0;
    max-width: 90%;
}
.custom-responsive-table .rc-slider-dot {
    width: 6px;
    height: 6px;
    border-width: 1px;
}
.custom-responsive-table .rc-slider-rail, .custom-responsive-table .rc-slider-track, .custom-responsive-table .rc-slider-step{
    height: 2px;
}
.custom-responsive-table .rc-slider-handle {
    margin-left: -4px !important;
    margin-top: -3px !important;
    width: 8px !important;
    height: 8px !important;
}
.custom-responsive-table .rc-slider-mark { top: 14px; }
.all-agenda-topics.repdAgenda_topic>li.repd-li .show-add span { margin-right: 6px; }
.staff-login-form button[type="submit"].btn-primary { white-space: normal; }
.topic-project .milestone-title-inner { background-color: #f7f7f7; }
.task_single_multi .show-add {
    display: inline-block;
    vertical-align: sub;
    margin-left: 10px;
}
.mandatoryBox span.mandatory_acceptance_icon svg{
    width: 120px !important;
    height: 120px !important;
    margin-left: -6px;
}
.mandatoryBox span.mandatory_acceptance_icon svg path.fillColor2 { fill: {{$transprancy2}} !important; }
.mandatoryBox span.mandatory_acceptance_icon svg path.fillColor3 { fill: {{$color3}} !important; }
.mandatoryBox span.h_title {
    font-size: 16px;
    font-weight: bold;
    color: #000;
}
.v-agling-top, .v-align-top{ vertical-align: top !important; }
.topic-project .DayPicker-Caption > div { font-size: 14px; }
.workshop-choose-field select { height: 30px; }
.workshop-choose-field .form-group { margin-bottom: 11px; }
.label-list-group {
    width: 33%;
    float: left;
}
th.my-th-coloumn,
.custom-responsive-table .table>thead>tr>th {
    padding: 10px 5px !important;
}
.next-meeting-div .dropdown.btn-inline { width: 45%; }
.next-meeting-div .dropdown.btn-inline span{
    display: inline-block;
    height: 28px;
    line-height: 28px;
    vertical-align: middle;
    margin-top: 3px;
}
.next-meeting-div .dropdown.btn-inline .cal-add svg{
    width: 34px;
    height: 34px;
}
.colorPickerBlock { position: relative; }
.colorPickerBlock > div + div { z-index: 3 !important; }
.mandatory-child-block{ min-height: 135px; }
.noborder-table .table>tbody>tr>th, .noborder-table .table>tfoot>tr>th, .noborder-table .table>tbody>tr>td, .noborder-table .table>tfoot>tr>td {
    border: none;
}
.DayPicker-NavButton.DayPicker-NavButton--prev::after,
.DayPicker-NavButton.DayPicker-NavButton--next::after {
    content: '';
    position: absolute;
    width: 8px;
    height: 8px;
    border-right: 1px solid #000;
    border-top: 1px solid #000;
    top: 6px;
}
.DayPicker-NavButton.DayPicker-NavButton--prev::after{
    -webkit-transform: rotate(-135deg);
    -moz-transform: rotate(-135deg);
    -ms-transform: rotate(-135deg);
    -o-transform: rotate(-135deg);
    transform: rotate(-135deg);
    left: 6px;
}
.DayPicker-NavButton.DayPicker-NavButton--next::after{
    -webkit-transform: rotate(45deg);
    -moz-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    -o-transform: rotate(45deg);
    transform: rotate(45deg);
    right: 6px;
}
.mandatoryBox span.h_title, .file-text-details {
    width: 100%;
    display: inline-block;
}
.file-text-details { margin-bottom: 5px; }
.file-text-details a { color: {{$color2}}; }
.panel-primary>.panel-heading{ border-bottom-color: {{$color2}}; }
.hide-border::before{ display: none; }
.table-style1 .dropdown ul.dropdown-menu + span {
    display: inline-block;
    vertical-align: middle;
    height: 26px;
}
.table-style1 .dropdown ul.dropdown-menu + span i {
    vertical-align: middle;
    height: 26px;
}
.doodle-space{ margin-left:3px; }
.table>tbody>tr>td .show a{ padding: 0px 0px; }
.th-innner, .th-inner, .th-inner-with-icon {
    max-width: 100%;
    display: inline-block;
    vertical-align: middle;
    position: relative;
}
.th-innner, .th-inner{ padding-right: 20px; }
.th-inner-with-icon{ padding-right: 40px; }
.react-bs-table .react-bs-container-footer>table>thead>tr>th .th-innner + span.order, .react-bs-table .react-bs-container-header>table>thead>tr>th .th-innner + span.order,
.react-bs-table .react-bs-container-footer>table>thead>tr>th .th-inner + span.order, .react-bs-table .react-bs-container-header>table>thead>tr>th .th-inner + span.order {
    position: relative;
    margin: auto auto auto -18px;
    vertical-align: middle;
}
.react-bs-table .react-bs-container-footer>table>thead>tr>th .th-inner-with-icon + span.order,
.react-bs-table .react-bs-container-header>table>thead>tr>th .th-inner-with-icon + span.order{
    right: 20px;
}
.react-bs-table .react-bs-container-footer>table>thead>tr>th, .react-bs-table .react-bs-container-header>table>thead>tr>th {
    vertical-align: middle;
    word-break: break-all;
}
.fillColor2{ fill: {{$transprancy2}} !important; }
.pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover {
    background-color: {{$color2}};
    border-color: {{$color2}};
}
.pagination>li>a, .pagination>li>span,
.pagination>li>a:focus, .pagination>li>a:hover, .pagination>li>span:focus, .pagination>li>span:hover {
    color: {{$color2}};
}
button.btn-large {
    height: 42px;
    font-size: 16px;
}
button.w-100 {  width: 100%; }
.cancelBtn {
    padding: 5px 10px;
    height: 36px;
    font-size: 14px;
}
.react-bs-table-pagination span.dropdown.react-bs-table-sizePerPage-dropdown {
    margin-bottom: 0px;
    display: block;
}
.valing-mid{ vertical-align: middle !important; }
.dropdown-menu>li>span>a, .dropdown-menu>li>a {
    display: inline-block; 
    width: 100%;
    text-decoration: none;
}
.text-muted, .text-muted:hover, .text-muted:focus{
    color: #777;
}
.mobile-multi {
    padding-left: 80px;
}
.form-left-right-row .form-left-sec .col-xs-12.col-sm-6.form-group.mb-10 {
    margin-bottom: 0 !important;
    margin-top: 9px;
}
.brower-text-view{
    text-align:center;
    padding-bottom:9px;
}
.w-100{ width:100% }
.btn-lg { 
    height: 42px;
    line-height: 26px;
}
#user-profile-fields-page .DayPicker.skill-date,
.filter-edit-content .filter-condition-row .DayPicker.skill-date {
    position: relative;
}
.radio label {
    display: inline-block;
    vertical-align: middle;
    position: relative;
}
.text-italic{
    font-style: italic;
}
.taskNameUpdateform .form-actionBtns .addNew-taskBtn {
    min-width: 70px;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    margin-right: 10px;
    height: 36px;
}
.custom-responsive-table table thead tr td, .custom-responsive-table table tbody tr td { min-width: 180px; }
.daycal-time .cal-time-block select.form-control { width: 100px; }
.modal-backdrop.in { position: fixed; }
.chooseblockItem img { width: 100% !important; }
.remove-column i{ 
    width: 16px;
    height: 16px;
    border: 1px solid {{$color2}};
    border-radius: 50%;
    line-height: 14px;
    text-align: center;
    font-size: 10px;
    background-color: #fff;
}
.autocomplete-list-li {
    padding: 3px 12px !important;
    display: inline-block;
    width: 100%;
    cursor: pointer;
}
.increse-list-value {
    display: inline-block;
    width: 100%;
    margin: 3px 0;
}
.rounded-add-btn,
.add-list-btn,
.remove-list-btn{
    background: #fff;
    border: 1px solid {{$color2}};
    color: {{$color2}};
    padding: 2px;
    vertical-align: middle;
    border-radius: 20px;
    cursor: pointer;
}
.rounded-add-btn:hover, .rounded-add-btn:focus,
.add-list-btn:hover, .add-list-btn:focus,
.remove-list-btn:hover, .remove-list-btn:focus{
    border: 1px solid {{$color2}} !important;
    color: {{$color2}};
    outline: none;
}
.rounded-add-btn,
.add-list-btn{
    font-size: 14px;
    height: 30px;
    width: 30px;
}
.remove-list-btn{
    font-size: 10px;
    line-height: 13px;
    height: 18px;
    width: 18px;
}
.increse-list-value .field-value {
    display: inline-block;
    vertical-align: top;
    padding-right: 5px;
    background-color: #fff;
    line-height: 24px;
}
.autocomplete-form input + div { max-height: 200px !important; }
.help .fixed-info-icon {
    top: 7px !important;
    margin: 0 auto !important;
}
.custom-autocomplete input { background-color: transparent; }
.custom-autocomplete i { margin: 15px auto 0 auto !important; }
.add-list-controller .add-list-btn{
    position: absolute;
    right: 35px;
    top: 2px;
}
.add-list-controller { padding-right: 80px !important; }
.div-table-col.div-td{
    height: 50px;
    position: relative;
}
.list-group-item-action .drag-icon, .drag-icon-candidate{
    background: url('../../public/img/vertical-dots.png') no-repeat center;
    position: absolute;
    width: 18px;
    height: 100%;
    background-size: auto 16px;
    left: 0px;
    top: 0px;
    cursor: move;
}
.all-msgs-container { margin-bottom: 50px; }
.allAgendaList .all-agenda-topics .list_number,
.agenda-actions li, .all-agenda-topics li span.order-no {
    cursor: inherit;
}
.drop-style1 .dropdown-toggle>i{ padding-left: 10px; }
table .dropdown ul.dropdown-menu {
    right: 0;
    left: inherit;
}
.btn:active, .btn.active {
    -webkit-box-shadow: none;
    box-shadow: none;
}
.rc-slider-disabled { background-color: transparent; }
.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
    cursor: not-allowed;
    background-color: #f7f7f7;
}
.table>tbody>tr>td a.btn.btn-primary { color: #fff !important; }
.table>tbody>tr>td .dropdown-menu>li a{ color: #333 !important; }
.btn-secondary{color: #fff !important}
.btn-secondary, button.btn-primary.gray {
    background: gray !important;
    border-color: gray !important;
}
.default-form .form-group.col-xs-12.col-md-6:nth-child(2n+1) {  clear: left; }
.xl-btn{ min-width: 220px; }
.autocomplete-input input + div {
    max-height: 200px !important;
    overflow: auto !important;
}
.text-label, .text-value, .skill-inputfield,
.dynamicSkill-blockname {
    word-break: break-word;
}
.btn-primary[disabled] { border-color: transparent !important; }
.side-panner-inner-body{
    padding: 15px;
    display: inline-block;
    width: 100%;
}
.radio-btn label input[type="radio"] { visibility: hidden; }
.radio-btn{ position: relative; }
.radio-btn, .radio-btn span, .radio-btn input{ cursor: pointer; }
.radio-btn:not(:last-child){ margin-right: 15px; }
.radio-btn label span::before {
    content: '';
    position: absolute;
    left: 0;
    top: 3px;
    width: 15px;
    height: 15px;
    border: 1px solid #9a9a9a;
    border-radius: 25px;
}
.radio-btn label input[type="radio"]:checked + span::after {
    content: '';
    position: absolute;
    width: 9px;
    height: 9px;
    background: {{$color2}};
    border-radius: 20px;
    left: 3px;
    top: 5px;
}
.panel-style-default .react-bs-table .react-bs-container-footer>table>thead>tr>th,
.panel-style-default .react-bs-table .react-bs-container-header>table>thead>tr>th{
    word-break: break-word;
}
.panel-style-default table tr td svg { width: 20px; }
.email-preview .modal-body img {
    width: 100% !important;
    max-width: 600px;
}
.card-info-tabs textarea.form-control{ height: 75px; }
.skill-table-textarea input.form-control { height: 34px; }
.fa-eye.icon::before {
    font-weight: 400;
    cursor: pointer;
}

/*****/
.fix-header .modal-header {
    height: 30px;
    padding: 2px 15px;
}
.auto-scroll-popup .modal-dialog {
    position:absolute;
    top:50% !important;
    transform: translate(0, -50%) !important;
    -ms-transform: translate(0, -50%) !important;
    -webkit-transform: translate(0, -50%) !important;
    margin:auto 5%;
    width:90%;
    max-height:80%;
    left: 0;
    right: 0;
    margin: auto;
    max-width: 850px;
}
.auto-scroll-popup .modal-content,
.auto-scroll-popup .scroll-modal-content {
    min-height:100%;
    border-radius: 0px;
    width: 100%;
}
.auto-scroll-popup .modal-body {
    overflow-y:auto;
  }
.auto-scroll-popup .modal-footer {
    position:absolute;
    bottom:0;
    left:0;
    right:0;
    padding: 5px 15px;
}
#image-crop-modal .modal-content-wrap{
    height: 100%;
    padding-bottom: 47px;
}

/********* For Event Modules **********/
.rdo-icon.border-none label { border: none; }
.auto-complete-input input {
    width: 100%;
    height: 34px;
    padding: 5px 12px;
    font-size: 14px;
    border: 1px solid #dfdfdf;
    box-sizing: border-box;
    outline: none !important;
    box-shadow: none !important;
    border-radius: 5px;
}
.autocomplete-dropdown-container{
    border-width: 0 1px 0px 1px;
    border-color: rgba(0,0,0,0.08);
    border-style: solid;
}
.skill-table-textarea .autocomplete-dropdown-container span,
.autocomplete-dropdown-container span{
    padding: 3px 5px;
    background: #f9f9f9 !important;
    width: 100%;
    display: inline-block;
    font-size: 12px;
}
.autocomplete-dropdown-container div{ font-size: 12px; }
.autocomplete-dropdown-container .suggestion-item span,
.autocomplete-dropdown-container .suggestion-item--active span{
    border-bottom: 1px solid rgba(0,0,0,0.08);
}
.autocomplete-dropdown-container span:hover{ background: #f5f5f5 !important;
}
.svg-radio svg path,
.rdo-icon.svg-radio input[type="radio"] + label svg path {
    fill: #c3c3c3 !important;
}
.rdo-icon.svg-radio input[type="radio"]:checked + label svg path { fill: {{$color2}} !important;}
.rdo-icon.svg-radio label {
    width: 120px;
    height: 70px;
    border-width: 0px;
}
.rdo-icon.svg-radio label svg {
    width: 110px;
    height: 60px;
}
.skill-file-inputfiles .input-group .input-group-btn span{
    vertical-align: middle;
    display: inline-block;
}
button.browse {
    border-top-right-radius: 4px !important;
    border-bottom-right-radius: 4px !important;
}
.form-control-xs{ height: 30px; }
button.edit-pencil-btn {
    padding: 5px;
    display: inline-block;
    font-size: 12px;
    color: #fff;
    box-shadow: none;
    outline: none;
    border: 0;
    height: 30px;
    width: 40px;
    min-width: 30px;
}
button.edit-pencil-btn i.fa {
    font-size: 12px;
}
.setting-opt-menu .Select-input > input{
    height: 30px;
    line-height: 18px;
    box-sizing: border-box !important;
    padding: 6px 0px; 
}
.setting-opt-menu .Select-control{
    height: 30px;
}
.setting-opt-menu .Select-multi-value-wrapper {
    height: 28px;
    box-sizing: border-box;
}
.daycal-time .inline-time-label label {
    min-width: 110px;
    text-align: right;
    padding-right: 10px;
}
/***** End Event Css *****/
/***** For Messenger Module *****/
.messenger-left-column {
    float: left;
    width: 100%;
}
.messenger-heading {
    display: inline-block;
    width: 100%;
    padding: 9px 0px 8px 0px;
    border-bottom: 2px solid {{$color2}};
}
.messenger-heading h4 {
    margin: 0;
    color: {{$color2}};
    font-size: 16px;
}
.messenger-left-list-wrap {
    display: inline-block;
    width: 100%;
    padding-top: 10px;
    padding-bottom: 10px;
}
.msg-group-heading {
    padding: 3px 25px 3px 0px;
    color: {{$color2}};
    font-size: 14px;
    position: relative;
    font-weight: 600;
}
.messenger-left-group {
    float: left;
    width: 100%;
}
.msg-topic {
    width: 100%;
    float: left;
}
.msg-topic ul {
    list-style: none;
    padding-left: 0px;
    margin-bottom: 5px;
}
.msg-topic ul li{
    display: inline-block;
    width: 100%;
    position: relative;
    padding: 2px 0px;
}
.msg-topic ul li a {
    padding: 1px 25px 1px 15px;
    color: #9c9c9c;
    font-size: 14px;
    display: inline-block;
    line-height: 16px;
    position: relative;
    width: 100%;
    text-decoration: none;
}
.msg-group-heading button.transparent-btn {
    position: absolute;
    right: 0px;
    color: #ccc;
    font-size: 10px;
    height: 20px;
}
.circle-plus-btn svg {
    width: 17px;
    height: 17px;
}
.msg-topic ul li a span.fa {
    font-size: 10px;
    position: absolute;
    left: 3px;
    top: 4px;
}
.user-status {
    position: relative;
    padding-left: 15px;
}
.user-status-sign {
    width: 10px;
    height: 10px;
    border: 2px solid #ccc;
    display: inline-block;
    border-radius: 10px;
    position: absolute;
    top: 8px;
    left: 5px;
}
.user-status-sign.online{
    background-color: green;
    border-color: green;
}
.user-status-sign.offline{
    border-color: #ccc;
}
.msg-user-name {
    color: #9c9c9c;
    cursor: pointer;
}
.unread-msg-count {
	background: #f50000;
    color: #fff;
    font-size: 10px;
    min-width: 22px;
    height: 15px;
    display: inline-block;
    border-radius: 25px;
    line-height: 11px;
    text-align: center;
    padding: 2px 2px;
    position: absolute;
    content: '';
    right: 25px;
    top: 5px;
    font-weight: bold;
}
.msg-r-icons-head button.all-like-btn {
    position: relative;
    margin-left: 10px;
}
.msg-r-icon { cursor: pointer; }
.msg-left-sidebar{
    width: 20%;
    border-right: 1px solid #e4e4e4;
}
.msg-right-content{ width: 80%; }
.msg-reply-btn {
    font-size: 12px;
    margin-left: 5px;
    padding: 2px 4px;
}
.msg-reply-btn i{
    font-size: 14px;
    margin-right: 2px;
}
#setting-icons svg {
    width: 24px;
    height: 24px;
    vertical-align: middle;
}
#setting-icons ul li svg path{ fill: #ffffff !important; }
.midHeadingTxt .form-control,
.doc-attach-form .form-control{
    height: 36px;
}
.midHeadingTxt .file-group .btn,
.doc-attach-form .file-group .btn {
    padding: 6px 10px;
    height: 36px;
}
.user-name-sec .user-name{ font-weight: bold; }
.msg-content p:last-child { margin-bottom: 0; }
.flex-wrap { flex-wrap: wrap; }
.msg-r-icons-head .comment-file-btns svg {
    width: 20px;
    height: 20px;
}
.msg-r-icons-head .comment-file-btns .btn-type1 span{
    bottom: 2px;
}
.msg-content a.file-icon {
    background-size: 20px;
    padding: 2px 0px 2px 25px;
    min-height: 24px;
}
/***************/
.txt-btn-group .form-control { padding: 10px 10px; }
.dropdown-menu li a { cursor: pointer; }
.msg-topic ul li:hover { background: #f6f6f6; }
.justify-content-end {
    -ms-flex-pack: end!important;
    justify-content: flex-end!important;
}

/***===== Video Meeting Css =====***/
.yellowColor { color: orange; }
select.form-control[name="start_time"], select.form-control[name="end_time"] {
    width: 155px;
    padding-right: 25px;
    /*height: 36px;*/
}
.react-dropdown-select {
    min-width: 155px !important;
    padding: 5px 15px !important;
    border-radius: 4px !important;
    border-color: #dfdfdf !important;
}
.z-index-99{ z-index: 99 !important; }
span.react-dropdown-select-item.disableClass { color: #c7c7c7; }
.react-dropdown-select-dropdown span.react-dropdown-select-item {
    padding: 1px 10px;
    font-size: 13px;
}
.react-dropdown-select-dropdown span.react-dropdown-select-item:hover{ background-color: #f7f7f7; }
/*.required-field span.switch-li label::after {
    position: absolute;
    right: -13px;
}*/
.required-field span.switch-li label::after{
    display: none;
}
.required-field span.switch-li::after {
    position: absolute;
    right: 0px;
    content: " *";
    color: #af0000;
    font-size: 22px;
    line-height: 3px;
    display: inline-block;
    position: relative;
    top: 4px;
    margin-left: 4px;
}
.vertical-align-bottom > *{ vertical-align: bottom; }
.btn-primary.disabled-link, .btn-primary.disabled-link:hover, .btn-primary.disabled-link:focus,
.disabled-link, .disabled-link:hover, .disabled-link:focus {
    background: #ddd !important;
    border-color: #ddd !important;
    opacity: 1;
    cursor: no-drop;
}
.my-auto{
    margin-top: auto;
    margin-bottom: auto;
}
.filter-condition-col .skill-date,
.date-field .skill-date {
    position: relative;
    display: inline-block;
}
.full-range-slider{
    padding-left: 5px;
    padding-right: 5px;
}
.full-range-slider .scal-slider { max-width: 100%; }
.doodle-meeting-btn button {
    font-size: 12px;
    padding: 5px 10px;
}
/**********OPS New Interface***********/
.contain-sideicon-menu {
    padding-left: 65px;
    position: relative;
}
.sidebar-icon-strip{
    width: 50px;
    position: fixed;
    left: 0;
    top: 0;
    height: 100%;
    background: #f7f6f7;
    border-right: 1px solid #e5e5e5;
    z-index: -1;
}
.sidebar-icon-wrap{
    max-width: 1140px;
    position: fixed;
    left: 0;
    right: 0;
    margin: auto;
    height: 100%;
}
.sidebar-icon-menu {
    width: 50px;
    top: 0px;
    left: 0px;
    height: 100%;
    z-index: 999;
    padding-top: 145px;
    padding-bottom: 65px;
}
.sidebar-icon-menu-inner{
    height: 100%;
    width: 100%;
}
.p-fixed{ position: fixed; }
#header {
    position: relative;
}
.dropdown.open .horizontal-icons-menu{
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    left: 100%;
    top: -1px;
    padding: 0px;
    background: #f7f6f7;
    border-radius: 0;
    margin: 0;
    box-shadow: none;
    border-color: #e5e5e5 !important;
    border-style: solid !important;
    border-width: 1px 1px 1px 0px !important;
}
.sidebar-icon-menu-inner > ul > li {
    border-bottom: 1px solid #e5e5e5;
    padding: 8px 0px;
}
.horizontal-icons-menu li {
    display: inline-block;
    padding: 7px 6px 6px 6px;
    border-left: 1px solid #e5e5e5;
}
.horizontal-icons-menu li span{
    padding: 0px 0px !important;
    margin: 5px 10px;
    float: left;
}
.horizontal-icons-menu li span svg{ vertical-align: baseline; }
.svg-gray svg path, .svg-gray svg circle, .svg-gray svg polygon{ fill: lightgray !important; }
.svg-gray.active svg path, .svg-gray.active svg circle, .svg-gray.active svg polygon{ fill: #222222 !important; }
.border-none{ border: none !important; }
.svg-16{width: 16px; height: 16px;}
.svg-20{width: 20px; height: 20px;}
.svg-30{width: 30px; height: 30px;}
.svg-35{width: 35px; height: 35px;}
.svg-40{width: 40px; height: 40px;}
.svg-45{width: 45px; height: 45px;}
.svg-16 svg, .svg-20 svg, .svg-30 svg, .svg-35 svg, .svg-40 svg, .svg-45 svg{
    width: 100% !important;
    height: 100% !important;
}
.list-style-none{ list-style: none; }
.align-items-end{
    -ms-flex-align: end!important;
    align-items: flex-end!important;
}   
h3{ font-size: 20px; }
.tab-menu-content .action_alert.d-inline.mr-10 {
    display: block;
    background-color: red;
    color: #fff;
    font-weight: normal;
    padding: 6px 10px;
    border-radius: 4px;
}
.rc-slider-disabled {
    background-color: transparent !important;
    opacity: 0.35;
}
.__react_component_tooltip.type-dark {
    background-color: {{$color2}};
    padding: 5px 10px;
    font-size: 12px;
    max-width: 200px;
}
.__react_component_tooltip.type-dark.place-top:after { border-top-color: {{$color2}}; }
.__react_component_tooltip.type-dark.place-bottom:after { border-bottom-color: {{$color2}}; }
.__react_component_tooltip.type-dark.place-left:after { border-left-color: {{$color2}}; }
.__react_component_tooltip.type-dark.place-right:after { border-right-color: {{$color2}}; }
.__react_component_tooltip.disable-tooltip.type-dark { background-color: #e5e5e5 !important; }
.__react_component_tooltip.disable-tooltip.type-dark.place-top:after { border-top-color: #e5e5e5 !important; }
.__react_component_tooltip.disable-tooltip.type-dark.place-bottom:after { border-bottom-color: #e5e5e5 !important; }
.__react_component_tooltip.disable-tooltip.type-dark.place-left:after { border-left-color: #e5e5e5 !important; }
.__react_component_tooltip.disable-tooltip.type-dark.place-right:after { border-right-color: #e5e5e5 !important; }
/**********************/
.listing-open .repd-list-head {color: {{$color2}};}
.react-bs-table-pagination .row { margin-top: 0 !important; }
ul.react-bootstrap-table-page-btns-ul.pagination { margin-bottom: 0; }
.react-bs-table-pagination {
    margin-top: 10px;
    margin-bottom: 5px;
}
.inner-modal{
    width: 100%;
    padding: 20px;
}
.text-ellipsis-80 {
    display: block;
    width: 80px;
    padding: 0 !important;
}
.review-popup{ min-width: 450px;}
.review-popup .qual-decision-data-outer,
.table-review-box .expert-box-count {
    width: 100%;
    border: none;
}
.green.meeting-three-dot svg path { fill: #00af5f !important; }
.svg-white path{ fill: white !important; }
.table-review-box .expert-box-count{ padding: 0px; }
.th-inner-with-icon.vid-meet-th { padding-right: 45px; }
.react-bs-table .react-bs-container-header>table>thead>tr>th .th-inner-with-icon.vid-meet-th + span.order{ right: 25px; }
.meeting-three-dot svg{
    width: 23px !important;
    height: 22px !important;
}
.vid-meet-th .meeting-three-dot svg{
    width: 20px !important;
    height: 19px !important;
}
.btn.btn-default {
    color: #333 !important;
    background-color: #fff !important;
    border-color: #ccc !important;
}
.DayPickerInput input {
    height: 34px;
    width: 100%;
    border: 1px solid #dfdfdf;
    border-radius: 4px;
    padding: 5px 10px;
}
.select-yellow .react-dropdown-select-content > span{
    color: #FFC107;
}
.repdAgenda_topic .cke_inner{
    padding: 5px;
}
.repdAgenda_topic .cke_top {
    background-color: #ffffff !important;
    border-radius: 10px;
}
.repdAgenda_topic .cke_reset, .repdAgenda_topic .cke_wysiwyg_frame, .repdAgenda_topic .cke_wysiwyg_frame >*, .repdAgenda_topic .cke_wysiwyg_div{
    background-color: #e8e8e8 !important;
}

/**/
.accordion-arrow-btn {
    width: 20px !important;
    height: 20px;
    border-radius: 20px;
    vertical-align: middle;
    text-align: center;
    margin: 4px 4px 4px 10px;
}
.accordion-arrow-btn.collapsed i.inactive.active,
.accordion-arrow-btn i.inactive {
    color: #fff;
    width: 6px;
    height: 6px;
    border-width: 2px 2px 0 0;
    border-style: solid;
    border-color: #fff;
    display: inline-block;
    position: absolute;
    left: -1px;
    right: 0;
    margin: auto;
    top: 0;
    bottom: 0;
    -webkit-transform: rotate(45deg);
    -moz-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    -o-transform: rotate(45deg);
    transform: rotate(45deg);
}
.accordion-arrow-btn i.inactive.active{
    -webkit-transform: rotate(135deg);
    -moz-transform: rotate(135deg);
    -ms-transform: rotate(135deg);
    -o-transform: rotate(135deg);
    transform: rotate(135deg);
    left: 0;
}
.double-line-text {
    font-size: 12px;
    line-height: 14px;
}
.task_single_multi h5{
    font-size: 14px;
}
.icon-top-count span.count {
    border-width: 1px;
    border-style: solid;
    border-color: #4a4a4a;
    min-width: 15px;
    height: 15px;
    border-radius: 25px;
    font-size: 9px;
    background: #fff;
    line-height: 13px;
    text-align: center;
    right: -8px;
    top: -7px;
}
.border-color1-transprancy1{
    border-color: {{$color1_transprancy1}};
}
.event-icon{ 
    width: 43px !important;
    height: 23px !important;
}
.lf-img-content-popup{
    padding: 0px;
}
.enabled-disabled-features-inner {
    background-image: url(../../public/img/idea-sharing.png);
}
.more-features-inner{
    background-image: url(../../public/img/pluses-transparent.png);
    background-size: cover;
}
.helpdesk-bg-inner{
    background-image: url(../../public/img/question-mark.png);
    background-size: 200px auto;
}
.enabled-disabled-features-inner, .more-features-inner, .helpdesk-bg-inner{
    background-position: center;
    background-color: {{$color2}};
    background-repeat: no-repeat;
    position: fixed;
    height: 100%;
    left: 0;
    top: 0;
    width: 50%;
    background-size: cover;
    background-position: center;
}
.enabled-disabled-features, .more-features, .helpdesk-bg{
    min-height: 350px;
}
.checkbox-w-label .custom-checkbox{
    position: absolute;
    left: 0;
    top: 1px;
}
.checkbox-w-label input[type="checkbox"]:checked ~ span.label-txt{
    color: {{$color2}};
}
.full-page-modal .modal-dialog{
    max-width: 1200px;
    width: 98%;
}
.content-fit-btn{
    min-width: auto;
}
.msg-actions button.single-like-btn.like-star {
    padding: 0;
    background: transparent;
    position: relative;
    right: inherit;
    color: #444444;
}
.icon-cell-30{
    width: 30px;
}

/* Custom Textarea Dragger*/
#dragger {
    background: #fff;
    width: 40px;
    height: 18px;
    display: block;
    border: 1px solid #e5e5e5;
    text-align: center;
    line-height: 16px;
    border-radius: 8px;
    box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.15);
    margin: -10px auto 0 auto;
    position: relative;
    z-index: 1;
    cursor: pointer;
}
#dragger svg {
    width: 10px;
    height: 10px;
}
#dragger svg path {
    fill: #8e8e8e !important;
}
.dropdown-search-list {
    position: absolute;
    min-width: 240px;
    top: 100%;
}
.dropdown-search {
    width: 100%;
    border: 1px solid #dfdfdf;
    padding: 8px;
    position: absolute;
    top: 0;
    left: 0;
}
.dropdown-search-list .dropdown-menu {
    position: relative;
    width: 100%;
    /*padding-top: 34px !important;*/
    overflow: hidden !important;
    max-height: 300px !important;
    border-radius: 0px;
}
.dropdown-search-list .dropdown-menu .list-group{
    max-height: 300px;
    overflow: auto;
}
.add-feaure-btn svg{
    width: 29px;
    height: 30px;
}
.content-fit-sidebar{
    position: absolute;
    width: 1170px;
    left: 0;
    right: 0;
    top: 0;
    margin: auto;
    height: 100%;
    background: #ccc;
}
.content-fit-sidebar .sidebar-icon-menu {
    top: 0;
    left: auto;
}
.convo-reply-sec {
    padding-left: 38px;
}
.convo-reply-sec #user-img{
	width: 25px;
	height: 25px;
	font-size: 10px;
    position: absolute;
    left: 0;
    top: 2px;
}
.convo-reply-sec .msg-content{
	padding-left: 35px;
}
.convo-reply-sec .reply-text-editor {
    margin: 10px 0px 10px 35px;
}
.coversation-close-btn {
    display: inline-block;
    margin-bottom: 10px;
    margin-right: 1px;
    width: 16px;
    height: 16px;
    line-height: 16px;
}
.reply-reaction{
	border: 1px solid transparent;
	padding: 3px 5px;
	border-radius: 5px;
    font-size: 13px;
}
.reply-reaction:hover{
	border-color: #e5e5e5;
	background-color: #f7f7f7;
}
/*** Instant Messenger ***/
.msg-left-sidebar {
    height: 90vh;   
}
.message-left-container {
    width: 23%;
    border-right: 1px solid #e4e4e4;
}
.message-right-container {
    width: 77%;
}
.messages-header {
    font-size: 20px;
    border-bottom: solid 1px #e4e4e4;
    padding-left: 18px;
}
.allmsgs-cover {
  overflow-x: hidden;
  overflow-y: auto;
  height: calc(68vh - 128px);
  padding-left: 0px;
  box-sizing: border-box;
  padding-top: 16px;
  background: #f9f9f9;
  margin-bottom: 93px;
  position: relative;
}
.name-time {padding-left: 40px;}
.msg-content { padding-left: 30px; }
.single-msg { padding-left: 10px !important; }
.single-msg-cover:hover { background-color: #f3f3f3; }
.msg-actions {
  border: 1px solid #e4e4e4;
  border-radius: 5px;
  padding: 5px;
  text-align: center;
  margin-top: -28px;
  background: #ffffff;
  display: none;
  max-width: 145px;
  float: right;
}
.single-msg:hover .msg-actions { display: block; }
.msg-topic ul li.selected-message-category,
.msg-topic ul li.selected-message-category:hover {
  background-color: #5ba8e8;
}
.msg-topic ul li a { padding: 1px 25px 1px 20px; }
.msg-topic ul li{ font-size: 0; }
.msg-topic ul li >*{ font-size: 14px; }
.messenger-left-group {
  padding-left: 5px;
  min-height: 25px;
}
.msg-group-heading{
  padding: 0px 25px 0px 20px;
  font-weight: normal;
  color: #424242;
}
.convo-heading-container {
  position: relative;
  padding: 0px 35px 0px 20px;
}
.convo-heading-container .direct-add, .topic-group .direct-add{
  right: 10px;
  top: 2px;
}
.convo-heading {
  font-size: 14px;
  color: #424242;
  font-weight: 600;
}
.left-acc {
  position: absolute;
  left: 0;
  top: 3px;
  cursor: pointer;
}
.convo-heading-container .left-acc { top: 0; }
.left-acc i {
  font-size: 20px;
  width: 12px !important;
  color: grey;
  line-height: 20px;
}
.messenger-left-column .Select-menu-outer {
  position: absolute !important;
  left: 0 !important;
  top: 20 !important;
}
.msg-topic ul li.selected-message-category a,
.msg-topic ul li.selected-message-category li:hover a {
  color: #fff !important;
  text-decoration: none;
}
.selected-message-category a:hover { text-decoration: none; }
.msg-menu-heading {
  display: inline-block;
  width: 100%;
  padding-bottom: 8px;
  padding-right: 14px;
}
.msg-menu-left {
  /* height: calc(80vh);
  overflow: auto; */
}
.msg-menu-top {
  float: left;
  width: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 11px;
  flex-direction: column;
  border-bottom: 2px solid #e4e4e4;
  padding-bottom: 8px;
  color: #8b8b8b;
}
.msg-menu-top :hover { cursor: pointer; }
.msg-menu-active {
  color: #109dcc;
  border-bottom: 2px solid #109dcc !important;
}
.left-menu-msg-usr{ padding : 0px; }
.left-menu-item { padding: 3px 45px 3px 23px;}
.msg-menu-usericon {
    width: 24px !important;
    height: 24px !important;
    display: inline-block !important;
    font-size: 10px;
    line-height: 12px;
}
.left-msgmenu-username { font-size: 14px; }
.msg-group-heading a {
  color: #442244;
  font-weight: normal;
  text-decoration: none;
}
.left-menu-msg-usr.user-status .user-status-sign {
  top: 8px;
  left: 0px;
}
.messeger-text-editor .ql-editor, .reply-text-editor .ql-editor {
  min-height: 50px !important;
  max-height: 200px;
  overflow: hidden;
  overflow-y: auto;
  overflow-x: hidden;
  background-color: #ffffff;
  border-radius: 5px 5px 0px 0px;
}
.messeger-text-editor {padding-top: 0px;}
.ql-picker {display: none !important;}
.reply-text-editor button svg path, .messeger-text-editor button svg path {
  fill: transparent !important;
}
.reply-text-editor button svg, .messeger-text-editor button svg {
  width: 100%;
}
.reply-text-editor .ql-toolbar.ql-snow .ql-formats, .messeger-text-editor .ql-toolbar.ql-snow .ql-formats {
  margin-right: 5px;
}
.reply-text-editor .ql-toolbar.ql-snow .ql-formats span.ql-header svg,
.messeger-text-editor .ql-toolbar.ql-snow .ql-formats span.ql-header svg {
  height: 14px;
  width: 14px;
  margin-top: -7px;
  top: 50%;
}
.messeger-text-editor .ql-toolbar.ql-snow .ql-formats span.ql-header svg .ql-stroke {
  stroke: #909090;
}
.ql-toolbar.ql-snow .ql-picker .ql-picker-label {
  border-color: #ccc;
  border-radius: 3px;
  font-size: 10px;
}
.ql-toolbar.ql-snow .ql-picker.ql-expanded .ql-picker-options { font-size: 10px; }
.ql-snow .ql-picker-options .ql-picker-item {
  cursor: pointer;
  display: block;
  padding-bottom: 2px;
  padding-top: 2px;
  line-height: normal;
}
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="1"]::before { font-size: 1.65em; }
button.single-like-btn.like-star {
  right: 100%;
  left: inherit;
  padding: 7px 5px 7px 10px;
  border-radius: 17px 0px 0px 18px;
  font-size: 10px;
}
.message-right-container .messages-header{
  width: 100%;
}
 .message-right-container .messeger-text-editor{
    background-color: #fff;
    margin-top: 5px;
}
.message-right-container .fixed-editor-message .messeger-text-editor{
    width: calc(100% - 5px);
    margin-left: 5px;
}
.reply-text-editor .ql-toolbar.ql-snow, .messeger-text-editor .ql-toolbar.ql-snow{
    border-width: 1px 0px 0px 0px;
    border-style: solid;
    border-color: #eaeaea;
    background-color: #f7f6f6;
    padding: 5px;
    border-radius: 0px 0px 5px 5px;
}
.left-menu-msg-usr .user-type-icon {
    left: 5px;
    top: 2px;
}
.left-menu-msg-usr .left-acc i{
    font-size: 16px !important;
    width: 12px !important;
    color: #c1c1c1;
    line-height: 20px;
}
.left-menu-msg-usr .user-type-icon i{
    font-size: 11px !important;
    width: 12px !important;
    color: #d0d0d0;
}
/*.coversation-group-list { padding-left: 5px; }*/
.coversation-group-list .left-acc{ left: 5px; }
.direct-add svg, .left-menu-item .delete-btn svg{
    width: 12px;
    height: 12px;
}
.direct-add svg path, .left-menu-item .delete-btn svg path{ fill: #444444 !important; }
.topic-group .direct-add {
    visibility: hidden;
    -webkit-transition: all 0.3s ease-in-out;
    -moz-transition: all 0.3s ease-in-out;
    -ms-transition: all 0.3s ease-in-out;
    -o-transition: all 0.3s ease-in-out;
    transition: all 0.3s ease-in-out;
}
.topic-group .left-menu-item:hover .direct-add,
.topic-group .left-menu-item.active .direct-add {
	visibility: visible;
}
.left-menu-item.active {
    background: #e2e2e2 !important;
    box-shadow: 0px 7px 15px -8px rgba(0, 0, 0, 0.2);
    z-index: 1;
}
.left-menu-item:hover {
    background: #f3f3f3;
}
.topic-list-group .left-menu-item {
    padding-left: 35px;
    padding-right: 45px !important;
}
.topic-list-group .left-menu-item .user-type-icon{
	left: 20px;
}
.message-left-container {
    height: 68vh;
    overflow: auto;
}
.scrollarea .scrollbar-container.vertical {
    width: 4px !important;
}
.scrollarea .scrollbar-container.vertical .scrollbar {
    width: 3px important;
}
.left-menu-item .delete-btn {
    min-width: inherit;
    width: 20px;
    padding: 0px;
    height: 20px;
    right: 5px;
    line-height: 20px;
    display: none;
    top: 4px;
}
.left-menu-item:hover .delete-btn{ display: block; }
.user-list .user-list-item {
    font-size: 14px;
    padding: 1px 5px;
    display: inline-block;
    width: 100%;
    margin-bottom: 5px;
}
.user-list button {
    margin-top: 20px;
    margin-left: 10px;
}
/*.msg-content :empty {
    display: none;
}*/
.custom-scroll{
	scrollbar-color: #555555 #f5f5f5;
	scrollbar-width: thin; 
}
.custom-scroll::-webkit-scrollbar {
	width: 7px;
	background-color: #F5F5F5;
	overflow-y: scroll;
}
.custom-scroll::-webkit-scrollbar-track{
	-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
	background-color: #F5F5F5;
}

.custom-scroll::-webkit-scrollbar-thumb {
	background-color: #000000;
	border: 2px solid #555555;
}
.like-count {
    position: absolute;
    top: -4px;
    left: 51%;
    font-size: 12px;
    border: 1px solid #ccc;
    border-radius: 12px;
    height: 16px;
    line-height: 12px;
    padding: 1px 2px;
    background: #fff;
}
.messages-header span.user-status-sign {
    position: relative;
    top: 0;
    left: 0;
}
.messages-header i {
    font-size: 14px;
    color: #ccc;
}
.editor-upload-btn {
    display: inline-block;
    color: #333 !important;
}
.reply-text-editor, .messeger-text-editor{
	position: relative;
}
.reply-text-editor .editor-upload-btn, .messeger-text-editor .editor-upload-btn,
.reply-text-editor .editor-send-btn, .messeger-text-editor .editor-send-btn {
    color: #333 !important;
    min-width: 80px !important;
    width: inherit !important;
    padding: 2px 5px!important;
    height: 24px !important;
    line-height: 20px;
}
.reply-text-editor .editor-upload-btn svg path, .messeger-text-editor .editor-upload-btn svg path,
.reply-text-editor .editor-send-btn svg path, .messeger-text-editor .editor-send-btn svg path{
	fill: #333 !important;
}
.reply-text-editor .editor-upload-btn span, .messeger-text-editor .editor-upload-btn span,
.reply-text-editor .editor-send-btn span, .messeger-text-editor .editor-send-btn span{
    vertical-align: sub;
    margin-left: 5px;
} 
.msg-actions .svgicon svg path {
    fill: #444 !important;
}
.msg-actions .svgicon:hover svg path,
.msg-actions .svgicon.fill svg path{
    fill: {{$color2}} !important;
}
.reply-item-box .msg-action-btns {
    display: none;
}
.reply-item-box:hover .msg-action-btns {
    display: block;
}
.editor-file-dropdown .dropdown-menu {
    min-width: 200px;
    width: auto !important;
    right: 0;
    left: inherit;
    position: absolute;
    padding: 5px 0px !important;
    bottom: 120%;
    top: inherit;
    left: 10px;
}
.editor-file-dropdown .dropdown-menu li {
    font-size: 13px;
    display: inline-block;
    width: 100%;
    padding: 3px 10px;
}
.editor-file-dropdown .dropdown-menu li:hover{
	background-color: #f7f6f6;
}
.reply-time {
    font-size: 12px;
    color: #b7b7b7;
}
.messeger-text-editor .loader-wrap {
    top: 0;
    bottom: 0;
    margin: auto;
    height: 40px;
}
.messeger-text-editor .loader-wrap .pg-content-loader {
    height: 40px !important;
    text-align: center;
}
.editor-loader .messeger-text-editor,
.editor-loader reply-text-editor{
	min-height: 80px;
}
.messeger-text-editor, .reply-text-editor {
    border: 1px solid #e5e5e5;
    border-radius: 5px;
}
.messeger-text-editor .ql-container,
.reply-text-editor .ql-container{
    border: none !important;
}
.attechment-view{
    width: 150px;
    border: 1px solid #ececec;
    border-radius: 3px;
    margin: 6px 3px 3px 3px;
    height: 45px;
    padding: 5px 5px 5px 5px;
    background-color: #f9f9f9;
}
.attechment-view .attachment{
    width: 35px;
    float: left
}
.attachment-details{
    max-width: 103px;
    float: left;
}
.attachment-details .name {
    margin-top: 9px;
    font-size: 13px;
    font-weight: 700;
}
.remove-btn {
    background: #444444;
    color: #fff;
    font-size: 6px;
    width: 14px;
    height: 14px;
    text-align: center;
    line-height: 12px;
    border-radius: 10px;
    top: -7px;
    border: 1px solid #fff;
    z-index: 1;
    right: -6px;
    display: none;
}
.attechment-view:hover .remove-btn{
    display: block;
}
.uploaded-attachment{
    max-width: 150px;
    width: auto !important;
    border: 1px solid transparent;
    border-radius: 3px;
    height: 40px;
    padding: 2px 5px 2px 2px;
    background-color: #f9f9f9;
    border-color: #e5e5e5;
}
.attachment-control {
    padding: 2px;
    right: 0;
    top: 0;
    display: none;
}
.uploaded-attachment:hover{
    background-color: #f9f9f9;
    border-color: #e5e5e5;
}
.uploaded-attachment:hover .attachment-control{
    display: inline-block;
}
.userlist-popup .modal-dialog {
    width: 400px;
}
.msg-content-docs{
    padding-left: 35px;
}
.attachment-control a {
    background: #f7f6f6;
    width: 20px;
    height: 20px;
    display: inline-block;
    text-align: center;
}
.uploaded-attachment-list .attechment-view {
    max-width: 100%;
    width: 100% !important;
    height: auto;
    margin: 0px;
    border-color: #ececec;
}
.uploaded-attachment-list:hover .attechment-view {
	box-shadow: 0px 1px 5px rgba(0,0,0,0.1);
}
.uploaded-attachment-list .attachment-details {
    max-width: calc(100% - 40px);
    padding: 5px 0px;
}
.attachment-by-date .sendby {
    font-size: 11px;
    font-weight: bold;
    line-height: 13px;
    color: #525252;
    float: left;
}
.attachment-by-date .time {
    font-size: 10px;
    color: #888;
    line-height: 12px;
    float: left;
}
.uploaded-attachment-list .attachment-details .name{
    margin-top: 0px;
}
#topicModal .modal-dialog,
#addDocsModal .modal-dialog {
    max-width: 450px;
}
.uploaded-attachment-list .attechment-view .attachment {
    width: 40px;
}
.uploaded-attachment-list .attechment-view .attachment .file-icon {
    background-size: 35px;
    margin-top: 5px;
}
.workshop-doc-modal .modal-dialog {
    max-width: 1150px !important;
}
#topicModal .modal-header {
    padding: 8px 15px 8px 15px;
    min-height: 38px;
}
#topicModal .modal-header h4 {
    font-size: 16px;
}
.Select-control, .Select-menu-outer{
    font-size: 14px;
}
.left-menu-item{ padding-right: 45px !important;}
.user-type-msg, .history-loading{
    position: sticky;
    left: 0;
    margin: 0;
    color: #b7b7b7;
    padding: 5px 15px;
    width: calc(100% - 10px);
    background: #f9f9f9;
    z-index: 2;
}
.user-type-msg{
    top: -26px;
    font-size: 10px;
}
.history-loading{
	top: -16px;
	font-size: 12px;
}
#site-lang form.login-form {padding: 0;}
#site-lang form li {display: inline-block;}
.mobViewOverlayDiv {
    display: flex !important;
}
.msg-content p {
    word-break: break-word;
    line-height: 14px;
    font-size: 13px;
    margin-bottom: 5px;
}
.fixed-editor-message {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
}
.field-info {
    font-size: 12px;
    color: #bbbbbb;
}
.radio-button-text label span {
    background-color: #b3b3b3;
    border-radius: 4px;
    padding: 8px 15px;
    min-width: 120px;
}
.radio-button-text label input {
    visibility: hidden;
    opacity: 0;
    position: absolute;
}
.radio-button-text label span:before {
    display: none;
}
.radio-button-text label span {
    color: #ffffff;
}
.radio-button-text label input:checked + span {
    background-color: {{$color2}};
}
.inc-dec-btns .increment,
.inc-dec-btns .decrement{
    border: 1px solid;
    width: 20px;
    height: 20px;
    font-size: 10px;
    line-height: 18px;
    padding: 1px 2px;
}
.inc-dec-btns button {
    background: #ffffff;
}
#main-menu .dropdown.drop-menu button {
    line-height: 20px;
    height: 20px;
    border: none !important;
    padding: 0px;
}
#main-menu .dropdown.drop-menu i{
	margin-top: 4px;
}
.label-left.daycal-time .inline-time-label label{
	text-align: left;
}
.inc-dec-btns button[disabled] {
    background: #efefef;
    cursor: not-allowed;
}
.inc-dec-btns span {
    width: calc(100% - 50px);
    display: inline-block;
}
svg circle{
	fill: {{$color2}};
}
.gray-text{
    color: #888888;
}
.main .container{
    max-width: 1170px;
    width: 100%;
    left: 25px;
}
button.disabled-field, button.disabled-field:focus, button.disabled-field:hover {
    opacity: 0.4;
}
.btn.disabled, .btn[disabled], fieldset[disabled] .btn {
    cursor: not-allowed;
    filter: alpha(opacity=4);
    -webkit-box-shadow: none;
    box-shadow: none;
    opacity: .4;
}
.customSkillField-blockdata .skill-inputfield span.text-danger {
    color: #a94442 !important;
}
.flexbox {
    display: flex !important;
}
.drag-handler-icon{
    position: absolute;
    left: -15px;
    top: 0;
    bottom: 0;
    margin: auto;
    width: 18px;
    height: 18px;
    text-align: center;
    cursor: grab;
}
.accordion-layout-menu ul li.has-child ul.sub-menu li ul.sub-menu li a {
  padding: 6px 20px 10px 25px;
}
.drop-style1 .dropdown-toggle{
    word-break: break-all;
}
.d-block{
  display: block;
}
.upload-from-device .file, .upload-from-device .input-group input {
    height: 0px;
    padding: 0;
    border: 0;
}
.choose-file-dropdown ul li{
    list-style: none;
    padding: 8px 10px 8px 40px;
    position: relative;
}
.choose-file-dropdown ul li:hover {
    background-color: #f7f7f7;
}
.choose-file-dropdown ul li span.svgicon{
    position: absolute;
    top: 8px;
    left: 10px;
}
.choose-file-dropdown .choose-file-label-btn {
    background: transparent !important;
    color: #999 !important;
    border: 1px solid #dfdfdf;
    width: calc(100% - 100px);
    text-align: left;
    padding: 6px 15px;
}
.dropdown-toggle-split {
    width: 100px;
    min-width: auto;
    padding: 6px 10px 6px 10px;
}
.dropdown-toggle-split i {
    font-size: 16px !important;
    margin-left: 0 !important;
}
.choose-file-dropdown .dropdown-menu {
    width: calc(100% - 100px);
    box-shadow: none;
    padding: 0;
}
.switch-li {
    position: relative;
}
.css-1vm9g5e {
    height: auto;
}
.search-select-box i{
    z-index: 2 !important;
    top: 13px;
    bottom: inherit;
}
.form-style2 .login-pass .pass-toggle {
    position: absolute;
    right: 30px;
    top: 15px;
    color: #c5c5c5;
    cursor: pointer;
}
.modal-header{
    min-height: 40px;
}
.select-small select {
    border: 1px solid {{$color2}};
    padding: 2px 20px 2px 5px;
    width: 100%;
    position: relative;
    margin-right: 5px;
}
.select-small::after {
    content: '';
    width: 6px;
    height: 6px;
    border-right: 2px solid {{$color2}};
    border-bottom: 2px solid {{$color2}};
    display: inline-block;
    -webkit-transform: rotate(45deg);
    -moz-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    -o-transform: rotate(45deg);
    transform: rotate(45deg);
    right: 7px;
    position: absolute;
    top: 6px;
}
.select-small {
    width: 100%;
    max-width: 136px !important;
    position: relative;
}
.text-red{
    color: red;
}
.text-red a{
    color: red;
    text-decoration: underline !important;
}
.manutal-opning-btn,
.link-btns-group .btn {
    max-width: 275px;
    width: 100%;
}
.btn-link-copy .copy-btn{
    position: absolute;
    right: 0;
    top: 5px;
}
.btn.choose-file-label-btn {
    max-width: 420px;
    overflow: hidden;
    width: 100%;
    text-overflow: ellipsis;
}
.toggle-field label i {
    font-size: 10px;
    margin-left: 5px;
}
.editNameMilestone.modal-style1 .modal-body {
    min-height: 200px;
}
.single-msg .userPicName, .single-msg .user-img, .reply-item-box .user-img, .reply-item-box .userPicName{
    background-color: #eceaea !important;
    border: 1px solid {{$color2}};
}
.bg-white {
background-color: #fff !important;
}
.fix-popup-body-space {
background: #fff;
padding: 15px;
}
.msg-edit-model .modal-dialog,
.reply-msg-model .modal-dialog {
max-width: 1000px;
}
.msg-edit-model.auto-scroll-popup .search-filter-result-view,
.reply-msg-model.auto-scroll-popup .search-filter-result-view {
padding-top: 40px;
}
.fix-popup-body-space .dropdown-menu {
right: 0;
left: inherit;
}
.repdAgenda_topic .cke_reset{
border-radius: 5px;
}
.all-agenda-topics .cke_button__bold_icon,
.all-agenda-topics .cke_button__italic_icon,
.all-agenda-topics .cke_button__justifyleft_icon,
.all-agenda-topics .cke_button__justifycenter_icon,
.all-agenda-topics .cke_button__justifyright_icon,
.all-agenda-topics .cke_hidpi .cke_button__bold_icon,
.all-agenda-topics .cke_hidpi .cke_button__italic_icon,
.all-agenda-topics .cke_hidpi .cke_button__justifyleft_icon,
.all-agenda-topics .cke_hidpi .cke_button__justifycenter_icon,
.all-agenda-topics .cke_hidpi .cke_button__justifyright_icon {
background: none !important;
text-align-last: center;
height: 18px !important;
width: 18px !important;
vertical-align: middle !important;
padding: 0 !important;
line-height: 18px;
color: {{$color2}};
font-size: 14px;
}
.all-agenda-topics .cke_button__bold_icon::before,
.all-agenda-topics .cke_button__italic_icon::before,
.all-agenda-topics .cke_button__justifyleft_icon::before,
.all-agenda-topics .cke_button__justifycenter_icon::before,
.all-agenda-topics .cke_button__justifyright_icon::before{
font-family: FontAwesome;
font-weight: normal;
font-style: normal;
}
.all-agenda-topics .cke_button__bold_icon::before {
content: "\f032";
}
.all-agenda-topics .cke_button__italic_icon::before{
content: "\f033";
}
.all-agenda-topics .cke_button__justifyleft_icon::before{
content: "\f036";
}
.all-agenda-topics .cke_button__justifycenter_icon::before{
content: "\f037";
}
.all-agenda-topics .cke_button__justifyright_icon::before{
content: "\f038";
}
.messeger-text-editor .ql-tooltip.ql-editing,
.reply-text-editor .ql-tooltip.ql-editing {
left: 0 !important;
right: 0 !important;
width: 320px;
margin: auto;
}
#docSearchReply1 .inline-form-sec.site-bg-color2 {
background: #fff;
}

.model-footer-link{
background: #fff;
padding: 3px 5px;
}
.skill-inputfield .skill-action-btns {
top: -9px;
}
.editNameMilestone.modal-style1 .modal-body {
    min-height: 200px;
}
.single-msg-cover {
padding: 0px 20px;
}
.ql-editor {
padding: 10px 0px 0px;
}
.single-msg:hover .msg-actions {
position: absolute;
right: 0;
top: 30px;
}
.messeger-text-editor .ql-tooltip.ql-editing, .reply-text-editor .ql-tooltip.ql-editing {
z-index: 2;
}
.search-filter-result-view .filter-inner-sec {
padding: 20px;
}
.auto-scroll-popup .modal-dialog {
max-width: 1010px;
}
#main-menu .drop-menu .dropdown-search-list .dropdown-menu{
padding-top: 0px !important;
}
#commissions-page .react-bs-table .react-bs-container-header>table>thead>tr>th {
font-size: 13px;
}
.tag-space .btn {
margin: 3px 5px 3px;
min-width: 100px;
position: relative;
}
.tag-space .btn span.fa.fa-pencil {
right: 0;
left: 0;
margin: auto;
top: 9px;
position: absolute;
width: 10px;
opacity: 0;
}
.tag-space .btn:hover span.fa {
opacity: 1 !important;
}
.tag-space .btn span.fa.fa-trash {
right: 20%;
top: 8px;
position: absolute;
opacity: 0;
width: 10px;
}
.tag-space .btn:hover span:first-of-type {
opacity: 0.5;
}
.tag-space .btn span:first-of-type{
padding: 6px 20px;
}
.tag-manage-link {
font-size: 14px;
display: inline-block;
text-decoration: none;
text-transform: capitalize;
padding: 10px;
}
.tag-space .btn span.fa.fa-eye, .tag-space .btn span.fa.fa-eye-slash {
position: absolute;
left: 20%;
top: 8px;
opacity: 0;
width: 10px;
}

.tag-manage-link:hover {
text-decoration: none;
}

.longhead{
text-overflow: ellipsis;
overflow: hidden;
white-space: nowrap;
}

.svgicon .fa-minus {
cursor: pointer;
}

span.ban-svg svg * {
fill: red !important;
}

span.ban-svg svg {
width: 18px;
height: 18px;
position: relative;
top: 4px;
}
