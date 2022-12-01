import React, {useEffect, useState} from "react";
import {Grid} from "@material-ui/core";
// import { DataGrid } from '@mui/x-data-grid';
import DataTable from "../../../Common/DataTable";
import eventAction from "../../../../redux/action/apiAction/event";
import {useDispatch, useSelector} from "react-redux";
import _ from "lodash";
import {confirmAlert} from "react-confirm-alert";
import Helper from "../../../../Helper";
import IconDropDown from "../../../Common/IconDropDown/IconDropDown";
import Select from "@material-ui/core/Select";
import MenuItem from "@material-ui/core/MenuItem";
import SearchBar from "../../../Common/SearchBar/SearchBar.js";
import userAction from "../../../../redux/action/apiAction/user";
// import AddUser from '../../../UserSettings/AddUser/AddUser';
import ImportUser from "../../../UserSettings/ImportUser/index";
import FilterComp from "./FilterComp.js";
import {useAlert} from "react-alert";
import AddUser from "./AddUserParticipants/AddUser";
import {reactLocalStorage} from "reactjs-localstorage";
import {useTranslation} from "react-i18next";
import "./ManagingRoles.css"

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component allows Pilot to view already added Participants and also add users inside an event &
 * designate them with available roles from the system like, VIP, Team A, Team B,
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from the parent component
 * @param {Boolean} props.isAutoCreated To indicate if the event is an auto created event or not
 * @param {Boolean} props.accessCode To indicate if access code present or not
 * @returns {JSX.Element}
 * @constructor
 */
export default function ManagingRoles(props) {
    const [showTeam, setShowTeam] = useState(false);
    // this hook used for apply filter on a specific role
    const [currentFilter, setFilter] = useState("");
    const [showParticipants, setShowParticipants] = useState(false);
    const [activeTab, setactiveTab] = useState("");
    const [columns, setColumns] = useState([]);
    const [moreAction, setmoreAction] = useState("Bulk Action");

    const dispatch = useDispatch();

    const [rows, setRows] = useState([]);

    const [selectedRows, setRow] = useState([]);

    const [mode, setMode] = useState(0);

    const newEvent = useSelector((data) => data.Auth.eventDetailsData);

    const eventRoles = useSelector((data) => data.Auth.eventRoleLabels);

    const alert = useAlert();

    const {t} = useTranslation(["labels"]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is used to render options(three dot popup menu) for a particular user from the list
     * in manage participants & roles component.This will return three dot dropdown menu component with limited
     * actions(allowed as per the roles eg- view profile, set as participants/team/expert/vip) depends upon current
     * selected filter from side bar component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} params Params for rendering cell actions
     * @param {EventUser} params.row Data required to performs actions over single row
     * @returns {JSX.Element}
     */
    const renderCellActions = (params) => {
        const {row} = params;
        let result;
        if (
            currentFilter === "space_host" ||
            currentFilter === "moderator" ||
            currentFilter === "speaker"
        ) {
            result = [
                {
                    name: "View Profile",
                    callBack: () => {
                        profileCallHistory(row.id);
                    },
                },
            ];
        } else {
            result = [];
            if (
                currentFilter === "team" ||
                currentFilter === "expert" ||
                currentFilter === "vip"
            ) {
                result.push({
                    name: "Set as Participant",
                    callBack: () => {
                        profileCallBack(row.id, 0);
                    },
                });
            }
            result.push(
                {
                    name: "Set as Team",
                    callBack: () => {
                        profileCallBack(row.id, 1);
                    },
                },
                {
                    name: "Set as Expert",
                    callBack: () => {
                        profileCallBack(row.id, 2);
                    },
                },
                {
                    name: "View Profile",
                    callBack: () => {
                        profileCallHistory(row.id);
                    },
                },
                ...(row.is_vip == 0
                    ? [
                        {
                            name: "Set as VIP",
                            callBack: () => {
                                profileCallBack(row.id, 3);
                            },
                        },
                    ]
                    : []),
                {
                    name: "Remove User",
                    callBack: () => {
                        deleteCallBack(row.id);
                    },
                }
            );
        }
        return <IconDropDown data={result} />;
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description - this is a  object prototype that creates the column data that to be render in tabel
     * -----------------------------------------------------------------------------------------------------------------
     *
     */
    const allColumns = {
        action: {
            field: "",
            headerName: "",
            headerClassName: "ActionTab",
            width: 70,
            headerAlign: "center",
            sortable: false,
            renderCell: renderCellActions,
        },
        spaceNames: {
            field: "spaceNames",
            headerName: "Spaces",
            width: 160,
            renderCell: (params) => {
                const {row} = params;
                const {spaceNames} = row;
                return <span>{Helper.limitText(spaceNames, 16)}</span>;
            },
        },
        fname: {
            field: "fname",
            headerName: "First Name",
            width: 160,
        },
        lname: {
            field: "lname",
            headerName: "Last Name",
            width: 160,
        },
        email: {
            field: "email",
            headerName: "Email",
            width: 210,
        },
        company: {
            field: "company",
            headerName: "Company",
            width: 140,
            renderCell: (params) => {
                const {row} = params;
                const {company} = row;
                return (
                    <span>
            {Helper.limitText(
                _.has(company, ["long_name"]) ? company.long_name : "",
                14
            )}
          </span>
                );
            },
        },
        companyPosition: {
            field: "position",
            headerName: "Position",
            width: 140,
            renderCell: (params) => {
                const {row} = params;
                const {company} = row;
                return (
                    <span>
            {Helper.limitText(
                _.has(company, ["position"]) ? company.position : "",
                14
            )}
          </span>
                );
            },
        },
        union: {
            field: "union",
            headerName: "Union",
            width: 140,
            renderCell: (params) => {
                const {row} = params;
                const {unions} = row;
                if (!_.isEmpty(unions)) {
                    const firstUnion = unions[0];
                    return (
                        <span>
              {Helper.limitText(
                  _.has(firstUnion, ["long_name"]) ? firstUnion.long_name : "",
                  14
              )}
            </span>
                    );
                }
                return <span></span>;
            },
        },
        unionPosition: {
            field: "union position",
            headerName: "Union Position",
            width: 150,

            renderCell: (params) => {
                const {row} = params;
                const {unions} = row;
                if (!_.isEmpty(unions)) {
                    const firstUnion = unions[0];
                    return (
                        <span>
              {Helper.limitText(
                  _.has(firstUnion, ["position"]) ? firstUnion.position : "",
                  14
              )}
            </span>
                    );
                }
                return <span></span>;
            },
        },
    };
    const eventRoleLabels = useSelector(
        (data) => data.Auth.eventRoleLabels.labels
    );
    const eventRolelabelCustomized = useSelector(
        (data) => data.Auth.eventRoleLabels.label_customized
    );

    // this array shows the labels according to language
    const roleList = [
        {
            key: "space_host",
            label:
                eventRolelabelCustomized === 1
                    ? Helper.getLabel("space_host", eventRoleLabels)
                    : t("labels:SpaceHost"),
        },
        {
            key: "team",
            label:
                eventRolelabelCustomized === 1
                    ? Helper.getLabel("business_team", eventRoleLabels)
                    : t("labels:TeamA"),
        },
        {
            key: "expert",
            label:
                eventRolelabelCustomized === 1
                    ? Helper.getLabel("expert", eventRoleLabels)
                    : t("labels:TeamB"),
        },
        {
            key: "vip",
            label:
                eventRolelabelCustomized === 1
                    ? Helper.getLabel("vip", eventRoleLabels)
                    : t("labels:VIP"),
        },
        {
            key: "moderator",
            label:
                eventRolelabelCustomized === 1
                    ? Helper.getLabel("moderator", eventRoleLabels)
                    : t("labels:Moderator"),
        },
        {
            key: "speaker",
            label:
                eventRolelabelCustomized === 1
                    ? Helper.getLabel("speaker", eventRoleLabels)
                    : t("labels:Speaker"),
        },
    ];

    const TabActive = () => {
        if (activeTab === "") {
            setactiveTab("selected_Heading");
        } else {
            setactiveTab("");
        }
    };
    const showPartTeam = () => {
        if (showParticipants === false) {
            setShowParticipants(true);
            setShowTeam(false);
        } else {
            setShowParticipants(false);
        }
    };
    const showEventTeam = (props) => {
        // setShowTeam(true);
        if (showTeam === false) {
            setShowTeam(true);
            setShowParticipants(false);
        } else {
            setShowTeam(false);
        }
    };

    const options = ["event_user", "team", "expert", "vip", "delete"];

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description - this method is used for set url of profile page after selecting the user from row
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id User's id
     */
    const profileCallHistory = (id) => {
        props.history.push(`/user-profile?id=${id}`);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will trigger when we change role of a user(from the list). This will take current user's
     * data(current user's id) with updated roles from parameter and pass it to 'triggerConfirm' function.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} userData  User's id
     * @param {Number} type  Roles id
     */
    const profileCallBack = (userData, type) => {
        const users = [userData];

        triggerConfirm(users, type);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to update user's role.This will take current user's
     * data(current user's id) with updated roles from parameter
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} user   User's id
     * @param {Number} type   Roles id
     */
    const triggerRoleChange = (user, type) => {
        const event_uuid = params.event_uuid;
        const group_key = props.match.params.gKey;

        const data = {
            users: user,
            role: type,
            event_uuid: event_uuid,
            group_key: group_key,
            _method: "PUT",
        };
        try {
            dispatch(userAction.updateRole(data))
                .then((res) => {
                    updateUsers(res.data.data, user);
                    alert.show("Record Updated SuccessFully", {type: "success"});
                    getParticipant(currentFilter);
                })
                .catch((err) => {
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    const updateUsers = (updated, data) => {
        // const updatedData = rows.map((item)=>{
        //   if (data.indexOf(item.id) !== -1 ){
        //     const dataup =  updated.filter((val)=>{if(val.id == item.id){return val}})
        //     return dataup[0];
        //   }else{
        //     return item
        //   }
        // });
        deleteAndUpdate(data);
        // setRows(updatedData);
        // setRow([]);
    };

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method is used for delete an user and after that updates the table
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {User} data Users data
     */
    const deleteAndUpdate = (data) => {
        const updatedData = rows.filter((item) => {
            if (data.indexOf(item.id) == -1) {
                return item;
            }
        });

        setRows(updatedData);
        setRow([]);
    };


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will handle an API call to delete users from the list.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} user User's Id
     */
    const triggerdelete = (user) => {
        const event_uuid = params.event_uuid;

        const data = {
            users: user,
            event_uuid: event_uuid,
            _method: "DELETE",
        };
        try {
            dispatch(userAction.updateRole(data))
                .then((res) => {
                    deleteAndUpdate(user);
                    alert.show("Record Updated SuccessFully", {type: "success"});
                })
                .catch((err) => {
                    // alert.show(Helper.handleError(err),{type:'error'});
                    if (err && _.has(err.response.data, ["errors"])) {
                        var errors = err.response.data.errors;
                        for (let key in errors) {
                            alert.show(errors[key], {type: "error"});
                        }
                    } else if (err && _.has(err.response.data, ["msg"])) {
                        var er = err.response.data;

                        alert.show(er.msg, {type: "error"});

                        // alert.show(err.response.data.msg,{type:'error'});
                    } else {
                        alert.show(Helper.handleError(err), {type: "error"});
                    }
                    // alert.show(Helper.handleError(err), { type: 'error' });
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method is used to confirm before deleting a user data and fetch the api and updates on sever
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} data User's id
     */
    const triggerDeleteConfirm = (userData) => {
        confirmAlert({
            message: `Are you sure you want to perform this action?`,
            buttons: [
                {
                    label: "Yes",
                    onClick: () => {
                        triggerdelete(userData);
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
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method is used to confirm before role change
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} userData User's Id
     * @param {Number} type Role type value in number
     */
    const triggerConfirm = (userData, type) => {
        confirmAlert({
            message: `Are you sure you want to perform this action?`,
            buttons: [
                {
                    label: "Yes",
                    onClick: () => {
                        triggerRoleChange(userData, type);
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
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will trigger when we click on remove user option from three dot dropdown menu(from the
     * list). This will take current user's data(current user's id) from parameter and pass it to 'triggerDeleteConfirm'
     * function.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} userData User's Id
     */const deleteCallBack = (userData) => {
        const users = [userData];

        triggerDeleteConfirm(users);
    };
    const language = useSelector((state) => state.Auth.language);

    useEffect(() => {
        getParticipant();
        setCurrentColumn();
    }, []);

    useEffect(() => {
        setCurrentColumn();
    }, [currentFilter, language]);


    /**
     *  ---------------------------------------------------------------------------------------------------------------------
     * @description - this method is used for set Current Column for space host and other roles
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const setCurrentColumn = () => {
        let newColumns = [];
        if (!_.isEmpty(currentFilter) && currentFilter === "space_host") {
            // filter empty
            newColumns.push(allColumns.action);
            newColumns.push(allColumns.spaceNames);
            newColumns.push(allColumns.fname);
            newColumns.push(allColumns.lname);
            newColumns.push(allColumns.email);
            newColumns.push(allColumns.company);
            newColumns.push(allColumns.companyPosition);
        } else {
            newColumns.push(allColumns.action);
            newColumns.push(allColumns.fname);
            newColumns.push(allColumns.lname);
            newColumns.push(allColumns.email);
            newColumns.push(allColumns.company);
            newColumns.push(allColumns.companyPosition);
            newColumns.push(allColumns.union);
            newColumns.push(allColumns.unionPosition);
        }
        setColumns(newColumns);
    };

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description - this is for labels
     * ---------------------------------------------------------------------------------------------------------------------
     */
    const bulkOptions = [
        {value: 0, label: "Set as Particpant"},
        {value: 2, label: "Set as Expert"},
        {
            value: 3,
            label: "Set as VIP",
        },
        {value: 1, label: "Set as Team"},
        {value: "delete", label: "Remove "},
    ];

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will handle an API call to get all participants data to show in the list as per current
     * filter(roles) selected by the user.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} filter The type of filter selected by the user
     */
    const getParticipant = (filter) => {
        var params = props.match.params;

        let data = {event_uuid: params.event_uuid};

        if (filter) {
            data.key = filter;
        }

        try {
            dispatch(eventAction.getParticipant(data))
                .then((res) => {
                    let data = res.data.data;
                    if (filter === "space_host") {
                        data = data.map((participant) => {
                            if (_.has(participant, ["spaces"])) {
                                let spaceNames = [];
                                participant.spaces.forEach((space) => {
                                    spaceNames.push(space.space_name);
                                });
                                participant.spaceNames = spaceNames.toString();
                            }
                            return participant;
                        });
                    }
                    setRows(data);

                    if (filter === "space_host") {
                        // setColumns([])
                    } else if (filter === "moderator" || filter === "speaker") {
                        // defaultColumns[0] = viewProfileAction;
                        // setColumns(defaultColumns)
                    } else if (filter === "event_user" || filter === undefined) {
                        // defaultColumns[0] = participantPageAction;
                        // setColumns(defaultColumns)
                    } else {
                        // setColumns(defaultColumns)
                    }
                })
                .catch((err) => {
                });
        } catch (err) {
        }
    };
    const params = props.match.params;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This callback function will be trigger when er add a user with add user manually/import user feature
     * Just after adding a user this function will return back to the list component and fetch all users data..
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const callBack = () => {
        setMode(0);
        getParticipant();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user select any filter from side bar component and it will pass the
     * filter to 'getParticipant' function to fetch user's list for this filter.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} filter Current filter selected by the user
     */
    const filterUsers = (filter) => {
        getParticipant(filter);
        setFilter(filter ? filter : '');
    }

    return (
        <div className="mangeRolesDropdownDiv">
            <div className="SelectRolesRow">
                <div className="group_eventsName">
                    <p className="customPara">{_.has(newEvent, ["title"]) ? newEvent.title : ""}</p>
                </div>

                <Select
                    native
                    value={mode}
                    onChange={(e) => {
                        setMode(e.target.value);
                    }}
                    name="number"
                    size="small"
                    className="selectCustom"
                    variant="outlined"
                    inputProps={{"aria-label": "age"}}
                >
                    <option value="">Participants List</option>
                    <option value={1}>Manually Add User</option>
                    <option value={2}>Import New User</option>
                    <option value={3} disabled>
                        Group Users
                    </option>
                    <option value={4} disabled>
                        Previous Event Users
                    </option>
                    <option value={5} disabled>
                        Organization Users
                    </option>
                </Select>
            </div>

            <Grid container lg={12}>
                <Grid item lg={2} className="sidebarMenu">

                    <FilterComp
                        callBack={filterUsers}
                        currentFilter={currentFilter}
                        newEvent={newEvent}
                        roleList={roleList}
                    />
                </Grid>
                <Grid item lg={10}>
                    {mode === "1" && (
                        <div>
                            <AddUser
                                event_uuid={params.event_uuid}
                                callBack={callBack}
                                gKey={params.gKey}
                            />
                        </div>
                    )}
                    {mode === "2" && (
                        <div>
                            <ImportUser event_uuid={params.event_uuid} callBack={callBack} />
                        </div>
                    )}
                    {mode == 0 && (
                        <>
                            <div className="rolesSearchBar">
                <span>
                  <SearchBar
                      type={"regular"}
                      callBack={setRows}
                      fetchCallBack={() => {
                          getParticipant(currentFilter);
                      }}
                      filter={currentFilter}
                  />
                </span>
                            </div>
                            <DataTable
                                rows={rows}
                                columns={columns}
                                pageSize={5}
                                rowsPerPageOptions={[5]}
                                checkboxSelection
                                disableSelectionOnClick
                                selectHandler={setRow}
                            />

                            {currentFilter == "space_host"
                                ? ""
                                : currentFilter == "moderator"
                                    ? ""
                                    : currentFilter == "speaker"
                                        ? ""
                                        : showParticipants == "true"
                                            ? ""
                                            : !_.isEmpty(selectedRows) && (
                                            <Select
                                                id="demo-simple-select"
                                                onChange={(e) => {
                                                    e.preventDefault();
                                                }}
                                            >
                                                {bulkOptions.map((item) => {
                                                    if (item.value == "delete") {
                                                        return (
                                                            <MenuItem
                                                                value={item.value}
                                                                onClick={() => {
                                                                    triggerDeleteConfirm(selectedRows);
                                                                }}
                                                            >
                                                                {item.label}
                                                            </MenuItem>
                                                        );
                                                    }
                                                    return (
                                                        <MenuItem
                                                            value={item.value}
                                                            onClick={() => {
                                                                triggerConfirm(selectedRows, item.value);
                                                            }}
                                                        >
                                                            {item.label}
                                                        </MenuItem>
                                                    );
                                                })}
                                            </Select>
                                        )}
                        </>
                    )}
                </Grid>
            </Grid>
        </div>
    );
}
