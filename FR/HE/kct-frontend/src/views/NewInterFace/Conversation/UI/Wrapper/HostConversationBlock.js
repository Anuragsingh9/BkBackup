import React, {useRef, useState} from 'react';
import CallingStatus from '../../../VideoConference/CallingStatus/CallingStatus.js';
import ConvSHButtons from '../Main/Components/SpaceHostOtherButtons';
import videoElementRepo from '../../../../VideoMeeting/VideoElementRepository.js';
import OtherUserVideo from '../Main/Common/OtherUserVideo.js';
import Helper from '../../../../../Helper.js';
import {Provider as AlertContainer, useAlert } from 'react-alert';
import '../../../VideoConference/VideoConference.css';
import ReactTooltip from 'react-tooltip';
import {useTranslation} from 'react-i18next';
import _ from 'lodash';

import {getData, getUserData, removeUser} from './../../Utils/Conversation.js';
import Slider from '../../../MyBadge/BadgeSideComponent/NewSlider.js';
import BanPopup from '../../../HostVideoConference/BanPopup/BanPopup.js';
import {connect, useDispatch} from 'react-redux';
import newInterfaceActions from "../../../../../redux/actions/newInterfaceAction";
import VideoUserName from '../Main/Common/VideoUserName/VideoUserName.js';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render other user video to space host in the conversation.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {ConversationMeta} props.conversationMeta Current conversation state from redux
 * @param {Function} props.updateConversationFullScreen  To toggle the current conversation full screen mode
 * @param {Function} props.updateVideoMuteText To update the conversation mute state and text related to it
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
let HostConversationBlock = (props) => {
    // dispatch hook 
    const dispacth = useDispatch();
    // alert ref
    const msg = useAlert();
    // button state
    const [buttonState, setButtonState] = useState(false);
    // ban user popup display state
    const [banPopup, setBanPopup] = useState(false);
    // ban user id state
    const [banUserId, setBanUser] = useState(null);
    // slider component state
    const [sliderOpen, setHoverSlider] = useState(false)
    // event data current state
    const event_data = dispacth(getData('interfaceEventData', {}));
    // auth data
    const auth = dispacth(getData('interfaceAuth', {}));

    const {t} = useTranslation('myBadgeBlock')

    const conversation_users = _.has(props.event_space, ['current_joined_conversation'])
        && props.event_space.current_joined_conversation !== null
        && props.event_space.current_joined_conversation.conversation_users.filter((val) => !val.hasOwnProperty("is_self"));

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

    videoElementRepo.allocateUsersSeat(conversation_users);

    return (
        <>
            {!props.conversationMeta.fullScreen
                &&
                <div className="video-meeting">

                    <ReactTooltip type="dark" effect="solid" id='conversation' />
                    {(props.makingCall) && <CallingStatus show={props.makingCall} />}
                    <AlertContainer ref={msg}{...Helper.alertOptions} />

                    <div className="row">
                        <div className="col-xs-12 col-md-12 px-0">
                            <div className="col-xs-12 col-md-12">
                            </div>
                            <div className="col-xs-12 col-md-12 px-0">

                                <div className="video-member-group host-view-members col-md-12 pl-0 pr-0 text-center">
                                    <div className="clearfix">
                                        <div className=" mt-5 w-100 text-center">

                                            {props.event_space.current_joined_conversation !== null && videoElementRepo.getSeats().map((seat, i) => {
                                                if (auth.user_id != videoElementRepo.SEATS[i].userId) {
                                                    return (
                                                        <div
                                                            className={`other-users-badge d-inline ${videoElementRepo.SEATS[i].userId ? '' : 'hidden'}`}>
                                                            <OtherUserVideo
                                                                i={i}
                                                                userId={videoElementRepo.SEATS[i].userId}
                                                                conversation_users={conversation_users}
                                                                tileState={videoElementRepo.SEATS[i].tileState}
                                                            />
                                                            <VideoUserName
                                                                user={getUserData(conversation_users, videoElementRepo.SEATS[i].userId)}
                                                            />

                                                            <ConvSHButtons
                                                                setHoverSlider={setHoverSlider}
                                                                removeUser={(e, cb) => {
                                                                    dispacth(removeUser(e, cb, videoElementRepo.SEATS[i].isDummy))
                                                                }}
                                                                toggleBan={toggleBan}
                                                                buttonState={buttonState}
                                                                userId={videoElementRepo.SEATS[i].userId}
                                                            />
                                                            {banPopup && banUserId &&
                                                                <BanPopup eventId={event_data.event_uuid}
                                                                    toggleBan={hideBanPopUp}
                                                                    banPopup={banPopup}
                                                                    msg={msg}
                                                                    user_id={banUserId}
                                                                />}
                                                            {(sliderOpen === videoElementRepo.SEATS[i].userId) &&
                                                                <div
                                                                    className="other-user-badge-popup"
                                                                >
                                                                    <Slider onBlur={() => {
                                                                        setHoverSlider(false)
                                                                    }}
                                                                        item={getUserData(conversation_users, videoElementRepo.SEATS[i].userId)} />
                                                                </div>
                                                            }
                                                        </div>
                                                    )

                                                }

                                            })
                                            }
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
        event_space: state.NewInterface.interfaceSpacesData,
        callJoin: state.NewInterface.callJoinState,
        makingCall: state.NewInterface.makingCall,
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(HostConversationBlock);
