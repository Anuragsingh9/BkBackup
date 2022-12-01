import React from 'react';
import {Field} from 'redux-form';
import {Select} from "@mui/material";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is child component  for select input
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Input} input Actual drop down value with its default properties
 * @param {Object} options Options of drop down
 * @param {Object} children Values of drop down
 * @param {Number} id Unique Id of the input
 * @param {String} value Value of drop down
 * @param {Boolean} disabled To check if the field is in disabled state
 * @param {String} label Labels for user interaction
 * @param {String} defaultValue Default value of select drop down
 * @param {Function} onOpen Function is used on open the drop down
 * @param {Function} onClose Function is used on close the drop down
 * @param {Object} touched To indicate if the select box is touched or not
 * @param {Object} error Error message from select box
 * @param {Object} custom Values of drop down
 * @constructor
 */
const renderSelectField = (
    {
        input,
        options,
        children,
        className,
        id,
        value,
        disabled,
        label,
        defaultValue,
        onOpen,
        onClose,
        meta: {touched, error},
        ...custom
    }
) => {

    return (
        <React.Fragment>
            <Select
                name={input.name}
                size="small"
                variant="filled"
                className={className}
                disabled={disabled}
                value={input.value ? input.value : defaultValue}
                defaultValue={defaultValue}
                onOpen={onOpen}
                onClose={onClose}
                onChange={input.onChange}
                MenuProps={{
                    anchorOrigin: {
                        vertical: "bottom",
                        horizontal: "left"
                    },
                    transformOrigin: {
                        vertical: "top",
                        horizontal: "left"
                    },
                    getContentAnchorEl: null
                }}
                {...input}
                error={true}
                helperText={"This is error"}
            >
                {children && children.map((item, key) => {
                    return item;
                })

                }
            </Select>
            {/* {touched && error &&<span className={'text-danger'}>{error}</span>} */}
        </React.Fragment>
    )
}

/**

 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component returns the Render Select component
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Props passed from parent
 * @param {String} props.name Name of the select field
 * @param {Boolean} props.disabled To check if the field is in disabled state
 * @param {Object} props.options options of drop down
 * @param {Number} props.id Unique Id of the select drop down
 * @param {Number} props.defaultValue Default value of drop down
 * @param {Function} props.handleChange To handle the change in the drop down
 * @param {Function} props.onChange Function is used on change the drop down value
 * @param {Object} props.children Values of drop down
 * @param {Function} props.onOpen Function is used on open the drop down
 * @param {Function} props.onClose Function is used on close the drop down
 * @return {JSX.Element}
 * @constructor
 */
const SelectField = (props) => {
    return (
        <Field
            name={props.name}
            disabled={props.disabled}
            options={props.options}
            id={props.id}
            defaultValue={props.defaultValue}
            handleChange={props.handleChange}
            component={renderSelectField}
            onChange={props.onChange}
            children={props.children}
            onOpen={props.onOpen}
            className={props.className}
            onClose={props.onClose}
            validate={props.validate}
        />
    )
}

export default SelectField;