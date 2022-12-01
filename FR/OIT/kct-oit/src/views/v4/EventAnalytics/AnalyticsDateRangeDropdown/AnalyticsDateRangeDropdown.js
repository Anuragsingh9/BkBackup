import React from 'react'
import SelectField from "../../Common/CoreInputs/SelectField"
import Constants from '../../../../Constants';
import {MenuItem} from '@material-ui/core';
import {useTranslation} from 'react-i18next';
import {connect} from 'react-redux';
import "../../Analytics/DateRangeDropdown/DateRangeDropdown.css"
import groupAction from '../../../../redux/action/reduxAction/group';
import {useEffect} from "react";
import CustomDateRangePickerModal from "../../Models/CustomDateRangePicker"
import _ from 'lodash';
import {useLocation, useParams, withRouter} from "react-router-dom";
import Helper from "../../../../Helper";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to filter analytics page table data as per some pre defined options.<br>
 * 1.custom<br>2.today<br>3.yesterday<br>4.last week<br>5.last month<br>6.last 7 days<br>7.last 30 days
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {JSX.Element}
 */
let AnalyticsDateRangeDropdown = (props) => {
    const {t} = useTranslation("analytics");
    const {event_uuid, gKey} = useParams();
    //This useeffect will set dates in range picker as per selected option from dropdown
    useEffect(() => {
        const rangeOptions = Object.values(CustomDateRangePickerModal)
        if (_.has(props, ['updateAnalyticRangePickerData']) && _.has(props, ['analytic_date_dropdown_val']) && props.analytic_date_dropdown_val !== 1) {
            //NOTE - here we are subtracting "2" from "engagement_tab_Dropdown_data" because in "rangeOptions" we don't
            //have option for "custom" option and "rangeOptions" is an array starting from "0"
            props.updateAnalyticRangePickerData(rangeOptions[props.analytic_date_dropdown_val - 2])
            const data = {
                from_date: rangeOptions[props.analytic_date_dropdown_val - 2][0].format('YYYY-MM-DD'),
                to_date: rangeOptions[props.analytic_date_dropdown_val - 2][1].format('YYYY-MM-DD'),
            }
            props.history.push(`/${gKey}/v4/event/analytics/${event_uuid}?${Helper.toQueryParam(data)}`)
        }
    }, [props.analytic_date_dropdown_val])

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
        console.log('anu dr eve', e.target.value)
        props.changeDateRange(e.target.value)
    }

    return (
        <div className='DateRangeDropdown'>
            <SelectField
                name="analytics_dateRangeSelector"
                labelId="analytics_dateRangeSelectorID"
                disabled={false}
                size={'small'}
                value={props.analytic_date_dropdown_val}
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
        changeDateRange: selectedDateRange => dispatch(groupAction.updateAnalyticTabDropdownData(selectedDateRange)),
        updateAnalyticRangePickerData: selectedDateRange => dispatch(groupAction.updateAnalyticRangePickerData(selectedDateRange)),
    }
};

const mapStateToProps = (state) => {
    return {
        analytic_range_picker_value: state.Group.analytic_range_picker_value,
        analytic_date_dropdown_val: state.Group.analytic_date_dropdown_val,
    }
}

AnalyticsDateRangeDropdown = connect(mapStateToProps, mapDispatchToProps)(AnalyticsDateRangeDropdown);
AnalyticsDateRangeDropdown = withRouter(AnalyticsDateRangeDropdown);
export default AnalyticsDateRangeDropdown;