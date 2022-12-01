const graphicEnable = (color) => {
    let css = `
    .welcome-sign>h2{
        color:#fff!important;
    }
    .welcome-sign{
        background-color:#2f68ff!important;
    }
    .forget-pass.font-14>a{
        color:#fff!important; 
    }

    .login-ac>.col-md-12>div>button{
        background:#6fd449!important;
        color:#fff!important;
    }
    .dark-spaces .every-space h3, .dark-spaces .every-space h4 {
        color: #2f68ff!important;
    }
    .regi-tags h4 {
        color: #2f68ff!important;
    }
    .number-outer>.number-inner {
        color: #2f68ff!important;
    }
    .dark-spaces .every-space h6 {
        color: #2f68ff!important;
    }
    .dark-form h4 {
        color: #337ab7;
    }
    .dark-form h4 {
        color: #2f68ff!important;
    }
    .cal-add .fa-calendar-plus-o, .bring-down, .cal-add .fa-calendar-plus-o, .cal-add .react-add-to-calendar__dropdown ul li:hover a {
        background: rgb(47 104 255) !important;
    }
    .black-btn button {
        background: rgb(47 104 255) !important;
        color: #FFF !important;
    }
    .dark-invite .no-border::placeholder {
        color: rgb(47 104 255) !important;
    }
    .regi-heading h3 {
        color: #2f68ff!important;
    }
    .badge-right-content p, .modify-badge, .pop-close-btn, .trash-btn, .badge2-body .editable-select .css-1pahdxg-control input, .selected-valuebox, .value-bx, .pop-tags, .site-color, .modal-title, .drop-btn, .drop-btn:hover, .drop-btn:visited, .drop-btn:link, .drop-btn:active, .dropdown-menu .dropdown-item, .badge-decription, .timer-para, .count-main p, .badge-name, .badge-para, .two-rings-space h4, .white-space, .white-space h4, .two-rings-space, .every-space h6, .host-outer h6 {
        color: rgb(47 104 255) !important;
    }
    .bform-head .css-1uccc91-singleValue {
        color: #2f68ff!important;
    }
    .dark-form .css-yk16xz-control, .dark-form .css-1pahdxg-control, .black-form input, .dark-invite input, .user-hover-photo::after, .online-circle, .user-toolbar {
        border-color: rgb(47 104 255) !important;
    }
    .cal-add .react-add-to-calendar__dropdown ul li a, .bform-head .css-1wa3eu0-placeholder, .black-form input, .dark-form .css-yk16xz-control, .dark-form .css-1pahdxg-control, .bform-head .css-1uccc91-singleValue, .bform-head .css-b8ldur-Input {
        color: rgb(47 104 255) !important;
    }
    .head-login h4, .have-log>p, .have-log>a{
        color:#fff!important;
    }
    .host-outer, .user-toolbar, .welcome-sign {
        background: #3b3b3b !important;
    }
    .host-outer>span>p {
        margin: 0 0 13px;
        color: rgb(255 255 255 / 50%);
    }
    .host-outer h6 {
    color: #ffffff!important;
}
    .user-btn, .user-btn button, .username-slider-dp, .username-dp, .grid-user-dp-name, .ban-con {
        background-color: #2f68ff !important;
    }
    .waiting-bg {
        background-image:url('')!important;
        background: #6fd449!important;
    }
    .color-extend .main-space {
        background: rgb(111 212 73) !important;
    }
    .grid-section {
        background: #2f68ff !important;
    }
     .videoframe, main-host {
        background: rgb(111 212 73) !important;
    }
    .black-form input {
        color: #2f68ff!important;
    }
    // .heading-color,
    //  .head-login h4,
    //   .have-log p,
    //    .have-log a,
    //     .otp-main h4,
    //      .forget-pass a,
    //       .resend-otp,
    //        .create-account-link {
    //     color: rgb(255 255 255) !important;
    // }
    .blackbox, .join-border, .header-name .username-slider-dp, .badge-inner .uploadePicCrop .noUserPicTxt .userPicName {
        background-color:#2f68ff!important;
    }
    .default-form .pop-tags, .badge-bottom-content .pop-tags {
        color: rgba(255, 255, 255, 1) !important;
        background: rgb(47 104 255) !important;
    }
    
    `
    setNewInterface(css);
}
const setNewInterface = (css) => {
    document.getElementById('graphicCustomCss').innerHTML = css;
}
export default graphicEnable;