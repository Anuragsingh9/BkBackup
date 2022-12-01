import React, {useState, useEffect} from 'react'
import {AdapterMoment} from "@mui/x-date-pickers/AdapterMoment";
import {LocalizationProvider, StaticDateRangePicker} from '@mui/x-date-pickers-pro';
import {TextField} from "@mui/material";
import {Button, Popover} from '@material-ui/core';
import "./CustomDateRangePicker.css"
import moment from "moment-timezone";
import {connect} from 'react-redux';
import groupAction from '../../../../redux/action/reduxAction/group';
import _ from "lodash";
import CustomDateRangePickerModal from '../../Models/CustomDateRangePicker';
import Constants from '../../../../Constants';

/**
 * @class
 * @component
 * 
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a custom range picker component which is using MUI popover and static DateRangePicker componet.
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {Object} props Object that contains some methods and values from redux
 * @param {Method} props.updateEngagementRangePickerData Method to update redux value for date range 
 * @param {Method} props.changeDateRange Mehotd to update redux value for range dropdown value 
 * @param {Array[String]} props.range_picker_val Array that consist start time and end time values
 * @returns {JSX.Element}
 */
let CustomDateRangePicker = (props) => {
    const [rangePickerBtnText, setRangePickerBtnText] = useState("No Date selected")
    moment.tz.setDefault("Europe/Paris");
    const [anchorEl, setAnchorEl] = React.useState(null);

    // While selecting date range this hook will prepare text as per start date and end date for range picker button.
    console.log('sdfvfds', moment(props.range_picker_val[0].format('DD-MM-YYYY HH:mm:ss')),
    moment(props.range_picker_val[1].format('DD-MM-YYYY HH:mm:ss')))
    useEffect(() => {
        if (_.has(props, ['range_picker_val'])
            && props.range_picker_val
            && props.range_picker_val[0] !== null
            && props.range_picker_val[1] !== null
        ) {
            // console.log('sdfert', props.range_picker_val)
            let startDateText = moment(props?.range_picker_val[0])
            let endDateText = moment(props?.range_picker_val[1])

            let fullText = `${startDateText.format('LL')} - ${endDateText.format('LL')}`

            setRangePickerBtnText(`${fullText}`)
        }

    }, [props.range_picker_val])

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
        if (_.has(props, ['changeDateRange'])) {
            props.changeDateRange(Constants.analyticsDateRange.CUSTOM.val)
        }
        let startTime = moment(newValue[0]).startOf('day');
        let endTime;

        // Check if user not selected end date then it will add start date(end time - 23:59:59) in end date
        if(newValue[1] instanceof moment){
            endTime = moment(newValue[1]).endOf('day');
        }else{
            endTime = moment(newValue[0]).endOf('day');
        }
        console.log('endTimee', endTime,newValue[1])
        props?.updateEngagementRangePickerData([startTime,endTime]);
    }

    return (
        <>
            <button
                aria-describedby={id}
                className="rangePicker_customButton"
                variant="contained"
                onClick={handleOpenPopup}
            >
                {rangePickerBtnText}
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
                        value={props.range_picker_val || [null, null]}
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

const mapDispatchToProps = (dispatch) => {
    return {
        updateEngagementRangePickerData: selectedDateRange => dispatch(groupAction.updateEngagementRangePickerData(selectedDateRange)),
        changeDateRange: selectedDateRange => dispatch(groupAction.updateEngagementTabDropdownData(selectedDateRange)),
    }
};

const mapStateToProps = (state) => {
    return {
        range_picker_val: state.Group.engagement_tab_data.range_picker_val,
    }
}

CustomDateRangePicker = connect(mapStateToProps, mapDispatchToProps)(CustomDateRangePicker);
export default CustomDateRangePicker;