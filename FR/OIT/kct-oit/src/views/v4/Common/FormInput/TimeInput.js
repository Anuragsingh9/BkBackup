import React, {useState} from 'react'
import MuiPickersUtilsProvider from '@material-ui/pickers/MuiPickersUtilsProvider';
import DateFnsUtils from "@date-io/moment";
import {KeyboardDatePicker} from '@material-ui/pickers/DatePicker';
import {Field} from 'redux-form';
import TimePickerComp from '../../../Common/TimePicker';

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
const renderTimePicker = (
    {input, id, label, defaultValue, meta: {touched, error}, ...custom},
) => {
    console.log('input', input)
    return (
        <React.Fragment>
            <MuiPickersUtilsProvider utils={DateFnsUtils}>
                <KeyboardDatePicker
                    name={input.name}
                    variant="filled"
                    size="small"
                    id={id}
                    inputVariant="filled"
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
const TimeInput = (props) => {
    const [value, setValue] = useState('');
    const handleTimeChange = (e) => {
        setValue(e);
        // props.onChange(e);
    }
    return (
        <Field
            name={props.name}
            type="time"
            disabled={props.disabled}
            onChange={(t) => handleTimeChange("start_time", t)}
            variant="filled"
            id={props.id}
            className="ThemeInputTag-2"
            startH={props.startH} //const [startTimeMin, setStartTimeMin] = useState({h: 0, m: 0});
            startM={props.startM}
            values={value} //`${state.date} ${state.start_time}`
            component={TimePickerComp}
        />
    )
}

export default TimeInput;
