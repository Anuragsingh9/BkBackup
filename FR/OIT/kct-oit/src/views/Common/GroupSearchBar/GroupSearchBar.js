import React, {useState} from 'react';
import OutlinedInput from '@material-ui/core/OutlinedInput';
import groupAction from '../../../redux/action/apiAction/group';
import {useDispatch} from 'react-redux';
import SearchOutlinedIcon from '@material-ui/icons/SearchOutlined';

/**
 * @global
 * @component
 *
 * ----------------------------------------------------------------------------------------------------------------------
 * @description - This is a common component which is used to render search bar. it takes parameter and fetch api to
 * search according to given parameter and returns result if api response is successful.
 * ----------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props props Object that contain search bar details.
 * @param {Function} props.onRemoveText Function is used on removing the text
 * @param {Function} props.callBack Function is used to get api data
 * @returns {JSX.Element}
 * @constructor
 */
const SearchBar = (props) => {
    const dispatch = useDispatch();


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description - This method is used to handle changing values in the search bar input and it calls handleSearch
     * method when input value length is more than three latter's or else it calls onRemoveText method to set default
     * values.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript Event Object
     */
    const handleChange = (e) => {
        if (e.target.value && e.target.value.length > 2) {
            handleSearch(e.target.value);
        } else {
            props.onRemoveText()
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description - This method is used to handle api call for search input value if input value length is more than
     * three latter's if response is successful and calls callback method and pass result data in it .
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} val Value that is used for searching
     */
    const handleSearch = (val) => {
        console.log("props.filter", props)
        const data = {
            key: val,

        }
        console.log("filter", data)
        try {
            dispatch(groupAction.groupSearch(data)).then((res) => {
                props.callBack(res.data.data)
                console.log(res);
            }).catch((err) => {

            })
        } catch (err) {

        }
    }

    return (
        <OutlinedInput
            onChange={handleChange}
            size="small"
            margin="dense"
            placeholder=" Search"
            startAdornment={<SearchOutlinedIcon position="start">$</SearchOutlinedIcon>}
        />
    )
}

export default SearchBar;