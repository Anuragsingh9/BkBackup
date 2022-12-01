/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description In this file auth related reducers are handled by switch case
 * so when a specific case of redux action is triggered this file will provide the handler for that specific redux
 * action type
 *
 * There is initial state has been defined which make sure the availability of the keys in redux store
 * ---------------------------------------------------------------------------------------------------------------------
 */

import _ from "lodash";
import Constants from "../../Constants";
import {KeepContact as KCT} from "../types";
import CustomDateRangePickerModal from "../../views/v4/Models/CustomDateRangePicker";

const initialState = {
    current_group: null,
    organisation_name: null,
    engagement_tab_data:{
        date_dropdown_val:Constants.analyticsDateRange.YESTERDAY.val,
        range_picker_val:CustomDateRangePickerModal.yesterday,
    },
    analytic_group_dropdown:[],
    searched_key:"",
    all_day_event_enabled: 0,
    analytic_range_picker_value:CustomDateRangePickerModal.today,
    analytic_date_dropdown_val:Constants.analyticsDateRange.TODAY.val,
};

export default function GroupReducer(state = initialState, action) {
    switch (action.type) {
        case KCT.GROUP.UPDATE_CURRENT_GROUP:
            state = {
                ...state,
                current_group: filterGroupObject(action.payload),
            }
            break;
        case KCT.GROUP.UPDATE_ORGANISATION_NAME:
            state = {
                ...state,
                organisation_name: action.payload,
            }
            break;
        case KCT.GROUP.UPDATE_ENGAGEMENT_TAB_DATA.DATE_DROPDOWN_VAL:
            state = {
                ...state,
                engagement_tab_data:{
                    ...state.engagement_tab_data,
                    date_dropdown_val:action.payload,
                }
            }
            break;
        case KCT.GROUP.UPDATE_ENGAGEMENT_TAB_DATA.RANGE_PICKER_VALUE:
            state = {
                ...state,
                engagement_tab_data:{
                    ...state.engagement_tab_data,
                    range_picker_val:action.payload,
                }
            }
            break;
        case KCT.GROUP.UPDATE_ANALYTIC_GRP_DROPDOWN:
            state = {
                ...state,
                analytic_group_dropdown: action.payload,
            }
            break;
        case KCT.GROUP.UPDATE_SEARCHED_KEY:
            state = {
                ...state,
                searched_key: action.payload,
            }
            break;
        case KCT.GROUP.UPDATE_ALL_DAY_EVENT_ENABLED:
            state = {
                ...state,
                all_day_event_enabled: action.payload,
            }
            break;
        case KCT.GROUP.ANALYTICS_RANGE_PICKER_VALUE:
            state = {
                ...state,
                analytic_range_picker_value: action.payload,
            }
            break;
        case KCT.GROUP.ANALYTICS_DROPDOWN_DATA:
            state = {
                ...state,
                analytic_date_dropdown_val:action.payload,
            }
        default:
    }
    return state;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To map the group object in proper available keys for group
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param groupObject
 * @returns {{group_is_super: Number, group_id, group_name, group_is_fav: Number, group_type: (string|String|*), group_description, group_key}}
 */
const filterGroupObject = (groupObject) => {
    return {
        group_key: groupObject.group_key,
        group_id: groupObject.id,
        group_name: groupObject.group_name,
        group_type: groupObject.group_type,
        group_is_fav: groupObject.is_fav_group,
        group_is_super: groupObject.is_super_group,
        group_description: groupObject.description,
    }
}