/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file provide the grid related methods
 * ---------------------------------------------------------------------------------------------------------------------
 */

import {
    addUserByAnotherUserId,
    addUserToExistingConversation,
    checkHost,
    getUserFromUserId,
    reArrangeConversations,
    removeUserFromActiveConversation,
    removeUserFromDashboard,
    updateActiveConversation
} from "./common";
import {prepareGridDataData} from "./pagination";
/**
 * This module handles the user reducer functions.
 * @module UserReducerUtils
 */

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function prepares new single user object.
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {Object} action Action object with user data
 * @param {UserBadge} action.payload.user User data
 * @returns {Object}
 * @method
 **/
const prepareData = (action) => {
    return {
        ...action.payload.user,
        conversation_uuid: null,
        is_conversation_private: 0,
        conversation_type: 'single_user',
        conversation_users: [
            action.payload.user
        ]
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function handles user connect event.
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {Object} state State from parent component.
 * @param {Object} action Action data to process
 * @param {UserBadge} action.payload.user User data
 * @returns {Object}
 * @method
 **/
export const userConnectHandler = (state, action) => {


    let conversations = removeUserFromDashboard(action.payload.user.user_id, state.interfaceSpacesData.current_space_conversations);

    const hostAvail = checkHost(action.payload.user.user_id, state);

    let avail = state.availabilityHost;

    if (hostAvail) {
        avail = true;
    }

    if (action.payload.conversationId) {

        conversations = addUserToExistingConversation(action.payload.user, action.payload.conversationId, conversations);

    } else {
        const data = prepareData(action);

        conversations.push(data);
    }

    return {
        ...state,
        availabilityHost: avail,
        interfaceSpacesData: {
            ...state.interfaceSpacesData,
            current_space_conversations: reArrangeConversations(conversations),
        },
        gridPagination: prepareGridDataData(conversations, state),
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function handles user disconnect event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} state State from parent component.
 * @param {Object} action Action data to process
 * @param {UserBadge} action.payload.fromUserId User id from conversation removed
 * @param {UserBadge} action.payload.toUserId User id to where from user shift, null for disconnect case
 * @param {UserBadge} action.payload.conversationId Conversation ID where from user shift, null for disconnect case
 * @returns {Object}
 * @method
 **/
export const userDisconnect = (state, action) => {
    const fromId = action.payload.fromUserId;
    const toId = action.payload.toUserId;
    const conversationId = action.payload.conversationId;
    let avail = state.availabilityHost;
    let conversations = state.interfaceSpacesData.current_space_conversations;
    let active_conversation = state.interfaceSpacesData.current_joined_conversation;

    const userObj = getUserFromUserId(fromId, state.interfaceSpacesData.current_space_conversations);
    // remove user from current position
    conversations = removeUserFromDashboard(fromId, conversations);

    if (toId) { // like in disconnect case we will update user conversation but toId will be null

        conversations = addUserByAnotherUserId(userObj.fromUser, toId, conversations, conversationId);

        if (active_conversation != null) {
            active_conversation = updateActiveConversation(userObj.fromUser, toId, conversations, conversationId, active_conversation);
        }

    } else { // here means removing user, so need to also remove from active_conversation if in it
        active_conversation = removeUserFromActiveConversation(fromId, active_conversation);

        avail = !checkHost(fromId, state);

    }
    return {
        ...state,
        availabilityHost: avail,
        interfaceSpacesData: {
            ...state.interfaceSpacesData,
            current_joined_conversation: active_conversation,
            current_space_conversations: reArrangeConversations(conversations)
        }
    }

}