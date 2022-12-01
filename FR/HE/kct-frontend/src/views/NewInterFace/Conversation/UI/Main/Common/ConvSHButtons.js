import React, {useEffect, useState} from 'react';
import ConverSationButton from './Button.js';
import {connect, useDispatch} from 'react-redux';

import {getMediaPermissions, leaveConversation, runDeviceTest, stopStreamedVideo} from '../../../Utils/Conversation.js';
import './shbutton.css'
import {useTranslation} from 'react-i18next';
import newInterfaceActions from "../../../../../../redux/actions/newInterfaceAction";
import ReactTooltip from 'react-tooltip';
import {Form} from "react-bootstrap";
import _ from 'lodash';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render conversation buttons for space host(system role) which are :
 * 1.self badge editor - to update user's detail/profile
 * 2.mute/unmute - mute or unmute current conversation
 * 3. leave conversation - to leave current ongoing conversation
 * 4. setting - to open a popup in which user can manage device(mic/camera/speaker) settings
 * 5. Isolation button - To make conversation isolate(user can't break conversation from his side or no other user can
 * join conversation)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Function} setIsMute To update the current mute value inside conversation
 * @param {Boolean} isMute Variable to store the value of current mute state
 * @param {Function} togglePopup To toggle the media device popup
 * @param {Boolean} buttonState Indicator for disabling and enabling buttons click
 * @param {Boolean} is_conversation_private To indicate if conversation is private or open for all event space user
 * @param {Function} askToPrivateConversation To Ask other user for making the conversation private
 * @param {Function} toggleMediaDevicePopup To hide/show the media device selection popup
 * @param {Function} setShowDeviceSelector To set the device selector visibility
 * @param {Function} setAvailableMediaDevice To update the available devices list in system
 * @param {Object} props Props passed from parent component
 *
 * @param {Function} props.updateConversationFullScreen  To toggle the current conversation full screen mode
 * @param {Function} props.updateVideoMuteText To update the conversation mute state and text related to it
 *
 * @param {ConversationMeta} props.conversationMeta Current conversation state from redux
 * @returns {JSX.Element}
 * @constructor
 */
let ConvSHButtons = ({
                           setIsMute,
                           isMute,
                           togglePopup,
                           buttonState,
                           is_conversation_private,
                           askToPrivateConversation,
                           toggleMediaDevicePopup,
                           setShowDeviceSelector,
                           setAvailableMediaDevice,
                           ...props
                       }) => {
    const dispacth = useDispatch();
    const [showLeaveLoader, setShowLeaveLoader] = useState(false);
    const [volumeIconBtn, setvolumeIconBtn] = useState(0);

    const {t} = useTranslation(['myBadgeBlock', 'popup', 'notification', 'spaceHost'])

    useEffect(() => {
        if (props.preRecordedVolume <= 0) {
            setvolumeIconBtn(0)
        } else {
            setvolumeIconBtn(1)
        }
    }, [props.preRecordedVolume])

    useEffect(() => {
        return () => {
            stopStreamedVideo();
        }
    }, [])

    useEffect(() => {
        runDeviceTest(null, () => {
            handleSettingPopup();
        });
    }, [])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user click on setting button(gear icon button from conversation
     * button). This will open media device selector popup from which user can manage all connected devices(camera,
     * microphone, speaker)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleSettingPopup = () => {
        toggleMediaDevicePopup(true);
        setShowDeviceSelector(true);
        //method to fetch all connected device
        getMediaPermissions(null, setAvailableMediaDevice)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This Function will trigger when user click on full screen button from conversation buttons and this
     * will open a full screen conversation mode for user.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleEnterFullScreen = () => {
        props.updateConversationFullScreen(!props.conversationMeta.fullScreen);
    }

    return (
        <div className="video-control space-host-buttons d-inline w-100 pt-10 text-left">
            <ReactTooltip type="dark" effect="solid" id='conversation' />
            <ConverSationButton
                dataTip={t("See/Update my Badge")}
                icon={'badge_icon'}
                onClick={togglePopup}
            />
            <ConverSationButton
                onClick={() => {
                    setIsMute(isMute ? 0 : 1);
                }}
                dataTip={isMute ? t("Unmute my microphone") : t("Mute my microphone")}
                icon={!isMute ? 'microphone' : 'mic'}
            />
            {!showLeaveLoader ? <ConverSationButton
                    dataTip={t("Quit conversation")}
                    // onClick={()=>{dispacth(leaveConversation())}}
                    onClick={() => {
                        // stopStreamedVideo();
                        setShowLeaveLoader(true);
                        return dispacth(leaveConversation(null, () => {
                            setShowLeaveLoader(false);
                            if (_.has(props, ['updateConversationFullScreen'])) {
                                props.updateConversationFullScreen(false);
                            }
                        }))
                    }}
                    disabled={buttonState}
                    icon={'exit'}
                />
                :
                <div className='control-button no-texture video-buttons'
                     style={{display: 'inline-flex', flexDirection: 'column', justifyContent: 'center'}}>
                    <div class="loader_custom"></div>
                </div>
            }
            {/* added the setting icon for the space host section */}
            <ConverSationButton
                dataTip={t("Change Device")}
                onClick={handleSettingPopup}
                disabled={buttonState}
                icon={'setting'}
            />

            <ConverSationButton
                data-tip={
                    is_conversation_private ? t("Conversation Not Private") : t("Conversation Private")
                }
                onClick={() => dispacth(askToPrivateConversation())}
                disabled={buttonState}
                icon={is_conversation_private ? 'new_lock_icon' : 'new_unlock_icon'}
            />

            {props.conversationMeta.fullScreen && props.contentManagementMeta.currentMediaType == 2 ?
                <div className='contentVolumeSlider'>
                    <ConverSationButton
                        dataTip={t("Pre-Recorded Video Volume")}
                        onChange={e => {
                            props.setPreRecordedVolume(e.target.value);
                        }}
                        value={props.preRecordedVolume}
                        disabled={buttonState}
                        icon={volumeIconBtn == 0 ? 'volume_mute_pannel' : 'volume_pannel'}
                    />
                    <Form.Control
                        type="range"
                        value={props.preRecordedVolume}
                        className="Slider control-button video-buttons fs-pre-slider "
                        onChange={e => {
                            props.setPreRecordedVolume(e.target.value);
                            // volumeIconHandler();
                        }}
                    />
                </div>
                :
                ''
            }

            {props.conversationMeta.fullScreen ?
                <div className={"fs-exit-btn"}>
                    <span> {t('popup:Exit Full Screen')}</span>
                    <ConverSationButton
                        dataTip={
                            t(props.conversationMeta.fullScreen
                                ? t('popup:Exit Full Screenn')
                                : 'Enter Full Screen')
                        }
                        onClick={handleEnterFullScreen}
                        disabled={buttonState}
                        icon={props.conversationMeta.fullScreen ? 'exit_full_screen' : 'enter_full_screen'}
                    />
                </div>
                :
                <ConverSationButton
                    dataTip={
                        t(props.conversationMeta.fullScreen
                            ? t('popup:Exit Full Screenn')
                            : 'Enter Full Screen')
                    }
                    onClick={handleEnterFullScreen}
                    disabled={buttonState}
                    icon={props.conversationMeta.fullScreen ? 'exit_full_screen' : 'enter_full_screen'}
                />
            }
        </div>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        updateConversationFullScreen: (data) => dispatch(
            newInterfaceActions.NewInterFace.setConversationFullScreen(data)
        ),
        updateVideoMuteText: (data) => dispatch(newInterfaceActions.NewInterFace.updateVideoMuteText(data)),
        setPreRecordedVolume: (data) => dispatch(newInterfaceActions.NewInterFace.setPreRecordedVolume(data)),
    }
}

const mapStateToProps = (state) => {
    return {
        conversationMeta: state.NewInterface.conversationMeta,
        preRecordedVolume: state.NewInterface.contentManagementMeta.preRecordedVolume,
        contentManagementMeta: state.NewInterface.contentManagementMeta,
    };
};

ConvSHButtons = connect(mapStateToProps, mapDispatchToProps)(ConvSHButtons);
export default ConvSHButtons;
