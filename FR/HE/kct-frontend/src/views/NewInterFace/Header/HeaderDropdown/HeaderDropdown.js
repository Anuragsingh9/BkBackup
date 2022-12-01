import React from 'react';
import {NavLink} from 'react-router-dom'
import Helper from '../../../../Helper.js';
import _ from 'lodash';
import {useTranslation} from 'react-i18next';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description - This component is used to render dropdown inside header component which contains of options like
 * My Active Event / Event list , Change password , Logout and My event registrations these options are used for
 * navigation inside the interface.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {UserBadge} props.event_badge User badge details
 *
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const HeaderDropDown = (props) => {
    const {t} = useTranslation('headerDropDown');
    const {activeEventId, event_badge} = props;
    return (
        <div className="dropdown">
            <a className="drop-btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false">
                {_.has(event_badge, ['user_fname']) ?
                    event_badge.user_avatar ?

                        <img className="badge-avtar" width="30px" height="30px" src={event_badge.user_avatar} />
                        :
                        <div
                            className="username-slider-dp">{Helper.nameProfile(event_badge.user_fname, event_badge.user_lname)}</div>
                    :
                    ''
                }
                <span className="fa fa-chevron-down"></span>
            </a>
            <div className="dropdown-menu" aria-labelledby="dropdownMenuLink">
                {/*<NavLink className="dropdown-item" to={activeEventId?`/dashboard/${activeEventId}`:"/event-list"}>{t("My Active Event")}*/}
                {/*</NavLink>*/}
                <NavLink className="dropdown-item" to="/event-list">{t("My event registrations")}
                </NavLink>
                <NavLink className="dropdown-item" to="/change-password">{t("Change Password")}
                </NavLink>
                <div className="dropdown-item" onClick={props.logout}>{t("Logout")}
                </div>
            </div>
        </div>
    )
}

export default HeaderDropDown;