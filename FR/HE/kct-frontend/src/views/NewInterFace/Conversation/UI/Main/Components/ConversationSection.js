import React, {useEffect} from 'react';
import _ from 'lodash';
import videoElementRepo from '../../../../../VideoMeeting/VideoElementRepository.js';
import BadgePopUp2 from '../../../../MyBadge/BadgePopup/BadgePopUp3.js';
import OtherBadgeButton from '../Common/OtherBadgeButton.js';
import OtherUserVideo from '../Common/OtherUserVideo.js';
import SelfVideoTile from '../Common/SelfVideoTile.js';
import IsolatedButton from '../Common/IsolatedButton';
import ConverSationButton from '../Common/Button.js';
import {askToPrivateConversation, checkDummyUser, checkSpaceHost, getUserData} from '../../../Utils/Conversation.js';
import {connect, useSelector} from "react-redux";
import FullScreenConversation from "./FullScreen/FullScreenConversation";
import newInterfaceActions from "../../../../../../redux/actions/newInterfaceAction";
import ConversationButton from '../Common/ConversationButtons.js';
import {useTranslation} from 'react-i18next';
import "./ConversationSection.css"
import '../../../../VideoConference/VideoConference.css';
import VideoUserName from '../Common/VideoUserName/VideoUserName.js';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a wrapper component of self conversation section.This component includes self video and some
 * control buttons(self badge editor, mute/unmute, leave conversation, setting, isolation conversation, call space host)
 * in it.This component will be render when user starts conversation with a user from user grid component.
 * conversation buttons  functionality:<br>
 * 1.self badge editor - to update user's detail/profile<br>
 * 2.mute/unmute - mute or unmute current conversation<br>
 * 3. leave conversation - to leave current ongoing conversation<br>
 * 4. setting - to open a popup in which user can manage device(mic/camera/speaker) settings<br>
 * 5. Isolation button - To make conversation isolate(user can't break conversation from his side or no other user can
 * join conversation)<br>
 * 6. Call space host - button to call space host(if available to join)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Object} props.msg Reference object for displaying notification popup
 * @param {Boolean} props.modal To indicate to show the media device popup or not
 * @param {Boolean} props.isMute To indicate if current conversation is muted or not
 * @param {Number} props.selfSeat Self user seat number in chime conversation video grid
 * @param {UserBadge} props.spaceHost Current space host data
 * @param {Function} props.setIsMute To update the mute state for current conversation
 * @param {EventData} props.event_data Current event data
 * @param {Number} props.sliderOpen User id on which current hovered to view user data
 * @param {Function} props.togglePopup To toggle the media device popup for update device selection
 * @param {Boolean} props.buttonState To indicate if buttons are clickable or not if conversation is on
 * @param {InterfaceSpaceData} props.event_space Current spaces data
 * @param {Function} props.setHoverSlider To update the user id for which badge is viewing
 * @param {Function} props.setShowDeviceSelector To update the device selector visibility
 * @param {Function} props.setAvailableMediaDevice To update the list of available devices list
 * @param {AvailableMediaDevices} props.availableMediaDevices To store the available devices list in system
 * @param {Function} props.setNoPreviewDiv To set the user badge popup preview state
 * @param {Function} props.updateConversationFullScreen To update the conversation full screen state
 * @param {Function} props.updateVideoMuteText To update the mute text to show user on hover of mute button
 * @param {ConversationMeta} props.conversationMeta Current conversation state from redux
 *
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
let ConversationSection = (props) => {
    const {t} = useTranslation('myBadgeBlock')
    // props data
    const {
        msg,
        modal,
        isMute,
        selfSeat,
        spaceHost,
        setIsMute,
        event_data,
        sliderOpen,
        togglePopup,
        buttonState,
        event_space,
        setHoverSlider,
        setShowDeviceSelector,
        setAvailableMediaDevice,
        availableMediaDevices,
        setNoPreviewDiv
    } = props;

    // auth current state
    const auth = useSelector((state) => {
        return _.get(state.NewInterface, `interfaceAuth`, [])
    });




    // current conversation users data
    const conversation_users = _.has(props.event_space, ['current_joined_conversation'])
        && props.event_space.current_joined_conversation !== null
        && props.event_space.current_joined_conversation.conversation_users.filter(
            (val) => !val.hasOwnProperty("is_self")
        );
    // allocation of seats for users
    videoElementRepo.allocateUsersSeat(conversation_users);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the conversation full screen state to redux so full screen can be toggled directly on
     * click
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleEnterFullScreen = () => {
        props.updateConversationFullScreen(!props.conversationMeta.fullScreen);
    };
    // const isSpaceHost = props.spaceHost[0].user_id == videoElementRepo.SEATS[i].userId;
    return (
        <>
            {
                props.conversationMeta.fullScreen
                    ?
                    <>
                        <FullScreenConversation
                            {...props}
                        />
                        {modal &&
                            <BadgePopUp2
                                alert={msg}
                                event_uuid={event_data.event_uuid}
                                togglePopup={togglePopup}
                                toggleReset={togglePopup}
                                modal={modal}
                            />
                        }
                    </>
                    :
                    <div>
                        {event_space.current_joined_conversation !== null &&
                            <div className="col-xs-12 col-md-12 px-0">
                                <div className="col-xs-12 col-md-12" />

                                <div className="col-xs-12 col-md-12 px-0">
                                    <div className="row" style={{position: "relative"}}>
                                        <IsolatedButton
                                            className={'private-btn'}
                                            askToPrivateConversation={askToPrivateConversation}
                                            is_conversation_private={
                                                props.event_space.current_joined_conversation.is_conversation_private
                                            }
                                        />
                                        {/* full screen button */}
                                        <div className='fullScreen_alternateBtn'>
                                            {/* <ReactTooltip type="dark" effect="solid" id='fullScreen_tooltip'/> */}
                                            {props.conversationMeta.fullScreen ?
                                                <div className={"fs-exit-btn"}>
                                                    <span> {t('popup:Exit Full Screen-u')}</span>
                                                    <ConverSationButton
                                                        dataTip={t(props.conversationMeta.fullScreen ? t('popup:Exit Full Screen-u') : 'Enter Full Screen')}
                                                        onClick={handleEnterFullScreen}
                                                        disabled={buttonState}
                                                        dataFor={"fullScreen_tooltip"}
                                                        icon={props.conversationMeta.fullScreen ? 'exit_full_screen' : 'enter_full_screen'}
                                                    />
                                                </div>
                                                :
                                                <ConverSationButton
                                                    dataTip={t(props.conversationMeta.fullScreen ? t('popup:Exit Full Screen-u') : 'Enter Full Screen')}
                                                    onClick={handleEnterFullScreen}
                                                    disabled={buttonState}
                                                    dataFor={"fullScreen_tooltip"}
                                                    icon={props.conversationMeta.fullScreen ? 'exit_full_screen' : 'enter_full_screen'}
                                                />
                                            }
                                        </div>
                                        <div className="col-md-3 col-sm-3 px-0">
                                            <div className="single-user-thumb no-texture first-person selfSimpleVideo">
                                                <SelfVideoTile
                                                    selfSeat={selfSeat}
                                                />
                                                <VideoUserName
                                                    user={{...props.event_space.current_joined_conversation.conversation_users.find(u => u.is_self), is_mute: isMute}}
                                                />
                                                <ConversationButton
                                                    alert={msg}
                                                    togglePopup={togglePopup}
                                                    isMute={isMute}
                                                    setIsMute={setIsMute}
                                                    buttonState={buttonState}
                                                    setShowDeviceSelector={setShowDeviceSelector}
                                                    setAvailableMediaDevice={setAvailableMediaDevice}
                                                    availableMediaDevices={availableMediaDevices}
                                                    setNoPreviewDiv={setNoPreviewDiv}
                                                    toggleMediaDevicePopup={props.toggleMediaDevicePopup}
                                                />
                                            </div>
                                        </div>


                                        {props.event_space.current_joined_conversation !== null
                                            && videoElementRepo.getSeats().map((seat, i) => {

                                                if (
                                                    checkSpaceHost(
                                                        props.event_space.current_joined_conversation,
                                                        spaceHost
                                                    )
                                                    &&
                                                    props.spaceHost[0].user_id == videoElementRepo.SEATS[i].userId
                                                ) {
                                                    return null;
                                                }
                                                if (auth.user_id != videoElementRepo.SEATS[i].userId) {
                                                    return (
                                                        <>
                                                            {videoElementRepo.SEATS[i].userId ?
                                                                <div
                                                                    className={`video-member-group col-sm-3 px-0 ${videoElementRepo.SEATS[i].userId ? 'col-md-3' : 'hidden'}`}>
                                                                    {/* other users video  */}
                                                                    <div className="clearfix">
                                                                        <div className=" mt-5 w-100 text-right">
                                                                            <div
                                                                                className={
                                                                                    `posi-relative other-users-badge 
                                                                            ${!_.isEmpty(checkDummyUser(
                                                                                        videoElementRepo.SEATS[i].userId
                                                                                    ))
                                                                                        ? 'dummy-user-convo'
                                                                                        : ''}
                                                                                    d-inline pull-left
                                                                                    ${videoElementRepo.SEATS[i].userId
                                                                                        ? ''
                                                                                        : 'hidden'
                                                                                    }`
                                                                                }>
                                                                                <OtherUserVideo
                                                                                    userId={videoElementRepo.SEATS[i].userId}
                                                                                    conversation_users={conversation_users}
                                                                                    tileState={
                                                                                        videoElementRepo.SEATS[i].tileState
                                                                                    }
                                                                                    i={i}
                                                                                />
                                                                                <VideoUserName
                                                                                    user={getUserData(conversation_users, videoElementRepo.SEATS[i].userId)}
                                                                                />
                                                                                <OtherBadgeButton
                                                                                    setHoverSlider={setHoverSlider}
                                                                                    userId={videoElementRepo.SEATS[i].userId}
                                                                                    conversation_users={conversation_users}
                                                                                    sliderOpen={sliderOpen}
                                                                                    isDummy={videoElementRepo.SEATS[i].isDummy}
                                                                                />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                :
                                                                ''}
                                                        </>
                                                    )
                                                }
                                            })
                                        }


                                    </div>
                                </div>
                            </div>
                        }
                        {modal &&
                            <BadgePopUp2
                                alert={msg.current}
                                event_uuid={event_data.event_uuid}
                                togglePopup={togglePopup}
                                toggleReset={togglePopup}
                                modal={modal}
                            />
                        }
                    </div>
            }
        </>


    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        updateConversationFullScreen: (data) => dispatch(newInterfaceActions.NewInterFace.setConversationFullScreen(data)),
        updateVideoMuteText: (data) => dispatch(newInterfaceActions.NewInterFace.updateVideoMuteText(data)),
    }
}

const mapStateToProps = (state) => {
    return {
        conversationMeta: state.NewInterface.conversationMeta,
        SelfData: state.NewInterface.interfaceBadgeData,
    };
};

ConversationSection = connect(mapStateToProps, mapDispatchToProps)(ConversationSection);
export default ConversationSection

