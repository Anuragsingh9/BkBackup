/* eslint-disable */
let Constants = {
    colors: {
        primary: "#0589b8",
        primary_2: "#f0f8ff",
        secondary: "3b3b3b",
    },
    gridRowSize: [4, 8, 12, 16],

    DATE_TIME_FORMAT: "YYYY-MM-DD HH:mm:ss",

    eventFormType: {
        CAFETERIA: 1,
        EXECUTIVE: 2,
        MANAGER: 3,
        ALL_DAY: 4
    },
    // Daagrid height variables
    DATA_GRID: {
        ONE_ROW_HEIGHT: 36,
        ADDITIONAL_SPACE: 10,
        PAGINATION_DIV_SPACE: 100
    },
    // types based on v4 event form
    broadcastType: {
        NONE: 0,
        MEETING: 1,
        WEBINAR: 2,
    },
    // This values are for analytics page dropdown
    analytic_grade_dropdown: ["All", "Executive", "Manager", "Employee", "Other"],

    analyticsDateRange: {
        CUSTOM: {name: "custom", val: 1},
        TODAY: {name: "today", val: 2},
        YESTERDAY: {name: "yesterday", val: 3},
        LAST_WEEK: {name: "last_week", val: 4},
        LAST_MONTH: {name: "last_month", val: 5},
        LAST_7_DAYS: {name: "last_7_days", val: 6},
        LAST_30_DAYS: {name: "last_30_days", val: 7},
    },

    gradeOptions: {
        ALL: "allGrade",
        MANAGER: "manager",
        EMPLOYEE: "employee",
        EXECUTIVE: "executive",
        OTHER: "other",
    },

    allowedTimeFormats: [
        'h.mm',
        'h.mm.ss',
        'h.mm a',
        'h.mm.ss a',
        'H.mm',
        'H.mm a',
        'H.mm.ss',
        'H.mm.ss a',
        'hh.mm',
        'hh.mm.ss',
        'hh.mm a',
        'hh.mm.ss a',
        'HH.mm',
        'HH.mm a',
        'HH.mm.ss',
        'HH.mm.ss a',

        'h:mm',
        'h:mm:ss',
        'h:mm a',
        'h:mm:ss a',
        'H:mm',
        'H:mm a',
        'H:mm:ss',
        'H:mm:ss a',
        'hh:mm',
        'hh:mm:ss',
        'hh:mm a',
        'hh:mm:ss a',
        'HH:mm',
        'HH:mm a',
        'HH:mm:ss',
        'HH:mm:ss a',
    ],

    eventType: {
        FUTURE_EVENT: 'future',
        DRAFT_EVENT: 'draft',
        PAST_EVENT: 'past',
    },

    // Keep the value same as in order of tabs in top bar
    eventTabType: {
        DETAILS: 0,
        MEDIA: 1,
        USER: 2,
        ANALYTICS: 3,
    },

    analyticsTabType: {
        ENGAGEMENT: 0,
        USERS: 1,
        EVENT_TYPE: 2,
    },

    addUserType: {
        ADD_USER: 1,
        IMPORT_USER: 2,
    },

    userManagementTabType: {
        EVENT_TEAM: 0,
        PARTICIPANTS: 1,
    },

    space: {
        MAX_CAPACITY: 1000,
        MIN_CAPACITY: 12,
    },

    recurrenceType: {
        NONE: 0,
        DAILY: 1,
        WEEKLY: 3,
        MONTHLY: 5,
        WEEKDAY: 2,
    },

    recurrenceMonthType: {
        ON_DAY: 1,
        ON_THE: 2,
    },

    mediaTabIcon: {
        IMAGE: 1,
        VIDEO: 2
    },
    recurrenceMonthWeek: {
        FIRST: 1,
        SECOND: 2,
        THIRD: 3,
        FOURTH: 4,
        LAST: -1,
    },

    timePickerInterval: 30,

    recWeekDayBinary: 124,

    defaultEventType: 'future',

    tPicker_minuteGap: 10, // minutes

    commonDateFormat: 'YYYY-MM-DD',

    event_analytic_dropdown: {
        ALL_OCCURRENCE: 0
    },

    momentType_networking: 1,
    momentType_webinar: 2,
    momentType_meeting: 4,
    momentType_youtube: 5,
    momentType_vimeo: 6,

    contentType_default: 1, // default youtube
    // contentType_youtube: 1,
    // contentType_vimeo: 2,
    contentType_broadcasting_meeting: 1,
    contentType_broadcasting_webinar: 2,
    contentType_broadcasting_youTube_live: 3,
    contentType_broadcasting_facebook_live: 4,
    contentType_youtube: 5,
    contentType_vimeo: 6,

    // types based on v1
    // changed acording to new values for brodcasting types  ( contentType_broadcasting_meeting: 1, contentType_broadcasting_webinar: 2,)
    broadcastType_webinar: 2,
    broadcastType_meeting: 1,
    broadcastType_default: 1,

    // before change(old)
    // broadcastType_webinar: 1,
    // broadcastType_meeting: 2,
    // broadcastType_default: 2,

    // moment default time difference (in minutes)
    momentTimeDifference: 10,

    // aliasing section defined after initialization
    momentToContentAlias: {},
    momentToBroadcastAlias: {},
    contentToMomentAlias: {},
    broadcastToMomentAlias: {},


    // event constants
    eventStatusLive: 1,
    eventStatusDraft: 2,

    breadcrumbsOptions: {
        ORGANISATION_NAME: 1,
        GROUP_NAME: 2,
        EVENT_NAME: 3,
        EVENTS_LIST: 4,
        NEW_EVENT: 5,
        GROUPS_LIST: 6,
        GROUP_CREATE: 7,
        MEDIA_TAB: 8,
        USERS_TAB: 9,
        DESIGN_SETTINGS: 10,
        TECHNICAL_SETTINGS: 11,
        USERS_LIST: 12,
        DASHBOARD: 13,
        SELF_USER: 14,
        CHANGE_UPDATE_PASSWORD: 15,
        MANAGE_PILOT_AND_OWN: 16,
        MANAGE_USERS: 17,
        ALL: 18,
        ADD_USER: 19,
        IMPORT_USER: 20,
        OTHER_USER_NAME: 21,
        TAGS: 22,
        ANALYTICS: 23,
        ENGAGEMENT: 24,
    },

    GOOGLE_CHART: {
        DEFAULT_PAGINATION: 5, // item per page
        ENGAGEMENT_PAGINATION: 1,
    },

    EVENT_ANALYTICS: {
        ALL_REC_DROP_DOWN: 'all-recurrence',
    },

    NUM_TO_ENG: ['Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'],

}

// Constants.contentMomentAlias = {
//     1: Constants.momentType_youtube, // youtube
//     contentType_vimeo: Constants.momentType_vimeo,
//     contentType_broadcasting: Constants.momentType_meeting, // default broadcasting is meeting,
// }

// aliasing moment type to content so passing the moment type will return the content type
Constants.momentToContentAlias[Constants.momentType_youtube] = Constants.contentType_youtube;
Constants.momentToContentAlias[Constants.momentType_vimeo] = Constants.contentType_vimeo;
Constants.momentToContentAlias[Constants.momentType_meeting] = Constants.contentType_broadcasting_meeting; // for meeting moment, content type is broadcasting,
Constants.momentToContentAlias[Constants.momentType_webinar] = Constants.contentType_broadcasting_webinar; // for webinar moment, content type is broadcasting,
Constants.momentToContentAlias[Constants.momentType_networking] = null; // for networking content type is null

Constants.momentToBroadcastAlias[Constants.momentType_meeting] = Constants.broadcastType_meeting; // for meeting moment, content type is broadcasting,
Constants.momentToBroadcastAlias[Constants.momentType_webinar] = Constants.broadcastType_webinar; // for webinar moment, content type is broadcasting,
Constants.momentToBroadcastAlias[Constants.momentType_vimeo] = null;
Constants.momentToBroadcastAlias[Constants.momentType_youtube] = null;
Constants.momentToBroadcastAlias[Constants.momentType_networking] = null; // for networking content type is null

Constants.contentToMomentAlias[Constants.contentType_youtube] = Constants.momentType_youtube;
Constants.contentToMomentAlias[Constants.contentType_vimeo] = Constants.momentType_vimeo;
Constants.contentToMomentAlias[Constants.contentType_broadcasting_webinar] = Constants.momentType_webinar; // default is webinar

Constants.broadcastToMomentAlias[Constants.broadcastType_webinar] = Constants.momentType_webinar; // default is webinar
Constants.broadcastToMomentAlias[Constants.broadcastType_meeting] = Constants.momentType_meeting; // default is webinar


// Constants.broadcastToMomentAlias = {
//     broadcastType_meeting: Constants.momentType_meeting,
//     broadcastType_webinar: Constants.momentType_webinar,
// }


export default Constants;