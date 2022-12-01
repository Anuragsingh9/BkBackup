import React, {useRef, useState} from 'react';
import Host from '../HostSection.js';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used to show main host video
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {EventData} event_data Current event data
 * @param {Object} setZoomInitialize To set zoom initialize value
 * @returns {JSX.Element}
 * @constructor
 */
const MainHostVideo = ({event_data, setZoomInitialize}) => {
    const webinarBox = useRef(null);
    const [mute, setMute] = useState(false);

    const muteWebinar = () => {
        if (webinarBox) {
            const webinar = webinarBox.current;
            if (webinar) {
                webinar.querySelectorAll('audio').forEach(function (audio) {
                    audio.muted = mute ? false : true;
                });
                setMute(!mute)
            }

        }
    }

    return (
        <section className={`host-section`}>
            {/* <AlertContainer ref={this.msg}{...Helper.alertOptions} /> */}
            <div className="container">
                <div className="row">
                    <div ref={webinarBox}>
                        <Host event_data={event_data}
                              event_during={true}
                              active={false}
                              setZoomInitialize={setZoomInitialize}
                        />
                    </div>
                    <button onClick={muteWebinar}>
                        {mute ?
                            <i className="far fa-volume-mute"></i>
                            :
                            <i className="fas fa-volume"></i>
                        }
                        click on me here
                    </button>
                </div>
            </div>
        </section>
    )
}

export default MainHostVideo;