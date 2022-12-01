import DataTableServerSide from "../../Common/DataTable/serverSidePaginated";
import React, {useEffect} from "react";
import Helper from "../../../Helper";
import _ from "lodash";
import UserTableHelper from "./UserTableHelper";
import {connect, useDispatch} from "react-redux";
import {useTranslation} from "react-i18next";
import {useAlert} from "react-alert";
import {useParams} from "react-router-dom";
import "./UserTable.css"
import eventAction from "../../../redux/action/reduxAction/event";


let UserTableList = (props) => {

    console.log('usertab',props);
    const {gKey} = useParams();
    const dispatch = useDispatch();
    const alert = useAlert();
    const {t} = useTranslation(["roleIcons", "confirm", "labels"]);

    const [sortModel, setSortModel] = React.useState([
        {
            field: "lname", //last name
            sort: "asc",
        },
    ]);
    console.log("sort sm", sortModel);
    console.log("sort re", props.sort_user_model);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for update the default state of server side pagination
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} page This is the current page number
     */
    const fetchUserByPage = (page) => {
        props.setTableMetaData({...props.tableMetaData, page: page + 1});
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for update the default state of server side pagination
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} pageSize Numbers of row per page
     */
    const handlePageSizeChange = (pageSize) => {
        props.setTableMetaData({...props.tableMetaData, rowPerPage: pageSize});
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will simply open(redirect) the profile of the user of the given id.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Number} id User's Id
     */
    const userProfileHandler = (id) => {
        props.history.push(`/${gKey}/user-profile?id=${id}`);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Columns of tables
     * -----------------------------------------------------------------------------------------------------------------
     */
    let userTableColumns = [
        {
            field: 'lname', headerName: 'Name', width: 200, flex: 1, sortable: true,
            renderCell: (params) => {
                const {row} = params;
                return (
                    <span className="userNameLink" onClick={() => userProfileHandler(row.id)}>
                        {row.name}
                    </span>
                );
            },
        },
        {field: 'email', headerName: 'Email', width: 250, flex: 1, sortable: true,},
        {
            field: 'company',
            headerName: 'Company',
            width: 250,
            flex: 1,
            sortable: false,
            headerAlign: "left",
            editable: false,
            renderCell: (params) => {
                const {value} = params;
                return (
                    <span>
                        {Helper.limitText(
                            _.has(value, ["long_name"])
                                ? `${params.value.long_name}, ${
                                    params.value.position !== null ? params.value.position : ""
                                }`
                                : "",
                            24
                        )}
                    </span>
                );
            },
        },
        {
            field: 'registration',
            headerName: 'Registration',
            width: 160,
            sortable: true,
            renderCell: (params) => {
                const {row} = params;
                return (
                    <>
                        {
                            row.registration === 0 ? "NO" : "YES"
                        }
                    </>
                )
            }
        },
        {
            field: 'attendance',
            headerName: `${props.userTableData && props.current_event?.event_recurrence?.rec_type
                ? 'Attendance (Out of ' + props.userTableData.meta.events_count + ')'
                : 'Attendance'}`,
            width: 230,
            sortable: false,
            renderCell: (params) => {
                const {row} = params;
                return (
                    <>
                        {
                            props.userTableData && props.current_event?.event_recurrence?.rec_type
                                ? row.attendance
                                : row.attendance === 0
                                    ? "NO" : "YES"
                        }
                    </>
                )
            }
        },
        {
            field: "role",
            headerName: "Role",
            width: 100,
            // flex:0.5,
            sortable: false,
            headerAlign: "left",
            editable: false,
            renderCell: (params) => UserTableHelper.renderRoleAction(
                params,
                {
                    ...props,
                    t: t,
                }
            )
        },
        {
            field: "",
            headerName: "",
            headerClassName: "ActionTab",
            width: 80,
            sortable: false,
            headerAlign: "left",
            renderCell: (params) => UserTableHelper.renderCellActions(
                params,
                {
                    ...props,
                    dispatch: dispatch,
                    setTableMetaData: props.setTableMetaData,
                    tableMetaData: props.tableMetaData,
                    t: t,
                    alert: alert
                }
            ),
        },
    ];

    return (
        <DataTableServerSide
            columns={userTableColumns}
            rows={props.userTableData?.data || []}
            totalItems={props.userTableData?.meta.total}
            fetchList={fetchUserByPage}
            onPageSizeChange={handlePageSizeChange}
            onPageChange={fetchUserByPage}
            selectHandler={props.setRow}
            className="userListCustomDataGrid"
            disableColumnMenu={true}
            setSortModel={(model) => {
                if (
                    !_.isEmpty(model) &&
                    JSON.stringify(model[0]) !== JSON.stringify(props.sort_user_model)
                ) {
                    props.updateUserSortModel(model)
                }
            }}
            sortModel={props.sort_user_model}
        />
    )
}

const mapStateToProps = (state) => {
    return {
        sort_user_model: state.Event.sort_user_model,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        updateUserSortModel : (sortModal) => dispatch(eventAction.updateUserSortModel(sortModal)),
    }
}
UserTableList = connect(mapStateToProps,mapDispatchToProps)(UserTableList)

export default UserTableList;