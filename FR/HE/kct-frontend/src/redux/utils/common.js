/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file contains some application level common utils dispatchers
 * ---------------------------------------------------------------------------------------------------------------------
 * @module CommonReducerUtils
 */

import _ from 'lodash';
import Constants from "../../Constants";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To check for the space host data updated
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} state Current redux object.
 * @param {UserBadge} profile User Badge data
 * @returns {InterfaceSpaceData}
 * @method
 */
export const checkUpdateSpaceHost = (state, profile) => {
    const {interfaceSpaceHostData} = state;

    const hostData = interfaceSpaceHostData[0];

    if (hostData.user_id == profile.user_id) {
        let newData = {
            ...hostData,
            fname: profile.user_fname,
            lname: _.has(profile, ['user_lname']) ? profile.user_lname : '',
            avatar: profile.user_avatar
        };
        return [newData];
    } else {
        return interfaceSpaceHostData;
    }

}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To filter the conversation to remove the duplicate user
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ConversationData[]} conversations  All the conversations of current space
 * @returns {ConversationData[]}
 * @method
 */
export const reFilterConversations = (conversations) => {
    const final_conversation = [];
    const oneOfDuplicates = [];
    conversations.map((item, key) => {
        let flag = false;
        conversations.map((val, index) => {
            if (key != index) {
                item.conversation_users.map((user) => {
                    val.conversation_users.map((userData) => {
                        if (user.user_id == userData.user_id) {
                            flag = true;
                            if (oneOfDuplicates.indexOf(item) == -1) {
                                oneOfDuplicates.push(item);
                            }
                        }
                    });
                });
            }
        });
        if (flag == false) {
            final_conversation.push(item);
        }
    });
    return [...oneOfDuplicates, ...final_conversation];
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To sort the conversations on the basis of number of users in it
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ConversationData[]} conversations  All the conversations of current space
 * @param {Number} userId User id of target user
 * @returns {ConversationData}
 * @method
 */
export const reArrangeConversationData = (conversations, userId) => {
    const newConversationData = conversations.filter((val, key) => {
        const userExist = val.conversation_users.filter((item) => {
            if (item.user_id == userId) {
                return item;
            }
        });

        if (_.isEmpty(userExist)) {
            return val;
        }
    });

    return newConversationData;

}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To slide the spaces to back to user's space
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {SpaceData[]} spaces All the spaces of user
 * @param {String} current Current space uuid of user
 * @returns {InterfaceSliderData}
 * @method
 */
export const sliderRearrangementFunc = (spaces, current) => {
    let limit = 4;

    const selected = spaces.filter((item => {
        if (item.space_uuid == current) {
            return item;
        }
    }));

    const allSpacesExceptSelf = spaces.filter((item => {
        if (item.space_uuid != current) { // regular + vip removed (&& item.is_vip_space == 0)
            return item;
        }
    }));

    const sortedSpaces = allSpacesExceptSelf.sort((a, b) => {
        if (a.users_count > b.users_count)
            return -1;
        if (a.users_count < b.users_count)
            return 1;
        return 0;
    });

    const trimmedSpacesData = sortedSpaces.slice(0, limit);
    const spacesLength = sortedSpaces.length;

    let total = Math.floor(spacesLength / limit);

    const roundOff = (spacesLength % limit)

    if (total >= 1) {
        if (roundOff > 0) {
            total = total + 1;
        }
    } else {
        if (roundOff > 0) {
            total = 1;
        }
    }

    let newSpaces = [...selected];
    let i = 0;

    for (const trimmedSpacesDatum of trimmedSpacesData) {
        if (i % 2 === 0) {
            newSpaces.push(trimmedSpacesDatum);
        } else {
            newSpaces.unshift(trimmedSpacesDatum);
        }
        i++;
    }
    return {
        spaces: newSpaces,
        maxPage: total,
    };

}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To apply the pagination of the spaces list
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {SpaceData[]} spaces All the spaces of user
 * @param {SpaceData} current Current space of user
 * @param {Number} pageNo Current page number
 * @returns {InterfaceSliderData}
 * @method
 */
export const spacesPaginationFetch = (spaces, current, pageNo) => {
    let limit = 4;
    const selected = spaces.filter((item => {
        if (item.space_uuid == current) {
            return item;
        }
    }));
    const allSpacesExceptSelf = spaces.filter((item => {
        if (item.space_uuid != current) { // regular + vip removed (&& item.is_vip_space == 0)
            return item;
        }
    }));

    const sortedSpaces = allSpacesExceptSelf.sort((a, b) => {
        if (a.users_count > b.users_count)
            return -1;
        if (a.users_count < b.users_count)
            return 1;
        return 0;
    });

    const lowerLimit = (pageNo - 1) * limit;

    const upperLimit = lowerLimit + limit;

    const trimmedSpacesData = sortedSpaces.slice(lowerLimit, upperLimit);

    const spacesLength = sortedSpaces.length;

    let total = Math.floor(spacesLength / limit);
    const roundOff = (spacesLength % limit)
    if (total >= 1) {
        if (roundOff > 0) {
            total = total + 1;
        }
    } else {
        if (roundOff > 0) {
            total = 1;
        }
    }


    let newSpaces = [];
    if (lowerLimit === 0) {
        newSpaces = [...selected];
    }
    let i = 0;

    for (const trimmedSpacesDatum of trimmedSpacesData) {
        if (i % 2 === 0) {
            newSpaces.push(trimmedSpacesDatum);
        } else {
            newSpaces.unshift(trimmedSpacesDatum);
        }
        i++;
    }
    return {
        spaces: newSpaces,
        maxPage: total,
    };
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the space user count came from socket in specific format
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {SpaceData}  targetSpace Target space
 * @param {SpaceData[]} updatedSpaces All updated spaces
 * @returns {SpaceData}
 * @method
 */
export const findAndUpdateSpaceUserCount = (targetSpace, updatedSpaces) => {
    updatedSpaces.forEach(updatedSpace => {
        if (updatedSpace.spaceId && updatedSpace.spaceId === targetSpace.space_uuid) {
            targetSpace.users_count = updatedSpace.usersCount;
        }
    })
    return targetSpace;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Search the user from the conversations
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Number} userId Id of user
 * @param {ConversationData[]} conversations  All the conversations of current space Conversations data from user will be searched
 * @returns {Object}
 * @method
 */
export const getUserFromUserId = (userId, conversations) => {
    let fromUser = null;
    let fromUserConversationIndex = -1;
    conversations.forEach((val, i) => {
        const user = !_.isEmpty(val.conversation_users) && val.conversation_users.find((el) => {
            return el.user_id === parseInt(userId);
        })
        if (user) {
            fromUser = user
            fromUserConversationIndex = i
        }
    });
    return {fromUser: fromUser, fromUserConversationIndex: fromUserConversationIndex};
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To remove the user from the dashboard by removing its conversation data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Number} userId Id of user
 * @param {ConversationData[]} conversations  All the conversations of current space Conversations data from user will
 * be removed
 * @returns {ConversationData[]}
 * @method
 */
export const removeUserFromDashboard = (userId, conversations) => {
    // removing conversations in which user id is as single
    conversations = conversations.filter((val) => {
        if (isUserExistsInConversation(val, userId)) {
            return val.conversation_users.length !== 1
        }
        return true;
    });
    // removing user from the conversation users
    conversations = conversations.map((val, i) => {
        val.conversation_users = val.conversation_users.filter((el) => el.user_id !== parseInt(userId));
        return val;
    });
    return conversations;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Add user to another conversation and remove user from current conversation
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {UserBadge} user User object to be moved to new conversation
 * @param {Number} toUserId Target user id where user will be join
 * @param {ConversationData[]} conversations  All the conversations of current space
 * @param {String} newConversationId New Conversation ID
 * @returns {ConversationData[]}
 * @method
 */
export const addUserByAnotherUserId = (user, toUserId, conversations, newConversationId = null) => {
    return conversations.map((val, i) => {
        // finding to user is in this conversation or not
        const toUser = !_.isEmpty(val.conversation_users) && val.conversation_users.find((el) => {
            return el.user_id === toUserId
        })
        if (toUser) {// if found `to user` conversation add `from user` to it
            val.conversation_users.push(user); // appending from user to `toUserId` conversation
            if (newConversationId) { // if toUser Conversation needs to update like in toUser as single user case
                val.conversation_uuid = newConversationId;
            }
        }
        return val;
    });
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To remove the user from current conversation
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Number} userId User id of target user
 * @param {ConversationData} activeConversation User's current conversation
 * @returns {ConversationData[]}
 * @method
 */
export const removeUserFromActiveConversation = (userId, activeConversation) => {
    if (activeConversation && activeConversation.conversation_users) {
        activeConversation.conversation_users = activeConversation.conversation_users.filter((el) => el?.user_id !== parseInt(userId));
    }
    return activeConversation
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To check if the target user exists in current user conversation or not
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ConversationData} conversation Conversation to check
 * @param {Number} userId User id of target user
 * @returns {ConversationData[]}
 * @method
 */
export const isUserExistsInConversation = (conversation, userId) => {
    return !_.isEmpty(conversation.conversation_users) && conversation.conversation_users.find((user) => {
        return user.user_id === parseInt(userId);
    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To search the conversation from the all the conversations
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ConversationData[]} conversations  All the conversations of current space
 * @param {String} id Conversation uuid
 * @returns {ConversationData[]}
 * @method
 */
export const getConversationById = (conversations, id) => {
    if (conversations) {
        const convert = conversations.filter((conversation) => {
            if (conversation.conversation_uuid === id) {
                return conversation;
            }
        });
        return convert[0];
    } else {
        return conversations;
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To remove the conversation from the conversation array
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ConversationData[]} conversations  All the conversations of current space
 * @param {String} id Conversation uuid
 * @returns {ConversationData[]}
 * @method
 */
export const deleteConversationById = (conversations, id) => {
    return conversations.filter((conversation) => {
        return conversation.conversation_uuid !== id;
    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To sort the single user's conversation where user count in conversation is one
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ConversationData} data User's conversations array to sort
 * @returns {UserBadge[]}
 * @method
 */
export const sortSingleUser = (data) => {
    const teamVip = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users) ? item.conversation_users[0] : {};
        if (val.event_role == 1 && val.is_vip) {
            return item;
        }
    });

    const expertVip = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users) ? item.conversation_users[0] : {};
        if (val.event_role == 2 && val.is_vip) {
            return item;
        }
    });

    const simpleTeam = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users) ? item.conversation_users[0] : {};
        if (val.event_role == 1 && !val.is_vip) {
            return item;
        }
    });

    const simpleExpert = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users) ? item.conversation_users[0] : {};
        if (val.event_role == 2 && !val.is_vip) {
            return item;
        }
    });

    const simpleVip = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users) ? item.conversation_users[0] : {};
        if (val.event_role == 0 && val.is_vip) {
            return item;
        }
    });


    const simpleUser = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users) ? item.conversation_users[0] : {};
        if (val.event_role == 0 && !val.is_vip) {
            return item;
        }
    });

    return [...teamVip, ...expertVip, ...simpleTeam, ...simpleExpert, ...simpleVip, ...simpleUser]

}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To sort the conversations by conversation users count in them
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ConversationData[]} conversations  All the conversations of current space
 * @returns {ConversationData[]}
 * @method
 */
export const reArrangeConversations = (conversations) => {
    conversations = conversations.filter(conversation => conversation?.conversation_users?.length);

    const sortedSingleUsers = conversations.filter((val) => {
        if (!_.isEmpty(val.conversation_users) && val.conversation_users.length == 1) {
            return val
        }
    });

    // taking the single users
    const singleUserConversations = sortSingleUser(sortedSingleUsers);
    let result = [...singleUserConversations];
    // variable to hold the number of user's conversation to be fetched from conversations array
    let conversationUsers = 2;

    let maxConversationUsers = Constants.CONVERSATION_MAX + 1; // 1 for space host

    // here all the conversations having users count above 1 will be pushed in result in ascending order
    while (result.length < conversations.length) {
        let filteredUsers = conversations.filter((val) => {
            if (!_.isEmpty(val.conversation_users) && val.conversation_users.length === conversationUsers) {
                return val;
            }
        });
        result= [...result, ...filteredUsers];
        if(conversationUsers > maxConversationUsers) {
            // putting the threshold to break the loop if it executed more than expected
            break;
        }
        conversationUsers += 1;
    }
    return result;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To add the user to active conversation
 * this will also handle if user already in active conversation then will leave as it is
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ConversationData} activeConversation active conversation object
 * @param {UserBadge} userObj user object
 * @method
 */
export const addUserToActiveConversation = (activeConversation, userObj) => {
    if (activeConversation && userObj && !isUserExistsInConversation(activeConversation, userObj.user_id)) {
        activeConversation.conversation_users.push(userObj);
    }
    return activeConversation;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To add the user object in provided conversation and update the all conversations array
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {UserBadge} user User data of target user
 * @param {Number} conversationId Conversation uuid
 * @param {ConversationData[]} conversations  All the conversations of current space
 * @returns {ConversationData[]}
 * @method
 */
export const addUserToExistingConversation = (user, conversationId, conversations) => {
    let flag = true; // using flag system
    conversations = conversations.map((val, i) => {
        if (val.conversation_uuid === conversationId) {
            val.conversation_users.push(user);
            flag = false;
        }
        return val;
    });

    if (flag) {
        // if user is in conversation and all users close the browser then we must append new conversation if no conversation found
        conversations.push(
            {
                "conversation_uuid": conversationId,
                "conversation_type": "active",
                "conversation_users": [user],
                "is_conversation_private": 0
            }
        );
    }
    return conversations;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the conversation state for updating the private state of conversation
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {SpaceData} spaceData Space data to update the conversation
 * @param {ConversationData} payload Conversation to merge in space data
 * @returns {ConversationData[]}
 * @method
 */
export const updateConversationState = (spaceData, payload) => {
    const oldConversation = spaceData.current_space_conversations
    const conversations = oldConversation.map((item) => {
        if (item.conversation_uuid && item.conversation_uuid == payload.conversationId) {
            return {
                ...item,
                is_conversation_private: payload.is_private
            }
        } else {
            return item
        }
    });
    return conversations;
}
/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the current joined conversations data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {SpaceData} spaceData New Space data
 * @param {Number} payload Private state of current conversation
 * @returns {ConversationData}
 * @method
 */
export const conversationUpdate = (spaceData, payload) => {
    return {
        ...spaceData.current_joined_conversation,
        is_conversation_private: payload.current_state
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the active conversation state
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {UserBadge} user User data of target user
 * @param {Number} toUserId User id of target user
 * @param {ConversationData[]} conversations  All the conversations of current space
 * @param {String} newConversationId Conversation id of new conversation
 * @param {ConversationData} activeCon Conversation Data for current conversation
 * @returns {ConversationData}
 * @method
 */
export const updateActiveConversation = (user, toUserId, conversations, newConversationId, activeCon) => {
    if (newConversationId && newConversationId == activeCon.conversation_uuid) {
        const flag = !_.isEmpty(activeCon.conversation_users) && activeCon.conversation_users.find((el) => {
            return el.user_id === user.user_id
        });
        if (_.isEmpty(flag)) {
            activeCon.conversation_users.push(user);
        }
    }

    return activeCon;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description to check if host is available or not in conversations
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Number} id Id of space host
 * @param {Object} state Current redux object.
 * @returns {Boolean}
 * @method
 */
export const checkHost = (id, state) => {
    const {interfaceSpaceHostData} = state;

    if (!_.isEmpty(interfaceSpaceHostData)) {
        const hostData = interfaceSpaceHostData[0];
        return (id == hostData.user_id)
    } else {
        return false;
    }

}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the current conversation state on grid
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {SpaceData} spaceData Current space data
 * @param {ConversationData} payload Target conversation to stored in space data
 * @returns {ConversationData}
 * @method
 */
export const updatePrivateInGrid = (spaceData, payload) => {
    const oldConversation = spaceData.current_space_conversations
    const conversations = oldConversation.map((item) => {
        if (item.conversation_uuid && item.conversation_uuid == payload.conversation_uuid) {
            return {
                ...item,
                is_conversation_private: payload.current_state
            }
        } else {
            return item
        }
    });
    return conversations;
}
