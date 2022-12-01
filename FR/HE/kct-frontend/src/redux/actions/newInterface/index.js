/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file provides the dashboard page related api's redux action dispatcher for triggering the api from
 * the newInterface
 * ---------------------------------------------------------------------------------------------------------------------
 */

import KeepContactagent from "../../../agents/KeepContactagent";
import {KeepContact as KCT} from '../../types';
import socketManager from '../../../socket/socketManager.js';
import configureStore from '../../rootreducer.js';
import Helper from '../../../Helper.js';
import _ from 'lodash';

const deleteConversation = (id) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.DELETE_CONVERSATIONS,
        payload: id,
    });
}

const addUserToActiveCon = (id) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.ADD_USER_TO_ACTIVE_CONVERSATIONS,
        payload: id,
    });
}

const setUserMediaDevices = (data) => dispatch => {
    return dispatch({
        type: KCT.EVENT.CHANGE_MEDIA_DEVICES,
        payload: data,
    });
}


const setBadgeData = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.SET_BADGE,
        payload: data,
    });
}
const setTagsData = (data) => dispatch => {

    return dispatch({
        type: KCT.NEW_INTERFACE.SET_TAGS,
        payload: data,
    });
}

const setInterfaceGraphics = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.SET_GRAPHICS,
        payload: data,
    });
}

const setEventData = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.SET_EVENT_DATA,
        payload: data,
    });
}

const setSpacesData = (data) => dispatch => {
    dispatch({
        type: KCT.NEW_INTERFACE.SET_SPACES_DATA,
        payload: data,
    });
}

const updateSpacesData = (data) => dispatch => {
    dispatch({
        type: KCT.NEW_INTERFACE.UPDATE_SPACES_DATA,
        payload: data,
    });
}

//user info for check vip user
const setUserInfoData = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.SET_USER_INFO_DATA,
        payload: data,

    });
}

const setVisibility = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.SET_VISIBILITY_DATA,
        payload: data,
    });
}

const setVisible = (data) => dispatch => {
    return KeepContactagent.Event.setVisibility(data);
}

const updateProfile = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.UPDATE_CONVERSATION_PROFILE,
        payload: data,
    });
}

const updateProfileTrigger = (data, event_uuid) => dispatch => {
    const newBadgeData = {
        ...data,
        user_lname: data.visibility.user_lname == 1 ? data.user_lname : '',
        company: data.visibility.company == 1 ? data.company : {},
        unions: data.visibility.unions == 1 ? data.unions : [],
        personal_info: {
            ...data.personal_info,
            field_1: data.visibility.p_field_1 == 1 ? data.personal_info.field_1 : '',
            field_2: data.visibility.p_field_2 == 1 ? data.personal_info.field_2 : '',
            field_3: data.visibility.p_field_3 == 1 ? data.personal_info.field_3 : '',
        },

    };
    delete newBadgeData.is_self;
    const badgeData = {
        user: newBadgeData,
        namespace: Helper.getNameSpace(),
        eventId: event_uuid,

    }
    socketManager.emitEvent.PROFILE_UPDATE(badgeData);
}

const filterOnline = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.FILTER_ONLINE_MEMBERS,
        payload: data,
    });
}

const askToJoin = (data) => dispatch => {
    const joinData = {
        valid: true,
        data: data
    };

    return dispatch({
        type: KCT.NEW_INTERFACE.ASK_TO_JOIN,
        payload: joinData
    });
}
const turnOffJoin = () => dispatch => {
    const joinData = {
        valid: false,
        data: {}
    };

    return dispatch({
        type: KCT.NEW_INTERFACE.ASK_TO_JOIN,
        payload: joinData
    });
}

//Isolation conversation
const askToPrivateConversation = (data) => dispatch => {
    const joinData = {
        valid: true,
        data: data,
    };
    const newData = {
        conversation_uuid: data.conversationId,
        current_state: data.is_private
    }
    updatePrivateConversation(newData);
    if (data.is_private) {
        return dispatch({
            type: KCT.NEW_INTERFACE.PRIVATE_CONVERSATION,
            payload: joinData
        });
    }

}

const askToPrivateConversation2 = (data) => dispatch => {

    return dispatch({
        type: KCT.NEW_INTERFACE.HANDLE_PRIVATE_CONVERSATION,
        payload: data
    });
}

const privateTurnOffJoin = () => dispatch => {
    const joinData = {
        valid: false,
        data: {}
    };

    return dispatch({
        type: KCT.NEW_INTERFACE.PRIVATE_CONVERSATION,
        payload: joinData
    });
}

const updatePrivateConversation = (data) => dispacth => {
    return dispacth({
        type: KCT.NEW_INTERFACE.UPDATE_CONVERSATION_TYPE,
        payload: data
    });
}

const callTrigger = () => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.SET_CALL_STATUS,
        payload: true
    });
}

const callOff = () => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.SET_CALL_STATUS,
        payload: false
    });
}

//SpaceHost
const setSpaceHostData = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.SET_SPACE_HOST_DATA,
        payload: data,
    });
}
const setMainHost = (data) => dispatch => {

    return dispatch({
        type: KCT.NEW_INTERFACE.SET_MAIN_HOST,
        payload: data
    })
}

const setCalledUserId = (data) => dispacth => {
    return {
        type: KCT.NEW_INTERFACE.SET_CALLED_USER_ID,
        payload: data
    }
}
const setSingleUserData = (data) => dispatch => {
    return {
        type: KCT.NEW_INTERFACE.SET_SINGLE_USER_DATA,
        payload: data,
    }
}

const handleConfereceUpdate = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.CONFERENCE_UPDATE,
        payload: data,
    })
}

const dynamicMaxSeat = () => {
    if (configureStore) {
        const storeCon = configureStore();
        let unsubscribe = storeCon.subscribe(function () {
            const state2 = storeCon.getState();
            return state2;
        })
        const state = storeCon.getState();

        if (state && _.has(state, ['NewInterface'])) {
            const {interfaceSpaceHostData, interfaceSpacesData} = state.NewInterface;
            if (!_.isEmpty(interfaceSpacesData) && _.has(interfaceSpacesData, ['current_joined_conversation', 'conversation_users']) && !_.isEmpty(interfaceSpacesData.current_joined_conversation.conversation_users)) {
                const spaceHost = interfaceSpaceHostData[0];

                const exist = interfaceSpacesData.current_joined_conversation.conversation_users.filter((val) => {
                    if (val.user_id == spaceHost.user_id) {
                        return val;
                    }
                });

                return !_.isEmpty(exist);

            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

const triggerPagination = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.PAGINATION_UPDATE,
        payload: data
    });
}

const setConversationMute = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.CONVERSATION_MUTE_UPDATE,
        payload: data,
    })
}

const setConversationFullScreen = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.CONV_FULL_SCREEN,
        payload: data,
    })
}

const setGridVisibility = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.GRID_VISIBILITY_UPDATE,
        payload: data,
    });
}

const setCurrentContent = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.CONTENT_MANAGE_DATA,
        payload: data,
    });
}

const setZoomUserAdmitState = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.ZOOM_USER_ADMIT,
        payload: data,
    });
}

const setIsVideoPlayerStarted = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.VIDEO_PLAYER_STARTED,
        payload: data,
    })
}

const setLangChange = (data) => dispatch => {
    return dispatch({
        type:KCT.NEW_INTERFACE.CHANGE_LANGUAGE,
        payload: data,
    })
}
const setPreRecordedVolume = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.PRERECORED_VOLUME,
        payload: data,
    })
}

const updateVideoMuteText = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.UPDATE_VIDEO_MUTE_TEXT,
        payload: data,
    })
}

const setContentComponentVisibility = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.CONTENT_COMPONENT_VISIBILITY,
        payload: data,
    });
}

const setZoomMuteButton = (data) => dispatch => {
    return dispatch({
        type: KCT.NEW_INTERFACE.CONTENT_MUTE_UPDATE,
        payload: data,
    })
}

const setTestAudioUrl = (data) => dispatch => {

    return dispatch({
        type: KCT.NEW_INTERFACE.UPDATE_TEST_AUDIO_URL,
        payload: data,
    })
}
const setSelectedBgOption = (data) => dispatch => {

    return dispatch({
        type: KCT.NEW_INTERFACE.SELECTED_BG_OPTION,
        payload: data,
    })
}

const EventActions = {
    setMainHost,
    filterOnline,
    updateProfileTrigger,
    updateProfile,
    setBadgeData,
    setTagsData,
    setInterfaceGraphics,
    setEventData,
    setSpacesData,
    setVisibility,
    setVisible,
    askToJoin,
    turnOffJoin,
    callTrigger,
    callOff,
    setSpaceHostData,
    setUserInfoData,
    setSingleUserData,
    askToPrivateConversation,
    setCalledUserId,
    privateTurnOffJoin,
    updatePrivateConversation,
    dynamicMaxSeat,
    askToPrivateConversation2,
    handleConfereceUpdate,
    deleteConversation,
    addUserToActiveCon,
    setUserMediaDevices,
    triggerPagination,
    setConversationMute,
    setConversationFullScreen,
    setGridVisibility,
    setCurrentContent,
    setContentComponentVisibility,
    setZoomMuteButton,
    setZoomUserAdmitState,
    setIsVideoPlayerStarted,
    setTestAudioUrl,
    updateVideoMuteText,
    setPreRecordedVolume,
    setLangChange,
    updateSpacesData,
    setSelectedBgOption,
}
export default EventActions;