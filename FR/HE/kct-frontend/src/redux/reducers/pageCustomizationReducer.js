/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file contains old reducer actions but some actions are remain same so they are being used from here
 * ---------------------------------------------------------------------------------------------------------------------
 */
import {KeepContact as KCT} from "../types";

const initialState = {
    isLoginLoading: false,
    initData: {graphics_data: {video_url: null}},
    userMediaDevice: {},
    isOnlineDataReceived: false,
    currentConversationuuid: '',
    page_customization: {
        event_uuid: "de10bec2-c74f-11ea-aeff-b82a72a009b4",
        embedded_url: "",
        event_title: "First Virtual Event",
        event_header_text: "First Virtual Event Header Text",
        event_description: null,
        event_date: "2020-07-19",
        event_start_time: "11:30:00",
        event_end_time: "11:41:00",
        event_address: "delhi",
        event_city: "Paris",
        event_image: "",
        event_status: {
            is_future: false,
            is_before_space_open: false,
            is_during: false,
            is_after_space_open: false,
            is_past: false
        },
        page_customisation: {
            keepContact_page_title: "new title here",
            keepContact_page_description:
                "descriptiondescriptescriptiondescriptiondescription",
            keepContact_page_logo:
                "http://projectdevzone.com/keepcontact/images/afnor-logo.png",
            website_page_link: "https://google.com",
            twitter_page_link: "https://google.com",
            linkedIn_page_link: "https://google.com",
            facebook_page_link: "https://google.com",
            instagram_page_link: "https://google.com",
        },
        graphics_setting: {
            main_background_color: {
                r: 200,
                g: 120,
                b: 123,
            },
            hover_border_color: {
                r: 200,
                g: 120,
                b: 123,
            },
            texts_color: {
                r: 200,
                g: 120,
                b: 123,
            },
            keepContact_color_1: {
                r: 200,
                g: 120,
                b: 123,
                a: 0.45,
            },
            keepContact_color_2: {
                r: 200,
                g: 120,
                b: 103,
                a: 0.45,
            },
            keepContact_background_color_1: {
                r: 200,
                g: 120,
                b: 123,
                a: 0.45,
            },
            keepContact_background_color_2: {
                r: 200,
                g: 120,
                b: 123,
                a: 0.45,
            },
            keepContact_selected_space_color: {
                r: 200,
                g: 120,
                b: 123,
                a: 0.45,
            },
            keepContact_unselected_space_color: {
                r: 200,
                g: 180,
                b: 121,
                a: 0.45,
            },
            keepContact_closed_space_color: {
                r: 200,
                g: 120,
                b: 123,
                a: 0.45,
            },
            keepContact_text_space_color: {
                r: 200,
                g: 120,
                b: 123,
                a: 0.45,
            },
            keepContact_names_color: {
                r: 200,
                g: 120,
                b: 123,
                a: 0.45,
            },
            keepContact_thumbnail_color: {
                r: 200,
                g: 120,
                b: 123,
                a: 0.45,
            },
            keepContact_countdown_background_color: {
                r: 200,
                g: 120,
                b: 123,
                a: 0.45,
            },
            keepContact_countdown_text_color: {
                r: 200,
                g: 120,
                b: 123,
                a: 0.45,
            },
        },
        section_text: {
            reply_text: "Reply textsfakjjlakkjlkjsjdwefase af",
            keepContact_section_line1:
                "line Reply textsfakjjlakkjlkjsjdwefase afLine",
            keepContact_section_line2:
                "line Reply textsfakjjlakkjlkjsjdwefase afLine",
        },
    },
    event_space: {
        status: true,
        data: {
            active_space: {
                space_uuid: "abc",
                space_name: "Anurag singhkkkkkk",
                space_short_name: "Chhotu Singh",
                space_mood: "Cool its very coool",
                max_capacity: 12,
                space_image_url: "www.image.com",
                space_icon_url: "www.icon.com",
                is_vip_space: 0,
                opening_hours: "12:34",
                event_uuid: 5,
                tags: "Itss a partyy",
                is_open: false,
            },

            conversations: [
                {
                    conversation_uuid: "",
                    conversation_members: [
                        {
                            user_id: "",
                            user_full_name: "abc def",
                            user_image: "",
                        },
                        {
                            user_id: "",
                            user_full_name: "abc def",
                            user_image: "",
                        },
                        {
                            user_id: "",
                            user_full_name: "abc def",
                            user_image: "",
                        },
                        {
                            user_id: "",
                            user_full_name: "abc def",
                            user_image: "",
                        },
                    ],
                },
                {
                    conversation_uuid: "",
                    conversation_members: [
                        {
                            user_id: "",
                            user_full_name: "mukesh bitlani",
                            user_image: "",
                        },
                        {
                            user_id: "",
                            user_full_name: "abc def",
                            user_image: "",
                        },
                        {
                            user_id: "",
                            user_full_name: "abc def",
                            user_image: "",
                        },
                    ],
                },
                {
                    conversation_uuid: "",
                    conversation_members: [
                        {
                            user_id: "",
                            user_full_name: "mukesh bitlani",
                            user_image: "",
                        },
                    ],
                },
            ],
        },
        spaces: [
            {
                space_uuid: "abc",
                space_name: "Anursdfsd dfsf",
                space_short_name: "dsfdfdggsddsgdgsg",
                space_mood: "fgggggggggggggggggdd",
                max_capacity: 15,
                space_image_url:
                    "http://projectdevzone.com/keepcontact/images/video-dummy-img.jpg",
                space_icon_url: null,
                is_vip_space: 1,
                opening_hours: "12:23",
                event_id: 5,
                tags: "fsfsfsffsffsfsffsfffsss",
            },
            {
                space_uuid: "",
                space_name: "Anurag singhkkkkkk",
                space_short_name: "Chhotu Singh",
                space_mood: "Cool its very coool",
                max_capacity: 12,
                space_image_url: "www.image.com",
                space_icon_url: "www.icon.com",
                is_vip_space: 0,
                opening_hours: "12:34",
                event_id: 5,
                tags: "Itss a partyy",
            },
            {
                space_uuid: "",
                space_name: "Ravi Pratap singh",
                space_short_name: "Ravi Pratap singh",
                space_mood: "Cool its very coool",
                max_capacity: 16,
                space_image_url: "www.imagdfdfe.com",
                space_icon_url: "www.icosfsfn.com",
                is_vip_space: 1,
                opening_hours: "11:45",
                event_id: 5,
                tags: "Itss a partyyyyxyz",
            },
            {
                space_uuid: "",
                space_name: "Priyanshu SIngh",
                space_short_name: "Priyanshu SIngh",
                space_mood: "Cool its very coool",
                max_capacity: 19,
                space_image_url: "www.imagdfdfe.com",
                space_icon_url: "www.icosfsfn.com",
                is_vip_space: 0,
                opening_hours: "10:25",
                event_id: 5,
                tags: "Itss a partyyyyxyz",
            },
        ],
    },
    event_contents: {
        status: true,
        data: [
            {
                event_title: "this is my title ",
                date: "20/10/2020",
                description: "this is descriptionn of this title",
                image: "http://projectdevzone.com/keepcontact/images/video-dummy-img.jpg"
            },
            {
                event_title: "this is my title ",
                date: "20/10/2020",
                description: "this is descriptionn of this title",
                image: "http://projectdevzone.com/keepcontact/images/video-dummy-img.jpg"
            },
            {
                event_title: "this is my title ",
                date: "20/10/2020",
                description: "this is descriptionn of this title",
                image: "http://projectdevzone.com/keepcontact/images/video-dummy-img.jpg"
            },
        ],
    }, my_events_list: {
        status: true,
        data: [
            {
                event_title: "space x",
                event_data: "",
                event_start_time: "10:00",
                event_end_time: "13:00",
                organiser_fname: "abc",
                organiser_lname: "Bitlani",
                is_presenter: 1,
                is_moderator: 0,
                is_host: 0,
                join_link: "http://google.com"
            },
            {
                event_title: "space x",
                event_data: "",
                event_start_time: "10:00",
                event_end_time: "13:00",
                organiser_fname: "Mukesh",
                organiser_lname: "Bitlani",
                is_presenter: 0,
                is_moderator: 1,
                is_host: 1,
                join_link: "http://google.com"
            },
            {
                event_title: "space x",
                event_data: "",
                event_start_time: "10:00",
                event_end_time: "13:00",
                organiser_fname: "xyz",
                organiser_lname: "Bitlani",
                is_presenter: 0,
                is_moderator: 0,
                is_host: 1,
                join_link: "http://google.com"
            },
            {
                event_title: "space x",
                event_data: "",
                event_start_time: "10:00",
                event_end_time: "13:00",
                organiser_fname: "Mukesh",
                organiser_lname: "Bitlani",
                is_presenter: 0,
                is_moderator: 0,
                is_host: 1,
                join_link: "http://google.com"
            },
        ]
    },
    event_badge: {
        status: true,
        data: {
            user_id: 1,
            user_full_name: "mukesh bitlani",
            user_picture: "",
            social_links: {
                facebook: "",
                linkedin: "",
                twitter: "",
                instagram: "",
                whatsapp: ""
            },
            entities: {
                company: {name: "martin", position: "CEO"},
                press: {name: "martin", position: "martin"},
                instance: {name: "martin", position: "martin"},
                unions: [
                    {name: "martin", position: "martin"},
                    {name: "martin", position: "martin"},
                    {name: "martin", position: "martin"},
                    {name: "martin", position: "martin"},
                    {name: "martin", position: "martin"},
                ]
            }
        }
    },
    event_design_settings: {},
    event_group_logo: null,
};


export function pageCustomizationReducer(state = initialState, action) {
    switch (action.type) {
        case KCT.EVENT.CHANGE_MEDIA_DEVICES: {
            return state = {
                ...state,
                userMediaDevice: action.payload
            }
        }

        case KCT.EVENT.SET_EVENT_DESIGN_SETTINGS: {
            return state = {
                ...state,
                event_design_settings: action.payload

            }
        }
        case KCT.EVENT.SET_EVENT_GROUP_LOGO: {
            return state = {
                ...state,
                event_group_logo: action.payload

            }
        }

        // to update the status of received online users list from socket
        case KCT.EVENT.CHANGE_ONLINE_USERS_IDENTIFIER: {

            return state = {
                ...state,
                isOnlineDataReceived: action.payload
            }
        }

        case KCT.EVENT.SET_EVENT_GRAPHICS: {
            return state = {...state, page_customization: action.payload}
        }
        case KCT.EVENT.SET_EVENT_SPACES: {
            return state = {
                ...state,
                event_space: action.payload
            }
        }
        case KCT.EVENT.ADD_EVENT_MEMBER_SINGLE: {
            const single = state.event_space.conversations.concat(action.payload)

            return state = {
                ...state, event_space: {
                    ...state.event_space,
                    conversations: single
                }
            }
        }
            break;
        case KCT.EVENT.ADD_EVENT_MEMBER_CONVERSATION: {
            const conversations = state.event_space.conversations.map((val, i) =>
                i === action.index ?
                    action.payload :
                    val
            )
            return state = {
                ...state, event_space: {
                    ...state.event_space,
                    conversations: conversations
                }
            }
        }
            break;

        case KCT.EVENT.SET_EVENT_URL: {
            const url = action.payload;
            return state = {
                ...state, page_customization: {
                    ...state.page_customization,
                    embedded_url: url
                }
            }
        }
            break;

        case KCT.EVENT.TOGGLE_DND: {
            const newState = action.payload;
            return state = {
                ...state,
                page_customization: {
                    ...state.page_customization,
                    auth: {
                        ...state.page_customization.auth,
                        active_state: newState
                    }
                }
            }

        }
            break;

        case KCT.EVENT.CHANGE_SPACE: {
            return state = {
                ...state, event_space: {
                    ...state.event_space,
                    conversations: action.payload.conversations,
                    single_users: action.payload.single_users,
                    active_space: action.payload.space
                }
            }
        }
        case KCT.EVENT.CHANGE_CONVERSATION_ID: {
            return state = {
                ...state, event_space: {
                    ...state.event_space,
                    active_conversation: action.payload
                }
            }
        }
        case KCT.EVENT.DELETE_CONVERSATION: {
            const conversations = state.event_space.conversations.map((val, i) => {
                    return val.conversation_uuid == action.payload.conversation_uuid ?
                        {...val, conversation_users: val.conversation_users.filter((item) => item.is_self !== 1)} :
                        val
                }
            )
            return state = {
                ...state, event_space: {
                    ...state.event_space,
                    conversations: conversations,
                    active_conversation: null
                }
            }
        }
        case KCT.EVENT.FILTER_ONLINE_MEMBER: {
            let onlineUsers = action.payload.currentSpaceOnlineUsers;
            let spacesOnlineUsersCount = action.payload.spacesUserCount;

            // from each conversation this will remove users which are not in online users id
            let conversations = state.event_space.conversations.map((val) => {
                    return {
                        ...val,
                        conversation_users: val.conversation_users.filter((item) => onlineUsers.indexOf(item.user_id) !== -1)
                    }
                }
            )

            // after that many conversation may left with no users;
            conversations = conversations.filter((val) => val.conversation_users.length > 0);

            const spaces = state.event_space.spaces.map((space) => {
                return findAndUpdateSpaceUserCount(space, spacesOnlineUsersCount);
            })

            return state = {
                ...state, event_space: {
                    ...state.event_space,
                    spaces: spaces,
                    conversations: conversations,
                }
            }
        }
        /*
        - This is called when someone start the conversation with self,
        - This method job is to update the second user (who started conversation with self user) position with current
         * payload: conversation response
         * userId: to which conversation started so we can replace that user conversation with new conversation
         */
        case KCT.EVENT.ADD_EVENT_MEMBER_BY_USER_ID: {
            const conversations = state.event_space.conversations
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
            return state = {
                ...state, event_space: {
                    ...state.event_space,
                    conversations: conversations,
                }
            }
        }
        case KCT.EVENT.UPDATE_INIT_DATA: {
            const data = action.payload
            return state = {
                ...state, initData: data
            }
        }

        case KCT.EVENT.UPDATE_INIT_NAME: {
            const data = action.payload
            return state = {
                ...state, initData: {
                    ...state.initData, auth: {...state.initData.auth, fname: data}
                }
            }
        }


        // ...state, event_space: {
        //   ...state.event_space,
        //   active_conversation: activeConversation
        // }

        /*
        * To update the user position when other user join a conversation
        * action will have
        * fromId
        * toId
        * conversationId
        */
        case KCT.EVENT.UPDATE_USER_CONVERSATION: {
            const fromId = action.payload.fromUserId;
            const toId = action.payload.toUserId;
            const conversationId = action.payload.conversationId;


            let conversations = state.event_space.conversations;
            let active_conversation = state.event_space.active_conversation;

            const userObj = getUserFromUserId(fromId, state.event_space.conversations);
            // remove user from current position
            conversations = removeUserFromDashboard(fromId, conversations);
            if (toId) { // like in disconnect case we will update user conversation but toId will be null
                conversations = addUserByAnotherUserId(userObj.fromUser, toId, conversations, conversationId);
            } else { // here means removing user, so need to also remove from active_conversation if in it
                active_conversation = removeUserFromActiveConversation(fromId, active_conversation)
            }
            return state = {
                ...state,
                event_space: {
                    ...state.event_space,
                    active_conversation: active_conversation,
                    conversations: conversations
                }
            }

        }
        case KCT.EVENT.ADD_NEW_EVENT_MEMBER: {
            let conversations = removeUserFromDashboard(action.payload.user.user_id, state.event_space.conversations);
            // here we will handle to add a new user to dashboard like
            // e.g. a new user connected so if previous conversation id given we will add him to existing
            // or create a new row with empty conversation
            if (action.payload.conversationId) {
                // new user already in conversation so add user to conversation by user id
                conversations = addUserToExistingConversation(action.payload.user, action.payload.conversationId, conversations);
            } else {
                // new user added with no conversation so add a new row
                const data = {
                    conversation_uuid: null,
                    conversation_type: 'single_user',
                    conversation_users: [
                        action.payload.user
                    ]
                }
                conversations.push(data);
            }
            return state = {
                ...state,
                event_space: {
                    ...state.event_space,
                    conversations: conversations,
                }
            }
        }


        case KCT.EVENT.UPDATE_EVENT_CONVERSATION: {
            let conversations = state.event_space.conversations
            let activeConversation = state.event_space.active_conversation;
            const conversation = getConversationById(conversations, action.payload.conversationId);
            if (conversation) {
                if (action.payload.type === 'delete') {
                    conversation.conversation_users.forEach((user) => {
                        if (!user.hasOwnProperty('is_self')) {
                            conversations.push({
                                conversation_uuid: null,
                                conversation_type: 'single_user',
                                conversation_users: [user]
                            })
                        }
                    })
                    if (state.event_space.active_conversation) {
                        if (action.payload.conversationId === state.event_space.active_conversation.conversation_uuid) {
                            activeConversation = null;
                        }
                    }
                    conversations = deleteConversationById(conversations, action.payload.conversationId);
                } else {
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
                }
            }
            return state = {
                ...state,
                event_space: {
                    ...state.event_space,
                    conversations: conversations,
                    active_conversation: activeConversation
                }
            }
        }
        // to add the user to active conversation if not already
        case KCT.EVENT.ADD_USER_TO_ACTIVE_CONVERSATION: {
            let activeConversation = state.event_space.active_conversation;
            const userObj = getUserFromUserId(action.payload, state.event_space.conversations);
            activeConversation = addUserToActiveConversation(activeConversation, userObj.fromUser);
            return state = {
                ...state, event_space: {
                    ...state.event_space,
                    active_conversation: activeConversation
                }
            }
        }

        case KCT.EVENT.UPDATE_SPACE_USERS_COUNT: {
            const spaces = state.event_space.spaces.map((space) => {
                return findAndUpdateSpaceUserCount(space, action.payload);
            })

            return state = {
                ...state,
                event_space: {
                    ...state.event_space,
                    spaces: spaces
                }
            }
        }

        default:
            break;
    }
    return state;
}

const getUserFromUserId = (userId, conversations) => {
    let fromUser = null;
    let fromUserConversationIndex = -1
    conversations.forEach((val, i) => {
        const user = val.conversation_users.find((el) => {
            return el.user_id === parseInt(userId);
        })
        if (user) {
            fromUser = user
            fromUserConversationIndex = i
        }
    });
    return {fromUser: fromUser, fromUserConversationIndex: fromUserConversationIndex};
}

const addUserToExistingConversation = (user, conversationId, conversations) => {
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
                "conversation_users": [user]
            }
        );
    }
    return conversations;
}

const addUserByAnotherUserId = (user, toUserId, conversations, newConversationId = null) => {
    return conversations.map((val, i) => {
        // finding to user is in this conversation or not
        const toUser = val.conversation_users.find((el) => {
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

const removeUserFromDashboard = (userId, conversations) => {
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


const isUserExistsInConversation = (conversation, userId) => {
    return conversation.conversation_users.find((user) => {
        return user.user_id === parseInt(userId);
    })
}

const removeUserFromConversation = (conversation, userId) => {
    conversation.conversation_users.filter((user) => {
        return user.user_id !== parseInt(userId);
    })
    return conversation;
}

const getConversationById = (conversations, id) => {
    if (conversations) {
        return conversations.find((conversation) => {
            return conversation.conversation_uuid === id;
        })
    } else {
        return conversations;
    }
}

const deleteConversationById = (conversations, id) => {
    return conversations.filter((conversation) => {
        return conversation.conversation_uuid !== id;
    })
}


const addUserToActiveConversation = (activeConversation, userObj) => {
    if (activeConversation && userObj && !isUserExistsInConversation(activeConversation, userObj.user_id)) {
        activeConversation.conversation_users.push(userObj);
    }
    return activeConversation;
}

const removeUserFromActiveConversation = (userId, activeConversation) => {
    if (activeConversation && activeConversation.conversation_users) {
        activeConversation.conversation_users = activeConversation.conversation_users.filter((el) => el.user_id !== parseInt(userId));
    }
    return activeConversation
}


const findAndUpdateSpaceUserCount = (targetSpace, updatedSpaces) => {
    updatedSpaces.forEach(updatedSpace => {
        if (updatedSpace.spaceId && updatedSpace.spaceId === targetSpace.space_uuid) {
            targetSpace.users_count = updatedSpace.usersCount;
        }
    })
    return targetSpace;
}


const printConversations = (conversations) => {
    let result = [];
    conversations.forEach((conversation) => {
        let userIds = [];
        conversation.conversation_users.forEach((user) => {
            userIds.push(user.user_id);
        })
        let users = userIds.join('---');
        result.push(`conversation ${conversation.conversation_uuid}, type: ${conversation.conversation_type} users: ${users}`);
    });
    return result;
}