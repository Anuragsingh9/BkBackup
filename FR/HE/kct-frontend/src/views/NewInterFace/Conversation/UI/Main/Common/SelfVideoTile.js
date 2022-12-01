import React from 'react';
import Helper from '../../../../../../Helper.js';
import {DefaultVideoTile} from "amazon-chime-sdk-js";


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render self video during conversation.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {DefaultVideoTile} selfSeat User self seat video element tile object
 * @returns {JSX.Element}
 * @constructor
 */
const SelfVideoTile = ({selfSeat}) => {
    return (
        <div className="profile-img bg-cover rounded-10 no-texture p-relative">
            {
                (selfSeat.tileState === null || selfSeat.tileState.active !== true)
                && <Helper.pageLoading />
            }
            <video className="first-person-video  first-person-thumb no-texture" id="self-video"></video>

            <audio id="self-audio"></audio>
            <div className="online-circle"></div>
        </div>
    )

}

export default SelfVideoTile;