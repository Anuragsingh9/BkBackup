/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description In this file auth related reducers are handled by switch case
 * so when a specific case of redux action is triggered this file will provide the handler for that specific redux
 * action type
 *
 * There is initial state has been defined which make sure the availability of the keys in redux store
 * ---------------------------------------------------------------------------------------------------------------------
 */

import {KeepContact as KCT} from "../types";

const initialState = {
    eventDetailsData: {},
    userSelfData: {},
    userMetaData: {
        allow_manage_pilots_owner: 1,
        allow_design_setting: 1,
        is_acc_analytics_enabled:1
    },
    appSettings: {},
    eventRoleLabels: {},
    graphicSettings: {},
    language: {},
};

export default function AuthReducer(state = initialState, action) {
    switch (action.type) {
        case KCT.AUTH.SET_USER_META_DATA:
            state = {
                ...state,
                userMetaData: action.payload,
            };
            break;
        case KCT.AUTH.EVENT_DETAILS:
            state = {
                ...state,
                eventDetailsData: action.payload,
            };
            break;
        case KCT.AUTH.USER_SET_SELF:
            state = {
                ...state,
                userSelfData: action.payload,
            };
            break;
        case KCT.AUTH.EVENT_ROLE_LABLES:
            state = {
                ...state,
                eventRoleLabels: action.payload,
            };
            break;
        case KCT.AUTH.APP_SETTINGS:
            state = {
                ...state,
                appSettings: action.payload,
            };
            break;

        case KCT.AUTH.SET_GRAPHIC_SETTINGS:
            state = {
                ...state,
                graphicSettings: action.payload,
            };
            break;
        case KCT.AUTH.SET_LANG:
            state = {
                ...state,
                language: action.payload,
            };

            break;
        default:
            break;
    }
    return state;
}
