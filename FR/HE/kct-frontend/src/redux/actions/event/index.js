/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file is used to provide the event related api's redux actions with dispatcher
 * ---------------------------------------------------------------------------------------------------------------------
 */

import KeepContactagent from "../../../agents/KeepContactagent";

const getUserInfoData = () => dispatch => {
    return KeepContactagent.Event.userInfo();
}
const getEventGraphics = (data, accessCode = null) => dispatch => {
    return KeepContactagent.Event.getEventGraphics(data, accessCode)
}
const getEventSpaces = (data) => dispatch => {
    return KeepContactagent.Event.getEventSpaces(data)
}
const spaceJoin = (data) => dispatch => {
    return KeepContactagent.Event.spaceJoin(data)
}
const conversationJoin = (data) => dispatch => {
    return KeepContactagent.Event.conversationJoin(data)
}
const leaveConversation = (data) => dispatch => {
    return KeepContactagent.Event.leaveConversation(data)
}
const eventList = (data) => dispatch => {
    return KeepContactagent.Event.eventList(data)
}
// const eventList=(data, page , sizePerPage, search, order_by, order, groupKey)=>dispatch=>{
//     return KeepContactagent.Event.eventList(data, page , sizePerPage, search, order_by, order, groupKey)
// }
const updatePass = (data) => dispatch => {
    return KeepContactagent.Event.updatePass(data)
}
const getProfile = (data) => dispatch => {
    return KeepContactagent.Event.getProfile()
}
const updateProfile = (data) => dispatch => {
    return KeepContactagent.Event.updateProfile(data)
}
const getCurrentConversation = (eventId) => dispatch => {
    return KeepContactagent.Event.getCurrentConversation(eventId);
}
const getBadge = (getBadge) => dispatch => {
    return KeepContactagent.Event.getBadge(getBadge);
}
const getTag = () => dispatch => {
    return KeepContactagent.Event.getTag();
}
const deleteTag = (data) => dispatch => {
    return KeepContactagent.Event.deleteTag(data);
}
const addTag = (data) => dispatch => {
    return KeepContactagent.Event.addTag(data);
}
const getEmbeddedUrl = (val) => dispatch => {
    return KeepContactagent.Event.getEmbeddedUrl(val);
}

const toggleDnd = (val) => dispatch => {
    return KeepContactagent.Event.toggleDnd(val);
}

const updateProfileData = (val) => dispatch => {
    return KeepContactagent.Event.updateProfileData(val);
}

const sortSpaces = (val) => dispatch => {
    return KeepContactagent.Event.sortSpaces(val);
}

const getMainHostData = (data) => dispatch => {
    return KeepContactagent.Event.getMainHost(data);
}

const privateConversation = (data) => dispatch => {
    return KeepContactagent.Event.privateConversation(data);
}

const userBan = (data) => dispatch => {
    return KeepContactagent.Event.banUser(data);
}
const mockBadge = (data) => dispatch => {
    return KeepContactagent.Event.mocKBadgeApi(data);
}
const createTag = (data) => dispatch => {
    return KeepContactagent.Event.createTag(data);
}

const removeTag = (tag_id) => dispatch => {
    return KeepContactagent.Event.removeTag(tag_id);
}
const updateTag = (tag_id) => dispatch => {
    return KeepContactagent.Event.updateTag(tag_id);
}

const updateInfo = (data) => dispatch => {
    return KeepContactagent.Event.updateInfo(data);
}

const getGroupGraphicsByEvent = (data) => dispatch => {
    return KeepContactagent.Event.getGroupGraphicsByEvent(data);
}

const addLog = (data) => dispatch => {
    return KeepContactagent.Auth.addLog(data);
}
const EventActions = {
    updateInfo,
    mockBadge,
    userBan,
    getMainHostData,
    getEventGraphics,
    getEventSpaces,
    spaceJoin,
    conversationJoin,
    leaveConversation,
    eventList,
    updatePass,
    getProfile,
    updateProfile,
    getCurrentConversation,
    getBadge,
    getEmbeddedUrl,
    toggleDnd,
    updateProfileData,
    sortSpaces,
    getTag, deleteTag, addTag,
    getUserInfoData,
    privateConversation,
    createTag, removeTag, updateTag,
    getGroupGraphicsByEvent,
    addLog,
}
export default EventActions;