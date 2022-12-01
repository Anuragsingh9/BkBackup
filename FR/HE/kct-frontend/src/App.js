import React from 'react';
import './App.css';
import {Provider} from 'react-redux';
import configureStore from './redux/rootreducer'
import AppRoutes from "./routes";
import {transitions, positions, Provider as AlertProvider} from 'react-alert'
import AlertTemplate from 'react-alert-template-basic'

// optional configuration
const options = {
    // you can also just use 'top center'
    position: positions.TOP_CENTER,
    timeout: 5000,
    offset: '30px',
    // you can also just use 'scale'
    transition: transitions.SCALE
}


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Application Container to load the routes, redux and application data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @class
 * @component
 * @constructor
 * @returns {JSX.Element}
 */
function App() {
    return (
        <Provider store={configureStore()}>
            <div className="App">

                <AlertProvider template={AlertTemplate} {...options}>
                    <AppRoutes />
                </AlertProvider>
            </div>
        </Provider>
    );
}

export default App;
