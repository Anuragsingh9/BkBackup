import React, {useEffect, useState} from "react";
import Paper from "@material-ui/core/Paper";
import {useDispatch, useSelector} from "react-redux";
import eventAction from "../../../../redux/action/apiAction/event";
import {Field, reduxForm} from "redux-form";
import "./Scheduling.css";
import TextareaAutosize from "@material-ui/core/TextareaAutosize";
import InfoOutlinedIcon from "@material-ui/icons/InfoOutlined";
import {
    Button,
    ButtonGroup,
    Checkbox,
    ClickAwayListener,
    FormControl,
    Grid,
    Grow,
    InputLabel,
    Link,
    MenuItem,
    MenuList,
    Popper,
    Select,
    TextField,
} from "@material-ui/core";
import ArrowDropDownIcon from "@mui/icons-material/ArrowDropDown";
import {useTranslation} from "react-i18next";
import Helper from "../../../../Helper";
import {useAlert} from "react-alert";
import eventReduxAction from "../../../../redux/action/reduxAction/event";
import _ from "lodash";
import LoadingContainer from "../../../Common/Loading/Loading";
import Tooltip from "@material-ui/core/Tooltip";
import Validation from "../../../../functions/ReduxFromValidation";
import moment from "moment-timezone";
import {KeyboardDatePicker, MuiPickersUtilsProvider,} from "@material-ui/pickers";
import DateFnsUtils from "@date-io/moment";
import TimePickerComp from "../../../Common/TimePicker";
import {useParams} from "react-router-dom";
import RecurrencePopup from "./RecurrencePopup";
import Constants from "../../../../Constants";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is used to check the start time and end time and if values are not correct then this
 * method returns the error massage for that value.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} values Validate the scheduling values
 * @param {String} values.start_time Scheduling start time
 * @param {String} values.end_time Scheduling end time
 * @param {object} errors Error messages for(start_time, end_time)
 */
const validateStartEndTime = (values, errors) => {
    if (values == undefined || values['start_time'] == undefined || values['end_time'] == undefined) {
        return;
    }
    let startTime = new moment();

    let start_time = convertTimeToObject(values['start_time']);
    let end_time = convertTimeToObject(values['end_time']);
    // console.log("sssssss111", values['start_time'], start_time, end_time);
    startTime.set({h: _.has(values['start_time'], ['h'])})

    if (start_time.h == end_time.h && start_time.m == end_time.m) {
        errors["end_time"] = "Start time and end time cannot be same";
    }
    return errors;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is used to check the values for start time and end time and title and date if values
 *  are empty then this method returns the error massage for that
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} values Validate the scheduling values
 * @param {String} values.title Event title
 * @param {String} values.date Event date
 * @param {String} values.start_time Event start time
 * @param {String} values.end_time Event end time
 * @returns errors
 */
const validate = (values) => {
    let errors = {};
    const requiredFields = ["title", "date", "start_time", "end_time"];

    requiredFields.forEach((field) => {
        if (!values[field]) {
            errors[field] = "Required";
        }
        if (field === "start_time" || field === "end_time") {
            validateStartEndTime(values, errors);
        }
    });
    return errors;
};

const convertTimeToObject = (startTime) => {
    if (typeof startTime === 'string') { //18:30
        let time = startTime.split(":");
        return {h: time['0'], m: time['1']};
    } else {
        return {h: startTime['h'], m: startTime['m']};
    }

}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component for date picker in redux form and returns the datepicker component in UI.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Input} input Actual input box with its default properties
 * @param {String} label Labels for user interaction
 * @param {String} defaultValue Default value of input box
 * @param {Object} touched To indicate if the input box is touched or not
 * @param {Object} error Error message from input box
 * @method
 */
const renderDatePicker = (
    {input, label, defaultValue, meta: {touched, error}, ...custom},
    ) => {
    console.log('input', input)
    return (
        <React.Fragment>
            <MuiPickersUtilsProvider utils={DateFnsUtils}>
                <KeyboardDatePicker
                    name={input.name}
                    variant="inline"
                    size="small"
                    inputVariant="outlined"
                    defaultValue={defaultValue}
                    onChange={input.onChange}
                    errorText={touched && error}
                    error={touched && error}
                    disablePast={true}
                    value={"2021-09-28"}
                    format={"YYYY-MM-DD"}
                    {...input}
                />
            </MuiPickersUtilsProvider>
            {touched && error && <span className={"text-danger"}>{error}</span>}
        </React.Fragment>
    );
};

const renderTextField = ({
                             input, label, defaultValue, meta: {touched, error}, ...custom
                         }) => {
    input.value = Helper.jsUcfirst(input.value);
    return (
        <React.Fragment>
            <TextField
                name={input.name}
                variant="outlined"
                defaultValue={Helper.jsUcfirst(defaultValue)}
                onChange={input.onChange}
                errorText={touched && error}
                error={touched && error}
                {...input}
                {...custom}
            />
            {touched && error && <span className={"text-danger"}>{error}</span>}
        </React.Fragment>
    );
};

const rendersmallTextField = ({
                                  input,
                                  label,
                                  defaultValue,
                                  meta: {touched, error},
                                  ...custom
                              }) => {

    return (
        <React.Fragment>
            <TextField
                name={input.name}
                variant="outlined"
                defaultValue={defaultValue}
                onChange={input.onChange}
                errorText={(touched && error) || custom.isValidate}
                error={(touched && error) || custom.isValidate}
                {...input}
                {...custom}
            />
            {touched && error && <span className={"text-danger"}>{error}</span>}
            {custom.isValidate && <span className={"text-danger"}>{custom.isValidate}</span>}
        </React.Fragment>
    );
};

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used to create and update events in which user can update title , description ,
 * start time , end time and date for new and existing events and also can set types like networking and
 * content + networking and event can be modified if is not published that time.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.setShowEventLinks To set the event links
 * @param {Function} props.setShowLiveTab To show live tab
 * @returns {JSX.Element}
 * @constructor
 */
const Scheduling = (props) => {
    const {t} = useTranslation("events");
    // redux form props
    const {handleSubmit, initialize} = props;

    var paramsData = props.match.params;

    // user badge data
    const user_badge = useSelector((data) => data.Auth.userSelfData)
    // date and time state
    const [state, setState] = useState({
        date: '',
        start_time: '',
        end_time: '',
        description: '',
        recurrence_type: Constants.recurrenceType.NONE,
        recurrence_end_date: '',
        recurrence_interval: 1,
        recurrence_weekdays: '',
        recurrence_onDay: 1,
        recurrence_month_type: '',
        recurrence_on_month_week: 1,
        recurrence_on_month_week_day: 'Monday',
    });
    // event active state
    const [active_event, setActive] = useState(false);
    // loading state
    const [loading, setLoading] = useState(false);
    // dispatch hook from redux
    const dispatch = useDispatch();
    // alert hook
    const alert = useAlert();
    // description hide/show state
    const [showDes, setDes] = useState(false);
    // event name state
    const [event_Name, setName] = useState('');

    const [customUrl, setCustomUrl] = useState("");
    const [customUrlErrorMessage, setCustomUrlErrorMessage] = useState(false);
    // dummy event state
    const [is_dummy_event, setDummyEvent] = useState(false);
    const [recDailyLabel, setRecDailyLabel] = useState('Daily');
    const [recWeeklyLabel, setRecWeeklyLabel] = useState('Weekly');
    const [recMonthlyLabel, setRecMonthlyLabel] = useState('Monthly');
    const [recurrenceSelectToolTip, setRecurrenceSelectToolTip] = useState('');
    const [recWeekDAYLabel, setRecWeekDAYLabel] = useState('Week Day');
    const [recModelMode, setRecModelMode] = useState(Constants.recurrenceType.NONE);
    const [eventOccurLine, setEventOccurLine] = useState('');
    // const [recModelMode, setRecModelMode] = useState(0);

    // event start time available hours
    const [startTimeMin, setStartTimeMin] = useState({h: 0, m: 0});
    const [endTimeMin, setEndTimeMin] = useState({h: 0, m: 0});
    const [selectedStartTime, setSelectedStartTime] = useState(new moment());
    const [selectedEndTime, setSelectedEndTime] = useState(new moment());

    //event type state
    const [event_type, setEventType] = useState(1);
    //split button show menu
    const [open, setOpen] = React.useState(false);
    //split button ref
    const anchorRef = React.useRef(null);
    // set create type
    const [createType, setCreateType] = useState();
    const [recModalOpen, setRecModalOpen] = useState(false)

    const {gKey} = useParams();

    const urlName = window.location.host;


    //check draft event
    // const [isDraft , setIsDraft] = useState(false);


    useEffect(() => {
        let today = new moment();
        if (
            !disabled &&
            selectedStartTime.format("YYYY-MM-DD") === today.format("YYYY-MM-DD")
        ) {
            let newStartTimeRange = {...startTimeMin};
            newStartTimeRange.h = today.hour();
            newStartTimeRange.m =
                selectedStartTime.hour() === today.hour() ? today.minute() : 0;
            setStartTimeMin(newStartTimeRange);
            let newEndTimeRange = {...endTimeMin};
            newEndTimeRange.h = 0;
            newEndTimeRange.m = 0;
            setEndTimeMin(newEndTimeRange);
        } else {
            let newStartTimeRange = {...startTimeMin};
            newStartTimeRange.h = 0;
            newStartTimeRange.m = 0;
            setStartTimeMin(newStartTimeRange);
            let newEndTimeRange = {...endTimeMin};
            newEndTimeRange.h = selectedStartTime.hour();
            newEndTimeRange.m = selectedStartTime.minute();
            setEndTimeMin(newEndTimeRange);
        }
    }, [selectedStartTime, selectedEndTime, active_event]);

    useEffect(() => {
        if (state.recurrence_end_date) {
            setRecDailyLabel(`Daily Until - ${state.recurrence_end_date}`);
        }
    }, [state.recurrence_end_date]);

    useEffect(() => {
        var params = props.match.params;
        if (_.has(params, ['event_uuid'])) {
            getEventData(params.event_uuid);
        } else {
            basicInitialise();
        }
    }, [props.match.params])

    useEffect(() => {
        let occurLine = state.recurrence_type === Constants.recurrenceType.NONE
            ? `happen on ${state.date}`
            : Helper.prepareRecurrenceLine(
                state.recurrence_interval,
                state.recurrence_type,
                state.recurrence_end_date,
                state.recurrence_onDay,
                Helper.convertNumberToWeekDay(state.recurrence_weekdays),
                state.recurrence_month_type,
                state.recurrence_on_month_week,
                state.recurrence_on_month_week_day,
            )
        setEventOccurLine(            `Your event ${Helper.jsUcfirst(event_Name)} ` +
            `will ${occurLine} from ` +
        `${moment(state.start_time, ["HH.mm"]).format("hh:mm a")} to ` +
        `${moment(state.end_time, ["HH.mm"]).format("hh:mm a")}`

        )
    }, [state]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to update Event name if event is not published and update state of event name.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const setEventName = (e) => {
        const capFirstLatter = Helper.jsUcfirst(e.target.value);
        setName(capFirstLatter);
        handleChange(e);
    };

    /**
     * --------------------------------------------------------------------------------------------------------------------
     * @description - function for custom url state on change.
     * --------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const changCustomUrl = (e) => {
        console.log('dddddddddddddddd change called', e.target.value);
        let data = {
            key: e.target.value,
        }
        var params = props.match.params;

        if (_.has(params, ["event_uuid"])) {
            data['current_event'] = params.event_uuid;
        }
        dispatch(eventAction.checkEventCode(data))
            .then((res) => {
                setCustomUrlErrorMessage(res.data.data.available ? false : "Url not available");
            })

        setCustomUrl(e.target.value);
        handleChange(e);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to create date object that takes input of simple date and converts that value
     * according to time zone for month, day and year
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} date Event date
     */
    const formatDateFunc = (date) => {
        return new Date(date).toLocaleDateString(
            {},
            {timeZone: "UTC", month: "long", day: "2-digit", year: "numeric"}
        );
    };


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is used to create a dummy users for events to show in grid box on "Events Platform"
     * and updates the state of dummy users.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const setDummy = (e) => {
            if (e.target.value == "false") {
                setDummyEvent(true);
            } else {
                setDummyEvent(false);
            }
        }
    ;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is used to sends the collection of data for event like date , time , description,
     * title , type and draft or not values to server using API call if response is successful it updates state values
     * for event is live or not.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Event} value Event data
     */
    const submitForm = (value) => {
        const group =
            _.has(user_badge, ["current_group"]) && user_badge.current_group;
        var params = props.match.params;
        // moment converts time in this formate 09:05:00 (format("hh:mm:ss"))
        const startTime = moment(state.start_time, ["HH.mm"]).format("HH:mm:ss");
        const endTime = moment(state.end_time, ["HH.mm"]).format("HH:mm:ss");
        const isRecurrence = recModelMode !== 0;
        let eventData = {
            ...value,
            date: new moment(state.date).format('YYYY-MM-DD'),
            description: state.description ? state.description : "",
            start_time: startTime,
            end_time: endTime,
            is_dummy_event: is_dummy_event ? 1 : 0,
            type: event_type,
            group_key: gKey,
            draft: event_type == 2 ? 1 : createType == 2 ? 1 : 0,
            join_code: customUrl ? customUrl : ""
        };
        if (isRecurrence) {
            eventData = {
                ...eventData,
                event_recurrence: {
                    recurrence_type: state.recurrence_type,
                    recurrence_end_date: state.recurrence_end_date,
                    recurrence_interval: state.recurrence_interval,
                }
            }
            if (recModelMode === Constants.recurrenceType.MONTHLY) {
                eventData = {
                    ...eventData,
                    event_recurrence: {
                        ...eventData.event_recurrence,
                        recurrence_ondays: state.recurrence_onDay,
                        recurrence_month_type: state.recurrence_month_type,
                        recurrence_on_month_week: state.recurrence_on_month_week,
                        recurrence_on_month_week_day: state.recurrence_on_month_week_day,
                    }
                }
            }
            if (recModelMode === Constants.recurrenceType.WEEKLY || recModelMode === Constants.recurrenceType.WEEKDAY) {
                eventData = {
                    ...eventData,
                    event_recurrence: {
                        ...eventData.event_recurrence,
                        rec_weekdays: state.recurrence_weekdays,
                    }
                }
            }
        } else {
            delete eventData.event_recurrence;
            delete eventData.recurrence_type;
            delete eventData.recurrence_onDay;
            delete eventData.recurrence_interval;
            delete eventData.rec_interval;
            delete eventData.rec_weekdays;
            delete eventData.recurrence_end_date;
        }
        if (_.has(params, ["event_uuid"])) {
            eventData.event_uuid = params.event_uuid;
            eventData.start_time = startTime;
            eventData.end_time = endTime;
            eventData._method = `PUT`;
        }

        console.log('dddddddddd event data', eventData);

        try {
            dispatch(eventAction.createEvent(eventData))
                .then((res) => {
                    dispatch(eventReduxAction.setEventData(res.data.data));

                    if (_.has(params, ["event_uuid"])) {

                        const {time_state} = res.data.data;
                        setActive(time_state.is_live == 1);
                        alert.show("Record Updated SuccessFully", {type: "success"});

                    } else {
                        props.history.push(`/${gKey}/edit-event/${res.data.data.event_uuid}`);
                        props.handleNext();
                        alert.show("Record Added SuccessFully", {type: "success"});
                    }
                })
                .catch((err) => {
                    if (err && _.has(err.response.data, ["errors"])) {
                        var errors = err.response.data.errors;
                        for (let key in errors) {
                            alert.show(errors[key], {type: "error"});
                        }
                    } else if (err && _.has(err.response.data, ["msg"])) {
                        var er = err.response.data.errors;
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
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is used to fetch the event data from the server by passing event Id in get API call
     * and if response is successful then it updates states for event type , active event , event time , event title ,
     * event dummy user or other wise it shows error response.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id  Event ID
     */
    const getEventData = (id) => {
        setLoading(true);
        try {
            dispatch(eventAction.getSingleEvent(id)).then((res) => {
                const {time_state} = res.data.data;
                setActive(time_state.is_live == 1);
                dispatch(eventReduxAction.setEventData(res.data.data));
                let event_data = res.data.data;
                const {event_recurrence} = res.data.data;

                let newState = {
                    ...state,
                    start_time: event_data.start_time,
                    end_time: event_data.end_time,
                    date: event_data.date,
                    description: event_data.description,
                };

                if (event_recurrence !== null) {
                    const recc_endDate = event_recurrence.recurrence_end_date;
                    newState = {
                        ...newState,
                        recurrence_type: event_recurrence.recurrence_type,
                        recurrence_end_date: event_recurrence.recurrence_end_date,
                        recurrence_interval: event_recurrence.repeat_interval,
                        recurrence_weekdays: event_recurrence.rec_weekdays,
                        recurrence_onDay: event_recurrence.recurrence_ondays,
                        recurrence_month_type: event_recurrence.recurrence_month_type,
                        recurrence_on_month_week: event_recurrence.recurrence_on_month_week,
                        recurrence_on_month_week_day: event_recurrence.recurrence_on_month_week_day,
                    };
                    setRecModelMode(event_recurrence.recurrence_type);

                    updateRecSelectLabel(newState);

                    event_data['recurrence_end_date'] = event_recurrence.recurrence_end_date;
                    event_data['rec_interval'] = event_recurrence.repeat_interval;
                    event_data['recurrence_onDay'] = event_recurrence.recurrence_ondays[0];
                    event_data['rec_start_date'] = event_data.date;
                    event_data['recurrence_month_type'] = event_recurrence.recurrence_month_type;
                    event_data['recurrence_on_month_week'] = event_recurrence.recurrence_on_month_week;
                    event_data['recurrence_on_month_week_day'] = event_recurrence.recurrence_on_month_week_day;
                } else {

                    newState = {
                        ...newState,
                        recurrence_type: Constants.recurrenceType.NONE,
                        recurrence_end_date: event_data.date,
                        recurrence_interval: 1,
                        recurrence_weekdays: 0,
                        recurrence_onDay: 1,
                        recurrence_month_type: 1,
                        recurrence_on_month_week: 1,
                        recurrence_on_month_week_day: 'Monday',
                    };

                    event_data['recurrence_end_date'] = event_data.date;
                    event_data['rec_interval'] = 1;
                    event_data['recurrence_onDay'] = 1;
                    event_data['rec_start_date'] = event_data.date;
                    event_data['recurrence_month_type'] = 1;
                    event_data['recurrence_on_month_week'] = 1;
                    event_data['recurrence_on_month_week_day'] = 'Monday';
                }
                initialize(event_data);
                setEventType(event_data.type);
                setState(newState);
                setName(event_data.title);
                setDummyEvent(event_data.is_dummy_event == 1);
                setLoading(false);
                setCustomUrl(event_data.join_code ? event_data.join_code : "")
                props.setShowEventLinks(
                    _.has(res.data.data, ["event_draft", "status"]) &&
                    res.data.data?.event_draft?.status === 1
                );
                props.setShowLiveTab(
                    _.has(res.data.data, ["is_auto_key_moment_event"]) &&
                    res.data.data.is_auto_key_moment_event === 1
                );
            }).catch((err) => {
                alert.show(Helper.handleError(err), {type: "error"});
                setLoading(false);
            });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
            setLoading(false);

        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to multiply with 10 in input value of time which updated in time input field
     * and returns the converted result of 10 and if values is equals to 60 then value would be converted in "00".
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} value value is used to take multiple of 10 for event time
     */
    const convertTo10Multiple = (value) => {
        let newvalue = value +
            // if there is remainder after 10 module then make it next 10 multiple
            (value % 10 !== 0 ? 10 - (value % 10) : 0);

        if (newvalue == 60) {
            newvalue = "00";
        }
        return newvalue;
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is for adding one hour if minutes are greater than 50 in input value for hour and
     * returns result
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} h Event time - hours
     * @param {Number} m Event time - minutes
     * @returns  h
     */
    const addOneHours = (h, m) => {
        h = parseInt(h);
        if (m > 50) {
            h = h + 1;
        }
        return h;
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to create a basic prototype of date and time to initialise values in time
     * input fields. Values will be displayed if the event is new and it will return string for start time and end time.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const basicInitialise = () => {
        const date = moment().format("YYYY-MM-DD");
        Date.prototype.addHours = function (h) {
            this.setHours(this.getHours() + h);
            return this;
        };

        var newDate = moment()._d;
        const n = newDate.toLocaleTimeString(navigator.language, {
            hour: "2-digit",
            minute: "2-digit",
            hour12: false,
        });

        const secondTime = moment().add(1, "hours")._d;
        const m = secondTime.toLocaleTimeString(navigator.language, {
            hour: "2-digit",
            minute: "2-digit",
            hour12: false,
        });
        const startString = n.split(":");
        const newStart = round(
            parseInt(startString[0]),
            convertTo10Multiple(parseInt(startString[1]))
        );
        const endString = m.split(":");
        const newEnd = round(parseInt(endString[0]), parseInt(endString[1]));
        initialize({
            title: "",
            start_time: newStart,
            end_time: newEnd,
            date: date,
            description: "",
            recurrence_end_date: date,
            rec_interval: 1,
            recurrence_onDay: 1,
            rec_weekdays: 0,
            rec_start_date: date,
            recurrence_month_type: Constants.recurrenceMonthType.ON_DAY,
            recurrence_on_month_week: Constants.recurrenceMonthWeek.FIRST,
            recurrence_on_month_week_day: 'Monday',
        });
        const addone = addOneHours(parseInt(startString[0]), startString[1]);

        setState({
            ...state,
            start_time: `${addOneHours(startString[0], startString[1])}:${convertTo10Multiple(parseInt(startString[1]))}`,
            end_time: `${addOneHours(endString[0], endString[1])}:${convertTo10Multiple(parseInt(endString[1]))}`,
            date: date,
            recurrence_end_date: date,
        });
        // setState({start_time: `${startString[0]}:${convertTo10Multiple(parseInt(startString[1]))}`,end_time: m,date: date})
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     *  @description Function for converting the start and end time
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {string} h Event start time hour
     * @param {string} x Event start time minute
     * @method
     *
     * */
    const round = (h, x) => {
        let newX = "";
        if (x % 5 == 0) {
            newX = parseInt(Math.floor(x / 5)) * 5;
        } else {
            newX = parseInt(Math.floor(x / 5)) * 5 + 5;
        }

        if (newX < 10) {
            newX = `0${newX}`;
        }
        return `${h}:${newX}`;
    };

    // use effect for getting event data api hit
    useEffect(() => {
        var params = props.match.params;
        if (_.has(params, ["event_uuid"])) {
            getEventData(params.event_uuid);
        } else {
            basicInitialise();
        }
    }, [props.match.params]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method handles state value to show and hide the description field and prevents reloading
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const handleDesShow = (e) => {
            e.preventDefault();
            setDes(!showDes);
        }
    ;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This is a common method to handles input field values and update states for schedule events
     * details
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const handleChange = (e) => {
        const name = e.target.name;
        const value = e.target.value;
        setState({...state, [name]: value});
    };

    const handleChangeField = (name, value) => {
        handleChange({target: {name, value}})
    }

    const {accessMode} = props;
    const disabled = accessMode || active_event;

    ////////////////////////split button
    const options = [
        "Create a merge commit",
        "Squash and merge",
        "Rebase and merge",
    ];

    const [selectedIndex, setSelectedIndex] = React.useState(0);

    const handleClick = () => {
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to select menu item  like create/edit event and Draft event and update state
     * for selecting item index and hide drop down , and also store value in state for create event type value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} event Javascript event object
     * @param {Number} index Index for menu item
     */
    const handleMenuItemClick = (event, index) => {
        setCreateType(event.target.value);
        setSelectedIndex(index);
        setOpen(false);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle toggle value state to show or hide dropdown for event type button.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleToggle = () => {
        setOpen((prevOpen) => !prevOpen);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handles time change in the input field and update the state for time
     * values
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} field  field is used for start time
     * @param {Object} t Use for time(JavaScript time object)
     */
    const handleTimeChange = (field, t) => {
        const time = `${t.h}:${t.m}:00`;
        if (field === "start_time") {
            let newStart = selectedStartTime.clone();
            newStart.hour(t.h);
            newStart.minute(t.m);
            setSelectedStartTime(newStart);
        } else {
            let newStart = new moment();
            newStart.hour(t.h);
            newStart.minute(t.m);
            setSelectedEndTime(newStart);
        }
        handleChange({target: {name: field, value: time}});
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handles the updated data and keeps the format of date
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} val Used event time(year, month, date) and format(JavaScript time Object)
     */
    const handleDateChange = (val) => {
        let newDate = selectedStartTime.clone();
        newDate.set({year: val.year(), month: val.month(), date: val.date()});
        setSelectedStartTime(newDate);
        handleChange({target: {name: "date", value: val.format("yyyy-MM-DD")}});

        let recEnd = new moment(state.recurrence_end_date);
        if (recEnd.format('YYYY-MM-DD') < newDate.format("YYYY-MM-DD")) {
            recEnd.set({year: val.year(), month: val.month(), date: val.date()});
        }

        let newState = {
            ...state,
            title: event_Name,
            join_code: customUrl,
            date: val,
            rec_start_date: new moment(val),
            rec_interval: state.recurrence_interval || 1,
            recurrence_end_date: recEnd,
        }

        setState(newState);
        initialize(newState);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this method is used to handles values for reference to current vlaues for drop down state
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} event Javascript event object
     */
    const handleClose = (event) => {
        if (anchorRef.current && anchorRef.current.contains(event.target)) {
            return;
        }

        setOpen(false);
    };


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used handling event occurence dropdown functionality
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} e Javascript Event Object
     */
    const handleRecurrenceChange = (e) => {
        setRecModelMode(e.target.value)
        setRecModalOpen(e.target.value !== Constants.recurrenceType.NONE);
        console.log('dddddddddddddddd console ', e.target.value);
        if (e.target.value === Constants.recurrenceType.NONE) {

            setState({
                ...state,
                recurrence_type: e.target.value,
            })
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the line for the recurrence to show the basic details of current selection data of
     * recurrence for the event so user will see what type of recurrence will be there with date, type and respective
     * selection Here the type of recurrence will be detected so in week if mon-fri is selected it will be counted as
     * weekday if mon-sun is selected with 1 interval it will be counted as everyday
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Number} localState.recurrence_interval Interval of recurring with respect to type
     * @param {Number} localState.recurrence_type Type of the recurring
     * @param {Number} localState.recurrence_weekdays Selected weekdays if recurring is weekly or weekdays in number
     * @param {Number} localState.recurrence_onDay The value for the month data on which the event will recur
     * @param {String} localState.recurrence_end_date Recurrence end date
     * @returns {String}
     */
    const updateRecSelectLabel = (localState = null) => {
        localState = localState ? localState : state;

        let label = Helper.prepareRecurrenceLine(
            localState.recurrence_interval,
            localState.recurrence_type,
            localState.recurrence_end_date,
            localState.recurrence_onDay,
            Helper.convertNumberToWeekDay(localState.recurrence_weekdays),
            localState.recurrence_month_type,
            localState.recurrence_on_month_week,
            localState.recurrence_on_month_week_day,
        )
        setRecurrenceSelectToolTip(localState.recurrence_type !== Constants.recurrenceType.NONE ? label : '');

        switch (localState.recurrence_type) {
            case Constants.recurrenceType.MONTHLY:
                setRecMonthlyLabel(label);
                break;
            case Constants.recurrenceType.WEEKLY:
                setRecWeeklyLabel(label);
                break;
            case Constants.recurrenceType.WEEKDAY:
                setRecWeekDAYLabel(label);
                break;
            case Constants.recurrenceType.DAILY:
                setRecDailyLabel(label);
                break;
            default:
                return;
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the popup closing and revert the data of redux form back to its value before open
     * -----------------------------------------------------------------------------------------------------------------
     */
    const handleClosePopup = () => {
        setRecModelMode(state.recurrence_type);
        setRecModalOpen(false);
        initialize({
            ...state,
            title: event_Name,
            join_code: customUrl,
            rec_start_date: state.date,
            rec_interval: state.recurrence_interval,
        });
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used handling event recurrence end data
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data
     * @param {String} data.startDate Event start date linked with event actual start date
     * @param {String} data.endDate Recurrence end date
     * @param {Number} data.recInterval Interval of recurring with respect to type
     * @param {Number} data.recType Type of the recurring
     * @param {Number} data.weekDays Selected weekdays if recurring is weekly or weekdays type in number
     * @param {Number} data.onDayOfMonth The value for the month data on which the event will recur
     * @param {Number} data.monthType Type of the week to indicate its on day or on specific week number and day
     * @param {Number} data.onTheMonthWeek Value of the week selected for month to recur
     * @param {String} data.onTheMonthWeekDay Name of the week day to occur the event     * @returns
     */
    const handleSaveRecurrence = (data) => {
        setRecModelMode(data.recType);
        if (data.recType === Constants.recurrenceType.WEEKLY) {
            if (data.weekDays === Constants.recWeekDayBinary) {
                data.recType = Constants.recurrenceType.WEEKDAY;
            } else if (data.weekDays === 127) {
                data.recType = Constants.recurrenceType.DAILY;
            }
        }

        let newState = {
            ...state,
            date: data.startDate ? moment(data.startDate).format("YYYY-MM-DD") : state.date,
            recurrence_end_date: data.endDate ? moment(data.endDate).format("YYYY-MM-DD") : state.recurrence_end_date,
            recurrence_interval: data.recInterval ? data.recInterval : state.recurrence_interval,
            recurrence_type: data.recType ? data.recType : state.recurrence_type,
            recurrence_weekdays: data.weekDays ? data.weekDays : state.recurrence_weekdays,
            recurrence_onDay: data.onDayOfMonth ? data.onDayOfMonth : state.recurrence_onDay,
            recurrence_month_type: data.monthType ? data.monthType : state.recurrence_month_type,
            recurrence_on_month_week: data.onTheMonthWeek ? data.onTheMonthWeek : state.recurrence_on_month_week,
            recurrence_on_month_week_day: data.onTheMonthWeekDay ? data.onTheMonthWeekDay : state.recurrence_on_month_week_day,
        };
        let localState = newState;
        let label = Helper.prepareRecurrenceLine(
            localState.recurrence_interval,
            localState.recurrence_type,
            localState.recurrence_end_date,
            localState.recurrence_onDay,
            Helper.convertNumberToWeekDay(localState.recurrence_weekdays),
            localState.recurrence_month_type,
            localState.recurrence_on_month_week,
            localState.recurrence_on_month_week_day,
        )

        console.log('dddddddddddddddd on save', newState);

        setState(newState)

        updateRecSelectLabel(newState);

        setRecModelMode(data.recType ? data.recType : state.recurrence_type);
        setRecModalOpen(false);
        newState = {
            ...newState,
            title: event_Name,
            join_code: customUrl,
            rec_start_date: new moment(newState.date),
            rec_interval: newState.recurrence_interval,
        }
        initialize(newState);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To open the recurrence model
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Number} mode Recurrence model popup mode to find the type of recurrence e.g. daily, monthly, weekly
     */
    const openRecModal = (mode) => {
        setRecModelMode(mode)
        setRecModalOpen(true);
    }

    return (
        <LoadingContainer loading={loading}>
            <div className="SchedulindMainDiv">
                <form onSubmit={handleSubmit(submitForm)}>
                    <Grid container spacing={3}>
                        <Grid item lg={2}>
                            <p className="customPara">Event Name:</p>
                        </Grid>
                        <Grid item lg={4}>
                            <Field
                                name="title"
                                placeholder="Enter Event Name"
                                variant="outlined"
                                disabled={disabled}
                                className="ThemeInputTag"
                                component={renderTextField}
                                onChange={setEventName}
                                validate={[Validation.max70]}
                            />
                        </Grid>
                        {!showDes && (
                            <Grid item>
                                <Link href="" onClick={handleDesShow} className="AddDisTxt">
                                    {_.has(paramsData, ["event_uuid"]) ? "Edit" : "Add"}{" "}
                                    Description
                                </Link>
                            </Grid>
                        )}
                    </Grid>
                    {showDes && (
                        <Grid container spacing={3}>
                            <Grid item lg={2}>
                                <p className="customPara">Description:</p>
                            </Grid>
                            <Grid item lg={4}>
                                <TextareaAutosize
                                    name="description"
                                    minRows={3}
                                    onChange={handleChange}
                                    value={state.description}
                                    disabled={disabled}
                                    placeholder="Description"
                                    variant="outlined"
                                    className="ThemeInputTag DisTextArea"
                                    component={renderTextField}
                                />
                                {/* <Field
                        name="description"
                        placeholder="Description"
                        variant="outlined"
                        className="ThemeInputTag"
                        component={renderTextField}
                    /> */}
                            </Grid>
                            <Grid item>
                                <Link href="" onClick={handleDesShow} className="AddDisTxt">
                                    Hide
                                </Link>
                            </Grid>
                        </Grid>
                    )}
                    <Grid container spacing={3}>
                        <Grid item lg={2}>
                            <p className="customPara">Start Date :</p>
                        </Grid>
                        <Grid item lg={4}>
                            <div className="createEventDivTextMain">
                                <div className="SubDivTextMain-1">
                                    <Field
                                        name="date"
                                        disabled={disabled}
                                        value={state.date}
                                        onChange={(val) => {
                                            handleDateChange(val);
                                        }}
                                        variant="outlined"
                                        className="ThemeInputTag-2"
                                        component={renderDatePicker}
                                    />
                                </div>
                                <div className="SubDivTextMain-2">
                                    <Field
                                        name="start_time"
                                        type="time"
                                        disabled={disabled}
                                        onChange={(t) => handleTimeChange("start_time", t)}
                                        variant="outlined"
                                        className="ThemeInputTag-2"
                                        startH={startTimeMin.h}
                                        startM={startTimeMin.m}
                                        values={`${state.date} ${state.start_time}`}
                                        component={TimePickerComp}
                                    />
                                    <Field
                                        name="end_time"
                                        type="time"
                                        disabled={disabled}
                                        onChange={(t) => handleTimeChange("end_time", t)}
                                        variant="outlined"
                                        className="ThemeInputTag-2"
                                        values={`${state.date} ${state.end_time}`}
                                        startH={endTimeMin.h}
                                        startM={endTimeMin.m}
                                        component={TimePickerComp}
                                    />
                                </div>
                            </div>
                        </Grid>
                    </Grid>
                    <Grid container spacing={3}>
                        <Grid item lg={2}>
                            <p className="customPara">Event Type:</p>
                        </Grid>
                        <Grid item lg={4}>
                            <FormControl variant="outlined" className="SelectEventType">
                                <InputLabel id="demo-simple-select-outlined-label">
                                    Type
                                </InputLabel>
                                <Select
                                    labelId="demo-simple-select-outlined-label"
                                    label="Age"
                                    value={event_type}
                                    disabled={disabled}
                                    onChange={(e) => {
                                        setEventType(e.target.value);
                                    }}
                                >
                                    <MenuItem value={1}>Networking</MenuItem>
                                    <MenuItem value={2}>Content + Networking</MenuItem>
                                </Select>
                            </FormControl>
                        </Grid>
                    </Grid>
                    <Grid container spacing={3}>
                        <Grid item lg={2}>
                            <p className="customPara">Custom Url:</p>
                        </Grid>
                        <Grid item lg={4}>
                            <Field
                                name="join_code"
                                placeholder="Enter Custom Url"
                                variant="outlined"
                                disabled={disabled}
                                className="ThemeInputTag"
                                component={rendersmallTextField}
                                onChange={changCustomUrl}
                                isValidate={customUrlErrorMessage}
                                validate={customUrl.length > 0 && [Validation.min3, Validation.max20, Validation.alpha_names_hypn_space]}
                            />
                        </Grid>
                        <Grid item>
                            {customUrl.length < 20 && (
                                <p className="customPara eventDisTxtDynamic">
                                    {urlName}/j/{customUrl}
                                </p>
                            )}
                        </Grid>
                    </Grid>
                    <Grid container spacing={3}>
                        <Grid item lg={2}>
                            <p className="customPara">Recurrence:</p>
                        </Grid>
                        <Grid item lg={4}>
                            <FormControl variant="outlined" className="SelectEventType SelectRecurrenceType">
                                <InputLabel id="demo-simple-select-outlined-label">Recurrence</InputLabel>
                                <Tooltip arrow title={recurrenceSelectToolTip}>
                                    <Select
                                        labelId="demo-simple-select-outlined-label"
                                        label="Recurrence"
                                        value={recModelMode}
                                        disabled={disabled}
                                        onChange={handleRecurrenceChange}
                                        id="custom_date_label"
                                        onOpen={(e) => {
                                            setRecDailyLabel(`Daily`)
                                            setRecMonthlyLabel(`Monthly`)
                                            setRecWeeklyLabel(`Weekly`)
                                            setRecWeekDAYLabel(`Week Day`)
                                        }}
                                        onClose={e => {
                                            if (recModelMode !== Constants.recurrenceType.NONE) {
                                                updateRecSelectLabel();
                                            }
                                        }}
                                    >
                                        <MenuItem
                                            value={Constants.recurrenceType.NONE}
                                            onClick={() => setState({...state, recurrence_type: Constants.recurrenceType.NONE})}
                                        >
                                            Does Not Repeat
                                        </MenuItem>
                                        <MenuItem
                                            value={Constants.recurrenceType.DAILY}
                                            onClick={() => openRecModal(Constants.recurrenceType.DAILY)}
                                        >
                                            {recDailyLabel}
                                        </MenuItem>
                                        <MenuItem
                                            value={Constants.recurrenceType.WEEKLY}
                                            onClick={() => openRecModal(Constants.recurrenceType.WEEKLY)}
                                        >
                                            {recWeeklyLabel}
                                        </MenuItem>
                                        <MenuItem
                                            value={Constants.recurrenceType.MONTHLY}
                                            onClick={() => openRecModal(Constants.recurrenceType.MONTHLY)}
                                        >
                                            {recMonthlyLabel}
                                        </MenuItem>
                                        <MenuItem
                                            value={Constants.recurrenceType.WEEKDAY}
                                            onClick={() => openRecModal(Constants.recurrenceType.WEEKDAY)}
                                        >
                                            {recWeekDAYLabel}
                                        </MenuItem>
                                    </Select>

                                </Tooltip>
                            </FormControl>
                        </Grid>
                    </Grid>
                    {
                        recModalOpen && recModelMode !== Constants.recurrenceType.NONE
                        &&
                        <RecurrencePopup
                            startDate={state.date}
                            recInterval={state.recurrence_interval}
                            recType={recModelMode}
                            weekDays={state.recurrence_weekdays}
                            onDay={state.recurrence_onDay}
                            endDate={state.recurrence_end_date}
                            recurrence_month_type={state.recurrence_month_type}
                            recurrence_on_month_week={state.recurrence_on_month_week}
                            recurrence_on_month_week_day={state.recurrence_on_month_week_day}
                            disabled={disabled}
                            onSave={handleSaveRecurrence}
                            closePopup={handleClosePopup}

                        />
                    }


                    <Grid container spacing={3}>
                        <Grid item lg={2}>
                            <p className="customPara">Demo Users :{" "}
                                <Tooltip arrow title={t("add_demo_users")}><InfoOutlinedIcon /></Tooltip>
                            </p>
                        </Grid>
                        <Grid item className="DemoUserDiv">
                            <Checkbox
                                onChange={setDummy}
                                disabled={disabled}
                                value={is_dummy_event}
                                defaultChecked={is_dummy_event}
                                className="DummyUserCheck"
                                color="primary"
                                inputProps={{"aria-label": "secondary checkbox"}}
                            />

                            {/* <p className="customPara">This event is a simulation event and uses demo users.</p> */}
                        </Grid>
                    </Grid>
                    <Grid container spacing={3}>
                        <Grid item>
                            {!disabled && (
                                <p className="customPara eventDisTxtDynamic">
                                    {eventOccurLine}
                                </p>
                            )}
                        </Grid>
                    </Grid>
                    {event_type == 2 && (
                        <Button
                            type="submit"
                            variant="contained"
                            disabled={disabled}
                            color="primary"
                            selected={1 === selectedIndex}
                            // onClick={handleClick}
                        >
                            {_.has(paramsData, ["event_uuid"])
                                ? "Edit Event"
                                : "Save as Draft"}
                        </Button>
                    )}
                    {event_type == 1 && (
                        <ButtonGroup
                            variant="contained"
                            ref={anchorRef}
                            aria-label="split button"
                        >
                            <Button type="submit" color="primary" onClick={handleClick}>
                                {event_type == 1 && selectedIndex === 0
                                    ? _.has(paramsData, ["event_uuid"])
                                        ? "Edit Event"
                                        : "Create Event"
                                    : selectedIndex === 1
                                        ? "Save As Draft"
                                        : ""}
                                {event_type == 2 && selectedIndex === 0 ? "Save As Draft" : ""}
                            </Button>
                            <Button
                                size="small"
                                aria-controls={open ? "split-button-menu" : undefined}
                                aria-expanded={open ? "true" : undefined}
                                aria-label="select merge strategy"
                                aria-haspopup="menu"
                                onClick={handleToggle}
                                color="primary"
                            >
                                <ArrowDropDownIcon />
                            </Button>
                        </ButtonGroup>
                    )}
                    <Popper
                        open={open}
                        anchorEl={anchorRef.current}
                        role={undefined}
                        transition
                        disablePortal
                    >
                        {({TransitionProps, placement}) => (
                            <Grow
                                {...TransitionProps}
                                style={{
                                    transformOrigin:
                                        placement === "bottom" ? "center top" : "center bottom",
                                }}
                            >
                                <Paper>
                                    <ClickAwayListener onClickAway={handleClose}>
                                        <MenuList id="split-button-menu">
                                            {/* {options.map((option, index) => (
                                                <MenuItem
                                                    key={option}
                                                    disabled={index === 2}
                                                    selected={index === selectedIndex}
                                                    onClick={(event) => handleMenuItemClick(event, index)}
                                                >
                                                    {option}
                                                </MenuItem>
                                            ))} */}
                                            {event_type == 1 && (
                                                <>
                                                    <MenuItem
                                                        color="primary"
                                                        selected={0 === selectedIndex}
                                                        type="submit"
                                                        key="one"
                                                        value={1}
                                                        onClick={(event) => handleMenuItemClick(event, 0)}
                                                    >
                                                        {_.has(paramsData, ["event_uuid"])
                                                            ? "Edit"
                                                            : "Create"}{" "}
                                                        Event
                                                    </MenuItem>
                                                    <MenuItem
                                                        color="primary"
                                                        selected={1 === selectedIndex}
                                                        type="submit"
                                                        key="two"
                                                        value={2}
                                                        onClick={(event) => handleMenuItemClick(event, 1)}
                                                    >
                                                        {" "}
                                                        Save as Draft
                                                    </MenuItem>
                                                </>
                                            )}
                                            {/* {event_type == 2 && <>
                                                <MenuItem  color="primary" selected={1 === selectedIndex}type="submit" key="two" value={2} onClick={(event) => handleMenuItemClick(event, 1)}> Save as Draft</MenuItem>
                                            </>} */}
                                        </MenuList>
                                    </ClickAwayListener>
                                </Paper>
                            </Grow>
                        )}
                    </Popper>{" "}
                    {_.has(paramsData, ["event_uuid"]) && (
                        <Button
                            variant="contained"
                            color="primary"
                            onClick={props.handleNext}
                        >
                            Next
                        </Button>
                    )}
                </form>
            </div>
        </LoadingContainer>
    );
};

export default reduxForm({
    form: "MaterialUiForm", // a unique identifier for this form
    validate,
    keepDirtyOnReinitialize: true,
},)(Scheduling);
