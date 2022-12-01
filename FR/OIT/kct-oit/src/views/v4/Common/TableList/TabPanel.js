import {Box, Typography} from "@mui/material";
import React from "react";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function is receiving props(from main nav tab components - down below)  to render content box of
 * nav tab's child.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Inherited from parent component
 * @param {JSX} props.children JSX element for tab panel
 * @param {Number} props.value Index of the tab panel
 * @param {Number} props.index Index of tab panel
 * @returns {JSX.Element}
 * @constructor
 */
let TabPanel = (props) => {
    const {children, value, index, ...other} = props;

    return (
        <div
            role="tabpanel"
            hidden={value !== index}
            id={`nav-tabpanel-${index}`}
            aria-labelledby={`nav-tab-${index}`}
            {...other}
        >
            {value === index && (
                <Box p={3}>
                    <Typography>{children}</Typography>
                </Box>
            )}
        </div>
    );
}

export default TabPanel;