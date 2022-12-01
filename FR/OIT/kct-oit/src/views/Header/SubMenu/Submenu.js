import React from 'react'
import {
    Container,
    Grid,
    Button,
} from '@material-ui/core';
import DashboardIcon from '@material-ui/icons/Dashboard';
import DashboardDropdown from '../DashboardDropdown/DashboardDropdown';
import {useSelector, useDispatch} from 'react-redux';
import _ from 'lodash';
import EventsDropdown from '../EventsDropdown/EventsDropdown';
import SettingDropdown from '../SettingDropdown/SettingDropdown';
import './Submenu.css'


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a parent component of sub menu bar(Black colored small nav under header component).Which consist
 * dashboard button, event dropdown component and setting dropdown component.
 * <br>
 * <br>
 * When user click on 'Dashboard' button this will land him on dashboard page from any page of the application.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props  route related props to handle page navigation for eg - history, location, match
 * @returns {JSX.Element}
 * @constructor
 */
const SubMenu = (props) => {
    const user_badge = useSelector((data) => data.Auth.userSelfData);

    return (
        <header className="SubMenuBar">
            {window.location.pathname !== "/oit/set-password" &&
            <Container>
                <Grid container spacing={3}>
                    <Grid item xs={6} lg={9}>
                        <Grid container spacing={0}>
                            <Grid className="submenu-div" >
                                    <DashboardDropdown  {...props}/>
                                {/* <Button onClick={() => { props.history.push('/dashboard') }} lg={6}>
                                    <DashboardIcon className="px-6" /> Dashboard</Button> */}

                                </Grid>
                                <Grid className="submenu-div" >
                                <EventsDropdown {...props} />
                            </Grid>
                        </Grid>
                    </Grid>
                    <Grid className="setting-submenu" item xs={6} lg={3}>
                        {(_.has(user_badge, ['is_organiser']) && user_badge.is_organiser == 1) &&
                        <SettingDropdown {...props} />
                        }
                    </Grid>
                </Grid>
            </Container>
            }
        </header>
    )
}
export default SubMenu