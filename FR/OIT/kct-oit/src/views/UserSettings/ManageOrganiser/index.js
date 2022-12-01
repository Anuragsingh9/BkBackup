import React, {useState, useEffect} from "react";
import DataTable from "../../Common/DataTable";
import {
    Button,
    Link,
    Grid,
    IconButton,
    Badge,
    Paper,
    Select,
    MenuItem,
} from "@material-ui/core";
import groupAction from "../../../redux/action/apiAction/group";
import {connect} from "react-redux";
import {useAlert} from "react-alert";
import {confirmAlert} from "react-confirm-alert";
import {useTranslation} from "react-i18next";
import Tooltip from "@material-ui/core/Tooltip";
import userAction from "../../../redux/action/apiAction/user";
import _ from "lodash";
import Helper from "../../../Helper";
import AddOrg from "../AddOrg/AddOrg";
import IconDropDown from "../../Common/IconDropDown/IconDropDown";
import "react-confirm-alert/src/react-confirm-alert.css";
import "./ManageOrganiser.css";
import SearchBar from "../../Common/SearchBar/SearchBar";

import Hstatus from "../../Group/common/HStatus/Hstatus";
import TrashIcon from "../../Svg/TrashIcon";
import PilotRoleIcon from "../../Svg/PilotRoleIcon";
import OwnerRoleIcon from "../../Svg/OwnerRoleIcon";
import {useParams} from "react-router-dom";
import CloseIcon from "@mui/icons-material/Close";
import FilterIcon from "../../Svg/FilterIcon";
import PilotIcon from "../../Svg/PilotIcon";
import OwnerIcon from "../../Svg/PilotIcon";
import ArrowDropDownIcon from "@mui/icons-material/ArrowDropDown";
import DataTableServerSide from "../../Common/DataTable/serverSidePaginated";
import CoPilotRoleIcon from "../../Svg/CoPilotRoleIcon";
import LoadingSkeleton from "../../Common/Loading/LoadingSkeleton"
import TableSkeleton from "../../v4/Skeleton/TableSkeleton.js"


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This Component is used in Manage Organiser page through which a group Pilot (system role) can view all
 * the added pilots,co-pilots and owners for a specific group.This page also has the feature of adding more co-pilots
 * and owners into a specific group.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.deleteUsers Function is used to delete users
 * @param {Function} props.getUserData Function is used to get user data
 * @param {User} props.user_badge User badge object
 * @return {JSX.Element}
 */
const ManageOrg = (props) => {
    console.log("propsss", props);
    const {t} = useTranslation("roleIcons");
    const preventDefault = (event) => event.preventDefault();
    const alert = useAlert();
    //state to manage selected row from a table
    const [selectedRows, setRow] = useState([]);
    const [tableRows, setRows] = useState([]);
    // state to manage hide/show of  add co-pilot & owner components
    const [showAdd, setShow] = useState(false);
    const [dropdown, setDropdown] = React.useState(0);
    const [roleSelect, setRoleSelect] = React.useState(0);
    const [sortModel, setSortModel] = React.useState([
        {
            field: "lname", //last name
            sort: "asc",
        },
    ]);
    //state to manage sorting dropdown open/close event.
    const [open, setOpen] = React.useState(false);
    const {gKey} = useParams();
    const [typeValue, setTypeValue] = useState([2, 3, 4]); // 2. pilot 3. owner 4. co-pilot

    const [rowsMetaData, setRowsMetaData] = useState();

    const [rowPerPage, setRowPerPage] = useState(10);
    const [loading, setLoading] = useState(true)

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Method to close sorting dropdown.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleClose = () => {
        setOpen(false);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Method to open sorting dropdown.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleOpen = () => {
        setOpen(true);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Method to set selected value from add user dropdown.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} event Javascript event object
     */
    const changeDropdown = (event) => {
        setDropdown(event.target.value);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Method to set selected value from sorting dropdown.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const changeRoleSelect = (e) => {
        setRoleSelect(e.target.value);
        if (e.target.value === 0) {
            // 2 = pilot  3 = owner  4 = Co-pilot
            setTypeValue([2, 3, 4]);
            getUsers([2, 3, 4]);
        } else {
            getUsers([e.target.value]);
            setTypeValue([e.target.value]);
        }
    };

    useEffect(() => {
        fetchUser();
    }, [sortModel]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will get logged in user(organizer) data from its props first. If data contains current
     * group id in props then this function will call getUser Function and pass current group id to it.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const fetchUser = () => {
        let user_badge = props.user_badge;
        if (_.has(user_badge, ['current_group', 'id'])) {
            getUsers(typeValue);
        }
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description The number value of list page
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} page User list page number
     */
    const fetchUserByPage = (page) => {
        getUsers(typeValue, page, rowPerPage);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method will handle the page size change.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} pageSize Number of results per page
     */
    const handlePageSizeChange = (pageSize) => {
        setRowPerPage(pageSize);
        getUsers(typeValue, 0, pageSize);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description To make the filter back to default state i.e- Filter icon will be removed and all type of users will
     * be fetched.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} role For adjusting filter icon with respect to role
     * @param {Array} userType Group's role 2. Pilot 3. Owner 4. Co-pilot
     */
    const resetFilter = (role, userType) => {
        setRoleSelect(role);
        setTypeValue(userType);
        getUsers(userType);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to get all data of pilot,co-pilot or owner according to the
     * selected type for a specific group and once the call successfully completed then it will save the response in a
     * state(setRows) other wise it will throw an error.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} type Type of user (2.Pilot 3.Owner 4.Co-pilot)
     * @param {Number} page Page number
     * @param {Number} itemPerPage Number of rows per page
     */
    const getUsers = (type, page = 0, itemPerPage = 10, shortModel) => {
        const data = {
            groupKey: gKey,
            type: type,
            mode: "extended",
            filter: "group_organiser",
            row_per_page: itemPerPage,
            isPaginated: 1,
            page: page + 1,
            order: sortModel[0].sort,
            order_by: sortModel[0].field,
        };
        console.log("data", data);
        try {
            props
                .getUserData(data)
                .then((res) => {
                    setTimeout(() => {
                        setLoading(false);
                    }, 400)
                    setRowsMetaData(res.data.meta);
                    console.log("list meta", res.data);
                    setRows(res.data.data);
                })
                .catch((err) => {
                    setLoading(false);
                    console.log(err);
                    // if set password not done redirect to set- password page
                    if (err.responsestatus === 403) {
                        Helper.replaceSetPassword(err.data.data);
                    }
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } catch (err) {
            setLoading(false);
            console.log(err);
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will open a popup component and take confirmation to perform delete
     * user action. That popup component contains 2 button('Yes', 'No'). If user click on 'Yes' then it will
     * pass selected row data(receiving from parameter) to 'deleteUsers' function otherwise it will
     * close the popup if user clicks on 'No' button.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {User} data  Array of objects(selected user's ID)
     */
    const deleteConfirm = (data) => {
        let users = [];
        data.map((item) => {
            users.push({id: item});
        });
        confirmAlert({
            message: "Are you sure? You want to remove?",
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
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to delete selected user(receiving in parameter) from a group and once the
     * call is successfully completed then it will pass response(deleted user id) to 'filterUsers' function other wise it
     * will throw an error.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Array} data  Array of objects
     * @param {Number} data.id Selected User's Id
     */
    const deleteUsers = (data) => {
        const dataVal = {user: data, _method: "DELETE"};
        try {
            props
                .deleteUsers(dataVal)
                .then((res) => {
                    //filter table after deleting
                    filterUsers(data);
                    alert.show("Successfully Deleted", {type: "success"});
                    // getUsers(props.user_badge.current_group.id);
                })
                .catch((err) => {
                    if (_.has(err.response.data, ["errors"])) {
                        let msg = err.response.data.errors;
                        for (let key in msg) {
                            alert.show(msg[key], {type: "error"});
                        }
                    } else {
                        alert.show(Helper.handleError(err), {type: "error"});
                    }
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will take data(deleted user's ID) from parameter and remove them from the group
     * list and set filtered data in state('setRows') for preview in the list.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Array} data Array of objects(selected user's ID)
     * @param {Number} data.id User's Id
     */
    const filterUsers = (data) => {
        const rows = [...tableRows];
        const newResult = rows.filter((item) => {
            const wasSelected = data.filter((val) => val.id == item.id);

            if (_.isEmpty(wasSelected)) {
                return item;
            }
        });

        setRows(newResult);
    };

    const columns = [
        {
            field: "lname",
            headerName: "First Name",
            // className: "text_primary_col",
            width: 220,
            flex: 1,
            headerAlign: "left",
            sortable: true,
            // sortingOrder: [ null, 'desc', 'asc'],
            align: "left",
            renderCell: (params) => {
                return (
                    <p
                        style={{cursor: "pointer"}}
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
        // {
        //   field: 'HStatus',
        //   headerName: 'HStatus',
        //   width: 120,
        //   headerAlign: 'center',
        //   sortable: false,
        //   editable: false,
        //   renderCell: (cellValues) => {
        //     return (
        //         <Hstatus />
        //     );
        // }
        // },
        {
            field: "company",
            headerName: "Company",
            width: 250,
            flex: 1,
            headerAlign: "left",
            sortable: false,
            editable: false,
            renderCell: (params) => {
                // console.log(params);
                const {value} = params;
                return (
                    <span>
                        {Helper.limitText(
                            _.has(value, ["long_name"])
                                ? `${params.value.long_name}${params.value.position !== null ? ', ' + params.value.position : ''}`
                                : "",
                            20
                        )}
                    </span>
                );
            },
        },
        {
            field: "union",
            headerName: "Union",
            width: 250,
            flex: 1,
            sortable: false,
            headerAlign: "left",
            editable: false,
            renderCell: (params) => {
                const {row} = params;
                const {union} = row;

                if (!_.isEmpty(union)) {
                    const firstUnion = union[0];
                    return (
                        <span>
                            {Helper.limitText(
                                _.has(firstUnion, ["long_name"])
                                    ? `${firstUnion.long_name}${firstUnion.position !== null ? ', ' + firstUnion.position : ""
                                    }`
                                    : "",
                                20
                            )}
                        </span>
                    );
                }
                return <span></span>;
            },
        },
        {
            field: "role",
            headerName: "Role",
            width: 110,
            headerAlign: "centlefter",
            sortable: false,
            editable: false,
            renderCell: (params) => {
                console.log('paramsss', params)
                const {row} = params;
                const {role} = row;
                return (
                    <>
                        {_.has(row, ["role"]) ? (
                            role === 2 ? (
                                <>
                                    <Tooltip arrow title={t("Pilot")}>
                                        <div className="role_icon_cell">
                                            <PilotRoleIcon />
                                        </div>
                                    </Tooltip>
                                </>
                            ) : role === 3 ? (
                                <Tooltip arrow title={t("Owner")}>
                                    <div className="role_icon_cell">
                                        <OwnerRoleIcon />
                                    </div>
                                </Tooltip>
                            ) : role === 4 ? (
                                <Tooltip arrow title={t("Co-pilot")}>
                                    <div className="role_icon_cell">
                                        <CoPilotRoleIcon />
                                    </div>
                                </Tooltip>
                            ) : (
                                ""
                            )
                        ) : (
                            ""
                        )}
                    </>
                );
            },
        },
        {
            field: "email",
            headerName: "Email",
            width: 250,
            flex: 1,
            headerAlign: "left",
            sortable: true,
            editable: false,
        },
        {
            field: "",
            headerName: "",
            headerClassName: "ActionTab",
            width: 50,
            headerAlign: "center",
            sortable: false,
            disableSelectionOnClick: true,
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
                    // <IconDropDown
                    //   data={[
                    //     { name: 'Profile', callBack: () => { profileCallBack(row.id) } },
                    //     { name: 'Delete', callBack: () => { deleteCallBack(row.id) } }
                    //   ]}
                    // />
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
     * @param {String} lname1 Column name in the table
     * @param {string} lname2 Column name in the table
     * @return {number|number}
     */
    function customSort(lname1, lname2) {
        if (lname1 == lname2) return 0;
        return lname1 > lname2 ? 1 : -1;
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function is used to perform  delete action  for specific user(direct from the list). This will
     * take ID(from parameter) of selected user from the list and pass it to 'deleteConfirm' function.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id User's Id
     */
    const deleteCallBack = (id) => {
        const data = [id];
        deleteConfirm(data);
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Callback method which takes to profile page of selected user from the table.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id User's Id
     */
    const profileCallBack = (id) => {
        props.history.push(`/${gKey}/user-profile?id=${id}`);
    };
    //get group id from local storage
    const curr_grp = localStorage.getItem("user_data");
    const idData = JSON.parse(curr_grp);
    const groupId = idData.current_group.id;

    return (
        <LoadingSkeleton loading={loading} skeleton={<TableSkeleton/>}>
            <Paper elevation={0} className="manageOrgDiv">
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
                            {/* <MenuItem value={1}>Add Pilot</MenuItem> */}
                            <MenuItem value={2}>Add Co-pilot</MenuItem>
                            <MenuItem value={3}>Add Owner</MenuItem>
                        </Select>
                    </Grid>
                    <Grid>
                        {
                            //show filter component when only list is appearing
                            dropdown == 0 && (
                                <Grid className="mangerole">
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
                                    </IconButton>
                                    &nbsp;&nbsp;
                                    <Select
                                        value={roleSelect}
                                        onChange={changeRoleSelect}
                                        displayEmpty
                                        variant="filled"
                                        className="manage_user_drop_down"
                                        size="small"
                                        inputProps={{"aria-label": "Without label"}}
                                        open={open}
                                        onClose={handleClose}
                                        onOpen={handleOpen}
                                        IconComponent={() =>
                                            roleSelect === 2 || roleSelect === 3 || roleSelect === 4 ? (
                                                <CloseIcon
                                                    style={{cursor: "pointer"}}
                                                    onClick={() => {
                                                        resetFilter(0, [2, 3, 4])
                                                    }}
                                                />
                                            ) : (
                                                <ArrowDropDownIcon
                                                    onClick={handleOpen}
                                                    style={{cursor: "pointer"}}
                                                />
                                            )
                                        }
                                    >
                                        <MenuItem value={0}>
                                            <div className="dropdown-alignment">
                                                <FilterIcon className="filter-svg " />
                                                <p>Role </p>
                                            </div>
                                        </MenuItem>
                                        <MenuItem value={2}>
                                            <div className="dropdown-alignment">
                                                <p>Pilot</p>
                                                <PilotRoleIcon />
                                            </div>
                                        </MenuItem>
                                        <MenuItem value={4}>
                                            <div className="dropdown-alignment">
                                                <p>Co-pilot</p>
                                                <CoPilotRoleIcon />
                                            </div>
                                        </MenuItem>
                                        <MenuItem value={3}>
                                            <div className="dropdown-alignment">
                                                <p>Owner</p>
                                                <OwnerRoleIcon />
                                            </div>
                                        </MenuItem>
                                    </Select>
                                    &nbsp;&nbsp;
                                </Grid>
                            )
                        }

                        {dropdown == 0 && (
                            <Grid className="mangerole">

                                <SearchBar
                                    type={"organiser"}
                                    callBack={setRows}
                                    fetchCallBack={fetchUser}
                                    filter={"group_organiser"}
                                    groupId={groupId ? groupId : ""}
                                />
                            </Grid>
                        )}
                    </Grid>
                </Grid>

                {/* <EventTable
        columns={props.columns}
        // rows={draftEventData}
        getRowId={(row) => row.event_uuid}
         rows={draftEventData}
        totalItems={draftEventData.meta.total}
        fetchList={fetchEventByPage}
        onPageSizeChange={handlePageSizeChange}
        onPageChange={fetchEventByPage}

      /> */}

                {dropdown == 0 ? (
                    <DataTableServerSide
                        columns={columns}
                        rows={tableRows}
                        className="eventListCustomDataGrid"
                        totalItems={rowsMetaData?.total}
                        fetchList={fetchUserByPage}
                        onPageSizeChange={handlePageSizeChange}
                        onPageChange={fetchUserByPage}
                        selectHandler={setRow}
                        setSortModel={(model) => {
                            if (
                                !_.isEmpty(model) &&
                                JSON.stringify(model[0]) !== JSON.stringify(sortModel[0])
                            ) {
                                setSortModel(model);
                                // getUsers(typeValue,model)
                                // console.log("model", model);
                            }
                        }}
                        sortModel={sortModel}
                    />
                ) : // {dropdown == 0 ? <DataTable
                    //   columns={columns}
                    //   rows={tableRows}
                    //   selectHandler={(e) => {console.log("chaaaaa",e)}}
                    //   // density="compact"
                    //   customSort="lname"
                    //   sortModel={sortModel}
                    //   onSortModelChange={(model) => setSortModel(model)}
                    // />

                    dropdown == 1 ? (
                        //show add pilot page component
                        <Paper className="add_user_manually" elevation={0}>
                            <AddOrg
                                {...props}
                                setDropdown={setDropdown}
                                add={"pilot"}
                                className="subAddOrgDiv"
                                addSubmit={() => {
                                    setShow(false);
                                    fetchUser();
                                }}
                            />
                        </Paper>
                    ) :
                        dropdown == 2 ?
                            (
                                //show add co-pilot page component
                                <Paper className="add_user_manually" elevation={0}>
                                    <AddOrg
                                        {...props}
                                        setDropdown={setDropdown}
                                        add={"copilot"}
                                        className="subAddOrgDiv"
                                        addSubmit={() => {
                                            setShow(false);
                                            fetchUser();
                                        }}
                                        F
                                    />
                                </Paper>
                            ) : (
                                //show add owner page component
                                <Paper className="add_user_manually" elevation={0}>
                                    <AddOrg
                                        {...props}
                                        setDropdown={setDropdown}
                                        add={"owner"}
                                        className="subAddOrgDiv owner_add"
                                        addSubmit={() => {
                                            setShow(false);
                                            fetchUser();
                                        }}
                                        F
                                    />
                                </Paper>
                            )}

                {/* {!showAdd ?
        <React.Fragment>
          <DataTable
            columns={columns}
            rows={tableRows}
            selectHandler={setRow}
          />
          <Button
            onClick={() => { deleteConfirm(selectedRows) }}
            disabled={_.isEmpty(selectedRows)}
            variant="contained"
            className="theme-btn"
            color="primary"
          >
            Bulk Delete {!_.isEmpty(selectedRows) ? selectedRows.length : ''} Selected
          </Button>

          <Link href="/" onClick={(e) => { e.preventDefault(); setShow(true) }}>
            Add Organizers Manually
          </Link>

        </React.Fragment>
        :
        <React.Fragment>

          <AddOrg {...props} className="subAddOrgDiv" addSubmit={() => { setShow(false); fetchUser() }} />
          <Link href="/" onClick={(e) => { e.preventDefault(); setShow(false) }} className="OrgListLink">
            Go To Organiser List
          </Link>
        </React.Fragment>
      } */}
            </Paper>
        </LoadingSkeleton>
    );
};

const mapDispatchToProps = (dispatch) => {
    return {
        getUserData: (dataObject) =>
            dispatch(groupAction.getGroupOrganiser(dataObject)),
        deleteUsers: (data) => dispatch(userAction.deleteMultiUser(data)),
    };
};

const mapStateToProps = (state) => {
    return {
        user_badge: state.Auth.userSelfData,
    };
};
export default connect(mapStateToProps, mapDispatchToProps)(ManageOrg);
