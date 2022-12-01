import React from 'react';
import TextField from '@mui/material/TextField';
import {Field} from 'redux-form';
// TODO GOURAV DOCUMENTATION

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is common component for input field for inputs
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Input} input Actual input box with its default properties
 * @param {Function} onChange Function is used change the state
 * @param {Boolean} disabled Name disabled or not
 * @param {String} data Text data
 * @param {String} label Label of text field
 * @param {String} defaultValue Default value of input box
 * @param {Object} touched To indicate if the input box is touched or not
 * @param {Object} error Error message from input box
 * @param {Object} custom Custom number of input box
 * @returns {JSX.Element}
 */
const renderTextField = (
    {
        input,
        placeholder,
        label,
        defaultValue,
        meta: {touched, error},
        ...custom
    },
) => {
    return (
        <React.Fragment>
            <TextField
                name={input.name}
                value={input.value}
                onChange={input.onChange}
                size="small"
                className="full_width"
                placeholder={placeholder}
                variant="filled"
                {...custom}
                {...input}
                helperText={touched && error}
                error={touched && error}
            />
        </React.Fragment>
    );
}

/**
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is common component for input type text.
 *   ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Props passed from parent component
 * @param {String} props.value Entered value by user
 * @param {Function} props.onChange To update the entered value by user
 * @return {JSX.Element}
 * @constructor
 */
const TextInput = (props) => {
    return (
        <Field
            name={props.name}
            disabled={props.disabled || false}
            placeholder={props.placeholder}
            variant="filled"
            inputVariant="filled"
            className="ThemeInputTag"
            validate={props.validate}
            label={props.placeholder}
            component={renderTextField}
            onChange={props.onChange}
            isValidate={props.isValidate}
            {...props}
        />
    )
}

export default TextInput;