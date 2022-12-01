@php
    $css_data = dynamicCss();
    $color1 = $css_data['color1'];
    $color2= $css_data['color2'];
    $color3= $css_data['color3'];
    $transprancy7=$css_data['transprancy7'];
    $transprancy1=$css_data['transprancy1'];
    $transprancy2=$css_data['transprancy2'];
@endphp
@import url('https://fonts.googleapis.com/css?family=Lato:300,400,700');
html, body, #root{
height: 100%;
}
form.default-form button, form.default-form button:hover, form.default-form button:active, form.default-form button:focus{
outline: none;
}

.app {
min-height: 100%;
position: relative;
padding-bottom: 76px;
}
body {
width: 100%;
font-family: 'Lato', sans-serif;
font-weight: 400;
font-size: 14px;
color: #333333; }
h5{
font-size: 16px;
}
.cal-event-list{
max-height:250px;
overflow:auto;
}
.nopadding {
padding-left: 0;
padding-right: 0; }
.nomargin{
margin-top: 0;
margin-bottom: 0;
}
.flex-box,.input-value-inner {
display: -webkit-box;
display: -moz-box;
display: -ms-flexbox;
display: -webkit-flex;
display: flex;
}
.cursor-pointer{
cursor: pointer;
}
.white-text{
color: #ffffff !important;
}
.site-color{
color: {{$color2}} !important;
}
.site-color2{
color: {{$color1}} !important;
}
.site-color3{
color: #898888 !important;
}
.site-bg-color{
background-color: {{$color2}} !important;
}
.site-bg-color2 {
background: #f6f6f6;
}
.site-bg-color3{
background-color: {{$color1}} !important;
}
.light-font{
font-weight: 300;
}
.label-black{
color:#171717 !important;
}
.transprancy1{
background-color: {{$transprancy1}} !important;
}
.transprancy2{
background-color: {{$transprancy2}} !important;
}

.checkbox label::before {
border: 1px solid #909090;
}

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

.ml-3{margin-left: 3px !important;}
.ml-5{margin-left: 5px !important;}
.ml-10{margin-left: 10px !important;}
.ml-15{margin-left: 15px !important;}

.mr-3{margin-right: 3px; !important}
.mr-5{margin-right: 5px; !important}
.mr-10{margin-right: 10px; !important}
.mr-15{margin-right: 15px; !important}

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

.font-wt-nrml{
font-weight: 400 !important;
}

.btn-inline, .inline-block {
display: inline-block;
}

#upgrade-info{
background: #002d59;
}
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
padding: 16px 0;
background: #fff;
}
#user-img {
width: 80px;
height: 80px;
display: inline-block;
background-size: cover;
border-radius: 50%;
vertical-align: middle;
}
.pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover
{
background-color: {{$color2}};
}
#head-user-info .dropdown {
padding-left: 40px;
min-height: 36px;
}
#user-info-inner ul.dropdown-menu {
margin-top: 10px;
}
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
}
#head-user-info button.dropdown-toggle i {
font-size: 12px;
color: #284664;
margin-left: 8px;
vertical-align: top;
margin-top: 2px;
}
#head-logo-sec img{
max-height: 62px;
}
#head-user-info {
padding-top: 12px;
}
#menu-setting-sec {
background: {{$color1}};
border-bottom: 1px solid {{$color1}};
}
#menu-setting-sec ul,#main-menu ul{
margin: 0;
}
#setting-icons ul {
display: inline-block;
}
#head-menu>ul>li{
padding-left: 0;
padding-right: 0;
}
#head-menu>ul>li,#main-menu ul>li>a{
color: #fff;
padding: 10px 15px;
}
#main-menu ul>li>a{
display: block;
cursor: pointer;
}
#head-menu ul li a:hover,#main-menu ul li a{
text-decoration: none;
}
#setting-icons ul li a,#head-menu ul li.icon a {
font-size: 20px;
padding: 6px 5px;
color: #fff;
display: block;
}
#head-menu ul li.icon{
padding: 0px 5px;
}
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
form.default-form{
padding-top: 24px;
}

.switch {
position: relative;
display: inline-block;
width: 32px;
height: 13px;
}

.switch input {display:none;}

.slider {
position: absolute;
cursor: pointer;
top: 0;
left: 0;
right: 0;
bottom: 0;
background-color: #ecebeb;
-webkit-transition: .4s;
transition: .4s;
}

.slider:before {
position: absolute;
content: "";
height: 13px;
width: 13px;
left: 0px;
bottom: 0px;
background-color: #828282;
-webkit-transition: .4s;
transition: .4s;
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
color: #ababab;
}
.checkbox.checkbox-inline,
.radio.radio-inline{
margin-right: 40px
}
.switch-style2 .slider {
top: 6.5px;
height: 2px;
}
.switch-style2 .slider:before {
bottom: -5.6px;
}
.switch-style2 .slider:before {
border-radius: 50%;
}
.ui-widget-content {
z-index: 999 !important;
}
.resources h4{
margin-bottom: 30px;
margin-top: 0px;
}
.file {
visibility: hidden;
position: absolute;
}
.file-group .btn{
padding: 10px 12px;
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
.setting-opt-menu ul{
-webkit-padding-start: 34px;
}
.setting-opt-menu ul li{
list-style-type: none;
position: relative;
}
.setting-opt-menu ul li a:hover{
text-decoration: none;
}
.setting-opt-menu ul li a small{
color: #a9a0b0;
}
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
.setting-opt-menu ul li.imp-opt span{
background-position: right;
}
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
.inner-content h4{
margin-bottom: 0;
}
.comment-date{
color: #999999;
}
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
.inner-vert-menu.icon-message:before{
content: "\f086";
font-size: 16px;
padding: 9px 8px;
line-height: 15px;
}
.inner-vert-menu.icon-task:before{
content: "\f0ae";
font-size: 16px;
padding: 9px 8px;
line-height: 15px;
}
.inner-vert-menu.icon-meeting:before{
content: "\f0c0";
font-size: 16px;
padding: 9px 8px;
line-height: 15px;
}
.inner-vert-menu.icon-file:before {
content: "\f15b";
font-size: 14px;
padding: 9px 11px;
line-height: 15px;
}



#main-menu{
background: {{$color2}};
}
#main-menu ul>li.menu-has-child>a>i {
font-size: 10px;
margin-left: 4px;
position: relative;
top: -1px;
}
#main-menu ul>li ul.dropdown-menu{
padding: 0;
}
#main-menu .nav>li>a:hover, #main-menu .nav>li>a:focus, #main-menu .nav>li.active>a{
background-color: {{$color3}};
}
#main-menu .dropdown-menu{
border: none;
}
#main-menu ul>li ul.dropdown-menu,
#main-menu ul>li ul.dropdown-menu li{
-webkit-transition: all 0.4s ease-in-out;
-moz-transition: all 0.4s ease-in-out;
-o-transition: all 0.4s ease-in-out;
transition: all 0.4s ease-in-out;
}
#main-menu ul>li ul.dropdown-menu li{
background: {{$color1}};
padding: 0 6px;
list-style: none;
}
#main-menu ul>li ul.dropdown-menu>li:hover,
#main-menu ul>li ul.dropdown-menu>li.active{
background: {{$color2}};
}
#main-menu ul>li ul.dropdown-menu>li>a {
display: block;
padding: 8px 11px;
background: none;
border-bottom: 1px solid {{$color2}};
}
#main-menu ul>li ul.dropdown-menu li:last-child>a{
border: none;
}

.inline-form-sec {
padding: 14px 0;
}
.inline-form-sec form {
background: #fff;
padding-top: 26px;
padding-bottom: 30px;
border: 1px solid #cecece;
}
.inline-form-sec .form-group{
margin-bottom: 0px;
}
.inline-form-sec label {
margin-bottom: 14px;
font-weight: normal;
font-size: 16px;
}

.inline-form-sec .form-control, .form-control {
color: #333333;
border: 1px solid #e9e9e9;
-webkit-box-shadow: none;
box-shadow: none;
height: 31px;
padding: 4px 24px 4px 12px;
}
form label,.form-group label{
font-size: 16px;
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
height: 42px;
padding: 5px 12px;
}
textarea.form-control {
height: 100px;
}
.inline-form-sec button.btn[type="submit"] {
margin-top: 35px;
background: none;
border: none !important;
outline: none !important;
-webkit-box-shadow: none !important;
box-shadow: none !important;
padding: 0;
font-size: 22px;
min-width: inherit;
}
.inline-form-sec .dsearch-form button.btn[type="submit"] {
min-width: inherit;
}

.inline-form-sec button.btn[type="submit"]:hover i {
color: {{$color1}};
}
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
padding-right: 28px;
}
.select-cover select::-ms-expand {
display: none;
}
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
padding: 9px 18px;

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
.btn-color1.btn-primary i{
margin-right: 10px;
}
.table-style1 table th, .table-style1 table td{
text-align: center;
}
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
.table-style1 .table thead tr {
background: #f7f6f7;
}
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
.table-style1 .table>thead>tr>th.no-sorting::after{
display: none;
}
.table>thead>tr>th,
.table>tbody>tr>th,
.table>tfoot>tr>th,
.table>thead>tr>td,
.table>tbody>tr>td,
.table>tfoot>tr>td {
padding: 11px 14px;
border-top: 1px solid #d0d0d0;
vertical-align: middle;
}

.table>tbody>tr>td {
padding: 12px 14px;
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


/***************************/
/* Bootstrap TAB CSS Start */
/***************************/
.tab-section {
padding-top: 20px;
/*padding-bottom: 34px;*/
}
.nav-tabs {
border-bottom: 1px solid #bababb;
}
.nav-tabs>li {
margin-bottom: 0;
}
.nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus,.tab-menu>li.current-menu-item>a {
color: #fff;
background-color: {{$transprancy7}};
/*border: 1px solid {{$transprancy7}};*/
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
padding: 5px 18px;
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
}
.nav-tabs .dropdown-menu>li{
padding: 0 6px;
}
.nav-tabs .dropdown-menu>li>a {
padding: 8px 12px;
color: #fff;
border-bottom: 1px solid {{$color3}};
}
.nav-tabs .dropdown-menu>li:last-child>a{
border: none;
}
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
/**************************/
/* Bootstrap TAB CSS end */
/**************************/


.dropdown.drop-style2 {
display: inline-block;
}

.drop-style2 button.dropdown-toggle {
border: 1px solid #cecece;
padding: 7px 18px;
border-radius: 5px;
}
.drop-style2 button.dropdown-toggle i {
font-size: 11px;
margin-left: 7px;
vertical-align: top;
margin-top: 2px;
}
.drop-style2 .dropdown-menu {
min-width: 100%;
}
.radio label {
padding-left: 5px;
}
.checkbox label{
padding-left: 10px;
}
.radio label,.radio label::before,.checkbox label,.checkbox label::before{
outline: none !important;
line-height: 15px;
}
.radio label::after {
background-color: {{$color2}};
}
.checkbox label::after {
color: {{$color2}};
}
.checkbox label::before {
border-radius: 0px;
}

form.default-form section {
border-top: 1px solid #d1d1d1;
padding-top: 15px;
padding-bottom: 15px;
}
form.default-form {
padding-top: 0;
padding-bottom: 15px;
}
form.default-form button[type="submit"] {
/*margin-top: 20px;*/
}
.switch {
position: relative;
display: inline-block;
width: 32px;
height: 13px;
margin: 4px 0;
margin-right: 20px;
vertical-align: middle;
}
.switch input {display:none;}
.slider {
position: absolute;
cursor: pointer;
top: 0;
left: 0;
right: 0;
bottom: 0;
background-color: #ecebeb;
-webkit-transition: .4s;
transition: .4s;
}
.slider:before {
position: absolute;
content: "";
height: 13px;
width: 13px;
left: 0px;
bottom: 0px;
background-color: #828282;
-webkit-transition: .4s;
transition: .4s;
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
}
.switch-style2 .slider:before {
border-radius: 50%;
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
.ui-widget-content {
z-index: 999 !important;
}
.btn {
min-width: 132px;
padding: 7px 16px;
}
.btn.small{
padding: 4px 10px;
}
.btn.normal{
min-width: initial;
}
.noselect,.noselect *{
-moz-user-select: none;
-khtml-user-select: none;
-webkit-user-select: none;
-ms-user-select: none;
user-select: none;
}
.page-content{
padding-top: 35px;
padding-bottom: 35px;
/* min-height: 684px; */
}

.business-email-form h3 {
line-height: 34px;
margin-bottom: 20px;
color: #000;
}
.business-email-form p{
font-size: 16px;
}
.business-email-form .input-group .form-control {
border: 1px solid {{$color1}};
}
.business-email-form .input-group {
padding-top: 12px;
}
.business-email-form .input-group-btn button{
height: 42px;
}
.business-email-form .input-group-btn:last-child>.btn{
margin-left: 0px;
}

.grey-bg{
background: #717171;
}
.registration-verifiy {
padding: 50px 0;
min-height: 735px;
}
.registration-verifiy h2{
font-size: 28px;
}
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
.business-email-form.your-email-account .input-group-btn .btn.disabled{
opacity: 1;
}
.your-account-submit {
margin-top: 30px;
}

/*************************/
/* Login Form CSS Start */
/************************/

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
.login-input{
position: relative;
}
.login-input i {
position: absolute;
left: 14px;
top: 0;
bottom: 0;
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
padding: 15px 20px 15px 38px;
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;
}
.login-form .form-control:focus {
z-index: 2;
}
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
background-color: #002d59;
border-color: #002d59;
width: 80%;
margin: 72px auto 50px;
outline: none !important;
padding: 12px 20px;
border-radius: 4px;
-webkit-box-shadow: 1px 2px 7px rgba(0, 0, 0, 0.43);
-moz-box-shadow: 1px 2px 7px rgba(0, 0, 0, 0.43);
box-shadow: 1px 2px 7px rgba(0, 0, 0, 0.43);
}
/************************/
/* Login Form CSS end */
/************************/

.table thead tr th i.fa-sort {
margin-left: 8px;
color: #6f6f6f;
font-size: 15px;
cursor: pointer;
}

/****************************/
/* Bootstrap MENU CSS start */
/***************************/

.tab-menu {
width: 100%;
}
.tab-menu .dropdown-submenu {
position: relative;
}

.tab-menu .dropdown-submenu>.dropdown-menu {
top: 0;
left: 100%;
margin-top: 0px;
margin-left: 0px;
-webkit-border-radius: 0 6px 6px 6px;
-moz-border-radius: 0 6px 6px;
border-radius: 0 6px 6px 6px;
}

.tab-menu .dropdown-submenu:hover>.dropdown-menu {
display: block;
}

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

.tab-menu .dropdown-submenu:hover>a:after {
border-left-color: #fff;
}

.tab-menu .dropdown-submenu.pull-left {
float: none;
}

.tab-menu .dropdown-submenu.pull-left>.dropdown-menu {
left: -100%;
margin-left: 10px;
-webkit-border-radius: 6px 0 6px 6px;
-moz-border-radius: 6px 0 6px 6px;
border-radius: 6px 0 6px 6px;
}
.tab-menu-content {
padding-top: 18px;
}
/**************************/
/* Bootstrap MENU CSS end */
/**************************/


table.dataTable {
margin-top: 0px !important;
margin-bottom: 0px !important;
}
div.dataTables_wrapper div.dataTables_info {
padding: 24px 8px 24px 30px;
}
div.dataTables_wrapper div.dataTables_paginate {
padding: 15px 30px 10px 12px;
}
.table-style1 table tbody tr:last-child td {
border-bottom: 1px solid #d0d0d0;
}
.table-style1 *{
outline: none !important;
}
table.dataTable thead .sorting:after,
table.dataTable thead .sorting_asc:after,
table.dataTable thead .sorting_desc:after,
table.dataTable thead .sorting_asc_disabled:after,
table.dataTable thead .sorting_desc_disabled:after {
position: relative;
top: 0px;
left: 12px;
bottom: initial;
right: initial;
display: inline;
}
.pagination>.disabled>span,
.pagination>.disabled>span:hover,
.pagination>.disabled>span:focus,
.pagination>.disabled>a, .pagination>.disabled>a:hover,
.pagination>.disabled>a:focus {
display: none;
}

.fa-ellipsis-v {
font-size: 28px;
vertical-align: middle;
margin-right: 5px;
}
.fa-ellipsis-v.green {
color: #00af5f;
}
.fa-ellipsis-v.blue{
color: #00a2d0;
}
.fa-ellipsis-v.red{
color: #e64156;
}
table .fa-pencil{
margin-left: 18px;
cursor: pointer;
}

table.dataTable thead .sorting:after {
content: "\f0dc";
font-family: FontAwesome;
}
table.dataTable thead .sorting_asc:after{
content: "\f0de";
font-family: FontAwesome;
}
table.dataTable thead .sorting_desc:after{
content: "\f0dd";
font-family: FontAwesome;
}
.footer {
padding: 10px 0;
position: absolute;
bottom: 0px;
width: 100%;
}
.btn-group.radio-btns{
display: block;
}
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
.table {
margin-bottom: 0;
}
.role {
color: #fff;
padding: 1px 5px;
display: inline-block;
margin: 3px 0;
border-radius: 2px;
margin-right: 4px;
}
.resend_mail{
color: #33c9ff;
}
.resend_mail:hover {
color: #33c9ff;
}
/*.tooltip-inner {
padding: 5px 6px;
color: #fff;
font-size: 11px;
background-color: {{$color1}};
border: 1px solid {{$color1}};
}
.tooltip.top .tooltip-arrow {
border-top-color: {{$color2}};
}*/
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
}
.all-agenda-topics .rc-draggable-list-draggableRow>li,.all-agenda-topics>li {
border-top: 1px solid #b9b9b9;
padding: 2px 0 2px 10px;
padding-right: 80px;
}
.rc-draggable-list-draggableRow{
font-size: 13px;
line-height: 22px;
position: relative;
}
.all-agenda-topics>li:last-child{
border-bottom: 1px solid #b9b9b9;
}
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
ol.all-agenda-topics>li a{
color: #333333;
}
.all-agenda-topics .order-no {
/*position: absolute;
left: 10px;
top: 10px;
z-index: 1;*/
margin-right: 5px;
}
.all-agenda-topics .text{
display: table-cell;
padding-right: 10px;
}

.agenda-heading-action {
border-bottom: 1px solid #b9b9b9;
position: relative;
}
ul.agenda-actions {
position: absolute;
right: 0px;
top: 1px;
margin: 0;
width: 70px;
text-align: right;
}
/*.topics-level1 ul.agenda-actions{
right: -70px;
}*/
ol.all-agenda-topics>li>ul.agenda-actions{
/*top: 10px;*/
top: 4px;
}
ul.agenda-actions>li{
width: 33%;
float: right;
}
ul.agenda-actions>li i{
cursor: pointer;
font-size: 14px;
}
i.inactive {
color: #adadad;
}
i.inactive svg path{
fill: #adadad;
}
.agenda-actions li i.active svg path{
fill: {{$color2}}
}
.agenda-actions li i.active{
color: {{$color2}}
}
.agenda-actions li.collapsed i{
color: #adadad
}
.agenda-actions li.collapsed i svg path{
fill: #adadad
}
i.fa-recycle {
position: relative;
top: 1px;
}
i.fa-gavel.active,i.fa-gavel[aria-expanded="true"] {
color: {{$color2}};
}
i.active svg path,i[aria-expanded="true"] svg path {
fill: {{$color2}};
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
.grandTitle{
font-weight: normal;
}
.dtext strong{
font-size: 12px;
}
.dtext p{
font-size: 16px;
//white-space: pre-wrap;
}
.discussion-block{
margin-left: 40px;
break-inside: avoid;
page-break-inside: avoid;
-webkit-column-break-inside: avoid;
-moz-column-break-inside: avoid;
}
.discussion-form label,.task-list label,.task-list table{
font-size: 12px;
}
/** Agenda PDF Css Start**/
.agenda-header {
border-bottom: 30px solid #0a6cb3;
}
.seprator{
border-top: 1px solid #0a6cb3;
}
.seprator-grey hr{
border-top: 1px solid #d1d1d1;
}
.agenda-content ul{
color: #17375e;
}
.agenda-content ul, .agenda-inner-content>ul{
-webkit-padding-start: 0;
list-style-type: none;
}
.topics-level1,.topics-level2{
padding-left: 40px;
}
.agenda-inner-content{
font-size: 16px;
}
.agenda-inner-content ul li{
list-style-type: none;
}
.doc-attach{
float: right;
}
.doc-attach a{
text-decoration: underline;
padding-right: 3px;
font-size: 12px;
color: #4e74b0;
}
.agenda-heading h4{
letter-spacing: 2px;
}
/** Agenda PDF Css End**/

.all-agenda-topics textarea.form-control {
height: 76px;
}
.all-agenda-topics hr {
margin-top: 0;
border-top: 5px solid #eee;
}
.allmsgs {
padding: 0px;
border: 2px solid #efefef;
}
.single-msg-cover {
padding: 30px 60px;
border-bottom: 2px solid #e4e4e4;
position: relative;
}
.single-msg-cover:last-child{
border: none;
}
.single-msg {
position: relative;
padding-left: 80px;
}
.single-msg .user-img {
height: 60px;
width: 60px;
display: block;
position: absolute;
top: 4px;
left: 0;
border-radius: 50%;
border: 1px solid {{$color2}};
}
.time-sec-inner {
font-size: 12px;
}
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
border: 1px solid {{$color2}};
background: #fff;
margin-left: 4px;
font-size: 12px;
padding: 2px 5px;
}
.single-reply-msg {
position: relative;
padding: 15px 20px;
padding-left: 90px;
margin-top: 26px;
background: #fff;
border: 1px solid #dcdcdc;
}
.single-msg .single-reply-msg .user-img,
.single-msg .single-reply-msg .userPicName{
left: 14px;
top: 15px;
}
.btn.btn-primary.collapsable-btn {
padding: 5px 14px;
}
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
.task-form-title span {
text-transform: none;
}
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
#ViewDecisionPDF .agenda-content{
padding-left: 5px;
}

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
.decision-box span{
font-style: italic;
}
.upcoming-meet{
border-top: 3px solid #4e74b0;
}
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
.empty-task h2{
background: #f2f2f2;
}
/** Decision PDF Css End**/

.company-icon {
border: 1px solid {{$color1}};
padding: 3px 4px;
-webkit-box-shadow: 0px 0px 1px #000;
-moz-box-shadow: 0px 0px 1px #000;
box-shadow: 0px 0px 1px #000;
margin-left: 10px;
float: right;
}
.company-icon img {
width: 26px;
}
#setting-icons ul li:last-child, #setting-icons ul li:last-child a {
padding-right: 0;
}
#main-menu .container {
position: relative;
}
.head-start-btn {
position: absolute;
right: 15px;
}
.head-start-btn .btn-color1.btn-primary {
padding: 4px 17px 3px;
border-radius: 14px;
margin: 5px 0 4px;
}
.panel{
border-radius: 2px;
margin-bottom: 30px;
}
.panel-heading {
border-top-left-radius: 1px;
border-top-right-radius: 1px;
}
.panel-primary .panel-heading{
background: {{$color2}};
}
.panel-primary .panel-heading .panel-title{
font-size: 14px;
text-transform: uppercase;
}
.btn-space{
margin: 10px 10px;
}
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
.title {
padding: 5px 15px;
}
.eventHints{
margin-top: 46px;
}
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
.hintBlue{
background: #446a9e;
}
.hintGreen{
background: #a5c74d;
}
.hintPink{
background: #ce89af;
}
.hintBlack{
background: #343f18;
}
.hintPurple{
background: #662a4b;
}
.hintTeal{
background: #006560;
}
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
.welcome-video{
background: #002d59;
}
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
.start-listing {
padding: 20px;
}
.start-listing ul{
padding-left: 20px;
}
.start-listing ul li{
list-style: none;
padding: 7px 0;
}
.panel-body .setting-opt-menu ul{
padding-left: 20px;
}
.setting-opt-menu ul li .switch {
position: relative;
left: 0;
top: 0;
margin: 0px 5px -1px 0px;
}
.date-confie small .fa{
font-size: 16px;
}
.btn-xs {
font-size: 14px !important;
padding: 5px 15px !important;
}
.clearfix {
display: block;
clear: both;
}
.dropdown-menu>li>span,.dropdown-menu>li>a {
display: block;
padding: 3px 20px;
clear: both;
font-weight: 400;
line-height: 1.42857143;
color: #333;
white-space: nowrap;
cursor: pointer;
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
}
body .DayPicker-wrapper {
border: 1px solid #cecece;
outline: none;
}
body .DayPicker-Day {
border: none;
position: relative;
}
body .DayPicker-Caption {
height: 32px;
}
body  .DayPicker-Month {
margin: 0;
width: 100%;
}
.form-inline .select-cover {
display: inline-block;
}
.date-list{
padding-left: 0;
}
.date-list li{
list-style: none;
}
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
span.caret{
margin: 5px 2px !important;
}
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
top: 5px;
width: 32px;
cursor: pointer;
}
.btn.btn-primary i{
font-size: 10px;
}
.head-start-btn .btn.btn-primary i {
font-size: 14px;
margin-right: 3px;
}
.date-view {
display: inline-block;
border-radius: 3px;
position: relative;
margin-top: 10px;
}
.approve-date, .reject-date, .pending-date{
border-radius: 50px;
height: 24px;
line-height: 26px;
MARGIN-TOP: 2PX;
margin-right: 10px;
width: 24px;
}
.approve-date{
background: #1f86ca;
color: #fff;
}
/* .approve-date::before {
content: "\f00c";
font-size: 20px;
font-family: FontAwesome;
} */
.reject-date{
background: #e64156;
color: #fff;
}
/* .reject-date::before {
content: "\f00d";
font-size: 20px;
font-family: FontAwesome;
} */
.date-view i {
font-size: 11px;
vertical-align: top;
}
.pending-date{
background: #ccc;
color: #444;
}
/* .pending-date::before {
content: "\f128";
font-size: 20px;
font-family: FontAwesome;
} */
.date-of-meeting {
margin: 5px 0px;
float: left;
width: 100%;
position: static;
}
.date-view i:last-child {
margin-right: 0px;
font-size: 32px;
}
.meeting-timedetail span {
color: #7b7b7b;
font-size: 14px;
}
.meeting-timedetail span.month{
font-size: 16px;
}
.meeting-mail-icon{
position: absolute;
left: 100%;
top: 13px;
cursor: pointer;
width: 24px;
height: 24px;
}
.fill-check {
position: relative;
margin-right: 10px !important;
margin-top: 1px !important;
width: 26px;
height: 26px;
}
.fill-check:before {
content: '';
position: absolute;
width: 26px;
height: 26px;
border: 1px solid #ccc;
background: #fff;
border-radius: 3px;
left: -1px;
top: -1px;
}
.fill-check:checked:before{
content: '';
background: {{$color2}};
border-color: {{$color2}};
}
.fill-check:checked:after {
content: '';
position: absolute;
width: 15px;
height: 8px;
border-left: 2px solid #fff;
border-bottom: 2px solid #fff;
-moz-transform: rotate(-50deg);
-webkit-transform: rotate(-50deg);
-ms-transform: rotate(-50deg);
transform: rotate(-50deg);
top: 6px;
left: 4px;
}
.radio label::before {
border: 1px solid #9a9a9a;
}
.table-style1 table .form-control {
height: 34px;
max-width: 240px;
margin: 0 auto;
}
.table-style1 table .form-control[type="file"] {
padding: 3px 0;
border: none;
}
.addunion-table .table-style1 table tbody tr:last-child td {
border-bottom: none;
}
.table-style1 table .remover {
font-size: 18px;
}
.settings-menu-list li ul li{
position: relative;
}
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
.action_btn{
cursor: pointer;
}
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
.profile-img{
border-radius: 50%
}
.modal-backdrop{
z-index: 0 !important;
}
.pointer{
cursor: pointer;
}

.text-field-block{
padding: 10px 0px;
/* border: 1px solid #ccc; */
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
.text-field-label::after {
content: ':';
position: absolute;
margin-left: 5px;
}
.view-radio .radio label::before{
display: none;
}
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
.collapse::after {
/* content: '\f106';
font-family: FontAwesome;
color: #fff;
font-size: 22px;
position: absolute;
top: -30px;
right: 15px;
line-height: 18px;
background: {{$color1}}; */
}
.all-agenda-topics > li .text{
display: inline-block;
}
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
border: 1px solid #ccc;
padding: 10px;
height:100px;
position: relative;
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
}
.download-btn{
cursor: pointer;
}
.react-bs-container-body,.table-style1{
overflow: inherit !important;
}
.blue{
color: {{$color2}};
}
.action_alert{
display: block;
color: red;
}


.tabs-cover {
position: relative;
padding-left: 43px;
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
.tabs-cover ul li.add-tab, .tabs-cover ul li.add-tab:hover {
padding: 0px 10px 0px;
}
button.like-star {
position: absolute;
top: 0;
left: 0;
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
padding-right: 13px;
border-radius: 0px 17px 18px 0px;
}
.single-msg-cover.active {
background: #e2f3ff;
}
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
.add-tab-btn:hover{
color: #0b8ec0;
}
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
.modal-body {
padding: 40px 35px;
}
.modal-body p{
word-break: break-all;
}
.modal-body p.mail-signature{
word-break: unset;
}
.modal-body img{
width: 100%;
}
.container.tab-section .container {
width: 100%;
}
.single-msg .collapse::after {
display: none;
}
.form-group span.text-danger {
margin-top: 5px;
display: block;
}
.checkbox-inline+.checkbox-inline, .radio-inline+.radio-inline {
margin-left: 0;
}
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

}
.linkBtn a{
margin-right: 10px;
margin-top: 5px;
display: inline-block;
}
.linkBtn a:last-child{
margin-right: 0px;
}
.page404{
margin-top: 10%;
}
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
.redacteur label{
padding-left: 0;
}
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
span.edit-del-tab {
position: absolute;
}
.tabs-cover ul li{
position: relative;
}
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
/* right: 0; */
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
.tabs-cover ul li:hover span.edit-del-tab {
display: block;
}
.tabs-cover ul li:first-child span.edit-del-tab{
display: none !important;
}
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
.input-value-inner span {
padding: 5px 0px;
}
.input-value-inner input {
width: 100%;
height: 30px;
border: none;
box-shadow: none;
padding: 0 6px;
outline: none;
}
.table-striped>tbody>tr:nth-of-type(odd) {
background-color: #fff;
}
.table-striped>tbody>tr:nth-of-type(even) {
background-color: #f9f9f9;
}
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
height: 36px;
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
height: 36px;
width: 36px;
position: absolute;
transform: translate(-50%, -50%);
pointer-events: none;
left: 12px;
top: 12px;
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
.pb-container.disabled .pb-button {
cursor: not-allowed;
}
.pb-container.loading .pb-button {
width: 36px;
height: 36px;
border-width: 6px;
border-color: {{$color2}};
cursor: wait;
border-radius: 50%;
background-color: transparent;
padding: 0;
position: relative;
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
.pb-container.success .pb-button .pb-checkmark > path {
opacity: 1;
}
.pb-container.error .pb-button {
border-color: #ED5565;
background-color: #ED5565;
}
.pb-container.error .pb-button span {
transition: all 0.15s;
opacity: 0;
display: none;
}
.pb-container.error .pb-button .pb-cross > path {
opacity: 1;
}
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
.pb-container.progress-btn-full {
width: 100%;
}

.autocomplete-form > div,.autocomplete-form > section > div {
display: block !important;
}
.autocomplete-form > div > div > div,.autocomplete-form > section > div > div > div{
padding: 0px;
}
.autocomplete-form > div > div,.autocomplete-form > section > div > div
{
position: inherit !important;
}
.inline-form-sec .autocomplete-form input{
height:31px;
}
.autocomplete-form input{
display: block;
width: 100%;
height: 42px;
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
.autocomplete-form input:focus {
box-shadow: rgba(0, 0, 0, 0.075) 0px 1px 1px inset, rgba(102, 175, 233, 0.6) 0px 0px 8px;
border-color: rgb(102, 175, 233);
outline: 0px;
}
.form-group.required-field label::after {
content: " *";
color: #af0000;
font-size: 22px;
line-height: 3px;
display: inline-block;
position: relative;
top: 4px;
margin-left: 4px;
}
.mail-signature{
display: table-caption;
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
#site-lang li:last-child::after{
display: none;
}
#site-lang li.active {
color: #acebfd;
}
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
#select_user{
text-align: left;
}
.file-icon {
background-repeat: no-repeat;
background-position: center left;
padding: 10px 0px 10px 40px;
background-size: 35px;
display: inline-block;
}
#messages {
overflow: hidden;
}
#messages .container {
padding-right: 30px;
}
#messages .container .container {
padding-right: 0;
padding-left: 0;
}
#messages .inline-form-sec .container {
padding-right: 15px;
}
.container .inline-form-sec .container {
padding-right: 15px !important;
padding-left: 15px !important;
}
.main .container .container {
width: 100%;
padding-left: 0;
padding-right: 0;
}
.docs-png{background-image: url(../../work/ops_laravel/public/img/file_icon/png.png);}
.docs-pdf{background-image: url(../../work/ops_laravel/public/img/file_icon/pdf.png);}
.docs-doc{background-image: url(../../work/ops_laravel/public/img/file_icon/doc.png);}
.docs-ai{background-image: url(../../work/ops_laravel/public/img/file_icon/ai.png);}
.docs-avi{background-image: url(../../work/ops_laravel/public/img/file_icon/avi.png);}
.docs-css{background-image: url(../../work/ops_laravel/public/img/file_icon/css.png);}
.docs-csv{background-image: url(../../work/ops_laravel/public/img/file_icon/csv.png);}
.docs-dbf{background-image: url(../../work/ops_laravel/public/img/file_icon/dbf.png);}
.docs-exe{background-image: url(../../work/ops_laravel/public/img/file_icon/exe.png);}
.docs-dwg{background-image: url(../../work/ops_laravel/public/img/file_icon/dwg.png);}
.docs-file{background-image: url(../../work/ops_laravel/public/img/file_icon/file.png);}
.docs-fla{background-image: url(../../work/ops_laravel/public/img/file_icon/fla.png);}
.docs-html{background-image: url(../../work/ops_laravel/public/img/file_icon/html.png);}
.docs-iso{background-image: url(../../work/ops_laravel/public/img/file_icon/iso.png);}
.docs-javascript{background-image: url(../../work/ops_laravel/public/img/file_icon/javascript.png);}
.docs-jpg{background-image: url(../../work/ops_laravel/public/img/file_icon/jpg.png);}
.docs-json{background-image: url(../../work/ops_laravel/public/img/file_icon/json.png);}
.docs-mp3{background-image: url(../../work/ops_laravel/public/img/file_icon/mp3.png);}
.docs-mp4{background-image: url(../../work/ops_laravel/public/img/file_icon/mp4.png);}
.docs-ppt{background-image: url(../../work/ops_laravel/public/img/file_icon/ppt.png);}
.docs-psd{background-image: url(../../work/ops_laravel/public/img/file_icon/psd.png);}
.docs-rtf{background-image: url(../../work/ops_laravel/public/img/file_icon/rtf.png);}
.docs-search{background-image: url(../../work/ops_laravel/public/img/file_icon/search.png);}
.docs-svg{background-image: url(../../work/ops_laravel/public/img/file_icon/svg.png);}
.docs-txt{background-image: url(../../work/ops_laravel/public/img/file_icon/txt.png);}
.docs-xls{background-image: url(../../work/ops_laravel/public/img/file_icon/xls.png);}
.docs-xml{background-image: url(../../work/ops_laravel/public/img/file_icon/xml.png);}
.docs-zip{background-image: url(../../work/ops_laravel/public/img/file_icon/zip.png);}
.docs-zip-1{background-image: url(../../work/ops_laravel/public/img/file_icon/zip-1.png);}
#commissions-page .table-style1,
#commissions-page .table-style1 .react-bs-container-body,
.table-style1.search-table,
.table-style1.search-table .react-bs-container-body{
/*overflow: auto !important;*/
}
.select-cover .DayPickerInput {
display: block;
}
.select-cover .DayPickerInput input{
background: transparent;
}
.table td .btn-color1.btn-primary {
padding: 5px 12px;
min-width: inherit;
}
.react-bs-table-tool-bar{
overflow:hidden;
}

body .DayPicker-wrapper {
padding: 8px 15px;
}
.error-txt span {
font-size: 90px;
color: {{$color2}};
display: block;
padding-bottom: 28px;
line-height: 50px;
}
.dashboard-event-cal{
max-width: 100% !important;
}
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
.feature_meeting{
color: #fff;
background: #e43838;
border-radius: 50%;
position: absolute;
width: 20px;
top: -3px;
font-size: 13px;
right: -1px;
}
.past_meeting{
color: #fff;
background: #6d737e;
border-radius: 50%;
position: absolute;
width: 20px;
top: -3px;
font-size: 13px;
right: -1px;
}
.dlink{
color: #464746;
cursor: pointer;
font-size: 12px;
}

.btn-primary.active.focus, .btn-primary.active:focus, .btn-primary.active:hover, .btn-primary:active.focus, .btn-primary:active:focus, .btn-primary:active:hover, .open>.dropdown-toggle.btn-primary.focus, .open>.dropdown-toggle.btn-primary:focus, .open>.dropdown-toggle.btn-primary:hover {
color: #fff;
background-color: {{$color3}};
border-color: {{$color3}};
}
.email-preview img{
width:100%;
}
.img-preview{
max-height: 62px;
}
.opentip-container,.opentip-container.ot-show-effect-appear.ot-visible{
background: #9b922f !important;
display: block !important;
}
.cke_combopanel{
width:200px !important;
}
.cke_button__createplaceholder{
display:none !important;
}
.panel-primary{
border-color: #cccccc;
}
.prepd-topic-input{
text-transform: capitalize;
}
#Dashboard .react-bs-table-no-data{
text-align:left;
}
.buttonlink{
background: transparent;
border: none;
color: #333;
text-decoration: underline;
}
.react-confirm-alert > h3,.react-confirm-alert > h1{
font-size:13px !important;
}
.agenda-actions li i, .all-agenda-topics li i{
pointer-events:none;
}
.agenda-actions li, .all-agenda-topics li span{
cursor:pointer;
}
.scroll-block{
overflow: auto;
}
.flex-block{
display:flex;
}
.fix-column{
float: left;
display: inline-block;
min-width: 172px;
}
.hide{
display:none;
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
width: 0px;  /* remove scrollbar space */
background: transparent;  /* optional: just make scrollbar invisible */
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
background: transparent url(../../public/app/img/send.png) no-repeat center left;
border: none;
padding: 5px 5px 5px 20px;
}
.sendTo button:focus{
outline: none;
}
.sendTo {
display: inline-block;
width: 100%;
margin-top: 5px;
}
.userControlList select:focus, .sendTo select:focus {
outline: none;
}
.videoControllDivBtn{
cursor: pointer;
}

.meetingMember {
position: absolute;
left: 0;
}
.meetingDates{
position: static;
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

/* Preparing Agenda Desing css by NK */
.btn-icon{
background: transparent;
font-size: 20px;
}
.btn-icon, .btn-icon:hover, .btn-icon:focus {
background: #ffffff;
font-size: 20px;
min-width: inherit;
margin: 0 5px;
padding: 0px 10px;
outline: none !important;
box-shadow: none !important;
margin-top: 5px;
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
/*display: none;*/
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
}
input.edit_button {
width: 350px;
}
.new_topic_line button {
background: {{$color2}};
color: #fff;
border: none;
padding: 5px 12px;
}
.allAgendaList .all-agenda-topics li .agenda_list_li:hover{
/*background: #ededed;*/
}
.all-agenda-topics li .agenda_list_li:hover .iconBtn,
.all-agenda-topics li .agenda_list_li:hover .list-drag,
.allAgendaList .all-agenda-topics li .agenda_list_li:hover .fileUploadIcon{
visibility: visible;
}
.all-agenda-topics li .agenda_list_li .iconBtn,
.all-agenda-topics li .agenda_list_li .list-drag,
.allAgendaList .all-agenda-topics li .agenda_list_li .fileUploadIcon{
/*display:inline-block;*/
display: table-cell;
}
.allAgendaList .all-agenda-topics .order-no {
position: absolute;
left: 35px;
top: 10px;
z-index: 1;
vertical-align: top;
}
.allAgendaList .all-agenda-topics .list-drag {
position: relative;
left: 0px;
margin-right: 5px;
padding: 0px 5px;
}
.allAgendaList .all-agenda-topics .rc-draggable-list-draggableRow>li{
padding-left: 0px;
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
.new_topic_line {
padding-left: 28px;
}
.fa-3x{
font-size: 20px !important;
}
.toggleTask {
padding: 5px 0px 15px 40px;
margin: 0px 0px 0px 0px;
}
.listing-open::before{
content: '';
position: absolute;
/*box-shadow: 0px -16px 15px -14px rgba(0, 0, 0, 0.22);*/
-webkit-box-shadow: 0px -5px 8px -6px rgba(0, 0, 0, 0.8);
-moz-box-shadow: 0px -5px 8px -6px rgba(0, 0, 0, 0.8);
box-shadow: 0px -5px 8px -6px rgba(0, 0, 0, 0.8);
width: 100%;
height: 100%;
top: 0;
left: 0%;
}
/*.listing-open::before{
content: '';
position: absolute;
box-shadow: 0px -14px 18px -17px #000;
width: 120%;
height: 100%;
top: 0;
left: -10%;
}
.toggleTask::after {
content: '';
position: absolute;
box-shadow: 0px 15px 18px -17px #000;
width: 120%;
height: 100%;
top: 0;
left: -10%;
} */
.toggleTask::after {
content: '';
position: absolute;
/*box-shadow: 0px 15px 18px -17px rgba(0, 0, 0, 0.34);*/
-webkit-box-shadow: 0px 11px 11px -13px rgb(0, 0, 0);
-moz-box-shadow: 0px 11px 11px -13px rgb(0, 0, 0);
box-shadow: 0px 11px 11px -13px rgb(0, 0, 0);
width: 100%;
height: 100%;
top: 0;
left: 0%;
}
.toggleTaskInner{
position: relative;
z-index: 1;
}
.right-zero{
right:0px
}
.show-add.new{
display: inline-block !important;
}
.show-add.new span{
margin: 0px 5px;
display: inline-block;
}
.show-add.new span img {
height: 35px;
}
.add_singleMulti_task {
margin-right: 5px;
display: inline-block;
margin-top: 7px;
vertical-align: top;
}
.add_singleMulti_task i{
font-size: 25px;
}
.pagination>li{
display:inline-block !important;
}
.first-level-li>li{
padding-left:10px !important
}
.first-level-li .new_topic_line{
padding-left:0px !important;
}
.note-blue{
color:#4F71D0 !important;
}
.note-blue svg path{
fill:#4F71D0 !important;
}
ol.first-level-addbox li {
padding: 7px 0px 7px 10px;
}
.first-level-addbox .new_topic_line{
padding-left:10px;
}
/* 2 feb 2018 */
.allAgendaList .all-agenda-topics li .agenda_list_li {
padding: 5px 0px 5px 0px;
position: relative;
}
.allAgendaList .all-agenda-topics li .topics-level1 .agenda_list_li{
/*padding-left: 28px;*/
padding-left: 0px;
}
.allAgendaList .all-agenda-topics li .topics-level2 .agenda_list_li{
/*padding-left: 56px;*/
padding-left: 0px;
}
.level_add_doc {
padding-left: 40px;
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
.repd_list_level3{
padding-left:40px;
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
}
.iconBtn svg{
height: 20px;
width: 20px;
vertical-align: middle;
display: table-cell;
}

.iconBtn svg path{
fill: {{$color2}};
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
.list-drag i::before{
font-weight: normal;
}
.pdfBtn svg{
height: 35px;
width: 35px;
}
.pdfBtn svg path{
fill : {{$color2}};
}
.selectUserBtn i{
/*font-size: 30px;*/
color: {{$color2}};
}
.repdDoc_sign svg {
height: 32px;
width: 32px;
}
.add_plus_doc.repdDoc_sign svg {
height: 22px;
width: 22px;
}
.reunion_add svg{
height: 20px;
width: 20px;
}
.reunion_add {
display: inline-block;
margin: 0 !important;
}
.repdDoc_sign svg path,
.reunion_add svg path{
fill: {{$color2}};
}
.repdAgenda_topic.all-agenda-topics li span{
vertical-align: inherit;
margin-right: 5px;
}
.topic-project .sub-topic>div{
display: block !important;
}
.new-topic-project .form-group:first-child div{
float: left !important;
}
.new-topic-project .form-group:last-child button{
float: right !important;
}
.new-topic-project .form-group:nth-child(2){
border: 1px solid #ddd !important;
padding: 8px 12px;
}
.new-topic-project .form-group:first-child, .new-topic-project .form-group:last-child,
{
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
.topic-project .sub-topic>div>div>div{
padding: 6px;
}
.colorPickerBox {
padding: 7px;
border-radius: 3px;
margin-top: 8px;
}
.colorPickerBox::before {
content: '';
position: absolute;
width: 8px;
height: 8px;
border-bottom: 8px solid #fdfdfd;
border-left: 4px solid transparent;
border-right: 4px solid transparent;
top: -8px;
z-index: 1;
left: 11px;
}
.colorPickerBox::after {
content: '';
position: absolute;
width: 8px;
height: 8px;
border-bottom: 8px solid #ccc;
border-left: 5px solid transparent;
border-right: 5px solid transparent;
top: -8px;
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
.multiple_list_sec .Select--multi {
padding: 0px;
}
.all-agenda-topics li .multiple_list_sec .Select--multi span{
display: table-cell !important;
}
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
.leftDivIcon svg {
width: 20px;
height: 20px;
}
.leftDivIcon svg path{
fill: {{$color2}};
}
ul.agenda-actions>li svg {
width: 13px;
height: 13px;
}

/* New CSS */
.dropdown.drop-style1.dropDown_plus {
padding-left: 35px;
position: relative;
}
/*.all-agenda-topics li .agenda_list_li .list-drag {
position: absolute;
left: -25px;
width: 25px;
margin-right: 0;
}
.all-agenda-topics li .topics-level1 .agenda_list_li .list-drag {
left: -40px;
}*/

.DayPicker-NavButton{
width: 13px !important;
height: 13px !important;
background-position: center !important;
background-size: cover !important;
}
.default-form.discussion-form{
padding-bottom: 0px;
}
.documentAddList{}
.documentAddList .topics-level1 {
padding-left: 0px;
}
.multiple_list_sec .Select--multi .Select-control .Select-input {
width: 35% !important;
}
.multiple_list_sec .Select--multi .Select-control .Select-input>input{
width: 100% !important;
height: 30px !important;
padding: 2px !important;
}
.delIconBtn svg{
height: 13px;
width: 13px;
}
.dropdown.drop-style1.dropDown_plus{
padding-left: 35px;
}
.dropdown.drop-style1.dropDown_plus>a {
position: absolute;
left: 0;
top: 0;
}
.drop-style1 .dropdown-toggle{
text-align: left;
padding-right: 15px;
position: relative;
overflow: hidden;
max-width: 100%;
}
.taskDataList div > h5 .btn {
padding: 0px !important;
margin-left: 10px;
}
.drop-style1 .dropdown-toggle>i {
position: absolute;
right: 0;
top: 0;
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
}
.level_add_doc span {
vertical-align: top !important;
}
.agendaHeadDiv {
padding-bottom: 10px;
position: relative;
}
.all-agenda-topics.repdAgenda_topic>li:first-child {
border-top: none !important;
}
.all-agenda-topics .rc-draggable-list-draggableRow:first-child>li{
border-top: none;
}
.all-agenda-topics.repdAgenda_topic li.repd-li>strong {
margin-top: 5px;
display: inline-block;
padding-left: 40px;
}
.agendaHeadDiv .text-left.redacteur {
margin-top: 11px;
}
.agendaHeadDiv .redacteur label {
margin-top: 5px;
}
.agenda-heading-action ul.agenda-actions {
top: 14px;
right: 0px;
}
.level_add_doc li ul.agenda-actions li {
padding: 0;
}
.all-agenda-topics li .agenda_list_li.editModeOn:hover .iconBtn,
.allAgendaList .all-agenda-topics li .agenda_list_li.editModeOn:hover .fileUploadIcon,
.all-agenda-topics li .agenda_list_li.editModeOn .iconBtn{
display: none;
}
.all-agenda-topics li .agenda_list_li.editModeOn:hover .list-drag{
visibility: hidden;
}
.opacity-50{
opacity: 0.5;
filter: alpha(opacity=50);
}
.cursor-none{
pointer-events: none;
}
.pg-content-loader svg path{
fill: {{$color2}};
}
.all-agenda-topics.repdAgenda_topic>li.repd-li span {
/*padding-right: 18px;*/
}
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
.all-agenda-topics ol.topics-level1 span.all-agenda-topics+li {
margin-left: -68px;
}
.all-agenda-topics ol.topics-level1 span.all-agenda-topics+li span.list_number{
float: left;
}
.all-agenda-topics ol.topics-level1 span.all-agenda-topics+li .addFirstTopic {
display: list-item;
}
.all-agenda-topics ol.topics-level1 span.all-agenda-topics+li .addFirstTopic input,
.all-agenda-topics ol.topics-level1 span.all-agenda-topics+li .addFirstTopic button{
display: table-cell;
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
}
.taskStatusBtn ul.dropdown-menu li {
border-bottom: 1px solid #e2e1e1;
padding: 5px 10px;
display: inline-block;
width: 100%;
font-size: 12px;
cursor: pointer;
}
.taskStatusBtn ul.dropdown-menu li:hover {
background: #f3f3f3;
}

.listControlIcons{
display: table-cell;
width: 150px;
}
.addFirstTopic {
display: list-item;
}
.allAgendaList.prepdDateList ul.agenda-actions{
right: -76px;
}

.repd-list-head{
margin-bottom: 10px;
}
.toggleTaskInner h4.task-form-title {
padding: 5px 0px;
}
.allAgendaList.prepdDateList .level_add_doc ul.agenda-actions {
right: 0;
}
.new-topic-project .dropdown{
padding-left: 0px;
padding-right: 0px;
}
.all-agenda-topics.clearfix.repdAgenda_topic .repd-list-head {
padding-right: 80px;
}
.all-agenda-topics.repdAgenda_topic>li.repd-li span.order-no {
position: absolute;
left: 0;
top: 5px;
}
.all-agenda-topics.clearfix.repdAgenda_topic .repd-list-head {
padding-right: 80px;
padding-left: 40px;
}
.uploadProfile-img{
height: 120px;
width: 120px;
background-size: cover !important;
background-position: center !important;
position: relative;
}
.form-group span.text-danger.span-inline{
display: inline-block;
padding-left: 10px;
}
#editMsgModal .modal-footer,
#replyMsgModal .modal-footer{
padding: 10px 35px;
}
.pb-container{
margin-top:20px;

}
/*.uploadProfile-img span.crossBtn {
float: right;
margin-right: 5px;
cursor: pointer;
font-weight: bold;
}*/
.form-group.daycal-time label {
min-width: 70px;
}

.editTxt.collapse.in {
display: inline-block;
width: 100%;
}
.footer-btns .pb-container {
margin-top: 0px;
}
.default-form .pb-container{
vertical-align: bottom;
}
span.crossBtn {
width: 16px;
height: 16px;
display: inline-block;
text-align: center;
border: 1px solid #6b6868;
line-height: 13px;
background: #fff;
border-radius: 10px;
font-size: 12px;
position: absolute;
top: -6px;
right: -6px;
cursor: pointer;
}
#main-menu ul>li ul.dropdown-menu.bg-70 li{
background-color: {{$transprancy7}};
}
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
}
.userImgUploadBox{
background: {{$transprancy7}};
border: 1px solid {{$color2}};
padding: 35px;
min-height: 150px;
margin-bottom: 20px;
}
.userFullName{
display: table-cell;
padding: 10px 0px 0px 10px;
}
.simpleIcon svg {
width: 35px;
height: 35px;
}
.simpleIcon svg path{
fill: {{$color2}};
}
.cal-add {
display: inline-block;
position: relative;
}
.cal-add .react-add-to-calendar__dropdown {
border: 1px solid rgba(215, 215, 215, 0.38);
box-shadow: 2px 3px 6px -4px rgba(0, 0, 0, 0.34);
-webkit-box-shadow: 2px 3px 6px -4px rgba(0, 0, 0, 0.34);
-moz-box-shadow: 2px 3px 6px -4px rgba(0, 0, 0, 0.34);
position: absolute;
left: 0px;
width: 140px;
margin-top:10px;
}
.cal-add .react-add-to-calendar__dropdown::before {
content: '';
position: absolute;
width: 10px;
height: 8px;
border-bottom: 8px solid rgb(215, 215, 215);
border-left: 4px solid transparent;
border-right: 4px solid transparent;
top: -8px;
left: 10px;
}
.cal-add .react-add-to-calendar__dropdown::after {
content: '';
position: absolute;
width: 8px;
height: 8px;
border-bottom: 8px solid #fff;
border-left: 4px solid transparent;
border-right: 4px solid transparent;
top: -8px;
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
.listCheckdrog{
padding-left: 0px;
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
}
.cal-add svg path {
fill: {{$color2}};
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
}
.DayPicker-Day.DayPicker-Day--selected.DayPicker-Day--disabled,
.DayPicker-Day--selected:not(.DayPicker-Day--disabled):not(.DayPicker-Day--outside){
background: {{$color2}} !important;
color: #fff;
}
.single-msg .userPicName {
height: 60px !important;
width: 60px !important;
display: block;
position: absolute;
top: 4px;
left: 0;
border-radius: 50%;
border: 1px solid #3c4757;
font-size: 20px;
padding: 15px 5px;
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
.verticalTxtTop table>tbody>tr>td {
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
/*display: inline-block; */
}
.start-listing ul li button.btn-link:focus {
outline: none !important;
}
.message-bar{
background : {{$color1}};
padding: 5px 0px;
position: absolute;
width: 100%;
bottom: 40px;
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
margin: 0px 2%;
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
border-radius: 3px;
margin-left: 8px;
}
.chat-form .Select.is-clearable.is-searchable.Select--single {
width: 40%;
height: 30px;
float: left;
}
.chat-form .Select.is-clearable.is-searchable.Select--single .Select-control {
background-color: #fff;
border-color: #d9d9d9 #ccc #b3b3b3;
border-radius: 0px;
border: 1px solid #3c4757;
color: {{$color2}};
height: 30px;
}
.chat-form .Select-placeholder, .Select--single > .Select-control .Select-value {
line-height: 28px !important;
height: 30px;
font-size: 12px;
}
.chat-form .Select.is-clearable.is-searchable.Select--single .Select-control .Select-multi-value-wrapper .Select-input {
height: 28px;
padding-left: 10px;
padding-right: 10px;
vertical-align: middle;
}
.chat-form .Select-menu-outer{
height: 28px;
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
margin-left:10px;
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
.mobileEnableDiv h2{
font-size: 24px;
}

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
.row>.pb-container{
padding: 7px 0px;
}
.pdfGrayBtn svg path{
fill: grey !important;
}
.cke_chrome .cke_bottom{
display:none !important;
}
{{--.all-agenda-topics.repdAgenda_topic>li.repd-li.listing-open .toggleTask {--}}
{{--display: block;--}}
{{--visibility: visible;--}}
{{--}--}}


/*project style module*/
.flexbox{
display: -webkit-box;
display: -moz-box;
display: -ms-flexbox;
display: -webkit-flex;
display: flex;
}

/*========= Start Select Tast Radio Buttons ==========*/
.task-type-select {
max-width: 100%;
white-space: nowrap;
overflow-x: auto;
{{-- overflow:hidden; --}}
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
.radio-icons path {
fill: #6b6b6b;
}
.task-type-select label input[type="radio"] {
display: none;
}
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
/*width: 200px !important;
max-width: 200px !important;*/
margin: 0px 3px;
/*background-color: #fff;*/
position: relative;
}
.milestone-title-inner{
    width: 217px;
    padding: 9px 10px;
    border-bottom: 2px solid #ccc;
    background-color: #fff;
}
.milestone-title-dots{
width: 100%;
display: inline-block;
padding-right: 22px;
position: relative;
}
.milestone-heading{
position: relative;
/*margin: 10px 0px;*/
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
.milestone-heading span.startdate{}
.milestone-heading span.proname{
font-weight: bold;
}
.milestone-heading span.enddate{}
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
.milestone-title-box .info img{
height: 15px;
}
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
.milestone-title-box .info .dropdown-menu::before {
content: '';
position: absolute;
width: 6px;
height: 6px;
border-bottom: 6px solid #d6d6d6;
border-left: 3px solid transparent;
border-right: 3px solid transparent;
top: -7px;
right: 7px;
}
.milestone-title-box .info .dropdown-menu::after {
content: '';
position: absolute;
width: 6px;
height: 6px;
border-bottom: 6px solid #fff;
border-left: 3px solid transparent;
border-right: 3px solid transparent;
top: -6px;
right: 7px;
}
.milestone-title-box .info .dropdown-menu .svgicon{
margin: 5px 5px;
cursor: pointer;
}
.milestone-title-box .info {
    position: static;
}
.milestone-donedue-status {
/*border-color: #c7c7c7;
border-width: 2px 0px 0px 0px;
border-style: solid;
padding: 5px;*/
min-height: 16px;
}
.milestone-donedue-status .status {
    border: 1px solid #c7c7c7;
    float: left;
    width: 12px;
    height: 12px;
    margin: 2px;
}
.milestone-donedue-status .done{
background-color: #ccc;
}
.milestone-donedue-status .due{
background-color: #fff;
}
.milestone-title-box.active .milestone-heading span.proname{
color: {{$color2}};
}
.milestone-title-box.active .milestone-heading{
color: {{$color2}};
}
.milestone-title-box.active,
.milestone-title-box.active .milestone-title-inner,
.milestone-title-box.active .milestone-donedue-status .status{
border-color: {{$color2}};
}
.milestone-title-box.active .milestone-donedue-status .done{
background-color: {{$color2}}
}
.milestone-title-box.active .info{
opacity: 1;
visibility: visible;
}
.addnew-mileston {
    cursor: pointer;
    vertical-align: top;
    margin-top: 12px;
    width: 30px;
    height: 30px;
    position: absolute;
    right: 0;
    top: 20px;
    border: 2px solid #c7c7c7;
    border-radius: 50%;
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
}
.addnew-mileston-btn {
display: inline-block;
margin: auto;
}
/*.addnew-mileston img {
width: 16px;
}*/
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
.smooth-dnd-container span:not(:last-child) .milestone-content-child-box {
    border-right: 2px solid #c8c8c8;
}
.milestone-content-child-box:not(:last-child){
border-right: 1px solid #fff;
}
.milestone-content-child-box header {
border-bottom: 1px solid #ccc;
}
.milestone-content-child-box header h4{
font-size: 16px;
font-weight: bold;
}
.cards-outer {
padding: 10px 0px;
}
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
.card-box.proj-card{
padding: 0px;
/*border-left: 3px solid {{$color1}};*/
}
.proj-card .left {
max-width: 55px;
min-width: 55px;
background-color: #f9f9f9;
/*padding: 10px 5px;*/
/*border: 1px solid #e8e8e8;*/
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
.selected-task-icon svg{
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

.task-type-select .svgicon svg {
    width: 25px;
    height: 25px;
}
svg path{
fill: {{$color1}};
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
.comment-file-btns .btn-type1{
margin-left: 5px;
}
.rounded{
border-radius: 100%;
}
.comment-file-btns .btn-type1{
/*background-color: #777;*/
color: #fff;
font-size: 14px;
width: 30px;
height: 30px;
display: inline-block;
text-align: center;
margin-right: 7px;
position: relative;
vertical-align: middle;
}
/*.comment-file-btns .btn-type1 span {
position: absolute;
color: #333;
width: 100%;
height: 100%;
text-align: center;
padding: 10px 5px;
}*/
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
.comment-file-btns .svgicon svg {
width: 25px;
height: 25px;
}
.depend-task-btn {
display: inline-block;
width: 100%;
}
.task-dependdiv {
    /*width: 26px;
    height: 16px;
    background: #777;
    border: 1px solid #777;*/
    padding: 3px 0;
    border-radius: 5px;
    display: inline-block;
    float: left;
    cursor: pointer;
}
/*.task-depend-btn {
    width: 26px;
    height: 8px;
    border: 1px solid #e5e5e5;
background-color: #fff;
display: inline-block;
float: left;
cursor: pointer;
}
.task-depend-btn2{
    width: 26px;
    height: 8px;
border: 1px solid pink;
background: pink;
display: inline-block;
float: left;
cursor: pointer;
}*/
.depend-lbtn{
margin-left: 5px;
float: left;
}
.depend-rbtn{
margin-right: 5px;
float: right;
}
.depend-on-multitask{
/*float: right; */
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
.delete-workshop img {
height: 20px;
}

/* Change Task*/
#task-change {
margin-top: 5px;
display: none;
}

/*Comment Form*/
#comments-block{
display: none;
}
.comment-box-inner {
/*padding-left: 45px;*/
position: relative;
/*margin-top: 15px;*/
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
.commenter-img-sec {
min-width: 50px !important;
}
.comment-data{
width: 100%;
}
.comment-data .commenter, .comment-data .date {
color: #b3b3b3;
font-size: 14px;
width: 100%;
}
.comment-data .text{
margin-top: 2px;
width: 100%;
}


/**/
#doc-link-block{
display: none;
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
/**/

/* Modal CSS */
.modal-style1 .modal-header {
padding: 8px 15px;
background: {{$color2}};
color: #fff;
}
.modal-style1 .modal-title {
font-size: 18px;
}
.modal-style1 .modal-body{
min-height: 250px;
}
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

/**/
#task-dependon-task,#task-dependon-multitask,#ownerchange{
display: none;
}
.comment-box-inner h5.title {
font-weight: 400;
font-size: 13px;
color: #888888;
margin-bottom: 0px;
padding-bottom: 8px;
}
#task-dependon-task .comments-outer,
#task-dependon-multitask .comments-outer{
margin-bottom: 15px;
}
.mCSB_scrollTools {
opacity: .30;
filter: "alpha(opacity=30)";
-ms-filter: "alpha(opacity=30)";
}

/*#ownerchange, #task-change, #comments-block, #doc-link-block, #task-dependon-task, #task-dependon-multitask {
border-top: 1px dashed #ebebeb;
margin-top: 10px;
}*/
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
#ownerchangebtn{
cursor: pointer;
}
.add-task-type{
margin: 10px 0px;
}
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
.remove-exiting-box .removeBtn img{
height: 20px;
}
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
    /* overflow: hidden; */
    width: 300px;
    max-height: 100px;
    overflow: auto;
    display: none;
}
.multiusericon:hover .multiusericon-outer{
    display: block
}
.multipleuser_imgs {
    background: #fff;
    /*display: none;*/
    overflow: hidden;
    /* white-space: nowrap; */
    font-size: 0px;
    width: 100%;
}
.multipleuser_imgs .user_icon {
display: inline-block;
margin: 3px;
}
/*.multiusericon:hover .multipleuser_imgs{
display: block;
}*/
.modal.fade {
background: rgba(0, 0, 0, 0.3);
}
.card-info-tabs {
padding: 10px;
}
.cardinnerheader {
background: #f6f6f6;
display: table;
width: 100%;
}
.cardinnerheader>div.cardsTab{
display: table-cell;
vertical-align: middle;
text-align: center;
cursor: pointer;
padding: 5px 5px;
}
.cardinnerheader>div.activeTab.cardsTab {
    background: #fff;
}
.card-box .cardinnerheader .activeTab svg path{
    fill: {{$color1}};
}
.cardinnerheader .comment-file-btns{
float: none;
}
.cardinnerheader .comment-file-btns .btn-type1 {
margin-left: 0;
}
.proj-card .right .add-task-type {
max-width: 95%;
}
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
.card-info-btn img{
height: 15px;
}
.comment-box-inner .comment-file-btns .btn-type1 {
margin-left: 0;
}
#project {
padding: 0;
}
/* Tooltip */
.tooltip{
position: fixed;
}
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
border-bottom-color:{{$color1}};
}

/**/

@media(max-width: 1199px){
.milestone-content-child-box {
min-width: 310px !important;
max-width: 310px !important;
width: 310px !important;
}
.milestone-donedue-status .status {
width: 16px;
height: 16px;
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
    border-top: 1px solid #c8c8c8;
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
    border-bottom: 2px solid #c8c8c8;
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
    right: -2px;
    border-right: 2px solid #f6f6f6;
    height: 100%;
    top: 0;
}
.milestone-content-child-box .sc-EHOje,
.milestone-content-child-box .sc-EHOje .mCustomScrollBox {
width: 100%;
height: 100%;
}
.milestone-content-child-box .sc-EHOje .mCustomScrollBox .mCSB_container {
<<<<<<< HEAD
    min-height: 100%;
    display: flex;
    margin-right: 5px !important;
}
.milestone-content-child-box .sc-EHOje{
	padding-bottom: 0px !important;
	max-height: 100% !important;
}
.milestone-content-child-box .smooth-dnd-container.vertical{
	min-height: 100%;
	width: 100%;
    padding: 0px 10px;
=======
min-height: 100%;
display: flex;
}
.milestone-content-child-box .sc-EHOje{
padding-bottom: 0px !important;
max-height: 100% !important;
}
.milestone-content-child-box .smooth-dnd-container.vertical{
min-height: 100%;
width: 100%;
padding: 0px 10px;
>>>>>>> c6560c2b236adcc619fa6e0d46b0febe49084de5
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
<<<<<<< HEAD
=======

>>>>>>> c6560c2b236adcc619fa6e0d46b0febe49084de5
ul.dropdown-menu {
    z-index: 1001;
}
.task-addbar {
    border: 1px dashed #dddddd;
    min-height: 45px;
    margin-top: 10px;
    margin-bottom: 0;
    text-align: left;
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
}
button:focus{
    outline: none;
}
.tast-radiobox label {
    margin: 4px;
}
.tast-radiobox:first-child{
    margin-left: 0px !important;
}
.tast-radiobox:last-child{
    margin-right: 0px !important;
}
.modal-small-view{
    width:
}
.modal-small-view .modal-dialog {
    max-width: 420px;
    width: inherit;
}
.modal-small-view .modal-body {
    padding: 20px 15px;
}
.modal form label, .form-group label{
    color: #888888;
    font-size: 13px;
    font-weight: 400;
    margin-bottom: 5px;
}
.width-100{
    max-width: 100% !important;
}
.modal-small-view .milestoneNameBox{
    margin-top: 22px;
}
.milestone-content-child-box>div.ejtCpj{
    width: 100%;
}
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
    width: 26px;
    height: 26px;
    border-width: 3px;
}
.card-info-tabs .pb-container .pb-button {
    padding: 3px 5px;
    height: inherit;
    margin: 0;
}
.card-info-tabs .pb-container .pb-button span{
    font-size: 13px;
}
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
    width: 8px;
    height: 100%;
    float: left;
    background: {{$color2}};
    cursor:pointer;
    border-radius: 3px 0px 0px 3px;
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
.taskcolorPickerBox::before{
	content: '';
	position:absolute;
	left: -6px;
    top: 3px;
    width: 6px;
    height: 6px;
    border-right: 6px solid #d6d6d6;
    border-top: 6px solid transparent;
    border-bottom: 6px solid transparent;
}
.taskcolorPickerBox::after{
	content: '';
	position:absolute;
	left: -5px;
    top: 3px;
    width: 6px;
    height: 6px;
    border-right: 6px solid #fff;
    border-top: 6px solid transparent;
    border-bottom: 6px solid transparent;
    font-size: 0px;
}
.taskcolorPickerBox li{
	width: 15px;
	height: 15px;
	display: inline-block;
	margin: 2px;
	cursor: pointer;
}
.jHtXmx{
	cursor: move !important;
}
.Select--multi .Select-value {
    position: relative;
    padding-right: 20px;
}
.Select--multi .Select-value-icon {
    position: absolute;
    right: 0;
    border: none !important;
}
.task-type-select .svgicon{
	cursor: pointer;
}
.Select-placeholder, .Select--single > .Select-control .Select-value{
	line-height: 34px;
}
.colorChoose-block {
    max-width: 300px;
    margin: 0 auto;
}
.colorChoose-block .pb-container {
    margin-top: 0px;
    margin-left: 10px;
    vertical-align: middle;
}
.Select.is-clearable.is-searchable.Select--single .Select-menu-outer{
    position: relative;
}
.DayPickerInput{
	width: 100%;
}
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
    color: {{$color1}};
}
button.removeBtn:hover{
	color: {{$color1}};
}
.example-enter {
  opacity: 0.01;
}

.example-enter.example-enter-active {
  opacity: 1;
  transition: opacity 500ms ease-in;
}

.example-leave {
  opacity: 1;
}

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
.content-singleline .milestone-title-box {
    min-width: 150px;
}
.pro-mileston-name .editProjectName {
    position: absolute;
    right: 10px;
    top: 0;
    cursor: pointer;
    opcity: 0;
    visibility: hidden;
}
.pro-mileston-name:hover .editProjectName {
    opcity: 1;
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
.cursor{
    cursor: pointer;
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
.mid-datepicker .DayPicker-Weekday,
.mid-datepicker .DayPicker-Week{
font-size: 14px !important;
}
.mid-datepicker .DayPicker-Caption {
font-size: 16px;
}
.mid-datepicker .DayPicker-NavButton {
width: 11px !important;
height: 11px !important;
}
.task-assign span {
    /* color: {{$color2}}; */
    width: 34%;
    float: left;
}
.task-assign{
margin: 2px 0px;
}
.d-inline{
    display: inline-block !important;
}
.default-pic-bg{
    background-color:gray !important;
}
.pro-milestone-parent {
    background: #f6f6f6;
    margin-top: 20px;
}
.milestone-content{
    background-color: #f6f6f6 !important;
    border: 0px !important;
<<<<<<< HEAD
    height: 600px !important;
=======
height: 600px !important;
>>>>>>> c6560c2b236adcc619fa6e0d46b0febe49084de5
}
.addNew-taskBtn {
    padding: 5px 10px;
    font-size: 14px;
}
.addNew-taskBtn i{
    font-size: 16px;
    margin-right: 5px;
}
.card-box svg path{
    fill: #9d9d9d;
}
.card-box .task-depend-btn2 svg path{
    fill: {{$color1}}
}
.calendarDate {
    float: left;
    margin-left: 2px;
    padding-top: 3px;
}
.cardinnerheader .cardsTab .task-dependdiv{
    float: none !important;
}
.card-description h5{
    color: #505050;
    font-weight: 700;
    font-size: 14px;
    margin-top: 0px;
}
.depend-task-description .taskname {
    font-size: 14px;
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
    top: 2px;
    text-align: center;
}
.default-pic-bg{
    background: #e6e6e6 !important;
}
.card-description .text-data,
.grayTxt {
    width: 100%;
    color: #888888;
    font-size: 13px;
    line-height: 14px;
    margin-bottom: 5px;
}
.addTaskUser {
    border-top: 1px solid #f6f6f6;
    padding-top: 5px;
}
span.Select-arrow, span.Select-arrow:hover {
    width: 10px;
    height: 10px;
    border-color: transparent #ccc #ccc transparent !important;
    border-width: 2px !important;
    border-style: solid;
    transform: rotate(45deg);
    -webkit-border-radius: 0px !important;
    -moz-border-radius: 0px !important;
    -ms-border-radius: 0px !important;
    -o-border-radius: 0px !important;
    border-radius: 0px !important;
}
.cardbox-inner .Select-control {
    border: 1px solid #eaeaea;
}

/* Start Dependancy Icons Colors */
.card-box .depPink svg path {
   fill: #ffc0cb;
}
.card-box .depRed svg path {
   fill: #c00000 !important;
}
/* End Dependancy Icons Colors */
.milestone-title-box .info .dropdown-menu
.milestone-title-box .info .dropdown-menu .svgicon svg {
    width: 20px;
    height: 20px;
}
.milestone-inner-dropdown{
    width: 100%;
}
.milestone-inner-dropdown .icons-li {
    margin: auto auto 0 auto;
    padding: 0px 2px;
}
.milestone-inner-dropdown .icons-li svg{
    width: 20px;
    height: 20px;
}
.editBtnSvg {
    background: transparent;
    border: none;
    padding: 2px;
    display: block;
    margin-top: 5px;
}
.editBtnSvg svg{
    width: 20px;
    height: 20px;
    vertical-align: middle;
}
.editBtnSvg svg path{
    fill: {{$color1}};
}
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
.project-due-details{
    padding: 6px 10px;
}
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
.taskAssignedUserImg .multiusericon-outer{
    width: 260px;
}
.taskAssignedUserImg .userText-imgIcon{
    margin: 2px;
}
.comment-box-inner h5.title {
    font-weight: 400;
    font-size: 13px;
    color: #888888;
    margin-bottom: 0px;
    padding-bottom: 8px;
}
.title-w-border{
    border-bottom: 1px solid #f6f6f6;
}
.border-bottom-dashed {
    border-bottom-style: dashed;
}
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
.statusAssignBtns button{
    color: {{$transprancy2}};
}
.statusAssignBtns button.active{
    color: {{$color2}}
}
.svgRed svg g path{
fill:red !important;
}
.card-description .text-data {
min-height: 25px;
}
.tastWcount span{
vertical-align: text-top;
font-size: 14px;
line-height: 14px;
margin-left: 3px;
float: left;
}
.default-selectbox {
padding: 5px 5px;
max-width: 200px;
}
span.scrollLeftBtn,
span.scrollRightBtn{
    position: absolute;
    height: 20px;
    width: 20px;
    margin: auto;
    top: 36px;
    background-size: cover !important;
cursor: pointer;
}
span.scrollLeftBtn{
    left: -20px;
    background: url('public/img/left-arrow.png') no-repeat center;
}
span.scrollRightBtn{
    right: 0px;
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
  /*Hiding the select arrow for firefox*/
  -moz-appearance: none;
  /*Hiding the select arrow for chrome*/
  -webkit-appearance:none;
  /*Hiding the select arrow default implementation*/
  appearance: none;
}
/*Hiding the select arrow for IE10*/
.custom-select select::-ms-expand {
    display: none;
}

.custom-select::before,
.custom-select::after {
  content: "";
  position: absolute;
  pointer-events: none;
}

.custom-select::after { /*  Custom dropdown arrow */
  content: "\25BC";
  height: 1em;
  font-size: .625em;
  line-height: 1;
  right: 1.2em;
  top: 50%;
  margin-top: -.5em;
}

.custom-select::before { /*  Custom dropdown arrow cover */
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
.form-control:focus,
:focus{
    box-shadow: none !important;
    border-color: #e9e9e9 !important;
}
.DayPicker{
    margin-bottom: 5px;
}

@media (max-width: 1199px){
    .cardsTab .svgicon svg {
        width: 20px;
        height: 20px;
    }
}

/*19 sept 18*/
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
    padding: 5px 20px;
}
.iconRadioBtn label input[type="radio"]
{
    width: 0px;
    height: 0px;
    visibility: hidden;
    opacity: 0;
    /*filter: alpha(opacity=0);*/
}
.iconRadioBtn label input[type="radio"]:checked + span{
    border-color: {{$color2}};
}
.iconRadioBtn label span svg {
    width: 70px;
    height: 70px;
    cursor: pointer;
}
.iconRadioBtn label span svg path,
.rdo-icon label svg path{
    fill: #444;
}
.iconRadioBtn label input[type="radio"]:checked + span svg path,
.rdo-icon input[type="radio"]:checked + label svg path{
    fill: {{$color2}};
}
.cardsTab .svgicon svg {
    width: 20px;
    height: 20px;
}
.DraftEditor-editorContainer, .DraftEditor-editorContainer * {
    font-family: 'Lato', sans-serif;
}

/*Project Improvement 3 Style*/
.menu-space {
    float: left;
    width: 50px;
    display: block;
    height: 1px;
}
.rdo-icon label {
    border: 2px solid #e1e1e1;
    width: 52px;
    height: 52px;
    padding: 5px;
    display: -webkit-box;
	display: -moz-box;
	display: -ms-flexbox;
	display: -webkit-flex;
	display: flex;
	cursor: pointer;
}
.rdo-icon svg {
    width: 40px;
    height: 40px;
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
.rdo-icon input[type="radio"]:checked + label{
	border-color: {{$color2}};
}
<<<<<<< HEAD
.discussion-form .cke_contents {
    /*height: unset !important;
    max-height: 250px !important;*/
    min-height: 100px !important;
=======
.cke_top, .cke_contents, .cke_bottom {
    display: block;
    overflow: hidden;
>>>>>>> c6560c2b236adcc619fa6e0d46b0febe49084de5
}
.cke_contents{
    height: 120px
}

.discussion-form .cke_contents body.cke_editable {
    margin: 5px 10px;
}
.discussion-form .cke_contents body.cke_editable p {
    margin: 2px 0px;
}
.selectedTxt{
	font-size: 18px;
	color: {{$color2}};
	margin-top: 5px;
}
.mr-n80{
	margin-right: -80px;
}
.externalTopicDoc{
	margin-top: 20px;
}
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
    border: 1px solid #dad5d5;
    border-bottom: none;
}
.externaldocsBg{
	background-color: #f7f7f7;
    padding: 15px 0px;
    border-radius: 0px 5px 5px 5px;
    border: 1px solid #dad5d5;
}
.externalTopicDoc .pb-container{
	margin-top: 0px;
}
.externalTopicDoc .pb-container .pb-button {
    line-height: 14px;
    padding: 10px;
    height: 42px;
}
.externalTopicDoc .pb-container.loading .pb-button{
    height: 36px;
}
.externalTopicDoc .pb-container .pb-button span{
	vertical-align: top;	
}
.externalTopicDoc form .form-sec-title {
    font-size: 16px;
    margin-bottom: -1px;
}
.project-milestone-details .milestoneCount,
.project-tasks-details .taskCount {
    font-size: 18px;
    font-weight: 600;
    float: right;
    margin-right: 0px;
}
.project-milestone-details .milestoneCount small,
.project-tasks-details .taskCount small{
    font-size: 13px;
}
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
.project-due-details{
	text-align: left;
}
.taskDueDateDays {
    font-size: 18px;
    vertical-align: bottom;
    display: inline-block;
    line-height: 24px;
    float: right;
    margin-top: 10px;
}
.needFeature{
	text-align: right;
}
.needFeature .lbox{
	margin-right: 25px;
}
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
.resize-none{
	resize: none;
}
.projectAccess {
    min-height: 45px;
    padding: 10px !important;
}
.userPermissionIcon svg {
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
    display: -webkit-box;
	display: -moz-box;
	display: -ms-flexbox;
	display: -webkit-flex;
	display: flex;
    margin: 10px 0px;
    list-style: none;
    width: 50%;
    float: left;
}
/*.agendaDocsListMenu li:not(:last-child):before {
    content: '';
    position: absolute;
    height: 100%;
    width: 1px;
    background: {{$color1}};
    left: 20px;
    z-index: -1;
    top: 20px;
}*/
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
.agendaDocsListMenu li span {
    margin: auto 0;
}
.agendaDocsListMenu li span.dlink {
    font-size: 14px;
    color: #888888;
}
.btn-transparent{
	background-color: transparent !important;
	border: none !important;
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
.userPermissionIconBtn{
	position: relative;
}
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
    background-image: url(../../work/ops_laravel/public/img/iphone-mockup.png);
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
	display: -webkit-box;
	display: -moz-box;
	display: -ms-flexbox;
	display: -webkit-flex;
	display: flex;
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
}
#mobViewHeader_bottom2{
	background-color: {{$color2}};
	min-height: 25px;
	width: 100%;
	float: left;
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
.mobViewListRow .icon-right i{
	font-size: 18px;
}
.mobViewListRow .icon-c-red i{
	color: red;
}
.mobViewListRow .icon-c-green i{
	color: green;
}
.mobViewListRow .listTitle{
	padding: 5px 20px;
	background-image: url('../../work/ops_laravel/public/img/vertical-dots.png');
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
.mobViewOverlay_popup .popup_body{
	background: #fff;
}
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
}
.prepdDateList .externalTopicDoc form {
    margin-right: -80px;
}
.picUpload_parent{
	width: 200px;
	height: 200px;
	position: relative;
}
.picUpload_parent div:first-child{
	border-radius: 100%;
}
<<<<<<< HEAD

.milestone-content-child-box .sc-EHOje:focus, .milestone-content-child-box .sc-EHOje *:focus {
    outline: none !important;
    border: none;
}
=======
.staff-login-form {
    max-width: 338px;
    padding: 25px;
}
>>>>>>> c6560c2b236adcc619fa6e0d46b0febe49084de5
