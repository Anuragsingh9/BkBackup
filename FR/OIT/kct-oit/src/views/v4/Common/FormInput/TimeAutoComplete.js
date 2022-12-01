import * as React from 'react';
import {useEffect, useState} from 'react';
import {Autocomplete} from "@mui/lab";
import {Field} from 'redux-form';
import {createFilterOptions, TextField} from '@mui/material';
import moment from "moment-timezone";
import Constants from '../../../../Constants';

const filter = createFilterOptions();

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is common redux form component which is using autocomplete component to render time dropdown in
 * event creation form.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @callback TimeAutoComplete~renderAutoComplete
 * @param {Input} input Actual input box with its default properties
 * @param {String} placeholder Function is used change the state
 * @param {String} label Label of text field
 * @param {Array} optionsVal To indicate if the input box is touched or not
 * @param {Object} touched To indicate if the input box is touched or not
 * @param {Object} error Error message from input box
 * @param {Object} custom Custom props of autoSelect input box
 * @returns {JSX.Element}
 */
const renderAutoComplete = (
    {
        input,
        placeholder,
        label,
        optionsVal,
        minimumTime,
        meta: {touched, error},
        ...custom
    },
) => {

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This is a onChange handler method used to pass input values from autocomplete component to redux-form
     * component for managing it's state.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e JavaScript event object
     * @param {moment} data input value received from autocomplete component to redux-form component.
     */
    const onchangeHandler = (e, data) => {
        if (data) {
            input.onChange(data)
        }
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This is a getLabel handler method used to pass label values from autocomplete component to redux-form
     * component for managing it's state.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} option JavaScript momemt(library to manage date&time) object
     */
    const getLabel = (option) => {
        if (typeof option === 'string') {
            option = new moment(option, "hh:mm a");
        }
        return option instanceof moment ? option.format('hh:mm A') : "";
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To allow the user to choose the custom time if entered time is in valid format
     * Currently allowed formats are defined in Constants.allowedTimeFormat
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param options
     * @param params
     * @returns {unknown[]}
     */
    const filterOptions = (options, params) => {
        let filtered = filter(options, params);

        const {inputValue} = params;

        if (inputValue !== '') {
            // checking if the entered time matched our defined format or not
            let m = moment(inputValue.trim(), Constants.allowedTimeFormats, true)
            if (m.isValid()) {
                filtered = filter([], params);
                filtered.push(m);
            } else {
                filtered = filter([], params);
                optionsVal.forEach(option => filtered.push(option));
            }
        }
        return filtered;
    }

    const compareTime = (time1, time2) => {
        return time1.valueOf() < time2.valueOf();
    }

    const onTextBlurHandler = (params) => {
        if (!moment(params.inputProps.value, Constants.allowedTimeFormats, true).isValid()) {
            input.onChange(moment())
        }
    }

    return (
        <React.Fragment>
            <Autocomplete
                // onBlur={input.onBlur}
                name={input.name}
                value={input.value}
                options={optionsVal}
                disabled={custom.disabled}
                defaultValue={optionsVal[0]}
                autoHighlight
                getOptionLabel={getLabel}
                getOptionDisabled={option => {
                    return minimumTime && compareTime(option, minimumTime);
                }}
                renderInput={
                    (params) => {
                        return <TextField
                            {...params}
                            variant="filled"
                            error={error}
                            helperText={error}
                            onBlur={() => onTextBlurHandler(params)}
                        />
                    }
                }
                variant="filled"
                className='timeSelectDesign'
                inputVariant="filled"
                onChange={onchangeHandler}
                freeSolo={custom.freeSolo}
                filterOptions={filterOptions}
            />
        </React.Fragment>
    );
}

/**
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is common component for Time autoselect dropdown.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Props passed from parent component
 * @param {String} props.name Name received from parameter
 * @param {Function} props.placeholder Placeholder text received from parameter
 * @return {JSX.Element}
 * @constructor
 */
const TimeAutoComplete = (props) => {

    // Array of object where each object holds time value and their label(in 'hh:mm A' format) data.
    const [optionsVal, setOptionsVal] = useState([]);

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @decription This method is developed to render time options in event creation > select time dropdown component.
     * This method will start from initializing time using moment.js.
     * ---------------------------------------------------------------------------------------------------------------------
     *
     */
    const calculateTimeOptions = () => {
        let time = new moment({hours: 0, minutes: 0, seconds: 0});
        if (props.minimum) {
            // if (props.checkTimezone && props.minimum.format("YYYY-MM-DD") === moment().format("YYYY-MM-DD")) {
            //     time.tz('Europe/Paris');
            //     props.minimum.tz('Europe/Paris');
            // }
            // time.set({date: props.minimum.date(), month: props.minimum.month(), year: props.minimum.year()});
            // console.log('ddddddddddddd minimum',props.minimum.format("YYYY-MM-DD hh:mm:ss a"), time.format("YYYY-MM-DD hh:mm:ss a") )
        }
        let j = 0;
        let options = [];
        while (j < 48) { // Here 48 represents totel interval of 30min in a single day, 24h = 24*2 = 48(30 min interval)
            // console.log('ddddddddddd props.minimum', time.clone().valueOf(), time.clone().tz("Europe/Paris").valueOf());
            // if (!props.minimum || time.clone().valueOf() > props.minimum?.valueOf()) {
            options.push(time.clone())
            // }

            if (time.hours() === 23 && time.minutes() === 30) {
                break;
            }
            time.add(Constants.timePickerInterval, 'm')
            j++
        }
        setOptionsVal(options);
    }

    useEffect(() => {
        calculateTimeOptions();
    }, [props.minimum]);

    return (
        <Field
            name={props.name}
            placeholder={props.placeholder}
            variant="filled"
            inputVariant="filled"
            className="ThemeInputTag"
            component={renderAutoComplete}
            validate={props.validate}
            disabled={props.disabled}
            onChange={props.onChange}
            freeSolo={true}
            optionsVal={optionsVal}
            minimumTime={props.minimum}
        />
    )
}
export default TimeAutoComplete
