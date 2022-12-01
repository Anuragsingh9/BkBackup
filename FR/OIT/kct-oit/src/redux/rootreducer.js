/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description In this file all the other reducers are combined and passed to redux store so redux store will get the
 * reducers for all the defined redux action in single store
 * ---------------------------------------------------------------------------------------------------------------------
 */

import {applyMiddleware, combineReducers, createStore} from "redux";
import {reducer as formReducer} from 'redux-form';
import thunk from "redux-thunk";
import AuthReducer from "./reducer/authReducer";
import EventReducer from "./reducer/eventReducer";
import GroupReducer from "./reducer/groupReducer";
import AnalyticsReducer from "./reducer/analyticsReducer";

const rootReducer = combineReducers({
    Auth: AuthReducer,
    Event: EventReducer,
    Group: GroupReducer,
    Analytics: AnalyticsReducer,
    form: formReducer,
});

const configureStore = () => {
    return createStore(rootReducer, applyMiddleware(thunk));
};

export default configureStore;
