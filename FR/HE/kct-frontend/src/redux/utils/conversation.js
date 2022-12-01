/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file contains conversation related dispatcher methods
 * ---------------------------------------------------------------------------------------------------------------------
 */

import {
    addUserByAnotherUserId,
    addUserToActiveConversation,
    checkHost,
    conversationUpdate,
    deleteConversationById,
    getConversationById,
    getUserFromUserId,
    reArrangeConversations,
    removeUserFromActiveConversation,
    removeUserFromDashboard,
    updateActiveConversation,
    updatePrivateInGrid
} from "./common";
import {prepareGridDataData} from "./pagination";
import _ from 'lodash';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This module handles the conversation reducer functions.
 * ---------------------------------------------------------------------------------------------------------------------
 * @module ConversationReducerUtils
 */


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function is used update state in case of user left the conversation
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} oldState Current redux object.
 * @param {Object} action Redux action object with payload and type.
 * @returns {Object} updated state
 * @method
 **/
export const leaveConversation = (oldState, action) => {
    let state = oldState;
    const fromId = action.payload.fromUserId;
    const toId = action.payload.toUserId;
    const conversationId = action.payload.conversationId;
    let authData = state.interfaceAuth;
    let avail = state.availabilityHost;

    let conversations = state.interfaceSpacesData.current_space_conversations;
    let active_conversation = state.interfaceSpacesData.current_joined_conversation;

    const userObj = getUserFromUserId(fromId, state.interfaceSpacesData.current_space_conversations);
    // remove user from current position
    conversations = removeUserFromDashboard(fromId, conversations);

    const eventData = state.interfaceEventData;
    const is_dummy = (_.has(eventData, ['is_dummy_event']) && eventData.is_dummy_event);

    if (toId) { // like in disconnect case we will update user conversation but toId will be null

        conversations = addUserByAnotherUserId(userObj.fromUser, toId, conversations, conversationId);
        if (active_conversation != null) {
            active_conversation = updateActiveConversation(userObj.fromUser, toId, conversations, conversationId, active_conversation);
        }

    } else { // here means removing user, so need to also remove from active_conversation if in it
        active_conversation = removeUserFromActiveConversation(fromId, active_conversation);
        const hostAvail = checkHost(fromId, state);
        if (hostAvail) {
            avail = false;
        }
    }
    return {
        ...state,
        availabilityHost: avail,
        interfaceSpacesData: {
            ...state.interfaceSpacesData,
            current_joined_conversation: active_conversation,
            current_space_conversations: reArrangeConversations(conversations)
        },
        gridPagination: prepareGridDataData(conversations, state),
    }

}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function is used update state in case of user changes conversation type
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} state Current redux object.
 * @param {Object} action Redux action object with payload and type.
 * @returns {Object} updated state
 * @method
 **/
export const changeConversationType = (state, action) => {
    const updateConversation = conversationUpdate(state.interfaceSpacesData, action.payload);
    const gridData = updatePrivateInGrid(state.interfaceSpacesData, action.payload);
    return {
        ...state,
        interfaceSpacesData: {
            ...state.interfaceSpacesData,
            current_joined_conversation: updateConversation,
            current_space_conversations: gridData,
        },
    }

}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * Function is used update state in case of conversation is deleted
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} oldState Current redux object.
 * @param {Object} action Redux action object with payload and type.
 * @returns {Object} updated state
 * @method
 **/
export const deleteConversation = (oldState, action) => {
    let state = oldState;
    let auth = state.interfaceAuth;
    const conversations = state.interfaceSpacesData.current_space_conversations.map((val, i) => {
            return val.conversation_uuid == action.payload.conversation_uuid ?
                {
                    ...val,
                    conversation_uuid: val.conversation_users.length > 2 ? val.conversation_uuid : null,
                    conversation_users: val.conversation_users.filter((item) => item.user_id != auth.user_id)
                } :
                val
        }
    )
    return {
        ...state, interfaceSpacesData: {
            ...state.interfaceSpacesData,
            current_space_conversations: reArrangeConversations(conversations),
            current_joined_conversation: null
        },
        gridPagination: prepareGridDataData(conversations, state),
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function handles state update in case when user is added to active conversation
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} oldState Current redux object.
 * @param {Object} action Redux action object with payload and type.
 * @returns {Object} updated state
 * @method
 **/
export const addUserInActiveConversation = (oldState, action) => {
    let state = oldState;
    let activeConversation = state.interfaceSpacesData.current_joined_conversation;
    const userObj = getUserFromUserId(action.payload, state.interfaceSpacesData.current_space_conversations);
    activeConversation = addUserToActiveConversation(activeConversation, userObj.fromUser);
    return {
        ...state, interfaceSpacesData: {
            ...state.interfaceSpacesData,
            current_joined_conversation: activeConversation
        }
    }
}

/**
 * @deprecated
 * @param {Object} state Current redux object.
 * @param {Object} action Redux action object with payload and type.
 * @returns {Object} updated state
 * @method
 */
export const conversationLeaveUpdate = (state, action) => {
    let conversations = state.interfaceSpacesData.current_space_conversations
    let auth = state.interfaceAuth;
    const spaceHost = !_.isEmpty(state.interfaceSpaceHostData) ? state.interfaceSpaceHostData[0] : {};

    if (auth.user_id == spaceHost.user_id) {
        return spaceHostConversationDelete(state, action, conversations);
    } else {
        return userConversationUpdate(state, action, conversations)
    }

}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the conversation data with new conversation data from action
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} oldState Current redux object.
 * @param {Object} action Redux action object with payload and type.
 * @returns {Object} updated state
 * @method
 */
export const updateConversations = (oldState, action) => {
    let state = oldState;
    let conversations = state.interfaceSpacesData.current_space_conversations
    let auth = state.interfaceAuth;
    let activeConversation = state.interfaceSpacesData.current_joined_conversation;
    const conversation = getConversationById(conversations, action.payload.conversationId);
    if (conversation) {
        if (action.payload.type === 'delete') {
            !_.isEmpty(conversation.conversation_users) && conversation.conversation_users.map((user) => {
                if (user?.user_id != auth?.user_id) {
                    conversations.push({
                        conversation_uuid: null,
                        conversation_type: 'single_user',
                        conversation_users: [user]
                    });
                }
            });
            if (state.interfaceSpacesData.current_joined_conversation) {
                if (action.payload.conversationId === state.interfaceSpacesData.current_joined_conversation.conversation_uuid) {
                    activeConversation = null;
                }
            }
            conversations = deleteConversationById(conversations, action.payload.conversationId);
        } else if (action.payload.type === 'remove') {
            const user = getUserFromUserId(action.payload.userId, conversations).fromUser;
            if (user) {
                conversations = removeUserFromDashboard(user.user_id, conversations);
                activeConversation = removeUserFromActiveConversation(user.user_id, activeConversation)
                conversations.push({
                    conversation_uuid: null,
                    conversation_type: 'single_user',
                    conversation_users: [user]
                })
            }

            activeConversation = removeUserFromActiveConversation(action.payload.userId, activeConversation)
        }
    }
    return {
        ...state,
        interfaceSpacesData: {
            ...state.interfaceSpacesData,
            current_space_conversations: reArrangeConversations(conversations),
            current_joined_conversation: activeConversation
        },
        gridPagination: prepareGridDataData(conversations, state),
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To remove the conversation for space host
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} state Current redux object.
 * @param {Object} action Redux action object with payload and type.
 * @param {ConversationData[]} action Redux action object with payload and type.
 * @returns {ConversationData[]} updated state
 * @method
 */
export const spaceHostConversationDelete = (state, action, conversations) => {
    let activeConversation = state.interfaceSpacesData.current_joined_conversation;
    const {conversationId} = action.payload;
    if (action.payload.type === 'delete') {


    }


}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the conversation data with new data of conversation for the user
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} state Current redux object.
 * @param {Object} action Redux action object with payload and type.
 * @param {ConversationData[]} conversations Redux action object with payload and type.
 * @returns {Object} updated state
 * @method
 */
export const userConversationUpdate = (state, action, conversations) => {
    let activeConversation = state.interfaceSpacesData.current_joined_conversation;

}

export const updateOtherMute = (state, userId, muteState, volume) => {
    if (state.interfaceSpacesData.current_joined_conversation) {
        let flagToUpdate = false;
        let users = state.interfaceSpacesData.current_joined_conversation.conversation_users.map(user => {
            if (user.user_id === userId) {
                user.is_mute = muteState;
                volume = Number.parseInt(volume * 10) * 10;
                if (volume !== user['volume'] || muteState !== null) {
                    // user volume changed majorly
                    flagToUpdate = true;
                    user['volume'] = volume;
                }
            }
            return user;
        });
        if(!flagToUpdate) {
            return state;
        }
        // return state;
        state = {
            ...state,
            interfaceSpacesData: {
                ...state.interfaceSpacesData,
                current_joined_conversation: {
                    ...state.interfaceSpacesData.current_joined_conversation,
                    conversation_users: users,
                }
            }
        }
    }
    return state;
}


