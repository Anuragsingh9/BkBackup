import React from 'react';
import TextareaAutosize from '@material-ui/core/TextareaAutosize';
import {Field} from 'redux-form';

/**
 * @functional
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component for description section to show text input area
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} data Props passed from parent component
 * @param {String} data.newVal Entered value by user
 * @param {Function} data.handleChange To handle the change in input value
 * @returns {JSX.Element}
 * @constructor
 */
const Description = (data) => {
    return (
        <TextareaAutosize
            name={`description-${data.index}`}
            minRows={3}
            value={data.newVal}
            disabled={data.disabled}
            onChange={data.handleChange}
            placeholder="Description"
            variant="outlined"
            className="ThemeInputTag DisTextArea"
        />
    )
}

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component renders the description section to show text input area
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {String} props.value Entered value by user in the description
 * @return {JSX.Element}
 * @constructor
 */
const DesField = (props) => {
    return (
        <Field
            {...props}
            newVal={props.value}
            component={Description}

        />
    )
}

export default DesField;