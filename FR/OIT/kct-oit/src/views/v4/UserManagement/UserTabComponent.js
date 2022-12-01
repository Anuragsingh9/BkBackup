import DeleteIcon from '@mui/icons-material/Delete';
import SearchBar from "../../Common/SearchBar/SearchBar";
import TabList from "@mui/lab/TabList";
import React, {useEffect, useState} from "react";
import {confirmAlert} from "react-confirm-alert";
import UserManageHelper from "./UserManageHelper";
import Tab from "@mui/material/Tab";
import _ from "lodash";
import {connect, useDispatch} from "react-redux";
import {useTranslation} from "react-i18next";
import {useAlert} from "react-alert";
import useEventUserData from "./Containers/UserManageContainer";
import {useParams} from "react-router-dom";
import {Box, Typography} from "@mui/material";
import UserTableList from "./UserTableList";
import './UserManagement.css'
import {IconButton} from '@material-ui/core';
import UserObj from "../../../Models/User";
import LinkTab from '../Common/TableList/LinkTab';
import TabPanel from '../Common/TableList/TabPanel';
import LoadingSkeleton from '../../Common/Loading/LoadingSkeleton';
import UserListSkeleton from '../Skeleton/UserListSkeleton';



/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used for User manage component list(Participants, Event team)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Passed from parent component
 * @param {Boolean} props.fetch Used for re-fetch the user list
 * @param {Number} props.setValue Used for set the user tab
 * @param {String} props.redirectUserValue url of user tab
 * @param {Number} props.value Used for user tab
 * @returns {JSX.Element}
 * @constructor
 */
let UserTabComponent = (props) => {
    const params = useParams();
    const dispatch = useDispatch();
    const alert = useAlert();
    const {t} = useTranslation(["roleIcons", "confirm", "labels"]);
    const [loading, setLoading] = useState(true)

    // This state used for show count of rows selected in user list table
    const [selectedRows, setRow] = useState([]);

    // This state is used for initialize the default value of server side pagination
    const [tableMetaData, setTableMetaData] = useState({
        reFetch: false,
        page: 1,
        rowPerPage: 10,
    });

    useEffect(() => {
        setTimeout(() => {
            setLoading(false);
        }, 400)
    }, [])
    useEffect(() => {
        callBackForReFetchUser();
    }, [props.fetch])

    const userData = useEventUserData({...props, tableMetaData: tableMetaData});

    // This state used for to exclude user which is not delete
    const [deleteExcluded, setDeleteExcluded] = useState([]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for set the default value for table meta data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const callBackForReFetchUser = () => {
        setTableMetaData({...tableMetaData, reFetch: true})
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to confirm before deleting a user data and fetch the api and updates on sever
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const triggerDeleteConfirm = () => {
        confirmAlert({
            message: `${t("confirm:confirmMessage")}` + `${deleteExcluded.length > 0 ? deleteExcluded + `This user can be ignore` : ``}`,
            buttons: [
                {
                    label: `${t("confirm:yes")}`,
                    onClick: () => {
                        UserManageHelper.deleteUser({
                            ...props,
                            alert: alert,
                            t: t,
                            toDeleteUserData: selectedRows
                        },
                            dispatch,
                            callBackForReFetchUser
                        );
                    },
                },
                {
                    label: `${t("confirm:no")}`,
                    onClick: () => {
                        return null;
                    },
                },
            ],
        });

    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for switching the user tabs(Event Team, participants)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} event Javascript event object
     * @param {Number} newValue User tab new value
     */
    const handleChange = (event, newValue) => {
        props.setValue(newValue);
        if (props.redirectUserValue) {
            props.history.push(props.redirectUserValue[newValue]);
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method give the data for users
     * 1. User details which is not delete(Names, ids)
     * 2. Provide user ids which is delete
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {UserObj} data Object of user details
     */
    const checkUserDeleteOrNot = (data) => {
        let flag = false;
        let users = userData.data.filter(function (e) {
            for (let i = 0;i < data.length;i++) {
                if (e.id === data[i]) {
                    flag = true
                }
                if (flag !== false && (e.id === data[i]) && (e.is_moderator === 1 || e.is_presenter === 1 || e.is_organiser === 1 || e.is_space_host === 1)) {
                    return e;
                }
            }
        });

        // This provide the user names that will be not deleted
        let userNames = users.map(function (value) {
            return value.name
        });
        setDeleteExcluded(userNames);

        // This provide the user ids that will be not deleted
        let userIds = users.map(function (value) {
            return value.id
        });

        // This provide the user ids that will be deleted
        let userIdsToDelete = data.filter(e => !userIds.includes(e));
        setRow(userIdsToDelete);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Tabs of User management tab(Event Team, Participants)
     * -----------------------------------------------------------------------------------------------------------------
     */
    const userTabData = [
        {
            label: 'Event Team',
            href: '/user/event-team',
            disable: !_.has(params, ['event_uuid']),
            child: <UserTableList
                userTableData={userData}
                setRow={checkUserDeleteOrNot}
                setTableMetaData={setTableMetaData}
                tableMetaData={tableMetaData}
                callBack={callBackForReFetchUser}
                {...props}

            />
        },
        {
            label: 'Participants',
            href: '/user/participants',
            disable: !_.has(params, ['event_uuid']),
            child:
                <UserTableList
                    userTableData={userData}
                    setRow={checkUserDeleteOrNot}
                    setTableMetaData={setTableMetaData}
                    tableMetaData={tableMetaData}
                    callBack={callBackForReFetchUser}
                    {...props}

                />
        }
    ];

    return (
        <LoadingSkeleton loading={loading} skeleton={<UserListSkeleton/>}>
            <div className="userListTopTabRow">
                <TabList onChange={handleChange} aria-label="vertical_tab" value={props.value} className="userListTabList">

                    {userTabData.map((item, key) => (
                        <LinkTab
                            label={item.label}
                            href={item.href}
                            disabled={item.disable}
                        >
                        </LinkTab>
                    ))}

                </TabList>
                <span className="searchDeleteBtnRow">
                    {selectedRows.length > 0 && (
                        <IconButton
                            size='small'
                            color="secondary"
                            onClick={() => {
                                triggerDeleteConfirm();
                            }}
                        >
                            <DeleteIcon />
                        </IconButton>
                    )}
                    &nbsp;&nbsp;
                    <SearchBar
                        type={"regular"}
                    />
                </span>
                {
                    userTabData.map((item, key) => {
                        return (
                            <TabPanel value={props.value} index={key}>
                                {item.child}
                            </TabPanel>
                        )
                    })
                }
            </div>
        </LoadingSkeleton>
    )
}

const mapStateToProps = (state) => {
    return {
        searched_key: state.Group.searched_key,
        sort_user_model: state.Event.sort_user_model,
    };
};

UserTabComponent = connect(mapStateToProps,null)(UserTabComponent);

export default UserTabComponent;