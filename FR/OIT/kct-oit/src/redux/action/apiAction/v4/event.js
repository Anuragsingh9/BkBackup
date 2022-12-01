/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Here all the event related api dispatcher are stored
 * To call the api of event related, this file will provide the redux action dispatcher which can be mapped withing the
 * props of component for easy access.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @module EventReduxActions
 */

import v4Api from "../../../../agents/v4Api";
import agents from "../../../../agents";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To hit the api for creating the event
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param jsonData
 * @returns {Function}
 */
const createEvent = (jsonData) => dispatch =>  {
    return v4Api.Event.createEvent(jsonData);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To hit the api for creating the event
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {Function}
 * @param eventUuid
 */
const getEvent = (eventUuid) => dispatch =>  {
    return v4Api.Event.getEvent(eventUuid);
}

/**
 *
 * @param eventUuid
 * @return {Function}
 */
const getEventInitData = (eventUuid = null) => dispatch =>  {
    return v4Api.Event.getEventInitData(eventUuid);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To hit the api for getting the event users
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} data Table meta data(row per page, page number, is pagination)
 * @returns {Function}
 */
const getEventUsers = (data) => dispatch => {
    return v4Api.Event.getEventUsers(data);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is used for deleting and updating the user role of the event users
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} data Table meta data(user's id, event uuid, method)
 * @returns {Function}
 */
const updateUserRole = (data) => dispatch => {
    return agents.User.updateRole(data);
}

const getAnalyticsData = (analyticsFilterData) => dispatch => {
    return v4Api.Event.getAnalyticsData(analyticsFilterData)
}

/**
 *
 * @param {Object} data Data to send to backend
 * @param {String} data.event_uuid Event uuid for which the analytics will be fetched
 * @param {String} data.from_date OPTIONAL date from which the data needs to be sent
 * @param {String} data.to_date OPTIONAL date till which the recurrence is required
 * @param {String} data.recurrence_uuid Rec id if single recurrence is required
 * @returns {Function}
 */
const getEventAnalytics = (data) => dispatch => {
    return v4Api.Event.getEventAnalytics(data);
}


let eventV4Api = {
    createEvent,
    getEvent,
    getEventInitData,
    getEventUsers,
    updateUserRole,
    getAnalyticsData,
    getEventAnalytics,
}

export default eventV4Api;