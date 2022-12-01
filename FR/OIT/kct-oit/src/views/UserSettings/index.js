import React from 'react';
import NavTabs from '../Common/NavTabs/NavTabs.js';
import ProfileIcon from '../Svg/ProfileIcon.js';
import ManageUser from './ManageUser/index.js';
import './UserSettings.css';
import Constants from "../../Constants";
import BreadcrumbsInput from "../v4/Common/Breadcrumbs/BreadcrumbsInput";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used as a container component  which is providing horizontal tab structure for Manage
 * User component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props  Route related props to handle page navigation for manage organizer page like history,location,match.
 * @return {JSX.Element}
 */
const UserSettings = (props) => {
    const [state, setState] = React.useState(null)
    const [dropdown, setDropdown] = React.useState(0);


    const tabData = [
        {
            label: 'ALL',
            href: '/contacts',
            child: <ManageUser
                dropdown={dropdown}
                setDropdown={setDropdown}
                {...props}
            />

        },
        // {
        //     label:'ADD USER',
        //     href:'/add-user',
        //     child: <AddUserContainer {...props} />

        // }
    ]

    return (
        <>
            <BreadcrumbsInput
                links={[
                    // Constants.breadcrumbsOptions.GROUPS_LIST,
                    Constants.breadcrumbsOptions.GROUP_NAME,
                    dropdown === 0 ? Constants.breadcrumbsOptions.MANAGE_USERS : null,
                    dropdown === 0
                        ? Constants.breadcrumbsOptions.ALL
                        : dropdown === 1
                            ? Constants.breadcrumbsOptions.ADD_USER
                            : dropdown === 2
                                ? Constants.breadcrumbsOptions.IMPORT_USER
                                : null,
                ]}
            />
            <div className="userSettingTabs">
                {/* <ProfileIcon /> */}
                <h3>Manage User</h3>
                <NavTabs tabId="user-tabs" tabData={tabData} />
            </div>
        </>
    )
}

export default UserSettings;