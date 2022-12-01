import React from 'react';
import NavTabs from '../../Common/NavTabs/NavTabs.js';
import ProfileIcon from '../../Svg/ProfileIcon.js';
import SupervisorAccountIcon from '@material-ui/icons/SupervisorAccount';
import ManageAllList from './ManageAllList/ManageAllList.js';
import './ManageGroupUsers.css';


/**
 * @class
 * @component
 * 
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a wrapper component to show manage all group participants list component and render in in a
 * horizontle nav tab component.
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @returns {JSX.Elelemt} 
 */
const ManageGroupUsers = (props) => {
    const [state, setState] = React.useState(null)

    const tabData = [
        {
            label: 'ALL',
            href: '/contacts',
            child: <ManageAllList />

        }
    ]

    return (
        <>
            <div className="userSettingTabs">
                <ProfileIcon />
                <NavTabs tabId="user-tabs" tabData={tabData} />
            </div>
        </>
    )
}

export default ManageGroupUsers;