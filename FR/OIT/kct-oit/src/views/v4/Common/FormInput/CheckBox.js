import React from 'react';
import {Field} from 'redux-form';
import {Checkbox} from "@mui/material";
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
const renderCheckbox = (
    {
        input,
        placeholder,
        label,
        hidden,
        defaultValue,
        meta: {touched, error},
        ...custom
    },
) => {
    return (
        <React.Fragment>
            <Checkbox
                name={input.name}
                checked={input.value}
                size="small"
                className="full_width"
                variant="filled"
                {...custom}
                {...input}
            />
        </React.Fragment>
    );
}

const CheckBox = (props) => {
    return (
        <Field
            name={props.name}
            inputVariant="filled"
            className={`ThemeInputTag`}
            component={renderCheckbox}
        />
    )
}

export default CheckBox;