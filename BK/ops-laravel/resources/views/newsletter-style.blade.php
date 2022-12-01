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

.btn-text-lg{font-size: 16px;}
.content-counter {
    width: 100%;
    max-width: 120px;
    padding: 0px 30px;
    position: relative;
    display: inline-block;
}
.content-counter button.increase,
.content-counter button.decrease{
    background-color: #ffff;
    position: absolute;
    top: 0;
    width: 30px;
    height: 42px;
    box-shadow: none;
    border: 1px solid #e9e9e9;
    color: #a9a9a9;
}
.content-counter button.increase{ left: 0; border-radius: 3px 0px 0px 3px; }
.content-counter button.decrease{ right: 0; border-radius: 0px 3px 3px 0px; }
.counter-value .form-control{
    text-align: center;
    border-radius: 0;
    border-left: 0;
    border-right: 0;
}
#template-header-code, #template-footer-code {
    padding: 10px 15px;
    min-height: 200px;
    max-height: 350px;
    height: auto !important;
    overflow: auto;
}
#template-header-code h1, #template-footer-code h1 {margin-top: 5px;}
.iframe-form-header-title {display: inline-block; width: 100%;}
.iframe-form-header-title h2{margin: 10px 0px;}
.iframe-form-header hr {
    margin: 0;
    border: none;
    background: darkgrey;
}
.iframe-form-body {padding: 15px 15px;}
.modify-html-inner{padding: 15px;}
.blocksContainer .simple-drag .simple-drag-row:not(:last-child), .modify-html-box:not(:last-child){margin-bottom: 10px;}
.blocksContainer .simple-drag .simple-drag-row:last-child{margin-bottom: 0px;}
.blocksContainer .simple-drag{
    background: #f5f5f5 !important;
    border: 1px solid darkgrey;
    padding: 10px;  
    margin: 0 0 10px 0;
    max-width: 100%;
    max-height: 550px !important;
    overflow: auto;
}
.show-people-block {padding-bottom: 20px;}
.show-people-block>.row {padding-top: 12px;}
.show-people-block>.row>div {padding-bottom: 10px;}
.modify-html-box .canvasImageDiv,
.chooseblockItem{
    background-position: center;
    background-size: 100% auto;
    background-repeat: no-repeat;
}
.modify-html-box .canvasImageDiv{min-height: 150px; display: table-cell;}
.chooseblockItem{
    min-height: 40px;
    /*margin-bottom: 15px;*/
    position: relative;
}
.blocksContainer .simple-drag .simple-drag-row {
    padding: 0;
    margin-top: 0;
    height: inherit;
    border-radius: 3px;
    border-color: #dfdfdf !important;
    outline: none !important;
    overflow: hidden;
}
.show-people-block>.row>div>.row button {
    border-radius: 50%;
    border: none;
    background: #6b6b6b;
    color: #fff;
    padding: 0;
    width: 25px;
    height: 25px;
    text-align: center;
    margin-top: 7px;
}
.head-panel{
    background-color: {{$color2}};
    color: #fff;
    padding: 10px 15px;
    opacity: 0.5;
    filter: alpha(opacity=50);
    margin-bottom: 20px;
    border-radius: 2px;
}
.head-panel h4{margin: 0px;}
.matchField-box {padding: 5px 0px; width: 100%; display: block;}
.matchFields-left {border: 1px solid #ccc; width: 100%;}
.matchDragFieldLabel {padding: 10px;}
.matchDragField {padding: 0px; border-left: 1px solid #cccc;}
.matchField-right {border: 1px solid #cccccc; margin: 5px 0px; display: inline-block; width: 100%; background: #fff;}
.matchDragItem {
    min-height: 40px;
    line-height: 24px;
    cursor: move;
    padding: 8px;
    color: #333;
    background-color: white;
    border-radius: 3px;
}
.matchFieldMenu {
    display: block;
    width: 100%;
    font-size: 0;
    margin: 15px 0px;
    border-bottom: 1px solid #e5e4e5;
}
.matchFieldMenu ul {padding-left: 0; display: inline-block; margin-bottom: 0px;}
.matchFieldMenu ul li {margin: 2px 5px; float: left; list-style: none; padding: 0px 5px; cursor: pointer;}
.matchFieldMenu ul li span svg {vertical-align: bottom;}
.matchFieldMenu ul li span svg path{fill: #444;}
.matchFieldMenu ul li.active span svg path{fill: {{$color2}};}
.dragFieldsArea{margin-top: 55px;}
.search-fields .input-group-btn{border-color: #dfdfdf; border-width: 1px 1px 1px 0px; border-style: solid; border-radius: 0px 4px 4px 0px;}
.search-fields .input-group-btn button svg{width: 25px; height: 25px;}
.dragDropLabelField {
    position: absolute;
    width: 100%;
    left: 0;
    top: 0;
    height: 100%;
    background: #f5f5f5;
    padding: 10px;
    font-style: italic;
    color: #9b9b9b;
}
.dragDropLabelField + div, .itemDragDropHere{position: relative; z-index: 1;}
.add-subscription h4.form-sec-title {margin-bottom: 30px;}
.block-img-bx-inner{
    background: #f5f5f5 !important;
    border: 1px solid darkgrey;
    padding: 10px;
    margin: 0 0 10px 0;
    max-width: 100%;
}
.block-img-bx-item {
    background-color: #ffffff;
    background-position: center;
    background-size: 100% auto;
    background-repeat: no-repeat;
    min-height: 40px;
    cursor: move;
    position: relative;
}
.block-img-bx-inner, .block-img-bx-item:not(:last-child) {margin-bottom: 10px;}
.list-item-img-button, .list-item-img-button:hover{
    position: absolute;
    top: 5px !important;
    right: 5px;
    left: inherit !important;
    background-color: #ffffff !important;
    outline: none;
    color: #000 !important;
    font-size: 12px;
    border: none;
    cursor: pointer;
    text-align: center;
    border-radius: 20px;
    height: 20px;
    width: 20px;
    line-height: 18px;
    padding: 1px 2px;
}
.addBlockBtn {
    border: 1px solid {{$color2}};
    background-color: #ffffff;
    text-align: center;
    color: {{$color2}};
    font-size: 20px;
    margin-bottom: 10px;
    cursor: pointer;
}
.addBlockBtn span{
    width: 100%;
    display: inline-block;
    padding: 5px 25px;
}
.blocksContainer .simple-drag-row .rc-draggable-list-handles,
.vertical-dots{
    padding: 5px;
    background-image: url(../../public/img/vertical-dots.png);
    background-repeat: no-repeat;
    background-position: center;
    background-size: auto 16px;
    width: 25px !important;
    height: 25px;
    position: absolute;
    left: 5px;
    font-size: 0px;
    z-index: 1;
    background-color: #fff;
    border-radius: 20px;
}
.vertical-dots{top: 8px;}
.blocksContainer .simple-dra g-row .rc-draggable-list-handles{top: 5px; display: none;}
.blocksContainer .simple-drag-row .list-item-img-button{display: none;}
.blocksContainer .simple-drag-row:hover .rc-draggable-list-handles, .blocksContainer .simple-drag-row:hover .list-item-img-button{ display: inline-block;}
#addBlockModal .chooseblockItem button{
    border: 1px solid #c3c3c3;
    background-color: #fff;
    right: 5px;
    margin: -10px;
    border-radius: 25px;
    height: 25px;
    width: 25px;
    line-height: 23px;
    text-align: center;
    position: absolute;
    top: 0;
}
.modify-html-inner{
    max-height: 600px;
    overflow: auto;
    /*background-color: #fff !important;*/
}
#generated-code .tab-content {max-height: 500px; overflow: auto;}
.head-foote-view-panel, .header-view-panel, .modify-html-outer, .add-resources section .row .col-xs-12.col-sm-7 {width: 65%;}
.head-foot-control-panel, .add-resources section .row .col-xs-12.col-sm-5 { width: 35%; }
.add-subscription .tab-pane .rc-slider { margin-top: 12px; }
#addBlockModal .blocksContainer textarea.form-control {height: 150px !important;}
.iframe-form-body form#subscription input {height: 36px; font-size: 13px;}
.iframe-form-body form#subscription label {
    padding-right: 0px;
    width: 28%;
    text-align: left;
    font-size: 13px;
    padding-top: 9px;
}
.iframe-form-body form#subscription .col-sm-9 {width: 72%;}
.iframe-form-body form#subscription .form-group {margin-bottom: 10px;}
.iframe-form-body form#subscription .btn {min-width: 120px; padding: 5px 10px;}
.blocks-view-area{width: 65%;}
.blocks-controller {width: 35%;}
.blocks-controller .block-img-bx {padding: 0; margin-left: -20px;}
.blocks-controller .block-img-bx-outer {
    /*max-height: 600px;*/
    overflow: auto;
    padding-left: 20px;
}
.workshop-userlist input {
    height: 42px;
    padding: 5px 12px;
    color: #333333;
    font-size: 14px;
    line-height: 1.42857143;
    background-color: #fff;
    background-image: none;
    border-radius: 4px;
    border: 1px solid #dfdfdf;
    width: 250px;
}
.workshop-userlist button{
    height: 36px;
    min-width: 100px;
    padding: 5px 10px;
    border-radius: 0 5px 5px 0px;
    margin-left: -1px;
    margin-top: 0px;
    /*border: 1px solid red !important;*/
    box-sizing: border-box;
    font-size: 14px;
}
.workshop-user-searchlist {padding-left: 0; list-style: none;}
.autoComplete_list_li {padding: 5px 10px;}
.workshop-user-searchlist li {border-bottom-width: 0px; padding: 3px 0px;}
.template-edit-block{position: relative;}
.remove-edit-block {
    background-color: #f5f5f5;
    border: 1px solid #ececec;
    border-radius: 20px;
    width: 25px;
    height: 25px;
    position: absolute;
    right: 20px;
    top: 40px;
    font-size: 14px;
    line-height: 23px;
    z-index: 1;
}
#html-container-img-id-view{padding-bottom:9px;}
.matchDragField button.transparent-btn {
    float: right;
    border-left: 1px solid #ccc !important;
    position: absolute;
    right: 0;
    height: 100%;
    top: 0;
    padding: 12px 10px;
    z-index: 1;
    cursor: pointer;
}
.matchDragField button.transparent-btn svg {width: 16px; height: 16px;}
.matchDragField button.transparent-btn[type="button"] {font-size: 14px; color: red; padding-bottom:9px;}
.input-btn-field .input-group-btn {vertical-align: top;}
.workshop-userlist .autoCompleteOption {padding: 5px 10px; background: #fff;}
.workshop-userlist .autoCompleteOption:hover{background-color: #f7f7f7 !important;}
.select-w-btn .Select {display: table-cell;}
.subscriberList ul{padding-left: 0px;}
.subscriberList ul li {
    list-style: none;
    display: inline-block;
    width: 100%;
}
.subscriberList ul li a {color: #171717;font-size: 14px;}
.subscriberList ul li .subscriberRemove{padding: 5px; visibility: hidden;}
.subscriberList ul li:hover .subscriberRemove {visibility: visible;}
.workshop-userlist .input-group>div, .workshop-userlist .input-group>div>input{width: 100%;}
.workshop-userlist .input-group>div>input{border-top-right-radius: 0px; border-bottom-right-radius: 0px;}
.blocks-view-area-inner{
    background-color: #dedede !important;
    border: solid 1px darkgray;
    /*height: 600px !important;*/
    min-height: 400px;
    overflow: auto;
}
.subscriber-list {display: inline-block; margin: 3px 0px;}
.subscriber-list .subscriber-name {margin-right: 10px;}
.subscriber-list .subscriber-count {font-size: 14px; margin-right: 10px;}
.subscriber-list .subscriber-name a {color: {{$color2}};}
.text-trancate{
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.text-trancate a{color: #333; margin: 1px 0px; display: inline-block;}
.dragFieldsArea, .drag-list-right-item{max-height: 470px; overflow: auto;}
.copy-icon-block {position: relative; padding-right: 40px;}
.copy-icon{
    position: absolute;
    right: 0;
    top: -1px;
    border-left: 1px solid #dfdfdf;
    height: 34px;
    cursor: pointer;
}
.copy-icon svg {width: 25px; height: 25px; margin: 5px 5px;}
#addBlockModal .modal-dialog {width: 680px;}
.list-type-input {
    position: relative;
    display: table;
    width: 100%;
    border: 1px solid #e9e9e9;
    border-radius: 4px;
}
.list-type-input input.form-control {border: none; display: table-cell;}
.list-type-input .list-type-title{
    height: 42px;
    padding: 5px 1px 5px 12px;
    line-height: 32px;
    display: table-cell;
    flex-grow: 1;
    width: 1%;
    white-space: nowrap;
    vertical-align: middle;
}
.workshop-userlist .Select-input {height: 40px;}
.workshop-userlist .Select-control .Select-placeholder {height: 42px; line-height: 42px;}
.workshop-userlist .Select-input input {margin: 0; padding: 0;}
#spamTestModal .modal-body ul {list-style: none; padding: 0;}
#addBlockModal .modal-dialog .modal-content .modal-body {padding: 10px;}
.modal-body .pg-content-loader {
    margin: 50px auto;
    height: 40px !important;
    width: 40px !important;
}
.empty-popupdata {
    color: {{$color2}};
    font-size: 20px;
    margin: 0px 10px 30px;
}
.noheader-popup .modal-header {
    background: {{$color2}};
    border: none;
    display: inline-table;
    width: 100%;
}
.noheader-popup .modal-header button.close {color: #fff;}
.matchDragItem .svg-show svg {
    width: 16px;
    height: 16px;
    vertical-align: middle;
    margin-right: 5px;
}
.blocks-view-area-inner span.rc-draggable-list-handles,
.rc-draggable-list-handles{
    position: absolute;
    right: 0px;
    top: 5px;
    font-size: 13px;
    width: 25px;
    text-align: center;
    height: 25px;
    line-height: 25px;
    background: #f5f5f5;
    border-radius: 25px;
    margin-right: 0;
    color: #333;
    border: 1px solid #ececec;
}
#addBlockModal .modal-body img {
    /*width: inherit;*/
}
.matchDragField .search-fields .input-group {width: 100%;}
.block-img-bx-item .block-banner, .chooseblockItem .block-banner {width: 100%;}
.block-view-inner {
    max-height: 683px;
    min-height: 400px;
    overflow: overlay;
    border: 1px solid darkgray;
    background-color: rgb(255, 255, 255);
}
.orange{text-color: orange;}
.block-img-bx-item::before {
    content: '';
    position: absolute;
    width: 15px;
    height: 1px;
    border-top: 2px dotted darkgrey;
    top: 0;
    bottom: 0;
    margin: auto;
    display: inline-block;
    left: -30px;
}
.block-img-bx-item::after {
    content: '';
    position: absolute;
    width: 8px;
    height: 8px;
    border-top: 2px dotted darkgray;
    border-left: 2px dotted darkgray;
    display: inline-block;
    top: 0;
    bottom: 0;
    margin: auto;
    left: -30px;
    -webkit-transform: rotate(-45deg);
    -moz-transform: rotate(-45deg);
    -ms-transform: rotate(-45deg);
    -o-transform: rotate(-45deg);
    transform: rotate(-45deg);
}
.blocks-view-area-inner #edit1 {
    max-width: 668px;
    margin: 0 auto;
}
.blocks-controller form .pb-container {
    margin-top: 0px;
    text-align: right;
}
.btn-text table tr td p {margin-bottom: 0;}
#manage-template .tab-section, #future-newsletter-page .tab-section, #newsletter-edit .tab-section, #member-page2 .tab-section { 
    padding-top: 0;
}
#newsletter-edit form .form-group {margin-bottom: 10px;}
#newsletter-edit form textarea.form-control{height: 80px;}
.dnd-block {position: relative;}
.dnd-block .rc-draggable-list-handles {
    right: 10px;
    font-size: 14px;
    color: #888;
    width: 25px;
    height: 25px;
    line-height: 25px;
    cursor: grab;
    z-index: 9;
}
.dnd-block.drag-active .template-edit-block{
    opacity: 0.5;
    border: 2px solid {{$color2}};
    background: #fff;
}
.medium-toolbar-arrow-over:before {
    border-color: transparent transparent {{$color2}} transparent !important;
}
.medium-editor-toolbar-anchor-preview {
    background: {{$color2}} !important;
    /*border-radius: 10px !important;*/
    color: #fff !important;
    min-width: 50px !important;
}
.medium-editor-anchor-preview a {margin: 2px 5px 2px !important; font-size: 12px !important;}
.medium-editor-toolbar {
    background: transparent !important;
    border: none !important;
    border-radius: 5px;
    box-shadow: none !important;
}
.medium-editor-toolbar li .medium-editor-button-active {
    background-color: {{$color2}} !important;
    background: {{$color2}} !important;
    font-weight: bold;
}
.medium-editor-toolbar li .medium-editor-button-first {
    /*border-bottom-left-radius: 0px !Important;
    border-top-left-radius: 0px !Important;*/
}
.medium-editor-toolbar li button {
    background-color: {{$color2}} !important;
    background: -webkit-gradient(linear,left top,left bottom,from({{$color2}}),to({{$color2}})) !important;
    background: linear-gradient(180deg,{{$color2}},{{$color2}}) !important;
    border: 0 !important;
    border-right: 1px solid {{$color2}} !important;
    border-left: 1px solid {{$color2}} !important;
    border-left: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.3);
}
.medium-editor-toolbar li button {font-size: 14px; padding: 5px !important; font-weight: 300;}
.medium-toolbar-arrow-under:after {border-color: {{$color2}} transparent transparent transparent !important;}
.search-bx input[type="text"] {
    border-style: solid;
    border-width: 0 0 1px 0;
    border-color: rgba(204, 204, 204, 0.5) !important;
    padding: 4px 10px 5px 20px;
    outline: none;
    width: 99%;
    display: table-cell;
    font-size: 14px;
    border-radius: 4px 0px 0px 4px;
    height: 34px;
    background-color: #fafafa;
}
.search-bx button {
    background: {{$color2}};
    border: 1px solid {{$color2}} !important;
    color: #fff;
    padding: 5px;
    display: table-cell;
    border-radius: 0px 4px 4px 0px;
    font-size: 14px;
    min-width: 50px;
    height: 34px;
}
.search-filter-group fieldset {padding: 15px 25px; width: 100%;}
.search-filter-group fieldset:not(:last-child) {border-bottom: 1px solid #e5e5e5;}
.search-filter-group {
    width: 100%;
    float: left;
    height: 100%;
    overflow: auto;
    overflow-x: hidden;
    background-color: #fafafa;
}
.search-filter-view .modal-body{padding: 0px;}
.search-filters {
    float: left;
    position: fixed;
    height: 100%;
    width: 23%;
    overflow: hidden;
    top: -2px;
    overflow-x: hidden;
    padding-top: 64px;
    z-index: 9;
}
.asset-type-filter-title {
    width: 100%;
    display: inline-block;
    font-size: 14px;
    font-family: 'Lato',sans-serif;
    font-weight: 600;
    color: #040404;
    margin-bottom: 10px;
}
.search-left-filters{
    padding: 10px 0px 0px 0px;
    min-height: 400px;
    width: 23%;
    background-color: #fafafa;
}
.search-filter-result-view {
    max-height: 100%;
    padding-top: 64px;
    overflow: auto;
}
.imageTypes {width: 100%; display: inline-block;}
.search-bx {
    padding: 15px 15px;
    position: absolute;
    width: 100%;
    background: #dedede;
    z-index: 999;
    top: 0;
}
.form-control.field-sm {height: 30px;}
.ReactGridGallery_tile svg circle + path {fill: transparent !important;}
.checkbox-w-label {position: relative;}
.checkbox-w-label input[type="checkbox"] {
    position: absolute;
    left: 1px !important;
    opacity: 0;
}
.checkbox-w-label input[type="checkbox"] + span{
    position: relative;
    cursor: pointer;
    line-height: normal;
}
.checkbox-w-label input[type="checkbox"] + span::before {
    content: '';
    position: absolute;
    width: 15px;
    height: 15px;
    border: 1px solid #c7c7c7;
    left: -20px;
    background: #fff;
    top: 0px;
}
.checkbox-w-label input[type="checkbox"]:checked + span::after {
    content: '';
    position: absolute;
    width: 8px;
    height: 4px;
    border-left: 1px solid #05a6ed;
    border-bottom: 1px solid #05a6ed;
    top: 4px;
    left: -16px;
    transform: rotate(-45deg);
}
.ReactGridGallery_tile-icon-bar {
    height: 24px !important;
    width: 34px !important;
    margin: auto;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
}
.ReactGridGallery_tile-icon-bar div[title="Select"] {
    height: 34px !important;
    width: 34px !important;
    padding: 0 !imporant;
}
.ReactGridGallery_tile-icon-bar svg {width: 34px; height: 34px;}
.full-w-btn-group {display: inline-block; padding: 5px 15px;}
.full-w-btn-group button:not(:last-child){margin-right: 2%;}
.pagination-bar .pagination {
    display: inline-block;
    padding-left: 0;
    margin: 5px 0;
    border-radius: 4px;
    width: 100%;
    font-size: 0;
}
.pagination-bar .pagination>li>a, .pagination-bar .pagination>li>span {
    border: none;
    color: #888888;
    padding: 5px 5px;
    margin: 2px;
}
.pagination-bar .pagination>li>a:hover, .pagination-bar .pagination>li>span:hover, .pagination-bar .pagination>li>a:focus, .pagination-bar .pagination>li>span:focus {
    color: {{$color2}};
    background-color: transparent;
}
.pagination-bar .pagination>.active>a, .pagination-bar .pagination>.active>a:focus, .pagination-bar .pagination>.active>a:hover, .pagination-bar .pagination>.active>span, .pagination-bar .pagination>.active>span:focus, .pagination-bar .pagination>.active>span:hover {
    background-color: transparent !Important;
    border-color: transparent !Important;
    color: {{$color2}};
}
.pagination-bar .pagination>li {display: inline-block !important; font-size: 14px;}
.pagination-bar {
    padding-left: 0;
    padding-right: 0;
    text-align: center;
}
.auto-scroll-popup .search-filter-view .modal-body, .auto-scroll-popup .scroll-modal-content .modal-body {height: 100%;}
.auto-scroll-popup .search-filter-view .modal-header,
.auto-scroll-popup .scroll-modal-content .modal-header{
    height: 0px;
    min-height: 0px;
    padding: 0px;
    border-bottom: none; 
}
.auto-scroll-popup .search-filter-view .modal-header button.close,
.auto-scroll-popup .scroll-modal-content .modal-header button.close{
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
.search-filter-result-view .pg-content-loader, .loader-wrap, .loader-wrap .pg-content-loader {
    margin: auto;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
}
.search-filter-result-view .pg-content-loader {
    width: 50px !important;
    max-height: 50px;
    margin: auto !important;
    z-index: 999;
}
.loader-wrap{
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, .50);
    z-index: 9;
}
.search-filters-result {padding: 0px 0px 0px 0px; font-size: 0px; width: 77%;}
.filter-apply-btn {padding: 15px 20px; border-top: 1px solid #e5e5e5;}
.pagination-and-btn-control {
    width: 77%;
    position: fixed;
    bottom: 1px;
    right: 1px;
    background: #fafafa;
    border-width: 1px 0px;
    border-style: solid;
    border-radius: 0;
    border-color: #8888;
}
.img-grid-view {
    display: inline-block;
    min-height: 100%;
    min-width: 100%;
}
.search-bx .search-icon {
    position: absolute;
    width: 15px;
    margin: auto;
    top: 0;
    bottom: 0;
    height: 15px;
    left: 15px;
}
.search-bx .search-icon svg {width: 15px; height: 15px;}
.crop-img-btn-group button.btn {
    min-width: 30px;
    height: 30px;
    background-color: transparent;
    color: {{$color2}};
    padding: 4px;
    border-radius: 20px;
    line-height: 20px;
}
.crop-img-btn-group button.btn,
.crop-img-btn-group button.btn:hover,
.crop-img-btn-group button.btn:focus {
    border: 1px solid {{$color2}};
}
.crop-img-btn-group {padding: 3px;}
.auto-scroll-popup .tab-content {
    display: inline-block;
    width: 100%;
    padding: 50px 15px 0px 15px;
    overflow: auto;
    height: 100%;
}
#imgReplaceModal.auto-scroll-popup .modal-dialog,
#linkReplaceModal.auto-scroll-popup .modal-dialog {
    max-width: 420px;
}
#imgReplaceModal .nav-tabs>li{width: 50%;}
#imgReplaceModal .nav-tabs>li>a {
    background-color: {{$transprancy2}};
    color: #fff;
    padding: 9px;
    border-radius: 0;
    border: 0px !important;
    margin: 0;
    text-align: center;
}
#imgReplaceModal .nav-tabs>li.active>a{background: {{$color2}};}
#imgReplaceModal.auto-scroll-popup .search-filter-view .modal-body,
#linkReplaceModal.auto-scroll-popup .search-filter-view .modal-body{
    overflow: hidden;
}
#imgReplaceModal .tabbable-panel{height: 100%;}
#imgReplaceModal .nav-tabs{
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    background-color: #fff;
    z-index: 9;
}
#imgReplaceModal .tabbable-panel {height: 100%;}
#imgReplaceModal .modal-footer{background-color: #fff;}
#imgReplaceModal .cropImgBlock {
    padding-bottom: 42px;
    height: 100%;
    overflow: auto;
    overflow-x: hidden;
}
.link-replace-body {
    padding: 60px 15px 15px 15px;
    display: inline-block;
    width: 100%;
    height: 100%
}
.link-replace-head {
    background: #05a6ed;
    color: #dfff;
    padding: 10px 15px;
    position: absolute;
    width: 100%;
    left: 0;
    top: 0;
    z-index: 9;
}
#lightboxBackdrop button.close_1x3s325 svg {width: 24px; height: 24px;}
.search-bx .text-danger {
    position: absolute;
    left: 0;
    top: 100%;
    background: #ffff;
    width: 100%;
}
.search-bx .text-danger .validation-message{
    padding: 5px 15px;
    box-shadow: 0px 13px 6px -10px rgba(0,0,0,0.05);
    text-align: center;
    width: 100%;
}
.filter-apply-btn button.btn {min-width: 100%;}
.color-picker .sketch-picker {width: 140px !important;}
.color-picker .github-picker {width: 112px !important;}
.custom-editor center a {color: #444;}
.custom-editor center {padding: 5px; max-width: 600px; margin: 5px auto;}
/*.medium-editor-toolbar-active {
    display: none;
}*/
.search-filters label{font-weight: normal; font-size: 13px;}
.search-filter-group .color-picker .colorPicBtn {
    position: absolute;
    right: 5px;
    top: 5px;
    width: 20px !important;
    height: 20px !important;
    background: url('../../public/img/transparent-pattern-img.jpg') no-repeat center;
    background-size: cover;
}
.choose-colorbox, .choose-color-label{color: #ccc; font-size: 13px; font-weight: 300;}
.filter-gallery-inner{background-color: #dedede; overflow: hidden; padding-bottom: 45px;}
.newsletter-preview .modal-dialog .modal-content .modal-body{background-color: #dedede;}
.search-filter-group fieldset .radio-btn label span::before {top: 3px;}
.radio-btn label input[type="radio"]:checked + span::after{top: 6px;}
.search-filter-group fieldset .radio-inline, .search-filter-group fieldset .checkbox-inline{padding-left: 25px;}
.search-filter-group fieldset .checkbox-w-label input[type="checkbox"] + span::before{left: -25px; top: 2px;}
.search-filter-group fieldset .checkbox-w-label input[type="checkbox"]:checked + span::after{left: -21px; top: 6px;}
.filter-inner-sec {width: 100%; min-height: 100%; overflow: auto;}