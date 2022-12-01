
/**
 * @type {Object}
 * @property {String} event_uuid Unique uuid of for the event
 * @property {String} name Title of the event
 * @property {String} description Description of the event
 * @property {Number} start_time Start time of event
 * @property {Number} end_time End time of event
 * @property {Date} reg_start Event registration start date and time
 * @property {Date} reg_end Event registration end date and time
 * @property {Number} is_reg_open To indicate whether registration for the event is opened or not
 * @property {Number} status To indicate status of the event (Draft or Live)
 * @property {Number} type Type of the event ( 1. Networking 2. Content + Networking )
 */
const DraftEvent = {
    event_uuid: "a81292d8-fc35-11ec-8134-0a502021c365",
    name: "Event Title",
    description: "This is event description",
    start_time: "14:10:00",
    end_time:"18:10:00",
    reg_start: "2022-06-20 14:10:00",
    reg_end: "2022-07-20 14:10:00",
    is_reg_open: 1,
    status: 1,
    type: "1"
}

export default DraftEvent;