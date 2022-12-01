import React, {useState} from 'react';
import {TextField} from '@material-ui/core';
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
const renderTextField = (
    {input, onChange, data, disabled, label, defaultValue, meta: {touched, error}, ...custom},
) => {
    return (
        <React.Fragment>
            <TextField
                name={input.name}
                value={data}
                disabled={disabled}
                size="small"
                className="full_width"
                variant="outlined"
                onChange={input.onChange}
                errorText={touched && error}
                error={touched && error}
            />
            {touched && error && <span className={'text-danger'}>{error}</span>}
        </React.Fragment>
    );
}

/**
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is common component for name inputs
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Props passed from parent component
 * @param {String} props.value Entered value by user
 * @param {Function} props.onChange To update the entered value by user
 * @return {JSX.Element}
 * @constructor
 */
const NameInput = (props) => {
    const [value, setValue] = useState(props.value);
    const handleChange = (e) => {
        setValue(e.target.value);
        props.onChange(e);
    }
    return (
        <Field
            name={props.name}
            placeholder={props.placeholder}
            disabled={props.disabled}
            size="small"
            variant="inline"
            inputVariant="outlined"
            data={props.value}
            className="ThemeInputTag"
            validate={props.validation}
            onChange={handleChange}
            component={renderTextField}
        />
    )
}

export default NameInput;