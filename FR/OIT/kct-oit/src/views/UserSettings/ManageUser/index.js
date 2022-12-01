import React, {useEffect, useState} from "react";
import {
    Badge,
    Grid,
    IconButton,
    MenuItem,
    Paper,
    Select,
} from "@material-ui/core";
import DataTable from "../../Common/DataTable";
import groupAction from "../../../redux/action/apiAction/group";
import userAction from "../../../redux/action/apiAction/user";
import {connect} from "react-redux";
import {useAlert} from "react-alert";
import Hstatus from "../../Group/common/HStatus/Hstatus";
import "./ManageUser.css";
import _ from "lodash";
import {confirmAlert} from "react-confirm-alert";
import TrashIcon from "../../Svg/TrashIcon";
import Helper from "../../../Helper";
import {useParams} from "react-router-dom";
import SearchBar from "../../Common/SearchBar/SearchBar";

import AddUser from "../AddUser/AddUser.js";
import ImportUser from "../ImportUser/index.js";
import ServerSideDataTable from "../../v4/Common/ServerSideDataTable/ServerSideDataTable";
import LoadingSkeleton from "../../Common/Loading/LoadingSkeleton";
import TableSkeleton from "../../v4/Skeleton/TableSkeleton";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This Component is used in Manage User page through which users can view all the
 * added users in the account for a specific group. This page also has the feature of adding more user into the same
 * account and check existence of a specific user with their respective details(first name, last name, company,
 * position, union and email) using search component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from the parent component
 * @param {Function} props.deleteUsers Function to delete group users
 * @param {Function} props.getUserData Function to fetch group users
 * @param {User} props.user_badge User's data
 * @return {JSX.Element}
 */
const ManageUser = (props) => {
    console.log("propsssss", props);

    const alert = useAlert();
    const {dropdown, setDropdown} = props;
    //state to manage selected row from a table
    const [selectedRows, setRow] = useState([]);
    const [tableRows, setRows] = useState([]);
    const {gKey} = useParams();
    const [pageSizeValue, setPageSizeValue] = useState(10);
    //state to manage sorting by roles from dropdown
    const [sortModel, setSortModel] = React.useState([
        {field: "lname", sort: "asc"},
    ]);

    const [rowsMetaData, setRowsMetaData] = useState();
    const [pageNumber, setPageNumber] = useState(1);

    const [callApi, setCallApi] = useState(false);
    const [loading, setLoading] = useState(true)

    const callAPI = (boolean) => {
        setCallApi(boolean);
    }
    useEffect(() => {
        if (callApi) {
            callAPI(callApi)
        }
    }, [])

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Method to set selected value from add user(import/manually) dropdown.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {object} event Javascript event object
     */
    const changeDropdown = (event) => {
        setDropdown(event.target.value);
        if (event.target.value === 0) {
            fetchUser();
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This is an array of object which is holding data of a specific column in a manner.
     * each object containing key value pairs
     * @example
     * {
     * field:string,
     * headerName:string,
     * className:string,
     * width:number,
     * headerAlign:string,
     * sortable:boolean,
     * editable:boolean,
     * renderCell:method,
     * }
     * -----------------------------------------------------------------------------------------------------------------
     */
    const columns = [
        {
            field: "lname",
            headerName: "Name",
            className: "text_primary_col",
            width: 200,
            flex: 1,
            headerAlign: "left",
            sortable: true,
            // sortingOrder: [ null, 'desc', 'asc'],
            align: "left",
            renderCell: (params) => {
                return (
                    <p
                        className="text_primary_col"
                        onClick={() => {
                            profileCallBack(params.row.id);
                        }}
                    >
                        {params.row.fname} {params.row.lname}
                    </p>
                );
            },
            sortComparator: customSort,
        },
        {
            field: "HStatus",
            headerName: "HStatus",
            width: 100,
            headerAlign: "center",
            align: "center",
            sortable: false,
            editable: false,
            renderCell: (cellValues) => {
                return <Hstatus />;
            },
        },
        {
            field: "company",
            headerName: "Company",
            width: 250,
            flex: 1,
            headerAlign: "left",
            sortable: true,
            editable: false,
            renderCell: (params) => {
                const {value} = params;
                return (
                    <span>
                        {Helper.limitText(
                            _.has(value, ["long_name"])
                                ? `${params.value.long_name}, ${params.value.position !== null ? params.value.position : ""
                                }`
                                : "",
                            24
                        )}
                    </span>
                );
            },
        },
        {
            field: "union",
            headerName: "Union",
            width: 280,
            flex: 1,
            sortable: true,
            headerAlign: "left",
            editable: false,
            renderCell: (params) => {
                const {row} = params;
                const {union} = row;

                if (!_.isEmpty(union)) {
                    const firstUnion = union[0];
                    console.log(firstUnion.position);
                    return (
                        <span>
                            {Helper.limitText(
                                _.has(firstUnion, ["long_name"])
                                    ? `${firstUnion.long_name}, ${firstUnion.position !== null ? firstUnion.position : ""
                                    }`
                                    : "",
                                24
                            )}
                        </span>
                    );
                }
                return <span></span>;
            },
        },
        {
            field: "email",
            headerName: "Email",
            width: 280,
            flex: 1,
            headerAlign: "left",
            sortable: true,
            editable: false,
        },
        {
            field: "",
            headerName: "ActionCol",
            headerClassName: "ActionTab",
            width: 70,
            headerAlign: "center",
            sortable: false,
            renderCell: (params) => {
                const {row} = params;
                return (
                    <Grid className="hover_delete_grid">
                        <IconButton
                            onClick={() => {
                                deleteCallBack(row.id);
                            }}
                            variant="contained"
                            className="theme-btn"
                            color="primary"
                            aria-label="delete"
                            size="large"
                        >
                            <TrashIcon />
                        </IconButton>
                    </Grid>
                );
            },
        },
    ];

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Method of sorting as per last name of users in the table.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {string} lname1
     * @param {string} lname2
     * @return {number|number}
     */
    function customSort(lname1, lname2) {
        console.log(lname1, "----", lname2);
        if (lname1 == lname2) return 0;
        return lname1 > lname2 ? 1 : -1;
    }


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function is used to perform  delete action for specific user(direct from the list). This will
     * take ID(from parameter) of selected user from the user list and pass it to 'deleteConfirm' function.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id Id of the user
     */
    const deleteCallBack = (id) => {
        const data = [id];
        deleteConfirm(data);
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function is used to navigate to specific user's profile page(which contain user details).This
     * function takes ID(from parameter) and pass it to route props(receiving from component's prop) for navigate to
     * specific user's profile page.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id Id of the User
     */
    const profileCallBack = (id) => {
        props.history.push(`/${gKey}/user-profile?id=${id}`)
    }

    const urlPayloadData = {
        key: props.searched_key,
        groupKey: gKey,
        filter: "group_user",
        pagination: 1,
        row_per_page: pageSizeValue,
        page: pageNumber,
    }

    useEffect(() => {
        fetchGroupUsers()
    }, [props.searched_key]);

    const fetchGroupUsers = () => {
        try {
            props
                .getUserData(urlPayloadData)
                .then((res) => {
                    setTimeout(() => {
                        setLoading(false);
                    }, 400)
                    setRows(res.data);
                    setRowsMetaData(res.data.meta);
                })
                .catch((err) => {
                    setLoading(false);
                    alert.show(Helper.handleError(err), {type: "error"});
                });
            } catch (err) {
            setLoading(false);
            alert.show(Helper.handleError(err), {type: "error"});
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will get logged in user data from its props first. If data contains current
     * group id in props then this function will call getUser Function and pass current group id to it to get user's
     * list.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const fetchUserByPage = (page) => {
        getUsers(page, pageSizeValue, sortModel);
        setPageNumber(page);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method handles page size (page per row ) on list
     * -------------------------------------------------------------------------------------------------------------------
     * @method
     * @param {Number} pageSize Number of the data in one page
     */
    const handlePageSizeChange = (pageSize) => {
        setPageSizeValue(pageSize);
        getUsers(0, pageSize, sortModel);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Method to call get users of an specific group key.
     * -----------------------------------------------------------------------------------------------------------------
     * @method
     * @param {Number} page Number of the current page
     * @param {Number} item_per_page Number of data on a single page
     */
    const fetchUser = (page = 0, item_per_page = 10) => {
        //group key to get specific group's user
        getUsers(page, item_per_page, sortModel);
        console.log("gKey", gKey);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to get users data and once the call successfully completed
     * then it will save the response(user list data) in a state(setRows) other wise it will throw an error.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} page Current page value
     * @param {Number} itemPerPage Number of rows on a single page
     * @param {Object} sortModel Sorting on column
     * @param {String} sortModel.sort Type of sorting i.e- ascending or descending
     * @param {String} sortModel.field Name of the field to sort
     */
    const getUsers = (page = 0, itemPerPage = 10, sortModel) => {
        const data = {
            groupKey: gKey,
            mode: "extended",

            row_per_page: itemPerPage,
            isPaginated: 1,
            page: page + 1,
            order: sortModel[0].sort,
            order_by: sortModel[0].field,
        };

        try {
            props
                .getUserData(data)
                .then((res) => {
                    // if set password not done redirect to set- password page
                    if (res.data.status == 403) {
                        Helper.replaceSetPassword(res.data.data);
                    }
                    setRows(res.data.data);
                    setRowsMetaData(res.data.meta);
                })
                .catch((err) => {
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };
    /**
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description Method to call deleteUsers and show modal confirmation box before deleting.
     * -----------------------------------------------------------------------------------------------------------------
     * @method
     * @param {number} data Selected user's id
     */
    const deleteConfirm = (data) => {
        let users = [];
        data.map((item) => {
            users.push({id: item});
        });
        confirmAlert({
            message: "Are you sure you want to remove this user?",
            confirmLabel: "Confirm",
            cancelLabel: "Cancel",
            buttons: [
                {
                    label: "Yes",
                    onClick: () => {
                        deleteUsers(users);
                    },
                },
                {
                    label: "No",
                    onClick: () => {
                        return null;
                    },
                },
            ],
        });
    };

    /**
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description Method to delete users from a group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object[]} data Object of the user's id which need to be deleted
     */
    const deleteUsers = (data) => {
        const dataVal = {user: data, _method: "DELETE"};
        try {
            props
                .deleteUsers(dataVal)
                .then((response) => {
                    if (response.data.status === true) {
                        alert.show("Successfully Deleted", {type: "success"});
                        fetchGroupUsers();
                    }
                })
                .catch((err) => {
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Method of filter users which can be trigger when new user will be added or remove existing users.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object[]} data Array object of users
     */
    const filterUsers = (data) => {
        const rows = [...tableRows];
        const newResult = rows.data.filter((item) => {
            const wasSelected = data.filter((val) => val.id == item.id);

            if (_.isEmpty(wasSelected)) {
                return item;
            }
        });

        setRows(newResult);
    };

    //get group id from local storage
    const curr_grp = localStorage.getItem("user_data");
    const idData = JSON.parse(curr_grp);
    const groupId = idData.current_group.id;

    return (
        <LoadingSkeleton loading={loading} skeleton={<TableSkeleton/>}>
            <Paper elevation={0} className="manageUserListDiv ManageAllListDiv">
                <Grid container specing={0} className="TableSearchBar">
                    <Grid>
                        <Select
                            value={dropdown}
                            onChange={changeDropdown}
                            displayEmpty
                            variant="filled"
                            className="manage_user_drop_down"
                            size="small"
                            inputProps={{"aria-label": "Without label"}}
                        >
                            <MenuItem value={0}>Users list</MenuItem>
                            <MenuItem value={1}>Add Users (Manually)</MenuItem>
                            <MenuItem value={2}>Add Users (Import)</MenuItem>
                        </Select>
                    </Grid>
                    {dropdown == 0 && (
                        <Grid>
                            <IconButton
                                onClick={() => {
                                    deleteConfirm(selectedRows);
                                }}
                                disabled={_.isEmpty(selectedRows)}
                                variant="contained"
                                className="theme-btn"
                                color="primary"
                                aria-label="delete"
                                size="large"
                            >
                                {!_.isEmpty(selectedRows) ? (
                                    <Badge
                                        color="secondary"
                                        badgeContent={
                                            !_.isEmpty(selectedRows) ? selectedRows.length : ""
                                        }
                                    >
                                        <TrashIcon />
                                    </Badge>
                                ) : (
                                    ""
                                )}

                                {/* <Badge color="secondary" badgeContent={!_.isEmpty(selectedRows) ? selectedRows.length : ''}>
                  <TrashIcon />
                </Badge> */}
                            </IconButton>
                            &nbsp;&nbsp;
                            <SearchBar
                                type={"regular"}
                                callBack={setRows}
                                fetchCallBack={fetchUser}
                            />
                        </Grid>
                    )}
                </Grid>
                {dropdown == 0 ? (
                    <div>
                        <ServerSideDataTable
                            url={userAction.userSearch}
                            columns={columns}
                            rows={tableRows}
                            getRowId={props.getRowId}
                            className="analyticsCustomDataGrid"
                            disableColumnMenu={true}
                            disableCheckBox={true}
                            // pinnedData={rowsData.pinnedRow}
                            disableRowPinned={true}
                            // renderResponse={renderRows}
                            // renderBottom={renderBottomRow}
                            urlPayloadData={urlPayloadData}
                            onPayloadDataChange={callAPI}
                        />
                    </div>
                ) : // <DataTable
                    //   columns={columns}
                    //   rows={tableRows}
                    //   selectHandler={setRow}
                    //   customSort="lname"
                    //   sortModel={sortModel}
                    //   onSortModelChange={(model) => setSortModel(model)}
                    // />
                    dropdown == 1 ? (
                        <Paper className="add_user_manually" elevation={0}>
                            <AddUser
                                {...props}
                                setDropdown={setDropdown}
                                org={"user"}
                                className="subAddOrgDiv"
                                addSubmit={() => {
                                    fetchUser();
                                }}
                            // F
                            />
                        </Paper>
                    ) : (
                        <Paper className="add_user_import" elevation={0}>
                            <ImportUser />
                        </Paper>
                    )}
            </Paper>
        </LoadingSkeleton>
    );
};

const mapDispatchToProps = (dispatch) => {
    return {
        // getUserData: (groupKey) => dispatch(groupAction.getGroupUsers(groupKey)),
        getUserData: (data) => dispatch(userAction.userSearch(data)),
        deleteUsers: (data) => dispatch(userAction.deleteMultiUser(data)),
    };
};

const mapStateToProps = (state) => {
    return {
        user_badge: state.Auth.userSelfData,
        searched_key: state.Group.searched_key
    };
};
export default connect(mapStateToProps, mapDispatchToProps)(ManageUser);
