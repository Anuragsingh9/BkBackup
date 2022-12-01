import React from 'react';
import TextField from '@mui/material/TextField';
import {Field} from 'redux-form';

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
const renderTextAreaField = (
    {input, placeholder, id, rows, disabled, label, defaultValue, meta: {touched, error}, ...custom},
) => {
    return (
        <React.Fragment>
            <TextField
                name={input.name}
                value={input.value}
                disabled={disabled}
                id={id}
                placeholder={placeholder}
                size="small"
                className="full_width textAreaV4"
                variant="filled"
                multiline
                rows={rows}
                onChange={input.onChange}
                errorText={touched && error}
                error={touched && error}
                {...input}
            />
            {touched && error && <span className={'text-danger'}>{error}</span>}
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
const TextAreaInput = (props) => {
    return (
        <Field
            name={props.name}
            id={props.id}
            placeholder={props.placeholder}
            disabled={props.disabled}
            size="small"
            type="text"
            variant="filled"
            inputVariant="filled"
            rows={props.row || 5}
            className="ThemeInputTag "
            validate={props.validation}
            component={renderTextAreaField}
        />
    )
}

export default TextAreaInput;