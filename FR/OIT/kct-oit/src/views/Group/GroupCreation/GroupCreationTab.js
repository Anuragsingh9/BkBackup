import {React, useState} from 'react';
import NavTabs from '../../Common/NavTabs/NavTabs.js';
import GroupCreation from './GroupCreation';
import GroupAddIcon from '@mui/icons-material/GroupAdd';
import _ from 'lodash';
import BreadcrumbsInput from "../../v4/Common/Breadcrumbs/BreadcrumbsInput";
import Constants from '../../../Constants';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a container Component for Create Event which provide a tab structure for group creation process.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component is received route related props eg - history,location,match
 * @returns {JSX.Element}
 */
const GroupCreationTab = (props) => {
    const [tabName, setTabName] = useState();
    const [groupData, setGroupData] = useState();

    const tabData = [
        {
            label: `${_.has(props.match.params, 'gKey') && props.mode !== 'create' ? `UPDATE ${!_.isUndefined(tabName) ? tabName : ""} GROUP` : 'CREATE GROUP'}`,
            href: '/contacts',
            child: <GroupCreation setTabName={setTabName} mode={props.mode} setGroup={setGroupData} />
        },
    ];

    return (
        <>
            <BreadcrumbsInput
                links={[
                    props.mode === 'create'
                        ? Constants.breadcrumbsOptions.GROUP_CREATE
                        : Constants.breadcrumbsOptions.GROUP_NAME,
                ]}
                group_data={groupData}
            />
            <div className="CreateEventWrap">
                <GroupAddIcon />
                <NavTabs tabId="user-tabs" tabData={tabData} />
            </div>
        </>
    )
}
export default GroupCreationTab