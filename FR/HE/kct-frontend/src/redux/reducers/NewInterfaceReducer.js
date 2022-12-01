/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file provides the reducer methods for the application related reducer actions
 * ---------------------------------------------------------------------------------------------------------------------
 */

import {KeepContact as KCT} from '../types';
import userCases from './ActionCases/userCases.js';
import conversationCases from './ActionCases/conversationCases';
import spacesCases from './ActionCases/spaceCases';
import eventCases from './ActionCases/eventCases';
import paginationCases from './ActionCases/paginationCases';
import Constants from "../../Constants";
// import {mainHostUpdate,updateConversationState,conversationUpdate} from '../utils/common.js';
const initialState = {
    zoomSdkState: {
        isInitialized: false,
    },
    interfaceBadgeData: {},
    interfaceTagData: {},
    interfaceGraphics: {},
    interfaceEventData: {},
    interfaceSpacesData: {
        current_space_conversations: [],
    },
    userMediaDevice: {},
    interfaceAuth: {},
    interfaceSliderData: {},
    spacesDataLoad: false,
    callJoinState: {
        valid: false,
        data: {},
    },
    privateCallJoinState: {
        valid: false,
        data: {},
    },
    makingCall: false,
    interfaceSpaceHostData: [],
    mainHostState: [],
    availabilityHost: false,
    userInfoData: {},
    singleUserData: {},
    calledUserId: null,
    isPrivate: 0,
    langChange: "EN",
    gridPagination: {
        current_page: 1,
        totalpages: 1,
        currentPageData: []
    },
    conversationMeta: {
        mute: false,
        fullScreen: false,
    },
    gridMeta: {
        visible: true,
    },
    contentManagementMeta: {
        componentVisibility: 0,
        currentMediaType: null,
        currentMediaData: {},
        isZoomMute: false,
        zoomJoinedUsers: [],
        isVideoPlayerLoaded: true,
        showMuteButtonText: false,
        preRecordedVolume: 50,
    },
    testAudioUrl: null,
    selectedBgOption: {
        type: Constants.CHIME_BG.TYPE.NONE,
        value: null,
    },
}


export default function NewInterfaceReducer(state = initialState, action) {

    state = userCases(state, action);
    state = conversationCases(state, action);
    state = spacesCases(state, action);
    state = eventCases(state, action);
    state = paginationCases(state, action);


    switch (action.type) {
        //data get set
        case KCT.NEW_INTERFACE.SET_SPACE_HOST_DATA://SpaceHost
            state = {
                ...state,
                interfaceSpaceHostData: action.payload,
            }
            break;

        case KCT.NEW_INTERFACE.SET_USER_INFO_DATA://user info
            state = {
                ...state,
                userInfoData: action.payload,
            }
            break;

        case KCT.NEW_INTERFACE.SET_SINGLE_USER_DATA://singleuser data
            state = {
                ...state,
                singleUserData: action.payload,
            }
            break;
        case KCT.NEW_INTERFACE.PRIVATE_CONVERSATION:
            state = {
                ...state,
                privateCallJoinState: action.payload,

            }
            break;
        //update conversation type.
        case KCT.NEW_INTERFACE.UPDATE_CONVERSATION_TYPE:
            const updateConversation = conversationUpdate(state.interfaceSpacesData, action.payload);
            const gridData = updatePrivateInGrid(state.interfaceSpacesData, action.payload);
            state = {
                ...state,
                interfaceSpacesData: {
                    ...state.interfaceSpacesData,
                    current_joined_conversation: updateConversation,
                    current_space_conversations: gridData,
                },
            }
            break;
        case KCT.NEW_INTERFACE.SET_MAIN_HOST:
            state = {
                ...state,
                mainHostState: action.payload
            }
            break;
        case KCT.NEW_INTERFACE.SET_CALL_STATUS:
            state = {
                ...state,
                makingCall: action.payload
            }
            break;
        case KCT.NEW_INTERFACE.ASK_TO_JOIN:
            state = {
                ...state,
                callJoinState: action.payload
            }
            break;
        case KCT.NEW_INTERFACE.RESET_SPACE:
            state = {
                ...state,
                spacesDataLoad: false
            }
            break;
        case KCT.NEW_INTERFACE.SET_USER_AUTH:
            state = {
                ...state,
                interfaceAuth: action.payload
            }
            break;
        case KCT.NEW_INTERFACE.SET_BADGE:
            state = {
                ...state,
                interfaceBadgeData: action.payload,
            }
            break;
        case KCT.NEW_INTERFACE.SET_TAGS:
            state = {
                ...state,
                interfaceTagData: action.payload,
            }
            break;
        case KCT.NEW_INTERFACE.SET_GRAPHICS:
            state = {
                ...state,
                interfaceGraphics: action.payload,
            }
            break;
        case KCT.NEW_INTERFACE.SET_EVENT_DATA:
            state = {
                ...state,
                interfaceEventData: {...state.interfaceEventData, ...action.payload,},
            }
            break;
        case KCT.NEW_INTERFACE.CHANGE_MEDIA_DEVICE: {
            state = {
                ...state,
                userMediaDevice: action.payload
            }
        }
            break;







        // conference 
        case KCT.NEW_INTERFACE.CONFERENCE_UPDATE://MainHost

            let event_data = mainHostUpdate(state.interfaceEventData, action.payload);
            state = {
                ...state,
                interfaceEventData: event_data
            }

            break;
        // zoom cases
        case KCT.NEW_INTERFACE.ZOOM_INITIALIZE_UPDATE :
            state = {
                ...state,
                zoomSdkState: {
                    ...state.zoomSdkState,
                    isInitialized: action.payload,
                }
            }
            break;
        // conversation cases
        // zoom cases
        case KCT.NEW_INTERFACE.CONVERSATION_MUTE_UPDATE :
            state = {
                ...state,
                conversationMeta: {
                    ...state.conversationMeta,
                    mute: action.payload,
                }
            }
            break;
        case KCT.NEW_INTERFACE.CONV_FULL_SCREEN :
            state = {
                ...state,
                conversationMeta: {
                    ...state.conversationMeta,
                    fullScreen: action.payload,
                }
            }
            break;
        case KCT.NEW_INTERFACE.GRID_VISIBILITY_UPDATE:
            state = {
                ...state,
                gridMeta: {
                    ...state.gridMeta,
                    visible: action.payload,
                }
            }
            break;
        case KCT.NEW_INTERFACE.CONTENT_MANAGE_DATA:
            state = {
                ...state,
                contentManagementMeta: {
                    ...state.contentManagementMeta,
                    currentMediaType: action.payload.currentMediaType,
                    currentMediaData: action.payload.currentMediaData,
                }
            }
            break;
        case KCT.NEW_INTERFACE.ZOOM_USER_ADMIT:
            let joinedUsers = state.contentManagementMeta.zoomJoinedUsers;
            joinedUsers.push(action.payload);
            state = {
                ...state,
                contentManagementMeta: {
                    ...state.contentManagementMeta,
                    zoomJoinedUsers: joinedUsers,
                }
            }
            break;

        case KCT.NEW_INTERFACE.CHANGE_LANGUAGE:
            state = {
                ...state,
                langChange: action.payload
            }
            break;
        case KCT.NEW_INTERFACE.PRERECORED_VOLUME:
            state = {
                ...state,
                contentManagementMeta: {
                    ...state.contentManagementMeta,
                    preRecordedVolume: action.payload
                }
            }
            break;

        case KCT.NEW_INTERFACE.VIDEO_PLAYER_STARTED:
            state = {
                ...state,
                contentManagementMeta: {
                    ...state.contentManagementMeta,
                    isVideoPlayerLoaded: action.payload
                }
            }
            break;
        case KCT.NEW_INTERFACE.UPDATE_VIDEO_MUTE_TEXT:
            state = {
                ...state,
                contentManagementMeta: {
                    ...state.contentManagementMeta,
                    showMuteButtonText: action.payload
                }
            }
            break;
        case KCT.NEW_INTERFACE.CONTENT_MUTE_UPDATE:
            state = {
                ...state,
                contentManagementMeta: {
                    ...state.contentManagementMeta,
                    isZoomMute: action.payload,
                }
            }
            break;
        case KCT.NEW_INTERFACE.CONTENT_COMPONENT_VISIBILITY:
            state = {
                ...state,
                contentManagementMeta: {
                    ...state.contentManagementMeta,
                    componentVisibility: action.payload
                }
            }
            break;

        case KCT.NEW_INTERFACE.UPDATE_TEST_AUDIO_URL:
            state = {
                ...state,
                testAudioUrl: action.payload,
            }
            break;
        case KCT.NEW_INTERFACE.SELECTED_BG_OPTION:
            state = {
                ...state,
                selectedBgOption: action.payload,
            }
            break;

        default:
            break;
    }
    return state;
}

const mainHostUpdate = (event_data, payload) => {
    let updatedData = event_data;
    if (payload.status) {
        const {conf_api_key, conf_user_name, conf_meeting_id, conf_user_email} = payload;
        updatedData = {
            ...updatedData,
            conf_user_name: conf_user_name,
            conf_meeting_id: conf_meeting_id,
            embedded_url: payload.embedded_url,
            conf_user_email: conf_user_email,
            conf_api_key: conf_api_key
        }

    } else {
        updatedData = {
            ...updatedData,
            embedded_url: null,
        }
        delete updatedData.embedded_url;
        delete updatedData.conf_api_key;
        delete updatedData.conf_meeting_id;
        delete updatedData.conf_user_email;
        delete updatedData.conf_user_name;
    }
    return updatedData;
}


const conversationUpdate = (spaceData, payload) => {
    return {
        ...spaceData.current_joined_conversation,
        is_conversation_private: payload.current_state
    }
}

const updatePrivateInGrid = (spaceData, payload) => {
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

