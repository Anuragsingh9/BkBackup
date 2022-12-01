@php
    $css_data = dynamicCss();
    $color1 = $css_data['color1'];
    $color2= $css_data['color2'];
    $color3= $css_data['color3'];
    $transprancy7=$css_data['transprancy7'];
    $transprancy1=$css_data['transprancy1'];
    $transprancy2=$css_data['transprancy2'];
@endphp

.left-sidebarstyle1 h3{
    color: {{$color2}};
}
.accordion-layout-menu{
    border: 1px solid #e5e4e5;
    display: inline-block;
    width: 100%;
}
.accordion-layout-menu ul,
.accordion-layout-menu .dnd-list-menu {
    list-style: none;
    padding-left: 0;
    margin-bottom: 0px;
    display: inline-block;
    width: 100%;
    float: left;
}
.accordion-layout-menu ul li,
.accordion-layout-menu .dnd-list-menu div {
    width: 100%;
    float: left;
    position: relative;
}
.accordion-layout-menu>ul>li,
.accordion-layout-menu .dnd-list-menu>div {
    padding: 0px;
}
.accordion-layout-menu ul li:not(:last-child) a::before,
/*.accordion-layout-menu .dnd-list-menu:not(:last-child) div a::before,*/
.accordion-layout-menu .dnd-list-menu div a::before{
    content: '';
    position: absolute;
    width: 95%;
    left: 0;
    right: 0;
    margin: 0 auto;
    border-bottom: 1px solid #e5e4e5;
    height: 1px;
    bottom: 0;
}
.accordion-layout-menu ul li a,
.accordion-layout-menu .dnd-list-menu div.has-child a{
    background-color: #fff;
    color: #444;
    display: block;
    font-size: 14px;
    padding: 10px 20px 10px 10px;
    position: relative;
}
.accordion-layout-menu ul.survey-sidebar-menu li a {
    padding-right: 30px;
}
.accordion-layout-menu ul.survey-sidebar-menu li.thanks-link a {
    padding: 5px 5px;
}
.accordion-layout-menu ul li > a:hover,
.accordion-layout-menu ul li a.active,
.accordion-layout-menu .dnd-list-menu div.has-child > a:hover,
.accordion-layout-menu .dnd-list-menu div.has-child a.active{
    background-color:  {{$transprancy7}};
    color: #fff;
    text-decoration: none;
}
.accordion-layout-menu ul.survey-sidebar-menu li.thanks-link a:hover,
.accordion-layout-menu ul.survey-sidebar-menu li.thanks-link a.active{
    background-color: #fff;
    color: #444;
}
.accordion-layout-menu ul li.has-child ul.sub-menu li a,
.accordion-layout-menu .dnd-list-menu .has-child ul.sub-menu li a  {
    padding-left: 20px;
}
.accordion-layout-menu ul li a.active::after,
.accordion-layout-menu .dnd-list-menu div a.active::after {
    /*content: '>';*/
    content: '';
    position: absolute;
    border-color: {{$transprancy7}};
    right: -12px;
    top: 0;
    bottom: 0;
    margin: auto;
    height: 10px;
    width: 10px;
    text-align: center;
    border-width: 2px 2px 0px 0px;
    border-style: solid;
    -webkit-transform: rotate(45deg);
    -moz-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    -o-transform: rotate(45deg);
    transform: rotate(45deg);
}
.survey-ques {
    position: relative;
    margin: 15px 0px;
    display: inline-block;
    width: 100%;
}
.survey-ques .ques-number,
.respblock .ques-number{
    position: absolute;
    left: 0;
    top: -5px;
    font-size: 17px;
    min-width: 45px;
    max-width: 54px;
    border: 1px solid {{$color2}};
    border-radius: 8px;
    color: {{$color2}};
    height: 32px;
    padding: 2px;
    text-align: center;
}
/*.survey-ques .ques-number{
    text-align: center;
}
.respblock .ques-number{
    text-align: right;
}*/
.survey-ques .ques-number::before,
.respblock .ques-number::before {
    content: '';
    width: 10px;
    height: 10px;
    position: absolute;
    bottom: -8px;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 10px solid #fff;
    z-index: 1;
}
.survey-ques .ques-number::after,
.respblock .ques-number::after {
    content: '';
    position: absolute;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 10px solid {{$color2}};
    height: 10px;
    width: 10px;
    bottom: -10px;
}
.survey-ques .ques-number::after,
.survey-ques .ques-number::before{
    left: 7px;
}
.respblock .ques-number::after,
.respblock .ques-number::before{
    right: 7px;
}
.survey-question {
    font-size: 16px;
    margin-bottom: 5px;
}
.ques-head-line{
    font-weight: 700;
    font-size: 14px;
    font-style: italic;
    margin-bottom: 5px;
    display: inline-block;
    width: 100%;
}
.radio-small label::before{
    width: 12px;
    height: 12px;
    left: 0;
    border: 2px solid #cccccc;
    top: 2px;
}
.radio-small label::after{
    width: 8px;
    height: 8px;
    left: 2px;
    top: 4px;
}
.user-survey-ques .checkbox label,
.user-survey-ques .radio label,
.radio-small label {
    padding-left: 0px;
    font-weight: 700;
    color: #2a2a2a;
    font-size: 14px;
}
.survey-right-block {
    padding-left: 30px;
}
.draglist-block-row {
    border: 2px solid transparent;
    padding: 5px 10px 5px 25px;
    cursor: move;
    position: relative;
}
.draglist-block-row:hover {
    border-color: #b0005c;
}
.draglist-block-row .list-drag {
    position: absolute;
    left: 10px;
    opacity: 0.7;
    top: 5px;
}
.draglist-block-row:hover .list-drag{
    visibility: visible;
}
.draglist-block-row .list-drag img{
    height: 18px;
}
.nobtn {
    background: none;
    border: none;
    padding: 0;
}
.fa-plus.circle {
    border: 1px solid;
    border-color: {{$color2}};
    padding: 3px 5px;
    border-radius: 50%;
}
.ques-form-block, .ques-option-block{
    padding-left: 100px;
}
.thanks-txtlink{
    font-size: 0px;
    display: block;
    width: 100%;
}
.radio-text-field span.radio-block, .checkbox-text-field span.checkbox-block {
    position: absolute;
    padding-top: 14px;
    left: 0;
    padding-left: 20px;
}
.checkbox-small.checkbox label {
    padding-left: 0px;
    font-weight: 700;
    color: #2a2a2a;
}
.checkbox-small.checkbox label::before {
    width: 12px;
    height: 12px;
}
.checkbox-small.checkbox label::after {
    padding-left: 2px;
    padding-top: 0px;
    top: 0px;
    font-size: 9px;
}
.radio.radio-text-field,
.checkbox.checkbox-text-field {
    padding-left: 28px;
}
.ques-type-options2 {
    display: flex;
    flex-flow: wrap;
}
.ques-type-options .icon-radio-li {
    border-bottom: 1px solid #f7f7f7;
    margin: 5px 0px;
}
.ques-type-options2 .icon-radio-li{
    float: left;
    width: 25%;
}
.ques-type-options .ques-type-options>.radio {
    margin-top: 5px;
    margin-bottom: 5px;
}
.ques-type-options>.icon-radio-li:nth-child(4n+1) {
    clear: left;
}
.ques-type-options .icon-radio-li input[type="radio"] {
    width: 0;
    height: 0;
    opacity: 0;
}
.ques-type-options .icon-radio-li label {
    cursor: pointer;
    /*width: 100%;*/
    position: relative;
    padding-left: 35px;
    padding-right: 10px;
    margin-bottom: 2px;
}
.ques-type-options .icon-radio-li label span.svgicon {
    position: absolute;
    left: 0;
}
.ques-type-options .icon-radio-li label span.svgicon svg{
    width: 28px;
    height: 28px;
    vertical-align: middle;
}
.ques-type-options .icon-radio-li label .icon-li-txt {
    display: inline-block;
    vertical-align: middle;
    border-bottom: 2px solid transparent;
    padding: 7px 0px 5px 0px;
}
.ques-type-options .icon-radio-li input[type="radio"] + label svg path{
    fill: #888;
}
.ques-type-options .icon-radio-li input[type="radio"]:checked + label svg path{
    fill: {{$color2}};
}
.ques-type-options .icon-radio-li input[type="radio"]:checked + label{
    color: {{$color2}};
}
.ques-type-options .icon-radio-li input[type="radio"]:checked + label .icon-li-txt{
    border-color: {{$color2}};
}

.table.no-border>thead>tr>td, 
.table.no-border>tbody>tr>td, 
.table.no-border>tfoot>tr>td {
    border-top: none;
}
.row-col-create{
    width:100%;
    max-width:380px;
}
.row-col-create input[type=number]::-webkit-inner-spin-button,
.row-col-create input[type=number]::-webkit-outer-spin-button,
.no-of-segments::-webkit-inner-spin-button,
.no-of-segments::-webkit-outer-spin-button {
   opacity: 1;
}
table.invitees-table {
    max-width: 400px;
    width: 100%;
    margin-top: 16px;
}
.table.invitees-table>tbody>tr>td {
    padding: 8px 14px;
}
.table.invitees-table .close,
.table.invitees-table .close:hover {
    font-size: 14px;
    color: #0e0e0e;
}

.thanx-txt {
    text-align: center;
    display: inline-block;
}
.thanx-txt span {
    min-width: 27px;
    height: 32px;
    font-size: 30px;
    text-align: center;
    color: #fff;
    font-weight: 700;
    float: left;
    line-height: 30px;
    padding: 0px 2px;
    -webkit-box-shadow: 0px 2px 5px #c0c0c0;
    -moz-box-shadow: 0px 2px 5px #c0c0c0;
    box-shadow: 0px 2px 5px #c0c0c0;
}
.thanx-txt span:nth-child(odd){
    background-color: {{$color2}};
    -webkit-transform: skewY(-3deg);
    -moz-transform: skewY(-3deg);
    -ms-transform: skewY(-3deg);
    -o-transform: skewY(-3deg);
    transform: skewY(-3deg);
}
.thanx-txt span:nth-child(even){
    background-color: {{$color1}};
    -webkit-transform: skewY(3deg);
    -moz-transform: skewY(3deg);
    -ms-transform: skewY(3deg);
    -o-transform: skewY(3deg);
    transform: skewY(3deg);
}
.thanx-txt.frthanx span {
    font-size: 18px;
    min-width: 16px;
    height: 28px;
    line-height: 28px;
}
.answer-done{
    color: {{$transprancy7}};
    width: 18px;
    height: 18px;
    border: 1px solid {{$transprancy7}};
    border-radius: 20px;
    line-height: 16px;
    text-align: center;
    right: 10px;
    position: absolute;
    top: 0;
    bottom: 0;
    margin: auto;
    font-size: 12px;
}
.accordion-layout-menu ul li > a:hover .answer-done,
.accordion-layout-menu ul li a.active .answer-done{
    color: #fff;
    border-color: #fff;
}
.percentage-fields {
    max-width: 100px;
    position: relative;
}
.percentage-label {
    position: absolute;
    right: 1px;
    top: 1px;
    height: 40px;
    width: 30px;
    text-align: center;
    line-height: 40px;
    border-left: 1px solid #e9e9e9;
    background-color: #fff;
    font-size: 16px;
    font-weight: 700;
    border-radius: 0px 4px 4px 0px;
}
.date-dmy-block {
    width: 70px;
    float: left;
    position: relative;
}
.date-dmy-block:not(:last-child){
    margin-right: 16px;
}
.date-dmy-block:not(:last-child)::after {
    content: '';
    position: absolute;
    width: 8px;
    height: 2px;
    background: #e5e4e5;
    top: 0;
    bottom: 0;
    margin: auto;
    right: -12px;
}
.date-dmy-block input.form-control {
    text-align: center;
}
.respblock {
    position: relative;
}
.respblock .resp-option{
    position: relative;
}
.respblock .resp-opt-count {
    position: absolute;
    left: -40px;
    top: 2px;
    font-size: 12px;
    min-width: 30px;
    max-width: 40px;
    border: 1px solid {{$color2}};
    border-radius: 5px;
    color: {{$color2}};
    text-align: center;
    height: 20px;
    padding: 2px;
    line-height: 16px;
}
.respblock{
    counter-reset: resp-counter;
}
.respblock .resp-opt-count span::before{
  counter-increment: resp-counter;
  content: "R " counter(resp-counter);
}
.respblock .resp-opt-count::before,
.respblock .resp-opt-count::after {
    content: '';
    position: absolute;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    height: 6px;
    width: 8px;
    right: 3px;
}
.respblock .resp-opt-count::after {
    border-top: 6px solid {{$color2}};
    bottom: -6px;
}
.respblock .resp-opt-count::before {
    bottom: -4px;
    border-top: 6px solid #fff;
    z-index: 1;
    right: 3px;
}
.ques-add-page a .edit-icon{
    position: absolute;
    right: 5px;
    top: 0;
    bottom: 0;
    margin: auto;
    width: 20px;
    height: 20px;
}
.ques-add-page a .edit-icon svg{
    width: 20px;
    height: 20px;
}
.ques-add-page a .edit-icon svg path{
    fill: #fff;
}
.numerical-slider{
    overflow: hidden;
}
.observaroty-table>thead>tr>th,
.observaroty-table>tbody>tr>td {
    padding: 0px;
    position: relative;
}
.observaroty-table>thead>tr>th span,
.observaroty-table>tbody>tr>td span{
    display: block;
    padding: 2px;
}
.observaroty-table>thead>tr>th span{
    margin: 5px 6px;
}
.observaroty-table>tbody>tr>td span{
    margin: 6px;
}
.observaroty-table>thead>tr>th:not(:first-child){
    border-left: 1px solid #d7d7d7;
}
.observaroty-table>tbody>tr>th input,
.observaroty-table>tbody>tr>td input{
    width: 100%;
    border: 1px solid #e5e4e5;
    padding: 5px;
    color: #444;
}
.observaroty-table>tbody>tr>td span.edit-cell{
    margin: 0px;
}
.observaroty-table>tbody>tr.separate-row>td {
    height: 35px;
}
.observaroty-table>tbody>tr.observatory-tr{
    background-color: {{$color2}};
    color: #fff;
}
.observaroty-table>tbody>tr.observatory-child-tr{
    background-color: {{$transprancy7}};
    color: #444;
}
.observaroty-table>tbody>tr>td {
    border-top: none;
}
.observaroty-table>tbody>tr.observatory-tr>td:not(:first-child),
.observaroty-table>tbody>tr.observatory-child-tr>td:not(:first-child) {
    border-left: 1px solid #fff;
}
.observaroty-table>tbody>tr.observatory-child-tr td:first-child{
    background-color: {{$transprancy7}};
}
.observaroty-table>tbody>tr.observatory-grandchild-tr>td>span {
    margin: 3px 6px;
}
.cell-saved{
    background: #f9f9f9;
    cursor: no-drop;
}
.observaroty-table>tbody>tr.observatory-tr>td,
.observaroty-table>tbody>tr.observatory-child-tr>td {
    font-weight: 700;
}
.observaroty-table>thead>tr,
.observaroty-table>tbody>tr{
    position: relative;
}
.observation-td-control {
    text-align: center;
    position: absolute;
    right: -22px;
    top: 8px;
}
.observaroty-table tr td .observation-td-control .svgicon {
    margin: 0px;
    width: 16px;
    height: 16px;
    border: 1px solid {{$color2}};
    border-radius: 50%;
    display: inline-block;
    padding: 1px;
}
.observation-td-control .svgicon svg {
    width: 12px;
    height: 12px;
    cursor: pointer;
    float: left;
}
.observaroty-table-wrap{
    padding-right: 22px;
}
button.nobtn:hover {
    text-decoration: underline;
}
.no-of-segments{
    max-width:150px;
}
.above-arrow svg {
    -webkit-transform: rotate(-30deg);
    -moz-transform: rotate(-30deg);
    -ms-transform: rotate(-30deg);
    -o-transform: rotate(-30deg);
    transform: rotate(-30deg);
}
.bellow-arrow svg {
    -webkit-transform: rotate(30deg);
    -moz-transform: rotate(30deg);
    -ms-transform: rotate(30deg);
    -o-transform: rotate(30deg);
    transform: rotate(30deg);
}
.menu-item {
  margin: 5px 10px;
  user-select: none;
  cursor: pointer;
  border: none;
}
.scroll-menu-arrow {
  padding: 0px;
  cursor: pointer;
}
.scroll-menu-arrow .arrow-prev, .scroll-menu-arrow .arrow-next {
    font-size: 24px;
    color: {{$color2}};
}
.scroll-menu-arrow .arrow-prev{
    margin-right: 10px;
}
.scroll-menu-arrow .arrow-next{
    margin-left: 10px;
}
.accordion-layout-menu ul li.has-child ul.sub-menu li ul.sub-menu li a {
    padding: 6px 10px 10px 25px;
    font-size: 13px;
}

/**************/
.menu-item-wrapper .icon-radio-li label{
	cursor: pointer;
}
.menu-item-wrapper .icon-radio-li svg path,
.menu-item-wrapper .icon-radio-li svg * {
    fill: #888 !important;
}
.menu-item-wrapper .icon-radio-li label.selected svg path {
    fill: {{$color2}} !important;
}
.menu-item-wrapper .icon-radio-li label.selected .icon-li-txt{
	color: {{$color2}};
}
.menu-item-wrapper .icon-radio-li label.disable{
	cursor: no-drop;
}
.menu-item-wrapper .icon-radio-li label.disable .icon-li-txt {
    color: #ccc;
}
.menu-item-wrapper .icon-radio-li label.disable svg path {
    fill: #ccc !important;
}
.check-w-dnd span.checkbox-block {
    left: -50px;
	padding-top: 10px;
}
.radio-text-field span.radio-block input, .checkbox-text-field span.checkbox-block input {
    position: absolute;
    left: 0;
    width: 18px;
    height: 18px;
    margin-top: 0px;
}
.check-w-dnd .dnd-block .rc-draggable-list-handles {
    right: inherit;
    left: -30px;
    background: transparent;
    border: none;
    /*opacity: 0;
    visibility: hidden;*/
    color: #ccc;
}
.check-w-dnd .dnd-block:hover .rc-draggable-list-handles {
    opacity: 1;
    visibility: visible;
}
.numerical-slider .scal-slider {
    max-width: 100%;
}
.check-w-field .checkbox-block{
    left: 15px;
    position: absolute;
    top: 8px;
}
.remove-add-row .btn-remove,
.accordion-layout-menu > ul > li a .btn-remove,
.accordion-layout-menu .dnd-list-menu > div a .btn-remove {
    right: 0;
    top: 2px;
    position: absolute;
}
.accordion-layout-menu > ul > li a .btn-remove,
.accordion-layout-menu .dnd-list-menu > div a .btn-remove {
    display: none;
}
.accordion-layout-menu > ul > li a:hover .btn-remove,
.accordion-layout-menu .dnd-list-menu > div a:hover .btn-remove {
    display: block;
}
.dnd-block .btn.btn-remove {
    position: absolute;
    top: -1px;
    right: 3px;
    z-index: 1;
}
.menu-item .icon-radio-li label .icon-li-txt{
    font-size: 14px;
}