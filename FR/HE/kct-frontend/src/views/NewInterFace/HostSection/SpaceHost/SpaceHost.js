import React, {useEffect, useRef, useState} from 'react';
import {connect, useDispatch} from 'react-redux';
import _ from 'lodash';
import BadgePopUp2 from '../../MyBadge/BadgePopup/BadgePopUp2';
import Helper from '../../../../../src/Helper';
import socketManager from '../../../../socket/socketManager';
import {KeepContact as KCT} from '../../../../redux/types';
import eventActions from '../../../../redux/actions/eventActions';
import * as videoMeeting from '../../../../views/Index/VideoConference/VideoMeeting/VideoMeeting';
import * as videoElementRepo from '../../../../views/Index/VideoConference/VideoMeeting/VideoElementRepository';
import newInterfaceActions from '../../../../redux/actions/newInterface/index';
import Svg from "../../../../Svg";
import "./SpaceHost.css";
import {Provider as AlertContainer } from 'react-alert';
import {useTranslation} from 'react-i18next';
import ReactTooltip from 'react-tooltip';
import Constants from "../../../../Constants";

let outerProps;

/**
 * @deprecated
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component renders SpaceHost Image/Video and conference section.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {UserBadge} props.spaceHostData Space host data in form of user badge
 * @param {InterfaceSpaceData} props.spaceData Spaces data including conversations from redux store
 * @param {UserBadge} props.event_badge Redux store mapped variable for holding user badge data
 * @param {EventData} props.event_data Redux store state variable to provide the event data
 * @class
 * @component
 * @constructor
 */
const SpaceHost = (props) => {

    outerProps = props;

    const {t} = useTranslation('spaceHost');
    const msg = useAlert();
    const {event_image, embedded_url, conference_type} = props.event_data;
    const {spaceHostData, spaceData} = props;
    const spaceHost = spaceHostData[0];
    const hostAvailibility = spaceData.hostAvailibility;
    const alertRef = useAlert();
    if (_.isEmpty(spaceHostData)) {
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method prepares data and handles socket call for ask to join call(request to add user in
     * conversation) and handles the response accordingly.
     *
     * Currently there can be maximum of 4 users and 1 space host in a conversation.
     *
     * If requested user is already in a conversation then user need to disconnect from current conversation
     * to join the new conversation.
     * -----------------------------------------------------------------------------------------------------------------
     *
     */
    const askToJoinCall = () => {

        const {conversation_users} = spaceData.current_joined_conversation;
        if (!_.isEmpty(spaceHost) && spaceHost.length == 1 && conversation_users && !_.isEmpty(conversation_users)
            && conversation_users.length < props.event_data?.event_conv_limit) {
            const user = spaceHost.fname;
            const userId = spaceHost.id;
            props.setCalledUserId(userId);
            const {conversation_uuid} = spaceData.current_joined_conversation;
            const socket_data = {
                conversation_id: conversation_uuid,
                space_id: spaceData.current_joined_space.space_uuid,
                event_id: props.event_data.event_uuid,
                target_user_id: userId.toString(),
                inviter_id: props.event_badge.user_id,
                is_dummy_user: _.has(user, ['is_dummy']) ? user.is_dummy : 0,
            };

            socketManager.emitEvent.ASK_JOIN_CONVERSATION(socket_data);

            if (!_.has(user, ['is_dummy'])) {
                checkInviteSucess(user.user_id);
            }

        } else {
            props.alert.show("Please disconnect current conversation !!", {type: 'error'})
        }

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description - This method is used to check the invite status in every 2 sec interval. and handles calls off or
     *  exist from the call in 2 sec if conversation is not started .
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} id Id of the user to check for the invite status
     */
    const checkInviteStatus = (id) => {
        let successInterval = setInterval(() => {
            if (spaceData.current_joined_conversation !== null) {
                const {conversation_users} = props.event_space.current_joined_conversation;
                if (!_.isEmpty(conversation_users)) {
                    const exist = conversation_users.filter((item) => (item.user_id == id))
                    if (!_.isEmpty(exist)) {
                        clearTimeout(statusInterval);
                        props.callOff();
                        clearInterval(successInterval);
                    }
                } else {
                    props.callOff();
                }
            } else {
                props.callOff();
            }
        }, 2000);
    }
    var statusInterval;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description - This method is used to check the invite success in 65 sec interval. and handles calls off or
     *  exist from the call in 2 sec if conversation is not started .
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} id Id of the user to check for the invite status
     */
    const checkInviteSucess = (id) => {
        props.callTrigger();
        checkInviteStatus(id);
        statusInterval = setTimeout(() => {
            if (props.event_space.current_joined_conversation !== null) {
                const {conversation_users} = props.event_space.current_joined_conversation;
                if (!_.isEmpty(conversation_users)) {
                    const exist = conversation_users.filter((item) => (item.user_id == id))
                    if (_.isEmpty(exist)) {
                        alertRef.show(t("Participant is not available now"));
                        props.callOff();
                    }

                } else {
                    props.callOff();
                }
            } else {
                props.callOff();
            }
        }, 65000)
    }

    const leaveConversation = (callback) => {
        if (spaceData.current_joined_conversation === null) {
            return null;
        }
        callback(true);
        const formData = new FormData()
        formData.append('conversation_uuid', spaceData.current_joined_conversation.conversation_uuid)
        formData.append('_method', 'DELETE')
        try {
            props.leaveConversation(formData)
                .then((res) => {
                    callback(false);
                    if (res.status) {
                        msg.show(Helper.alertMsg.FLASH_MSG_REC_UPDATE_1, {
                            type: "success",
                        });
                        if (res.data.data) {
                            props.deleteConversation(spaceData.current_joined_conversation)
                            const authId = spaceData.current_joined_conversation.conversation_users.find((user) => {
                                return user.hasOwnProperty('is_self');
                            })
                            const data = {
                                conversationId: spaceData.current_joined_conversation.conversation_uuid,
                                userId: authId.user_id,
                                type: res.data.data === true ? 'delete' : 'remove',
                            }
                            socketManager.emitEvent.CONVERSATION_LEAVE(data);
                            videoElementRepo.resetSeats();
                            videoMeeting.stopVideo();
                        }
                    }
                })
                .catch((err) => {
                    msg && msg.show(Helper.alertMsg.FLASH_MSG_REC_ADD_0, {type: "error"});
                    callback(false);
                });
        } catch (err) {
            msg && msg.show(Helper.handleError, {type: "error"});
            callback(false);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To allocate the HTMLVideo element to provided user id
     * from video meeting factory if user already have element assigned that will be returned
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} userId Id of the user to allocate the element
     */
    const provideVideoElementToUser = (userId) => {
        // return occupiedVideoElement[userId].videoElement;
        // outerProps.addUserToActiveCon(userId);
        outerProps.addUserToActiveCon(userId);
        const i = videoElementRepo.getUserIndex(userId);
        if (i != -1) {
            return document.getElementById(`other-video${i}`);
        } else {
            return null;
        }
    }


    return (
        <section className="host-section">
            <AlertContainer ref={msg}{...Helper.alertOptions} />
            <div className="container">
                <div className="row">
                    {/* <div className="col-md-4 col-sm-4"></div> */}
                    <div className="col-md-4 col-sm-4 main-host ">
                        <div className="text-center position-relative host-outer kct-customization">
                            {spaceData.current_joined_conversation != null ?
                                <HostVideoBlock {...props} provideVideoElementToUser={provideVideoElementToUser}
                                                leaveConversation={leaveConversation} msg={msg}
                                                event_space={spaceData} />
                                :
                                <React.Fragment>
                                    <img className="img-fluid hostphoto no-texture" src={spaceHost.avatar} alt="" />
                                    <h6>{spaceHost.fname}</h6>
                                </React.Fragment>
                            }
                            {
                                hostAvailibility || 1 ?
                                    spaceData.current_joined_conversation != null ?
                                        (_.has(spaceData.current_joined_conversation.conversation_users,
                                            ['is_self']) || 1) ?
                                            <SpaceHostButtonBlock {...props} leaveConversation={leaveConversation} />
                                            :
                                            <SpaceHostOtherViewButtonBlock {...props} />
                                        :
                                        ''
                                    :
                                    <span>
                                        <p>{t("Host is not currently available")}</p>
                                        <ul className="d-inline-block host-left-icon">
                                            <li className="mb-2 host-reception">
                                                <button type="button"
                                                        className="control-button video-buttons"
                                                        data-for='conversation'
                                                        onClick={askToJoinCall}
                                                    // data-tip={KCTLocales.TOOL_TIPS.CALL_HOST}
                                                    // dangerouslySetInnerHTML={{__html:  Svg.ICON.reception }}
                                                >
                                                    <span className="svgicon no-texture"
                                                          dangerouslySetInnerHTML={{__html: Svg.ICON.reception}}></span>
                                                </button>
                                            </li>
                                        </ul>
                                    </span>
                            }
                        </div>

                        {/* <div className="bring-down" style={(!openPlayer && event_during) ? { backgroundColor: '#E75480' } : {}}>
                            <span >{openPlayer ? '^' : '^'}</span>
                        </div> */}
                    </div>
                    {/* <div className="col-md-4"></div> */}
                </div>
            </div>

        </section>
    )
}

/**
 * @deprecated
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.privateConversation To make the conversation private
 * @returns {JSX.Element}
 * @constructor
 */
const SpaceHostButtonBlock = (props) => {
    const [modal, setModal] = useState(false);
    const [buttonState, setButtonState] = useState(false);
    const [isMute, setIsMute] = useState(0);
    const {spaceData, event_data, event_badge} = props;
    const askToPrivateConversation = () => {
        if (spaceData.current_joined_conversation === null) {
            return null;
        }
        const formData = new FormData()
        const conversation_uuid = spaceData.current_joined_conversation.conversation_uuid
        formData.append('conversation_uuid', conversation_uuid);
        if (spaceData.current_joined_conversation.is_conversation_private == 0) {
            formData.append('is_private', 1);
        } else {
            formData.append('is_private', 0);
        }
        try {
            props.privateConversation(formData).then((res) => {
                socketManager.emitEvent.PRIVATE_CONVERSATION({
                    namespace: Helper.getNameSpace(),
                    spaceId: spaceData.current_joined_space.space_uuid,
                    eventId: event_data.event_uuid,
                    conversationId: conversation_uuid,
                    is_private: res.data.data.is_conversation_private,
                    senderId: event_badge.user_id,
                })
            });

        } catch (e) {
            console.error(e);
        }
    }
    //To hanlde badgePoppup
    const togglePopup = () => {
        setModal(!modal);
    }
    //To handle leaveConversation

    //Handle mute
    const handleMute = () => {
        if (videoMeeting.getCurrentMuteState() === false) {
            videoMeeting.toggleMute(1);
            setIsMute(1);
        } else {
            videoMeeting.toggleMute(0);
            setIsMute(0);
        }
    }


    return (
        <div className="conversation-control video-control  d-inline w-100 pt-5 text-center">
            <button type="button"
                    className="btn btn-white no-texture video-buttons"
                    onClick={togglePopup}
                    dangerouslySetInnerHTML={{__html: Svg.ICON.badge_icon}}>
            </button>
            <button type="button"
                    className="btn btn-white no-texture video-buttons mic-btn"
                    onClick={handleMute}
                    dangerouslySetInnerHTML={{__html: !isMute ? Svg.ICON.microphone : Svg.ICON.mic}}>

            </button>
            <button type="button"
                    className="btn btn-white no-texture video-buttons exit-btn"
                    onClick={() => {
                        props.leaveConversation(setButtonState)
                    }}
                    disabled={buttonState}
                    dangerouslySetInnerHTML={{__html: Svg.ICON.exit}}>
            </button>
            <button className="host-private-btn no-texture video-buttons btn" type="button"
                    onClick={askToPrivateConversation}
                    disabled={buttonState}
            >
                <ReactTooltip type="dark" effect="solid" id='isolation_btn' />
                <span
                    className={props.spaceData.current_joined_conversation.is_conversation_private
                        ? "grey-private-btn" : "private-btn-color2"}
                    data-for='isolation_btn' data-tip={t("tooltip")}>
                    <span className="svgicon" dangerouslySetInnerHTML={{__html: Svg.ICON.private_btn}}></span>

                </span>
            </button>
            {modal &&
            <BadgePopUp2 togglePopup={togglePopup} event_uuid={event_data.event_uuid} toggleReset={togglePopup}
                         modal={modal} />}
        </div>
    )

}

/**
 * @deprecated
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.privateConversation To make the conversation private
 * @returns {JSX.Element}
 * @constructor
 */
const SpaceHostOtherViewButtonBlock = (props) => {
    const [showMenu, set_showmenu] = useState(-1);
    const closemenu = (event) => {
        return set_showmenu(-1, [
            document.removeEventListener("click", closemenu),
        ])
    };
    const showmenu = (event, index) => {
        event.preventDefault();
        return showMenu < 0
            ? set_showmenu(index, [document.addEventListener("click", closemenu)],)
            : "";
    };
    return (
        <div className="conversation-control w-100 d-inline mt-5">
            <button type="button"
                    className="btn btn-white no-texture"
                    onClick={(e) => showmenu(e, 1)}//userIndexInConversationList=1
                    dangerouslySetInnerHTML={{__html: Svg.ICON.badge_icon}}>
            </button>
        </div>
    )
}

/**
 * @deprecated
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.privateConversation To make the conversation private
 * @returns {JSX.Element}
 * @constructor
 */
const HostVideoBlock = (props) => {

    const [selfSeat, setSelfSeat] = useState({tileState: null});

    let dispatch = useDispatch();

    useEffect(() => {
        if (props.event_space.current_joined_conversation) {
            startVideoConversation(props.event_space.current_joined_conversation);
        }
    }, []);

    const startVideoConversation = (conversation) => {
        const elements = {
            selfVideoElement: document.getElementById("self-video"),
            selfAudioElement: document.getElementById('self-audio')
        }
        const meeting = conversation.meeting;
        const additional = {
            states: {
                selfSeat: {
                    get: selfSeat,
                    set: setSelfSeat
                }
            },
            handlers: {
                provideVideoElementToUser: props.provideVideoElementToUser,
                conversationErrorHandler: conversationErrorHandler
            },
            devices: {
                audioInput: localStorage.getItem("user_audio"),
                videoInput: localStorage.getItem("user_video"),
                audioOutput: localStorage.getItem('user_audio_o'),
            },
            dispatch,
        }

        videoMeeting.start(meeting, elements, additional)
            .then((res) => {
                if (res) {
                }
            })
            .catch((err) => {
                conversationErrorHandler(err);
            })
    }
    const nothing = () => {

    }

    const conversationErrorHandler = (err) => {
        props.msg && props.msg.show("Some error occurred in starting video Conversation",
            {type: "error"});
        props.leaveConversation(nothing);
    }

    return (
        <div className="profile-img bg-cover rounded-10 no-texture p-relative">
            {
                (selfSeat.tileState === null || selfSeat.tileState.active !== true)
                && <Helper.pageLoading />
            }
            <video className="first-person-video  first-person-thumb no-texture" id="self-video"></video>

            <audio id="self-audio"></audio>
            <div className="online-circle"></div>
        </div>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        leaveConversation: (id) => dispatch(eventActions.Event.leaveConversation(id)),
        deleteConversation: (id) => dispatch({type: KCT.EVENT.DELETE_CONVERSATION, payload: id}),
        addMemberConversation: (data, index) => dispatch({
            type: KCT.NEW_INTERFACE.ADD_EVENT_MEMBER_CONVERSATIONS,
            payload: data,
            userId: index
        }),
        addUserToActiveCon: (userId) => dispatch({
            type: KCT.NEW_INTERFACE.ADD_USER_TO_ACTIVE_CONVERSATIONS,
            payload: userId
        }),
        changeConversationId: (id) => dispatch({type: KCT.NEW_INTERFACE.CHANGE_CONVERSATION_IDS, payload: id}),
        callOff: () => dispatch(newInterfaceActions.NewInterFace.callOff()),
        callTrigger: () => dispatch(newInterfaceActions.NewInterFace.callTrigger()),
        setCalledUserId: (id) => dispatch(newInterfaceActions.NewInterFace.setCalledUserId(id)),
        privateConversation: (id) => dispatch(eventActions.Event.privateConversation(id)),
    }
}

const mapStateToProps = (state) => {
    return {
        spaceHostData: state.NewInterface.interfaceSpaceHostData,
        spaceData: state.NewInterface.interfaceSpacesData,
        event_badge: state.NewInterface.interfaceBadgeData,
        event_data: state.NewInterface.interfaceEventData,

    }
}

export default connect(mapStateToProps, mapDispatchToProps)(SpaceHost);
