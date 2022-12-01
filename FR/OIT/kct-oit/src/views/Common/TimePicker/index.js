import React, {useEffect, useState} from 'react';
import moment from "moment-timezone";
import Helper from '../../../Helper';
import {useAlert} from 'react-alert';
import {MuiPickersUtilsProvider} from "@material-ui/pickers";
import DateFnsUtils from "@date-io/moment";
import {FormControl, Grid, InputLabel, MenuItem, Select} from "@mui/material";
import _ from "lodash";
import Constants from "../../../Constants";
import './TimePicker.css';

/**
 * @deprecated
 */
const validateVal = (date) => {
    const time_zone = 'Europe/Paris';
    return Helper.getTimeUserTimeZone(time_zone, date._d);
}

/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render a timepicker element so that user can pick a time(hour/minute)
 * to create an event and a moment.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Input} input Actual input box with its default properties
 * @param {String} values Value of the input box
 * @param {Boolean} disabled Time picker disabled or not
 * @param {String} label Label of time picker
 * @param {String} defaultValue Default value of time picker
 * @param {Number} startH Start Time - Hour
 * @param {Number} endH End Time - Hour
 * @param {Number} startM Start Time - Minute
 * @param {Number} endM End Time - Minute
 * @param {Object} touched To indicate if the input box is touched or not
 * @param {Object} error Error message from input box
 * @param {Object} custom Custom number of input box
 * @returns {JSX.Element}
 */
const TimePickerComp = (
    {
        input, values, disabled, label, defaultValue, startH, endH,
        startM, endM, meta: {touched, error}, ...custom
    },
) => {
    const alert = useAlert();
    const [selectedHour, setSelectedHour] = useState('07');
    const [selectedMinute, setSelectedMinute] = useState('00');
    const [availableHours, setAvailableHours] = useState([]);
    const [availableMinutes, setAvailableMinutes] = useState([]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method takes value of time(hour/minute) and then convert them into 2 digit values.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} v Time in hours
     * @returns {string}
     */
    const timeTo2Digit = (v) => {
        console.log('v', v)
        return ('0' + v).slice(-2);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to render hour range in the timepicker dropdown in the form of 2 digit and set
     * '00' for initial state(when no value is selected by user in hour dropdown) of timepicker  component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const setHoursRange = () => {
        let h = [];

        startH = 0;
        endH = 24;
        for (let i = startH; i <= endH && i < 24; i++) {
            h.push(('0' + i).slice(-2));
        }
        if (h.find(hh => hh === selectedHour) === undefined) {
            setSelectedHour(h.length > 0 ? h[0] : '00');
        }
        setAvailableHours(h);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description - This method is used to render minute range in the timepicker dropdown in the form of 2 digit and
     * set '00' for initial state(when no value is selected by user in minute dropdown) of timepicker component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const setMinutesRange = () => {
        let m = [];
        if (!startM) {
            startM = 0;
        } else {
            startM = startM + (Constants.tPicker_minuteGap - startM % Constants.tPicker_minuteGap);
        }
        if (!endM) {
            endM = 55;
        }
        startM = 0;
        endM = 55;
        for (let i = startM; i <= endM && i <= 60; i += 10) {
            m.push(('0' + i).slice(-2));
        }
        if (m.find(hh => hh === selectedMinute) === undefined) {
            setSelectedMinute(m.length > 0 ? m[0] : '00');
        }
        if (!_.isEmpty(m)) {
            setAvailableMinutes(m);
        }
    }

    useEffect(() => {
        let value = null;
        if (!_.isEmpty(values.trim())) {
            value = moment(values);
        } else {
            value = moment();
        }
        if (value.hour()) {
            // setting hours and minute value,
            setSelectedHour(timeTo2Digit(value.hour()));
            setSelectedMinute(
                timeTo2Digit(
                    convertTo10Multiple(value.minute())
                )
            );
        }

        setHoursRange();
        setMinutesRange();
    }, [values, startH, startM]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description  This method convert the input time value in multiple of 10 and returns the result
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} value Used for time
     */
    const convertTo10Multiple = (value) => {
        return value
            // if there is remainder after 10 module then make it next 10 multiple
            + (value % 10 !== 0 ? (10 - value % 10) : 0);
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is for handling input change hour values and update latest value in a
     * state(setSelectedHour).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const handleHourChange = (e) => {
        setSelectedHour(e.target.value);
        input.onChange({
            h: ('0' + e.target.value).slice(-2),
            m: ('0' + selectedMinute).slice(-2)
        })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description - this method is for handling input change minutes values and update latest value in a
     * state(setSelectedMinute)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const handleMinuteChange = (e) => {
        setSelectedMinute(e.target.value);
        input.onChange({
            h: ('0' + selectedHour).slice(-2),
            m: ('0' + e.target.value).slice(-2),
        })
    }


    return (
        <React.Fragment>
            <MuiPickersUtilsProvider utils={DateFnsUtils}>

                <Grid container spacing={2} xs={12} md={12} className="common_TimePicker_div">
                    <Grid item xs={5} md={5} className="startTime_subDiv">
                        <FormControl variant="outlined" className="" size={"small"}>
                            <InputLabel id="demo-simple-select-outlined-label">HH</InputLabel>
                            <Select
                                labelId="demo-simple-select-outlined-label"
                                label="Age"
                                value={selectedHour}
                                disabled={disabled}
                                onChange={handleHourChange}
                            >
                                {availableHours.map(time => {
                                    return <MenuItem value={time}>{time}</MenuItem>
                                })}
                            </Select>
                        </FormControl>

                    </Grid>
                    {/* <Grid item xs={1} className="schedule_arrow_div">
                       <RightArrowIcon className="right_arr"/>
                    </Grid> */}
                    <Grid item xs={5} md={5} className="endTime_subDiv">
                        <FormControl variant="outlined" className="" size={"small"}>
                            <InputLabel id="demo-simple-select-outlined-label">MM</InputLabel>
                            <Select
                                labelId="demo-simple-select-outlined-label"
                                label="Age"
                                value={selectedMinute}
                                disabled={disabled}
                                onChange={handleMinuteChange}
                                variant={"outlined"}
                            >
                                {availableMinutes.map(time => {
                                    return <MenuItem value={time}>{time}</MenuItem>
                                })}
                            </Select>
                        </FormControl>
                    </Grid>
                </Grid>
                {<span className={"text-danger"}>{error}</span>}
            </MuiPickersUtilsProvider>
        </React.Fragment>
    )
}


export default TimePickerComp;
