import React from 'react';
import ReactDOM from 'react-dom';
import './index.css';
import App from './App';
import reportWebVitals from './reportWebVitals';
import * as serviceWorker from './serviceWorker';
import {I18nextProvider} from "react-i18next";
import i18n from './i18n';
import {BrowserRouter as Router} from 'react-router-dom';
import ReactGA from "react-ga4";




if (module.hot) {
    module.hot.accept()
}

ReactGA.initialize(process.env.REACT_APP_HE_GA4_MEASUREMENT_ID || 'G-SWDF9HCBVK');
ReactGA.send("pageview");

const root = ReactDOM.createRoot(document.getElementById('root'));

root.render(
    <React.StrictMode>
        <I18nextProvider i18n={i18n}>
            <Router basename={"/e"}>
                    <App />
            </Router>
        </I18nextProvider>
    </React.StrictMode>
);

// If you want your app to work offline and load faster, you can change
// unregister() to register() below. Note this comes with some pitfalls.
// Learn more about service workers: https://bit.ly/CRA-PWA
serviceWorker.unregister();

reportWebVitals();
