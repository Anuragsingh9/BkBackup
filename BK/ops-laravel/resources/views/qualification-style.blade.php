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
@import url('https://fonts.googleapis.com/css?family=Lato:300,400,700');
html, body, #root{
height: 100%;
}
.status-inactive, .status-active{
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}
.status-active{background-color: green;}
.status-inactive{background-color: gray;}
.blocklist-with-icons {width: 100%; display: block;}
.blocklist-row {display: inline-block; width: 100%; margin: 3px 0px 3px 20px;}
.blocklist-title-link {position: relative; padding-right: 70px; display: inline-block;}
.blocklist-controller {
    width: 70px;
    display: inline-block;
    position: absolute;
    right: 0;
}
.regi-check-slide {margin: 5px 0px; display: inline-block; width: 100%;}
.registration-step{font-size: 14px;}
.registration-step span.step-count {
    width: 26px;
    height: 26px;
    background: {{$color2}};
    display: inline-block;
    text-align: center;
    line-height: 26px;
    color: #fff;
    font-size: 14px;
    border-radius: 25px;
    margin-right: 5px;
    position: absolute;
    left: 10px;
    top: 0px;
}
.regis-step-list {
    border-bottom: 1px solid #e7e7e7;
    display: inline-block;
    margin-top: 20px;
    width: 100%;
}
.regis-step-list .rc-draggable-list{width: 100%; float: left;}
.regis-step-list .rc-draggable-list-draggableRow {
    max-width: 25%;
    float: left;
    margin-bottom: 10px;
}
.regis-step-list .registration-step {
    float: left;
    width: 100%;
    padding: 3px 20px 3px 40px;
    position: relative;
}
.registration-form-steps .regis-step-list .registration-step {
    max-width: 25%;
    width: auto;
    margin-bottom: 10px;
}
.regis-step-list .registration-step .step-name{
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}
.regis-step-list .registration-step.step-w-btns {padding-right: 50px;}
.regis-step-list .registration-step span.step-count {background: #b7b7b7;}
.regis-step-list .registration-step.active{color: {{$color2}};}
.regis-step-list .registration-step.active span.step-count {background: {{$color2}};}
.regis-step-list .registration-step .step-controller {
    width: 47px;
    position: absolute;
    right: 0;
    top: 3px;
    display: none;
}
.regis-step-list .registration-step:hover .step-controller{display: block;}
.step-controller .edit,
.step-controller .delete {
    background-color: transparent;
    border: none;
    color: {{$color2}};
    float: left;
    font-size: 12px;
    outline: none;
    padding: 2px;
    width: 18px;
    height: 18px;
    text-align: center;
    margin-left: 3px;
}
.step-controller .edit span{vertical-align: super;}
.step-description { margin-bottom: 20px; margin-top: 15px;}
.step-detail, .step-description label { font-size: 16px;}
.step-fields-w-label { margin-top: 5px; margin-bottom: 5px;}
.step-fields-w-label .step-label {
    padding-right: 0;
    font-size: 14px;
    color: #171717;
    padding-top: 10px;
    font-weight: 700;
}
.step-fields-w-label .step-content-field span.svgicon,
.step-fields-w-label .step-content-field .transparent-btn {
    position: absolute;
    right: 15px;
    top: 10px;
    cursor: pointer;
}
.step-fields-w-label .step-content-field .input-value { margin-top: 10px; min-height: 32px;}
.step-fields-w-label .step-content-field {padding-right: 100px; padding-left: 30px;}
.btn-lg { height: 42px; line-height: 26px;}
.step-description .input-group.select-w-btn {max-width: 320px;}
.qualification-header {background: {{$color2}};}
.qualification-header button.dropdown-toggle {color: #fff; width: 100%;}
.left-dropdowm-menu{border-right: 1px solid rgba(255, 255, 255, 0.3);}
.left-dropdowm-menu .dropdown.drop-style1 {padding: 5px 10px;}
.left-dropdowm-menu .dropdown {width: 150px;}
.panel-review-style .panel-heading {background-color: #f5f5f5 !important; padding: 0px 0px;}
.panel-review-style .panel-heading .panel-title a.accordion-toggle {
    color: {{$color2}};
    font-size: 16px;
    width: 100%;
    display: inline-block;
    padding: 7px 10px;
    text-decoration: none;
}
.panel-group .panel-review-style{
    border-width: 0px;
    border-bottom-width: 1px;
    margin-bottom: 0;
    border-radius: 0px !important;
    box-shadow: none;
}
.panel-group .panel-review-style + .panel-review-style .panel-body {min-height: 100px;}
.panel-review-style {border-color: {{$color2}} !important;}
.panel-review-style .panel-heading .accordion-toggle:after{color: {{$transprancy7}};}
.panel-heading .accordion-toggle:after {
    font-family: 'Glyphicons Halflings';
    content: "\e114";
    float: right;
    color: grey;
}
.panel-heading .accordion-toggle.collapsed:after {content: "\e080";}
.color-opt-radio {
    position: relative;
    height: 30px;
    width: 30px;
    margin: 2px;
    float: left;
}
.color-opt-radio label {
    width: 30px;
    height: 30px;
    background-color: #fff;
    border: 2px solid #e8e8e8;
    text-align: center;
    line-height: 18px;
    cursor: pointer;
    overflow: hidden;
    margin: 0px;
}
.color-opt-radio input[type="radio"]{
    visibility: hidden;
    width: 0;
    height: 0;
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto !important;
}
.color-opt-radio input[type="radio"] + label span.rdo-color{
    opacity: 0;
    filter: alpha(opacity=0);
    width: 12px;
    height: 12px;
    display: block;
    border-radius: 50%;
    -webkit-transition: all ease-in-out 300ms;
    -moz-transition: all ease-in-out 300ms;
    -ms-transition: all ease-in-out 300ms;
    -o-transition: all ease-in-out 300ms;
    transition: all ease-in-out 300ms;
    margin: 7px auto;
    background-color: gray;
}
.color-opt-radio.rdo-red input[type="radio"]:checked + label{border-color: #e0001b;}
.color-opt-radio.rdo-red input[type="radio"]:checked + label span.rdo-color,
.color-opt-radio.rdo-orange input[type="radio"]:checked + label span.rdo-color,
.color-opt-radio.rdo-green input[type="radio"]:checked + label span.rdo-color{ opacity: 1; filter: alpha(opacity=100); }
.color-opt-radio.rdo-red input[type="radio"]:checked + label span.rdo-color{background-color: #e0001b;}
.color-opt-radio.rdo-orange input[type="radio"]:checked + label{border-color: #f07f0a;}
.color-opt-radio.rdo-orange input[type="radio"]:checked + label span.rdo-color{background-color: #f07f0a;}
.color-opt-radio.rdo-green input[type="radio"]:checked + label{border-color: #80bd26;}
.color-opt-radio.rdo-green input[type="radio"]:checked + label span.rdo-color{background-color: #80bd26;}
.color-ops-rdo .radio-icons-group {display: inline-block;}
.panelStyle .panel-heading .detail-header-accordion .accordion-toggle:after {color: rgb(255, 255, 255);}
.detail-header-accordion a.accordion-toggle {
    width: 20px;
    height: 20px;
    background: {{$color2}};
    border-radius: 20px;
    display: inline-block;
    text-align: center;
    font-size: 12px;
    line-height: 20px;
}
.detail-header-accordion h4.panel-title {
    color: {{$color2}};
    font-size: 16px;
    width: 100%;
    display: inline-block;
    padding: 7px 10px;
    text-decoration: none;
}
.detail-header-accordion .title-box {
    float: left;
    width: 43%;
    min-height: 64px;
    line-height: 64px;
}
.detail-header-accordion .color-ops-rdo{
    float: left;
    width: 100%;
    padding: 15px 10px;
    font-size: 0px;
    min-width: 64px;
    text-align: center;
    font-size: 0px;
}
.detail-header-accordion:hover .color-ops-rdo{visibility: visible;}
.accroding-btn-box{
    float: left;
    width: 3%;
    padding: 5px 5px 5px 0px;
    min-height: 64px;
    box-sizing: border-box;
    line-height: 54px;
}
.accroding-btn-box:after { display: none; }
.accroding-btn-box a {
    width: 20px;
    height: 20px;
    background: {{$color2}};
    border-radius: 20px;
    display: inline-block;
    line-height: 20px;
    text-align: center;
    cursor: pointer;
}
.accroding-btn-box a i,
.panel-style-default .panel-heading .accordion-toggle:after {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
    -webkit-transition: all ease-in-out 300ms;
    -moz-transition: all ease-in-out 300ms;
    -ms-transition: all ease-in-out 300ms;
    -o-transition: all ease-in-out 300ms;
    transition: all ease-in-out 300ms;
    color: #fff;
    text-align: center;
}
.accroding-btn-box a.collapsed i,
.accroding-btn-box.collapsed a i,
.panel-style-default .panel-heading .accordion-toggle.collapsed:after {
    -webkit-transform: rotate(-90deg);
    -moz-transform: rotate(-90deg);
    -ms-transform: rotate(-90deg);
    -o-transform: rotate(-90deg);
    transform: rotate(-90deg);
}
.digit-input {
    padding: 5px 5px 5px 13px;
    max-width: 70px;
    text-align: center;
}
.form-control-sm{height: 32px;}
.lable-box {padding: 3px 10px;}
.input-field-box {padding: 5px 10px;}
.switch-box {
    padding: 10px;
    max-width: 80px;
    width: 100%;
    vertical-align: middle;
}
.quali-row-opt-item > div {vertical-align: middle;}
.tab-accordion-block .accordion-tab-left-header {width: 45%; float: left;}
.tab-accordion-block .accordion-tab-menu{
    width: 55%;
    float: left;
    padding-right: 5%;
}
.tab-accordion-block .accordion-tab-menu nav{ width: 100%; float: left;}
.tab-accordion-block .accordion-tab-menu nav .nav-tabs{
    width: 100%;
    flex-flow: row;
    display: flex;
    border-bottom: none;
}
.accordion-tab-menu .nav-tabs>li {width: 14.28%; flex: none;}
.accordion-tab-menu .nav-tabs>li>a {
    padding: 5px;
    width: 100%;
    text-align: center;
    word-break: break-word;
    height: 100%;
    vertical-align: middle;
    display: flex;
    font-size: 12px;
}
.accordion-tab-menu .nav-tabs>li>a span {display: inline-block; margin: auto;}
.accordion-tab-left-header h4 {color: {{$color2}}; margin: 5px 0px 5px 0px; }
.accordion-tab-left-header h4 span {font-size: 14px; margin-left: 10px;}
.check-group-block, .vote-review-icons-block {width: 50%; float: left;}
.check-group-block .switch-box,
.vote-review-icons-block .votereview-icon {
    padding: 3px;
    width: 14.28%;
    box-sizing: border-box;
    float: left;
    text-align: center;
    max-width: inherit;
}
.check-group-block .switch-box .switch {margin-right: 0;}
.check-group-block .switch-box:not(:last-child),
.vote-review-icons-block .votereview-icon:not(:last-child) {
    border-right: 1px solid #e5e5e5;
}
.votereview-icon .horizontal-dots {
    font-size: 24px;
    color: {{$color2}};
    height: 20px;
    line-height: 20px;
    vertical-align: middle;
}
.expert-vote-review-que .expertname {
    width: 45%;
    float: left;
    color: {{$color2}};
    font-size: 14px;
    margin-top: 4px;
}
.expert-vote-review-que {
    width: 100%;
    display: inline-block;
    margin: 5px 0;
}
.votereview-icon .horizontal-dots.red{color: #e64156;}
.qualification-header-right {padding-left: 15px;}
.qualification-header .navbar-nav>li>a {padding: 6px 15px; color: #fff;}
.qualification-header .navbar-nav>li>a:hover {background: {{$color2}};}
.btn-link:focus {border-color: transparent !important;}
.data-list ul {list-style: none; padding-left: 0px;}
.data-list ul li {padding: 5px 0px; display: inline-block; width: 100%;}
.regis-step-w-btn{padding-right: 40px; position: relative;}
.regis-step-w-btn .rounded-add-btn{
    position: absolute;
    right: 0px;
    top: -5px;
}
.step-content-field .customSkillField-blockdata > div .input-group {width: 100%;}
.step-form-btns button:not(:last-child){margin-right: 10px;}
.step-form-btns .d-inline > div {float: left;}
.step-form-btns .d-inline > button {border-radius: 4px; margin-left: 10px;}
.step-detail {
    font-size: 16px;
    padding: 10px;
    background: #f7f7f7;
    border: 1px solid #ececec;
    min-height: 100px;
}
.step-fields-w-label .step-content-field .transparent-btn svg {width: 20px; height: 20px;}
.step-content-field .drag-icon-candidate {top: 2px; height: 20px; left: 10px;}
.regis-step-list .registration-step .drag-icon-candidate{width: 5px;}
.panel.panel-review-style {border-radius: 0px;}
.panel-review-style > .panel-heading {
    background-color: #fff !important;
    border-width: 0 0 1px 0 !important;
}
.panel-review-style>.panel-heading+.panel-collapse>.panel-body{
    border-top: 0px !important;
    width: 100%;
    min-height: 220px;
}
.panel-review-style .panel-body {padding: 0px;}
.val-exp-dec-section{
    width: 100%;
    float: left;
    text-align: center;
}
.val-exp-dec-section .validation-block{width: 46%;}
.val-exp-dec-section .experts-block{width: 27%;}
.val-exp-dec-section .decision-block{width: 27%;}
.val-exp-dec-section .validation-block,
.val-exp-dec-section .experts-block,
.val-exp-dec-section .decision-block {
    float: left;
    font-size: 20px;
    text-transform: uppercase;
    text-align: center;
    color: {{$color2}};
    position: relative;
}
.val-exp-dec-section .experts-block::before,
.val-exp-dec-section .decision-block::before,
.val-exp-dec-section .experts-block::after,
.val-exp-dec-section .decision-block::after{
    content: '';
    position: absolute;
    border-top: 6px solid transparent;
    border-bottom: 6px solid transparent;
    top: 0;
    bottom: 0;
    height: 12px;
    width: 12px;
    margin: auto;
}
.val-exp-dec-section .experts-block::before,
.val-exp-dec-section .decision-block::before{
    left: 0;
    border-left: 12px solid {{$color2}};
}
.val-exp-dec-section .experts-block::after,
.val-exp-dec-section .decision-block::after{
    left: -4px;
    border-left: 12px solid #fff;
}
.expert-box-count {
    width: 27%;
    float: left;
    text-align: center;
    padding: 15px 10px;
    font-size: 0px;
    min-width: 64px;
}
.expert-box-count .expert-count {
    width: 30px;
    height: 30px;
    border: 2px solid;
    float: left;
    text-align: center;
    margin: 2px;
    font-size: 14px;
    line-height: 26px;
}
.experts-rev-group{display: inline-block;}
.expert-box-count .expert-count.red{
    color: #e0001b;
    border-color: #e0001b;
}
.expert-box-count .expert-count.orange{
    color: #f07f0a;
    border-color: #f07f0a;
}
.expert-box-count .expert-count.green{
    color: #80bd26;
    border-color: #80bd26;
}
.expert-box-count, .qual-decision-data-outer,
.expert-rev-data{
    border-left: 2px dashed {{$transprancy7}};
}
.qual-validation-section {
    width: 46%;
    display: table-cell;
    padding: 15px 15px 15px 0%;
}
.expert-rev-data, .qual-decision-data{
    display: inline-block;
    width: 100%;
    padding: 15px;
}
.expert-rev-data, .qual-decision-data-outer {
    width: 27%;
    position: relative;
    float: left;
    display: inline-block !important;
    clear: none;
}
.qual-decision-data-view{
    display: table-cell !important;
    border-left: 2px dashed {{$transprancy7}};
    padding: 15px;
    min-height: 200px;
    width: 27%;
}
.accordion-toggle ~ .qual-decision-data-outer .qual-decision-data {
    display: block;
    position: absolute;
    top: 100%;
    left: -2px;
    z-index: 1;
}
.accordion-toggle.collapsed ~ .qual-decision-data-outer .qual-decision-data {display: none;}
.qual-exp-data {
    width: 100%;
    max-width: 206px;
    min-width: 200px;
    border: 2px solid {{$color2}};
    margin: auto;
    font-size: 12px;
    color: {{$color2}};
    position: relative;
}
.qual-exp-inner{
    width: 100%;
    position: relative;
    min-height: 50px;
    background-color: #fff;
    padding: 5px 10px;
    z-index: 3;
}
.qual-exp-data.center-pop::before,
.qual-exp-data.left-pop::before,
.qual-exp-data.right-pop::before{
    content: '';
    position: absolute;
    border-right: 26px solid {{$color2}};
    border-top: 26px solid transparent;
    border-bottom: 26px solid transparent;
    top: -26px;
    z-index: 1;
}
.qual-exp-data.center-pop::after,
.qual-exp-data.left-pop::after,
.qual-exp-data.right-pop::after{
    content: '';
    position: absolute;
    border-right: 22px solid #fff;
    border-top: 22px solid transparent;
    border-bottom: 22px solid transparent;
    top: -26px;
    z-index: 2;
}
.qual-exp-data.left-pop::before, .qual-exp-data.left-pop::after {left: 21%;}
.qual-exp-data.center-pop::before, .qual-exp-data.center-pop::after {left: 37%;}
.qual-exp-data.right-pop::before, .qual-exp-data.right-pop::after {left: 54%;}
.qual-exp-data.left-pop::after, .qual-exp-data.center-pop::after, .qual-exp-data.right-pop::after {margin-left: 2px; top: -21px;}
.qual-decision-data form {max-width: 260px; margin: 0 auto;}
.qual-decision-data form textarea {
    box-shadow: none;
    border-radius: 0px;
    height: 70px;
}
.memb-reply{
    width: 100%;
    padding: 5px 0px;
    display: inline-block;
    word-break: break-all;
}
.memb-reply:not(:last-child){border-bottom: 2px dashed {{$transprancy7}};}
.qual-decision-data form button,
.qual-memb-submit-btn {
    background: {{$color2}};
    color: #fff;
    border-radius: 0px;
    padding: 5px 10px;
    min-width: 80px;
    float: right;
    font-size: 12px;
    height: 32px;
    line-height: 22px;
}
.qual-memb-submit-btn{
    height: 36px;
    font-size: 14px;
}
.qual-decision-data form button, .qual-decision-data form button:hover, .qual-decision-data form button:focus, .qual-memb-submit-btn:hover, .qual-memb-submit-btn:focus {
    color: #fff;
}
.icon-checkbox {
    display: inline-block;
    position: relative;
    width: 20px;
    height: 20px;
}
.icon-checkbox input[type="checkbox"] {
    z-index: 1;
    position: relative;
    opacity: 0;
    width: 20px;
    height: 20px;
    margin: 0;
    cursor: pointer;
}
.icon-checkbox input[type="checkbox"] + .icon-check-span {
    cursor: pointer;
    width: 20px;
    height: 20px;
    display: inline-block;
    position: absolute;
    left: 0;
    top: 0;
    border: 1px solid #abacad;
    text-align: center;
    color: #abacad;
}
.icon-checkbox input[type="checkbox"] + .icon-check-span i{
    display: none;
    font-size: 14px;
    line-height: 18px;
}
.icon-checkbox input[type="checkbox"]:checked + .icon-check-span i{display: block}
.icon-checkbox.right input[type="checkbox"]:checked + .icon-check-span{border-color: #80bd26; color: #80bd26;}
.icon-checkbox.wrong input[type="checkbox"]:checked + .icon-check-span{
    border-color: #e0001b;
    color: #e0001b;
}
.qual-candidate-header {
    border-bottom: 1px solid {{$color2}};
    margin-bottom: 25px;
    padding: 10px 0px;
}
.current-qual-memb {
    color: {{$color2}};
    font-size: 18px;
    font-weight: bold;
    padding: 0px 25px;
    position: relative;
    display: inline-block;
}
.current-qual-memb span{
    font-size: 14px;
    font-weight: normal;
}
.qual-comp-list{
    color: {{$color2}};
    font-size: 14px;
}
.qual-comp-list span.icon {
    float: left;
    margin-right: 5px;
}
.qual-comp-list span.icon svg {
    width: 18px;
    height: 18px;
}
.qual-comp-list span.icon svg path{fill: {{$color2}};}
.border-radius-0{border-radius: 0px !important;}
.quali-row-opt-item span.svgicon path {fill: gray;}
.current-qual-memb button.prev-btn, .current-qual-memb button.next-btn {
    background: {{$color2}};
    border: none;
    box-shadow: none !important;
    color: #fff;
    width: 20px;
    height: 20px;
    padding: 0px !important;
    vertical-align: middle;
    font-size: 20px;
    line-height: 20px;
    position: absolute;
    top: 0;
    bottom: 0;
    margin: auto;
}
.current-qual-memb button.prev-btn{left: 0;}
.current-qual-memb button.next-btn{right: 0;}
.current-qual-memb .name{
    margin-left: 5px;
    margin-right: 5px;
}
.ques-icon-check,
.wright-icon-check,
.remove-icon-check {
    border-width: 1px;
    border-style: solid;
    cursor: pointer;
    height: 16px;
    width: 16px;
    text-align: center;
    line-height: 14px;
}
.ques-icon-check{
    border-color: #abacad;
    color: #abacad;
}
.wright-icon-check {
    border-color: #80bd26;
    color: #80bd26;
    margin-top: 6px;
    font-size: 10px;
}
.remove-icon-check {
    border-color: #e0001b;
    color: #e0001b;
    margin-top: 6px;
    font-size: 10px;
}
.experts-rev-group .__react_component_tooltip {
    background: #fff;
    padding: 0;
}
.experts-rev-group .__react_component_tooltip.place-bottom:after,
.experts-rev-group .__react_component_tooltip.place-top:after{display: none;}
.experts-rev-group .__react_component_tooltip.place-top .qual-exp-data.center-pop::before{top: inherit; bottom: -26px}
.experts-rev-group .__react_component_tooltip.place-top .qual-exp-data.center-pop::after {top: inherit; bottom: -21px;}
.expert-rev-data-pop{text-align: left;}
.panel-style-default {
    display: inline-block;
    width: 100%;
    border: none;
    box-shadow: none;
    border-radius: 0px;
}
.panel-style-default>.panel-heading {
    float: left;
    width: 100%;
    position: relative;
    background: transparent;
    padding: 0px;
}
.panel-style-default>.panel-heading a.accordion-toggle {
    color: {{$color2}};
    padding: 15px 30px 15px 0px;
    display: inline-block;
    width: 100%;
    border-bottom: 1px solid {{$color2}};
}
.panel-style-default .panel-heading .accordion-toggle:after {
    content: "\f078" !important;
    font-family: 'FontAwesome';
    background: {{$color2}};
    width: 20px;
    height: 20px;
    border-radius: 50px;
    line-height: 20px;
    font-size: 12px;
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto;
}
.panel-style-default .panel-body {
    float: left;
    width: 100%;
    border: none !important;
    border-bottom: 1px solid {{$color2}} !important;
}
.data-comingsoon {
    min-height: 300px;
    text-align: center;
}
.SubmitMessage {margin: auto;}
.SubmitMessage img {max-width: 150px;}
.pdf-view-popup .modal-dialog .modal-body {padding: 0px;}
.pdf-view-popup .modal-dialog .modal-body,
.pdf-view-popup .modal-dialog,
.qual-validation-section .quali-row-opt-item{
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
}
.modal-body.iframe-modal-body {overflow: hidden !important;}
.pdf-view-popup .modal-dialog iframe {
    width: 800px;
    height: 500px;
}
.pdf-view-popup .modal-content .modal-header {
    padding: 0;
    background: transparent !important;
    min-height: 0px;
    border: none;
}
.pdf-view-popup .modal-header .close {
    background: {{$color1}};
    position: absolute;
    right: -24px;
    z-index: 9;
    margin-top: 0;
    opacity: 1;
    font-size: 18px;
    display: inline-block;
    width: 24px;
    height: 24px;
}
.pdf-view-popup {
    position: fixed;
    width: 100%;
    left: 0;
    right: 0;
    margin: auto;
    height: 100%;
}
.pdf-view-popup .modal-dialog {
    position: absolute;
    top: 50% !important;
    transform: translate(0, -50%) !important;
    -ms-transform: translate(0, -50%) !important;
    -webkit-transform: translate(0, -50%) !important;
    margin: auto 5%;
    width: 90%;
    max-height: 80%;
    left: 0;
    right: 0;
    margin: auto;
    max-width: 850px;
}
.pdf-view-popup .modal-content{
    min-height: 100%;
    border-radius: 0px;
    max-width: 100%;
    margin: 0 auto;
}
.pdf-view-popup .modal-body {
    height: 100%;
    left: 0;
    right: 0;
    overflow-y: hidden;
    padding: 0px;
}
.pdf-img-pop-body{
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
}
.pdf-view-popup .image-popup{
    height: 100%;
    overflow: auto;
}
.pdf-view-popup .modal-body .popimg img {
    display: block;
    max-width: 100%;
    margin: auto;
    align-self: center;
    align-items: center;
    width: auto !important;
}
.qual-validation-inner .lable-box .icon::before {color: gray !important;}
.qual-validation-inner .lable-box .icon.active::before {color: {{$color2}} !important;}
.step-data .crm-lfields-personalinfo .skill-file-field .skill-file-inputfiles,
.registration-form-steps .crm-lfields-personalinfo .skill-file-field .skill-file-inputfiles {
    width: 400px;
    float: left;
}
.step-data button.btn.btn-primary,
.registration-form-steps .crm-lfields-personalinfo .skill-file-field .d-inline > button,
.registration-form-steps .uploadfile-group .upload-file-btn {
    float: left;
    width: auto;
    margin-top: 0px;
}
.step-data .crm-lfields-personalinfo .customSkillField-blockdata .text-left a {color: {{$color2}};}
.step-fields-w-label .rc-draggable-list-draggableRow {display: inline-block; width: 100%;}
.uploadfile {
    position: absolute;
    right: 0;
    top: 0;
}
.uploadfile svg {
    width: 28px;
    height: 28px;
    float: left;
    margin-top: 3px;
}
.form-control-sm + span.input-group-btn button {height: 32px; padding: 5px 10px;}
.nouploaded svg path {fill: gray !important;}
#banner {
    background: url('../../public/qualification/banner-pattern.png') no-repeat center bottom;
    background-size: 100% auto;
    padding: 60px 0px;
    min-height: 300px;
}
.welcome-user {
    width: 30%;
    float: left;
    margin-right: 30px;
}
.parent-card-block {
    position: relative;
    display: block;
    max-width: 340px;
    margin: 0 auto;
    float: left;
    width: 100%;
}
.card-block {
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    right: 0;
    top: 0;
    margin: auto;
}
.card-upper-block {height: 45%; padding: 15px 0px 5px;}
.card-lower-block {height: 55%;}
.card-company-block {
    width: 70%;
    max-width: 100%;
    float: left;
    min-height: 40px;
    padding-right: 17px;
}
.card-upper-logo {
    width: 27%;
    float: left;
    position: relative;
    min-height: 30px;
    padding: 0 3px;
}
.card-company-block span {
    display: block;
    position: relative;
    color: #22498e;
    text-align: left;
    padding-left: 25px;
    font-family: 'Open Sans', sans-serif;
    font-weight: 600;
    font-size: 12px;
    line-height: 12px;
    word-break: break-word;
}
.zip-code{margin-top: 5px;}
.card-upper-logo img {max-width: 100%; max-height: 64px;}
.card-lower-block {height: 55%;}
.card-bottom-logo {
    float: left;
    width: 30%;
    padding: 6px 0px 5px 0px;
    text-align: center;
}
.card-bottom-logo img {max-height: 70px;}
.domians-name {
    float: left;
    width: 70%;
    display: table;
    height: 100%;
}
.domians-name ul {
    display: table-cell;
    vertical-align: middle;
    margin: 0;
    color: #fff;
    padding: 0;
    list-style: none;
}
.domians-name ul li {
    font-size: 9px;
    text-align: right;
    padding: 2px 0px;
    padding-right: 26px;
    position: relative;
    word-break: break-all;
}
.domians-name ul li:after {
    content: '';
    position: absolute;
    width: 15px;
    height: 5px;
    background: gold;
    top: 6px;
    right: 0;
}
.data-view-block {padding: 50px 0px; width: 100%;}
h4.heading-txt {margin-bottom: 5px; padding-bottom: 10px;}
div.heading-text{
    padding-top: 18px;
    padding-bottom: 14px;
    font-size: 14px;
    color: #444;
    font-weight: 600;
    position: relative;
    text-transform: uppercase;
}
.heading-inner-text{padding-bottom: 10px;}
.circle-icon-check {
    width: 16px;
    height: 16px;
    text-align: center;
    font-size: 10px;
    line-height: 16px;
    cursor: pointer;
}
#validation-data-accordion span.badge.badge-danger {
    background: red;
    height: 20px;
    display: inline-block;
    vertical-align: middle !important;
    padding: 3px 3px;
    min-width: 20px;
    text-align: center;
    line-height: 14px;
    margin-left: 5px;
}
.radio-group label.radio-inline:not(:last-child) {margin-right: 20px;}
.quali-row-opt-item .lable-box, .quali-row-opt-item .lable-box span {word-break: break-word !important;}
.quali-row-opt-item .circle-icon-check{margin-top: 6px;}
.quali-row-opt-item .lable-box span img {margin-left: 5px;}
.qual-decision-data form button{
    display: block;
    margin: auto;
    float: none !important;
}
.quali-row-opt-item .comment-txt {
    display: inline-block;
    padding-left: 20px;
    color: #808080;
}
.txt-info-icon span{vertical-align: middle;}
.txt-info-icon img {
    opacity: 0.5;
    vertical-align: middle;
    position: absolute;
    right: 0;
    top: 2px;
}
.field-label-icon-txt {width: 100%;}
.div-table-col.div-td .txt-info-icon {vertical-align: middle; display: inline-block; position: relative;}
.div-table-col.div-td .txt-info-icon span {display: inline-block;}
.txt-info-icon-inner {display: inline-block; position: relative; padding-right: 22px;}
.txt-info-icon-inner img {
    position: absolute;
    right: 0;
    top: 4px;
    width: 16px;
    height: auto;
}
.field-label-icon-txt .txt-info-icon {position: relative;}
/* Qualification Referrer Page CSS */
#banner{
    background: url({{ url('public/qualification/banner-pattern.png') }}) no-repeat center bottom;
    background-size: 100% auto;
    padding: 60px 0px;
    min-height: 300px;
}
.welcome-user {
    width: 30%;
    float: left;
    margin-right: 30px;
    min-height: 100px;
}
.parent-card-block {
    position: relative;
    display: block;
    max-width: 340px;
    margin: 0 auto;
    float: left;
}
.card-block {
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    right: 0;
    top: 0;
    margin: auto;
}
.card-upper-block{height: 45%; padding: 15px 0px 5px;}
.card-lower-block{height: 55%;}
.card-company-block{width: 70%; float: left;}
.card-upper-logo {width: 27%; float: left; position: relative; min-height: 30px; padding: 0 3px;}
.card-upper-logo img{max-width: 100%; max-height: 64px;}
.card-bottom-logo {float: left; width: 30%; padding: 5px 0px;}
.domians-name{float: left; width: 70%; display: table; height: 100%;}
.card-company-block span.arrow-disable:before, .card-upper-logo.arrow-disable:after{display: none;}
.domians-name ul{display: table-cell; vertical-align: middle; margin: 0; color: #fff; padding: 0; list-style: none;}
.domians-name ul li {font-size: 9px; text-align: right; padding: 2px 0px; padding-right: 26px; position: relative;}
.domians-name ul li:after {content: ''; position: absolute; width: 15px;height: 5px; background: gold; top: 6px; right: 0;}
.data-view-block{padding: 50px 0px; width: 100%;}
.text-label, .text-value {font-size: 16px; color: #444;}
.text-value-row {margin-top: 5px; margin-bottom: 5px; width: 100%;}
.form-candidate {margin-top: 50px;}
h4.heading-txt {color: {{$color2}}; margin-bottom: 20px; border-bottom: 1px solid {{$color2}}; padding-bottom: 10px; }
.double-border-line{width: 100%; border-top: 14px solid {{$color1}}; border-bottom: 14px solid {{$color2}}; }
.color-opt-radio.rdo-red.gray-btn input[type="radio"]:checked + label,
.color-opt-radio.rdo-green.gray-btn input[type="radio"]:checked + label{border-color: #888888;}
.color-opt-radio.rdo-red.gray-btn input[type="radio"]:checked + label span.rdo-color,
.color-opt-radio.rdo-green.gray-btn input[type="radio"]:checked + label span.rdo-color {background-color: #888888;}
.fa-question-circle:before {color: #585858;}
.quali-row-opt-item .lable-box.field-label-icon-txt {width: 55%; max-width: 55%;}
.dynamicSkill-blockname .txt-info-icon-inner img {top: 1px;}
.heading-b-border{border-bottom: 1px solid {{$color2}}; padding-bottom: 5px; margin-bottom: 20px; font-size: 14px;}
.cer-req-li {display: inline-block; width: 100%;}
.cer-req-txt{display: inline-block; margin-right: 10px;  padding: 3px 0px;}
.view-eye-btns .icon {padding: 1px 4px; margin: 0px 5px; display: inline-block; width: 28px; text-align: center; cursor: pointer}
.view-eye-btns .icon::before {font-size: 20px; font-weight: 300;}
.cer-req-li .btn-link {min-width: auto; padding: 0px 5px; height: 22px; line-height: 18px; color: {{$color2}}; }
.ref-data-eye span.use-eye-icon {
    width: 7px;
    height: 4px;
    border-top: 1px solid {{$color2}};
    border-bottom: 1px solid {{$color2}};
    display: inline-block;
    position: relative;
    float: right;
    margin-top: 8px;
}
span.fa.fa-eye.icon.ref-data-eye {width: 42px; text-align: left;}
span.fa.fa-eye.icon.ref-data-eye::before {position: relative;}
.ref-data-eye span.use-eye-icon::before {
    content: '';
    height: 11px;
    width: 10px;
    border-top: 1px solid {{$color2}};
    border-bottom: 1px solid {{$color2}};
    position: absolute;
    right: 0;
    top: -5px;
    display: inline-block;
}
.reg-cer-req-wrap {
    width: 100%;
    display: inline-block;
    margin: 2px 0px;
}
/*.reg-cer-req-wrap span,*/
.reg-cer-req-label {
    color: #000;
    font-weight: bold;
    vertical-align: top;
    display: inline-flex;
    padding-right: 10px;
}
.reg-cer-req-wrap span{/*display: inline-flex;*/}
.reg-cer-req-wrap span.custom-check-span {padding: 0;}
.certification-req-list {margin-bottom: 20px;}
.skill-table-textarea .autocomplete-dropdown-container{border: 1px solid #e9e9e9;}
.referrer-form .edit-map-btn {width: 30px; right: 16px; top: 0px;}
.reg-cer-req-wrap span p {margin-bottom: 0;}
.btn-min-200{min-width: 200px;}
.card-issue-date {color: #fff; font-size: 12px; text-align: center; line-height: 12px; margin-top: 10px; display: inline-block; width: 100%;}
.card-issue-date span {display: inline-block; width: 100%;}
.panel-style-default.gray-panel >.panel-heading a.accordion-toggle {color: gray !important; border-bottom-color: gray !important;}
.panel-style-default.gray-panel .panel-heading .accordion-toggle:after{background: gray !important;}
.no-background{background: none !important;}
.qualifelec-banner-img{width: 60%; float: left;}
.qualifelec-banner-img img {float: right; max-width: 300px; margin-top: 0px; max-height: 300px;}
.no-background .welcome-user {margin-top: 50px;}
.customSkillField-blockdata .skill-inputfield span{color: #444 !important; text-decoration: none;}
@media(max-width: 1440px){
	.pdf-view-popup .modal-dialog iframe {
	    width: 650px;
	    min-height: 380px;
	}
}
.registration-form-steps .dynamicSkill-blockname {
    width: 35%;
}
.registration-form-steps .customSkillField-blockdata {
    width: 65%;
}