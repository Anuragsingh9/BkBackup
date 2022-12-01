import TabList from "@mui/lab/TabList";
import React from "react";
import EngagementComp from "./Engagement/EnagagementComp";
import EventTypeComp from "./EventType/EventTypeComp";
import UsersComp from "./Users/UsersComp";
import LinkTab from "../Common/TableList/LinkTab";
import TabPanel from "../Common/TableList/TabPanel";
import GroupDropDown from "./GroupDropDown";
import {connect} from "react-redux";


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used for User manage component list(Participants, Event team)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Passed from parent component
 * @param {Number} props.setValue Used for set the analytics tab
 * @param {String} props.analyticsRedirectValue url of analytics tab
 * @param {Number} props.value Used for analytics tab
 * @returns {JSX.Element}
 * @constructor
 */
let AnalyticsTabComponent = (props) => {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handling the tabs
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} event Javascript event object
     * @param {String} newValue url value
     */
    const handleChange = (event, newValue) => {
        props.setValue(newValue);
        if (props.analyticsRedirectValue) {
            props.history.push(props.analyticsRedirectValue[newValue]);
        }
    };

    const analyticsTabData = [
        {
            label: 'Engagement',
            href: '/engagement',
            disable: false,
            child: <EngagementComp {...props} />
        },
        {
            label: 'Users',
            href: '/users',
            disable: true,
            child: <UsersComp {...props} />,
        },
        {
            label: 'Event Type',
            href: '/eventType',
            disable: true,
            child: <EventTypeComp {...props} />,
        },
    ]

    return (
        <>
            <div className='verticleTab__EventManage'>
                <TabList onChange={handleChange} aria-label="vertical_tab" value={props.value}>
                    {analyticsTabData.map((item, key) => (
                        <LinkTab
                            label={item.label}
                            href={item.href}
                            disabled={item.disable}
                        />
                    ))}

                    {/*group drop down*/}

                </TabList>
                <GroupDropDown />
            </div>
            {
                analyticsTabData.map((item, key) => {
                    return (
                        <TabPanel value={props.value} index={key}>
                            {item.child}
                        </TabPanel>
                    )
                })
            }
        </>
    )
}

const mapStateToProps = (state) => {
    return {
        fetch_analytic_group_dropdown: state.Group.analytic_group_dropdown,
    };
};

AnalyticsTabComponent = connect(mapStateToProps, null)(AnalyticsTabComponent)

export default AnalyticsTabComponent;