/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Auth reducer for the authenticated user data action handler
 * ---------------------------------------------------------------------------------------------------------------------
 */

import {KeepContact as KCT} from '../types';

const initialState = {
    eventDetailsData: null,
    eventDetailsLoad: false
}

export default function AuthReducer(state = initialState, action) {
    switch (action.type) {
        case KCT.AUTH.EVENT_DETAILS:
            state = {
                ...state,
                eventDetailsData: action.payload,
                eventDetailsLoad: true
            }
            break;
        default:
            break;
    }
    return state;
}