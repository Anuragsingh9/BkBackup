import React, {useState, useEffect} from "react";
import EventNoteIcon from "@material-ui/icons/EventNote";
import {
    IconButton,
    Button,
    Grid,
    Badge,
    Paper,
    Select,
    MenuItem,
} from "@material-ui/core";
import Tooltip from "@material-ui/core/Tooltip";
import AddCircleOutlineIcon from "@material-ui/icons/AddCircleOutline";
import _ from "lodash";
import Container from "@material-ui/core/Container";
import {connect} from "react-redux";
import moment from "moment-timezone";
import Helper from "../../../Helper";
import {useAlert} from "react-alert";
import {useParams} from "react-router-dom";
import "./GroupList.css";

import GroupListTable from "../common/List/GroupListTable";

import NavTabs from "../../Common/NavTabs/NavTabs";

import groupAction from "../../../redux/action/apiAction/group";
import SplitButton from "../common/SplitButton/SplitButton";
import FavStar from "../common/FavStar/FavStar";
import GroupSearchBar from "../../Common/GroupSearchBar/GroupSearchBar";
import CloseIcon from "@mui/icons-material/Close";
import FilterIcon from "../../Svg/FilterIcon";
import ArrowDropDownIcon from "@mui/icons-material/ArrowDropDown";
import {useTranslation} from 'react-i18next';
import BreadcrumbsInput from "../../v4/Common/Breadcrumbs/BreadcrumbsInput";
import Constants from "../../../Constants";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for rendering/displaying Group list.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props  This component is received route related props eg - history,location,match
 * @param {Function} props.getGroups Function to get group data.
 * @param {Function} props.updateGroup Function to update specific group data.
 * @returns {JSX.Element}
 */
const GroupList = (props) => {
    // for localization
    const {t} = useTranslation("groupList");
    const [groupData, setGroupData] = React.useState([]);
    //state to set initial data in list on no value in search bar
    const [fixData, setFixData] = React.useState([]);
    const [isFav, setIsFav] = React.useState("");
    const alert = useAlert();
    const [canCreateGroup, setCanCreateGroup] = useState(false);
    const [groupSelect, setGroupSelect] = React.useState(0);
    const [loading, setLoading] = useState(true)
    const [sortModel, setSortModel] = useState([
        {
            "field": "name",
            "sort": "asc"
        }
    ])
    const [open, setOpen] = React.useState(false);

    const [rowsMetaData, setRowsMetaData] = useState();
    const {gKey} = useParams();

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used to close select dropdown of "group type".
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleClose = () => {
        setOpen(false);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used to open select dropdown of "group type".
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleOpen = () => {
        setOpen(true);
    };

    useEffect(() => {
        getGroups();
    }, []);

    useEffect(() => {
        getGroups();
    }, [sortModel]);


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to Limit json string
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} obj Json object
     * @param {Number} count Count of string
     * @returns {String}
     */
    const limitJSON_Str = (obj, count) => {
        let strObj = JSON.stringify(obj)
        console.log('obj', typeof strObj, strObj)
        return Helper.limitText(strObj, count)
    }

    // column data for group list table
    const columns = [
        {
            field: "is_fav",
            headerName: " ",
            width: 70,
            sortable: false,
            headerAlign: "center",
            className: "Fav_star_col",
            align: "center",
            renderCell: (params) => {
                const {row} = params;
                // console.log("rowww", row.group_key)
                return (
                    <FavStar
                        fav={row.is_fav}
                        setFav={(data) => setFav(data, row)}
                    />
                );
            },
        },
        {
            field: "group_name",
            headerName: "Group Name",
            width: 180,
            flex: 1.5,
            headerAlign: "center",
            sortable: true,
            editable: false,
            renderCell: (params) => {
                const {row} = params;
                // console.log("g name", row.group_name);
                return (
                    <div className="overflow_cell">
                        <p>{row.group_name && `${Helper.jsUcfirst(row.group_name)}`}</p>
                    </div>
                );
            },
        },
        {
            field: "group_type",
            headerName: "Group Type",
            width: 150,
            headerAlign: "center",
            sortable: false,
            editable: false,
            renderCell: (params) => {
                const {row} = params;
                // console.log("g name", row.group_name);
                return (
                    <div className="overflow_cell">
                        <p>
                            {row.group_name && `[${Helper.groupTypeCapital(row.group_type)}]`}
                        </p>
                    </div>
                );
            },
        },
        {
            field: "pilot",
            headerName: "Pilot",
            width: 160,
            headerAlign: "center",
            sortable: false,
            editable: false,
            renderCell: (params) => {
                const {row} = params;
                // console.log("pilots", row.pilot.company_name);
                return (
                    row.pilot && (
                        <div className="custom_group_cell ">
                            <p>{`${row.pilot.fname} ${row.pilot.lname} `}</p>
                            {
                                !_.isEmpty(row.pilot.company_name)
                                    ? limitJSON_Str(row.pilot.company_name, 12)
                                    : ""
                            }
                            {
                                !_.isEmpty(row.pilot.company_position)
                                    ? limitJSON_Str(row.pilot.company_position, 12)
                                    : ""
                            }
                            {/* <p>{ `${row.pilot.company_name}}`, `${row.pilot.company_position}`}</p>           */}
                        </div>
                    )
                );
            },
        },
        {
            field: "users",
            headerName: "Users Count",
            width: 120,
            headerAlign: "center",
            sortable: false,
            editable: false,
        },
        {
            field: "events_count",
            headerName: "Event Count",
            width: 120,
            sortable: false,
            headerAlign: "center",
            editable: false,
            renderCell: (params) => {
                const {row} = params;
                const totalEvent = row.all_events_count ? row.all_events_count : 0;
                const futureEvent = row.future_events_count ? row.future_events_count : 0;
                const draftEvent = row.draft_events_count ? row.draft_events_count : 0;
                const published = row.published_events_count ? row.published_events_count : 0;
                return (
                    <div className="custom_group_cell">
                        <div className="event_count">
                            <b>
                                {totalEvent}
                            </b>
                            <div className="all_draft_count">
                                <b>
                                    <p>Past- {published}</p>
                                    <p>Draft- {draftEvent}</p>
                                    <p>Future- {futureEvent}</p>
                                </b>
                            </div>
                        </div>
                    </div>
                );
            },
        },
        {
            field: "next_event",
            headerName: "Next Event",
            width: 270,
            flex: 2,
            headerAlign: "center",
            sortable: true,
            editable: false,
            renderCell: (params) => {
                const {row} = params;

                if (row.next_event) {
                    const nextEvent = row.next_event;
                    const name = nextEvent.event_name ? nextEvent.event_name : "";
                    if (nextEvent.date == null || undefined) {
                        return ("No Event Scheduled");
                    } else {
                        const date = nextEvent.date
                            ? ` ${moment(nextEvent.date).format("ll")} ${moment(
                                nextEvent.date
                            ).format("dddd")} `
                            : "";
                        const stime = nextEvent.start_time
                            ? ` ${moment(`${nextEvent.date} ${nextEvent.start_time}`).format(
                                "hh:mm a"
                            )}`
                            : ` ${moment(`${nextEvent.date} ${nextEvent.time}`).format(
                                "hh:mm a"
                            )}`;
                        const etime = nextEvent.end_time
                            ? ` ${moment(`${nextEvent.date} ${nextEvent.end_time}`).format(
                                "hh:mm a"
                            )}`
                            : ` ${moment(`${nextEvent.date} ${nextEvent.time}`).format(
                                "hh:mm a"
                            )}`;

                        // return `${name} ${date} @ ${stime} to ${etime} `;
                        console.log('namewww', stime)
                        return (
                            <Tooltip arrow title={`
                            ${name},
                            ${date} @ ${stime} to ${etime}
                            `}>
                                <div className="custom_group_cell time">
                                    <p style={{"z-index": "1"}}>
                                        {name ? Helper.limitText(name, 15) : "hI"},<span>{date}</span>
                                    </p>
                                    <p>
                                        {" "}
                                        @ {stime} to {etime}
                                    </p>
                                </div>
                            </Tooltip>
                        );
                    }
                }

                if (row?.next_event === null) {
                    return <div className="custom_group_cell">No Event Scheduled</div>
                }
            },
        },
        {
            field: "",
            headerName: "",
            width: 160,
            headerAlign: "center",
            sortable: false,
            editable: false,
            renderCell: (params) => {
                const {row} = params;
                const gkey = row.group_key;
                // console.log("row data", gkey);

                return (
                    <>
                        <SplitButton gkey={gkey} groupData={row} reloadGroup={getGroups} defaultGroup={rowsMetaData} />
                    </>
                );
            },
        },
    ];

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used to make a group to favourite group and update all group list data.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {boolean} data boolean value
     * @param {GroupObj} groupData Group data
     */
    const setFav = (data, groupData) => {
        setIsFav(data);

        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("is_favourite", `${data === true ? 1 : 0}`);
        formData.append("group_key", `${groupData.group_key}`);
        formData.append("group_name", `${groupData.group_name}`);
        formData.append("pilot[]", `${groupData.pilot.id}`);
        try {
            props
                .updateGroup(formData)
                .then((res) => {
                    console.log("fav api", res.data);
                    alert.show(`
          ${data === true ? t("addFav") : t("removeFav")}
          `);
                })
                .catch((err) => {
                    // console.log("fav err", err);
                    if (err && _.has(err.response.data, ["errors"])) {
                        var errors = err.response.data.errors;
                        for (let key in errors) {
                            alert.show(errors[key], {type: "error"});
                        }
                    } else if (err && _.has(err.response.data, ["msg"])) {
                        var er = err.response.data.msg;
                        for (let key in er) {
                            alert.show(er[key], {type: "error"});
                        }
                        // alert.show(err.response.data.msg,{type:'error'});
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
     * @description This method will fetch filtered group list as per selected option(group type select component).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e javaScript event object
     */
    const changeGroupSelect = (e) => {
        setGroupSelect(e.target.value);
        if (e.target.value === 0) {
            getGroups();
        } else {
            getGroups(e.target.value);
        }
    };
    //fetch all data in group list if no option is selected in group type select component.
    useEffect(() => {
        if (groupSelect == 0) {
            getGroups([]);
        }
    }, [groupSelect]);

    const fetchUserByPage = (page) => {
        getGroups(groupSelect == 0 ? [] : groupSelect, page);
    };

    const handlePageSizeChange = (pageSize) => {
        getGroups(groupSelect == 0 ? [] : groupSelect, 0, pageSize);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method will fetch filtered group list as per selected group type.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Array} type Array of numbers as per group type.
     */
    const getGroups = (type, page = 0, itemPerPage = 10) => {
        const data = {
            mode: "extended",
            row_per_page: itemPerPage,
            isPaginated: 1,
            page: page + 1,
            order_by: sortModel[0].field,
            order: sortModel[0].sort,
            type: type, // [2,3],
        };

        // const data =!_.isEmpty(type) ? type : []


        try {
            props
                .getGroups(data)
                .then((res) => {
                    // console.log("get groups", res.data.data);
                    setGroupData(res.data.data);
                    setFixData(res.data.data);
                    if (res.data.meta) {
                        const groupSettings = res.data.meta.group_settings;
                        console.log("dddddd", groupSettings.can_create_group)
                        _.has(groupSettings, ["can_create_group"])
                            ? setCanCreateGroup(groupSettings.can_create_group)
                            : setCanCreateGroup(false);
                    }
                    setRowsMetaData(res.data.meta);
                    setLoading(false)
                })
                .catch((err) => {
                    console.log(err);
                    setLoading(false)
                });
        } catch (err) {
            console.log(err);
            setLoading(false)
        }
    };

    console.log(canCreateGroup, "canCreateGroup")
    const tabData = [
        {
            label: "GROUP LIST",
            href: "/contacts",
            child: <GroupListTable
                columns={columns}
                rows={groupData}
                setSortModel={(model) => {
                    if (
                        !_.isEmpty(model) &&
                        JSON.stringify(model[0]) !== JSON.stringify(sortModel[0])
                    ) {
                        setSortModel(model);
                        console.log("model", model);
                    }
                }}
                totalItems={rowsMetaData?.total}
                fetchList={fetchUserByPage}
                onPageSizeChange={handlePageSizeChange}
                onPageChange={fetchUserByPage}
                loading={loading} />,
        },
    ];

    return (
        <>
            <BreadcrumbsInput
                links={[
                    Constants.breadcrumbsOptions.GROUPS_LIST,
                ]}
            />
            <div className="GroupListWrap">
                {/* <EventNoteIcon/> */}
                <span className="create_group_btn">
                    <Select
                        value={groupSelect}
                        onChange={changeGroupSelect}
                        displayEmpty
                        variant="outlined"
                        className="manage_user_drop_down"
                        size="small"
                        inputProps={{"aria-label": "Without label"}}
                        open={open}
                        onClose={handleClose}
                        onOpen={handleOpen}
                        IconComponent={() =>
                            groupSelect !== 0 ? (
                                <CloseIcon
                                    style={{cursor: "pointer"}}
                                    onClick={() => {
                                        setGroupSelect(0);
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
                            {" "}
                            <div className="dropdown-alignment">
                                <FilterIcon className="filter-svg " /> <p>Group Type </p>
                            </div>
                        </MenuItem>
                        <MenuItem value={"head_quarters_group"}>Headquaters </MenuItem>
                        <MenuItem value={"local_group"}>Local </MenuItem>
                        <MenuItem value={"functional_group"}>Function </MenuItem>
                        <MenuItem value={"topic_group"}>Topic </MenuItem>
                        <MenuItem value={"spontaneous_group"}>Spontaneous </MenuItem>
                        <MenuItem value={"water_fountain_group"}>Water-Fountain </MenuItem>
                    </Select>

                    <GroupSearchBar
                        className="search-bar"
                        callBack={setGroupData}
                        onRemoveText={() => {
                            setGroupData(fixData);
                        }}
                    />
                    <Tooltip arrow title={canCreateGroup ? "Create Group" : "Max. Group limit reached. Contact Admin for Support"}
                        placement="top-start">
                        <div>
                            <Button
                                variant="outlined"
                                color="primary"
                                size="large"
                                disabled={!canCreateGroup}
                                onClick={() => {
                                    props.history.push(`/${gKey}/create-group`);
                                }}
                            >
                                Create &nbsp;&nbsp;
                                <AddCircleOutlineIcon />
                            </Button>
                        </div>
                    </Tooltip>
                </span>
                <NavTabs tabId="user-tabs" tabData={tabData} />
            </div>
        </>
    );
};

const mapStateToProps = () => {
    return {};
};

const mapDispatchToProps = (dispatch) => {
    return {
        getGroups: (data) => dispatch(groupAction.getGroups(data)),
        updateGroup: (data) => dispatch(groupAction.updateGroup(data)),
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(GroupList);
