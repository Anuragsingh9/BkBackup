import React from 'react';
import ReactPlayer from 'react-player'

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for video rendering of a dummy user
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {String} props.url Video Link URL For the dummy user
 * @param {Number} props.volume Volume of dummy user video
 *
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */

const DummyVideo = (props) => {
    return (
        <div>

            <center style={{height: "100%", width: "100%"}}>
                <ReactPlayer
                    height="100%"
                    width="100%"
                    className='react-player'
                    url={props.url}
                    controls={false}
                    playing
                    volume={props.volume * 0.01}
                />
            </center>
        </div>
    )
}

export default DummyVideo;