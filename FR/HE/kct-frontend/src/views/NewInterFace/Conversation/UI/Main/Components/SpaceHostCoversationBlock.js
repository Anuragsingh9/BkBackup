import React, {useState,useEffect} from 'react';
import {connect} from "react-redux";
import {useTranslation} from "react-i18next";
import ConvSHButtons from '../Common/ConvSHButtons.js';
import {
    askToPrivateConversation,
    checkDummyUser,
    checkSpaceHost,
    getUserData,
    leaveConversation,
} from '../../../Utils/Conversation.js';
import _ from 'lodash';
import newInterfaceActions from "../../../../../../redux/actions/newInterfaceAction";
import videoElementRepo from '../../../../../VideoMeeting/VideoElementRepository.js';
import BadgePopUp2 from '../../../../MyBadge/BadgePopup/BadgePopUp3.js';
import '../../../../HostSection/SpaceHost/SpaceHost.css';
import SelfVideoTile from '../Common/SelfVideoTile.js';
import FullScreenConversation from "./FullScreen/FullScreenConversation";
import OtherUserVideo from "../Common/OtherUserVideo";
import BanPopup from "../../../../HostVideoConference/BanPopup/BanPopup";
import './spacehostblock.css';
import VideoUserName from '../Common/VideoUserName/VideoUserName.js';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed for view self conversation to space host(system role).This component will
 * render when space host start conversation with other user.This component includes self(space host) video and some
 * conversation buttons:
 * 1.self badge editor - to update user's detail/profile
 * 2.mute/unmute - mute or unmute current conversation
 * 3. leave conversation - to leave current ongoing conversation
 * 4. setting - to open a popup in which user can manage device(mic/camera/speaker) settings
 * 5. Isolation button - To make conversation isolate(user can't break conversation from his side or no other user can
 * join conversation)
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
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const SpaceHostConversation = (props) => {
    // props data
    const {
        selfSeat,
        togglePopup,
        isMute,
        buttonState,
        modal,
        msg,
        event_space,
        event_data,
        setIsMute,
        spaceHostData,
        setShowDeviceSelector,
        setAvailableMediaDevice,
        availableMediaDevices,
        setNoPreviewDiv
    } = props;
    // current conversation users data
    const conversation_users = _.has(props.event_space, ['current_joined_conversation'])
        && props.event_space.current_joined_conversation !== null
        && props.event_space.current_joined_conversation.conversation_users.filter(
            (val) => !val.hasOwnProperty("is_self")
        );
    // allocation of seats for users
    videoElementRepo.allocateUsersSeat(conversation_users);
    const [banPopup, setBanPopup] = useState(false);
    const [banUserId, setBanUser] = useState(null);
    const {t} = useTranslation('myBadgeBlock')



    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will check the user(whether it is a dummy user or not) in conversation.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id Id of user to check if user is dummy or not
     * @return {Boolean}
     */
    const isDummy = (id) => {
        let value = false;
        conversation_users.filter((item) => {
            if (item.user_id == id && item.is_dummy == 1) {
                value = true;
            }
        });
        return value;

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function checks the user whether it is a dummy user or not, According to that it toggles the popup
     * or displays notification.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id Id of user to ban
     */
    const toggleBan = (id) => {
        if (id) {
            if (isDummy(id)) {
                msg.show && msg.show(t("Cannot ban dummy user"), {type: 'error'})
            } else {
                setBanUser(id)
                setBanPopup(true)
            }
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles ban popup toggle(open/close) while user is in conversation.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    const hideBanPopUp = () => {
        setBanUser(null);
        setBanPopup(false);
    }

    return (
        <>
            {banPopup && banUserId &&
                <BanPopup eventId={event_data.event_uuid}
                    toggleBan={hideBanPopUp}
                    banPopup={banPopup}
                    msg={msg}
                    user_id={banUserId}
                />}
            {
                props.conversationMeta.fullScreen
                    && props.spaceHostData[0]
                    && _.has(props.spaceHostData[0], ['user_id'])
                    && props.spaceHostData[0].user_id == props.event_badge.user_id
                    ?
                    <>
                        <FullScreenConversation
                            toggleBan={toggleBan}
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
                    <div className="profile-img bg-cover rounded-10 no-texture p-relative SHSelfVideoFrame">
                        {
                            props.spaceHostData[0]
                                && _.has(props.spaceHostData[0], ['user_id'])
                                && props.spaceHostData[0].user_id == props.event_badge.user_id ?
                                <React.Fragment>

                                    <SelfVideoTile
                                        selfSeat={selfSeat}
                                    />
                                    <VideoUserName
                                        user={{...event_space.current_joined_conversation.conversation_users.find(u => u.is_self), is_mute: isMute}}
                                    />
                                    <ConvSHButtons
                                        leaveConversation={leaveConversation}
                                        setIsMute={setIsMute}
                                        togglePopup={togglePopup}
                                        isMute={isMute}
                                        buttonState={buttonState}
                                        is_conversation_private={
                                            event_space.current_joined_conversation.is_conversation_private
                                        }
                                        askToPrivateConversation={askToPrivateConversation}
                                        toggleMediaDevicePopup={props.toggleMediaDevicePopup}
                                        setShowDeviceSelector={setShowDeviceSelector}
                                        setAvailableMediaDevice={setAvailableMediaDevice}
                                        availableMediaDevices={availableMediaDevices}
                                        setNoPreviewDiv={setNoPreviewDiv}
                                    />

                                </React.Fragment>

                                :

                                <React.Fragment>

                                    {videoElementRepo.getSeats().map((seat, i) => {
                                        if (
                                            checkSpaceHost(
                                                props.event_space.current_joined_conversation, spaceHostData
                                            )
                                            && props.spaceHostData[0].user_id == videoElementRepo.SEATS[i].userId
                                        ) {
                                            return (
                                                <div
                                                    className={`
                                            other-users-badge space-host-video 
                                            ${!_.isEmpty(checkDummyUser(videoElementRepo.SEATS[i].userId))
                                                            ? 'dummy-user-convo' : ''}
                                             d-inline pull-left 
                                             ${videoElementRepo.SEATS[i].userId ? '' : 'hidden'}`
                                                    }
                                                >
                                                    <div
                                                        className={`
                                                    member-user-thumbs no-texture h-100 w-100 bg-cover otherToSHVideo
                                                    `}
                                                    >
                                                        <OtherUserVideo
                                                            userId={videoElementRepo.SEATS[i].userId}
                                                            conversation_users={conversation_users}
                                                            tileState={videoElementRepo.SEATS[i].tileState}
                                                            i={i}
                                                        />
                                                        <VideoUserName
                                                            user={getUserData(conversation_users, videoElementRepo.SEATS[i].userId)}
                                                        />
                                                        <div
                                                            className="
                                                            video-control 
                                                            hosticons 
                                                            d-inline 
                                                            w-100 
                                                            pt-10 
                                                            text-center
                                                            "
                                                        >
                                                        </div>
                                                    </div>
                                                </div>
                                            )
                                        }

                                    })
                                    }

                                </React.Fragment>
                        }

                        {modal &&
                            <BadgePopUp2
                                alert={msg}
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
        SelfSHData: state.NewInterface.interfaceBadgeData,
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(SpaceHostConversation);
