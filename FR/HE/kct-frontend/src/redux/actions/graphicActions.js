import {KeepContact} from "../types";

const updateEventGraphics = (eventGraphics) => dispatch => {
    return dispatch({
        type: KeepContact.GRAPHICS.UPDATE_EVENT_GRAPHICS,
        payload: eventGraphics,
    })
}

const updateEventScenery = (eventGraphics) => dispatch => {
    return dispatch({
        type: KeepContact.GRAPHICS.UPDATE_EVENT_SCENERY,
        payload: eventGraphics,
    })
}


const graphicActions = {
    updateEventGraphics: updateEventGraphics,
    updateEventScenery: updateEventScenery,
}


export default graphicActions;