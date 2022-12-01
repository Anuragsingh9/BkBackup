import React, {useEffect, useRef, useState} from 'react';
import {ReactComponent as EyesOpen} from "../../../../../images/eye-variant-with-enlarged-pupil.svg";
import {ReactComponent as EyeCross} from '../../../../../images/eye-cross2.svg';
import SimpleReactValidator from 'simple-react-validator';
import {useTranslation} from 'react-i18next'
import useForceUpdate from 'use-force-update';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for rendering/displaying DesInput in Description.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {String} props.value Value of the respective field
 * @param {String} props.field Name of the field
 * @param {Function} props.onBlur Handler when user gets blur from the field to update the data
 * @param {String} props.name Name of field
 * @param {Number} props.visibility Visibility level of current field for other users
 * @param {String} props.visibilityType Type of the visibility as each field have respective visibility field name
 * @param {Number} props.eyeState Eye value for the visibility for on and off
 * @param {Function} props.delete Handler when the field is deleted
 * @param {String} props.dataTip Tooltip value to show user on hover
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const DesInput = (props) => {


    const {t} = useTranslation('myBadgeBlock');
    const [value, setValue] = useState(props.value);
    const [name, setName] = useState(props.name);
    const [showDelete, setShowDelete] = useState(true);
    const [field, setField] = useState();

    const forceUpdate = useForceUpdate();


    useEffect(() => {
        blurCallback();
    }, [])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description validator function handled validation error massage for max length
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const reactValidater = new SimpleReactValidator({
        messages: {
            max: t("Max"),

        }
    });

    const validator = useRef(reactValidater)


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description function handled change in input value and update state value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const handleChange = (e) => {
        let value = e.target.value;
        setValue(value)
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description function handled out side click and apply validation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleBlur = () => {

        if (value.length <= 100) {
            props.onBlur && props.onBlur({field: props.field, value: value}, blurCallback)
        } else {
            validator.current.showMessages();

            forceUpdate();
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this function handles delete event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleDelete = () => {
        props.delete && props.delete({field: props.field, value: value}, callback, blurCallback)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this function recall the value after any action
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const callback = () => {
        setValue('');
        setField(props.field);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this function handles callback for blueCallBack
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const blurCallback = () => {
        if (value == '') {
            setShowDelete(false);
        } else {
            setShowDelete(true);
        }

    }

    return (
        <div className="row">
            <div className="col-md-9 col-sm-8 sm-pr-0">
                <textarea rows={3}
                          value={value}
                          name={name}
                          onBlur={handleBlur} onChange={handleChange}
                          className=" no-border form-control-xs YourDisTextArea"
                > </textarea>
                <span className="text-danger">
                   {validator.current.message('max', value, 'max:100')}
                </span>
            </div>

            <div className="col-md-3 col-sm-3 p-0 sm-p-0">
                {
                    showDelete &&
                    <span className="eyepop eyeposition1" data-tip={props.dataTip}
                          onClick={() => props.visibility(props.visibilityType)}>
                        {props.eyeState ? <EyesOpen /> : <EyeCross />}</span>
                }
            </div>
            <div className="col-md-3 col-sm-3 p-0 sm-p-0">
                {showDelete &&
                <span className="fa trash-btn trash-btn-ub fa-trash p-0 m-0" onClick={handleDelete}></span>
                }
            </div>


        </div>
    )
}

export default DesInput