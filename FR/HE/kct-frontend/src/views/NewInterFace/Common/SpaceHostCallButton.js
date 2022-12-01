import React, {useEffect, useRef, useState} from 'react';
import _ from 'lodash';
import {connect} from "react-redux";
import socketManager from "../../../socket/socketManager";
import newInterfaceActions from "../../../redux/actions/newInterfaceAction";
import Svg from "../../../Svg";
import ReactTooltip from "react-tooltip";
import {useTranslation} from "react-i18next";
import Constants from "../../../Constants";
import Helper from "../../../Helper";
import "./SpaceHostCallButton.css"
import {useAlert} from 'react-alert';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to call space host(system role). This will show a request popup to space
 * host side and once he accept the invitation then socket will trigger and conversation will start with space host.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Label[]} props.event_labels All the event labels with different locales
 * @param {UserBadge} props.event_badge User badge details
 * @param {Boolean} props.isHostAvailable To indicate if host is online or not
 * @param {InterfaceSpaceData} props.spaceData Spaces data including conversations from redux store
 * @param {UserBadge} props.spaceHostData Space host data in form of user badge
 * @param {Function} props.callOff To show or hide the calling popup when calling to other user
 * @param {Function} props.callTrigger To call the api for joining conversation
 * @param {Function} props.setCalledUserId To update the caller id to display the calling popup
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const SpaceHostCallButton = (props) => {
    const {
        event_labels,
        event_badge,
    } = props;
    const spaceHost = props.spaceHostData[0];
    // to indicate if current user is space host or not
    const isCurrentUserSpaceHost = !_.isEmpty(spaceHost)
        && spaceHost.user_id === event_badge.user_id;
    const [hostStatus, setHostStatus] = useState(Constants.hostStatus.HOST_OFFLINE);
    const [tooltipMessage, setTooltipMessage] = useState(null);
    const [canCallSpaceHost, setCanCallSpaceHost] = useState(!isCurrentUserSpaceHost && props.isHostAvailable);
    const msg = useAlert();
    const {t} = useTranslation('spaceHost');
    const showAlert = (data, options = {}) => {
        props.alert && props.alert.show(data, options);
    }


    useEffect(() => {
        making.current = props.makingCall;
        // showAlert(t("Participant is not available now"));
    }, [props.makingCall]);

    let making = useRef(props.makingCall);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * This use effect will check if host is online of not if host is offline -> mark host as OFFLINE else it
     * will check if host is in conversation or not if not in conversation then -> mark as ONLINE ONLY else it will
     * check if host is in same conversation or not if in same conversation -> mark as IN SAME CONVERSATION  else mark
     * as HOST IS IN CONVERSATION
     * -----------------------------------------------------------------------------------------------------------------
     */
    useEffect(() => {
        let conversations = props.spaceData.current_space_conversations;
        // checking if conversations are there and host is online or not
        if (conversations && props.isHostAvailable) {
            // host is online
            let hostConversation = null;
            conversations.forEach(conversation => {
                if (conversation && conversation.conversation_users && conversation.conversation_uuid) {
                    // conversation is valid
                    conversation.conversation_users.forEach((user => {
                        if (user && spaceHost && user.user_id === spaceHost.user_id) {
                            hostConversation = conversation;
                            // host is in conversation
                            let currentConversation = props.spaceData.current_joined_conversation
                            if (currentConversation
                                && currentConversation.conversation_uuid === conversation.conversation_uuid) {
                                // host and current user are in same conversation
                                setHostStatus(Constants.hostStatus.HOST_IN_SAME_CONVERSATION);
                            } else {
                                // host and user are in different conversation
                                setHostStatus(Constants.hostStatus.HOST_IN_CONVERSATION);
                            }
                        }
                    }))
                }
            });
            if (!hostConversation) {
                // host is not in conversation
                setHostStatus(Constants.hostStatus.HOST_ONLINE);
            }
        } else {
            // host is offline
            setHostStatus(Constants.hostStatus.HOST_OFFLINE);
        }
    }, [props.spaceData.current_space_conversations, props.spaceHostData]);


    useEffect(() => {
        if (hostStatus === Constants.hostStatus.HOST_OFFLINE) {
            setCanCallSpaceHost(false);
            setTooltipMessage(`${!_.isEmpty(event_labels)
                ? Helper.getLabel("space_host", event_labels)
                : "Space Host"} ${t("Host is not currently available")}`);

        } else if (hostStatus === Constants.hostStatus.HOST_ONLINE) {
            setCanCallSpaceHost(true);
            setTooltipMessage(t("Call spaceHost"))
        } else if (hostStatus === Constants.hostStatus.HOST_IN_CONVERSATION) {
            setCanCallSpaceHost(false);
            setTooltipMessage(`${!_.isEmpty(event_labels)
                ? Helper.getLabel("space_host", event_labels)
                : "Space Host"} ${t("Host in another call")}`);

        }
    }, [hostStatus]);

    let statusInterval;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will check the status of invitation of space host for join a call.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id Id of the user to check for the invite status
     */
    const checkInviteStatus = (id) => {
        let successInterval = setInterval(() => {
            if (props.spaceData.current_joined_conversation !== null) {
                const {conversation_users} = props.spaceData.current_joined_conversation;
                const {spaceHostData} = props;
                if (!_.isEmpty(spaceHostData) && !_.isEmpty(conversation_users)) {
                    const exist = conversation_users.filter((item) => (item.user_id == id))
                    if (!_.isEmpty(exist)) {
                        clearTimeout(statusInterval);
                        props.callOff();
                        clearInterval(successInterval);
                    }
                } else {
                    // props.callOff();
                }
            } else {
                // props.callOff();
            }
        }, 2000);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will check the status of request and make sure that space host is joined the
     * conversation or not.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id Id of the user to check for the invite success check
     */
    const checkCallSuccess = (id) => {
        props.callTrigger();
        checkInviteStatus(id);
        statusInterval = setTimeout(() => {
            if (props.spaceData.current_joined_conversation !== null) {
                const {conversation_users} = props.spaceData.current_joined_conversation;
                const {spaceHostData} = props;
                if (!_.isEmpty(spaceHostData)) {
                    const exist = conversation_users.filter((item) => (item.user_id == id))
                    if (_.isEmpty(exist) && !_.isEmpty(conversation_users)) {
                        if (making.current) {
                            showAlert(t("Participant is not available now"));
                        }
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

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to start conversation with space host and see space host data(
     * video audio) to other users(who will be in conversation with space host).Once the API call excuted successfully
     * it will start conversation other wise throw an error.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const askHostToJoinCall = () => {
        if (!canCallSpaceHost) {
            showAlert("Space Host not available", {type: 'error'});
            return;
        }
        const {spaceData, spaceHostData} = props;
        if (!_.isEmpty(spaceHostData)) {
            const user = spaceHostData[0];
            props.setCalledUserId(user.user_id);
            const socket_data = {
                conversation_id: spaceData.current_joined_conversation
                    ? spaceData.current_joined_conversation.conversation_uuid
                    : '',
                space_id: spaceData.current_joined_space.space_uuid,
                event_id: props.event_data.event_uuid,
                target_user_id: user.user_id.toString(),
                inviter_id: props.event_badge.user_id,
                is_dummy_user: _.has(user, ['is_dummy']) ? user.is_dummy : 0,
            };
            socketManager.emitEvent.ASK_JOIN_CONVERSATION(socket_data);
            if (!_.has(user, ['is_dummy'])) {
                checkCallSuccess(user.user_id);
            }
        } else {
            showAlert("Space Host not available", {type: 'error'})
        }
    }


    return (
        <>
            {
                (
                    !isCurrentUserSpaceHost
                    && hostStatus !== Constants.hostStatus.HOST_IN_SAME_CONVERSATION
                    && hostStatus !== Constants.hostStatus.HOST_OFFLINE) &&

                <button
                    className="no-texture audioVideoBtn control-button video-buttons"
                    data-for='call_sh'
                    disabled={!canCallSpaceHost}
                    onClick={askHostToJoinCall}
                    data-tip={tooltipMessage}
                >
                    <span
                        // className="svgicon no-texture"
                        dangerouslySetInnerHTML={{__html: Svg.ICON.reception}}
                    />

                </button>
            }
            {
                canCallSpaceHost &&
                <ReactTooltip type="dark" effect="solid" id='call_sh' />
            }
        </>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        callOff: () => dispatch(newInterfaceActions.NewInterFace.callOff()),
        callTrigger: () => dispatch(newInterfaceActions.NewInterFace.callTrigger()),
        setCalledUserId: (id) => dispatch(newInterfaceActions.NewInterFace.setCalledUserId(id)),
    }
}

const mapStateToProps = state => {
    return {
        event_labels: state.page_Customization.initData.labels,
        event_data: state.NewInterface.interfaceEventData,
        event_badge: state.NewInterface.interfaceBadgeData,
        spaceHostData: state.NewInterface.interfaceSpaceHostData,
        spaceData: state.NewInterface.interfaceSpacesData,
        isHostAvailable: state.NewInterface.availabilityHost,
        makingCall: state.NewInterface.makingCall,
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(SpaceHostCallButton);

