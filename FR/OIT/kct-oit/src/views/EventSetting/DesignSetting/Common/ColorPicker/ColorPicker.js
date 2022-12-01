import React, {useEffect, useState} from 'react';
import {SketchPicker, ChromePicker} from 'react-color'
import Slider from '@material-ui/core/Slider';
import reactCSS from 'reactcss';
import {Button, Grid} from '@material-ui/core';
import _ from 'lodash'
import OutsideClickHandler from 'react-outside-click-handler';
import './ColorPicker.css';
import ColorRGBA from '../../../../../Models/ColorRGBA';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common color picker component by which user can select any color.In additional, it is also
 * provide a slider component to manage opacity of current selected color.When user clicks on it a popup will appear and
 * through it user can select color using color sliders + hexcode + rgba values.This is also provide some color
 * suggestion.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props object received from parent component consist color objects and functions to save primary
 * colors
 * @param {Function} props.callBack function to save current selected color values from color pickers
 * @param {ColorRGBA} props.color primary color's object in rgba form
 * @param {String} props.color primary color's opacity value(0 to 1)
 * @returns {JSX.Element}
 * @constructor
 */
const ColorPicker = (props) => {
    const [color, setColor] = useState({r: '', g: '', b: '', a: ''});
    const [display, setDisplay] = useState();
    const [sliderVal, setSlider] = useState(0);


    useEffect(() => {
        if (_.has(props.color, ['value'])) {
            const {value, field} = props.color;
            // console.log('dddddddddddddd props oclor', props.color);
            setColor(value)
            setSlider(value.a * 100);
        }
    }, [])


    useEffect(() => {
        if (_.has(props.color, ['value']) && JSON.stringify(props.color.value) != JSON.stringify(color)) {
            const {value, field} = props.color;
            setColor(value)
            setSlider(value.a * 100);
            props.callBack({field: field, value: value})
        }
    }, [props.color])

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will run when user change color values from color picker and update state(for current
     * color, current color's opacity value) as per data(getting form parameter).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} c Current color object contain different color formats(hex,hsl,rgb etc.) and we are using rgba
     * form currently
     * @param {String} c.hex Current color value in HEX form eg - "#ffffff"
     * @param {Object} c.hsl Current color value in HSL form eg - {
     * "h": 166.32124352331607,
     * "s": 0.7111975154823178,
     * "l": 0.67659558,
     * "a": 1
     * }
     * @param {Object} c.hsv Current color value in HSV form eg- {
     * "h": 166.32124352331607,
     * "s": 0.5074000000000001,
     * "v": 0.9066,
     * "a": 1
     * }
     * @param {Number} c.oldHue Current color value in OldHue form
     * @param {ColorRGBA} c.rgb Current color value in RGBA form
     * @param {String} c.source Current color  source value eg - "hsv"
     */
    const changeColor = (c) => {
        console.log('c', c)
        const {field} = props.color;
        const {rgb} = c;
        props.callBack({field: field, value: rgb})

        setColor(rgb)
        setSlider(rgb.a * 100);
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method will trigger when user change opacity slider's value. This will take opacity value from
     * its parameter and update it in state(setColor,setSlider) and pass it to callback function(method to save color).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} val Color opacity value
     */
    const handeSliderChange = (val) => {
        console.log('val', val)
        const {field} = props.color;
        setColor({...color, a: (val / 100)});
        props.callBack({field: field, value: {...color, a: (val / 100)}})
        setSlider(val);
    }

    //Style object
    const styles = reactCSS({
        'default': {
            color: {
                // width: '100px',
                height: '52px',
                padding: '0 0px 0 15px',
                borderRadius: '0px',
                background: `rgba(${color.r}, ${color.g}, ${color.b}, ${color.a})`,
            },
            'swatch': {
                padding: '0px',
                width: '100px',
                height: '52px',
                background: '#fff',
                borderRadius: '4px',
                overflow: 'hidden',
                display: 'inline-block',
                cursor: 'pointer',
            },
            'popover': {
                position: 'absolute',
                zIndex: '2',
            },
            'cover': {
                position: 'fixed',
                top: '0px',
                right: '0px',
                bottom: '0px',
                left: '0px',
            },
        },
    });

    return (
        <OutsideClickHandler
            onOutsideClick={() => {
                setDisplay(false);
            }}
        >
            <div className="SubColorPicker">
                {/* <h3>{_.has(props.color,['field'])?props.color.field:''}</h3> */}
                <span className="SliderSpan">
                    <Slider
                        className="MainSliderPadding"
                        defaultValue={100}
                        value={sliderVal}
                        aria-labelledby="discrete-slider-small-steps"
                        step={10}
                        marks
                        min={0}
                        max={100}
                        onChange={(e, v) => {
                            handeSliderChange(v)
                        }}
                        valueLabelDisplay="auto"
                        disabled={props?.disabled}
                    />
                    <div className="ColorPickerLabelDiv">
                        <div className="PickerLabel-1">0</div>
                        {/* <div className="PickerLabel-2">{props.transparencyLabel || "Opacity"}</div> */}
                        <div className="PickerLabel-3">100</div>
                    </div>
                </span>
                <div style={styles.swatch} onClick={props?.disabled ? "" : (c) => {
                    setDisplay(true)
                }}>
                    <div style={styles.color} />
                </div>
                {display &&
                    <div className="PickerPopOver">
                        <div className="CoverColorpicker">
                            <SketchPicker color={color} name="color1" value={color} onChange={(c) => {
                                changeColor(c);
                            }} />

                        </div>
                    </div>
                }

            </div>
        </OutsideClickHandler>

    )
}

export default ColorPicker;