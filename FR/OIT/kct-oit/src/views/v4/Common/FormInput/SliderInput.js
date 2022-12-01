import React from 'react';
import TextField from '@mui/material/TextField';
import {Field} from 'redux-form';
import ColorPicker from "../../../Common/ColorPickerNew/ColorPickerNew";
import Slider from "@mui/material/Slider";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component for slider in redux form
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Input} input Actual slider line with its default properties
 * @param {String} inputProps Input props for slider line
 * @param {String} label Labels for user interaction
 * @param {String} defaultValue Default value of slider line
 * @param {Object} touched To indicate if the slider line is touched or not
 * @param {Object} error Error message from slider line
 * @param {Object} custom Value of slider line
 * @returns {JSX.Element}
 */
const renderSlider = ({
    input,disabled, inputProps, label, defaultValue, meta: { touched, error}, ...custom
}) => {
    return (
        <React.Fragment>
            <Slider
                className="MainSliderPadding"
                defaultValue={90}
                // value={92}
                aria-labelledby="discrete-slider-small-steps"
                step={1}
                min={50}
                size="small"
                color="primary"
                max={100}
                disabled={disabled}
                value={input.value}
                onChange={input.onChange}
                valueLabelDisplay="auto"
            />
            {touched && error && <span className={"text-danger"}>{error}</span>}
        </React.Fragment>
    );
};

/**
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is common component for slider input type
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Props passed from parent component
 * @param {String} props.name Name of the slider line
 * @param {Boolean} props.disabled To check if the field is in disabled state
 * @param {Number} props.id Unique Id of the slider line
 * @return {JSX.Element}
 * @constructor
 */
const SliderInput = (props) => {

    return (
        <Field
            name={props.name}
            disabled={props.disabled}
            variant="filled"
            inputVariant="filled"
            className="ThemeInputTag"
            component={renderSlider}
            id={props.id}
            size={"small"}
        />
    )
}

export default SliderInput;