import React from 'react'
import {Field} from 'redux-form';
import {LocalizationProvider} from '@mui/x-date-pickers/LocalizationProvider';
import {AdapterMoment} from "@mui/x-date-pickers/AdapterMoment";
import {DatePicker} from '@mui/x-date-pickers/DatePicker';
import {TextField} from "@mui/material";

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
            <LocalizationProvider dateAdapter={AdapterMoment}>
                <DatePicker
                    value={input.value}
                    onChange={(newValue) => {
                        input.onChange(newValue);
                    }}
                    inputFormat={"YYYY-MM-DD"}
                    className="ThemeInputTag"
                    renderInput={(params) => {
                        return <TextField
                            {...params}
                            className="ThemeInputTag"
                            variant="filled"
                            error={error}
                            helperText={error}
                        />
                    }}
                    onBlur={(e) => {
                        input.onBlur(e);
                        console.log('ddddddddddd date picker', e);
                    }}
                    {...input}
                    {...custom}
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
const DateInput = (props) => {

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

export default DateInput;
