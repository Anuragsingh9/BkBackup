import React from "react";
import AnalyticsTopBar from "./AnalyticsTopBar";
import {useParams} from "react-router-dom";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used for wrapper component of analytics
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Passed from parent component
 * @returns {JSX.Element}
 * @constructor
 */
const AnalyticsWrap = (props) => {

    const {gKey} = useParams();

    const routeValues = [
        `/${gKey}/v4/engagement`,
        `/${gKey}/v4/users`,
        `/${gKey}/v4/eventType`
    ]

    return (
        <>
            <AnalyticsTopBar
                analyticsRedirectValue={routeValues}
                analyticsTabValue={props.tabValue}
                // formMode={location.state?.formMode || Constants.eventFormType.CAFETERIA}
                {...props}
            />
        </>
    )
}

export default AnalyticsWrap;