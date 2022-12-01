import React from 'react';
import TextField from '@mui/material/TextField';
import {Field} from 'redux-form';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component structure for a input field to render input box for space capacity input box.
 *  This will take data(from parameter where it called) which is necessary to render relative number field.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Input} input Actual input box with its default properties
 * @param {String} value Value of the input box
 * @param {Object} inputProps Object that contains Input props for input box if any.
 * @param {String} label Label of text field
 * @param {String} defaultValue Default value of input box
 * @param {Object} invalid Object that contain message of "Enter value is invalid" in key value pair
 * @param {Object} touched To indicate if the input box is touched or not
 * @param {Object} error Error message object for input box
 * @param {Object} custom Custom props (if any) to render on input box
 * @returns {JSX.Element}
 */
const renderNumberField = ({
    input, inputProps, label, defaultValue, meta: {invalid, touched, error}, ...custom
}) => {
    return (
        <React.Fragment>
            <TextField
                name={input.name}
                value={input.value}
                type="number"
                errorText={touched && error}
                inputProps={inputProps}
                {...input}
                {...custom}
                error={touched && error && invalid}
                helperText={touched && error}
            />
            {/*{touched && error && <span className={"text-danger"}>{error}</span>}*/}
        </React.Fragment>
    );
};

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
const NumberInput = (props) => {

    return (
        <Field
            name={props.name}
            placeholder={props.placeholder}
            disabled={props.disabled}
            variant="filled"
            inputVariant="filled"
            className="ThemeInputTag"
            validate={props.validate}
            component={renderNumberField}
            id={props.id}
            size={"small"}
            inputProps={props.inputProps}
        />
    )
}

export default NumberInput;