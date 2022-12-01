/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description In this file group related redux action types are handled by switch case
 * so when a specific case of redux action is triggered this file will provide the handler for that specific redux
 * action type
 * ---------------------------------------------------------------------------------------------------------------------
 *
 */
import { KeepContact as KCT } from '../../types';

const setGraphicSetting = (data) => dispatch => {
    return dispatch({
        type: KCT.AUTH.SET_GRAPHIC_SETTINGS,
        payload: data
    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the redux store current group data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} groupObject Group object data
 * @param {Number} groupObject.id Id of group
 * @param {String} groupObject.group_key Key of the group
 * @param {String} groupObject.name Name of the group
 * @param {String} groupObject.description Description of the group
 * @param {Number} groupObject.is_fav_group To indicate if the group is favorite or not
 * @param {Number} groupObject.is_super_group To indicate if the group is super or not
 *
 * @returns {Function}
 */
const updateCurrentGroup = (groupObject) => dispatch => {
    return dispatch({
        type: KCT.GROUP.UPDATE_CURRENT_GROUP,
        payload: groupObject,
    })
}

const updateOrganisationName = (name) => dispatch => {
    return dispatch({
        type: KCT.GROUP.UPDATE_ORGANISATION_NAME,
        payload: name
    })
}

const updateEngagementTabDropdownData = (tabData) => dispatch => {
    return dispatch({
        type: KCT.GROUP.UPDATE_ENGAGEMENT_TAB_DATA.DATE_DROPDOWN_VAL,
        payload: tabData
    })
}
const updateEngagementRangePickerData = (rangeData) => dispatch => {
    return dispatch({
        type: KCT.GROUP.UPDATE_ENGAGEMENT_TAB_DATA.RANGE_PICKER_VALUE,
        payload: rangeData
    })
}
const updateAnalyticRangePickerData = (rangeData) => dispatch => {
    return dispatch({
        type: KCT.GROUP.ANALYTICS_RANGE_PICKER_VALUE,
        payload: rangeData
    })
}
const updateAnalyticTabDropdownData = (rangeData) => dispatch => {
    return dispatch({
        type: KCT.GROUP.ANALYTICS_DROPDOWN_DATA,
        payload: rangeData
    })
}


const updateAnalyticGroupDropdown = (data) => dispatch =>{
    return dispatch({
        type: KCT.GROUP.UPDATE_ANALYTIC_GRP_DROPDOWN,
        payload: data,
    })
}

const updateSearchedKey = (key) => dispatch =>{
    return dispatch({
        type: KCT.GROUP.UPDATE_SEARCHED_KEY,
        payload: key,
    })
}

const updateAllDayEventEnabled = (data) => dispatch => {
    return dispatch({
        type: KCT.GROUP.UPDATE_ALL_DAY_EVENT_ENABLED,
        payload: data,
    })
}

const groupAction = {
    setGraphicSetting,
    updateCurrentGroup,
    updateOrganisationName,
    updateAnalyticGroupDropdown,
    updateEngagementTabDropdownData,
    updateEngagementRangePickerData,
    updateSearchedKey,
    updateAllDayEventEnabled,
    updateAnalyticRangePickerData,
    updateAnalyticTabDropdownData,
}

export default groupAction;