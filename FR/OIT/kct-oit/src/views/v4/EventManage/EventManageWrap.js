import React from 'react'
import TopBar from './TopBar'
import {useLocation, useParams} from "react-router-dom";
import Constants from "../../../Constants";

const EventManageWrap = (props) => {
    const {gKey,event_uuid} = useParams();

    const location = useLocation();


    const routeValues = [
        `/${gKey}/v4/${event_uuid ? `event-update/${event_uuid}` : 'event-create'}`,
        `/${gKey}/v4/event/media/${event_uuid}`,
        `/${gKey}/v4/event/user/${event_uuid}`,
        `/${gKey}/v4/event/analytics/${event_uuid}`
    ]
    return (
        <>
            <TopBar
                redirectValue={routeValues}
                tabValue={props.tabValue}
                formMode={location.state?.formMode || Constants.eventFormType.CAFETERIA}
                {...props}
            />
        </>
    )
}

export default EventManageWrap