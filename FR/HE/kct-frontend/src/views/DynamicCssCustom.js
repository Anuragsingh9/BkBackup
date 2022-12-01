import React from 'react'

const generateCSS = (colorObject) => {
    // let dynamicColor = setCssColor(colorObject.color1, colorObject.color2, colorObject.headerColor1, colorObject.headerColor2, colorObject.transprancy1, colorObject.transprancy2, colorObject.transprancy7);


    let {
        main_background_color,
        keepContact_background_color_1,
        keepContact_background_color_2,
        KeepContactColor1,
        KeepContactColor2,
        SelectedSpaceColor,
        UnselectedSpaceColor,
        TextSpaceColor,
        NameColor,
        ThumbnailColor,
        ClosedSpaceColor,
        CountdownBg,
        CountdownTextColor,
        hover_border_color,
        texts_color
    } = colorObject
    let css = `
    .App {
      background-color: ${main_background_color};
    }
    .spaces-block-inner {
      background-color: ${keepContact_background_color_1} !important;
    }
    .kct-squre-logo .squre-box.kct-squr1 {
      border-color: ${KeepContactColor1};
    }
    .kct-squre-logo .squre-box.kct-squr2,
    .video-block,
    .kct-border,
    .others-user-thumbs .circle-border {
      border-color: ${KeepContactColor2} !important;
    }
    .spaces-block{
      background-color: ${UnselectedSpaceColor};
      border-color: ${UnselectedSpaceColor};
    }
    .spaces-select{
      background-color: ${SelectedSpaceColor};
      border-color: ${SelectedSpaceColor};
    }
    .spaces-disable{
      background-color: ${ClosedSpaceColor} !important;
      border-color: ${ClosedSpaceColor} !important;
    }
    .space-block-content .kct-left h3,
    space-block-content .number-of-person,
    space-block-content .profile-title {
      color: ${TextSpaceColor} !important;
    }
    .site-color{
      color: ${KeepContactColor2} !important;
    }
    .countdownlayout{
      background-color: ${CountdownBg};
    }
    .countdownlayout{
      background-color: ${CountdownTextColor} !important;
    }
    .kct-thumb-color {
      background-color: ${ThumbnailColor} !important;
    }
    .host_center .kct-img-thumb{
      border-color: ${ThumbnailColor} !important;
    }
    .thumb-letters, .kct-thumb-name{
      color: ${NameColor} !important;
    }
    .badge-popup{
      border-color: ${KeepContactColor2} !important;
    }
    .badge-popup .badge-profile .badge-image,
    .badge-popup button.badge-close,
    .badge-popup button.badge-close {
      border-color: ${KeepContactColor2} !important;
    }
    .badge-popup button.badge-close,
    .badge-popup .badeg-content .badge-name,
    .badge-popup .badeg-content .badge-about {
      color: ${KeepContactColor2} !important;
    }
    svg *,
    .conversation-control .btn-white svg path, .conversation-control .btn-white svg rect, .conversation-control .btn-white svg rect{
      fill: ${KeepContactColor1};
    }
    .vertical-menu a,
    .user-dropdown h5{
      color: ${KeepContactColor2} !important;
    }
    .vertical-menu{
      border-color: ${KeepContactColor2} !important;
    }
    .vertical-menu a:hover, .vertical-menu a.active {
      background-color: ${KeepContactColor2} !important;
      color: #ffffff !important;
    }
    svg path, svg circle, svg rect {
      fill: ${KeepContactColor2} !important;
    }
    .kct-3 .kct-3-inner,
    .footer{
      background-color: ${KeepContactColor2} !important;
    }
    .wordpress_title,
    .header-mid-txt h4 a{
      color: ${KeepContactColor2} !important;
    }
    .main-single-user .kct-img-thumb.active-border,
    .kct-thumbnail .kct-img-thumb {
      border-color: ${ThumbnailColor} !important;
    }
    .user-outer-border{
      border-color: ${keepContact_background_color_2} !important
    } 
    .kct-thumbnail .main-single-user-thumb .circle, .single-user-thumb .circle-border,
    .host_center .circle, .main-single-user-thumb .circle{
      border-color: ${KeepContactColor1} !important;
    }
    .video-conversation .circle,
    .main-single-user-thumb .circle2{
      border-color: ${KeepContactColor2} !important;
    }
    .video-block, .ifram-video-wrap {
      border-bottom-color: ${KeepContactColor2} !important;
    }
    .space-locked i{
      background-color: ${ClosedSpaceColor} !important;
    }
    .btn-primary, .btn-primary:hover {
      background-color: ${KeepContactColor2} !important;
      border-color: ${KeepContactColor2} !important;
    }
    .modal-title{
      color: ${KeepContactColor2};
    }
    .imgUploadeStyle1 {
    background-color: ${KeepContactColor2} !important;
    padding: 10px;
}
.imgUploadeStyle1 .picUpload_parent {
    background-color: ${KeepContactColor2} !important;
    background-size: cover !important;
}
input:checked + .slider:before, span.crossBtn {
    background-color: ${KeepContactColor2} !important;
}
span.crossBtn {
    width: 20px;
    height: 20px;
    display: inline-block;
    text-align: center;
    line-height: 19px;
    background: ${KeepContactColor2};
    border-radius: 20px;
    font-size: 12px;
    position: absolute;
    top: -6px;
    right: -6px;
    cursor: pointer;
    color: #fff;
}
  `;

    setCssAtHeader(css);
    setCssDefault('');

}


const generateDefaultCSS = (colorObject) => {
    let {

        mainColor1,
        mainColor2,
        mainColor3,
        headerBackground,
        separationLineColor,
        bottomBackgroundColor,
        headerTextColor,

        customizeTexture,
        textureRound,
        textureWithFrame,
        selectedSpacesSquare,
        unselectedSpacesSquare,
        textureWithShadow,
    } = colorObject;

    let css = `
    .App {
      background-color: #fff !important;
    }
    .two-part-section .left,
    .radio-btn label input[type="radio"]:checked + span::after,
    .two-part-section .right .login-btn, .two-part-section .right .login-btn:hover, .two-part-section .right .login-btn:focus,
    .nav-dots .dot.active {
      background-color: ${mainColor2} !important;
    }
    .two-part-section .left h1{
      color: ${headerTextColor} !important;
    }
    .pagination>.active>a,
    .pagination>.active>a:focus,
    .pagination>.active>a:hover,
    .pagination>.active>span,
    .pagination>.active>span:focus,
    .pagination>.active>span:hover,
    .modal .btn-default:hover,
    .two-part-section .btn.btn-primary,
    .single-sm-form-style .btn.btn-primary,
    .form-style2 .pb-container .pb-button, .single-sm-form-style .pb-container .pb-button,
    .event_join_btn,
    .btn-primary, .btn-primary:hover {
      background-color: ${mainColor2} !important;
      border-color: ${mainColor2} !important;
    }
    .pb-container.loading .pb-button {
      border-color: ${mainColor2};
      background-color: transparent !important;
    }
    .pb-container.loading .pb-button:hover, .pb-container.loading .pb-button:focus{
      border-color: ${mainColor2} !important;
    }
    .account-name h3, .text-color2, .site-color,
    .two-part-section .right .text-w-logo,
    .two-part-section .forget-pass a,
    .create-account a,
    .badge-preview .badeg-content .badge-name, .badge-popup .badeg-content .badge-name{
      color: ${mainColor2} !important;
    }
    .site-color2{
      color: ${mainColor2} !important;
    }
    .vertical-menu a,
    .user-dropdown h5{
      color: ${mainColor2} !important;
    }
    .vertical-menu{
      border-color: ${mainColor2} !important;
    }
    .vertical-menu a:hover, .vertical-menu a.active {
      background-color: ${mainColor2} !important;
      color: #ffffff !important;
    }
    svg path, svg circle, svg rect {
      fill: ${mainColor2} !important;
    }
    .table-style1 {
      border-top-color: ${mainColor2} !important;
    }
    .line-menu > li > a.active, .line-menu > li > a.active:hover, .line-menu > li > a.active:focus {
      color: ${mainColor2} !important;
      border-bottom-color: ${mainColor2} !important;
      background-color: #f5f5f5;
    }
    .badge-preview .badge-profile .badge-image, .badge-popup .badge-profile .badge-image{
      background-color: ${mainColor2} !important;
    }
    .badge-preview .badge-profile .badge-image, .badge-popup .badge-profile .badge-image, 
    .badge-preview{
      border-color: ${mainColor2} !important;
    }
    .video-block, .ifram-video-wrap {
      border-bottom-color: ${mainColor2} !important;
    }
    .footer{
      background-color: ${mainColor2} !important;
    }
    .react-add-to-calendar a {
      color: ${mainColor2} !important;
    }
    .pagination > li > a, .pagination > li > span, .pagination > li > a:focus, .pagination > li > a:hover, .pagination > li > span:focus, .pagination > li > span:hover{
      color: ${mainColor2} !important;
    }
    
    .badge-preview .badeg-content .badge-name, .badge-popup .badeg-content .badge-name{
      color: ${mainColor2} !important;
    } 
    .badge-preview .badge-profile .badge-image, .badge-popup .badge-profile .badge-image, 
    .badge-preview, .badge-popup,
    .kct-thumbnail .kct-img-thumb{
      border-color: ${mainColor2} !important;
    }
    .kct-thumb-color{
      background-color: ${mainColor2} !important;
    }
    .badge-popup button.badge-close{
      color: ${mainColor2} !important;
      border-color: ${mainColor2} !important;
    }
    .no-event-list a{
      color: ${mainColor2};
    }
    .modal-title{
      color: ${mainColor2};
    }
   .host-outer{
      background: ${mainColor1} !important;
   }
  `;

    setCssDefault(css)

}


const generateNewInterfaceCSS = (color) => {
    setCssAtHeader('');
    setCssDefault('');
    let newCss = `
 
  .badge-right-content p,.modify-badge,.pop-close-btn,.trash-btn,.badge2-body .editable-select .css-1pahdxg-control input, .selected-valuebox, .value-bx,.pop-tags,.site-color,.modal-title ,.drop-btn , .drop-btn:hover,.drop-btn:visited, .drop-btn:link,.drop-btn:active, .dropdown-menu .dropdown-item , .badge-decription ,.timer-para ,.count-main p,.badge-name, .badge-para, .two-rings-space h4, .white-space, .white-space h4,.two-rings-space, .every-space h6, .host-outer h6 {
    color: ${color.color2} !important;
  }
  .badge2-position input:-ms-input-placeholder,.black-form input:-ms-input-placeholder { /* Internet Explorer 10-11 */
   color: ${color.color2} !important;
  }
  
  .badge2-position input::-ms-input-placeholder,.black-form input::-ms-input-placeholder { /* Microsoft Edge */
   color: ${color.color2} !important;
  }
  .user-btn ,.user-btn button,.username-slider-dp,.username-dp , .grid-user-dp-name,.ban-con{
    background-color:${color.color2} !important;
  }
  .badge-contact-edit button svg * , .video-control button svg * , .badge2-body .eyeposition2 svg *, .badge2-body .eyeposition3 svg *, .badge2-body .eyeposition1 svg *,.badge2-body span.svgicon svg{
    fill:${color.color2} !important;
  }
  .profile-img .site-loader .center-block, .private-btn-color2 .svgicon svg,.host-private-btn-color2 .svgicon svg{
    fill:  ${color.color2} !important;
  }
  .profile-img>.online-circle {
    border-color: ${color.color1} !important;
  }
  .btn-primary, .btn-primary:hover{
    background-color: ${color.color2} !important;
    border-color: ${color.color2} !important;
  }
  // .pop-close-btn,.plus-select .pb-container .pb-button{
  //   background-color:${color.color2} !important;
  // }
 .video-control .exit-btn,.video-control .member-ban-btn,.meeting-enter,.calling-bar{
   background-color:${color.color3} !important;
 }
 .host-reception .svgicon{
   background-color:${color.color3} !important;
 }
 .host-reception .svgicon svg *{
   fill: white !important;
 }
 .pop-tags{
    color:${'#fff'} !important;
 }
//  .welcome-sign .form-style2 .pb-container .pb-button{
//   background: #4275b7 !important;
// }
.welcome-sign .form-style2 .pb-container.loading .pb-button{
  background: transparent !important;
  color: white !important;
}
`;

    if (color.customizeColor) {
        newCss += `
    .dark-space-inner>.selected-width>.vip-selected-space-bg>.vip-space{
      margin: 56px auto;
  }
  .uni-color-btn>.otp-pro{
    background-color:  ${color.color1} !important;
    color: #fff!important;
  }
  .slick-slide>div>.selected-width>.vip-selected-space-bg {
    bottom: 0px;
}
  .other-users-badge {
    margin-left: 8px!important;
    }
    .dark-space-inner>.selected-width>.vip-selected-space-bg {
      width: 220px!important;
      height: 220px!important;
  }
  .dark-space-inner>.selected-width>.vip-selected-space-bg{
    transform: translate(-6px, 0px)!important;
    margin-bottom: 32px!important;
  }
  .dark-space-inner>.selected-width>.two-rings-space-bg {
    height: 220px !important;
      width: 220px !important;
  }
  .slick-slide>div>.spance-slide-bx>.vip-selected-space-bg {
    transform: translate(0px, -30px); 
  }
    @media screen and (max-width: 1300px){
      .two-rings-space {
        height: auto!important;
    }
    .regi-heading>div>.kct-2-first>.dark-space-inner>.selected-width>.selected-space>.every-space {
      left: 4px!important;
    }
    .dark-space-inner>.selected-width>.vip-selected-space-bg{
      transform: translate(-6px, 0px)!important;
      margin-bottom: 0!important;
    }
    .regi-heading>div>.kct-2-first>.dark-space-inner>.selected-width>.selected-space>.every-space {
      display: block;
      padding-left: 8px!important;
    }
      .dark-spaces .two-rings-space {
        margin-top: auto!important;
        margin-left: auto!important;
    }
      .dark-spaces .two-rings-space {
        margin-left: 31px !important;
    }
      .regi-heading>div>.kct-2-first>.dark-space-inner>.selected-width>.selected-space>.every-space {
        display: block;
        padding-left: 10px;
    }
    .dark-spaces .unselected-space {
      margin-top: 25px !important;
    }
    
    .dark-spaces .two-rings-space-bg {
        height: 200px !important;
        width: 200px !important;
        left: -25px;
        top: 0px;
        margin-left: 0px!important;
        background-size: 190px 190px!important;
    }
    .one-spaces {
        height: 165px!important;
        background-size: 100% 100%;
        width: 165px!important;
        position: relative;
        margin: 0;
    }
  }
  @media screen and (max-width: 1280px){
    
    .regi-heading>div>.kct-2-first>.dark-space-inner>.selected-width>.selected-space>.every-space{
      position: relative!important;
      left: -6px;
    }
    .dark-space-inner>.selected-width>.two-rings-space-bg {
      height: 200px !important;
      width: 200px !important;
    }
  }
  @media screen and (max-width: 1025px){
      
    .dark-spaces .selected-width{
      position: relative!important;
      left: 15px;
    }
    .regi-heading>div>.kct-2-first>.dark-space-inner>.selected-width>.selected-space>.every-space {
      display: block;
      position: relative!important;
      top: auto;
      left: -2px!important;
      padding-left: 12px!important;
  }
    .regi-heading>div>.kct-2-first>.dark-space-inner>.selected-width>.selected-space>.every-space {
      display: block;
      padding-left: 15px!important;
    }
    .selected-width>.two-rings-space-bg{
      position: relative!important;
      left: -6px!important;
    }
    .slick-slide>div>.selected-width>.two-rings-space-bg{
      position: relative!important;
      left: -0px!important;
    }
    .selected-width>.vip-selected-space-bg{
      position: relative!important;
      left: -12px!important;
    }
  }
    @media screen and (max-width:835px){
      .slick-slide>div>.selected-width>.vip-selected-space-bg {
        bottom: -3px!important;
      }
    }
    @media screen and (max-width: 811px){
      
      .dark-spaces .selected-width{
        position: relative!important;
        left: 3px;
      }
      .slick-slide>div>.selected-width>.vip-selected-space-bg {
        bottom: -3px!important;
      }ndex.c
      .slick-slide>div>.selected-width>.vip-selected-space-bg {
        bottom: -8px!important;
      }
      .dark-spaces .vip-selected-space-bg.selected-space{
        height: 200px !important;
        width: 200px !important;
      }
      .dark-space-inner>.selected-width>.vip-selected-space-bg {
        position: absolute!important;
        left: 14px!important;
      }
      .slick-slide>div>.selected-width>.two-rings-space-bg>.every-space {
        position: relative!important;
        left: -5px!important;
        transform: translate(6px, 0px)!important;
      }
      .selected-width>.two-rings-space-bg{
        position: relative!important;
        left: 15px!important;
        transform: translate(6px, 0px)!important;
      }
      .slick-slide>div>.selected-width>.two-rings-space-bg{
        position: relative!important;
        left: 15px!important;
        transform: translate(-4px, 0px)!important;
      }
      .selected-width>.two-rings-space-bg>.every-space{
        position: relative!important;
        left: 15px!important;
        transform: translate(6px, 0px)!important;
      }
      
    }
    
    @media screen and (max-width: 771px){
        .dark-spaces .two-rings-space-bg {
            max-height: 190px !important;
            width: 190px !important;
        }
        .regi-heading>div>.kct-2-first>.dark-space-inner>.selected-width>.selected-space>.every-space {
            display: block;
            position: relative!important;
            top: auto;
            left: -2px!important;
            padding-left: 12px!important;
        }
        .slick-slide>div>.selected-width>.vip-selected-space-bg {
          bottom: -7px!important;
        }
        .selected-width>.two-rings-space-bg{
          position: relative!important;
          left: 5px!important;
        }
        .selected-width>.vip-selected-space-bg{
          position: relative!important;
          left: -12px!important;
        }
        .regi-heading>div>.kct-2-first>.dark-space-inner>.selected-width>.selected-space>.every-space {
            display: block;
            padding-left: 14px!important;
        }
       .dark-spaces .selected-width{
          position: relative!important;
          left: 15px;
       }
      .regi-heading>div>.kct-2-first>.dark-space-inner>.selected-width>.selected-space>.every-space {
        display: block;
        padding-left: 12px!important;
      }
    }
       .describelabelTxt, .questionIcon, .heartIcon, .searchIcon, .proTagHeading, .perTagHeading, .PertagHeadingUserbadge, .ProtagHeadingUserbadge{
        color: ${color.color2} !important;
      }
    //   .BadgeDetailDiv .col .tab-content {
    //       background-color:  ${color.color2} !important;
    //   }
    //   .BadgeDetailDiv .col .nav-tabs a:hover{
    //        background-color:  ${color.color2} !important;
    //   }
    //   .BadgeDetailDiv .col .nav-tabs>a.active, .nav-tabs>a.active>a:focus, .nav-tabs>a.active>a:hover {
    //     color: ${color.color1} !important;
    //     border: 1px solid  ${color.color2} !important;
    //     background:  ${color.color2} !important;
    // }

      
    
      .professional-pop-tags {
        background-color: #6d35ad;
        color: #fff;
    }
      .YourDisTextArea{
        color: ${color.color2} !important;
        border-color: ${color.color2} !important;
      }
      .IcOnColor{
        fill: ${'#000'} !important;
      }
      
     
      
      
      .badge2-body .form-control,.badge2-body .selected-valuebox,.badge2-body .value-bx,.badge2-body .css-yk16xz-control, .badge2-body .css-1pahdxg-control{
          color: ${color.color2} !important;
          border-color: ${color.color2} !important;
      }
      .badge2-position input::placeholder,.black-form input::placeholder,.badge2-body .css-1uccc91-singleValue,.badge2-body .form-control::placeholder,.badge2-body .selected-valuebox::placeholder,.badge2-body .value-bx::placeholder {
        color: ${color.color2} !important;
      }
      .badge2-body .css-1wa3eu0-placeholder,.bform-head .css-1uccc91-singleValue,.invite-cross,.badge2-body label,.badge2-body .editable-select .css-yk16xz-control input{
         color:${color.color2} !important;
      }
      .waiting-bg{
        background:${color.color1} !important;
      }
      .pop-tags,.videoframe,main-host{
         background:${color.color1} !important;
      }
      .default-form .pop-tags,.badge-bottom-content .pop-tags{
          color:${color.tagColor || '#fff'} !important;
           background:${color.color2} !important;
      }
      .ProtagTextColor>#preview-tag>.pop-tags, .PertagTextColor>#preview-tag>.pop-tags{
        background:${color.color2} !important;
      }
      .black-tags>.default-form>#preview-tag>.grey-tags{
        color:white!important;
      }
      .grid-section{
        background:${color.color2} !important;
        border-radius:0px !important;
        border:none !important;
        box-shadow:none !important;
      }
      .grid-user-dp-name{
        border: 2px solid white !important;
      }
      .user-toolbar .grid-user-dp-name{
        border: none !important;
      }
       .color-extend .main-space {
        background: ${color.color1} !important ; 
        box-shadow: none ;
        border: none ;
      } 
      .cal-add .react-add-to-calendar__dropdown ul li:hover a{
        color: white !important ;
      }
      .cal-add .react-add-to-calendar__dropdown ul li a,.bform-head .css-1wa3eu0-placeholder,.black-form input,.dark-form .css-yk16xz-control, .dark-form .css-1pahdxg-control,.bform-head .css-1uccc91-singleValue,.bform-head .css-b8ldur-Input{
        color:${color.color2} !important ; 
      }
      .dark-invite input::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
        color:${color.color2} !important
      }
      
      .dark-invite input:-ms-input-placeholder { /* Internet Explorer 10-11 */
        color:${color.color2} !important
      }
      
      .dark-invite input::-ms-input-placeholder { /* Microsoft Edge */
        color:${color.color2} !important
      }
      .cal-add .react-add-to-calendar__dropdown::before{
        border-bottom-color: ${color.color2} !important;
      }
      .black-tags .pop-tags,{
         background:${color.color2} !important ; 
         color:${color.tagColor} !important
      }
      .cal-add .fa-calendar-plus-o,.bring-down,.cal-add .fa-calendar-plus-o,.cal-add .react-add-to-calendar__dropdown ul li:hover a{
        background:${color.color2} !important; 
      }
      .header-name .dropdown-toggle .fa-chevron-down:before{
      color:${color.color2} !important;
      }
      .blackbox,.join-border,.header-name .username-slider-dp,.badge-inner .uploadePicCrop .noUserPicTxt .userPicName{
        background:${color.color2} !important;
      }
      .badge-popup-video,.badge2-body,#tag-view{
        background:${color.badgeBgColor} !important;
      }
     .user-hover-photo::after , .online-circle, .user-toolbar{
        border-color: ${color.color2} !important;
      }
      .dark-form .css-yk16xz-control, .dark-form .css-1pahdxg-control,.black-form input, .dark-invite input,.user-hover-photo::after , .online-circle, .user-toolbar{
        border-color: ${color.color2} !important;
      }
    .user-toolbar{
      box-shadow: 0px -3px 7px 0px ${color.color3}!important;
      -webkit-box-shadow: 0px -3px 7px 0px ${color.color3}!important;
      -moz-box-shadow: 0px -3px 7px 0px ${color.color3}!important;
    }
    .host-outer,.user-toolbar,.welcome-sign{
      background:${color.color1} !important;
    } 
    .heading-color,.head-login h4,.have-log p,.have-log a, .otp-main h4,.forget-pass a,.resend-otp,.create-account-link{
      color:${color.color2} !important;
    }
    .dark-invite .no-border{
      color:${color.color2} !important;
    }
    .black-btn button{
      background:${color.color2} !important;
      color:#FFF !important;
    }
    .welcome-sign .form-style2 .pb-container .pb-button,.uni-color-btn  button,.pro-btn .pb-container .pb-button,.nxt-btn button span span,.login-btn-ac button span span,.forgot-pbtn button span span,.otp-pbtn button span span{
      background:${color.joinButtonBgColor} !important;
    }
    .welcome-sign .form-style2 .pb-container .pb-button,.uni-color-btn .otp-pro{
      border-color:${color.joinButtonBgColor} !important;
    }
    .uni-color-btn>.otp-pro,.welcome-sign .form-style2 .pb-container .pb-button,.uni-color-btn button,.pro-btn .pb-container .pb-button,.nxt-btn button span,.login-btn-ac button span,.forgot-pbtn button span span{
      color:${color.joinButtonTextBgColor} !important;
    }
   .number-outer{
      border: none !important;
      box-shadow: none !important;
      margin-right: 5px !important;
   }
   .simple-vip .svgicon>svg{
     fill: #d7b976!important;
   }
   .svgicon>svg{
     fill:${color.color1} !important; 
   }
   .number-inner{
      box-shadow: none !important;
   }
    `
        if (!(color.selectedSpacesSquare || color.unselectedSpacesSquare)) {
            newCss += `.selected-space{
        background: white !important;
        border-radius: 50% !important;
        position: unset !important;
        border: 18px solid #eef4f5 !important;
        height: 216px !important;
        width: 216px !important;
      }
      .unselected-space {
          background: white !important;
          border-radius: 50% !important;
          position: unset !important;
          margin: 40px 11px !important;
          height: 178px !important;
          width: 178px !important;
          border: 1px solid #c7ced1;
        } 
        
      .two-rings-space {
        margin-top: 60px !important;
      }
      .dark-spaces .unselected-space {
        margin-top: 25px !important;
      }
      .dark-spaces .two-rings-space {
        margin-left: 45px !important;
      }
      
      .vip-space-bg {
        background: white !important;
        position: relative !important;
        box-sizing: border-box;
        border-radius: 0;
      }
      .vip-space-bg:before{
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: -1;
        margin: -5px;
        border-radius: inherit;
        background: -webkit-linear-gradient(left, #8f6B29, #FDE08D, #DF9F28);
      }
      .vip-selected-space-bg.selected-space{
        border: 18px solid #ccac61 !important;
      }
      .dark-spaces .vip-selected-space-bg.selected-space{
        height: 220px !important;
        width: 220px !important;
      }
        `
        }


        if (color.customizeTexture) {

            if (!parseInt(color.textureRound)) {
                newCss += `
        .kct-customization{
          border-radius:10px !important;
        }
       .join-border{
          border-radius:20px !important;
        }
        .welcome-sign{
          border-radius:10px !important;
        }
      `
            } else {
                newCss += `.no-texture,.video-react-video,.host-outer,.videoframe{
        border-radius:0px !important;
        }
        .editable-select .css-yk16xz-control, .editable-select .css-1pahdxg-control{
           border-radius:0px  !important;
           border-top-left-radius:0px  !important;
           border-top-right-radius:0px  !important;
        }
        .badge2-body .form-control{
           border-radius:0px !important;
        }
         .join-border,.welcome-sign{
          border-radius:0px !important;
        }
        .no-border,.uni-color-btn .pb-container .pb-button,.dark-video iframe,.cal-add .fa-calendar-plus-o,.dark-form .css-yk16xz-control, .dark-form .css-1pahdxg-control,.bform-head .css-26l3qy-menu,.otp-ac input,.forgot-ac input,.register-ac .form-group input,.login-ac .form-group input,.reset-ac .form-group input,.pro-btn .pb-container .pb-button{
           border-radius:0px !important;
        }
        `
            }
            if (!parseInt(color.textureWithFrame)) {
                newCss += `.kct-customization{
        border:1px solid #fff !important;
      }
       .join-border{
          border:1px solid  #fff !important;
        }
      `
            } else {
                newCss += `.no-texture{
          border:none !important;
        }
        .join-border,.host-outer,.videoframe{
          border:0px !important;
        }
        #enter_right + .meeting-enter{
          right:-41px!important;
        }
        .meeting-enter-left {
            left: -41px!important;
            right: auto;
        }
        `
            }
            if (!parseInt(color.textureWithShadow)) {
                newCss += `.kct-customization,.join-border,.welcome-sign{
          -webkit-box-shadow: 0px 2px 19px 0px rgba(0,0,0,0.48) !important;
          -moz-box-shadow: 0px 2px 19px 0px rgba(0,0,0,0.48) !important;
          box-shadow: 0px 2px 19px 0px rgba(0,0,0,0.48) !important;
        }
        
        `
            } else {
                newCss += `.no-texture,.join-border,.welcome-sign,.host-outer,.videoframe{
          box-shadow:none !important;
        }`
            }

        } else {
            newCss += `.kct-customization{
          -webkit-box-shadow: 0px 2px 19px 0px rgba(0,0,0,0.48) !important;
          -moz-box-shadow: 0px 2px 19px 0px rgba(0,0,0,0.48) !important;
          box-shadow: 0px 2px 19px 0px rgba(0,0,0,0.48) !important;
         border-radius:10px !important;
          border:1px solid #fff !important;

      }
        `
        }
        if (parseInt(color.hasBottomBackgroundColor)) {
            newCss += `.color-extend{
          background-color: ${color.color1} !important;
        }
        .color-extend .main-space{
        background-color: transparent !important;
        box-shadow:none !important;
        border:none !important;
        }
        `
        }

        newCss += selectedSquareUnselectedSquare(newCss, color)
    } else {
        newCss += `
    .badge2-body .css-yk16xz-control,.badge2-body .css-1pahdxg-control{
    border: 2px solid #467abb !important;
    }
    .dark-form .css-yk16xz-control, .dark-form .css-1pahdxg-control,.black-form input {
        border: 2px solid #e7eaee !important;
      }
      .heading-color{
        color:#337ab7;
      }
    `
    }
    //enable selected color


    if (color.hasHeaderBackground) {
        newCss += `#header{
  background: ${color.headerBackground} !important;
  margin-bottom: 2px;
  border-bottom: 2px solid ${color.separationLineColor} !important;
  color: ${color.headerTextColor} !important;
}
//     .header-title1, .header-title2{
//   color: ${color.headerTextColor} !important;
// }
    .header-name.username-slider-dp{
  background-color: ${color.headerTextColor} !important;
}
    .footer-bg{
  background: ${color.headerBackground} !important;
  color: ${color.headerTextColor} !important;
  border-top: 2px solid ${color.separationLineColor} !important;
}
  .header-text-color{
  color: ${color.headerTextColor} !important;
  }
`
    } else {
        newCss += `
  .powerby .text,.header-title1, .header-title2,.footer-ul li a, .copyright-txt,.footer-powerby .text,.footer-ul li{
    color: ${color.color2} !important;
  }
  .footer-ul li:not(:last-child),.count-outer{
    border-color:${color.color2} !important;
  }
  `
    }
    setNewInterface(newCss);
}
const selectedSquareUnselectedSquare = (newCss, color) => {

    if (color.selectedSpacesSquare || color.unselectedSpacesSquare) {
        if (color.selectedSpacesSquare && color.unselectedSpacesSquare) {
            newCss += `.unselected-space {
          background: white!important;
          border-radius: 0 % !important;
          position: unset!important;
          border: 1px solid #c7ced1 !important;
          margin: 40px 11px !important;
          height: 178px !important;
          width: 178px !important;
      }
      .selected-space{
        background: white!important;
        border-radius: 0 % !important;
        position: unset!important;
        border: 18px solid #eef4f5 !important;
        height: 216px !important;
        width: 216px !important;
      }
`
        } else {

            if (color.unselectedSpacesSquare) {
                newCss += `.unselected-space {
                background: white !important;
                border-radius: 0% !important;
                position: unset !important;
                border: 1px solid #c7ced1;
                margin: 40px 11px;
                height: 178px;
                width: 178px;
          }
        .selected-space{
                background: white !important;
                border-radius: 50% !important;
                position: unset !important;
                border: 18px solid #eef4f5;
                height: 216px !important;
                width: 216px !important;
        }
        .vip-selected-space-bg.selected-space{
          border: 18px solid #ccac61 !important;
        }
        `
            }
            if (color.selectedSpacesSquare) {
                newCss += `.selected-space{
                background: white!important;
                border-radius: 0% !important;
                position: unset!important;
                border: 18px solid #eef4f5;
                height: 250px;
                width: 250px;
            }
            .unselected-space {
              background: white !important;
              border-radius: 50% !important;
              position: unset!important;
              border: 1px solid #c7ced1;
          }
          .vip-selected-space-bg.selected-space{
            border: 18px solid #ccac61 !important;
          }

          `
            }

        }

        newCss += `
    
      @media screen and (max-width: 835px){
        .dark-space-inner>.selected-width>.two-rings-space-bg {
          height: 200px !important;
          width: 200px !important;
        }
        
      }
    .two-rings-space-bg {
        height: 260px !important;
        width: 260px !important;
        // box-shadow: 0px 2px 12px 0px rgba(0,0,0,0.27);
        // -webkit-box-shadow: 0px 2px 12px 0px rgba(0,0,0,0.27);
        // -moz-box-shadow: 0px 2px 12px 0px rgba(0,0,0,0.27);
      }
      .two-rings-space {
        margin-top: auto !important;
      }
      .selected-width {
        height: 310px !important;
        width: 280px !important;
      }
      .step-circle1, .step-circle2{
        box-shadow: none !important;
      }
      .step-circle1{
        border: none !important;
      }
      .custom-three-step-circle {
        border: 10px solid #c7ced1 !important;
        background: #fff !important;
        box-shadow: none !important;
        width: 100% !important;
        height: 100% !important;
      }
      .dark-spaces .two-rings-space {
        margin-top: 60px !important;
        margin-left: 50px !important;
      }
      
      
      .dark-spaces .unselected-space {
        margin-top: 10px !important;
      }
      .dark-spaces .selected-width {
        height: 250px !important;
        width: 250px !important;
    }
    .vip-space-bg {
      background: white !important;
      position: relative !important;
      box-sizing: border-box;
      border-radius: 0;
    }
    .vip-space-bg:before{
      content: "";
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      z-index: -1;
      margin: -5px;
      border-radius: inherit;
      background: -webkit-linear-gradient(left, #8f6B29, #FDE08D, #DF9F28);
    }
    .vip-selected-space-bg.selected-space{
      border: 18px solid #ccac61 !important;
    }
    .dark-spaces .vip-selected-space-bg.selected-space{
      height: 220px !important;
      width: 220px !important;
    }

`
    }
    return newCss
}
const setCssAtHeader = (css) => {
    document.getElementById('dashDynamicCss').innerHTML = css;
}


const setCssDefault = (css) => {
    // document.getElementById('defaultDynamicCss').innerHTML = css;
}
const setNewInterface = (css) => {
    document.getElementById('newInterFaceCss').innerHTML = css;
}

const cssActions = {
    generateCSS,
    generateDefaultCSS,
    generateNewInterfaceCSS,
    setCssDefault
}

export default cssActions;