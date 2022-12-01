import React, {useEffect, useState} from 'react';
import {Form} from "react-bootstrap";
import {
    askToPrivateConversation,
    getMediaPermissions,
    leaveConversation,
    runDeviceTest,
    stopStreamedVideo
} from '../../../Utils/Conversation.js';
import ConverSationButton from './Button.js';
import {connect, useDispatch} from 'react-redux';
import {useTranslation} from 'react-i18next';
import SpaceHostCallButton from "../../../../Common/SpaceHostCallButton";
import newInterfaceActions from "../../../../../../redux/actions/newInterfaceAction";
import ReactTooltip from 'react-tooltip';
import _ from 'lodash';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render conversation buttons for normal user which are :
 * 1.self badge editor - to update user's detail/profile
 * 2.mute/unmute - mute or unmute current conversation
 * 3. leave conversation - to leave current ongoing conversation
 * 4. setting - to open a popup in which user can manage device(mic/camera/speaker) settings
 * 5. Isolation button - To make conversation isolate(user can't break conversation from his side or no other user can
 * join conversation)
 * 6. Call space host - button to call space host(if available to join)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Function} togglePopup To toggle the media device popup
 * @param {Boolean} isMute Variable to store the value of current mute state
 * @param {Boolean} buttonState Indicator for disabling and enabling buttons click
 * @param {Function} setIsMute To update the current mute value inside conversation
 * @param {Function} toggleMediaDevicePopup To hide/show the media device selection popup
 * @param {Function} setShowDeviceSelector To set the device selector visibility
 * @param {Function} setAvailableMediaDevice To update the available devices list in system
 * @param {Boolean} is_conversation_private To indicate if conversation is private or open for all event space user
 * @param {Object} props Props passed from parent component
 * @param {ConversationMeta} props.conversationMeta Current conversation state from redux
 * @param {Function} props.updateConversationFullScreen  To toggle the current conversation full screen mode
 * @param {Function} props.updateVideoMuteText To update the conversation mute state and text related to it
 *
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
let ConversationButton = (
    {
        togglePopup,
        isMute,
        buttonState,
        setIsMute,
        toggleMediaDevicePopup,
        setShowDeviceSelector,
        setAvailableMediaDevice,
        is_conversation_private,

        ...props
    }) => {
    const dispatch = useDispatch();
    const {t} = useTranslation('myBadgeBlock')
    const [showLeaveLoader, setShowLeaveLoader] = useState(false);
    const [volumeIconBtn, setvolumeIconBtn] = useState(0);


    useEffect(() => {
        if (props.preRecordedVolume <= 0) {
            setvolumeIconBtn(0)
        } else {
            setvolumeIconBtn(1)
        }
    }, [props.preRecordedVolume])


    useEffect(() => {
        runDeviceTest(null, () => {
            handleSettingPopup();
        });
        return () => {
            stopStreamedVideo();
        }
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
        <div className="video-control d-inline w-100 pt-10 text-left">
            <ReactTooltip type="dark" effect="solid" id='conversationfull' />
            <ConverSationButton
                dataTip={t("See/Update my Badge")}
                icon={'contact_popup'}
                onClick={togglePopup}
            />
            <ConverSationButton

                dataTip={isMute ? t("Unmute my microphone") : t("Mute my microphone")}
                icon={!isMute ? 'microphone' : 'mic_mute'}
                onClick={() => {
                    setIsMute(isMute ? 0 : 1);
                }}
            />
            {!showLeaveLoader ?
                <ConverSationButton
                    dataTip={t("Quit conversation")}
                    onClick={() => {
                        setShowLeaveLoader(true);
                        return dispatch(leaveConversation(null, () => {
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
            <ConverSationButton
                dataTip={t("Change Device")}
                onClick={handleSettingPopup}
                disabled={buttonState}
                icon={'setting'}
            />
            {props.conversationMeta.fullScreen &&

                <ConverSationButton
                    data-tip={
                        is_conversation_private ? t("Conversation Not Private") : t("Conversation Private")
                    }
                    onClick={() => dispatch(askToPrivateConversation())}
                    disabled={buttonState}
                    icon={is_conversation_private ? 'new_lock_icon' : 'new_unlock_icon'}
                />
            }
            {props.conversationMeta.fullScreen && props.contentManagementMeta.currentMediaType == 2 ?
                <div className="contentVolumeSlider">
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
                        className="Slider control-button video-buttons fs-pre-slider"
                        onChange={e => {
                            props.setPreRecordedVolume(e.target.value);
                        }}
                    />
                </div>
                :
                ''
            }


            <SpaceHostCallButton
                alert={props.alert}
            />
            {/*  */}

            {props.conversationMeta.fullScreen ?
                <div className={"fs-exit-btn"}>
                    <span> {t('popup:Exit Full Screen')}</span>
                    <ConverSationButton
                        dataTip={
                            t(props.conversationMeta.fullScreen
                                ? t('popup:Exit Full Screen')
                                : 'Enter Full Screen')
                        }
                        onClick={handleEnterFullScreen}
                        disabled={buttonState}
                        icon={props.conversationMeta.fullScreen ? 'exit_full_screen' : 'enter_full_screen'}
                    />
                </div>
                :
                ""
                // <ConverSationButton
                //     dataTip={t(props.conversationMeta.fullScreen ? t('popup:Exit Full Screen') : 'Enter Full Screen')}
                //     onClick={handleEnterFullScreen}
                //     disabled={buttonState}
                //     icon={props.conversationMeta.fullScreen ? 'exit_full_screen' : 'enter_full_screen'}
                // />
            }

        </div>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        updateConversationFullScreen: (data) => dispatch(newInterfaceActions.NewInterFace.setConversationFullScreen(data)),
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



ConversationButton =  connect(mapStateToProps, mapDispatchToProps)(ConversationButton);
export default ConversationButton;