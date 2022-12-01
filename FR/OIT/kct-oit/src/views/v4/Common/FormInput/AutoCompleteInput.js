import React, {useState} from "react";
// import "./UserAutoComplete.css";
import {Autocomplete} from "@mui/lab";
import {TextField} from "@mui/material";

/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component used for searching and fetching users data by their email. It fetches the
 * data according to the key entered by user.
 * <br>
 * <br>
 * The search API triggers only when the entered key is greater than 2 characters.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Boolean} props.disabled Auto complete disabled or not
 * @param {Event} props.event_data Event Data
 * @param {Object} props.name Space host name
 * @param {Function} props.userSearch Searching on event data
 * @returns {JSX.Element}
 */
const AutoCompleteInput = (props) => {
    const [options, setOptions] = useState([]);

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This is used for handle changing data
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     * @param {Object} value Object of user
     */
    const handleChange = (e, value) => {
        props.onTypeSearchHandler(value, setOptions);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This fetch api and get data of enter entity when entity length is 3 or greater than 3
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Entity data
     */
    const getOptionLabel = (option) => {
        return option ? `${option.fname} ${option.lname} (${option.email})` : '';
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To render the component for the text input where user can enter text to search
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} params Parameters for the input text component
     * @returns {JSX.Element}
     */
    const renderTextInput = (params) => (
        <TextField
            {...params}
            // label="Space Host "
            className="autoCompleteInput"
            placeholder={props.placeholder}
            variant="filled"
            size="small"
            disabled={props.disabled ? props.disabled : false}
            errorText={props.errorText}
            error={props.error}
            inputProps={{
                ...params.inputProps,
                autoComplete: "new-password", // disable autocomplete and autofill
            }}
            helperText={props.helperText}
        />
    )

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Change handler for the component as the on change passes the data in second parameter, this will
     * pass that data to redux on change method
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} event Javascript event object
     * @param {*} data Data that has been initialized from options
     */
    const onChangeHandler = (event, data) => {
        props.onChange(data);
    }

    return (
        <Autocomplete
            value={props.value}
            options={options ? options : []}
            name={props.name}
            onChange={onChangeHandler}
            autoHighlight
            getOptionLabel={getOptionLabel}
            onInputChange={handleChange}
            renderInput={renderTextInput}
            error={props.error}
            helperText={props.helperText}
        />
    );
};


export default AutoCompleteInput;
