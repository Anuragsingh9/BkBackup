import React, {useState} from 'react'
import {Field} from 'redux-form';
import {RadioGroup} from "@mui/material";

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
const renderRadioButton = (
    {
        input,
        children,
        label,
        defaultValue,
        meta: {touched, error},
        ...custom
    },
) => {
    return (
        <React.Fragment>
            <RadioGroup
                {...input}
                {...custom}
                name={input.name}
                value={input?.value ? input.value : defaultValue}
                onChange={(event) => input.onChange(Number.parseInt(event.target.value))}
            >
                {
                    children && children.map((item, key) => {
                        return item;
                    })
                }
            </RadioGroup>

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
const RadioButtonInput = (props) => {
    return (
        <Field
            name={props.name}
            type="time"
            variant="filled"
            className="ThemeInputTag-2"
            component={renderRadioButton}
            children={props.children}
            row={props.row}
            disabled={props.disabled}
            defaultValue={props.defaultValue}
        />
    )
}

export default RadioButtonInput;
