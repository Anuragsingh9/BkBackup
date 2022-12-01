import React from "react";
import UserManagementTopBar from "./UserManagementTopBar";
import {useParams} from "react-router-dom";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is wrapper component of user management component
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Passed from parent component
 * @returns {JSX.Element}
 * @constructor
 */
const UserManageWrap = (props) => {
    const {gKey, event_uuid} = useParams();

    const routeValues = [
        `/${gKey}/v4/event/user/event-team/${event_uuid}`,
        `/${gKey}/v4/event/user/participants/${event_uuid}`
    ]
    return (
        <>
            <UserManagementTopBar
                redirectUserValue={routeValues}
                userTabValue={props.userTabValue}
                {...props}
            />
        </>
    )
}

export default UserManageWrap;