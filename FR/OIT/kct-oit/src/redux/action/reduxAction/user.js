/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description In this file user related redux action types are handled by switch case
 * so when a specific case of redux action is triggered this file will provide the handler for that specific redux
 * action type
 * ---------------------------------------------------------------------------------------------------------------------
 *
 */
import { useGridControlState } from '@material-ui/data-grid';
import { KeepContact as KCT } from '../../types';

const setUserData = (data) => dispatch => {
    return dispatch({
        type: KCT.AUTH.USER_SET_SELF,
        payload: data
    })
}

const setUsersMetaData = (data) => dispatch => {
    return dispatch({
        type: KCT.AUTH.SET_USER_META_DATA,
        payload: data
    })
}

const setLabelData = (data) => dispatch => {
    return dispatch({
        type: KCT.AUTH.EVENT_ROLE_LABLES,
        payload: data
    })
}

const setLang = (data) => dispatch =>{
    return dispatch({
        type: KCT.AUTH.SET_LANG,
        payload : data
    })
}

const setAppSettings = (data) => dispatch => {
    return dispatch({
        type: KCT.AUTH.APP_SETTINGS,
        payload: data
    })
}

const userAction = {
    setUserData,
    setLabelData,
    setLang,
    setAppSettings,
    setUsersMetaData
}

export default userAction;