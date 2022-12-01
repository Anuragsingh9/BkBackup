import React from 'react';
import {Field} from 'redux-form';
import Switch from '@mui/material/Switch';
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
const renderSwitchInput = (
    {
        input,
        defaultValue,
        disabled,
        meta: {touched, error},
        ...custom
    },
) => {
    return (
        <React.Fragment>
            <Switch
                name={input.name}
                disabled={disabled}
                // size="small"
                {...custom}
                {...input}
                checked={input.value}
            />
        </React.Fragment>
    );
}

const SwitchInput = (props) => {
    return (
        <Field
            name={props.name}
            component={renderSwitchInput}
            disabled={props.disabled}
        />
    )
}

export default SwitchInput;