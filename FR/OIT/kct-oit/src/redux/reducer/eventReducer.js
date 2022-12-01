/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description In this file auth related reducers are handled by switch case
 * so when a specific case of redux action is triggered this file will provide the handler for that specific redux
 * action type
 *
 * There is initial state has been defined which make sure the availability of the keys in redux store
 * ---------------------------------------------------------------------------------------------------------------------
 */

import {KeepContact as KCT} from "../types";
import RecurrenceModel from "../../views/v4/Models/RecurrenceModel";
import _ from 'lodash';

const initialState = {
    current_event_uuid: null,
    events: [],
    is_event_form_updated: false,
    temp_recurrence_data: RecurrenceModel,
    event_form: {
        publish_submit_action: false,
        save_submit_action: false,
    },
    space_form_status: {
        is_modified: false,
        is_open: false,
    },
    add_user_pop_up: {
        display: false,
        mode: null,
        fetch: false,
    },
    sort_user_model: [{
        field: 'lname',
        sort: 'desc',
    }],
    analytic_event_all_recurrence: [],
    analytic_selected_event_date: null,
    all_day_event: {
        event_uuid: null,
    }
};

export default function EventReducer(state = initialState, action) {
    switch (action.type) {
        case KCT.EVENT.ADD_EVENT:
            let event = findEvent(action.payload.event_uuid, state.events);
            let events = state.events;
            if (event) {
                events = updateEvent(action.payload, state.events);
            } else {
                events.push(filterEventObject(action.payload));
            }
            state = {
                ...state,
                events: events,
            };
            break;
        case KCT.EVENT.IS_EVENT_FORM_UPDATED:
            state = {
                ...state,
                is_event_form_updated: action.payload,
            };
            break;
        case KCT.EVENT.CURRENT_EVENT_UUID:
            state = {
                ...state,
                current_event_uuid: action.payload,
            };
            break;
        case KCT.EVENT.UPDATE_TEMP_REC_DATA:
            state = {
                ...state,
                temp_recurrence_data: filterTempRecData(action.payload),
            }
            break;
        case KCT.EVENT.UPDATE_CURRENT_EVENT:
            if (state.current_event_uuid) {
                let currentEvent = findEvent(state.current_event_uuid, state.events);
                currentEvent = {
                    ...currentEvent,
                    ...action.payload,
                }
                let events = updateEvent(filterEventObject(currentEvent), state.events);
                state = {
                    ...state,
                    events,
                }
            }
            break;
        case KCT.EVENT.PUBLISH_EVENT_SUBMIT:
            state = {
                ...state,
                event_form: {
                    ...state.event_form,
                    publish_submit_action: action.payload,
                },
            }
            break;
        case KCT.EVENT.SAVE_EVENT_SUBMIT:
            state = {
                ...state,
                event_form: {
                    ...state.event_form,
                    save_submit_action: action.payload,
                },
            }
            break;

        case KCT.EVENT.UPDATE_SPACE_FORM_STATUS:
            state = {
                ...state,
                space_form_status: {
                    is_modified: _.has(action.payload, ['is_modified']) ? action.payload.is_modified : state.space_form_status.is_modified,
                    is_open: _.has(action.payload, ['is_open']) ? action.payload.is_open : state.space_form_status.is_open,
                }
            }
            break;

        case KCT.EVENT.UPDATE_USER_POP_UP:
            state = {
                ...state,
                add_user_pop_up: {
                    ...state.add_user_pop_up,
                    display: action.payload.display,
                    mode: action.payload.mode,
                    fetch: action.payload.fetch,
                }
            }
            break;
        case KCT.EVENT.UPDATE_USER_SORT_MODEL:
            state = {
                ...state,
                sort_user_model: [{
                    ...state.sort_user_model[0],
                    field: action.payload.field,
                    sort: action.payload.sort,
                }]
            }
            break;
        case KCT.EVENT.UPDATE_ALL_DAY_EVENT:
            state = {
                ...state,
                all_day_event: {
                    ...state.all_day_event,
                    event_uuid: action.payload.event_uuid,
                }
            }
            break;
        default:
    }
    return state;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To make sure the proper keys are available in event resource and only defined keys are stored in redux
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param eventObject
 * @returns {Object}
 */
const filterEventObject = (eventObject) => {
    let spaces = [];
    for (const space in eventObject.event_spaces) {
        spaces.push(filterSpaceObject(space));
    }
    return {
        event_uuid: eventObject.event_uuid,
        event_title: eventObject.event_title,
        event_start_date: eventObject.event_start_date,
        event_start_time: eventObject.event_start_time,
        event_end_time: eventObject.event_end_time,
        event_custom_link: eventObject.event_custom_link,
        event_description: eventObject.event_description,
        event_is_published: eventObject.event_is_published,
        event_links: eventObject.event_links,
        event_scenery: eventObject.event_scenery,
        event_top_bg_color: eventObject.event_top_bg_color,
        event_component_op: eventObject.event_component_op,
        event_scenery_asset: eventObject.event_scenery_asset,
        event_spaces: spaces,
        event_state: {
            is_past: eventObject?.event_state?.is_past ? 1 : 0,
            is_live: eventObject?.event_state?.is_live ? 1 : 0,
            is_future: eventObject?.event_state?.is_future ? 1 : 0,
        },
        event_type: eventObject.event_type,
        event_recurrence: {
            rec_start_date: eventObject?.event_recurrence?.rec_start_date,
            rec_type: eventObject?.event_recurrence?.rec_type,
            rec_end_date: eventObject?.event_recurrence?.rec_end_date,
            rec_month_date: eventObject?.event_recurrence?.rec_month_date,
            rec_interval: eventObject?.event_recurrence?.rec_interval,
            rec_month_type: eventObject?.event_recurrence?.rec_month_type,
            rec_on_month_week: eventObject?.event_recurrence?.rec_on_month_week,
            rec_on_month_week_day: eventObject?.event_recurrence?.rec_on_month_week_day,
            rec_selected_weekday: eventObject?.event_recurrence?.rec_weekdays,
        },
        event_conv_limit: eventObject?.event_conv_limit || 4,
        event_is_recurrence: eventObject.is_recurrence,
    };
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To make sure the proper keys are available in space resource and only defined keys are stored in redux
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param spaceObject
 * @returns {Object}
 */
const filterSpaceObject = (spaceObject) => {
    return {
        space_uuid: spaceObject.space_uuid,
        event_uuid: spaceObject.event_uuid,
        space_line_1: spaceObject.space_line_1,
        space_line_2: spaceObject.space_line_2,
        is_default: spaceObject.is_default,
        max_capacity: spaceObject.max_capacity,
    }
}

const filterTempRecData = (data) => {
    return {
        event_rec_start_date: data.event_rec_start_date || RecurrenceModel.event_rec_start_date,
        event_rec_type: data.event_rec_type || RecurrenceModel.event_rec_type,
        event_rec_end_date: data.event_rec_end_date || RecurrenceModel.event_rec_end_date,
        event_rec_weekdays: data.event_rec_weekdays || RecurrenceModel.event_rec_weekdays,
        event_rec_month_date: data.event_rec_month_date || RecurrenceModel.event_rec_month_date,
        event_rec_interval: data.event_rec_interval || RecurrenceModel.event_rec_interval,
        event_rec_month_type: data.event_rec_month_type || RecurrenceModel.event_rec_month_type,
        event_rec_on_month_week: data.event_rec_on_month_week || RecurrenceModel.event_rec_on_month_week,
        event_rec_on_month_week_day: data.event_rec_on_month_week_day || RecurrenceModel.event_rec_on_month_week_day,
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To find the event by the event uuid from the provided event collection
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} eventUuid Target event uuid to be searched
 * @param {Object[]} events Events collection from where the event will be searched by event uuid
 * @returns {Object[]}
 */
const findEvent = (eventUuid, events) => {
    return events.find(e => e.event_uuid === eventUuid);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the event by the provided event by searching from event uuid
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} eventObj Event object to be updated by searching
 * @param {Object[]} events All events from where the target event will be searched and replaced
 * @returns {Object[]}
 */
const updateEvent = (eventObj, events) => {
    return events.map(e => {
        if (e.event_uuid === eventObj.event_uuid) {
            return {
                ...e,
                ...eventObj,
            };
        } else {
            return e;
        }
    })
}
