import React from 'react'
import "./VideoUserName.css"
import Helper from '../../../../../../../Helper';
import Svg from '../../../../../../../Svg';

const VideoUserName = (props) => {

    return (
        <div className={`${props?.className ? props.className : "videoUserNameDiv"} `}>
            <span className={`micFillUser`} style={{width: `${(props.user?.volume || 0)}%`}} />
            {!!props?.user?.is_mute &&
            <div
                className={`${props.className ? 'userNameMicStatus-fl' : 'userNameMicStatus'}`}
                dangerouslySetInnerHTML={{__html: Svg.ICON.mic_mute}}
            />
            }

            {Helper.limitText(`${props?.user?.user_fname} ${props?.user?.user_lname}`, 20)}
        </div>
    )
}

export default VideoUserName