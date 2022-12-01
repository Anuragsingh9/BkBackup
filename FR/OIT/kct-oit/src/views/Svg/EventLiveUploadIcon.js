import React from 'react';

let EventLiveIcon = (props) => (


   <div className='eventLiveIconWrap' style={{
        width:"130px",
        height:"80px",
        borderRadius:"5px",
        backgroundColor:"#dbdbdb",
        border: "1px solid #3b3b3b",
        display:"flex",
        justifyContent: "center",
        flexWrap: "nowrap",
        flexDirection: "column",
        alignItems:"center",
        cursor:"pointer"

   }}>
       <div>
        <svg xmlns="http://www.w3.org/2000/svg" width="31.12" height="24.896" viewBox="0 0 31.12 24.896">
                <path id="Icon_awesome-photo-video" data-name="Icon awesome-photo-video" d="M29.564,0H7.78A1.556,1.556,0,0,0,6.224,1.556V6.224H14V3.112H23.34v15.56h6.224a1.556,1.556,0,0,0,1.556-1.556V1.556A1.556,1.556,0,0,0,29.564,0ZM11.281,5.008a.438.438,0,0,1-.438.438H9.385a.438.438,0,0,1-.438-.438V3.55a.438.438,0,0,1,.438-.438h1.459a.438.438,0,0,1,.438.438ZM28.4,15.123a.438.438,0,0,1-.438.438H26.5a.438.438,0,0,1-.438-.438V13.664a.438.438,0,0,1,.438-.438H27.96a.438.438,0,0,1,.438.438Zm0-5.057a.438.438,0,0,1-.438.438H26.5a.438.438,0,0,1-.438-.438V8.607a.438.438,0,0,1,.438-.438H27.96a.438.438,0,0,1,.438.438Zm0-5.057a.438.438,0,0,1-.438.438H26.5a.438.438,0,0,1-.438-.438V3.55a.438.438,0,0,1,.438-.438H27.96a.438.438,0,0,1,.438.438ZM20.228,7.78H1.556A1.556,1.556,0,0,0,0,9.336v14A1.556,1.556,0,0,0,1.556,24.9H20.228a1.556,1.556,0,0,0,1.556-1.556v-14A1.556,1.556,0,0,0,20.228,7.78ZM4.668,10.892a1.556,1.556,0,1,1-1.556,1.556,1.556,1.556,0,0,1,1.556-1.556Zm14,10.892H3.112V20.228l3.112-3.112L7.78,18.672,14,12.448l4.668,4.668Z" opacity="0.49"/>
            </svg>
        </div>
        <p style={{
            fontSize:"10px",
            margin:"0"
        }}>Click here  to upload</p>
   </div>

    // <svg xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink" width="138" height="98"
    //      viewBox="0 0 138 98">
    //     <defs>
    //         <filter id="Path_535" x="0" y="0" width="138" height="98" filterUnits="userSpaceOnUse">
    //             <feOffset dy="2" input="SourceAlpha"/>
    //             <feGaussianBlur stdDeviation="3" result="blur"/>
    //             <feFlood flood-opacity="0.161"/>
    //             <feComposite operator="in" in2="blur"/>
    //             <feComposite in="SourceGraphic"/>
    //         </filter>
    //     </defs>
    //     <g id="Group_1309" data-name="Group 1309" transform="translate(-332 -309)">
    //         <g transform="matrix(1, 0, 0, 1, 326, 309)" filter="url(#Path_535)">
    //             <g id="Path_535-2" data-name="Path 535" transform="translate(9 7)" fill="#dbdbdb">
    //                 <path
    //                     d="M 111 79.5 L 9 79.5 C 4.313076972961426 79.5 0.5 75.68692016601562 0.5 71 L 0.5 9 C 0.5 4.313076972961426 4.313076972961426 0.5 9 0.5 L 111 0.5 C 115.6869201660156 0.5 119.5 4.313076972961426 119.5 9 L 119.5 71 C 119.5 75.68692016601562 115.6869201660156 79.5 111 79.5 Z"
    //                     stroke="none"/>
    //                 <path
    //                     d="M 9 1 C 4.588783264160156 1 1 4.588783264160156 1 9 L 1 71 C 1 75.41121673583984 4.588783264160156 79 9 79 L 111 79 C 115.4112167358398 79 119 75.41121673583984 119 71 L 119 9 C 119 4.588783264160156 115.4112167358398 1 111 1 L 9 1 M 9 0 L 111 0 C 115.9705657958984 0 120 4.029434204101562 120 9 L 120 71 C 120 75.97056579589844 115.9705657958984 80 111 80 L 9 80 C 4.029434204101562 80 0 75.97056579589844 0 71 L 0 9 C 0 4.029434204101562 4.029434204101562 0 9 0 Z"
    //                     stroke="none" fill="#707070"/>
    //             </g>
    //         </g>
    //         <path id="Icon_metro-image" data-name="Icon metro-image"
    //               d="M26.91,5.479l0,0V24.95l0,0H4.2l0,0V5.482l0,0Zm0-1.623H4.194A1.628,1.628,0,0,0,2.571,5.479V24.953a1.628,1.628,0,0,0,1.623,1.623h22.72a1.628,1.628,0,0,0,1.623-1.623V5.479a1.628,1.628,0,0,0-1.623-1.623Zm-3.246,5.68A2.434,2.434,0,1,1,21.233,7.1a2.434,2.434,0,0,1,2.434,2.434ZM25.29,23.33H5.816V20.084l5.68-9.737,6.491,8.114H19.61l5.68-4.868Z"
    //               transform="translate(379.447 333.424)" opacity="0.45"/>
    //         <text id="Click_here_to_upload" data-name="Click here to upload" transform="translate(356 372)"
    //               fill="rgba(0,0,0,0.54)" font-size="9" font-family="Roboto-Regular, Roboto" letter-spacing="0em">
    //             <tspan x="0" y="0">Click here to upload</tspan>
    //         </text>
    //     </g>
    // </svg>

);


export default EventLiveIcon;