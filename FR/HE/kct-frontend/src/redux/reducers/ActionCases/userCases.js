/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file contains the reducer action handler for the user related task like updating space host data
 * updating user badge etc.
 * ---------------------------------------------------------------------------------------------------------------------
 */

import {KeepContact as KCT} from '../../types';
import _ from 'lodash';
import {checkUpdateSpaceHost, profileDataUpdate, updateUserDataFromConversations} from '../../utils/profile.js';
import {addUserToActiveConversation, getUserFromUserId, reArrangeConversations} from '../../utils/common.js';
import {userConnectHandler} from '../../utils/user.js';
import {prepareGridDataData} from '../../utils/pagination';

const userCases = (state, action) => {

    switch (action.type) {
        case KCT.NEW_INTERFACE.SET_SPACE_HOST_DATA://SpaceHost
            state = {
                ...state,
                interfaceSpaceHostData: action.payload,
            }
            break;
        case KCT.NEW_INTERFACE.UPDATE_CONVERSATION_PROFILE:
            let auth = state.interfaceAuth;
            const updateSpacesData = profileDataUpdate(state.interfaceSpacesData, action.payload, auth);
            const spaceHost = !_.isEmpty(state.interfaceSpaceHostData) ? checkUpdateSpaceHost(state, action.payload) : [];

            let gridRows = state.gridPagination.currentPageData;
            let newGridData = gridRows.map(conversations => {
                return updateUserDataFromConversations(conversations, action.payload, auth);
            })
            state = {
                ...state,
                interfaceSpacesData: updateSpacesData,
                interfaceSpaceHostData: spaceHost,
                gridPagination: {
                    ...state.gridPagination,
                    currentPageData: newGridData,
                }
            }
            break;
        case KCT.NEW_INTERFACE.ADD_EVENT_MEMBERS_SINGLE: {
            const single = state.interfaceSpacesData.current_space_conversations.concat(action.payload)

            state = {
                ...state, interfaceSpacesData: {
                    ...state.interfaceSpacesData,
                    current_space_conversations: reArrangeConversations(single)
                },
                gridPagination: prepareGridDataData(single, state)
            }
        }
            break;
        case KCT.NEW_INTERFACE.ADD_NEW_EVENT_MEMBERS: {

            state = userConnectHandler(state, action);
        }
            break;
        case KCT.NEW_INTERFACE.ADD_USER_TO_ACTIVE_CONVERSATIONS: {
            let activeConversation = state.interfaceSpacesData.current_joined_conversation;
            const userObj = getUserFromUserId(action.payload, state.interfaceSpacesData.current_space_conversations);
            activeConversation = addUserToActiveConversation(activeConversation, userObj.fromUser);
            state = {
                ...state, interfaceSpacesData: {
                    ...state.interfaceSpacesData,
                    current_joined_conversation: activeConversation
                }
            }
        }
            break;
    }

    return state;


}

export default userCases;