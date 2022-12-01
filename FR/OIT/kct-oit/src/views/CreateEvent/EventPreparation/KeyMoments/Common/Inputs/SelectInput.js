import React from 'react';
import Select from "@material-ui/core/Select";
import MenuItem from "@material-ui/core/MenuItem";
import { Field } from 'redux-form';
import _ from 'lodash';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is child component  for select input
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Props passed from parent
 * @param {Number} props.id Unique Id of the input
 * @param {Boolean} props.disabled To check if the field is in disabled state
 * @param {String} props.name Name of the input fields
 * @param {Function} props.handleChange To handle the change in the input box
 * @return {JSX.Element}
 * @constructor
 */
const RenderSelect = (props) => {
    return(
        <React.Fragment>
            <Select
                labelId={`demo-simple-select-label${props.id}`}
                name={props.name}
                size="small"
                variant="outlined"
                id="demo-simple-select"
                disabled={props.disabled}
                value={props.value}
                onChange={props.handleChange}
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
                >

                    {props.options.map((item)=>{

                        return(
                            <MenuItem value={item.value} disabled ={_.has(item, ["disabled"] ) ? item.disabled : false } >{item.label}</MenuItem>
                        )
                    })

                    }
            </Select>
            {/* {touched && error &&<span className={'text-danger'}>{error}</span>} */}
        </React.Fragment>
    )
}

/**

 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component returns the RenderSelect component
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Props passed from parent
 * @param {Number} props.id Unique Id of the input
 * @param {Boolean} props.disabled To check if the field is in disabled state
 * @param {String} props.name Name of the input field
 * @param {Function} props.handleChange To handle the change in the input box
 * @return {JSX.Element}
 * @constructor
 */
const SelectInput = (props) => {
    return(
        <RenderSelect
            name={props.name}
            disabled={props.disabled}
            options={props.options}
            value={props.value}
            id={props.id}
            handleChange={props.handleChange}
        />
    )
}

export default SelectInput;