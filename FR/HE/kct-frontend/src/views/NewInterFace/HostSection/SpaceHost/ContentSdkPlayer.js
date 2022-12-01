import React from 'react';
import "./SpaceHost.css";
import ZoomComponentView from "../ZoomPlayer/ZoomComponentView";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a child component which is used rendering/displaying Zoom component in content section for
 * meetings and webinars.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Object} props.alert Reference object for displaying notification popup
 *
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const ContentSdkPlayer = props => {
    return (
        <div className={`col-sm-12 col-md-12 videoframe tt kct-customization zoom-height ${props.display ? ''
            : 'hidden'}`}>
            <div className="host-video-frame no-texture" id="host-video-frame">
                <ZoomComponentView
                    alert={props.alert}
                />
            </div>
        </div>
    );
}
export default ContentSdkPlayer;

