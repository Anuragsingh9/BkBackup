import React, {useEffect, useRef, useState} from 'react';
import _ from 'lodash';
import {Provider as AlertContainer, useAlert } from 'react-alert';
import Helper from '../../../../../Helper.js';
import '../../../VideoConference/VideoConference.css';
import {connect, useDispatch, useSelector} from "react-redux";
import ConversationSection from '../Main/Components/ConversationSection.js';
import EventActions from '../../../../../redux/actions/newInterface/index.js';
import SpaceHostConversation from '../Main/Components/SpaceHostCoversationBlock.js';
import CallingStatus from '../../../VideoConference/CallingStatus/CallingStatus.js';
import MediaDevicePopup from '../../../../Index/MediaDevicePopup/MediaDevicePopup.js';
import {getMediaPermissions, handleMute, StartConversation} from '../../Utils/Conversation.js';
import VideoElementRepository from '../../../../VideoMeeting/VideoElementRepository.js';
import newInterfaceActions from "../../../../../redux/actions/newInterfaceAction";
import Constants from "../../../../../Constants";
import VideoMeetingClass from "../../../../VideoMeeting/VideoMeetingClass";
import ZoomMtgEmbedded from "@zoomus/websdk/embedded";
import ChimeBGService from "../../../../VideoMeeting/ChimeBGService";


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is a wrapper component which includes all conversation components for normal user and
 * space host user and give a desired layout.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Boolean} props.isSpaceHost To indicate if the user is space host or not
 * @param {Boolean} props.hostVideo To indicate if user is space host or not
 * @param {Function} props.updateProfileData Api method to update the user profile data
 * @param {Function} props.updateConversationMute To update the conversation mute status
 * @param {Boolean} props.callStatus To indicate to show or hide the calling popup
 * @param {Function} props.updateConversationFullScreen To update the conversation full screen state
 * @param {Function} props.updateVideoMuteText To update the mute text to show user on hover of mute button
 * @param {ConversationMeta} props.conversationMeta Current conversation state from redux
 * @param {UserBadge} props.event_badge Redux store mapped variable for holding user badge data
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
let ConversationWrapper = (props) => {
    if (_.isEmpty(VideoElementRepository.SEATS)) {
        VideoElementRepository.resetSeats();
    }
    // dispatch hook 
    const dispacth = useDispatch();
    // alert ref
    const msg = useAlert();
    // button state
    const [buttonState, setButtonState] = useState(false);
    // self user seat
    const [selfSeat, setSelfSeat] = useState({tileState: null});
    // device popup show state
    const [showDeviceSelector, setShowDeviceSelector] = useState(false);
    // all available devices
    const [availableMediaDevices, setAvailableMediaDevice] = useState({});
    // state to handle no preview div
    const [noPreviewDiv, setNoPreviewDiv] = useState(true);
    // user badge show state
    const [modal, setModal] = useState(false);
    // slider component state
    const [sliderOpen, setSliderOpen] = useState(false);
    // space host props data
    const {isSpaceHost, hostVideo} = props;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles badge popup toggle while user is in conversation.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    const togglePopup = () => {
        setModal(!modal)
    }

    // spaces data state using useSelector hook
    const event_space = useSelector((state) => {
            return _.get(state.NewInterface, `interfaceSpacesData`, [])
        }
    );
    // spaceHost data state
    const spaceHost = useSelector((state) => {
            return _.get(state.NewInterface, `interfaceSpaceHostData`, [])
        }
    );
    // user badge state
    const event_badge = useSelector((state) => {
            return _.get(state.NewInterface, `interfaceBadgeData`, [])
        }
    );
    // event data state
    const event_data = useSelector((state) => {
            return _.get(state.NewInterface, `interfaceEventData`, [])
        }
    );

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will trigger when a new user joins this will first update the active conversation state
     * once the active conversation state update the video element will be created now this method will catch the
     * video element created and return that video element
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} userId User id to which the video grid element needs to be allocated
     * @return {HTMLElement}
     */
    const provideVideoElementToUser = (userId) => {
        dispacth(EventActions.addUserToActiveCon(userId));
        const i = VideoElementRepository.getUserIndex(userId);

        if (i != -1) {
            return document.getElementById(`other-video${i}`);
        } else {
            return null;
        }
    }


    useEffect(() => {
        Helper.zoom.mute();
        if ((hostVideo && isSpaceHost) || !isSpaceHost) {
            VideoElementRepository.resetSeats();
            getMediaPermissions(setShowDeviceSelector, setAvailableMediaDevice);
            if (event_space.current_joined_conversation) {
                dispacth(StartConversation(
                    event_space.current_joined_conversation,
                    selfSeat,
                    setSelfSeat,
                    provideVideoElementToUser
                )).then(() => {
                    let localSavedType = Number.parseInt(localStorage.getItem('chime_bg_type'));
                    if (localSavedType
                        // using the local storage for blur and none only
                        && (props.selectedBgOption.type === Constants.CHIME_BG.TYPE.NONE
                            || props.selectedBgOption.type === Constants.CHIME_BG.TYPE.BLUR)
                    ) {
                        // local storage and current bg are not of same type
                        props.setSelectedBgOption({type: localSavedType, value: null});
                        ChimeBGService.applyBackground(localSavedType, null);
                    }
                });
            }
        }



    }, []);

    useEffect(() => {
        getMediaPermissions(setShowDeviceSelector, setAvailableMediaDevice);
        // updating state to show the text near mute button
        props.updateVideoMuteText(true);
        let timer = setTimeout(() => {
            // this will remove the text near mute button after 15 sec
            props.updateVideoMuteText(false);
        }, 10000);
        return () => {
            // in case before 15 sec if the conversation ended the text should disappear
            props.updateVideoMuteText(false);
            clearTimeout(timer);
        }
    }, [])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user click on 'submit' button in media device popup(where user can
     * select devices - camera/speaker/mic).This function will save all device id in local storage.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} audioDevice Selected Audio input device id
     * @param {String} videoDevice Selected video input device id
     * @param {String} audioOutputDevice Selected Audio output device id
     * @param {Boolean} showPopup To indicate to show or hide the popup
     */
    const onMediaDeviceSubmit = (audioDevice, videoDevice, audioOutputDevice, showPopup) => {
        localStorage.setItem("user_audio", audioDevice);
        localStorage.setItem("user_video", videoDevice);
        localStorage.setItem("user_audio_o", audioOutputDevice);
        setShowDeviceSelector(showPopup);
        VideoMeetingClass.assignAudioVideoDevice(audioDevice, videoDevice, audioOutputDevice)
            .then(() => {
            })
            .catch((err) => {
            });
    }


    let previousMuteState = false;
    let client = ZoomMtgEmbedded.createClient();
    useEffect(() => {
        previousMuteState = client.getCurrentUser() ? client.getCurrentUser().muted : false;
        handleMute(previousMuteState);
    }, []);

    useEffect(() => {
        if (!props.conversationMeta.fullScreen) {
            VideoMeetingClass.updateSelfTile(selfSeat.tileState);
            VideoMeetingClass.refreshUserTiles();
        }
    }, [props.conversationMeta.fullScreen])

    useEffect(() => {
        handleMute(props.conversationMeta.mute);
    }, [props.conversationMeta.mute]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function take clicked  image data(from parameter) and then pass it to the function
     * 'updateProfileData' to save user profile.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {File} file File to save as user profile picture
     */
    const saveImage = (file) => {
        props.updateProfileData({field: "avatar", value: file});
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user clicked /uploaded an image to save profile and pass that image
     * object into 'saveImage' function to call an API to update user profile.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {File} file File to save as user profile picture
     */
    const onImageUploadFromPopup = (file) => {
        saveImage(file);
        setShowDeviceSelector(false);
    };


    return (
        <>
            <div className="video-meeting">
                {/* Alert Container Component  */}
                <AlertContainer
                    ref={msg}
                    {...Helper.alertOptions}
                />

                {(props.makingCall) && !isSpaceHost &&
                <CallingStatus
                    calledUserId={props.calledUserId}
                    show={props.makingCall}
                />
                }

                <div className="row">
                    {isSpaceHost ?

                        <SpaceHostConversation
                            selfSeat={selfSeat}
                            togglePopup={togglePopup}
                            isMute={props.conversationMeta.mute}
                            setIsMute={props.updateConversationMute}
                            buttonState={buttonState}
                            modal={modal}
                            setHoverSlider={setSliderOpen}
                            sliderOpen={sliderOpen}
                            msg={msg}
                            event_space={event_space}
                            event_data={event_data}
                            spaceHostData={spaceHost}
                            event_badge={event_badge}
                            //
                            setShowDeviceSelector={setShowDeviceSelector}
                            setAvailableMediaDevice={setAvailableMediaDevice}
                            availableMediaDevices={availableMediaDevices}
                            setNoPreviewDiv={setNoPreviewDiv}
                            //
                            toggleMediaDevicePopup={(val) => {
                                setShowDeviceSelector(val)
                            }}
                            isSpaceHost={isSpaceHost}
                        />

                        :

                        <ConversationSection
                            selfSeat={selfSeat}
                            togglePopup={togglePopup}
                            isMute={props.conversationMeta.mute}
                            setIsMute={props.updateConversationMute}
                            buttonState={buttonState}
                            modal={modal}
                            setHoverSlider={setSliderOpen}
                            sliderOpen={sliderOpen}
                            msg={msg}
                            event_space={event_space}
                            event_data={event_data}
                            spaceHost={spaceHost}
                            //
                            isSpaceHost={isSpaceHost}
                            setShowDeviceSelector={setShowDeviceSelector}
                            setAvailableMediaDevice={setAvailableMediaDevice}
                            availableMediaDevices={availableMediaDevices}
                            setNoPreviewDiv={setNoPreviewDiv}
                            //
                            toggleMediaDevicePopup={(val) => {
                                setShowDeviceSelector(val)
                            }
                            }
                        />

                    }

                </div>
                {/* Media device popup for selection of device */}

            </div>
            {
                showDeviceSelector &&
                <MediaDevicePopup
                    allowClose={true}
                    onClose={() => setShowDeviceSelector(false)}
                    msg={msg && msg && msg.show}
                    mode={Constants.mediaDevicePop.MODE_DEVICE_SET}
                    onSubmit={onMediaDeviceSubmit}
                    onSaveImage={onImageUploadFromPopup}
                    userFirstName={props.event_badge.user_fname}
                    userLastName={props.event_badge.user_lname}
                />
            }
        </>
    )

}


const mapDispatchToProps = (dispatch) => {
    return {
        updateConversationMute: (data) => dispatch(newInterfaceActions.NewInterFace.setConversationMute(data)),
        updateVideoMuteText: (data) => dispatch(newInterfaceActions.NewInterFace.updateVideoMuteText(data)),
        setSelectedBgOption: (selectedOption) => dispatch(newInterfaceActions.NewInterFace.setSelectedBgOption(selectedOption)),
    }
}

const mapStateToProps = (state) => {
    return {
        conversationMeta: state.NewInterface.conversationMeta,
        event_badge: state.NewInterface.interfaceBadgeData,
        makingCall: state.NewInterface.makingCall,
        selectedBgOption: state.NewInterface.selectedBgOption,
    };
};

ConversationWrapper = connect(mapStateToProps, mapDispatchToProps)(ConversationWrapper);
export default ConversationWrapper;
