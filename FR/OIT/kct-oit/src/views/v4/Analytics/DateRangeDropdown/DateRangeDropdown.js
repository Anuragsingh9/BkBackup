import React from 'react'
import SelectField from "../../Common/CoreInputs/SelectField"
import Constants from '../../../../Constants';
import {MenuItem} from '@material-ui/core';
import {useTranslation} from 'react-i18next';
import {connect} from 'react-redux';
import "./DateRangeDropdown.css"
import groupAction from '../../../../redux/action/reduxAction/group';
import {useEffect} from "react";
import CustomDateRangePickerModal from "../../Models/CustomDateRangePicker"
import _ from 'lodash';

/**
 * @class
 * @component
 * 
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to filter analytics page table data as per some pre defined options.<br>
 * 1.custom<br>2.today<br>3.yesterday<br>4.last week<br>5.last month<br>6.last 7 days<br>7.last 30 days
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent components
 * @param {Function} props.updateEngagementRangePickerData Redux function to handle the Date Range picker data
 * @param {Function} props.changeDateRange Redux function to handle the change in the value of Date range picker
 * @param {Object} props.engagement_tab_Dropdown_data Redux data holding tab dropdown data
 * @param {Object} props.range_picker_val Redux data holding range picker values
 * @returns {JSX.Element}
 */
let DateRangeDropdown = (props) => {
    const {t} = useTranslation("analytics");

    //This useeffect will set dates in range picker as per selected option from dropdown
    useEffect(() => {
        const rangeOptions = Object.values(CustomDateRangePickerModal)
        if(_.has(props,['updateEngagementRangePickerData']) && _.has(props,['engagement_tab_Dropdown_data'])){
            
            //NOTE - here we are subtracting "2" from "engagement_tab_Dropdown_data" because in "rangeOptions" we don't 
            //have option for "custome" option and "rangeOptions" is an array starting from "0"
            props.updateEngagementRangePickerData(rangeOptions[props.engagement_tab_Dropdown_data - 2])
        }
        // If user select "custom" option in range dropdown then last selected range will remain same.
        if(_.has(props,['engagement_tab_Dropdown_data']) && props.engagement_tab_Dropdown_data == 1){
            props.updateEngagementRangePickerData(props.range_picker_val)
        }
    }, [props.engagement_tab_Dropdown_data])
    

    /**
     * @method
     * 
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle selected option state from dropdown.
     * -----------------------------------------------------------------------------------------------------------------
     * 
     * @param {Object} e JavaScript event object 
     */
    const handleDateRange = (e) => {
        props.changeDateRange(e.target.value)
    }

    return (
        <div className='DateRangeDropdown'>
            <SelectField
                name="analytics_dateRangeSelector"
                labelId="analytics_dateRangeSelectorID"
                disabled={false}
                size={'small'}
                value={props.engagement_tab_Dropdown_data}
                onChange={handleDateRange}
            >
                {
                    Object.values(Constants.analyticsDateRange).map((option, index) => (
                        <MenuItem value={option.val} key={index + option.val}>{t(`${option.name}`)}</MenuItem>
                    ))
                }
            </SelectField>
        </div>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        changeDateRange: selectedDateRange => dispatch(groupAction.updateEngagementTabDropdownData(selectedDateRange)),
        updateEngagementRangePickerData: selectedDateRange => dispatch(groupAction.updateEngagementRangePickerData(selectedDateRange)),
    }
};

const mapStateToProps = (state) => {
    return {
        engagement_tab_Dropdown_data: state.Group.engagement_tab_data.date_dropdown_val,
        range_picker_val: state.Group.engagement_tab_data.range_picker_val,
    }
}

DateRangeDropdown = connect(mapStateToProps, mapDispatchToProps)(DateRangeDropdown);
export default DateRangeDropdown;