@php
    $css_data = dynamicCss();
    $color1 = $css_data['color1'];
    $headerColor1 = $css_data['headerColor1'];
    $color2= $css_data['color2'];
    $headerColor2= $css_data['headerColor2'];
    $color3= $css_data['color3'];
    $transprancy7=$css_data['transprancy7'];
    $transprancy1=$css_data['transprancy1'];
    $transprancy2=$css_data['transprancy2'];
@endphp


/************************/
/**** CRM DESIGN CSS ****/
/************************/


.row-space-35{
	margin-left: -35px;
	margin-right: -35px;
}
.x-space-35{
	padding-left: 35px;
	padding-right: 35px;
}
.planned-header {
    width: 100%;
    text-align: center;
    font-size: 18px;
    position: relative;
    margin-bottom: 15px;
    display: inline-block;
}
.planned-header::after{
	content: '';
	position: absolute;
	width: 100%;
	height: 1px;
	background-color: #e5e5e5;
	top: 0;
	bottom: 0;
	left: 0;
	margin: auto;
}
.planned-header span{
	background-color: #fff;
	display: inline-block;
	padding: 5px 10px;
	position: relative;
	z-index: 1;
}
.task-timeline-detail {
    border: 1px solid #e5e5e5;
    padding: 5px 15px;
    position: relative;
    width: 100%;
    min-height: 50px;
}
.task-timeline-detail::before {
    content: '';
    position: absolute;
    width: 8px;
    height: 16px;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 8px solid #ddd;
    left: -8px;
    top: 0;
}
.svg-icon-box {
    margin-right: 30px;
    display: inline-block;
    background: #fff;
    position: relative;
    z-index: 1;
    vertical-align: middle;
    padding: 5px;
    /*border: 1px solid #e5e5e5;*/
}
.svg-icon-box svg {
    width: 24px;
    height: 24px;
    float: left;
    /*background-color: #fff;*/
}
.svg-icon-box svg path {
    fill: #9d9d9d;
}
.task-plan-block {
    margin-bottom: 8px;
}
.task-timline-right,
.task-timline-note{
	font-size: 12px;
    color: #888888;
    float: left;
    width: 100%;
}
.task-timline-right{
	margin-top: 5px;
}
.task-timline-note {
    padding-right: 15px;
    position: relative;
}
.task-timline-right .date-time, .task-timline-right .taskBy, .task-timline-right .taskFor,
.task-timline-note .date-time, .task-timline-note .taskBy, .task-timline-note .taskFor {
    display: inline-block;
}
.t-task-title.due-task,
.task-timline-right .date-time.due-task{
	color: red;
}
.task-plan-block-inner .task-icon {
    position: relative;
}
.task-plan-block:not(:last-child) .task-plan-block-inner .task-icon:after {
    content: '';
    position: absolute;
    height: 100%;
    width: 1px;
    border: 1px dashed #e5e5e5;
    left: 16px;
    top: 15px;
}
.task-timline-note-desc {
    font-size: 14px;
    line-height: 16px;
    margin-top: 3px;
    color: #444;
    float: left;
    width: 100%;
}
.task-timline-note .more{
    position: absolute;
    right: 0;
    top: 0;
}
.task-timline-note .more .dropdown-menu{
    right: 0;
    left: inherit;
    min-width: 120px;
}
.task-timline-note .more {
    position: absolute;
    right: -15px;
    top: -5px;
}
.task-timline-note .more img{
	height: 12px;
}
.task-timline-note .more button.dropdown-toggle {
    background: transparent;
    padding: 5px 13px;
    border: none;
}

.task-name-radio .custom-radio {
    float: left;
    display: inline-block;
}
.custom-radio [type="radio"]:checked,
.custom-radio [type="radio"]:not(:checked) {
    /*position: absolute;
    left: -9999px;*/
    width: 0px;
    height: 0px;
    visibility: hidden;
    margin-left: -3px;
}
.custom-radio [type="radio"]:checked + .radio-slide,
.custom-radio [type="radio"]:not(:checked) + .radio-slide
{
    position: relative;
    padding-left: 22px;
    cursor: pointer;
    line-height: 20px;
    display: inline-block;
    color: #666;
    vertical-align: middle;
    height: 14px;
    margin: 0;
}
.custom-radio [type="radio"]:checked + .radio-slide:before,
.custom-radio [type="radio"]:not(:checked) + .radio-slide:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 14px;
    height: 14px;
    border: 1px solid #ccc;
    border-radius: 100%;
    background: #fff;
}
.custom-radio [type="radio"]:checked + .radio-slide:after,
.custom-radio [type="radio"]:not(:checked) + .radio-slide:after {
    content: '';
    width: 8px;
    height: 8px;
    background: {{$color2}};
    position: absolute;
    top: 3px;
    left: 3px;
    border-radius: 100%;
    -webkit-transition: all 0.2s ease;
    transition: all 0.2s ease;
}
.custom-radio [type="radio"]:not(:checked) + .radio-slide:after {
    opacity: 0;
    -webkit-transform: scale(0);
    transform: scale(0);
}
.custom-radio [type="radio"]:checked + .radio-slide:after {
    opacity: 1;
    -webkit-transform: scale(1);
    transform: scale(1);
}


.custom-checkSlide [type="checkbox"]:checked,
.custom-checkSlide [type="checkbox"]:not(:checked) {
    /*position: absolute;
    left: -9999px;*/
    width: 0px;
    height: 0px;
    visibility: hidden;
    margin-left: -3px;
}
.custom-checkSlide [type="checkbox"]:checked + .checkbox-slide,
.custom-checkSlide [type="checkbox"]:not(:checked) + .checkbox-slide
{
    position: relative;
    padding-left: 22px;
    cursor: pointer;
    line-height: 20px;
    display: inline-block;
    color: #666;
    vertical-align: middle;
    height: 14px;
    margin: 0;
}
.custom-checkSlide [type="checkbox"]:checked + .checkbox-slide:before,
.custom-checkSlide [type="checkbox"]:not(:checked) + .checkbox-slide:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 14px;
    height: 14px;
    border: 1px solid #ccc;
    background: #fff;
}
.custom-checkSlide [type="checkbox"]:checked + .checkbox-slide:after,
.custom-checkSlide [type="checkbox"]:not(:checked) + .checkbox-slide:after {
    content: '';
    width: 8px;
    height: 5px;
    position: absolute;
    top: 3px;
    left: 3px;
    -webkit-transition: all 0.2s ease;
    transition: all 0.2s ease;
    border-left: 2px solid {{$color2}};
    border-bottom: 2px solid {{$color2}};
}
.custom-checkSlide [type="checkbox"]:not(:checked) + .checkbox-slide:after {
    opacity: 0;
    -webkit-transform: scale(0);
    transform: scale(0);
}
.custom-checkSlide [type="checkbox"]:checked + .checkbox-slide:after {
    opacity: 1;
    -webkit-transform: scale(1) rotate(-45deg);
    transform: scale(1) rotate(-45deg);
}

/**********/
.crm-timeline-header{
    background-color: {{$color2}};
    padding: 0px 1px;
}
.menu-searchbox{
	padding: 3px 3px 1px 2px;
	border-right: 1px solid rgba(255, 255, 255, 0.3);
}
.menu-searchbox .searchbox {
    position: relative;
    width: 300px;
}
.menu-searchbox .searchbox .search-icon {
    position: absolute;
    font-size: 16px;
    top: 0px;
    left: 8px;
    bottom: 0;
    margin: auto;
    color: #bbb;
    height: 14px;
}
.menu-searchbox .searchbox .search-icon svg{
	width: 14px;
    height: 14px;
    float: left;
}
.menu-searchbox .searchbox .search-icon svg path{
	fill: #888;
}
.menu-searchbox .searchbox input {
    padding-left: 27px;
    padding-right: 5px;
    font-size: 14px;
    border-radius: 0px;
    box-shadow: none;
    border-color: #f6f6f6;
    height: 30px;
}
.crm-menu-first{
	flex: 1;
}
.crm-menu ul {
    margin: 0px;
    padding: 0px 10px;
    border-right: 1px solid rgba(255, 255, 255, 0.3);
    /* display: flex; */
    flex-wrap: nowrap;
    flex: auto;
    text-align: right;
}
.crm-menu ul li{
	flex: inherit;
    display: inline-block;
    padding: 0px 5px;
    position: relative;
}
.crm-timeline-header .dropdown .dropdown-menu li{
    padding: 0px 5px;
    position: relative;
    width: 100%;
    display: inline-block;
}
.crm-menu ul li:hover,
.crm-timeline-header .dropdown .dropdown-menu li:hover {
    background-color: {{$color2}};
}
.crm-menu ul li a{
	color: #fff;
    padding: 7px 10px;
    display: block;
    font-size: 14px;
    line-height: 22px;
}
.crm-timeline-header .dropdown .dropdown-menu li:not(:first-child){
    border-top: 1px solid {{$color2}};
}
.crm-menu ul li a span {
    font-size: 11px;
    margin-left: 3px;
    vertical-align: middle;
}
.crm-menu ul li .dropdown-menu,
.crm-timeline-header .dropdown .dropdown-menu {
    padding: 0px 0px;
    background-color: {{$color1}};
    border: none;
    border-radius: 0px;
}
.crm-menu ul li .dropdown-menu li{
    width: 100%;
    text-align: left;
}
.crm-menu ul li .dropdown-menu li a,
.crm-timeline-header .dropdown .dropdown-menu li a {
    display: block;
    padding: 8px 11px !important;
    clear: both;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    color: #ffffff !important;
    white-space: nowrap;
    cursor: pointer;
}
.crm-menu ul li a.active{
	color: #fff;
}
.crm-menu ul li a:hover,
.crm-menu ul li a:focus,
.crm-timeline-header .dropdown .dropdown-menu li a:hover,
.crm-timeline-header .dropdown .dropdown-menu li a:focus{
    background-color: {{$color2}};
    color: #fff;
	text-decoration: none;
}
.crm-filter, .crm-setting {
    padding: 0px 0px;
}
.crm-setting {
    width: 55px;
}
.crm-filter{
	border-right: 1px solid rgba(255, 255, 255, 0.3);
}
.crm-filter .dropdown .btn,
.crm-setting .dropdown .btn {
	background-color: transparent;
	color: #fff;
    font-size: 14px;
    padding: 7px 15px;
    min-width: inherit;
    border: none !important;
    line-height: 22px;
}
.crm-filter .dropdown .btn:active,
.crm-setting .dropdown .btn:active,
.crm-filter .dropdown .btn:focus,
.crm-setting .dropdown .btn:focus{
	box-shadow: none;
	outline: none;
}
.crm-filter .dropdown .btn span, .crm-setting .dropdown .btn span {
    font-size: 11px;
    margin-left: 3px;
    vertical-align: middle;
}
.crm-setting .dropdown .btn {
    font-size: 18px;
    padding: 7px 14px;
}
.crm-setting .dropdown .dropdown-menu,
.crm-filter .dropdown .dropdown-menu {
    left: inherit;
    right: 0;
    margin-top: 0px;
}
.crm-filter .dropdown input.form-control,
.crm-filter input.form-control{
	border-radius: 0;
    font-size: 13px;
    padding: 8px 10px;
    height: 36px;
}
.crm-alpha-letter {
    background-color: #e5e5e5;
    padding: 10px 0px;
}

.crm-alpha-letter ul{
	margin-bottom: 0px;
}
.crm-main {
    padding: 30px 0px;
}
.table-border-box {
    border: 1px solid #ddd;
}
.crm-alpha-letter .list-inline {
    margin-left: -10px;
}
.crm-alpha-letter .list-inline>li {
    padding-left: 10px;
    padding-right: 10px;
}
.crm-alpha-letter li span {
    width: 35px;
    height: 35px;
    display: inline-block;
    text-align: center;
    border: 1px solid #b8b2ae;
    border-radius: 25px;
    color: #b8b2ae;
    font-size: 18px;
    line-height: 33px;
}
.crm-alpha-letter li.active span{
	color: {{$color2}};
	border-color: {{$color2}};
}
.crm-filter-section {
    margin-bottom: 30px;
}
#crmResultAddPopup .modal-header {
    padding: 8px 15px;
    background: {{$color2}};
    color: #fff;
}
#crmResultAddPopup .modal-header button.close {
    color: #fff;
    box-shadow: none !important;
    opacity: 0.8;
    font-weight: 400;
    font-size: 22px;
}
.crm-filter-section .flter-name {
    font-size: 17px;
    color: {{$color2}};
    margin-bottom: 20px;
    display: inline-block;
    width: 100%;
}
.crm-filter-section .flter-name span {
    color: #444;
}
.addResult-list-txtBtn{
	padding-left: 0px;
}
.addResult-list-txtBtn span{
	padding-right: 15px;
}

.custom-radio [type="radio"]:checked,
.custom-radio [type="radio"]:not(:checked) {
    /*position: absolute;
    left: -9999px;*/
    width: 0px;
    height: 0px;
    visibility: hidden;
    margin-left: -3px;
}
.custom-radio [type="radio"]:checked + .radio-slide,
.custom-radio [type="radio"]:not(:checked) + .radio-slide
{
    position: relative;
    padding-left: 18px;
    cursor: pointer;
    line-height: 20px;
    display: inline-block;
    color: #666;
    vertical-align: middle;
    height: 14px;
    margin: 0;
    float: left;
}
.custom-radio [type="radio"]:checked + .radio-slide:before,
.custom-radio [type="radio"]:not(:checked) + .radio-slide:before {
    content: '';
    position: absolute;
    left: 0;
    top: 3px;
    width: 14px;
    height: 14px;
    border: 1px solid #ccc;
    border-radius: 100%;
    background: #fff;
}
.custom-radio [type="radio"]:checked + .radio-slide:after,
.custom-radio [type="radio"]:not(:checked) + .radio-slide:after {
    content: '';
    width: 8px;
    height: 8px;
    background: {{$color2}};
    position: absolute;
    top: 6px;
    left: 3px;
    border-radius: 100%;
    -webkit-transition: all 0.2s ease;
    transition: all 0.2s ease;
}
.custom-radio [type="radio"]:not(:checked) + .radio-slide:after {
    opacity: 0;
    -webkit-transform: scale(0);
    transform: scale(0);
}
.custom-radio [type="radio"]:checked + .radio-slide:after {
    opacity: 1;
    -webkit-transform: scale(1);
    transform: scale(1);
}
.custom_rdo-outer{
    float: left;
    display: inline-block;
}
.custom_rdo-outer:not(:last-child){
    margin-right: 10px;
}

@media (max-width: 991px){
	.crm-menu ul li a {
	    padding: 7px 5px 6px 5px;
	    font-size: 14px;
	}
	.menu-searchbox .searchbox {
	    position: relative;
	    width: 160px;
	}
	.crm-filter .dropdown .btn{
		font-size: 14px;
	}
	.crm-filter, .crm-setting {
	    padding: 14px 5px 13px 5px;
	}
	.crm-menu ul {
	    padding: 14px 5px;
	}
}
.search-points {
    background-color: #f5f5f5f5;
    padding: 5px 10px;
    border: 1px solid {{$transprancy7}};
}

.crm-lr-fields {
    height: 100%;
    padding-top: 15px;
}
.crm-lr-fields .crm-lfield {
    min-height: 150px;
    border-right: 1px solid {{$transprancy7}};
}

/*=== Text Radio ===*/
.txt-rdo input{
    width: 0px;
    height: 0px;
    opacity: 0;
    filter: alpha(opacity=0);
}
.txt-rdo input + label.radio-slide {
    width: 30px;
    height: 30px;
    display: inline-block;
    border: 1px solid #c7c7c7;
    border-radius: 25px;
    text-align: center;
    line-height: 28px;
    cursor: pointer;
    margin-bottom: 0px;
    color: #888888;
}
.txt-rdo input:checked + label.radio-slide {
    border-color: {{$color2}};
    color: {{$color2}};
}
.highLightTxt{
    color: {{$color2}}
}
.app-body {
    padding-bottom: 12px;
    min-height: calc(100vh - 267px );
    height: calc(100% - 267px );
}
.app-body{
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
}
#root, .app, #header, #pg-loader,
main.main,
.app-body,
.main-inner-sec{
    width: 100%;
}
.main-inner-sec{
    height: 100%;
}
#pg-loader{
    text-align : center;
}
.crm-body {
    height: calc(100% - 53px);
}
.crm-search-result li.list-group-item {
    border-color: {{$transprancy7}};
    padding: 10px 10px;
}
.crm-result-header, .crm-right-nav {
    border: 1px solid #ececec;
    background: #f7f6f7;
    padding: 5px 5px 5px 15px;
    margin-bottom: 5px;
    width: 100%;
    display: inline-block;
}
.crm-result-header h5{
    color: {{ $color2 }};
}
.crm-right-nav ul{
    display: table;
    margin-bottom: 0px;
    padding-left: 0px;
    width: 100%;
}
.crm-right-nav ul li {
    display: table-cell;
    padding: 2px;
    text-align: center;
    position: relative;
}
.crm-right-nav ul li:not(:last-child):after{
    content: '';
    border-right: 1px solid {{$transprancy7}};
    height: 15px;
    width: 0px;
    position: absolute;
    top: 0;
    bottom: 0;
    right: 0;
    margin: auto;
}
.crm-right-nav ul li a {
    color: #333;
    font-size: 14px;
    text-decoration: none;
    line-height: 15px;
}
.crm-right-nav ul li a.active{
    color : {{$color2}};
}
.panelStyle .panel-heading {
    background-color: #f5f5f5 !important;
    padding: 5px 5px;
}
.panelStyle .panel-heading .panel-title a.accordion-toggle {
    color: {{$color2}};
    font-size: 14px;
    width: 100%;
    display: inline-block;
    padding: 5px 10px;
    text-decoration: none;
    line-height: 16px;
}
.panel-group .panelStyle{
    margin-bottom: 0;
    border-radius: 0px;
}
.panel-group .panelStyle + .panelStyle {
    margin-top: 0;
}
.panel-group .panelStyle + .panelStyle .panel-body {
    min-height: 100px;
}
.panel-group .panelStyle{
    border-bottom-width: 0px;
}
.panel-group .panelStyle:last-child{
    border-bottom-width: 1px;
}
.panelStyle {
    border-color: {{$transprancy7}};
}
.panelStyle .panel-heading .accordion-toggle:after{
    color: {{$transprancy7}};
}
.crm-search-result ul li span.first-letter{
    width: 25px;
    height: 25px;
    border: 1px solid #c7c7c7;
    display: inline-block;
    border-radius: 15px;
    color: #888888;
    line-height: 23px;
    text-align: center;
    margin-right: 5px
}
.form-control-sm {
    height: 36px;
}
.editable-select .input-group{
    width: 100%
}
.editable-select .input-group .Select {
    vertical-align: top;
}
.editable-select .control-btns{
    margin-left: -1px;
    position: relative;
    z-index: 5;
    vertical-align: top;
}
.editable-select .control-btns span {
    width: 36px;
    top: 0;
    height: 36px;
    line-height: 36px;
    text-align: center;
    color: {{$color2}};
    border: 1px solid #cccccc;
    margin-left: -1px;
    background-color: #ffff;
    font-size: 14px;
}
.editable-select .selected-valuebox{
    height: 36px;
    padding: 6px 10px 6px 0px;
    width: 100%;
    display: table-cell;
    line-height: 24px;
}
.editable-select .Select-control .Select-value {
    height: 34px;
    line-height: 34px !important;
    font-size: 14px;
}
.editable-select .input-group .Select .Select-control {
    border-radius: 4px 0px 0px 4px;
}
.taskBy{
    padding-left: 5px;
}
.list-group-item{
    display: inline-flex;
    width: 100%;
}
.crm-lfield .skill-action-btns .svgicon svg,
.map-control-btns span.svgicon svg{
    width: 18px;
    height: 18px;
}
.edit-filter-categories .crm-alpha-letter {
    background: transparent;
    margin-bottom: 20px;
    padding: 5px 0px 0px 0px;
}
.edit-filter-categories .crm-alpha-letter .list-inline {
    margin-left: -5px;
}
.edit-filter-categories .crm-alpha-letter .list-inline>li {
    padding-left: 5px;
    padding-right: 5px;
}
.edit-filter-categories .crm-alpha-letter li span {
    width: 30px;
    height: 30px;
    font-size: 16px;
    line-height: 28px;
    background: #fff;
    position: relative;
    z-index: 2;
}
.filter-condition-row .row{
    margin-left: -5px;
    margin-right: -5px;
}
.filter-condition-col{
    margin: 5px 0px;
    padding: 0px 5px;
}
.filter-condition-col .form-control {
    height: 36px;
}
.btn-remove {
    min-width: 26px;
    display: inline-block;
    border-radius: 25px;
    height: 26px;
    margin: 5px 0px;
    padding: 0;
}
.btn-remove, .btn-remove:hover, .btn-remove:focus{
    background-color: #fff;
    color: {{$color2}};
    border: 1px solid {{$color2}};
}
.editable-select .pb-container {
    min-width: inherit;
    margin-top: 0px;
    vertical-align: top;
}
.editable-select .pb-container button.pb-button {
    padding: 0;
    min-width: auto !important;
    width: auto;
    border: none !important;
}
.co-person-list {
    width: 100%;
    padding: 5px 25px 5px 0px;
    position: relative;
    min-height: 30px;
}
.co-person-list .action-btn {
    position: absolute;
    right: 0px;
    top: 5px;
    text-align: center;
}
.co-person-list .action-btn span {
    padding: 3px;
    color: red;
    font-weight: normal;
    font-size: 10px;
    border: 1px solid red;
    border-radius: 50%;
}
.crm-lfield .dynamicSkill-blockname {
    font-size: 13px;
    padding-right: 0px;
    word-break: break-word;
    margin-top: 2px;
}
.crm-rfield .pb-container {
    display: inline-block;
    text-align: center;
    min-width: 85px;
}
.crm-rfield .pb-container button.pb-button {
    height: 34px;
}
.crm-rfield .pb-container.loading button.pb-button {
    height: 34px;
    width: 34px;
}
.crm-lfield .panel-body {
    /*max-height: 300px;*/
    overflow: auto;
}
.crm-lfields-personalinfo .skill-inputfield span.input-group-btn{
    padding-left: 0px;
}
.crm-lfields-personalinfo .customSkillField-blockdata{
    min-height: 30px;
    float: right;
}
.crm-lfields-personalinfo .customSkillField-blockdata input.form-control,
.crm-lfields-personalinfo .customSkillField-blockdata select.form-control,
.crm-lfields-personalinfo .customSkillField-blockdata .form-control {
    height: 30px !important;
}
.crm-lfields-personalinfo .skill-inputfield span.input-group-btn button{
    border-top-left-radius: 0px !important;
    border-bottom-left-radius: 0px !important;
    padding: 5px 10px;
    height: 30px;
}
.crm-lfield #accordion{
    /*max-height: 500px;*/
    overflow: auto;
}
.crm-result-header h4{
    float: left;
    font-size: 16px;
    margin-top: 3px;
    margin-bottom: 3px;
    line-height: 18px;
}
.crm-result-header .pb-container{
    margin-top: 0px;
    float: right;
}
.more .dropdown-menu ul {
    padding-left: 0;
    margin-bottom: 0px;
}
.more .dropdown-menu ul li{
    list-style: none;
    width: 100%;
    margin: 2px 0px;
    display: inline-block;
}
.more .dropdown-menu ul li a {
    color: #444;
    text-decoration: none;
    padding: 2px 10px;
    width: 100%;
    display: block;
    cursor: pointer;
    font-size: 13px;
}
.panel-body .pg-content-loader {
    margin-top: 50px;
    margin-bottom: 50px;
    height: 45px !important;
    width: 45px !important;
}
button.svgicon {
    background: transparent;
    border: none;
    margin-top: 5px;
}
.union-add-remove-outer{
    width: 100%;
    display: inline-block;
    text-align: right;
    float: left;
}
.union-add-remove {
    float: right;
    color: {{$color2}};
    margin-bottom: 15px;
}
.union-add-remove span {
    padding: 3px;
    border: 1px solid {{$color2}};
    text-align: center;
    color: {{$color2}};
    border-radius: 25px;
    font-size: 9px;
    line-height: 12px;
}
.union-add-remove span.glyphicon-plus::before {
    text-align: center !important;
    width: 11px;
    display: block;
    height: 12px;
    margin-left: 1px;
}
.entity-datatabs .nav .open>a,
.entity-datatabs .nav .open>a:hover,
.entity-datatabs .nav .open>a:focus,
.entity-datatabs .nav-tabs>li>a:hover, .entity-datatabs .nav-tabs>li.active>a,
.entity-datatabs .nav-tabs>li.active>a:hover, .entity-datatabs .nav-tabs>li.active>a:focus,
.entity-datatabs .tab-menu>li.current-menu-item>a {
    background: transparent !important;
    border-color: transparent !important;
    color: {{$color2}} !important;
    border-color: {{$color2}} !important;
    border-width: 0px 0px 1px 0px;
    border-style: solid;
}
.crm-lfields-personalinfo .skill-file-field .skill-file-inputfiles {
    width: 100%;
}
.crm-lfields-personalinfo .skill-file-inputfiles .input-group .input-group-btn button.browse {
    padding: 5px 10px;
    font-size: 13px;
    height: 30px;
    min-width: inherit;
}
.crm-lfields-personalinfo .skill-file-field .d-inline > button,
.uploadfile-group .upload-file-btn {
    float: right;
    margin-top: 5px;
    height: 30px;
    font-size: 13px;
    padding: 5px 10px;
    width: 100%;
}
.crm-lfields-personalinfo .skill-tab-select .select-cover,
.crm-lfields-personalinfo .skill-tab-select .select-cover select,
.crm-lfields-personalinfo .select-cover select{
    max-width: 100%;
    height: 30px;
}
.crm-lfields-personalinfo .skill-table-textarea .cke_contents {
    height: 120px !important;
}
.crm-lfields-personalinfo .skill-table-textarea button.btn,
.crm-lfields-personalinfo .skill-inputfield button.btn, .skilltab-percentage button.btn,
.crm-lfields-personalinfo .file-group .input-group button.btn {
    height: 30px;
    padding: 5px 10px;
    line-height: 18px;
    font-size: 13px;
}
.crm-lfields-personalinfo .edit-map-btn,
.crm-lfields-personalinfo .map-control-btns a.edit-map img,
.crm-lfields-personalinfo .edit-map-btn {
    width: 24px;
}
.crm-lfields-personalinfo .edit-map-btn {
    top: 3px;
}
.crm-lfields-personalinfo .customSkillField-blockdata .clearfix.mt-5 {
    margin-top: 0 !important;
    margin-bottom: 10px !important;
}
.uploadImgPreview .upload_img {
    max-width: 100px;
    max-height: 50px;
}
.crm-lfields-personalinfo .DayPicker.skill-date {
    position: relative;
    max-width: 230px;
}
.crm-lfields-personalinfo .skill-date-field span.svgicon {
    vertical-align: top;
    margin-right: 5px;
}
.crm-filter ul {
    padding: 0;
    margin: 0;
}
.crm-filter > ul > li {
    position: relative;
}
.crm-filter > ul > li > span {
    color: #fff;
    font-size: 15px;
    width: 100%;
}
.crm-filter > ul > li > ul {
    position: absolute;
    right: 0;
    top: 100%;
    background: #d64848;
    z-index: 1;
    width: 160px;
    display: none;
}
.crm-filter > ul li {
    list-style: none;
    width: 100%;
    display: inline-block;
    padding: 10px 15px;
}
.crm-filter > ul li a {
    color: #fff;
    text-decoration: none;
}
.crm-filter > ul > li > ul > li{
    padding: 0px 5px;
}
.crm-timeline-header .crm-filter > ul > li > ul > li:not(:first-child) a {
    border-top: 1px solid {{$color2}};
}
.crm-filter > ul > li > ul > li a{
    display: block;
    padding: 8px 11px !important;
    clear: both;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    color: #ffffff !important;
    white-space: nowrap;
    cursor: pointer;
}
.crm-filter > ul > li > ul > li:hover{
    background-color: {{$color2}};
}
.crm-filter > ul > li > span span.fa {
    font-size: 10px;
    margin-left: 2px;
}
/*.crm-filter-editbtn{
    position: absolute;
    right: 0;
    top: 0px;
}*/
.crm-filter .dropdown .dropdown-menu {
    min-width: 200px;
    max-width: 250px;
}
.crm-filter .dropdown .dropdown-menu ul {
    max-height: 216px;
    overflow: auto;
}
.crm-timeline-header .crm-filter .dropdown .dropdown-menu li a{
    padding: 5px 8px !important;
}
.fiter-menu-footer {
    background-color: #fff;
    padding: 5px 12px;
    width: 100%;
    float: left;
}
.fiter-menu-footer a {
    color: {{$color2}};
    width: 48%;
    display: inline-block;
    padding: 2px 0px;
}
.list-filter-btn{
    float: left;
}
.add-filter-btn{
    float: right;
    text-align: right;
}
.crm-timeline-header .crm-filter .dropdown .dropdown-menu li {
    padding-right: 56px;
}
.col-condition-block {
    padding-right: 50px;
}
.col-condition-block > .filter-condition-col {
    position: absolute;
    right: 0;
    top: 5px;
    width: 60px;
}
.col-condition-block > .filter-condition-col .btn-remove {
    margin: 0px 10px;
}
.select-opt-th > div {
    display: inline-block;
    max-width: 130px;
    width: 100%;
    position: relative;
    border: 1px solid #d0d0d0;
}
.select-opt-th > div::after{
    content: '\f078';
    position: absolute;
    width: 15px;
    height: 15px;
    font-family: FontAwesome;
    right: 3px;
    top: 0;
    bottom: 0;
    margin: auto;
    color: {{$color2}};
    font-size: 10px;
}
.select-opt-th > div select.form-control {
    padding: 3px 5px;
    font-style: normal !important;
    height: 28px;
}
.select-opt-th > div select.filter {
    font-size: 12px !important;
}
.dropdown #crm-user {
    cursor: pointer;
}
.select-opt-th.custom-selecto-opt select {
    border: none;
    background-color: transparent;
    color: #444 !important;
    font-size: 14px !important;
    font-style: normal;
    padding-right: 20px !important;
    z-index: 1;
    position: relative;
}
.select-opt-th.custom-selecto-opt select,
.select-opt-th.custom-selecto-opt select option{
    cursor: pointer;
    font-style: normal !important;
}
.timeline-plan {
    padding-bottom: 20px;
}
.timeline-planned-body {
    max-height: 300px;
    overflow: auto;
}
.task-icon .svg-icon-box {
    background-color: transparent;
}
.entity-datatabs .dropdown a,
.entity-datatabs .dropdown a:active{
    cursor: pointer !important;
}
.crm-lfields-personalinfo .skill-table-textarea input{
    border: 1px solid #e9e9e9;
    padding: 6px 10px !important;
}
.crm-lfields-personalinfo .percentage-sign {
    padding: 4px 5px;
    height: 100%;
}
.planned-header.select-filter {
    text-align: center;
}
.planned-header.select-filter select {
    border: 1px solid #e5e5e5;
    background-color: transparent;
    padding: 5px 20px 5px 10px;
    position: relative;
    z-index: 1;
    font-size: 14px;
    border-radius: 15px;
    cursor: pointer;
    min-width: 150px;
    max-width: 200px;
}
.select-filter select:focus {
    outline: none;
}
.select-w-arrow {
    position: relative;
    display: inline-block;
    background-color: #fff;
    z-index: 1;
}
.select-w-arrow span {
    position: absolute;
    right: 8px;
    font-size: 10px;
    top: 8px;
    padding: 0px;
}
.task-timeline-inner {
    word-break: break-all;
}
.entity-datatabs .nav-tabs>li.dropdown.open>a:hover {
    color: #fff;
}
.entity-datatabs .nav-tabs>li{
    vertical-align: bottom;
    word-break: break-all;
    float: left;
    display: block;
    width: 25%;
}
.entity-datatabs .nav-tabs li a{
    font-size: 14px;
    line-height: 14px;
    z-index: 999;
    border-width: 0px 0px 1px 0px !important;
    border-style: solid;
    border-color: #ddd;
    padding: 5px 10px;
    float: left;
    width: 100%;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}
.bulk-edit-outer{
    position: relative;
    width: 100%;
}
.bulk-edit-outer .bulk-edit-btn {
    position: absolute;
    top: 100%;
    right: 140px;
    margin-top: 10px;
    height: 34px;
    padding: 4px 15px;
    z-index: 3;
}
.btn-xs{
    height: 34px;
}
.crm-setting .dropdown .btn {
    width: 100%;
}
.crm-timeline-header .dropdown.open .dropdown-toggle {
    background: {{$color1}};
    border-radius: 0px;
}
.edit-filter-categories .crm-alpha-letter ul {
    position: relative;
    overflow: hidden;
}
.crm-alpha-letter ul li:not(:last-child):after {
    content: '';
    position: absolute;
    width: 100%;
    height: 1px;
    background: #bdb8b4;
    left: 50%;
    top: 0;
    bottom: 0;
    margin: auto;
    z-index: 1;
}
.edit-filter-categories .crm-alpha-letter ul::after {
    content: '';
    width: 100%;
    height: 1px;
    background: #bdb8b4;
    position: absolute;
    left: 30px;
    top: 0;
    bottom: 0;
    margin: auto;
}
.entity-datatabs .nav-tabs .dropdown-menu>li>a{
    padding: 5px 5px;
    font-size: 13px;
}

/* CRM SPIRINT 7 */
.attech-task .proj-card .left {
    display: none;
}
.attech-task .cardbox-inner{
    border: none;
}
.attech-task .cardbox-inner .form-body,
.add-new-file {
    border-width: 1px;
    border-radius: 0px 0px 3px 3px;
    border-color: #e8e8e8;
    border-style: solid;
}
.task-horizontal-heading{
    border-radius: 3px 3px 0px 0px;
    border-width: 1px 1px 0px 1px;
    border-style: solid;
    border-color: #e8e8e8;
    padding: 8px 15px;
    background-color: #f7f6f7;
}
.task-horizontal-heading h5 {
    margin: 0px;
    font-size: 14px;
    font-weight: bold;
}
.attech-task .right.card-info-tabs{
    width: 100%;
    padding: 0;
}
.heading-r-icon{
    position: relative;
    padding-right: 35px;
}
.heading-r-icon .right-icon{
    position: absolute;
    right: 8px;
    top: 3px;
}
.heading-r-icon .right-icon svg {
    width: 24px;
    height: inherit;
    float: right;
}
.heading-r-icon .right-icon svg path {
    fill: #333;
}
.attech-task .default-form-style {
    margin: 0px;
    padding: 0px;
}
.attech-task .default-form-style .form-body {
    display: inline-block;
    width: 100%;
    margin-bottom: 10px;
    padding: 10px 10px;
}
.add-new-file{
    display: inline-block;
    width: 100%;
    margin: 0px 0px 10px 0px;
    padding: 10px 10px;
}
.task-icon .svg-icon-box svg {
    background: #fff;
}
.radio-checkbox{
    position: relative;
    padding-left: 20px;
}
.radio-checkbox input[type="checkbox"] {
    opacity: 0;
    filter: alpha(opacity=0);
    visibility: hidden;
    margin-left: -13px;
}
.radio-checkbox label::before {
    content: "";
    display: inline-block;
    position: absolute;
    width: 15px;
    height: 15px;
    left: 0;
    top: 5px;
    border: 1px solid #cccccc;
    border-radius: 50%;
    background-color: #fff;
    -webkit-transition: border 0.15s ease-in-out;
    -o-transition: border 0.15s ease-in-out;
    transition: border 0.15s ease-in-out;
}
.radio-checkbox label::after,
.required-field .radio-checkbox label::after {
    background-color: {{$color2}};
    position: absolute;
    content: " ";
    width: 9px;
    height: 9px;
    left: 3px;
    top: 8px;
    display: inline-block;
    border-radius: 50%;
    -webkit-transform: scale(0);
    -moz-transform: scale(0);
    -ms-transform: scale(0);
    -o-transform: scale(0);
    transform: scale(0);
}
.radio-checkbox input:checked + label::after,
.required-field .radio-checkbox input:checked + label::after{
    -webkit-transform: scale(1);
    -moz-transform: scale(1);
    -ms-transform: scale(1);
    -o-transform: scale(1);
    transform: scale(1);
}
.radio-checkbox input, .radio-checkbox label{
    cursor: pointer;
}
.crm-timeline-header .crm-filter .dropdown .dropdown-menu li span.li-name a{
    word-break: break-all;
    white-space: normal;
}
.crm-timeline-header .crm-filter .dropdown .dropdown-menu li span.action {
    position: absolute;
    right: 0;
    display: flex;
    top: 0;
}
.crm-timeline-header .crm-filter .dropdown .dropdown-menu li span.action a {
    font-size: 12px;
    padding: 5px !important;
    margin: 0px 3px;
}
.task-plan-block-inner .task-icon .file-icon {
    min-width: 40px;
    margin-right: 24px;
    z-index: 1;
    position: relative;
}
.task-timeline-inner .file-name-link {
    margin-top: 5px;
}
.edit-panel-title {
    padding-left: 30px;
    position: relative;
}
.edit-panel-title .svgicon {
    position: absolute;
    left: 8px;
    top: 5px;
    cursor: pointer;
}
.edit-panel-title .svgicon svg {
    width: 18px;
    height: 18px;
}
.file-name-value-field .form-control {
    height: 32px;
}
.dateShow span {
    padding: 8px;
    display: inline-block;
}
a.dropdown-toggle {
    cursor: pointer !important;
}
.nav-tabs .dropdown-menu{
    max-height: 200px;
    overflow: auto;
}
.entity-datatabs .nav-tabs li a.dropdown-toggle span.fa {
    font-size: 10px;
    margin-left: 3px;
}
.note-control-btns{
    border: 1px solid #ececec;
    background: #f7f6f7;
    padding: 5px 5px 5px 5px;
    margin-bottom: 5px;
    width: 100%;
    display: inline-block;
    margin-top: 10px;
}
.note-control-bx {
    width: 25%;
    float: left;
    text-align: center;
}
.note-control-bx:not(:last-child){
    border-right: 1px solid {{$transprancy7}};
}
.note-control-bx span{
    color: {{$color2}};
}
.editable-select input.form-control {
    height: 36px;
}
.social-wrap .input-group-btn .txt-btn {
    font-size: 16px;
    min-width: 36px;
    padding: 5px;
    height: 30px;
    line-height: 16px;
}
.crm-lfield .editable-select input.form-control {
    height: 30px;
}
.crm-lfield .Select-input input {
    height: 30px;
    padding: 0;
    width: 100%;
}
.crm-lfield .editable-select .input-group .Select .Select-control,
.crm-lfield .select-cover select{
    height: 30px !important;
}
.crm-lfield .editable-select .selected-valuebox,
.crm-lfield .Select-placeholder,
.crm-lfield .Select-input {
    height: 28px !important;
    line-height: 28px;
}
.crm-lfield .editable-select .selected-valuebox{
    line-height: 18px;
    border: 1px solid #dfdfdf;
    border-radius: 4px 0px 0px 4px;
    padding: 5px 10px 5px 10px;
}
.crm-lfield .editable-select .control-btns span {
    width: 36px;
    top: 0;
    height: 30px;
    line-height: 28px;
}
.crm-lfield .editable-select .pb-container .pb-button{
    height: 30px;
}
.editable-select .pb-container.loading{
    border-color: #ccc;
    border-width: 1px 1px 1px 0px;
    border-style: solid;
}
.crm-lfield .editable-select .pb-container.loading{
    height: 30px;
}
.editable-select .pb-container button.pb-button{
    height: 36px;
}
.crm-lfield .editable-select .pb-container button.pb-button {
    height: 30px;
}
.crm-lfield .editable-select .Select-control .Select-value {
    height: 28px;
    line-height: 28px !important;
}
.form-horizontal .child-form-group .control-label{
    text-align: left;
    padding-right: 0px;
}
.value-edit-block {
    width: 100%;
}
.value-edit-block .value-box{
    width: 100%;
    border: 1px solid #dfdfdf;
    border-radius: 4px 0px 0px 4px;
    font-size: 14px;
    padding: 5px;
    line-height: 18px;
}
.value-edit-block .input-group-btn {
    vertical-align: top;
    height: 100%;
    border-width: 1px 1px 1px 0px;
    border-style: solid;
    border-color: #dfdfdf;
    border-radius: 0px 4px 4px 0px;
    padding: 5px;
    cursor: pointer;
}
.input-group span.input-group-btn .svgicon svg {
    width: 18px;
    height: 18px;
}
.note-control-bx button.active,
.note-timer.active {
    color: red !important;
}
.task-timline-note-desc p {
    margin-bottom: 5px;
}
.editable-select .data-edit .control-btns span{
    border: none;
}
.crm-lfield .editable-select .control-btns span svg {
    width: 18px;
    height: 18px;
}
.entity-datatabs .nav-tabs li a span.text-ellipsis-wrap {
    position: relative;
    max-width: 100%;
    padding-right: 20px;
}
.crm-lfield .editable-select .data-edit .selected-valuebox{
    border-color: transparent;
}
.child-form-group .label-bx{
    color: #888888;
    font-size: 14px;
}
.data-edit .label-bx, .data-edit .value-bx {
    padding-top: 5px;
}
.editable-select .data-edit .selected-valuebox{
    padding-left: 0px;
}
.skill-file-field .file-icon {
    background-size: 25px;
    padding: 5px 0px 5px 25px;
    min-height: 30px;
}
.entity-tabs .switch-li label.switch {
    margin-left: 10px;
}
.control-btns .svgicon{
    cursor: pointer;
}
.child-form-group label.control-label {
    padding-right: 0;
}
.person-icon .task-icon{
    display: inline-block;
}
.person-icon .svg-icon-box {
    padding: 2px;
    margin-right: 10px;
}
.person-icon svg {
    width: 16px;
    height: 16px;
}
.skill-action-btns .edit-rfield {
    text-align: right;
}
.customSkillField-blockdata .skill-action-btns{
    height: 18px;
}