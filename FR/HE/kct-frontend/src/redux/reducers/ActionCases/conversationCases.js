/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file is used for the conversation reducing methods to handle the actions related to conversation
 * ---------------------------------------------------------------------------------------------------------------------
 */

import {KeepContact as KCT} from '../../types';
import _ from 'lodash';
import {prepareGridDataData} from '../../utils/pagination';
import {
    addUserToExistingConversation,
    checkHost,
    conversationUpdate,
    reArrangeConversationData,
    reArrangeConversations,
    removeUserFromDashboard,
    updateConversationState
} from "../../utils/common";
import {
    addUserInActiveConversation,
    deleteConversation,
    leaveConversation,
    updateConversations, updateOtherMute
} from '../../utils/conversation.js';


const conversationCases = (oldState, action) => {
    let state = oldState;
    switch (action.type) {
        case KCT.NEW_INTERFACE.HANDLE_PRIVATE_CONVERSATION://private
            const updatedConversation = updateConversationState(state.interfaceSpacesData, action.payload);
            const newData = {
                conversation_uuid: action.payload.conversationId,
                current_state: action.payload.is_private
            }
            const {current_joined_conversation} = state.interfaceSpacesData;
            let currentConversation = current_joined_conversation

            if (current_joined_conversation && _.has(current_joined_conversation, ['conversation_uuid']) && newData.conversation_uuid == current_joined_conversation.conversation_uuid) {
                currentConversation = conversationUpdate(state.interfaceSpacesData, newData);

            }

            state = {
                ...state,
                interfaceSpacesData: {
                    ...state.interfaceSpacesData,
                    current_joined_conversation: currentConversation,
                    current_space_conversations: updatedConversation
                },
            }
            break;
        case KCT.NEW_INTERFACE.UPDATE_USERS_CONVERSATION: {
            state = leaveConversation(state, action);
        }
            break;
        case KCT.NEW_INTERFACE.UPDATE_EVENT_CONVERSATIONS: {
            state = updateConversations(state, action)
            break;
        }
        case KCT.NEW_INTERFACE.UPDATE_CONVERSATION_OTHER_MUTE: {
            state = updateOtherMute(state, action.payload.userId, action.payload.muted, action.payload.volume)
            break;
        }
        case KCT.NEW_INTERFACE.DELETE_CONVERSATIONS: {
            state = deleteConversation(state, action);
        }
            break;
        case KCT.NEW_INTERFACE.ADD_USER_TO_ACTIVE_CONVERSATIONS: {
            state = addUserInActiveConversation(state, action);
        }
            break;

        case KCT.NEW_INTERFACE.ADD_EVENT_MEMBER_CONVERSATIONS: {
            const conversations = reArrangeConversationData(state.interfaceSpacesData.current_space_conversations, action.userId);
            const newConversation = {
                conversation_uuid: action.payload.conversation_uuid,
                conversation_users: action.payload.conversation_users,
                is_conversation_private: 0,
                conversation_type: 'active'
            };
            conversations.push(newConversation);
            state = {
                ...state, interfaceSpacesData: {
                    ...state.interfaceSpacesData,
                    current_space_conversations: reArrangeConversations(conversations),
                },
                gridPagination: prepareGridDataData(conversations, state),

            }
        }
            break;
        case KCT.NEW_INTERFACE.ADD_NEW_EVENT_MEMBERS: {
            let conversations = removeUserFromDashboard(action.payload.user.user_id, state.interfaceSpacesData.current_space_conversations);
            const hostAvail = checkHost(action.payload.user.user_id, state);
            let avail = state.availabilityHost;
            if (hostAvail) {
                avail = true;
            }
            // here we will handle to add a new user to dashboard like
            // e.g. a new user connected so if previous conversation id given we will add him to existing
            // or create a new row with empty conversation
            if (action.payload.conversationId) {
                // new user already in conversation so add user to conversation by user id
                conversations = addUserToExistingConversation(action.payload.user, action.payload.conversationId, conversations);
            } else {
                // new user added with no conversation so add a new row
                const data = {
                    ...action.payload.user,
                    conversation_uuid: null,
                    is_conversation_private: 0,
                    conversation_type: 'single_user',
                    conversation_users: [
                        action.payload.user
                    ]
                }
                conversations.push(data);
            }
            state = {
                ...state,
                availabilityHost: avail,
                interfaceSpacesData: {
                    ...state.interfaceSpacesData,
                    current_space_conversations: reArrangeConversations(conversations),
                },
                gridPagination: prepareGridDataData(conversations, state),
            }
        }
            break;
        case KCT.NEW_INTERFACE.CHANGE_CONVERSATION_IDS: {
            state = {
                ...state, interfaceSpacesData: {
                    ...state.interfaceSpacesData,
                    current_joined_conversation: action.payload
                }
            }
        }
            break;

        /*
       - This is called when someone start the conversation with self,
       - This method job is to update the second user (who started conversation with self user) position with current
       * payload: conversation response
       * userId: to which conversation started so we can replace that user conversation with new conversation
       */
        case KCT.NEW_INTERFACE.ADD_EVENT_MEMBER_BY_USER_IDS: {
            const conversations = state.interfaceSpacesData.current_space_conversations
                .map((val) => {
                    const isSecondUserPresentInConversation = val.conversation_users.find(user => {
                        return user.user_id === action.userId
                    });
                    if (isSecondUserPresentInConversation) {
                        return action.payload
                    } else {
                        return val;
                    }
                })
            state = {
                ...state, interfaceSpacesData: {
                    ...state.interfaceSpacesData,
                    current_space_conversations: reArrangeConversations(conversations),
                },
                gridPagination: prepareGridDataData(conversations, state),
            }
        }
            break;
        default:
            break;
    }
    return state;
}


export default conversationCases;

