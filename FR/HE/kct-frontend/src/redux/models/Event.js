/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description this file is used to provide the model of the event so when the event data is fetched this will ensure
 * if the event data contains the proper required keys or not
 * ---------------------------------------------------------------------------------------------------------------------
 */

import Helper from '../../Helper.js';

/**
 * @property
 * @property {String} conference_type Type of the event conference
 * @property {String} event_date Event Start Date
 * @property {String} event_description Description of the event
 * @property {String} event_end_time Event End time
 * @property {String} event_header_text Event Header text
 * @property {String} event_image Image of the event
 * @property {String} event_start_time Event Start time
 * @property {String} event_title Event Title
 * @property {String} event_uuid Current Event Uuid
 * @property {String} header_line_one Event Header Line one
 * @property {String} header_line_two Event Header line two
 * @property {Number} is_dummy_event To indicate if event is dummy or with real user only
 * @property {Number} event_is_live To indicate if event is live or not
 *
 * @type {Object}
 */
const eventModel = {

    conference_type: null,
    event_date: "2021-07-22",
    event_description: "Xcvfgh",
    event_end_time: "23:59:59",
    event_header_text: "Asdfvbgn",
    event_image: "https://s3.eu-west-2.amazonaws.com/ooionline.com/event/event_default_image.jpeg",
    event_start_time: "03:00:00",
    event_title: "Today till 3",
    event_uuid: "48cd76d8-ea18-11eb-9309-0242982c7304",
    header_line_one: "Sdfghj",
    header_line_two: "Sdfghj",
    is_dummy_event: 1,
    main_hosts: [],
    manual_opening: 0,
    opening_after: 0,
    opening_before: 0,
    opening_during: 1,
    event_is_live: 0,
};

class Event {


    checkEvent = (data) => {

        return Helper.compareObjects(data, eventModel);

    }

}

const event = new Event();

const checkEvent = event.checkEvent;


export default {
    checkEvent
} 