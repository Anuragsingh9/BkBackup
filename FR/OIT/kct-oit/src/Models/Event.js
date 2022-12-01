import User from '../Models/User'
import DraftEvent from '../Models/DraftEvent'

/**
 * @type {Object}
 * @property {String} event_uuid Unique uuid of for the event
 * @property {String} title Event's title
 * @property {Date} date Date of the event
 * @property {Number} start_time Start time of event
 * @property {Number} end_time End time of event
 * @property {String} description Description of the event
 * @property {Number} type Type of the event <br> 1. Networking<br> 2. Content + Networking<br> 
 * @property {String} header_line_1 Header line one of the event
 * @property {String} header_line_2 Header line two of the event
 * @property {Number} is_auto_key_moment_event To indicate if the event's moments are auto created
 * @property {Number} is_dummy_event To indicate if event should have dummy users or not
 * @property {Number} is_self_header To indicate if event having its own header or the design settings header will be followed
 * @property {DraftEvent} event_draft Event's draft data
 * @property {User} organiser Event organiser's data
 * @property {Object} time_state Time state of the event
 * @property {Number} time_state.is_future Event is in future or not
 * @property {Number} time_state.is_live Event is live or not
 * @property {Number} time_state.is_past Event is in past or not
 * @example {
    event_uuid: "a81292d8-fc35-11ec-8134-0a502021c365",
    title: "Event Title",
    date: "2022-08-20",
    start_time: "14:10:00",
    end_time:"18:10:00",
    description: "This is event description",
    type: "1",
    header_line_1: 'This is header line one',
    header_line_2:'This is header line two',
    is_auto_key_moment_event: 1,
    is_dummy_event:1,
    is_self_header:1,
    event_draft: '',
    organiser: '',
    time_state: {
        is_future: 0,
        is_live: 0,
        is_past: 1
    }
}
 */
const Event = {
    event_uuid: "a81292d8-fc35-11ec-8134-0a502021c365",
    title: "Event Title",
    date: "2022-08-20",
    start_time: "14:10:00",
    end_time:"18:10:00",
    description: "This is event description",
    type: "1",
    header_line_1: 'This is header line one',
    header_line_2:'This is header line two',
    is_auto_key_moment_event: 1,
    is_dummy_event:1,
    is_self_header:1,
    event_draft: '',
    organiser: '',
    time_state: {
        is_future: 0,
        is_live: 0,
        is_past: 1
    }
}

export default Event;