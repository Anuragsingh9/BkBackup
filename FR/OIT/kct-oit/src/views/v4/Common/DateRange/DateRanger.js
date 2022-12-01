import React, {useEffect, useState} from "react";
import {TextField} from "@mui/material";
import {LocalizationProvider, StaticDateRangePicker} from "@mui/x-date-pickers-pro";
import _ from "lodash";
import moment from "moment-timezone";
import {Popover} from "@material-ui/core";
import {AdapterMoment} from "@mui/x-date-pickers/AdapterMoment";
import {connect} from "react-redux";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used for common date range picker
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Passed from parent component
 * @param {Function} props.onDateChange use for send the start and end date
 * @returns {JSX.Element}
 * @constructor
 */
let DateRanger = (props) => {

    const [anchorEl, setAnchorEl] = React.useState(null);

    const handleOpenPopup = (event) => {
        setAnchorEl(event.currentTarget);
    }
    const handleClosePopup = () => {
        setAnchorEl(null);
    };

    //Open state using anchor-element
    const open = Boolean(anchorEl);//This method will convert anchor-element into boolean val
    const id = open ? 'simple-popover' : '';//Here we are setting id as per open condition


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will update redux value for range picker component and dropdown option.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Array[String]} newValue Array that consist start time and end time values
     */
    const handleRangePickerChange = (newValue) => {
        let startTime = moment(newValue[0]).startOf('day');
        let endTime;

        // Check if user not selected end date then it will add start date(end time - 23:59:59) in end date
        if(newValue[1] instanceof moment){
            endTime = moment(newValue[1]).endOf('day');
        }else{
            endTime = moment(newValue[0]).endOf('day');
        }
        props.onDateChange(startTime, endTime);
    }

    return (
        <>
            <button
                aria-describedby={id}
                className="rangePicker_customButton"
                variant="contained"
                onClick={handleOpenPopup}
            >
                {props.rangePickerBtnText}
            </button>
            <Popover
                id={id}
                open={open}
                anchorEl={anchorEl}
                onClose={handleClosePopup}
                anchorOrigin={{
                    vertical: 'bottom',
                    horizontal: 'left',
                }}
                transformOrigin={{
                    vertical: 'top',
                    horizontal: 'left',
                }}
            >
                <LocalizationProvider
                    dateAdapter={AdapterMoment}
                    localeText={{start: '', end: ''}}
                >
                    <StaticDateRangePicker
                        displayStaticWrapperAs="desktop"
                        value={[props.dateRange.start, props.dateRange.end] || [null, null]}
                        onChange={handleRangePickerChange}
                        disableFuture
                        renderInput={(startProps, endProps) => (
                            <React.Fragment>
                                <TextField {...startProps} className="customDateRangePicker" />
                                <h4>to</h4>
                                <TextField {...endProps} className="customDateRangePicker" />
                            </React.Fragment>
                        )}
                    />
                </LocalizationProvider>
            </Popover>
        </>
    )
}

export default DateRanger;