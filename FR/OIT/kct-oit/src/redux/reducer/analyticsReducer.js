/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description In this file auth related reducers are handled by switch case
 * so when a specific case of redux action is triggered this file will provide the handler for that specific redux
 * action type
 *
 * There is initial state has been defined which make sure the availability of the keys in redux store
 * ---------------------------------------------------------------------------------------------------------------------
 */
import moment from "moment-timezone";
import {KeepContact as KCT} from "../types";
import Constants from "../../Constants";

moment.tz.setDefault("Europe/Paris");
const initialState = {
    date_range: {
        start: moment(),
        end: moment(),
    },
    // there will be the list of the recurrences in event with name and date only
    recurrences_list: [],

    // here will be the list of the recurrences with the analytics data in it
    recurrences_analytics: [],
    refreshPage:false,
};

export default function AnalyticsReducer(state = initialState, action) {
    switch (action.type) {
        case KCT.ANALYTICS.UPDATE_RECURRENCES_LIST:
            state = {
                ...state,
                recurrences_list: filterRecList(action.payload),
            }
            break;

        case KCT.ANALYTICS.UPDATE_ANALYTICS_LIST:
            state = {
                ...state,
                recurrences_analytics: filterRecAnalyticsList(action.payload),
            }
            break;

        case KCT.ANALYTICS.DATE_DROPDOWN_VAL:
            state = {
                ...state,
                date_range: {
                    ...state.date_range,
                    start: action.payload.startDate,
                    end: action.payload.endDate,
                }
            }
            break;
        case KCT.ANALYTICS.FILTER_ANALYTICS_LIST:
            let recurrence = findRecurrenceByUuid(action.payload, state.recurrences_analytics);
            state = {
                ...state,
                recurrences_analytics: recurrence ? [recurrence] : [],
            }
            break;
        case KCT.ANALYTICS.SET_PAGE_REFRESH:
            state = {
                ...state,
                refreshPage: action.payload,
            }
            break;

        default:
    }
    return state;
}

const findRecurrenceByUuid = (recId, recs) => {
    return recs.find(rec => rec.recurrence_uuid === recId);
}

const filterRecList = (recurrencesList) => {
    return recurrencesList.map(recurrence => {
        return {
            recurrence_uuid: recurrence.recurrence_uuid,
            rec_start_date: moment(recurrence.recurrence_date, Constants.DATE_TIME_FORMAT),
        }
    })
}

const filterRecAnalyticsList = (recurrencesList) => {
    console.log('recurrencesList', recurrencesList)
    return recurrencesList.map(recurrence => {
        let convo_data = [];
        let all_convo_count = 0;
        let all_convo_duration = 0;
        recurrence.conv_analytics_data.forEach((data, i) => {
            convo_data[i] = {
                users: data.user_count,
                count: data.convo_count,
                duration: moment(data.convo_duration),
            };
            all_convo_count = all_convo_count + data.convo_count;
            all_convo_duration = all_convo_duration + data.convo_duration;

        })

        return {
            recurrence_uuid: recurrence.recurrence_uuid,
            rec_start_date: moment(recurrence.occurrence_start || recurrence.rec_start_date, Constants.DATE_TIME_FORMAT),
            rec_end_date: moment(recurrence.occurrence_end || recurrence.rec_end_date, Constants.DATE_TIME_FORMAT),
            total_registration: recurrence.total_registration,
            total_attendance: recurrence.total_attendance,
            total_current_online: recurrence.current_online || 0,
            media_video: recurrence.media_video,
            media_image: recurrence.media_image,
            conversations: convo_data,
            total_conv_count: all_convo_count,
            total_conv_duration: moment(all_convo_duration),
            attendance_data: recurrence.attendance_data,
            average_duration:recurrence.average_duration,
            event_type: recurrence.event_type,
        }
    })
}