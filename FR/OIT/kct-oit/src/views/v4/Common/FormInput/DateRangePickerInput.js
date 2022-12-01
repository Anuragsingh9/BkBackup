import React from 'react'
import {Field} from 'redux-form';
import {AdapterMoment} from "@mui/x-date-pickers/AdapterMoment";
import {LocalizationProvider} from '@mui/x-date-pickers-pro';
import {Box, TextField} from "@mui/material";
import {DateRangePicker} from "@mui/x-date-pickers-pro/DateRangePicker";

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
    {input, id, label, validate, defaultValue, meta: {touched, error}, ...custom},
) => {
    return (
        <React.Fragment>
            <LocalizationProvider
                dateAdapter={AdapterMoment}
                localeText={{start: 'Check-in', end: 'Check-out'}}
            >
                <DateRangePicker
                    value={input.value || [null, null]}
                    onChange={input.onChange}
                    renderInput={(startProps, endProps) => (
                        <React.Fragment>
                            <TextField {...startProps} />
                            <Box sx={{mx: 2}}> to </Box>
                            <TextField {...endProps} />
                        </React.Fragment>
                    )}
                />
            </LocalizationProvider>
        </React.Fragment>
    );
}


/**
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is common component for input type text.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Props passed from parent component
 * @param {String} props.value Entered value by user
 * @param {Function} props.onChange To update the entered value by user
 * @return {JSX.Element}
 * @constructor
 */
const DateRangePickerInput = (props) => {

    return (
        <Field
            name={props.name}
            disabled={props.disabled}
            validate={props.validate}
            variant="filled"
            id={props.id}
            className="ThemeInputTag-2"
            component={renderDatePicker}
            onChange={props.onChange}
            disablePast
        />
    )
}

export default DateRangePickerInput;
