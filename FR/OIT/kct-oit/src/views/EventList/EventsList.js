import React, {useEffect, useState} from "react";
import EventListIcon from "../Header/EventsDropdown/EventListIcon.js";
import {connect} from "react-redux";
import {useAlert} from "react-alert";
import {confirmAlert} from "react-confirm-alert";
import userAction from "../../redux/action/apiAction/user";
import eventAction from "../../redux/action/apiAction/event";
import "./EventList.css";
import Helper from "../../Helper";
import "react-confirm-alert/src/react-confirm-alert.css";
import NavTabs from "../Common/NavTabs/NavTabs";
import "./UserSettings.css";
import userReduxAction from "../../redux/action/reduxAction/user";
import IconDropDown from "../Common/IconDropDown/IconDropDown";
import {useTranslation} from "react-i18next";
import {useParams} from "react-router-dom";
import LabelDropdown from "../Common/LabelDropDown/LabelDropdown.js";
import Constants from "../../Constants";
import EventTable from "./EventTable";
import _ from "lodash";
import BreadcrumbsInput from "../v4/Common/Breadcrumbs/BreadcrumbsInput";
import {Grid} from "@mui/material";
import RecurrenceIcon from "../v4/Svg/RecurrenceIcon.js";
import Tooltip from "@material-ui/core/Tooltip";
import {IconButton} from '@mui/material';
import DeleteIcon from '@mui/icons-material/Delete';
import moment from "moment-timezone";
import LoadingSkeleton from "../Common/Loading/LoadingSkeleton.js";
import EventListSkeleton from "../v4/Skeleton/EventListSkeleton.js";

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
 * @param {Object} props Inherited from parent component
 * @param {Function} props.deleteEvent Function is used to delete the event
 * @param {Function} props.getDraftEvents To get draft events
 * @param {Function} props.getEvents To get all events
 * @param {Function} props.getUserData To get user data
 * @param {Function} props.setUser To set user data
 * @param {Object} props.userGroupId User group data
 * @returns {JSX.Element}
 * @constructor
 */
const EventsList = (props) => {
    const {t} = useTranslation(["eventList", "confirm"]);
    const {gKey} = useParams();
    const [eventListData, setEventListData] = useState({
        data: [],
        meta: {},
        links: {},
    });
    const alert = useAlert();
    const [showFilter, setShowFilter] = useState(false);
    const [list, setList] = useState([]);
    const [selectedKey, setSelectedKey] = useState("");
    const [pageSizeValue, setPageSizeValue] = useState("");
    const [loading, setloading] = useState(true)

    const {eventType} = useParams();

    useEffect(() => {

        if (eventType === undefined || possibleRoutes.indexOf(eventType) < 0) {
            props.history.push(`/${gKey}/event-list/${Constants.defaultEventType}`);
        }
        if (eventType) {
            getEvents();
        }
    }, [eventType]);

    const possibleRoutes = Object.values(Constants.eventType);

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used to delete a row by calling this callback method when delete button is clicked
     * in row's dropdown then this method calls deleteConfirm() for confirmation.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @param {String} id Event uuid
     */
    const deleteCallBack = (id) => {
        deleteConfirm(id);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle event modification page redirection. This method takes row data and
     * put that in url of event modification
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @param {Number} id Row Id
     * @param {String} row Event uuid
     */
    const eventModifyHandler = (row, id) => {
        props.history.push(`/${gKey}/v4/event-update/${row}`);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used to show confirmation popup model before perform delete action .It consists of
     * two buttons Confirm and Cancel by clicking on confirm button it calls the delteEvent() method to delete the event
     * and by clicking on cancel button it hides the pop up and do nothing
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @param {String} data Event uuid
     */
    const deleteConfirm = (data) => {

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
     * @description This method is used to delete the events data and fetch api to delete a particular event by sending
     * event uuid on server and removes that event after getting successful response it updates states data for event
     * list other wise shows an error massage notification.
     * -------------------------------------------------------------------------------------------------------------------
     *
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
                })
                .catch((err) => {
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used to get the events data of a specific group and show it in the list for future
     * events, past events and draft events. It has feature of server side rendering for pagination of event list and send
     * data according to parameters like item per page , page no. , and isPaginated .
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @param {Number} page Number of pages
     * @param {Number} itemPerPage Item in per page
     */
    const getEvents = (page = 0, itemPerPage = 10) => {
        const sendData = {
            event_type: eventType,
            limit: itemPerPage || 10,
            page: page + 1,
            isPaginated: 1,
            groupKey: gKey,
        };

        try {
            props
                .getEvents(sendData)
                .then((res) => {
                    setTimeout(() => {
                        setloading(false);
                    }, 400)
                    res["data"]["data"] = res.data.data.map((event) => {
                        event["id"] = event.event_uuid;
                        return event;
                    });
                    setEventListData(res.data);
                })
                .catch((err) => {
                    setloading(false)
                    console.log(err);
                });
        } catch (err) {
            setloading(false)
            console.log(err);
        }
    };


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used to handling page size value for server side pagination in events list and fetch
     * events data by page size value and pass data in getEvents method and get response according to page size.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @param {Number} page Number of pages
     */
    const fetchEventByPage = (page) => {
        getEvents(page, pageSizeValue);
    };


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used to handling page number for server side pagination in events list and fetch
     * events data by page number and pass data in getEvents method and get response according to page number.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @param {Number} pageSize Number of pages
     */
    const handlePageSizeChange = (pageSize) => {
        setPageSizeValue(pageSize);
        getEvents(0, pageSize);
    };

    // column define data in events table and add data dynamically i rows according to column values
    const fields = {
        title: {
            field: "title",
            headerName: t("eventTitle"),
            // width: 260,
            flex: 2,
            editable: false,
            // headerAlign: "center",
            cellClassName: "text_center_eventsList",
            renderCell: (params) => {
                const {row} = params;
                if (row.title) {
                    return (
                        <span className="eventNameLink" onClick={() => eventModifyHandler(row.id)}>
                            {row.title}
                        </span>
                    )
                }
                else {
                    return "No event name"
                }

            }
        },
        organizer: {
            field: "organizer",
            headerName: t("org"),
            width: 140,
            editable: false,
            // headerAlign: "center",
            cellClassName: "text_center_eventsList",
            valueGetter: (params) => {
                let result = [];

                if (params.row.organiser !== null) {
                    result.push(params.row.organiser.fname);
                } else {
                    result = [" "];
                }
                return result;
            },
            // flex: 0.5,
        },
        type: {
            field: "type",
            headerName: t("type"),
            width: 200,
            // headerAlign: "center",
            cellClassName: "text_center_eventsList recurrenceCellCustomOverflow",
            renderCell: (params) => {
                let result = [];
                const {row} = params;
                console.log('asdas', row)

                if (params.row.event_type) {
                    params.row.event_type === 1
                        ? result.push(t('cafeteria_event'))
                        : params.row.event_type === 2
                            ? result.push(t('executive_event'))
                            : result.push(t('manager_event'));
                } else {
                    result = ["Unknown"];
                }
                let showRecIcon = _.has(row, ["recurrence_data"]) && row.recurrence_data !== null
                    && row.recurrence_data !== undefined;

                return (
                    <span className="reccCellWithIcon">
                        {showRecIcon &&
                            <Tooltip arrow title={t('reccEvent')} placement="top-start" className="tooltipRecIcon">
                                <span>
                                    <RecurrenceIcon />
                                </span>
                            </Tooltip>
                        }
                        &nbsp;
                        {result}
                    </span>
                )
            },
            flex: 1,
        },

        start_time: {
            field: "start_time",
            headerName: t("st"),
            width: 220,
            // headerAlign: "center",
            cellClassName: "text_center_eventsList",
            renderCell: (data) => {
                const {row} = data;
                const startDate = _.has(row, [
                    "recurrence_data",
                    "recurrence_start_date",
                ])
                    ? moment(row.recurrence_data.recurrence_start_date).format('DD MMM YYYY')
                    : moment(row.start_date).format('DD MMM YYYY');
                const startTime = moment(row.start_time, "HH:mm:ss").format("hh:mm A");
                return (
                    <span>
                        {startDate} at {startTime}
                    </span>
                );
            },
            flex: 1,
        },
        end_time: {
            field: "end_time",
            headerName: t("et"),
            width: 220,
            // headerAlign: "center",
            cellClassName: "text_center_eventsList",
            renderCell: (data) => {
                const {row} = data;
                const endDate = _.has(row, ["recurrence_data", "recurrence_start_date"])
                    ? moment(row.recurrence_data.recurrence_end_date).format('DD MMM YYYY')
                    : moment(row.end_date).format('DD MMM YYYY');
                const endTime = moment(row.end_time, "HH:mm:ss").format("hh:mm A");
                return (
                    <span>
                        {endDate} at {endTime}
                    </span>
                );
            },
            flex: 1,
        },
        action: () => {
            return {
                field: "",
                headerName: "",
                headerClassName: "ActionTab",
                Width: 50,
                align: "center",
                // flex:0.1,
                // headerAlign: "center",
                sortable: false,
                renderCell: (params) => {
                    const {row} = params;
                    return (
                        <IconButton className="deleteEventCell" onClick={() => deleteCallBack(row.id)}>
                            <DeleteIcon />
                        </IconButton>
                    );
                },
            };
        },
    };
    const pastColumns = [
        fields.title,
        fields.organizer,
        fields.type,
        // fields.date,
        fields.start_time,
        fields.end_time,
        fields.action(true, true),
    ];
    const futureColumns = [
        fields.title,
        fields.organizer,
        fields.type,
        // fields.date,
        fields.start_time,
        fields.end_time,
        fields.action(),
    ];

    // handles tabs value and show events according the value
    const tabData = [
        {
            label: t("future"),
            href: `/${gKey}/event-list/${Constants.eventType.FUTURE_EVENT}`,
            child: (
                <div>
                    <EventTable
                        columns={futureColumns}
                        rows={eventListData.data}
                        getRowId={(row) => row.event_uuid}
                        totalItems={eventListData.meta.total}
                        fetchList={fetchEventByPage}
                        onPageSizeChange={handlePageSizeChange}
                        onPageChange={fetchEventByPage}
                        className="eventListCustomDataGrid"
                    />
                </div>
            ),
        },
        {
            label: t("draft events"),
            href: `/${gKey}/event-list/${Constants.eventType.DRAFT_EVENT}`,
            child: (
                <div>
                    <EventTable
                        columns={futureColumns}
                        rows={eventListData.data}
                        getRowId={(row) => row.event_uuid}
                        totalItems={eventListData.meta.total}
                        fetchList={fetchEventByPage}
                        onPageSizeChange={handlePageSizeChange}
                        onPageChange={fetchEventByPage}
                        className="eventListCustomDataGrid"
                    />
                </div>
            ),
        },
        {
            label: t("past"),
            href: `/${gKey}/event-list/${Constants.eventType.PAST_EVENT}`,
            child: (
                <div>
                    <EventTable
                        columns={pastColumns}
                        rows={eventListData.data}
                        getRowId={(row) => row.event_uuid}
                        totalItems={eventListData.meta.total}
                        fetchList={fetchEventByPage}
                        onPageSizeChange={handlePageSizeChange}
                        onPageChange={fetchEventByPage}
                        className="eventListCustomDataGrid"
                    />
                </div>
            ),
        },
    ];


    //returning/rendering eventList JSX structure
    return (
        <LoadingSkeleton loading={loading} skeleton={<EventListSkeleton/>}>
            <div>
                <div className="EventListPage">
                    <Grid container specing={0} lg={12} sm={8}>
                        <Grid item lg={12} sm={12}>
                            <BreadcrumbsInput
                                links={[
                                    Constants.breadcrumbsOptions.GROUP_NAME,
                                    Constants.breadcrumbsOptions.EVENTS_LIST,
                                ]}
                            />
                        </Grid>
                    </Grid>
                    <div>
                        {showFilter && (
                            <LabelDropdown list={list} setSelectedKey={setSelectedKey} />
                        )}
                    </div>
                    <NavTabs
                        tabId="user-tabs"
                        tabData={tabData}
                        tabValue={possibleRoutes.indexOf(eventType)}
                        redirectValue={tabData.map((a) => a.href)}
                        {...props}
                    />
                </div>
            </div>
        </LoadingSkeleton>
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

export default connect(mapStateToProps, mapDispatchToProps)(EventsList);
