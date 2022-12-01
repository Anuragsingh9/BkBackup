import {KeepContact as KCT} from '../../types';
import _ from 'lodash';
import {prepareGridDataData} from '../../utils/pagination';
import {findAndUpdateSpaceUserCount, reArrangeConversations, sliderRearrangementFunc} from '../../utils/common.js';

const eventCases = (state, action) => {

    switch (action.type) {
        case KCT.NEW_INTERFACE.FILTER_ONLINE_MEMBERS: {
            let onlineUsers = action.payload.currentSpaceOnlineUsers;
            let spacesOnlineUsersCount = action.payload.spacesUserCount;

            // from each conversation this will remove users which are not in online users id

            let {
                conversations,
                totalUsers
            } = filterConversationsWithOnlineUser(state.interfaceSpacesData.current_space_conversations, onlineUsers);


            let isSpaceHostOnline = false;

            if (!_.isEmpty(state.interfaceSpaceHostData)) {
                const spaceHost = state.interfaceSpaceHostData[0];
                const flag = onlineUsers.filter((onlineUserId) => {
                    if (onlineUserId == spaceHost.user_id) {
                        return onlineUserId;
                    }
                });
                isSpaceHostOnline = !_.isEmpty(flag);
            }

            const spaces = state.interfaceSpacesData.spaces.map((space) => {
                return findAndUpdateSpaceUserCount(space, spacesOnlineUsersCount);
            })
            const currentSpaceId = state.interfaceSpacesData.current_joined_space.space_uuid;
            state = {
                ...state, interfaceSpacesData: {
                    ...state.interfaceSpacesData,
                    spaces: spaces,
                    current_space_conversations: reArrangeConversations(conversations),
                },

                gridPagination: prepareGridDataData(conversations, state),
                interfaceSliderData: sliderRearrangementFunc(spaces, currentSpaceId),
                spacesDataLoad: true,
                availabilityHost: isSpaceHostOnline,
            }
        }

    }
    return state;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will return conversations array with online users only, and the total count of users
 *
 * @note this will sort the conversations also by users count in conversation also
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param conversations
 * @param onlineUsers
 * @returns {{totalUsers: number, conversations: *[]}}
 */
const filterConversationsWithOnlineUser = (conversations, onlineUsers) => {
    let resultConversations = {};

    let totalUsers = 0;

    conversations.forEach((conversation) => {
        let usersCountInCurrentConv = 0;
        let filteredUsers = [];

        if (!conversation) {
            console.error('conversation found with no data in conversations');
        }

        // checking for each user of conversation presence in online users array
        conversation.conversation_users.forEach((conversationUser) => {
            if (conversationUser.is_dummy === 1 || onlineUsers?.indexOf(conversationUser?.user_id) !== -1) {
                filteredUsers.push(conversationUser);
                usersCountInCurrentConv++;
            }
        });

        // if any user found to be added in conversation then add it
        if (usersCountInCurrentConv !== 0) {
            if (!resultConversations[usersCountInCurrentConv]) {
                // users are present but in result its first time of data with this users count conversation
                resultConversations[usersCountInCurrentConv] = [];
            }
            resultConversations[usersCountInCurrentConv].push({
                ...conversation,
                conversation_users: filteredUsers,
            })
        }
        totalUsers += usersCountInCurrentConv;
    });

    // added the data and in each key of object it was user count so it becomes easy to sort
    let conversationCounts = Object.keys(resultConversations).sort((a, b) => a - b);
    let result = [];
    conversationCounts.forEach(count => {
        result = [...result, ...resultConversations[count]];
    });
    return {conversations: result, totalUsers};
}


export default eventCases;