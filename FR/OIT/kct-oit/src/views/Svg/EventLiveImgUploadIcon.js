import React from 'react';

let EventLiveImgUploadIcon = (props) => (


    <div className='eventLiveIconWrap' style={{
        width: "130px",
        height: "80px",
        borderRadius: "5px",
        backgroundColor: "#dbdbdb",
        border: "1px solid #3b3b3b",
        display: "flex",
        justifyContent: "center",
        flexWrap: "nowrap",
        flexDirection: "column",
        alignItems: "center",
        cursor: "pointer"

    }}>
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="25.965" height="22.72" viewBox="0 0 25.965 22.72">
                <path id="Icon_metro-image" data-name="Icon metro-image" d="M26.91,5.479l0,0V24.95l0,0H4.2l0,0V5.482l0,0Zm0-1.623H4.194A1.628,1.628,0,0,0,2.571,5.479V24.953a1.628,1.628,0,0,0,1.623,1.623h22.72a1.628,1.628,0,0,0,1.623-1.623V5.479a1.628,1.628,0,0,0-1.623-1.623Zm-3.246,5.68A2.434,2.434,0,1,1,21.233,7.1a2.434,2.434,0,0,1,2.434,2.434ZM25.29,23.33H5.816V20.084l5.68-9.737,6.491,8.114H19.61l5.68-4.868Z" transform="translate(-2.571 -3.856)" opacity="0.45" />
            </svg>

        </div>
        <p style={{
            fontSize: "10px",
            margin: "0"
        }}>Click here  to upload</p>
    </div>


);


export default EventLiveImgUploadIcon;