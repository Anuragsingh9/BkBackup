import React from 'react';
import {Player} from 'video-react';
import '../../VideoPlayer/videoplayer.css';
import Constants from "../../../../Constants";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component to show the image with passed url
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} event_image Url of image to load
 * @returns {JSX.Element}
 * @constructor
 */
const EventImage = ({event_image}) => {

    return (
        <div>
            <div className="video-block text-center crash d-inline w-100">
                <img
                    src={event_image ? event_image : Constants.contentManagement.EVENT_DEFAULT_IMAGE}
                    className="img-responsive"
                />
            </div>
        </div>
    )

};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component renders Event image or video.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} event_image Url of image to load
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const EventImageComponent = ({event_image}) => {
    return (<EventImage event_image={event_image} />);
}

export default EventImageComponent;