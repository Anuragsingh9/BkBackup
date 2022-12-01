/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Here all the event related api dispatcher are stored
 * To call the api of event related, this file will provide the redux action dispatcher which can be mapped withing the
 * props of component for easy access.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @module EventReduxActions
 */
import agents from "../../../agents"
import Event from "../../../Models/Event"
import LiveMediaObj from "../../../Models/LiveMediaObj"

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will call the API to fetch a single event by event_uuid
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} id ID to fetch event details
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getEvent = (id) => dispatch => {
    return agents.Event.getEvent(id)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To check the event custom url code availability and exclude the event uuid if any
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} data Data to send to check for custom link availability
 * @param {String} data.key Key to check for availability
 * @param {String|undefined} data.event_uuid Event uuid of current event to ignore
 * @returns {Function}
 */
const checkEventCode = (data ) => dispatch => {
    return agents.Event.checkEventCode(data );
}

/**---------------------------------------------------------------------------------------------------------------------
 * @description This method will call the API to create an event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Event} data Event details object
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const createEvent = (data) => dispatch => {
    return agents.Event.createEvent(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will call API which is responsible for updating the scenery data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {SceneryDataObj} data Object of scenery data
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const updateSceneryData = (data) => dispatch => {
    return agents.Event.updateSceneryData(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will call API to fetch all event related links
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} data Event unique ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getEventLinks = (data) => dispatch => {
    return agents.Event.getEventLinks(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API which will fetch single event data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} data Event unique ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getSingleEvent = (data) => dispatch => {
    return agents.Event.getSingleEvent(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will call the API which is responsible for fetching all the spaces of an event
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} data Event unique ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getSpaces = (data) => dispatch => {
    return agents.Event.getSpaces(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will call the API to update a space by space_uuid
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} data Event unique ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const updateSpaces = (data) => dispatch => {
    return agents.Event.updateSpaces(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will call the API which responsible for deleting an event
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} data Event unique ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const deleteEvent = (data) => dispatch => {
    return agents.Event.deleteEvent(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will call the API which is responsible for creating the moments for an event
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} data Event unique ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const postKeyMoments = (data) => dispatch => {
    return agents.Event.postKeyMoments(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will call the API which is responsible for fetching the all moments of an event
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} data Event unique ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getMoments = (data) => dispatch => {
    return agents.Event.getMoments(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will call the API which is responsible for fetching all participants of an event
 * <br>
 * <br>
 * Participants can be Space host,Event member,Team A,Team B and VIP
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} data Event unique ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getParticipant = (data) => dispatch => {
    return agents.Event.getParticipant(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API which is responsible for getting the event's draft data.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {DraftEvent} data Draft event's data
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getDraft = (data) => dispatch => {
    return agents.Event.getDraft(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API which is responsible for updating the event's draft data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {DraftEvent} data Draft event's data
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const updateDraft = (data) => dispatch => {
    console.log("sssssssss nnnn");
    return agents.Event.updateDraft(data)
}

/**
 * @deprecated
 */
const getEventListOnPageChange = (data) => dispatch => {
    return agents.Event.updateDraft(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to fetch the event's live page data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Event} data Event's live tab data
 * @param {Event} data.eventUuid Event's unique ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getEventLiveData = (data) => dispatch => {
    return agents.Event.getEventLiveData(data);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To method is responsible for calling the API to update the event's live page data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Event} data Event's live tab data
 * @param {Event} data.eventUuid Event's unique ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const updateEventLiveData = (data) => dispatch => {
    return agents.Event.updateEventLiveData(data);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to delete an asset from the event's live page data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {LiveMediaObj} data Live tab media object
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const deleteEventLiveImage = (data) => dispatch => {
    return agents.Event.deleteEventLiveImage(data);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to upload an asset in event live page data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {LiveMediaObj} data Live tab media object
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const uploadEventLiveImage = (data) => dispatch => {
    return agents.Event.deleteEventLiveImage(data);
}

const eventAction = {
    createEvent,
    getEvent,
    getEventLinks,
    getSingleEvent,
    getSpaces,
    updateSpaces,
    deleteEvent,
    postKeyMoments,
    getMoments,
    getParticipant,
    getDraft,
    updateDraft,
    getEventLiveData,
    updateEventLiveData,
    deleteEventLiveImage,
    uploadEventLiveImage,
    updateSceneryData,
    checkEventCode,
}

export default eventAction;