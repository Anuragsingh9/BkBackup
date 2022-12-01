/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description In this file event related redux action types are handled by switch case
 * so when a specific case of redux action is triggered this file will provide the handler for that specific redux
 * action type
 * ---------------------------------------------------------------------------------------------------------------------
 *
 */
import {KeepContact as KCT} from '../../types';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the event data in redux store
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} data Event data keys to store in redux
 * @returns {Function}
 */
const setEventData = (data) => dispatch => {
    return dispatch({
        type: KCT.AUTH.EVENT_DETAILS,
        payload: data
    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To add the event object in events list of redux
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {EventModel} eventObject
 * @returns {Function}
 */
const addEvent = (eventObject) => dispatch => {
    return dispatch({
        type: KCT.EVENT.ADD_EVENT,
        payload: eventObject,
    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the flag for the event form update, so when user modify the form this will be true
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param data
 * @returns {function(*): *}
 */
const updateInputField = (data = true) => dispatch => {
    return dispatch({
        type: KCT.EVENT.IS_EVENT_FORM_UPDATED,
        payload: data,
    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the current event uuid in redux store
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param data
 * @returns {function(*): *}
 */
const currentEventUuid = (data) => dispatch => {
    return dispatch({
        type: KCT.EVENT.CURRENT_EVENT_UUID,
        payload: data,
    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the temp recurrence data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param recData
 * @returns {function(*): *}
 */
const updateTempRecData = (recData) => dispatch => {
    return dispatch({
        type: KCT.EVENT.UPDATE_TEMP_REC_DATA,
        payload: recData,
    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To get the event from the events array and with the current event uuid
 * This method will take the current event uuid from redux state and search the event from the events array by matching
 * the event uuid
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} state State which contains the event data
 * @param {String} state.current_event_uuid Current event uuid
 * @param {EventModel[]} state.events Events array containing different events object
 * @returns {null|*}
 */
const getCurrentEvent = (state) => {
    if (state.Event.current_event_uuid) {
        return state.Event.events.find(event => event.event_uuid === state.Event.current_event_uuid);
    }
    return state.Event.current_event_uuid;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the current event
 * This method will add the event in events array of redux store and update the current event uuid
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} eventObject The object of the event
 * @param {String} eventObject.event_uuid Uuid of event
 * @param {String} eventObject.event_title Title of the event
 * @param {moment} eventObject.event_start_date Start date of the event
 * @param {moment} eventObject.event_start_time Start time of the event
 * @param {moment} eventObject.event_end_time End time of the event
 * @param {String} eventObject.event_custom_link Event custom link
 * @param {String} eventObject.event_description Description of event
 * @param {Number|Boolean} eventObject.event_is_published To indicate if event is published or not
 * @param {String[]} eventObject.event_links Links of the event
 * @param {Number} eventObject.event_scenery Selected scenery id for the event
 * @param {ColorRGBA} eventObject.event_top_bg_color Top BG Color for the event
 * @param {Number} eventObject.event_component_op Event component opacity level
 * @param {Number|Boolean} eventObject.event_scenery_asset Event asset id
 * @param {Object} eventObject.event_state Event time related state
 * @param {Number|Boolean} eventObject.event_state.is_past To indicate event is past
 * @param {Number|Boolean} eventObject.event_state.is_live To indicate event is live
 * @param {Number|Boolean} eventObject.event_state.is_future To indicate event is future
 * @returns {Function}
 */
const updateCurrentEvent = (eventObject) => dispatch => {
    return dispatch({
        type: KCT.EVENT.UPDATE_CURRENT_EVENT,
        payload: eventObject,
    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To submit the event with having publish button state true
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param submitState
 * @returns {function(*): *}
 */
const publishEventSubmit = (submitState) => dispatch => {
    return dispatch({
        type: KCT.EVENT.PUBLISH_EVENT_SUBMIT,
        payload: submitState,
    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the event save update submit state
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param submitState
 * @returns {function(*): *}
 */
const saveEventSubmit = (submitState) => dispatch => {
    return dispatch({
        type: KCT.EVENT.SAVE_EVENT_SUBMIT,
        payload: submitState,
    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To close the popup for the space manage
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {Function}
 */
const closeSpacePopup = () => dispatch => {
    return dispatch({
        type: KCT.EVENT.UPDATE_SPACE_FORM_STATUS,
        payload: {is_open: false},
    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To open the space popup
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {Function}
 */
const openSpacePopup = () => dispatch => {
    return dispatch({
        type: KCT.EVENT.UPDATE_SPACE_FORM_STATUS,
        payload: {is_open: true},
    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To open the space popup in edit mode
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {Function}
 */
const editSpaceMode = () => dispatch => {
    return dispatch({
        type: KCT.EVENT.UPDATE_SPACE_FORM_STATUS,
        payload: {is_modified: true},
    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To open the space popup in create mode
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {Function}
 */
const createSpaceMode = () => dispatch => {
    return dispatch({
        type: KCT.EVENT.UPDATE_SPACE_FORM_STATUS,
        payload: {is_modified: false},
    })
}

const updateAddUserPopUpDisplay = (display, mode, fetch) => dispatch => {
    // TODO display,mode and  fetch should updated separately
    return dispatch({
        type: KCT.EVENT.UPDATE_USER_POP_UP,
        payload: {display: display, mode, fetch},
    })
}

const updateUserSortModel = (sortModel) => dispatch => {
    return dispatch({
        type: KCT.EVENT.UPDATE_USER_SORT_MODEL,
        payload: {field: sortModel[0].field, sort: sortModel[0].sort},
    })
}

const updateAllDayEvent = (data) => dispatch => {
    return dispatch({
        type: KCT.EVENT.UPDATE_ALL_DAY_EVENT,
        payload: data,
    })
}

const eventAction = {
    setEventData,
    addEvent,
    updateInputField,
    currentEventUuid,
    updateTempRecData,
    getCurrentEvent,
    updateCurrentEvent,
    publishEventSubmit,
    closeSpacePopup,
    openSpacePopup,
    editSpaceMode,
    createSpaceMode,
    updateAddUserPopUpDisplay,
    saveEventSubmit,
    updateUserSortModel,
    updateAllDayEvent
}

export default eventAction;