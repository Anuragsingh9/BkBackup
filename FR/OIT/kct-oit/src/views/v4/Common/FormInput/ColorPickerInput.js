import React from 'react';
import TextField from '@mui/material/TextField';
import {Field} from 'redux-form';
import ColorPicker from "../../../Common/ColorPickerNew/ColorPickerNew";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component for color picker in redux form
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Input} input Actual color box with its default properties
 * @param {String} inputProps Input props for input box
 * @param {String} label Labels for user interaction
 * @param {String} defaultValue Default value of color box
 * @param {Object} touched To indicate if the color box is touched or not
 * @param {Object} error Error message from color box
 * @param {Object} custom Value of color
 * @method
 */
const renderColorComponent = ({
    input, inputProps,disabled, label, defaultValue, meta: { touched, error}, ...custom
}) => {
    return (
        <React.Fragment>
            <ColorPicker
                callBack={input.onChange}
                color={input.value}
                transparencyLabel={"Transparency"}
                disabled={disabled}
            />
            {touched && error && <span className={"text-danger"}>{error}</span>}
        </React.Fragment>
    );
};

/**
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is common component for color picker input
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Props passed from parent component
 * @param {String} props.name Name of the color box
 * @param {Boolean} props.disabled To check if the field is in disabled state
 * @param {Number} props.id Unique Id of the color box
 * @return {JSX.Element}
 * @constructor
 */
const ColorPickerInput = (props) => {

    return (
        <Field
            name={props.name}
            disabled={props.disabled}
            variant="filled"
            inputVariant="filled"
            className="ThemeInputTag"
            component={renderColorComponent}
            id={props.id}
            size={"small"}
        />
    )
}

export default ColorPickerInput;