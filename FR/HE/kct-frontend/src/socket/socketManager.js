import React from "react";
import socketIoClient from 'socket.io-client';
import _ from 'lodash';
import Helper from '../Helper.js';
import Constants from "../Constants";
import ZoomMtgEmbedded from "@zoomus/websdk/embedded";
import VideoElementRepository from "../views/VideoMeeting/VideoElementRepository";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file provides the socket related methods
 * Here all the socket events for both listening and emitting are stored, the props is supposed to contain the methods
 * for handling the different events.
 * the props should contain the getter and setter if latest data needs to be fetch else the value at initial time
 * will be passed which will be not updated along with redux state update
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @module socketManager
 */

/**
 * @type {Object}
 * @property {String} USER_CONNECT Socket event for USER_CONNECT
 * @property {String} UPDATE_DASHBOARD Socket event for UPDATE_DASHBOARD
 * @property {String} CONVERSATION_CREATE Socket event for CONVERSATION_CREATE
 * @property {String} CONVERSATION_JOIN Socket event for CONVERSATION_JOIN
 * @property {String} USER_DISCONNECT Socket event for USER_DISCONNECT
 * @property {String} CONVERSATION_LEAVE Socket event for CONVERSATION_LEAVE
 * @property {String} SPACE_USER_COUNT_CHANGE Socket event for SPACE_USER_COUNT_CHANGE
 * @property {String} EVENT_MANUALLY_OPEN Socket event for EVENT_MANUALLY_OPEN
 * @property {String} PROFILE_UPDATE Socket event for PROFILE_UPDATE
 * @property {String} ASK_JOIN_CONVERSATION Socket event for ASK_JOIN_CONVERSATION
 * @property {String} PRIVATE_CONVERSATION Socket event for PRIVATE_CONVERSATION
 * @property {String} BANNED_USER Socket event for BANNED_USER
 * @property {String} REMOVED_USER Socket event for REMOVED_USER
 * @property {String} CONFERECE_UPDATE Socket event for CONFERECE_UPDATE
 * @property {String} PILOT_UPDATE_NETWORKING Socket event for PILOT_UPDATE_NETWORKING
 * @property {String} PILOT_UPDATE_CONTENT Socket event for PILOT_UPDATE_CONTENT
 * @property {String} PILOT_UPDATE_CNT_MNGM Socket event for PILOT_UPDATE_CNT_MNGM
 * @property {String} CALL_ACKNOWLEDGEMENT Socket event for CALL_ACKNOWLEDGEMENT
 * @property {String} ZOOM_PARTICIPANT_JOINED Socket event for ZOOM_PARTICIPANT_JOINED
 */
const listeningEvents = {
    USER_CONNECT: 'kct-user-connect',
    UPDATE_DASHBOARD: 'kct-update-dashboard',
    CONVERSATION_CREATE: 'kct-conversation-create',
    CONVERSATION_JOIN: 'kct-conversation-join',
    USER_DISCONNECT: 'kct-user-disconnect',
    CONVERSATION_LEAVE: 'kct-conversation-leave',
    SPACE_USER_COUNT_CHANGE: 'kct-space-count',
    EVENT_MANUALLY_OPEN: 'kct-event-manually-open',
    PROFILE_UPDATE: 'kct-profile-update',
    ASK_JOIN_CONVERSATION: 'ask-join-conversation',
    PRIVATE_CONVERSATION: 'kct-conversation-type-update',
    BANNED_USER: "kct-sh-banned-user",
    REMOVED_USER: "kct-sh-removed-user",
    CONFERECE_UPDATE: 'kct-conference-state-updated',
    PILOT_UPDATE_NETWORKING: 'kct-pilot-update-networking',
    PILOT_UPDATE_CONTENT: 'kct-pilot-update-content',
    PILOT_UPDATE_CNT_MNGM: 'kct-pilot-update-cnt-mgmt',
    CALL_ACKNOWLEDGEMENT: 'kct-call-ack',
    ZOOM_PARTICIPANT_JOINED: 'kct-zoom-u-joined',
    EVENT_DATA_UPDATED: 'kct-event-data-updated',
}

/**
 *
 * @type {Object}
 * @property {String} USER_CONNECT Socket emitting event for USER_CONNECT
 * @property {String} CONVERSATION_CREATE Socket emitting event for CONVERSATION_CREATE
 * @property {String} CONVERSATION_JOIN Socket emitting event for CONVERSATION_JOIN
 * @property {String} CONVERSATION_LEAVE Socket emitting event for CONVERSATION_LEAVE
 * @property {String} SPACE_CHANGE Socket emitting event for SPACE_CHANGE
 * @property {String} UPDATE_DASHBOARD Socket emitting event for UPDATE_DASHBOARD
 * @property {String} PROFILE_UPDATE Socket emitting event for PROFILE_UPDATE
 * @property {String} ASK_JOIN_CONVERSATION Socket emitting event for ASK_JOIN_CONVERSATION
 * @property {String} PRIVATE_CONVERSATION Socket emitting event for PRIVATE_CONVERSATION
 * @property {String} BANNED_USER Socket emitting event for BANNED_USER
 * @property {String} REMOVED_USER Socket emitting event for REMOVED_USER
 * @property {String} CONFERECE_UPDATE Socket emitting event for CONFERECE_UPDATE
 * @property {String} PILOT_UPDATE_NETWORKING Socket emitting event for PILOT_UPDATE_NETWORKING
 * @property {String} PILOT_UPDATE_CONTENT Socket emitting event for PILOT_UPDATE_CONTENT
 * @property {String} PILOT_UPDATE_CNT_MNGM Socket emitting event for PILOT_UPDATE_CNT_MNGM
 * @property {String} CALL_ACKNOWLEDGEMENT Socket emitting event for CALL_ACKNOWLEDGEMENT
 */
const emittingEvents = {
    // in most case the event name are kept same for emitter and listner
    // in case if they are different use that
    USER_CONNECT: listeningEvents.USER_CONNECT,
    CONVERSATION_CREATE: listeningEvents.CONVERSATION_CREATE,
    CONVERSATION_JOIN: listeningEvents.CONVERSATION_JOIN,
    CONVERSATION_LEAVE: listeningEvents.CONVERSATION_LEAVE,
    SPACE_CHANGE: 'kct-space-change',
    UPDATE_DASHBOARD: listeningEvents.UPDATE_DASHBOARD,
    PROFILE_UPDATE: listeningEvents.PROFILE_UPDATE,
    ASK_JOIN_CONVERSATION: listeningEvents.ASK_JOIN_CONVERSATION,
    PRIVATE_CONVERSATION: listeningEvents.PRIVATE_CONVERSATION,
    BANNED_USER: listeningEvents.BANNED_USER,
    REMOVED_USER: listeningEvents.REMOVED_USER,
    CONFERECE_UPDATE: listeningEvents.CONFERECE_UPDATE,
    PILOT_UPDATE_NETWORKING: listeningEvents.PILOT_UPDATE_NETWORKING,
    PILOT_UPDATE_CONTENT: listeningEvents.PILOT_UPDATE_CONTENT,
    PILOT_UPDATE_CNT_MNGM: listeningEvents.PILOT_UPDATE_CNT_MNGM,
    CALL_ACKNOWLEDGEMENT: listeningEvents.CALL_ACKNOWLEDGEMENT,
    USER_CON_IN_LIVE_MODE: 'kct-user-in-live',
}

let init = false;
// const socketUrl = "http://localhost:5050"
const socketUrl = `${process.env.REACT_APP_HE_SKT_PROTOCOL || 'https'}://${process.env.REACT_APP_HE_SKT_HOSTNAME}:${process.env.REACT_APP_HE_SKT_PORT}`;
let socketInstance = {}
// initially the props is null this variable will hold the parent props key (cloned)
let props = null;


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To count the space users in the current space by iterating
 * through each conversation (multi user + single)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ConversationData[]} conversations All the conversations
 * @returns {Number} Count of total users
 * @method
 */
const countCurrentSpaceUsers = (conversations) => {
    let result = 0;
    conversations.forEach((c) => {
        result += c.conversation_users.length;
    })
    return result + 1; // adding one as self id is not included in conversations list
}


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To prepare the data for the new interface , here all the data will be prepared for the user to send
 * to socket for initializing the user data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} p Props from parent
 * @returns {Object}
 * @method
 */
const prepareUserDataForNewInterface = (p) => {
    let user = p.event_badge
    let auth = p.auth;
    let event_data = p.event_data;
    let space = p.spaces_data.current_joined_space;
    let conversation = p.spaces_data?.current_joined_conversation;
    let conversations = p.spaces_data?.current_space_conversations;
    let currentSpaceUsersCount = countCurrentSpaceUsers(conversations); // todo check if neccessary
    // // preparing the data to send to socket, this data will be received by all user
    // // make sure this data is in same format we are receiving from backend
    return {
        user: Helper.filterBadgeData(user),
        is_dummy_event: event_data.is_dummy_event,
        namespace: Helper.getNameSpace(),
        spaceId: space.space_uuid,
        eventId: event_data.event_uuid,
        currentSpaceUsersCount: currentSpaceUsersCount,
        conversationId: conversation != null ? conversation.conversation_uuid : null
    };
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To prepare the user data which need to send
 * preparing the data here so whenever user connect call the data is not required to prepare every time
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} p Props from parent component
 * @returns {Object}
 * @method
 */
const prepareUserDataForUserConnect = (p) => {
    let user = p.page_Customization.auth;
    let space = p.event_space.active_space;
    let conversation = p?.event_space.active_conversation;
    let conversations = p?.event_space.conversations;
    let currentSpaceUsersCount = countCurrentSpaceUsers(conversations); // todo check if neccessary
    // preparing the data to send to socket, this data will be received by all user
    // make sure this data is in same format we are receiving from backend
    return {
        user: {
            user_id: user.user_id,
            user_fname: user.user_fname,
            user_lname: user.user_lname,
            user_email: user.user_email,
            user_avatar: user.user_avatar,
            social_links: user.social_links,
            company: user.company,
            unions: user.unions,
            press: user.press,
            instance: user.instance,
            active_state: user.active_state
        },
        namespace: Helper.getNameSpace(),
        spaceId: space.space_uuid,
        eventId: space.event_uuid,
        currentSpaceUsersCount: currentSpaceUsersCount,
        conversationId: conversation != null ? conversation.conversation_uuid : null
    };
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To perform some operation before calling the props dashboard update
 * as on dashboard update this will send
 * online users id
 * and spaces count
 * and here the pilot panel data will be also send along with that so more than one props value needs to be called
 * conditionally and that is done by this method
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} data Response data from socket
 * @param {String} data.currentSpaceOnlineUsers Current Space online users
 * @param {String} data.eventNetworking To indicate if networking is allowed
 * @param {String} data.eventNetworkingMute To indicate if networking is mute
 * @param {String} data.eventContent Current Content state by pilot
 * @param {String} data.eventContentMute Current Content mute state by pilot
 * @param {String} data.eventCurrentComponent Current Content Data set by pilot
 * @method
 */
const dashboardUpdateHandler = (data) => {
    let usersId = [];
    for (let user of data.currentSpaceOnlineUsers) {
        usersId.push(parseInt(user));
    }
    data = {
        ...data,
        currentSpaceOnlineUsers: usersId
    }
    props.filterOnline(data);
    props.setIsOnlineDataReceived(true);

    // updating networking state set by pilot
    if (data.eventNetworking !== null) {
        props.updateGridVisibility(data.eventNetworking);
    }
    if (data.eventNetworkingMute !== null) {
        props.updateConversationMute(data.eventNetworkingMute);
    }
    if (data.eventContent !== null) {
        props.setContentComponentVisibility(!!data.eventContent);
    } else {
        props.setContentComponentVisibility(!!props.graphics_data.content_customized);
    }
    if (data.eventContentMute !== null) {
        props.setZoomMuteButton(data.eventContentMute);
    }
    if (data.eventCurrentComponent !== null) {
        setContentByKey(data.eventCurrentComponent);
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To set the current content by the key only
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} key Key of the content type
 * @method
 */
const setContentByKey = (key) => {
    let content = null;
    if (key === 'zoom' && _.has(props.event_data, ['embedded_url']) && !_.isEmpty(props.event_data.embedded_url)) {
        // if zoom is set then just set it to zoom
        content = {
            currentMediaData: {},
            currentMediaType: Constants.contentManagement.CNT_MGMT_ZOOM_SDK,
        }
    }
    if (content === null) {
        // if content is still null try to find the key in images
        props.event_data.event_live_images.forEach((value) => {
            if (value.key === key) {
                content = {
                    currentMediaData: value,
                    currentMediaType: Constants.contentManagement.CNT_MGMT_IMAGE,
                }
            }
        })
    }
    if (content === null) {
        // if still content is not found the search in video
        props.event_data.event_live_video_links.forEach((value) => {
            if (value.key === key) {
                content = {
                    currentMediaData: value,
                    currentMediaType: Constants.contentManagement.CNT_MGMT_VIDEO,
                }
            }
        })
    }
    if (content) {
        // if provided key is found the set it to content component
        props.updateContentData(content);
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To get the user id from given conversation other than self
 * using to find which user started the conversation with self.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ConversationData} conversation Conversation data from user id will be fetched
 * @method
 */
const getSecondUserId = (conversation) => {
    let userId = null;
    conversation.conversation_users.forEach((value) => {
        if (!value.hasOwnProperty('is_self')) {
            userId = value.user_id;
        }
    })
    return userId;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To handle when someone other user start the conversation with self.
 * Then the active_conversation should have the conversation details
 * the conversation details are fetched by hitting api
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} data Response data from socket
 * @param {String} data.eventId Event id
 * @method
 */
const conversationCreateHandler = (data) => {
    props.getCurrentConversation(data.eventId).then((res) => {
        if (res.data.status) {
            props.changeConversationId(res.data.data)
            props.selfJoinedNewConversation(res.data.data, getSecondUserId(res.data.data))
        }
    })
        .catch(err => console.error("Error in getting conversation details", err));
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description this is handler to update the event status, when the event is manually opened then
 * this method will re-fetch the event data with updated one to open/close the method
 *
 * @deprecated
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} data Response data from socket
 * @param {String} data.eventId Event id
 * @method
 */
const eventManualOpenHandler = (data) => {
    let eventId = null;
    if (props && _.has(props, ['event_space', 'active_space'])) {
        eventId = props.event_space.active_space.event_uuid;
    } else if (data && _.has(data, ['eventId'])) {
        eventId = data.eventId;
    }
    if (eventId !== null) {
        props.spaceApiTrigger(eventId);
    }
}


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description to start the socket
 * and will return the current socket start state;
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @return {Boolean}
 * @method
 */
const socketInit = () => {
    if (!checkSocket()) {
        socketInstance = socketIoClient(socketUrl,
            { // adapt to your server
                reconnection: true,             // default setting at present
                reconnectionDelay: 1000,        // default setting at present
                reconnectionDelayMax: 5000,    // default setting at present
                reconnectionAttempts: Infinity  // default setting at present
            });
        init = true;
    }
    return init;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the networking data that is updated by the pilot,
 * if pilot closed the networking then grid needs to be hidden as well as if current user is in conversation then
 * conversation should be closed from self user side
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} data Response data from socket
 * @param {String} data.action Event id
 * @param {Object} data.currentMediaType Current media type,
 * @param {Object} data.currentMediaData Current media type data,
 * @param {String} data.currentMediaData.value Current value,
 * @param {Number} data.value Value of action
 * @param {Number} data.action Action type for networking or content type
 * @method
 */
const pilotUpdateNetworkingState = (data) => {
    if (data.action === Constants.networkingState.CLOSE) {
        // grid is closed so leaving the conversation if user is in conversation and hiding the grid
        props.showAlert.show(`Conversation are now ${data.value ? "Opened" : "Closed"} by pilot`, {type: "success"})
        props.leaveConversation();
        props.updateGridVisibility(data.value);
    } else if (data.action === Constants.networkingState.MUTE) {
        props.showAlert.show(`All the conversations ${data.value ? 'muted' : 'UnMuted'} by pilot`, {type: "success"})
        props.updateConversationMute(data.value ? 1 : 0);
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the content section data when the pilot update the content section
 * Here pilot can mute the users but internally all user's receive this event and mute self so it shows all users muted
 * by the pilot
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} data Response data from socket
 * @param {String} data.action Event id
 * @param {Object} data.currentMediaType Current media type,
 * @param {Object} data.currentMediaData Current media type data,
 * @param {String} data.currentMediaData.value Current value,
 * @param {Number} data.value Value of action
 * @param {Number} data.action Action type for networking or content type
 * @method
 */
const pilotUpdateContentManageState = (data) => {
    props.showAlert.show(`Content Section Updated `, {type: "success"});
    if (data.currentMediaType !== Constants.contentManagement.CNT_MGMT_ZOOM_SDK) {
        try {
            const client = ZoomMtgEmbedded.createClient();

            client.mute(true);
        } catch (e) {
            console.error('error in mute');
        }
    }
    props.updateContentData(data);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description When pilot updated the content section then proper content data needs to be shown here all the
 * content data is sent in data so if any new data is even added that will be also visible to user even without
 * refreshing the data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} data Response data from socket
 * @param {String} data.action Event id
 * @param {Object} data.currentMediaType Current media type,
 * @param {Object} data.currentMediaData Current media type data,
 * @param {String} data.currentMediaData.value Current value,
 * @param {Number} data.value Value of action
 * @param {Number} data.action Action type for networking or content type
 * @method
 */
const pilotUpdateContentSection = (data) => {
    if (data.action === Constants.contentState.CLOSE) {
        props.showAlert.show(`Content Section Updated `, {type: "success"});
        props.setContentComponentVisibility(data.value);
        props.updateContentData(data);
    } else if (data.action === Constants.contentState.MUTE) {
        props.showAlert.show(`Zoom is muted by pilot`, {type: "success"});
        props.setZoomMuteButton(data.value);
        try {
            const client = ZoomMtgEmbedded.createClient();
            client.mute(data.value === 1);
        } catch (e) {
            console.error('zoom mtg mute error', e);
        }
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description to hit the space join api
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param space_uuid
 */
const joinSpaces = (space_uuid) => {
    try {
        const formData = new FormData();
        formData.append('space_uuid', space_uuid)
        formData.append('event_uuid', props.event_data.event_uuid)

        let accessCode = props.event_data.accessCode || localStorage.getItem("accessCode");
        if (accessCode) {
            formData.append('access_code', accessCode);
        }
        props.spaceJoin(formData)
            .then(res => {
                if (res.data.status) {
                    const {space} = res.data.data;
                    props.setSpaceHostData(space.space_hosts);
                    props.resetSpace(false);
                    props.changeSpace(res.data.data);
                    const socketData = {
                        ...props,
                        spaces_data: {
                            ...props.spaces_data,
                            current_joined_space: space
                        }
                    }
                    emitEvent.SPACE_CHANGE(socketData);
                    // after space change changing the grid pagination back to 1
                    props.triggerPagination({page: 1});
                }
            })
            .catch(err => {
                props.msg && props.msg.show(Helper.handleError(err), {type: 'error'})
            })

    } catch (err) {
        props.msg && props.msg.show(Helper.handleError(err), {type: 'error'})
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To handle the new space data
 * if the space in which user is currently removed then user will be going to join the default space and if user is in
 * conversation that conversation will leave first then user will jump into the new space to load the new users in that
 *
 * If user is not in that space which has removed then it will just simply fetch the spaces data and filter the online
 * users as dummy user may be added in event so its required to fetch the space data and filter the online users again
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param data
 */
const handleSpaceDataUpdate = (data) => {
    let currentSpaceData = props.getCurrentSpace();
    let currentSpace = data.spaces.find(space => space.space_uuid === currentSpaceData.current_joined_space.space_uuid);

    props.showAlert.show('Event Data is updated');

    if (!currentSpace) { // user current space is not found in new spaces
        // remove the user from current space
        props.showAlert.show(`Current Space removed! You will auto join default space in ${Constants.spaceFallbackRemoveTime} seconds`);
        setTimeout(function () {
            try {
                if (!_.isEmpty(currentSpaceData.current_joined_conversation)) {
                    props.showAlert.show('You are about to disconnect from this conversation');
                    props.leaveConversation(() => {
                        props.refreshUserData((spacesData) => {
                            joinSpaces(spacesData.spaces_data.current_joined_space.space_uuid);
                        })
                    });
                } else {
                    props.refreshUserData((spacesData) => {
                        joinSpaces(spacesData.spaces_data.current_joined_space.space_uuid);
                    });
                }

            } catch (err) {
                props.showAlert.show(Helper.handleError(err), {type: 'error'});
            }
        }, Constants.spaceFallbackRemoveTime * 1000);
    } else {
        let currentSpaceData = {...props.getCurrentSpace()};
        props.refreshUserData((spacesData) => {
            if (currentSpaceData.current_joined_conversation && spacesData.spaces_data.current_joined_conversation) {
                let conversationUsers = spacesData.spaces_data.current_joined_conversation.conversation_users;
                let oldConversationUser = currentSpaceData.current_joined_conversation.conversation_users;
                conversationUsers = conversationUsers.map(conversationUser => {
                    let oldUser = oldConversationUser.find(oldUser => oldUser.user_id === conversationUser.user_id);
                    if(oldUser) {
                        return {
                            ...conversationUser,
                            is_mute: oldUser.is_mute,
                        }
                    }
                    return conversationUser;
                })
                spacesData.spaces_data.current_joined_conversation.conversation_users = conversationUsers;
            }
            if (spacesData.spaces_data?.current_joined_conversation?.conversation_users.length <= 1) {
                props.leaveConversation();
            }
            props.filterOnline(data.spacesOnlineData);
            joinSpaces(spacesData.spaces_data.current_joined_space.space_uuid);
        });
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To handle the event dummy status updated
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param data
 */
const handleEventDataUpdate = (data) => {
    let oldData = props.getEventData();
    let convLimit = Number.parseInt(data.event_conv_limit);
    props.setEventGraphics({
        ...oldData,
        event_title: data.event_title,
        event_conv_limit: convLimit,
        is_dummy_event: data.is_dummy_event,
        event_grid_rows: data.event_grid_rows,
    })

    props.setEventMeta({
        ...props.eventMeta,
        short_join_link: `${window.location.origin}/j/${data.event_link}`,
    })
    VideoElementRepository.MAX_USERS = convLimit;
    props.setSceneryData(data.current_scenery_data);
    props.applyScenerygraphicData(data.current_scenery_data);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To handle when event data is live updated
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param data
 */
const handleEventDataUpdated = (data) => {
    localStorage.setItem('event_grid_rows', data.event_grid_rows);
    handleSpaceDataUpdate(data);
    handleEventDataUpdate(data);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will start the listeners for the all available events.
 * As on socket events from server here currently the redux part is updated
 * so the redux methods by props are passed
 * ---------------------------------------------------------------------------------------------------------------------
 * @method
 */
const startSocketEventListeners = () => {
    // to start only if sockets started;
    if (socketInstance !== undefined && props) {
        socketInstance.on(listeningEvents.USER_CONNECT, (data) => {
            props.addNewUser(data);
        });
        socketInstance.on(listeningEvents.UPDATE_DASHBOARD, dashboardUpdateHandler);
        socketInstance.on(listeningEvents.CONVERSATION_CREATE, conversationCreateHandler);
        socketInstance.on(listeningEvents.CONVERSATION_JOIN, props.updateUserPosition);
        socketInstance.on(listeningEvents.USER_DISCONNECT, (data) => {
            props.updateUserPosition(data);
        });
        socketInstance.on(listeningEvents.CONVERSATION_LEAVE, props.conversationLeave);
        socketInstance.on(listeningEvents.SPACE_USER_COUNT_CHANGE, props.spaceUserCountUpdate);
        socketInstance.on(listeningEvents.EVENT_MANUALLY_OPEN, eventManualOpenHandler);
        socketInstance.on(listeningEvents.PROFILE_UPDATE, props.updateProfile);
        socketInstance.on(listeningEvents.ASK_JOIN_CONVERSATION, props.askJoinConversation);
        //handle this from dashboard
        socketInstance.on(listeningEvents.PRIVATE_CONVERSATION, props.askToPrivateConversation);
        socketInstance.on(listeningEvents.REMOVED_USER, props.handleBanUser);
        // socketInstance.on(listeningEvents.BANNED_USER.props.bannedUser)
        socketInstance.on(listeningEvents.BANNED_USER, props.handleBanUser2);
        socketInstance.on(listeningEvents.PILOT_UPDATE_NETWORKING, pilotUpdateNetworkingState);
        socketInstance.on(listeningEvents.PILOT_UPDATE_CONTENT, pilotUpdateContentSection);
        socketInstance.on(listeningEvents.PILOT_UPDATE_CNT_MNGM, pilotUpdateContentManageState);
        socketInstance.on(listeningEvents.EVENT_DATA_UPDATED, handleEventDataUpdated);

        socketInstance.on(listeningEvents.CALL_ACKNOWLEDGEMENT, (data) => {
            props.callOff();
            if (data.state === 0) {
                props.showAlert.show(`User Rejected the call`, {type: "success"})
            }

        });

        socketInstance.on(listeningEvents.CONFERECE_UPDATE, (data) => {
            if (data.status === 0) {
                try {
                    const client = ZoomMtgEmbedded.createClient();
                    client.leaveMeeting().then(() => {})
                        .catch(e => console.error('error in leave zoom', e));
                    if (props.getContentManagementMeta().currentMediaType
                        === Constants.contentManagement.CNT_MGMT_ZOOM_SDK) {
                        props.updateContentData({
                            contentMediaType: null,
                            currentMediaData: {}
                        });
                    }
                } catch (e) {
                    console.error("ERROR ", e);
                }
            }
            props.handleConfereceUpdate(data);
        });
        socketInstance.io.on("reconnect", (attempt) => {
            let p = props;
            let callback = (p) => {
                const userConnectData = prepareUserDataForNewInterface(p);
                console.debug(new Date().toTimeString(), `SKT_DEBUG reconnected socket with attempt ${attempt}`, {type: 'success'}, userConnectData, _.has(p, ['event_data', 'event_version']) && p.event_data.event_version);
                emitEvent.USER_CONNECT(userConnectData);
            }
            p.refreshUserData(callback);
        });
        socketInstance.on("connect_error", (err) => {
            props.showAlert.show(
                `There is some issue with connection, Please check your connection`, {type: "warning"}
            )
            console.debug(new Date().toTimeString(), ` SKT_DEBUG connect_error skt failed to connect`,);
        });

        socketInstance.io.on("reconnect_attempt", (attempt) => {
            // props.showAlert.show(
            //     `There is some issue with connection please refresh the page`, {type: "warning"}
            // )
            console.debug(new Date().toTimeString(), ` SKT_DEBUG  reconnection attempting ${attempt}`);
        });
        socketInstance.on(listeningEvents.ZOOM_PARTICIPANT_JOINED, (data) => {
            props.setZoomUserAdmitState(data.user_id);
        });
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To initialize the socket with props, here props contains all the required methods for providing the
 * handler for the socket events from server
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} p Initial props from parent component
 * @method
 */
const initiateSocket = (p) => {
    if (p && !checkSocket()) {
        props = p; // assiging the props came from starter so props can be used globally in this file

        socketInit(); // this will create a socket connection
        startSocketEventListeners(); // this will start listening for the available sockets
        if (_.has(p, ['event_data', 'event_version']) && p.event_data.event_version == 2) {
            const userConnectData = prepareUserDataForNewInterface(p);
            emitEvent.USER_CONNECT(userConnectData);
        } else {
            const userConnectData = prepareUserDataForUserConnect(p);  // after initialize to emit the user connect
            emitEvent.USER_CONNECT(userConnectData);
        }
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To emit any type of event from emitable events
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} event Event name to emit
 * @param {Any} data the data need to send
 * @return {Boolean}
 * @method
 */
const emit = (event, data) => {
    if (Helper.objLength(socketInstance)) {
        socketInstance.emit(event, data);
        return true;
    }
    return false;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To emit the space change as space change is treated as disconnect for one space and online for another
 * space
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} p Initial props from parent component
 * @method
 */
const emitSpaceChange = (p) => {
    if (_.has(p, ['event_data', 'event_version']) && p.event_data.event_version == 2) {
        const userConnectData = prepareUserDataForNewInterface(p);
        emit(emittingEvents.SPACE_CHANGE, userConnectData);
    } else {
        const userConnectData = prepareUserDataForUserConnect(p);  // after initialize to emit the user connect
        emit(emittingEvents.SPACE_CHANGE, userConnectData);
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description These are the emits, which gonna call from different files whenever needed
 * Some emit data will be prepared so just directly event will be emit
 * else the data may be prepared here
 * ---------------------------------------------------------------------------------------------------------------------
 */
const emitEvent = {
    USER_CONNECT: (data) => emit(emittingEvents.USER_CONNECT, data),
    CONVERSATION_CREATE: (data) => emit(emittingEvents.CONVERSATION_CREATE, data),
    CONVERSATION_JOIN: (data) => emit(emittingEvents.CONVERSATION_JOIN, data),
    CONVERSATION_LEAVE: (data) => emit(emittingEvents.CONVERSATION_LEAVE, data),
    SPACE_CHANGE: emitSpaceChange,
    UPDATE_DASHBOARD: (data) => emit(emittingEvents.UPDATE_DASHBOARD, data),
    PROFILE_UPDATE: (data) => emit(emittingEvents.PROFILE_UPDATE, data),
    ASK_JOIN_CONVERSATION: (data) => emit(emittingEvents.ASK_JOIN_CONVERSATION, data),
    PRIVATE_CONVERSATION: (data) => emit(emittingEvents.PRIVATE_CONVERSATION, data),
    BANNED_USER: (data) => emit(emittingEvents.BANNED_USER, data),
    REMOVED_USER: (data) => emit(emittingEvents.REMOVED_USER, data),
    CONFERECE_UPDATE: (data) => emit(emittingEvents.CONFERECE_UPDATE, data),
    PILOT_UPDATE_NETWORKING: (data) => emit(emittingEvents.PILOT_UPDATE_NETWORKING, data),
    PILOT_UPDATE_CONTENT: (data) => emit(emittingEvents.PILOT_UPDATE_CONTENT, data),
    PILOT_UPDATE_CNT_MNGM: (data) => emit(emittingEvents.PILOT_UPDATE_CNT_MNGM, data),
    CALL_ACKNOWLEDGEMENT: (data) => emit(emittingEvents.CALL_ACKNOWLEDGEMENT, data),
    USER_CON_IN_LIVE_MODE: (data) => emit(emittingEvents.USER_CON_IN_LIVE_MODE),
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To disconnect the socket, set local state to false,
 * and emit socket close will emit disconnected event
 * ---------------------------------------------------------------------------------------------------------------------
 * @method
 */
const disconnectSocket = () => {
    if (checkSocket()) {
        socketInstance.close();
        init = false;
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To reconnect the socket
 * ---------------------------------------------------------------------------------------------------------------------
 * @method
 */
const reconnectSocket = () => {
    if (checkSocket()) {
        socketInstance.socket.reconnect();
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description to check if the socket has already initialized or not to avoid the multiple connection to socket
 * else the single event will be listen more than one time and it will execute the handler more than one time as well
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {Boolean}
 * @method
 */
const checkSocket = () => {
    if (socketInstance && _.has(socketInstance, ['connected']) && _.has(socketInstance, ['disconnected'])) {
        return socketInstance.connected == true
    } else {
        return false
    }
}


export default {
    initiateSocket: initiateSocket,
    disconnectSocket: disconnectSocket,
    reconnectSocket: reconnectSocket,
    emitEvent: emitEvent,
    checkSocket: checkSocket,
}