import React, {useEffect, useState} from 'react';
import "./SpaceHost.css";
import VideoPlayer from "../../PreRecord/VideoPlayer";
import {Form} from "react-bootstrap";
import Svg from '../../../../Svg';
import {connect} from "react-redux";
import newInterfaceActions from "../../../../redux/actions/newInterfaceAction";
import {useTranslation} from "react-i18next";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description - This is a child component that handles Video player and controllers it has features like start and
 * stop the video player and volume buttons and default volume is 50.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Boolean} props.isVideoPlayerLoaded To indicate if the video player has loaded or not for auto play purpose
 * @param {Boolean} props.showMuteButtonText To show the mute button text as user will see text over mute button
 * @param {Function} props.setIsVideoPlayerStarted To update the indicator of video player played or not
 *
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const ContentVideoPlayer = props => {
    const {t} = useTranslation('notification');
    // const [volume, setVolume] = useState(50);
    const [volumeIconBtn, setvolumeIconBtn] = useState(0);

    useEffect(() => {
        if (props.preRecordedVolume <= 0) {
            setvolumeIconBtn(0)
        } else {
            setvolumeIconBtn(1)
        }
    }, [props.preRecordedVolume])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description - This method is used to handle video player to strt video by using setIsVideoPlayerStarted()
     * method by passing true value in it.
     * -----------------------------------------------------------------------------------------------------------------
     */
    const startVideoPlayer = () => {
        props.setIsVideoPlayerStarted(true);
    }


    return (
        <>
            <div className={`col-sm-12 col-md-12 videoframe kct-customization bluejeans-height`}>
                <div className="host-video-frame no-texture" id="host-video-frame">
                    {props.isVideoPlayerLoaded ?
                        <VideoPlayer url={props.videoUrl} volume={props.preRecordedVolume} />
                        :
                        <button id="click-id-test" onClick={startVideoPlayer}>
                            Click here to start the experience
                        </button>
                    }
                </div>
            </div>

            <div className='volume_adjuster_wrap'>
                <Form>
                    <Form.Group controlId="formBasicRange">
                        <div className="dropdown custom_adjuster_position">
                            {
                                (props.showMuteButtonText) &&
                                <div> {t('adjust video volume here')}</div>
                            }
                            <a
                                className="drop-btn dropdown-toggle pannel_controllerBtn"
                                href="#"
                                role="button"
                                id="volumeAdjuster"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                <span
                                    dangerouslySetInnerHTML={{__html: volumeIconBtn == 0 ? Svg.ICON.volume_mute_pannel : Svg.ICON.volume_pannel}}></span>
                            </a>

                            <div className="dropdown-menu dropdown-menu-right" aria-labelledby="volumeAdjuster">
                                <div className="dropdown-item ">
                                    <Form.Control type="range" value={props.preRecordedVolume} className="Slider"
                                                  onChange={e => {
                                                      props.setPreRecordedVolume(e.target.value);
                                                      // volumeIconHandler();
                                                  }}
                                    />
                                </div>
                            </div>
                        </div>

                    </Form.Group>
                </Form>
            </div>
        </>
    );
}

const mapDispatchToProps = dispatch => {
    return {
        setIsVideoPlayerStarted: (data) => dispatch(newInterfaceActions.NewInterFace.setIsVideoPlayerStarted(data)),
        setPreRecordedVolume: (data) => dispatch(newInterfaceActions.NewInterFace.setPreRecordedVolume(data)),
    }
}

const mapStateToProps = (state) => {
    return {
        isVideoPlayerLoaded: state.NewInterface.contentManagementMeta.isVideoPlayerLoaded,
        showMuteButtonText: state.NewInterface.contentManagementMeta.showMuteButtonText,
        preRecordedVolume: state.NewInterface.contentManagementMeta.preRecordedVolume,
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(ContentVideoPlayer);

