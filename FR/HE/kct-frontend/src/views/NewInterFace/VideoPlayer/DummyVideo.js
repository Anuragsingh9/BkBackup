import React from 'react';
import {Player} from 'video-react';
import _ from 'lodash';
import './videoplayer.css';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for video rendering of a dummy user
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Object} props.user user data
 * @returns {JSX.Element}
 * @constructor
 */
const DummyVideo = (props) => {
    const {user} = props;
    // dummt user object
    const dummy = !_.isEmpty(user) ? user[0] : {};

    // dummy user video url
    let dummyUrl = '';

    if (dummy.dummy_video_url) {
        dummyUrl = dummy.dummy_video_url.replace('west-2', 'west-1');
    }

    return (
        <div className="DummyVideoDiv" style={{width: '248px'}}>
            <Player
                src={dummyUrl ? dummyUrl : 'https://s3.eu-west-1.amazonaws.com/kct-videos/13-Work-Man-07-1.mp4'}
                autoPlay={true}
                controls={false}
                loop={true}
                muted={true}
                class
                videoWidth={245}
                videoHeight={138}
            />
            {
                props.children && props.children
            }
        </div>
    )
}

export default DummyVideo;