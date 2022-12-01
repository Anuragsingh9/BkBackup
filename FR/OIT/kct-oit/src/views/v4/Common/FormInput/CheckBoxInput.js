import React from 'react'
import MuiPickersUtilsProvider from '@material-ui/pickers/MuiPickersUtilsProvider';
import DateFnsUtils from "@date-io/moment";
import Checkbox from '@mui/material/Checkbox';
import {Field} from 'redux-form';

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
const renderCheckBox = (
    {input, id, label, defaultValue, meta: {touched, error}, ...custom},
) => {

    return (
        <React.Fragment>
                <Checkbox
                    name={input.name}
                    // variant="filled"
                    id={id}
                    size="small"
                    inputVariant="outlined"
                    checked={input.value}
                    {...input}
                    {...custom}
                />
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
const CheckBoxInput = (props) => {

    return (
        <Field
            name={props.name}
            disabled={props.disabled}
            variant="outlined"
            id={props.id}
            className="ThemeInputTag-2"
            component={renderCheckBox}
            icon={props.icon}
            checkedIcon={props.checkedIcon}
        />
    )
}

export default CheckBoxInput;
