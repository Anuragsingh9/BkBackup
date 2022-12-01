import React from 'react';
import {createTheme, ThemeProvider} from '@material-ui/core/styles';
import Routes from '../src/routes/index.js';
import {Provider} from 'react-redux';
import configureStore from './redux/rootreducer';
import './i18n';
import moment from "moment-timezone";
import {LicenseInfo} from "@mui/x-date-pickers-pro";

/**
 * @class
 * @component
 * 
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for Main App Rendering and provide route and theme for all components.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {JSX.Element}
 */
const HCTTheme = createTheme({
  palette: {
    primary: {
      main: '#0589B8',
    },
    secondary: {
      main: '#3b3b3b',
    },
  },
});

LicenseInfo.setLicenseKey(process.env.REACT_APP_MUI_PRO_KEY);

function App() {
  moment.tz.setDefault("Europe/Paris");
  return (
    <Provider store={configureStore()}>
      <ThemeProvider theme={HCTTheme}>
        <div className="App">
          <Routes />
        </div>
      </ThemeProvider>
    </Provider>
  );
}

export default App;
