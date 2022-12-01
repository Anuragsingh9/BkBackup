import React, {useRef, useState} from 'react';
import {getAlphaValidator} from '../../../../functions/commons';
import useForceUpdate from 'use-force-update';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component to render a text field to take inputs from the user for (first name, last
 * name, union, company).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {String} props.value Value of text input field
 * @param {String} props.name Field Name of text input field
 * @param {UserBadge} props.event_badge User badge details
 * @param {Function} props.setBadge To update the user badge details in parent component state
 * @param {Function} props.onChangeHandler Handler method from parent when field is updated
 * @param {String} props.field Field name of field to be used internally
 * @param {String} props.placeholder Placeholder value of respective field
 * @param {Function} props.onBlur Handler to execute when field is blur
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const BadgeTextInput = (props) => {
    const [value, setValue] = useState(props.value);
    const [name, setName] = useState(props.name);
    const validator = useRef(getAlphaValidator());
    // in this forceUpdate hooK is used for functional component
    const forceUpdate = useForceUpdate();

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Function handles any change in input area for first name and last name and save it in a state.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const handleChange = (e) => {
        setValue(e.target.value);
        let field = ''
        if (name == 'First name') {
            field = 'user_fname'
        }
        if (name == 'Last name') {
            field = 'user_lname'
        }
        if (validator.current.fieldValid(name)) {
            props.onChangeHandler(e.target.value, field)
        } else {
            validator.current.showMessages();
        }
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Function handles any change in input area after on blur event for first name and last name.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const handleBlur = (e) => {
        if (validator.current.allValid()) {
            props.onBlur && props.onBlur({field: props.field, value: value})
        } else {
            validator.current.showMessages();
            forceUpdate();
        }
    }


    return (
        <React.Fragment>
            <input
                type="text"
                autocapitalize="none"
                value={value}
                placeholder={props.placeholder}
                onBlur={handleBlur}
                onChange={handleChange}
                className="form-control "
            />
            <span className="text-danger">
        {validator.current.message(name, value, 'alpha_space|required')}
      </span>
        </React.Fragment>
    );
}


export default BadgeTextInput;