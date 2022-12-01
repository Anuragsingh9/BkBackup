import React from 'react';
import ReactPlayer from 'react-player'

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for video rendering at dynamic link coming from api.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Boolean} props.isFromUpdate To indicate if component is loading from dashboard to apply the padding
 * @param {String} props.url The url of the video to be played
 *
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const VideoPlayer = (props) => {
    return (
        <div className="videoBeforeEvent" style={props.isFromUpdate ? {} : {padding: '20px'}}>
            <ReactPlayer width="100%" height="100%" url={props.url} />
        </div>
    )
}

export default VideoPlayer;