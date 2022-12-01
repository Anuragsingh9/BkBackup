import Tab from "@mui/material/Tab";
import React from "react";


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will take data(for create a basic link component for nav tab) from parameter
 * and return a component(JSX) on which if user clicks then it will render relative child components to it.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Props passed from parent component
 * @param {String} props.aria-controls Css related key for link tab
 * @param {Boolean} props.disabled To disable the link tab
 * @param {Boolean} props.fullWidth To get the full width view
 * @param {String} props.href link's
 * @param {String} props.id User's Id
 * @param {Boolean} props.indicator To indicate the current tab by showing a horizontal line below selected tab
 * @param {String} props.label Label on link tab
 * @param {Function} props.onChange Function is used change the state
 * @param {Boolean} props.selected Link is selected or not
 * @param {String} props.textColor Text color
 * @param {Number} props.value Link value
 * @returns {JSX.Element}
 * @constructor
 */
function LinkTab(props) {
    return (
        <Tab wrapped
             onClick={(e) => {
                 e.preventDefault()
             }}
             {...props}
        />
    );
}

export default LinkTab;