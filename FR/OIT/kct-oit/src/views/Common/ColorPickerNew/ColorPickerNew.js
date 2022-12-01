import React from 'react';
import ColorPicker from "../../EventSetting/DesignSetting/Common/ColorPicker/ColorPicker";
import ColorKeyObject from '../../../Models/ColorKeyObject';

/**
 * @class
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a wrapper component which is returning a common color picker component.Component is called from
 * the design setting common component's folder.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This object consist a color object(value,key) and function to save current color value and a
 * label for transparency slider component.
 * @param {Function} props.callBack Function to save current color value from color picker
 * @param {ColorKeyObject} props.color Object that consist component's unique key and a color object
 * @returns {JSX.Element}
 * @constructor
 */
const ColorPickerNew = (props) => {
    return (
        <ColorPicker {...props}>

        </ColorPicker>
    )
}

export default ColorPickerNew