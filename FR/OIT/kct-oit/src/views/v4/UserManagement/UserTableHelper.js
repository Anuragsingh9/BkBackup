import React from "react";
import UserManageHelper from "./UserManageHelper";
import {confirmAlert} from "react-confirm-alert";
import ModifyUserRole from "../Common/TableList/ModifyUserRole";
import Tooltip from "@material-ui/core/Tooltip";
import PilotRoleIcon from "../../Svg/PilotRoleIcon";
import TeamAIcon from "../Svg/TeamAIcon";
import TeamBIcon from "../Svg/TeamBIcon";
import SpeakerIcon from "../Svg/SpeakerIcon";
import ParticipantIcon from "../Svg/ParticipantIcon";
import VIPRoleIcon from "../Svg/VIPRoleIcon";
import SpaceHostRoleIcon from "../Svg/SpaceHostRoleIcon";
import ModeratorRoleIcon from "../Svg/ModeratorRoleIcon";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is used to confirm before role change
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Passed from parent component
 * @param {Number} userData User's Id
 * @param {Number} type Role type value in number
 */
const triggerConfirm = (props, userData, type) => {
    const users = [userData];
    confirmAlert({
        message: `${props.t("confirm:confirmMessage")}`,
        buttons: [
            {
                label: `${props.t("confirm:yes")}`,
                onClick: () => {
                    UserManageHelper.updateUserRole(props, users, type, props.callBack);
                },
            },
            {
                label: `${props.t("confirm:no")}`,
                onClick: () => {
                    return null;
                },
            },
        ],
    });
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function is used to render modify option for a particular user from the list
 * in manage participants & roles component.This will return modify label menu component with limited
 * actions(allowed as per the roles eg- set as participants/team/expert/vip) depends upon current
 * selected user role.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Passed from parent component
 * @param {Object} params Params for rendering cell actions
 * @param {EventUser} params.row Data required to performs actions over single row
 * @returns {JSX.Element}
 */
const renderCellActions = (params, props) => {
    const {row} = params;
    let result;
    if (
        (row.is_vip === 1 &&
            row.is_organiser === 0 &&
            row.is_moderator === 0 &&
            row.is_presenter === 0 &&
            row.is_space_host === 0 &&
            row.event_user_role === null
        )
        ||
        (row.is_vip === 0 &&
            row.is_organiser === 0 &&
            row.is_moderator === 0 &&
            row.is_presenter === 0 &&
            row.is_space_host === 0 &&
            row.event_user_role === null
        )
    ) {
        result = [];
        result.push(
            {
                name: `${props.t("labels:TeamA")}`,
                role: 1,
                callBack: () => {
                    triggerConfirm(props, row.id, 1);
                },
            },
            {
                name: `${props.t("labels:TeamB")}`,
                role: 2,
                callBack: () => {
                    triggerConfirm(props, row.id, 2);
                },
            },
        )
        if (row.is_vip === 1) {
            result.push(
                {
                    name: `${props.t("labels:Participants")}`,
                    role: 0,
                    callBack: () => {
                        triggerConfirm(props, row.id, 0);
                    },
                },
            )
        } else {
            result.push(
                {
                    name: `${props.t("labels:VIP")}`,
                    role: 3,
                    callBack: () => {
                        triggerConfirm(props, row.id, 3);
                    },
                },
            )
        }
    } else {
        result = [];
        if (row.event_user_role === 1 || row.event_user_role === 2) {
            result.push({
                    name: `${props.t("labels:Participants")}`,
                    role: 0,
                    callBack: () => {
                        triggerConfirm(props, row.id, 0);
                    },
                },
                {
                    name: `${props.t("labels:VIP")}`,
                    role: 3,
                    callBack: () => {
                        triggerConfirm(props, row.id, 3);
                    },
                },
            );
            if (row.event_user_role === 1) {
                result.push(
                    {
                        name: `${props.t("labels:TeamB")}`,
                        role: 2,
                        callBack: () => {
                            triggerConfirm(props, row.id, 2);
                        },
                    },
                );
            } else {
                result.push(
                    {
                        name: `${props.t("labels:TeamA")}`,
                        role: 1,
                        callBack: () => {
                            triggerConfirm(props, row.id, 1);
                        },
                    }
                );
            }
        }
    }
    return <ModifyUserRole data={result} user={row} />;
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description  This function is used to render user role options(participants, vip, space host, organiser, Team A,
 * Team B) for a particular user from the list in manage participants & Event team component.This will return user role
 * icon (roles icon eg- set as participants/team/expert/vip) depends upon current selected user role.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} params Params for rendering cell actions
 * @param {Object} props Passed from parent component
 * @param {Function} props.t Function is used for localization
 * @returns {JSX.Element}
 */
const renderRoleAction = (params, props) => {
    const {row} = params;
    return (
        <div className="UserList__roleColumnCell">
            {row.is_vip === 1 && (
                <>
                    <Tooltip arrow title={props.t("labels:VIP")}>
                        <div className="role_icon_cell">
                            <VIPRoleIcon />
                        </div>
                    </Tooltip>
                </>
            )}
            {row.is_organiser === 1 && (
                <>
                    <Tooltip arrow title={props.t("labels:Organiser")}>
                        <div className="role_icon_cell">
                            <PilotRoleIcon />
                        </div>
                    </Tooltip>
                </>
            )}
            {row.is_moderator === 1 && (
                <>
                    <Tooltip arrow title={props.t("labels:Moderator")}>
                        <div className="role_icon_cell">
                            <ModeratorRoleIcon />
                        </div>
                    </Tooltip>
                </>
            )}
            {row.event_user_role === 1 && (
                <>
                    <Tooltip arrow title={props.t("labels:TeamA")}>
                        <div className="role_icon_cell">
                            <TeamAIcon />
                        </div>
                    </Tooltip>
                </>
            )}
            {row.event_user_role === 2 && (
                <>
                    <Tooltip arrow title={props.t("labels:TeamB")}>
                        <div className="role_icon_cell">
                            <TeamBIcon />
                        </div>
                    </Tooltip>
                </>
            )}
            {row.is_space_host === 1 && (
                <>
                    <Tooltip arrow title={props.t("labels:SpaceHost")}>
                        <div className="role_icon_cell">
                            <SpaceHostRoleIcon />
                        </div>
                    </Tooltip>
                </>
            )}
            {row.is_presenter === 1 && (
                <>
                    <Tooltip arrow title={props.t("labels:Speaker")}>
                        <div className="role_icon_cell">
                            <SpeakerIcon />
                        </div>
                    </Tooltip>
                </>
            )}
            {row.is_vip === 0 &&
            row.is_organiser === 0 &&
            row.is_moderator === 0 &&
            row.is_presenter === 0 &&
            row.is_space_host === 0 &&
            row.event_user_role === null && (
                <>
                    <Tooltip arrow title={props.t("labels:Participant")}>
                        <div className="role_icon_cell">
                            <ParticipantIcon />
                        </div>
                    </Tooltip>
                </>
            )}
        </div>
    );
}

let UserTableHelper = {
    renderCellActions,
    renderRoleAction
}

export default UserTableHelper