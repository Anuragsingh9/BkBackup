import React, {useState} from "react";
import {connect, useDispatch} from "react-redux";
import SearchOutlinedIcon from "@material-ui/icons/SearchOutlined";
import _ from "lodash";
import {FilledInput} from "@material-ui/core";
import groupAction from "../../../redux/action/reduxAction/group";


/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common search component which takes user input and make an API call to get filtered data
 * as per input keywords.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.updateSearchedKey Function is used to update the value in redux
 * @returns {JSX.Element}
 * @constructor
 */
let SearchBar = (props) => {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user start typing in search field and once input length is  greater
     * than 1 then it will store the entered input keyword into the redux store. This function is using
     * "Debouncing"(javaScript ES6 feature) to avoid unnecessary API calls.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e  Javascript event object
     */
    const handleChange = _.debounce((e) => {
        if (e.target.value && e.target.value.length > 1) {
            props.updateSearchedKey(e.target.value)
        }else{
            props.updateSearchedKey("")
        }
    }, 1000)


    return (
        <FilledInput
            onChange={handleChange}
            size="small"
            margin="dense"
            placeholder=" Search"
            className="filledSearchComponent"
            startAdornment={
                <SearchOutlinedIcon position="start">$</SearchOutlinedIcon>
            }
        />
    );
};

const mapStateToProps = (state) => {
    return {
        searched_key: state.Group.searched_key,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        updateSearchedKey: (key) => dispatch(groupAction.updateSearchedKey(key)),
    };
};


SearchBar = connect(mapStateToProps,mapDispatchToProps)(SearchBar);

export default SearchBar;
