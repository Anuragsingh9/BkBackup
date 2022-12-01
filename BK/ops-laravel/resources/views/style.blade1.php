@php
$css_data = dynamicCss();
$color1 = $css_data['color1'];
$color2= $css_data['color2'];
$color3= $css_data['color3'];
//$transprancy7=$css_data['transprancy7'];
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
padding-bottom: 40px;
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

.ml-3{margin-left: 3px;}
.ml-5{margin-left: 5px;}
.ml-10{margin-left: 10px;}
.ml-15{margin-left: 15px;}

.mr-3{margin-right: 3px;}
.mr-5{margin-right: 5px;}
.mr-10{margin-right: 10px;}
.mr-15{margin-right: 15px;}

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
width: 35px;
height: 35px;
display: inline-block;
background-size: cover;
border-radius: 50%;
vertical-align: middle;
position: absolute;
left: 0;
top: 0;
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
background: url(../../public/img/caution.png) no-repeat;
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
border: 1px solid #cecece;
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
min-width: initial;
}
.inline-form-sec button.btn[type="submit"]:hover i {
color: #008cc2;
}
.title-sec{
padding-top: 26px;
padding-bottom: 26px;
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
}
.drop-style1 i {
margin-left: 5px;
margin-top: 4px;
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
padding-bottom: 34px;
}
.nav-tabs {
border-bottom: 1px solid #bababb;
}
.nav-tabs>li {
margin-bottom: 0;
}
.nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus,.tab-menu>li.current-menu-item>a {
color: #fff;
background-color: {{$transprancy2}};
border: 1px solid {{$transprancy2}};
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
margin-top: 20px;
}
.switch {
position: relative;
display: inline-block;
width: 32px;
height: 13px;
margin: 4px 0;
margin-right: 20px;
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
.tooltip-inner {
padding: 5px 6px;
color: #fff;
font-size: 11px;
background-color: {{$color1}};
border: 1px solid {{$color1}};
}
.tooltip.top .tooltip-arrow {
border-top-color: {{$color1}};
}
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
.all-agenda-topics{
padding: 0;
}
.all-agenda-topics .rc-draggable-list-draggableRow>li,.all-agenda-topics>li {
border-top: 1px solid #b9b9b9;
padding: 10px 0;
padding-left: 50px;
/* padding-right: 70px; */
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
position: absolute;
left: 10px;
top: 10px;
z-index: 1;
}
.all-agenda-topics .text{
display: inline-block;
margin-right: 10px;
}

.agenda-heading-action {
height: 20px;
position: relative;
}
ul.agenda-actions {
position: absolute;
right: 0;
top: 5px;
margin: 0;
width: 70px;
}
/*.topics-level1 ul.agenda-actions{
right: -70px;
}*/
ol.all-agenda-topics>li>ul.agenda-actions{
top: 10px;
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
.agenda-actions li i.active{
color: {{$color2}}
}
.agenda-actions li.collapsed i{
color: #adadad
}
i.fa-recycle {
position: relative;
top: 1px;
}
i.fa-gavel.active,i.fa-gavel[aria-expanded="true"] {
color: {{$color2}};
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
}
.discussion-block{
margin-left: 14px;
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
padding-left: 28px;
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
.single-msg .single-reply-msg .user-img{
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
background: #d9534f;
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
padding-left: 40px;
}
.setting-opt-menu ul li .switch {
position: absolute;
left: 0;
top: 0;
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
padding: 15px;
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
display: inline-block;
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
border-radius: 5px;
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
padding: 0.7em 1em;
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
border-width: 6px;
border-color: {{$color1}};
cursor: wait;
border-radius: 50%;
background-color: transparent;
padding: 0;
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
padding: 6px;
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
padding: 10px 0px 10px 35px;
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
padding: 7px 10px;
outline: none !important;
box-shadow: none !important;
}
.iconBtn{
padding: 5px 5px;
margin-right: 5px;
visibility: hidden;
display: none;
color: #9e9c9c;
}
.list-drag{
float: left;
opacity: 0.5;
}
.list-drag, .fileUploadIcon{
visibility: hidden;
cursor: pointer;
display: none;
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
.new_topic_line button {
background: {{$color2}};
color: #fff;
border: none;
padding: 5px 12px;
}
.allAgendaList .all-agenda-topics li .agenda_list_li:hover{
background: #ededed;
}
.all-agenda-topics li .agenda_list_li:hover .iconBtn,
.all-agenda-topics li .agenda_list_li:hover .list-drag,
.allAgendaList .all-agenda-topics li .agenda_list_li:hover .fileUploadIcon{
visibility: visible;
display:inline-block;
}
.allAgendaList .all-agenda-topics .order-no {
position: absolute;
left: 35px;
top: 10px;
z-index: 1;
}
.allAgendaList .all-agenda-topics .list-drag {
position: relative;
left: 0px;
margin-right: 10px;
}
.allAgendaList .all-agenda-topics .rc-draggable-list-draggableRow>li{
padding-left: 10px;
}
.allAgendaList .all-agenda-topics .order-no {
position: relative;
left: 0;
top: 0;
z-index: 1;
margin-right: 10px;
}
.allAgendaList .all-agenda-topics .list_number{
margin-right: 10px;
}
.new_topic_line {
padding-left: 28px;
}
.fa-3x{
font-size: 20px !important;
}
.toggleTask{
padding: 15px 0px;
margin: 20px 0px;
}
.toggleTask::before{
content: '';
position: absolute;
box-shadow: 0px -14px 18px -17px #000;
width: 100%;
height: 100%;
top: 0;
}
.toggleTask::after {
content: '';
position: absolute;
box-shadow: 0px 15px 18px -13px #000;
width: 100%;
height: 100%;
top: 0;
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
ol.first-level-addbox li {
padding: 7px 0px 7px 10px;
}
.first-level-addbox .new_topic_line{
padding:0;
}
/* 2 feb 2018 */
.allAgendaList .all-agenda-topics li .agenda_list_li {
padding: 5px 0px;
position: relative;
}
.allAgendaList .all-agenda-topics li .topics-level1 .agenda_list_li{ 
padding-left: 28px;
}
.allAgendaList .all-agenda-topics li .topics-level2 .agenda_list_li{ 
padding-left: 56px;
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
width: 24px;
}
.level_add_doc li ul.agenda-actions li{
width: 100%;
}
.repd_list_level3{
padding-left:30px;
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
