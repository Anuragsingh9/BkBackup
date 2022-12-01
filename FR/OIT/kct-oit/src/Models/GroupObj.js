/**
 * @type {Object}
 * @property {String} event_uuid Event's unique ID
 * @property {String} event_name Event's name
 * @property {String} date Event start date
 * @property {String} start_time Event start time
 * @property {String} end_time Event end time
 */
let nextEvent ={
    event_uuid: "2ede2dec-07ec-11ed-ab3a-0a502021c365",
    event_name: "Test recur 2",
    date: "2022-07-26",
    start_time: "11:00:00",
    end_time: "12:00:00"
}




/**
 *
 * @type {Object}
 * @property {String} id Group unique ID
 * @property {String} group_type Group type
 * @property {String} group_key Group key
 * @property {String} is_fav Group is favourite or not
 * @property {String} group_name Group's name
 * @property {Object} pilot Group's pilot data
 * @property {String} pilot.id Pilot's ID
 * @property {String} pilot.fname Pilot's first name
 * @property {String} pilot.lname Pilot's last name
 * @property {String} pilot.email Pilot's email address
 * @property {String} pilot.company_name Pilot's company name
 * @property {String} pilot.company_position Pilot's company poisition
 * @property {nextEvent} next_event Upcoming event information
 * @property {Number} allow_manage_pilots_owner Permission to manage pilots & owner data (if current value is 1)
 * @property {Number} allow_design_setting Permission to manage design settings (If current value is 1)
 * @property {Number} users No of users in this group
 * @property {Number} future_events_count Total no of future events in this group
 * @property {Number} all_events_count Total no of events in this group
 * @property {Number} published_events_count Total no of published events in this group
 * @property {Number} draft_events_count Total no of draft events in this group
 *
 */
 let GroupObj = {
    id: 55,
    group_type: "head_quarters_group",
    group_key: "@#0055",
    is_fav: 0,
    group_name: "@#@#@#@#@#s@#@#@#@#@#@#@#@#",
    pilot: {
        id: 1,
        fname: "Default",
        lname: "Pilot",
        email: "hctdevelopment@mailinator.com",
        company_name: "HumannConnect",
        company_position: "Developer"
    },
    next_event: null,
    allow_manage_pilots_owner: 1,
    allow_design_setting: 1,
    users: 1,
    future_events_count: 0,
    all_events_count: 0,
    published_events_count: 0,
    draft_events_count: 0
}