import React, {useEffect, useState} from 'react';
import ColorPicker from '../Common/ColorPicker/ColorPicker';
import UpDownArrowIcon from '../../../Svg/UpDownArrowIcon.js';
import './ColorCommon.css';
import ColorRGBA from '../../../../Models/ColorRGBA';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used to render color pickers for primary color 1 and color 2.
 * <br>
 * It also have an arrow button which will swap the colors between color1 and color2.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Object received from parent component consist color objects and functions to save primary
 * colors
 * @param {ColorRGBA} props.main_color_1 Primary color 1 value in rgba
 * @param {ColorRGBA} props.main_color_2 Primary color 2 value in rgba
 * @param {Function} props.callBack Function to save current selected color values from color pickers
 * @param {Function} props.colorCallback Function to reset current colors into primary colors
 * @returns {JSX.Element}
 * @constructor
 */
const ColorCommon = (props) => {

    const [main_color_1, setMain1] = useState(props.main_color_1)
    const [main_color_2, setMain2] = useState(props.main_color_2)

    useEffect(() => {
        if (props.main_color_1 != main_color_1) {
            setMain1(props.main_color_1);
            setMain2(props.main_color_2);
            props.colorCallback([props.main_color_1, props.main_color_2]);
        }
    }, [props.main_color_1])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will interchange primary color value 1 and primary color value 2 and pass updated
     * data to function 'callBack' to update design setting values.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const interchangeColor = () => {
        const color2 = main_color_2;
        const color1 = main_color_1;

        props.callBack({field: 'main_color_1', value: color2.value})
        props.callBack({field: 'main_color_2', value: color1.value})

        setTimeout(() => {
            setMain1({field: 'main_color_1', value: color2.value});

            setMain2({field: 'main_color_2', value: color1.value});

        }, 1000)

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This callback function will trigger when user change primary color values from color picker and
     * save them in states(setMain1, setMain2).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Object of primary colors with value and key
     * @param {String} data.field Primary color's key
     * @param {ColorRGBA} data.value Primary color's object in rgba form
     */
    const callBack = (data) => {
        if (data.field == 'main_color_1') {
            setMain1(data);
        } else {
            setMain2(data);
        }
        props.callBack(data)
    }


    return (
        <div className="ColorPickerComponent">
            <ColorPicker color={main_color_1} callBack={callBack} disabled={props?.disabled} />
            <UpDownArrowIcon className="ExchangeArrow" onClick={props?.disabled ? "" : interchangeColor} />
            <ColorPicker color={main_color_2} callBack={callBack} disabled={props?.disabled} />
        </div>
    )

}

export default ColorCommon;