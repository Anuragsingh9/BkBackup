import React, {useEffect, useState} from "react";
import {IconButton} from "@material-ui/core";
import EventListIcon from "../Header/EventsDropdown/EventListIcon.js";
import {connect} from "react-redux";
import {useAlert} from "react-alert";
import {confirmAlert} from "react-confirm-alert";
import userAction from "../../redux/action/apiAction/user";
import eventAction from "../../redux/action/apiAction/event";
import AddCircleOutlineIcon from "@material-ui/icons/AddCircleOutline";
import "./EventList.css";
import Helper from "../../Helper";
import "react-confirm-alert/src/react-confirm-alert.css";
import PastEvents from "./PastEvents";
import FutureEvents from "./FutureEvents";
import DraftEvents from "./DraftEvents";
import NavTabs from "../Common/NavTabs/NavTabs";
import "./UserSettings.css";
import userReduxAction from "../../redux/action/reduxAction/user";
import IconDropDown from "../Common/IconDropDown/IconDropDown";
import Tooltip from "@material-ui/core/Tooltip";
import {useTranslation} from "react-i18next"
import Event from '../../Models/Event'
import {useParams} from "react-router-dom";
import LabelDropdown from "../Common/LabelDropDown/LabelDropdown.js";

/**
 * @deprecated
 */
const rows = [
    // { id: 1, title: 'Snow', organizer: 'Jon', type: 'Kct', date: '', start: '', end: '' },
    {
        id: 2,
        lname: "Lannister",
        fname: "Cersei",
        company: "Kct",
        position: "",
        union: "",
        email: "abcd@gmail.com",
    },
    {
        id: 3,
        lname: "Lannister",
        fname: "Jaime",
        company: "Kct",
        position: "",
        union: "",
        email: "abcd@gmail.com",
    },
    {
        id: 4,
        lname: "Stark",
        fname: "Arya",
        company: "Kct",
        position: "",
        union: "",
        email: "abcd@gmail.com",
    },
    {
        id: 5,
        lname: "Targaryen",
        fname: "Daenerys",
        company: "Kct",
        position: "",
        union: "",
        email: "abcd@gmail.com",
    },
    {
        id: 6,
        lname: "Melisandre",
        fname: null,
        company: "Kct",
        position: "",
        union: "",
        email: "abcd@gmail.com",
    },
    {
        id: 7,
        lname: "Clifford",
        fname: "Ferrara",
        company: "Kct",
        position: "",
        union: "",
        email: "abcd@gmail.com",
    },
    {
        id: 8,
        lname: "Frances",
        fname: "Rossini",
        company: "Kct",
        position: "",
        union: "",
        email: "abcd@gmail.com",
    },
    {
        id: 9,
        lname: "Roxie",
        fname: "Harvey",
        company: "Kct",
        position: "",
        union: "",
        email: "abcd@gmail.com",
    },
];

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a parent component which stores the data of all the events associated with one default group
 * of the account. It also filters the events into Future, past and draft event list based on certain factors like
 * Published status and Start & End timings.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from the parent for event list
 * @param {Event} props.getEvents [Dispatcher] This method will fetch all event of future and past
 * @param {Event} props.getDraftEvents [Dispatcher]This method will fetch all draft event
 * @param {User} props.getUserData [Dispatcher] This method will fetch current user data
 * @param {Function} props.setUser [Dispatcher] This method will set the current user data
 * @param {Function} props.deleteCallBack This method will invoke a confirm delete pop up on deleting an event
 * @return {JSX.Element}
 * @constructor
 */
const EventList = (props) => {
    // useTranslatio hook is used for translation for English and French labels
    const {t} = useTranslation(["eventList", "confirm"]);
    const {gKey} = useParams();
    // column define data in events table and add data dynamically i rows according to column values
    const column = [
        {
            field: "title",
            headerName: t("eventTitle"),
            width: 220,
            editable: true,
        },
        {
            field: "organizer",
            headerName: t("org"),
            width: 220,
            editable: true,
            valueGetter: (params) => {
                let result = [];

                if (params.row.organiser !== null) {
                    result.push(params.row.organiser.fname);
                } else {
                    result = [" "];
                }
                return result;
            },
        },
        {
            field: "type",
            headerName: t("type"),
            width: 160,
            valueGetter: (params) => {
                let result = [];

                if (params.row.type) {
                    params.row.type === 1
                        ? result.push("Networking")
                        : result.push("Content");
                } else {
                    result = ["Unknown"];
                }
                return result;
            },
        },
        {
            field: "date",
            headerName: t("date"),
            width: 180,
        },
        {
            field: "start_time",
            headerName: t("st"),
            width: 170,
        },
        {
            field: "end_time",
            headerName: t("et"),
            width: 170,
        },
        {
            field: "",
            headerName: "",
            headerClassName: "ActionTab",
            width: 70,
            headerAlign: "center",
            sortable: false,
            renderCell: (params) => {
                const {row} = params;

                return (
                    <strong>
                        <IconDropDown
                            data={[
                                {
                                    name: t("modify"),
                                    callBack: () => {
                                        profileCallBackModify(row.id);
                                    },
                                },
                                {
                                    name: t("remove"),
                                    callBack: () => {
                                        deleteCallBack(row.id);
                                    },
                                },
                            ]}
                        />
                    </strong>
                );
            },
        },
    ];
    const columnPast = [
        {
            field: "title",
            headerName: t("eventTitle"),
            width: 220,
            editable: true,
        },
        {
            field: "organizer",
            headerName: t("org"),
            width: 220,
            editable: true,
            valueGetter: (params) => {
                let result = [];

                if (params.row.organiser !== null) {
                    result.push(params.row.organiser.fname);
                } else {
                    result = [" "];
                }
                return result;
            },
        },
        {
            field: "type",
            headerName: t("type"),
            width: 160,
            valueGetter: (params) => {
                let result = [];

                if (params.row.type) {
                    params.row.type === 1
                        ? result.push("Networking")
                        : result.push("Content");
                } else {
                    result = ["Unknown"];
                }
                return result;
            },
        },
        {
            field: "date",
            headerName: t("date"),
            width: 180,
        },
        {
            field: "start_time",
            headerName: t("st"),
            width: 170,
        },
        {
            field: "end_time",
            headerName: t("et"),
            width: 170,
        },
        {
            field: "",
            headerName: "",
            headerClassName: "ActionTab",
            width: 70,
            headerAlign: "center",
            sortable: false,
            renderCell: (params) => {
                const {row} = params;

                return (
                    <strong>
                        <IconDropDown
                            data={[
                                {
                                    name: t("modify"),
                                    callBack: () => {
                                        profileCallBackModify(row.id);
                                    },
                                },
                                // {name:'Remove',callBack:()=>{deleteCallBack(row.id)}}
                            ]}
                        />
                    </strong>
                );
            },
        },
    ];

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used for delete method callback that remove a row.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id Id to be removed
     */
    const deleteCallBack = (id) => {
        deleteConfirm(id);
    };

    /**
     * @deprecated
     */
    const profileCallBack = (row, id) => {
        props.history.push(`/${gKey}/access-event/${row}`);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used for open a particular event method callback that
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} row Row unique ID
     */
    const profileCallBackModify = (row, id) => {
        props.history.push(`/${gKey}/edit-event/${row}`);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is shows a model for confirmation before perform delete action.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} data Event ID
     */
    const deleteConfirm = (data) => {
        // let users = [];
        // data.map((item)=>{
        //   users.push({id:item})
        // });
        confirmAlert({
            message: `${t("confirm:sure")}`,
            confirmLabel: t("confirm:confirm"),
            cancelLabel: t("confirm:cancel"),
            buttons: [
                {
                    label: t("confirm:yes"),
                    onClick: () => {
                        deleteEvent(data);
                    },
                },
                {
                    label: t("confirm:no"),
                    onClick: () => {
                        return null;
                    },
                },
            ],
        });
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used for fetch api for delete a particular event
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} data Event uuid
     */
    const deleteEvent = (data) => {
        const dataVal = {event_uuid: data, _method: "DELETE"};
        try {
            props
                .deleteEvent(dataVal)
                .then((res) => {
                    alert.show("Successfully Deleted", {type: "success"});
                    getEvents();
                    let draftEvents = draftEventData.filter(
                        (event) => event.event_uuid !== data
                    );
                    setDraftEventData(draftEvents);
                })
                .catch((err) => {
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    // state to store current group event data
    const [groupValues, setGroupValues] = useState({});
    const [futureEventData, setFutureEventData] = useState([]);
    const [pastEventData, setPastEventData] = useState([]);
    const [draftEventData, setDraftEventData] = useState([]);
    const alert = useAlert();
    const [tableRows, setRows] = useState([]);
    const [showFilter, setShowFilter] = useState(false);
    const [list, setList] = useState([]);
    const [selectedKey, setSelectedKey] = useState('')


    const tabData = [
        {
            label: t("future"),
            href: `/${gKey}/event-list/future-events`,
            child: (
                <FutureEvents
                    columns={column}
                    rows={futureEventData}
                    showFilter={setShowFilter}
                    setList={setList}
                    selectedKey={selectedKey}
                />
            ),
        },
        {
            label: t("draft events"),
            href: `/${gKey}/event-list/draft-events`,
            child: (
                <DraftEvents
                    columns={column}
                    rows={draftEventData}
                    showFilter={setShowFilter}
                    setList={setList}
                    selectedKey={selectedKey}
                />
            ),
        },
        {
            label: t("past"),
            href: "/event-list/past-events",
            child: (
                <PastEvents
                    columns={columnPast}
                    rows={pastEventData}
                    showFilter={setShowFilter}
                    setList={setList}
                    selectedKey={selectedKey}
                />
            ),
        },
    ];

    useEffect(() => {
        let user_badge = {};
        const data = localStorage.getItem("user_data");
        const userId = localStorage.getItem("userId");
        if (data) {
            user_badge = JSON.parse(data);
        }
        if (userId) {
            // getProfileData(userId);
        }
        // getDraft();
    }, []);

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method fetch the initial data of user details and show in fields.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id User ID
     */
    const getProfileData = (id) => {
        try {
            props
                .getUserData(id)
                .then((res) => {
                    const {data} = res.data;
                    // if set password not done redirect to set- password page
                    if (res.data.status == 403) {
                        Helper.replaceSetPassword(res.data.data);
                    }
                    setGroupValues({
                        ...groupValues,
                        groupId: data.current_group.id,
                    });
                    const group_id = data.current_group.id;
                    getEvents(group_id);
                    props.setUser(res.data.data);
                })
                .catch((err) => {
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };
    var futureList = [];
    var pastList = [];
    var draftList = [];

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method will get the event data of a specific group and show it in fields.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id Event ID
     */
    const getEvents = (id) => {
        const groupId =
            props.userGroupId.current_group && props.userGroupId.current_group.id;

        if (groupId) {
            try {
                props.getEvents(groupId)
                    .then((res) => {
                        // id property is nesseccery in data list material ui  component so we added id from event_uuid
                        futureList = res.data.data.future_events;


                        pastList = res.data.data.past_events;
                        // pastList.map((event)=>event["id"]=event.event_uuid)

                        getFutureEvents();
                        gePastEvents();

                    })
                    .catch((err) => {
                        console.error(err);
                    });
            } catch (err) {
                console.error(err);
            }
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method will get the draft event data of a specific group.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const getDraft = () => {
        try {
            props
                .getDraftEvents()
                .then((res) => {
                    draftList = res.data.data.draft_events;
                    draftList.map((event) => (event["id"] = event.event_uuid));
                    setDraftEventData(draftList);
                })
                .catch((err) => {
                    console.error(err);
                });
        } catch (err) {
            console.error(err);
        }
    };


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is for adding event_uuid value in id for mapping value of the futureList array.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const getFutureEvents = () => {
        futureList.map((event) => event["id"] = event.event_uuid)
        setFutureEventData(futureList);
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method divide  the past list of events
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {JSX.Element}
     */
    const gePastEvents = () => {
        pastList.map((event) => (event["id"] = event.event_uuid));

        setPastEventData(pastList);
    };
    //returning/rendering eventList JSX structure
    return (
        <div>
            <div className="EventListPage">
                <div>{showFilter && <LabelDropdown list={list} setSelectedKey={setSelectedKey} />}</div>

                <EventListIcon />

                <IconButton
                    color="primary"
                    variant="contained"
                    className="CreateEveBtn"
                    aria-label="upload picture"
                    component="span"
                    onClick={() => {
                        props.history.push(`/${gKey}/v4/event-create`);
                    }}
                >
                    <Tooltip arrow title="Create New Event" placement="top-start">
                        <AddCircleOutlineIcon />
                    </Tooltip>
                </IconButton>

                <NavTabs tabId="user-tabs" tabData={tabData} />


            </div>
        </div>
    );
};

const mapStateToProps = (state) => {
    return {
        userGroupId: state.Auth.userSelfData,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        getDraftEvents: () => dispatch(userAction.getDraftEvents()),
        getUserData: (id) => dispatch(userAction.getUserData(id)),
        getEvents: (data) => dispatch(userAction.getEvents(data)),
        setUser: (data) => dispatch(userReduxAction.setUserData(data)),
        deleteEvent: (data) => dispatch(eventAction.deleteEvent(data)),
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(EventList);

let meta = {
    groups: [
        {
            group_key: "default",
            group_name: null,
        },
        {
            group_key: "newgroup",
            group_name: null,
        },
        {
            group_key: "fifth",
            group_name: null,
        },
        {
            group_key: "fi0007",
            group_name: null,
        },
        {
            group_key: "fi0008",
            group_name: null,
        },
        {
            group_key: "FI0027",
            group_name: null,
        },
    ],
};
