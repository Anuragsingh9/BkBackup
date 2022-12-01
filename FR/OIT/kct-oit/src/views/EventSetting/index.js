import React from 'react';
import './EventSetting.css';
import EventSettingIcon from '../Svg/EventSettingIcon.js';
import NavTabs from '../Common/NavTabs/NavTabs.js';
import DesignSettings from './DesignSetting/DesignSetting';
import TechnicalSettings from './TechnicalSettings/TechnicalSettings';
import {useParams} from "react-router-dom";
import Constants from "../../Constants";
import BreadcrumbsInput from "../v4/Common/Breadcrumbs/BreadcrumbsInput";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used as a container component  which is providing horizontal tab structure for the
 * Event setting page.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component is received route related props eg - history,location,match
 * @returns {JSX.Element}
 * @constructor
 */
const EventSetting = (props) => {
    const {gKey} = useParams();
    const [isSuperGroup, setIsSuperGroup] = React.useState(false);

    React.useEffect(() => {


        const localData = localStorage.getItem("Current_group_data");
        const parseLocalData = JSON.parse(localData);
        const isSuperGroup = parseLocalData.is_super_group;
        console.log("isSuperGroup", isSuperGroup);
        setIsSuperGroup(isSuperGroup == 1 ? true : false);
    }, [gKey]);

    // tabs options for setting
    const tabData = [
        {
            label: 'DESIGN SETTINGS',
            href: '/contacts',
            child: <DesignSettings {...props} />
        },
        // {
        //     label: 'EMAIL SETTINGS',
        //     href: '/add-user',
        //     disable: true,
        //     child: <h1>EMAIL setting</h1>
        // },
        {
            label: 'Technical SETTINGS',
            href: '/event-setting/technical-setting',
            disable: !isSuperGroup,
            child: <TechnicalSettings {...props} />
        },
        // {
        //     label: 'SECURITY SETTINGS',
        //     href: '/manage-org',
        //     disable: true,
        //     child: <h1>SECURITY setting</h1>
        // },
    ]

    // route values to update url link
    const routeValues = [
        `/${gKey}/event-setting`,
        // '',
        `/${gKey}/event-setting/technical-setting`,
        // '',
    ]

    return (
        <>
            <BreadcrumbsInput
                links={[
                    Constants.breadcrumbsOptions.GROUP_NAME,
                    props.name === 'Event Setting' ?
                        Constants.breadcrumbsOptions.DESIGN_SETTINGS
                        : (
                            props.name === 'Technical Setting'
                                ? Constants.breadcrumbsOptions.TECHNICAL_SETTINGS
                                : null
                        )
                ]}
            />
            <div className="DesignSettingWrap">
                {/* <EventSettingIcon /> */}
                <NavTabs tabId="user-tabs" tabData={tabData} redirectValue={routeValues} {...props} />
            </div>
        </>
    )
}
export default EventSetting