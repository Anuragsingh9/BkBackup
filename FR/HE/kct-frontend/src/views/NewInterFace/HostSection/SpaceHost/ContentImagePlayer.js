import React, {useEffect, useState} from 'react';
import "./SpaceHost.css";
import EventImage from "../EventImage/EventImage";
import {connect} from "react-redux";
import Constants from "../../../../Constants";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used to show image in content player section when meeting or webinar is not started
 * or after ending .
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {String} props.image_url Image url of image to be displayed on content player
 * @param {String} props.event_image Image url of event image to use as fallback or default image
 *
 * @param {GraphicsData} props.graphics_data [State] This variable holds the current graphics data set in redux
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const ContentImagePlayer = props => {
    const [checkForEventImage, setCheckForEventImage] = useState(false);

    useEffect(() => {
        if (props.image_url) {

                // no event image is there so displaying what ever image is sent
                setCheckForEventImage(true);
        }
    }, [props.image_url]);

    return (
        <>
            {
                checkForEventImage &&
                <div className={`col-sm-12 col-md-12 videoframe tt kct-customization`}>
                    <div className="host-video-frame no-texture" id="host-video-frame">
                        <EventImage event_image={props.image_url} />
                    </div>
                </div>
            }
        </>
    );
}

const mapDispatchToProps = (dispatch) => {
    return {}
}

const mapStateToProps = (state) => {
    return {
        graphics_data: state.NewInterface.interfaceGraphics,
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(ContentImagePlayer);

