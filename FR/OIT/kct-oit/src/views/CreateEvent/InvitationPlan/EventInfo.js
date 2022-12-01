import React, {useEffect, useState} from "react";
import {Button, Grid, Switch} from "@material-ui/core";
import InfoOutlinedIcon from "@material-ui/icons/InfoOutlined";
import _ from "lodash";
import {useAlert} from "react-alert";
import {useDispatch, useSelector} from "react-redux";
import moment from "moment-timezone";
import RegistrationWindow from "./RegistrationWindow.js";
import eventAction from "../../../redux/action/apiAction/event.js";
import Helper from "../../../Helper";
import Constants from "../../../Constants";
import {Box} from "@mui/material";
import {blue, green} from "@mui/material/colors";
import CircularProgress from "@mui/material/CircularProgress";
import {useTranslation} from "react-i18next";
import Tooltip from "@material-ui/core/Tooltip";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a child component shows event's basic details like title , description , start time , end Time
 * start date and end date and registration window and registration time.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {String} props.name Name of the event
 * @param {String} props.description Event description
 * @param {String} props.share_agenda Share the agenda of the event
 * @param {String} props.event_type Type of the event(network, content+network)
 * @returns {JSX.Element}
 * @constructor
 */
const EventInfo = (props) => {
    const dateFormat = 'MMMM DD, YYYY';
    const {t} = useTranslation("events");
    const [showInfo, setshowInfo] = useState("View All");
    const alert = useAlert();
    const [visible, setvisible] = useState(0);
    const [switchState, setSwitch] = useState(0);
    const event_data = useSelector((data) => data.Auth.eventDetailsData);
    const dispatch = useDispatch();
    const [labelButton, setLabelButton] = useState();
    const [showBox, setShowBox] = useState(true);
    // draft event model data
    const [eventId, setEventId] = useState();
    const [eventStatus, setEventStatus] = useState(0);
    const [isRegOpen, setIsRegOpen] = useState();
    const [date, setDate] = useState({
        regStartDate: "",
        regStartTime: "",
        eventStartDate: "",
        eventStartTime: "",
        regEndTime: "",
        regEndDate: "",
        eventEndTime: "",
        eventEndDate: "",
    });

    const [loading, setLoading] = React.useState(false);
    const [success, setSuccess] = React.useState(false);
    const [recur_note, setRecurNote] = React.useState(null);
    const timer = React.useRef();

    const showDescriptionText = props.description.length > 0 ? props.description : '-'

    useEffect(() => {
        if (_.has(event_data, ["event_uuid"])) {
            setEventId(event_data.event_uuid);
        }
        if (props) {
            if (props.share_agenda == 0) {
                setSwitch(0)
            } else {
                setSwitch(1)
            }
            setStateFromResponse(props);
        }

        if (!_.isEmpty(props.recur_data)) {
            let recurMessage = 'Every';
            if (props.recur_data.recurrence_type === Constants.recurrenceType.DAILY) {
                recurMessage += ' Day';
            }
            recurMessage += ` Until ${Helper.formatDateTime(props.recur_data.end_date, dateFormat)}`
            setRecurNote(recurMessage)
        }
    }, [props.name]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to displays  time and date values of events and  updates the state
     * for registration start and end time and date from the response in props which is coming from parent and keeps
     * time values in format using moment library.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} res Date for the registration open
     */
    const setStateFromResponse = (res) => {
        // setting data
        let newDate = {
            ...date,
            regStartDate: res.reg_start
                ? moment(res.reg_start).format("YYYY-MM-DD")
                : "",
            regStartTime: res.reg_start
                ? moment(res.reg_start).format("HH:mm:ss")
                : "",
            regEndDate: res.reg_end ? moment(res.reg_end).format("YYYY-MM-DD") : "",
            regEndTime: res.reg_end ? moment(res.reg_end).format("HH:mm:ss") : "",
        };
        if (_.has(res, ["start_date"])) {
            newDate = {
                ...newDate,
                eventStartDate: res.start_date
                    ? moment(res.start_date).format("YYYY-MM-DD")
                    : "",
                eventStartTime: res.start_date
                    ? moment(res.start_date).format("hh:mm A")
                    : "",
                eventEndTime: res.end_date
                    ? moment(res.end_date).format("hh:mm A")
                    : "",
                eventEndDate: res.end_date
                    ? moment(res.end_date).format("YYYY-MM-DD")
                    : "",
            };
        }
        setDate(newDate);

        // setting reg open status
        setIsRegOpen(res.is_reg_open);
        // setting event status for launched or not

        setEventStatus(res.status && res.status == 1 ? 1 : 2);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to save data for time and general information about event and also
     * changes the format for time and date to use correctly and update values on server by API calling if response is
     * successful then it updates states for that values.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} val Use for registration open or not
     */
    const updateEvent = (val) => {

        if (isNaN(val)) {
            val = 0
        }

        const data = {
            _method: "PUT",
            event_uuid: eventId,
            reg_start_time:
                isRegOpen == 0 && eventStatus != 1
                    ? moment().format("HH:mm:ss")
                    : date.regStartTime,
            reg_start_date:
                isRegOpen == 0 && eventStatus != 1
                    ? moment().format("YYYY-MM-DD")
                    : date.regStartDate,
            // "reg_start_date": isRegOpen ==0 && eventStatus!=1 ? moment().format("YYYY-MM-DD") :date.regStartDate,
            // "reg_start_time": isRegOpen ==0  && eventStatus!=1? moment().format("HH:mm:ss") :date.regStartTime,
            reg_end_date: date.regEndDate,
            reg_end_time: date.regEndTime,
            share_agenda: switchState,
            event_status: 1,
            is_reg_open: val != undefined && val != null ? val : 0,
            // "is_reg_open":  isRegOpen
        };

        try {
            setSuccess(false);
            setLoading(true);

            dispatch(eventAction.updateDraft(data)).then((res) => {
                setSuccess(true);
                setLoading(false);
                const data = res.data.data
                // setEventStatus(1)
                setStateFromResponse(res.data.data);
                props.setShowEventLinks(true);

                alert.show("Record Added Successfull", {type: 'success'});
            }).catch((err) => {
                setLoading(false);
                if (err.response && err.response.data) {
                    const er = err.response.data
                    if (_.has(er, ["errors"])) {
                        for (let key in er.errors) {
                            alert.show(er.errors[key], {type: 'error'});
                        }

                    }

                } else {
                    alert.show(Helper.handleError(err), {type: 'error'});
                }

            })
        } catch (err) {
            alert.show(Helper.handleError(err), {type: 'error'});
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle show and hide values  and updates state value for agenda section.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const ShowAll = () => {
        if (showInfo == "View All") {
            setshowInfo("View Less");
            setvisible(1);
        } else {
            setshowInfo("View All");
            setvisible(0);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle the switch state for show agenda on page upon clicking on the switch
     * button and updates state.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleSwitch = () => {
        setSwitch(switchState === 1 ? 0 : 1);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to get data from parent component by props and updating states for
     * time and date of registration window
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Data is used for event date and time
     * @param {String} data.startDate Event start date
     * @param {String} data.startTime Event start time
     * @param {String} data.endTime Event end time
     * @param {String} data.endDate Event end date
     */
    const getTime = (data) => {

        let newDate = {...date};
        setDate({
            ...newDate,
            regStartDate: data.startDate ? data.startDate : "",
            regStartTime: data.startTime ? data.startTime : "",
            regEndTime: data.endTime ? data.endTime : "",
            regEndDate: data.endDate ? data.endDate : "",
        });
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to update event data of time , date , share agenda , registration
     * open or close time and date on server by using api call and  update states if response is successful other wise
     * shows error massage.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Event update data
     * @param {String} data.startTime Event start time
     * @param {String} data.endTime Event end time
     * @param {String} data.startDate Event start date
     * @param {String} data.endDate Event end date
     * @param {Boolean} data.eventStatus Event published or not
     * @param {Boolean} data.isRegOpen Is registration open or not
     */
    const updateEventData = (data) => {
        let currentData = {
            "event_uuid": eventId,
            "reg_start_date": date.regStartDate,
            "reg_start_time": date.regStartTime,
            "reg_end_date": date.regEndDate,
            "reg_end_time": date.regEndTime,
            "share_agenda": switchState,
            "event_status": eventStatus,
            "is_reg_open": isRegOpen,
            "reg_time_updated": false,
            "_method": "PUT",
        }
        let newData = {
            ...currentData,
            ...data
        }
        try {
            dispatch(eventAction.updateDraft(newData)).then((res) => {
                data = res.data.data;
                setStateFromResponse(res.data.data);
                alert.show("Record Added Successfull", {type: 'success'});
            }).catch((err) => {
                if (err.response && err.response.data) {
                    const er = err.response.data
                    if (_.has(er, ["errors"])) {
                        for (let key in er.errors) {
                            alert.show(er.errors[key], {type: 'error'});
                        }
                    }
                } else {
                    alert.show(Helper.handleError(err), {type: 'error'});
                }
            })
        } catch (err) {
            alert.show(Helper.handleError(err), {type: 'error'});
        }
    }
    // updating css by condition
    const buttonSx = {
        ...(success && {
            bgcolor: blue[500],
            "&:hover": {
                bgcolor: blue[700],
            },
        }),
    };

    console.log('props.description', typeof props.description, props.description)
    return (
        <div className="invitationTemplate">
            <Grid container xs={12}>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className="customPara customPara-2"> Name : </p>
                    </Grid>
                    <Grid item xs={4}>
                        {props.name}
                    </Grid>
                </Grid>

                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className="customPara customPara-2"> Description : </p>
                    </Grid>
                    <Grid item xs={4}>
                        {/* {props.description} */}
                        {showDescriptionText}
                    </Grid>
                </Grid>

                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className="customPara customPara-2"> Event Schedule : </p>
                    </Grid>
                    <Grid item xs={8}>
                        {
                            _.isEmpty(props.recur_data) ?
                                `${Helper.formatDateTime(date.eventStartDate, dateFormat)} (${date.eventStartTime} to ${date.eventEndTime})`
                                : (props.recur_data.is_started
                                    ? `${date.eventStartTime} to ${date.eventEndTime}`
                                    : `${Helper.formatDateTime(props.recur_data.start_date, dateFormat)} (${date.eventStartTime} to ${date.eventEndTime})`)
                        }
                    </Grid>
                    {
                        !_.isEmpty(props.recur_data) && recur_note
                        && <>
                            <Grid item xs={3} />
                            <Grid item xs={8}>
                                {/*<br/>*/}
                                {recur_note}
                            </Grid>
                        </>

                    }
                </Grid>

                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className="customPara customPara-2"> Event Type : </p>
                    </Grid>
                    <Grid item xs={4}>
                        {props.event_type == 2 ? "Content + Networking" : "Networking"}
                        {!_.isEmpty(props.recur_data) &&
                        <span className={"event-type-recur-icon"}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="18.621" viewBox="0 0 15 18.621">
                            <g id="Icon_feather-repeat" data-name="Icon feather-repeat"
                               transform="translate(-3.75 -0.439)">
                              <path id="Path_537" data-name="Path 537" d="M25.5,1.5l3,3-3,3"
                                    transform="translate(-10.5)" fill="none"
                                    stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
                              <path id="Path_538" data-name="Path 538" d="M4.5,12V10.5a3,3,0,0,1,3-3H18"
                                    transform="translate(0 -3)"
                                    fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="1.5" />
                              <path id="Path_539" data-name="Path 539" d="M7.5,28.5l-3-3,3-3"
                                    transform="translate(0 -10.5)" fill="none"
                                    stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
                              <path id="Path_540" data-name="Path 540" d="M18,19.5V21a3,3,0,0,1-3,3H4.5"
                                    transform="translate(0 -9)"
                                    fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="1.5" />
                            </g>
                          </svg>
                        </span>
                        }
                    </Grid>
                </Grid>
                {props.event_type == 2 && (
                    <Grid container xs={12} className="FlexRow agendaFlex_row">
                        <Grid item xs={3}>
                            <p className="customPara customPara-2"> Event Agenda : </p>
                        </Grid>
                        <Grid item xs={9}>
                            {/* <EventAgenda startDate={props.start_date} endDate={props.end_date}/> */}
                            <div className="viewAllWrap">
                                <div className="viewAll_button">
                                    <p className="viewAll_txt" onClick={ShowAll}>
                                        {showInfo}
                                    </p>
                                    <div class="gray_saprator"></div>
                                </div>
                                {visible == 1 && (
                                    <div className="fullInfoWrap">
                                        {props.agenda &&
                                        !_.isEmpty(props.agenda) &&
                                        props.agenda.map((v, i) => (
                                            <div>
                                                <p className="customPara">{v.moment_name}</p>
                                                <p className="startEndTime">
                                                    {v.start_time}
                                                    <span className="customPara"> - </span>
                                                    {v.end_time}
                                                </p>
                                                <p>{v.moment_description}</p>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </Grid>
                    </Grid>
                )}
                {props.event_type == 2 && (
                    <Grid container xs={12} className="FlexRow">
                        <Grid item xs={3}>
                            <p className="customPara customPara-2">
                                {" "}
                                Share Agenda :{" "}
                                <Tooltip arrow title={t("share_agenda")}>
                                    <InfoOutlinedIcon />
                                </Tooltip>
                            </p>
                        </Grid>
                        <Grid item xs={4}>
                            <Switch
                                checked={switchState}
                                color="primary"
                                onClick={handleSwitch}
                            />
                        </Grid>
                    </Grid>
                )}

                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className="customPara customPara-2"> Event Status : </p>
                    </Grid>
                    {eventStatus === Constants.eventStatusDraft ? (
                        <Grid item xs={4}>
                            <Box sx={{display: "flex", alignItems: "center"}}>
                                <Box sx={{m: 1, position: "relative"}}>
                                    <Button
                                        color="primary"
                                        variant="contained"
                                        onClick={updateEvent}
                                        sx={buttonSx}
                                        disabled={loading}
                                        // onClick={handleButtonClick}
                                    >
                                        PUBLISH EVENT
                                    </Button>
                                    {loading && (
                                        <CircularProgress
                                            size={24}
                                            sx={{
                                                color: green[500],
                                                position: "absolute",
                                                top: "50%",
                                                left: "50%",
                                                marginTop: "-12px",
                                                marginLeft: "-12px",
                                            }}
                                        />
                                    )}
                                </Box>
                            </Box>


                        </Grid>
                    ) : (

                        <Grid item xs={4}>
                            PUBLISHED
                        </Grid>
                    )}
                </Grid>

                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className="customPara customPara-2">
                            {" "}
                            Registration Window :{" "}
                            <Tooltip arrow title={t("registration_window")}>
                                <InfoOutlinedIcon />
                            </Tooltip>
                        </p>
                    </Grid>
                    <Grid item xs={9}>

                        <RegistrationWindow
                            startTime={
                                isRegOpen == 0 ? moment().format("HH:mm:ss") : date.regStartTime
                            }
                            endTime={date.regEndTime}

                            startDate={
                                isRegOpen == 0
                                    ? moment().format("YYYY-MM-DD")
                                    : date.regStartDate
                            }
                            endDate={date.regEndDate}
                            getTime={getTime}
                            eventStatus={eventStatus}
                            isRegOpen={isRegOpen}
                            updateEventData={updateEventData}
                        />
                    </Grid>
                </Grid>
            </Grid>
        </div>
    );
};
export default EventInfo;
