import React, {useEffect, useState} from 'react';
import {Button, Grid} from '@material-ui/core';
import _ from 'lodash';
import moment from "moment-timezone";
import {useDispatch, useSelector} from 'react-redux';
import {KeyboardDatePicker, MuiPickersUtilsProvider, TimePicker} from "@material-ui/pickers";
import DateFnsUtils from '@date-io/moment';
import {Field, reduxForm} from 'redux-form';
import './invitationPlan.css';
import RightArrowIcon from '../../Svg/RightArrowIcon';
import Constants from "../../../Constants";
import TimePickerComp from "../../Common/TimePicker";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This  method is for validation of input values like  'startDate', 'endDate', 'startTime',
 * 'endTime', input fields values if values are not valid then it return error massage for that value.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} values Validate the registration window values
 * @param {String} values.startDate Registration start date
 * @param {String} values.endDate Registration end date
 * @param {String} values.startTime Registration start time
 * @param {String} values.endTime Registration end time
 */
const validate = (values) => {
    const errors = {};
    const requiredFields = [

        'startDate',
        'endDate',
        'startTime',
        'endTime',
    ];
    requiredFields.forEach(field => {
        if (!values[field]) {
            errors[field] = 'Required';
        }
    });
    return errors;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component for date picker in redux form and returns the datepicker component in UI
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Input} input Actual input box with its default properties
 * @param {String} values Value of the input box
 * @param {Boolean} disabled Date picker component is disabled or not
 * @param {String} label Label of date
 * @param {String} defaultValue Default value of input box
 * @param {Object} touched To indicate if the input box is touched or not
 * @param {Object} error Error message from input box
 * @returns {JSX.Element}
 */
const renderDatePicker = (
    {input, values, disabled, label, defaultValue, meta: {touched, error}, ...custom},
) => {
    return (
        <React.Fragment>
            <MuiPickersUtilsProvider utils={DateFnsUtils}>
                <KeyboardDatePicker

                    name={input.name}
                    variant="inline"
                    size="small"
                    inputVariant="outlined"
                    defaultValue={`${values}`}
                    onChange={input.onChange}
                    errorText={touched && error}
                    error={touched && error}
                    disablePast={true}
                    value={values}
                    disabled={disabled}
                    selected={moment(values)}
                    format={'YYYY-MM-DD'}
                    {...input}

                />
            </MuiPickersUtilsProvider>
            {touched && error && <span className={'text-danger'}>{error}</span>}
        </React.Fragment>
    )
}


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is a child component in EventInfo.js and renders the registration window on
 *  invitation plan page which manage the time and date of open and close registration of event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props
 * @param {String} props.endDate Event end date
 * @param {String} props.endTime Event end time
 * @param {Number} props.eventStatus Event status
 * @param {String} props.startDate Event start date
 * @param {String} props.startTime Event start time
 * @param {Function} props.getTime Function to get current time
 * @param {Function} props.updateEventData Function to update event data
 * @returns {JSX.Element}
 * @constructor
 */
const RegistrationWindow = (props) => {
    const {handleSubmit, pristine, reset, submitting, initialize} = props;
    const [time, setTime] = useState({
        startDate: "2000-12-11",
        endDate: "",
        startTime: '10:00:00',
        endTime: '10:00:00'
    })
    const [eventId, setEventId] = useState();
    const [isRegWindowDisabled, setIsRegWindowDisabled] = useState(false)
    const event_data = useSelector((data) => data.Auth.eventDetailsData)

    useEffect(() => {
        if (_.has(event_data, ['event_uuid'])) {
            setEventId(event_data.event_uuid)
        }
        if (props) {
            setTime({
                startDate: props.startDate ? props.startDate : '',
                endDate: props.endDate ? props.endDate : '',
                startTime: props.startTime ? props.startTime : '',
                endTime: props.endTime ? props.endTime : "13:00:00"
            })
            // }
            initialize(props)
        }
        setIsRegWindowDisabled(props.eventStatus === Constants.eventStatusDraft);

    }, [props.endTime, props.eventStatus, props.startDate])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle date value from input fields of start date and updates state.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} val  Javascript Date object
     * @example Moment {_isAMomentObject: true, _i: '2022-07-11', _f: 'YYYY-MM-DD', _isUTC: false, _pf: {…}, …}
     */
    const handleStartDate = (val) => {

        if (val) {
            setTime({
                ...time,
                startDate: val.format('yyyy-MM-DD')
            })
            props.getTime({...time, startDate: val.format('yyyy-MM-DD')})
        }

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle date value from input fields of end date and update state.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} val  Javascript Date object
     * @example Moment {_isAMomentObject: true, _i: '2022-07-11', _f: 'YYYY-MM-DD', _isUTC: false, _pf: {…}, …}
     */
    const handleEndDate = (val) => {
        if (val) {
            setTime({
                ...time,
                endDate: val.format('yyyy-MM-DD')
            })
            props.getTime({...time, endDate: val.format('yyyy-MM-DD')})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle start Time value of start time input field and update state.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} date Time object contain hour and minute values. ex - {"h": "15","m": "10"}
     */
    const handleStartTime = (date) => {
        if (date) {
            let newTime = `${date.h}:${date.m}:00`;
            setTime({
                ...time,
                startTime: newTime,
            })
            props.getTime({...time, startTime: newTime})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle end time value of end time input field and update state.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} date val  Javascript Date object
     * @example Moment {_isAMomentObject: true, _i: '2022-07-11', _f: 'YYYY-MM-DD', _isUTC: false, _pf: {…}, …}
     */
    const handleEndTime = (date) => {
        if (date) {
            let newTime = `${date.h}:${date.m}:00`;
            setTime({
                ...time,
                endTime: newTime
            })
            props.getTime({...time, endTime: newTime})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is to sends data of registration window on server by using API call and here
     * it sends false value to update the registration time of event and it invokes updateEventData() method in parent
     * if api response is successful then it updates  state regarding to registration window other  wise returns
     * wise returns error massage
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const updateTimeAndOpenReg = () => {
        const data = {
            "reg_start_date": time.startDate,
            "reg_start_time": time.startTime,
            "reg_end_date": time.endDate,
            "reg_end_time": time.endTime,
            "is_reg_open": 1,
            "reg_time_updated": false,
        }
        props.updateEventData(data);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is for sending data of registration window on server by using API call and here
     * it sends true value to update the registration of event and it invokes updateEventData() method in parent if api
     * response is successful then it updates state regarding to registration window other wise returns error massage.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const updatRegTime = () => {
        const data = {
            "reg_start_date": time.startDate,
            "reg_start_time": time.startTime,
            "reg_end_date": time.endDate,
            "reg_end_time": time.endTime,
            "is_reg_open": 1,
            "reg_time_updated": true,
        }
        props.updateEventData(data);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to close the registration window and send data of registration window in
     * parent component using props callback function and updates the data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const onCloseRegistration = () => {
        const data = {
            "reg_start_date": time.startDate,
            "reg_start_time": time.startTime,
            "reg_end_date": time.endDate,
            "reg_end_time": time.endTime,
            "is_reg_open": 2,
        }
        props.updateEventData(data);
    }

    return (
        <>
            <Grid container className="registrationWindow">
                <Grid lg={5} className="startTimeWrap">
                    <Field
                        name="startDate"
                        disabled={isRegWindowDisabled}
                        size="small"
                        // value={time.start_time}
                        onChange={handleStartDate}
                        variant="inline"
                        className="DateIndicator"
                        component={renderDatePicker}
                        values={`${time.startDate}`}


                    />
                    <Field
                        name="startTime"
                        size="small"
                        type="time"
                        disabled={isRegWindowDisabled}
                        onChange={(val) => handleStartTime(val)}
                        variant="inline"
                        className="ThemeInputTag-2"
                        startH={0}
                        startM={0}
                        values={`${time.startDate} ${time.startTime}`}
                        component={TimePickerComp}
                    />

                </Grid>
                <Grid className="rightArrowIcon">
                    <RightArrowIcon />
                </Grid>
                <Grid lg={5} className="endTimeWrap">

                    <Field
                        name="endDate"
                        disabled={isRegWindowDisabled}
                        size="small"
                        // value={time.start_time}
                        onChange={handleEndDate}
                        variant="inline"
                        className="DateIndicator"
                        component={renderDatePicker}
                        values={`${time.endDate}`}
                        // inputProps={{value : time.start_time} }

                    />
                    <Field
                        name="endTime"
                        size="small"
                        disabled={isRegWindowDisabled}
                        onChange={(val) => handleEndTime(val)}
                        variant="inline"
                        className="TimeIndicator"
                        startH={0}
                        startM={0}
                        inputVariant="outlined"
                        values={`${time.endDate} ${time.endTime}`}
                        component={TimePickerComp}
                    />

                </Grid>

                {/* Event is live, if registration open show update time and close registration button */}
                {(props.eventStatus === Constants.eventStatusLive && props.isRegOpen === 1) &&
                <div>
                    <Button
                        type="submit"
                        disabled={isRegWindowDisabled}
                        color="primary"
                        variant="contained"
                        onClick={updatRegTime}
                    >
                        Update
                    </Button>
                </div>
                }
            </Grid>
            {(props.eventStatus === Constants.eventStatusLive && props.isRegOpen === 1) &&
            <>
                <br />
                <Grid xs={12}>
                    <Button
                        type="submit"
                        disabled={isRegWindowDisabled}
                        color="primary"
                        variant="contained"
                        onClick={onCloseRegistration}
                    >
                        Close Registration
                    </Button>
                </Grid>
            </>
            }

            {/* This button visible in two case
                    1. Either event is not launched, i.e. status is draft -> show as disabled
                    2. Event is live and not open for registration
                 */}
            {(
                (props.eventStatus === Constants.eventStatusDraft)
                || (props.eventStatus === Constants.eventStatusLive && props.isRegOpen === 0 || props.isRegOpen === 2))
            &&
            <>
                <br />
                <Grid xs={12}>
                    <Button
                        type="button"
                        disabled={props.eventStatus === Constants.eventStatusDraft}
                        color="primary"
                        variant="contained"
                        onClick={updateTimeAndOpenReg}
                    >
                        Open Registration
                    </Button>
                </Grid>
            </>
            }
        </>
    )
}

export default reduxForm({
    form: 'regMaterialUiForm', // a unique identifier for this form
    validate,
    keepDirtyOnReinitialize: true,
})(RegistrationWindow);
