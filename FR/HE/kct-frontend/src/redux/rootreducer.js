/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Root reducer contains the linking of the other reducers of application
 * It combine all the reducers to able them provide different type of reducers
 * ---------------------------------------------------------------------------------------------------------------------
 */

import {applyMiddleware, combineReducers, createStore} from "redux";
import {reducer as formReducer} from 'redux-form';
import thunk from "redux-thunk";
import {pageCustomizationReducer} from "./reducers/pageCustomizationReducer";
import AuthReducer from "./reducers/authReducer";
import NewInterfaceReducer from './reducers/NewInterfaceReducer.js';
import {composeWithDevTools} from "redux-devtools-extension";
import GraphicsReducer from "./reducers/graphicsReducer";
import DashboardReducer from "./reducers/dashboardReducer";

const rootReducer = combineReducers({
    page_Customization: pageCustomizationReducer,
    AuthReducer: AuthReducer,
    form: formReducer,
    NewInterface: NewInterfaceReducer,
    Graphics: GraphicsReducer,
    Dashboard: DashboardReducer,
});

const configureStore = () => {
    return createStore(rootReducer,
        composeWithDevTools(
            applyMiddleware(thunk)
        )
    );
};

export default configureStore;
