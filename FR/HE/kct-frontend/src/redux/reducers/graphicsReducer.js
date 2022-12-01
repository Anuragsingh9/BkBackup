/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Auth reducer for the authenticated user data action handler
 * ---------------------------------------------------------------------------------------------------------------------
 */

import {KeepContact as KCT} from '../types';
import eventGraphicReduxHelper from "../models/EventGraphics";

const initialState = {
    applicationGraphics: {},
    eventGraphics: {},
    eventScenery: null,
}


export default function GraphicsReducer(state = initialState, action) {
    switch (action.type) {
        case KCT.GRAPHICS.UPDATE_APPLICATION_GRAPHICS:
            // todo update it, when it is start to be in use
            break;
        case KCT.GRAPHICS.UPDATE_EVENT_GRAPHICS:
            state = {
                ...state,
                eventGraphics: eventGraphicReduxHelper.validateGraphicsObject({
                    ...state.eventGraphics,
                    ...action.payload,
                })
            }
            break;
        case KCT.GRAPHICS.UPDATE_EVENT_SCENERY:
            state = {
                ...state,
                eventScenery: {
                    ...state.eventScenery,
                    ...action.payload,
                }
            }
            break;
        default:
            break;
    }
    return state;
}