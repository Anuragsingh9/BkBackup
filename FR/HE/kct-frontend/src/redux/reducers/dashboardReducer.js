/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file provides the reducer methods for the application related reducer actions
 * ---------------------------------------------------------------------------------------------------------------------
 */
import {KeepContact} from "../types";

const initialState = {
    networkingState: {
        allowed: true,
    },
    gridState: {
        maxColumn: 12,
        maxRow: 4,
    }
}


export default function DashboardReducer(state = initialState, action) {
    switch (action.type) {
        case KeepContact.DASHBOARD.UPDATE_NETWORKING_ALLOW:
            state = {
                ...state,
                networkingState: {
                    ...state.networkingState,
                    allowed: !!action.payload,
                }
            }
            break;
        default:
            return state;
    }
    return state;
}