import {KeepContact as KCT} from "../../types";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description In this file event related redux action types are handled by switch case
 * so when a specific case of redux action is triggered this file will provide the handler for that specific redux
 * action type
 * ---------------------------------------------------------------------------------------------------------------------
 *
 */


const updateAnalyticsRecList = (recurrencesList) => dispatch => {
    return dispatch({
        type: KCT.ANALYTICS.UPDATE_RECURRENCES_LIST,
        payload: recurrencesList,
    })
}


const updateAnalyticsList = (recurrencesList) => dispatch => {
    return dispatch({
        type: KCT.ANALYTICS.UPDATE_ANALYTICS_LIST,
        payload: recurrencesList,
    })
}

const updateAnalyticsDropdownData = (data) => dispatch => {
    return dispatch({
        type: KCT.ANALYTICS.DATE_DROPDOWN_VAL,
        payload: data
    })
}

const filterAnalyticsList = (data) => dispatch => {
    return dispatch({
        type: KCT.ANALYTICS.FILTER_ANALYTICS_LIST,
        payload: data
    })
}
const setPageRefresh = (data) => dispatch => {
    return dispatch({
        type: KCT.ANALYTICS.SET_PAGE_REFRESH,
        payload: data
    })
}


const analyticsAction = {
    updateAnalyticsRecList: updateAnalyticsRecList,
    updateAnalyticsList: updateAnalyticsList,
    filterAnalyticsList: filterAnalyticsList,
    updateAnalyticsDropdownData: updateAnalyticsDropdownData,
    setPageRefresh:setPageRefresh
}

export default analyticsAction;