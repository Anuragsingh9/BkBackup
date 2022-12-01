import React from 'react';
import HeaderLogo from "./HeaderLogo.js"
import {
    Container,
    Grid,
} from '@material-ui/core';
import SubMenu from './SubMenu/Submenu.js';
import Dropdown from './Dropdown/Dropdown.js';
import './Header.css';
import {useParams} from "react-router-dom";
import SettingDropdown from './SettingDropdown/SettingDropdown.js';
import {useSelector, useDispatch} from 'react-redux';
import _ from 'lodash';
import EventsDropdown from './EventsDropdown/EventsDropdown.js';
import DashboardDropdown from './DashboardDropdown/DashboardDropdown.js';
import CustomContainer from "../Common/CustomContainer/CustomContainer"

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a navbar component which consist logo(in left side) and current logged in user's profile image
 * component with a dropdown to navigate(profile page, change password page and logout from the application).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component is received route related props eg - history,location,match
 * @returns {JSX.Element}
 * @constructor
 */
const Header = (props) => {
    const {gKey} = useParams();
    const user_badge = useSelector((data) => data.Auth.userSelfData);
    const userOnSetPswd = window.location.pathname == "/oit/set-password";

    return (
        <>
            <header className="NavBar">
                <>
                    <CustomContainer>

                        <Grid container xs={6} lg={9} spacing={0} className="header_leftDiv">
                            {
                                window.location.pathname !== "/oit/set-password"
                                    ?
                                    <div onClick={() => props.history.push(`/${gKey}/dashboard`)} className="headerLogo">
                                        <HeaderLogo className="navLogo" />
                                    </div>
                                    :
                                    <div className="headerLogo">
                                        <HeaderLogo className="navLogo" />
                                    </div>
                            }
                            {!userOnSetPswd &&
                                <>
                                    <DashboardDropdown  {...props} />
                                    <EventsDropdown {...props} />
                                </>
                            }
                        </Grid>
                        <Grid className="profileDiv" item xs={6} lg={3}>
                            {(_.has(user_badge, ['is_organiser']) && user_badge.is_organiser == 1) && !userOnSetPswd &&
                                <SettingDropdown {...props} />
                            }
                            <Dropdown {...props} />
                        </Grid>
                    </CustomContainer>
                </>
            </header>
            {/* <SubMenu {...props} /> */}
        </>
    )
}
export default Header