/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file contains the user profile related util methods
 * ---------------------------------------------------------------------------------------------------------------------
 */

import _ from 'lodash';

/**
 * This module handles the profile reducer functions.
 * @module ProfileReducerUtils
 */

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function prepares updated profile data
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {UserBadge} profile Updated profile.
 * @param {UserBadge} item Current profile data
 * @returns {UserBadge}
 * @method
 **/
const prepareProfileData = (profile, item) => {
    return {
        ...profile,
        is_vip: item.is_vip,
        event_role: item.event_role,
        personal_tags: !_.isEmpty(profile.personal_tags) ? profile.personal_tags.filter((val) => val.is_moderated == 1) : [],
        professional_tags: !_.isEmpty(profile.professional_tags) ? profile.professional_tags.filter((val) => val.is_moderated == 1) : []
    };
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function checks user and performs action accordingly if needed.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {UserBadge} profile Updated profile.
 * @param {UserBadge} item Current profile data
 * @param {UserBadge} auth Auth user data
 * @returns {UserBadge}
 * @method
 **/
const profileUpdater = (profile, item, auth) => {
    if (profile.user_id == item.user_id) {
        // check if the updated user 
        if (auth.user_id == profile.user_id) {
            profile.is_self = 1;
        }
        return prepareProfileData(profile, item);
    } else {
        return item;
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function checks current conversation and updates profile inside current conversation.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {UserBadge} profile Updated profile.
 * @param {InterfaceSpaceData} spaceData Space data object
 * @param {UserBadge} auth Auth user data
 * @returns {ConversationData}
 * @method
 **/
const activeConversationProfileUpdate = (spaceData, profile, auth) => {
    const conversation_users = spaceData.current_joined_conversation.conversation_users.map((item) => {
        //  check if the user is the one we need to update in case of active conversation.
        return profileUpdater(profile, item, auth);
    });

    return {
        ...spaceData.current_joined_conversation,
        conversation_users: conversation_users,
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function checks non active conversations and updates profile there.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {UserBadge} profile Updated profile.
 * @param {InterfaceSpaceData} spaceData Space data object
 * @param {UserBadge} auth Auth user data
 * @returns {ConversationData[]}
 * @method
 **/
const normalConversationProfileUpdate = (spaceData, profile, auth) => {
    return spaceData.current_space_conversations.map(item => {
        const conversation_users = item.conversation_users.map(val => {
            return profileUpdater(profile, val, auth);
        });
        return {
            ...item,
            conversation_users: conversation_users
        }
    });
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function checks type of conversation calls the update handlers
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {UserBadge} profile Updated profile.
 * @param {InterfaceSpaceData} spaceData Space data object
 * @param {UserBadge} auth Auth user data
 * @returns {InterfaceSpaceData}
 * @method
 **/
export const profileDataUpdate = (spaceData, profile, auth) => {
    let conversation = null;
    if (spaceData.current_joined_conversation) {
        conversation = activeConversationProfileUpdate(spaceData, profile, auth);
    }

    let allConversations = normalConversationProfileUpdate(spaceData, profile, auth);

    return {
        ...spaceData,
        current_joined_conversation: conversation,
        current_space_conversations: allConversations,
    }

}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function checks and updates profile for space host.
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {UserBadge} profile Updated profile.
 * @param {Object} state Redux state
 * @returns {Object}
 * @method
 **/
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
 * @description To update the user data from the conversation
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ConversationData[]} conversations Current space conversations to find the user from
 * @param {UserBadge} userNewData New data of user to put in conversations users data
 * @param {UserBadge} selfUser Old user data by which to find the user from conversations
 * @returns {ConversationData[]}
 * @method
 */
export const updateUserDataFromConversations = (conversations, userNewData, selfUser) => {

    return conversations.map(conversation => {
        conversation.conversation_users = conversation.conversation_users.map(val => {
            return profileUpdater(userNewData, val, selfUser);
        });
        return conversation;
    });
}