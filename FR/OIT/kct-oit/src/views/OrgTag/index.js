import React from 'react';
import Container from '@material-ui/core/Container';
import TagSettingIcon from '../Svg/TagSettingIcon.js';
import NavTabs from '../Common/NavTabs/NavTabs.js';
import EventTag from './OrgTags.js'
import './OrgTag.css';
import BreadcrumbsInput from "../v4/Common/Breadcrumbs/BreadcrumbsInput";
import Constants from "../../Constants";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used as a container component  which is providing horizontal tab structure for Manage
 * Tags component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received route related props eg - history,location,match
 * @return {JSX.Element}
 */
const OrgTagsWrap = (props) => {
    const [groupName,setGroupName] = React.useState(null)
    var params = props.match.params;

    React.useEffect(() => {
        const localData = localStorage.getItem("Current_group_data");
        const parseLocalData = JSON.parse(localData);
        const groupName = parseLocalData.group_name;
        console.log("isSuperGroup", groupName);
        setGroupName(groupName)
      }, []);

    const tabData = [
        {
            label: groupName ? `Manage tags for ${groupName} ` : `Manage tags for Group`  ,
            href:'/contacts',
            child: <EventTag {...props}/>
        },
    ]

    return (
        <>
            <BreadcrumbsInput
                links={[
                    // Constants.breadcrumbsOptions.GROUPS_LIST,
                    Constants.breadcrumbsOptions.GROUP_NAME,
                    Constants.breadcrumbsOptions.TAGS,
                ]}
            />
            <div className="OrgTagsWrap">
                {/* <TagSettingIcon className="TagIconMain" /> */}
                <NavTabs tabId="user-tabs" tabData={tabData} {...props} />
            </div>
        </>
    )
}
export default OrgTagsWrap