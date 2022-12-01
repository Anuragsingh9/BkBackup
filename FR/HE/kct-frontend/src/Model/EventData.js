import UserBadge from './UserBadge';

/**
 * @type {Object}
 * @property {Number} id Id of moment
 * @property {String} moment_name Name of moment
 * @property {Object} moment_description Moment description
 * @property {String} start_time Start time of moment within event
 * @property {String} end_time End time of moment withing event
 * @property {Number} moment_type Type of the moment, e.g. Networking or content
 * @property {UserBadge} moderator Moderator of moment if moment is zoom
 * @property {UserBadge[]} speakers Speakers of moment if moment is zoom
 */
const Moment = {
    id: 1,
    moment_name: 'moment_name',
    moment_description: 'moment_description',
    start_time: 'start_time',
    end_time: 'end_time',
    moment_type: 'moment_type',
    moderator: UserBadge,
    speakers: [UserBadge],
}

/**
 *
 * @type {Object}
 * @property {String} event_uuid Uuid of the event
 * @property {String} event_title Title of the event
 * @property {String} event_header_text Header line of the event
 * @property {String} event_description Description of the event
 * @property {String} event_date Starting date of event
 * @property {String} start_date Starting date of event
 * @property {String} end_data Ending date of event
 * @property {String} event_start_time Start time of event
 * @property {String} event_end_time End time of event
 * @property {String} header_line_one Header line 1 of the event
 * @property {String} header_line_two Header line 1 of the event
 * @property {Number} manual_opening Indicate if event is manually opened or not
 * @property {Number} is_dummy_event To indicate if event have dummy users or not
 * @property {String} event_image Event image url
 * @property {Number} conference_type Type of the conference event following
 * @property {Number} opening_before Time before the event, spaces will open
 * @property {Number} opening_after Time after the event, spaces will remain open
 * @property {Number} opening_during To indicate if spaces are opened or not during the event
 * @property {Number} is_mono_present Indicate if event have mono spaces or not
 * @property {Object} moments Moments of the event
 * @property {Object} event_live_images Images url for the event pilot panel
 * @property {String} event_live_images.key Key of live data
 * @property {String} event_live_images.value Images url respective live data
 * @property {Object} event_live_video_links Videos url for event pilot panel
 * @property {String} event_live_video_links.key Key of live data
 * @property {String} event_live_video_links.value Video url respective live data
 * @property {Boolean} pilot_panel To indicate to show or hide the pilot panel
 * @property {Number} is_auto_created To indicate if the event is following auto moments created or not
 * @property {Number} event_is_live To indicate if event is live or not
 */
const EventData = {
    "event_uuid": "6f6257ec-e660-11ec-9e29-0a502021c365",
    "event_title": "After long days",
    "event_header_text": null,
    "event_description": null,
    "event_date": "2100-01-01",
    "start_date": "2100-01-01",
    "end_date": "2100-01-01",
    "event_start_time": "14:50:00",
    "event_end_time": "15:50:00",
    "header_line_one": "HCT Staging Account Used For Testing Purpose",
    "header_line_two": "This is Line 2 This is Line 2This is Line 2This This is",
    "manual_opening": 0,
    "is_dummy_event": 0,
    "event_image": "https://s3.eu-west-2.amazonaws.com/kct-dev/general/event_image/default.jpg",
    "conference_type": null,
    "opening_before": 0,
    "opening_after": 0,
    "opening_during": 1,
    "is_mono_present": 0,
    "moments": [
        Moment
    ],
    "event_live_images": [],
    "event_live_video_links": [],
    "pilot_panel": true,
    "is_auto_created": 0,
    event_is_live: 0,
}

export default EventData;