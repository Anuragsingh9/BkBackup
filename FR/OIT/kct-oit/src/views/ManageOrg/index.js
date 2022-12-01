import React from 'react';
import MainManageOrg from '../UserSettings/ManageOrganiser/index.js';
import NavTabs from '../Common/NavTabs/NavTabs.js';
import ProfileIcon from '../Svg/ProfileIcon.js';
import './MangeOrg.css';
import BreadcrumbsInput from "../v4/Common/Breadcrumbs/BreadcrumbsInput";
import Constants from '../../Constants';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used as a container component  which is providing horizontal tab structure for Manage
 * Organiser component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Route related props to handle page navigation for manage organizer page eg - history,match,location
 * @return {JSX.Element}
 */
const ManageOrg = (props) => {

    const [state, setState] = React.useState(null)
    //array of object to show tabs(horizontal tab's data)
    // Array of object which is used to add horizontal tabs in nav tab(common) component.const tabData = [
    const tabData = [
        {
            label: 'Pilots & Owners',
            href: '/contacts',
            child: <MainManageOrg {...props} />
        },
    ]
    return (
        <>
            <BreadcrumbsInput
                links={[
                    // Constants.breadcrumbsOptions.GROUPS_LIST,
                    Constants.breadcrumbsOptions.GROUP_NAME,
                    Constants.breadcrumbsOptions.MANAGE_PILOT_AND_OWN,
                ]}
            />
            <div className="ManageOrgListDiv">
                {/* <ProfileIcon /> */}
                <NavTabs tabId="user-tabs" tabData={tabData}  {...props} />
            </div>
        </>
    )
}

export default ManageOrg;