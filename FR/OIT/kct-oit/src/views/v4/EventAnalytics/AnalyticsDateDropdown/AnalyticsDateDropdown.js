import React, {useEffect, useState} from 'react'
import {MenuItem, Select} from '@material-ui/core';
import {connect} from "react-redux";
import Constants from "../../../../Constants";
import {useLocation, useParams, withRouter} from "react-router-dom";
import Helper from "../../../../Helper";
import eventAction from "../../../../redux/action/reduxAction/event";
import {useTranslation} from "react-i18next";
import groupAction from "../../../../redux/action/reduxAction/group";
import CustomDateRangePickerModal from "../../Models/CustomDateRangePicker";
import _ from "lodash";

let AnalyticsDateDropdown = (props) => {
    const singleItem = 36;
    const paddingY = 18;
    const showItemAtATime = 6;
    const {t} = useTranslation("analytics");

    const [dropDownLabel, setDropDownLabel] = useState('All Ocurrences');
    const [selectedRecurrence, setSelectedRecurrence] = useState(Constants.EVENT_ANALYTICS.ALL_REC_DROP_DOWN);
    const [menuItems, setMenuItems] = useState([]);

    const location = useLocation();
    const query = new URLSearchParams(location.search);
    const {event_uuid, gKey} = useParams();

    useEffect(() => {
        if (props.current_event?.event_type !== 4) {

            setMenuItems(props.recurrences_list)
            setSelectedRecurrence(Constants.EVENT_ANALYTICS.ALL_REC_DROP_DOWN)
        } else {
            setMenuItems(Object.values(Constants.analyticsDateRange))
            setSelectedRecurrence(props.analytic_date_dropdown_val);
        }
    }, [props.current_event?.event_type,props?.current_event])

    useEffect(() => {
        const rangeOptions = Object.values(CustomDateRangePickerModal)
        if (props.current_event?.event_type === 4 &&_.has(props, ['updateAnalyticRangePickerData']) && _.has(props, ['analytic_date_dropdown_val']) && props.analytic_date_dropdown_val !== 1) {
            //NOTE - here we are subtracting "2" from "engagement_tab_Dropdown_data" because in "rangeOptions" we don't
            //have option for "custom" option and "rangeOptions" is an array starting from "0"
            props.updateAnalyticRangePickerData(rangeOptions[props.analytic_date_dropdown_val - 2])
            const data = {
                from_date: rangeOptions[props.analytic_date_dropdown_val - 2][0].format('YYYY-MM-DD'),
                to_date: rangeOptions[props.analytic_date_dropdown_val - 2][1].format('YYYY-MM-DD'),
            }
            props.history.push(`/${gKey}/v4/event/analytics/${event_uuid}?${Helper.toQueryParam(data)}`)
        }
        else{
            setLabelFromQuery();
        }
    }, [query.get('r'), query.get('from_date'), query.get('to_date'),props.analytic_date_dropdown_val])


    const setLabelFromQuery = () => {

        let r = query.get('r');
        let from = Helper.timeHelper.checkAndGetMoment(query.get('from_date'), [Constants.DATE_TIME_FORMAT, 'YYYY-MM-DD']);
        let to = Helper.timeHelper.checkAndGetMoment(query.get('to_date'), [Constants.DATE_TIME_FORMAT, 'YYYY-MM-DD']);
        if (from && to) {
            setSelectedRecurrence("custom");
        }else{
            if (r) {
                setSelectedRecurrence(r);
            }
            if (selectedRecurrence === Constants.EVENT_ANALYTICS.ALL_REC_DROP_DOWN){
                setSelectedRecurrence(Constants.EVENT_ANALYTICS.ALL_REC_DROP_DOWN)
                props.history.push(`/${gKey}/v4/event/analytics/${event_uuid}?r=${Constants.EVENT_ANALYTICS.ALL_REC_DROP_DOWN}`)
            }
        }

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for redirect to selected recurrence
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param e
     */
    const handleDropDownChange = (e) => {
        if (props.current_event?.event_type !== 4){
            setSelectedRecurrence(e.target.value);
            props.history.push(`/${gKey}/v4/event/analytics/${event_uuid}?r=${e.target.value}`)
        }else{
            setSelectedRecurrence(e.target.value)
            props.changeDateRange(e.target.value)
        }
    }

    return (
        <div className='AnalyticsEventDateDropdown'>
            <Select
                size="small"
                variant="filled"
                name={"event_rec_type"}
                onChange={handleDropDownChange}
                onClose={setLabelFromQuery}
                value={selectedRecurrence}
                MenuProps={{
                    anchorOrigin: {
                        vertical: "bottom",
                        horizontal: "left"
                    },
                    transformOrigin: {
                        vertical: "top",
                        horizontal: "left"
                    },
                    getContentAnchorEl: null,
                    PaperProps: {
                        style: {
                            maxHeight: (singleItem * showItemAtATime) + paddingY,
                        },
                    },
                }}
            >
                {/* hidden item to show the custom as selected in select box*/}

                <MenuItem value={"custom"} style={{display: "none"}}>Custom</MenuItem>
                {props.current_event?.event_type !== 4 &&
                    <MenuItem
                        className='positionStickyTop'
                        value={Constants.EVENT_ANALYTICS.ALL_REC_DROP_DOWN}
                    >
                        All Recurrences
                    </MenuItem>
                }

                {
                    menuItems.map((recc, index) => (
                        props.current_event?.event_type === 4 ?
                            <MenuItem value={recc.val}>{t(`${recc.name}`)}</MenuItem>
                            :
                            <MenuItem value={recc.recurrence_uuid}>
                                {`Rec ${index} - ${recc.rec_start_date.format('MMM D, YYYY')}`}
                            </MenuItem>
                    ))
                }
            </Select>
        </div>
    )
}

const mapStateToProps = (state) => {
    return {
        recurrences_list: state.Analytics.recurrences_list,
        current_event: eventAction.getCurrentEvent(state),
        analytic_date_dropdown_val: state.Group.analytic_date_dropdown_val,

    }
}
const mapDispatchToProps = (dispatch) => {
    return {
        changeDateRange: selectedDateRange => dispatch(groupAction.updateAnalyticTabDropdownData(selectedDateRange)),
        updateAnalyticRangePickerData: selectedDateRange => dispatch(groupAction.updateAnalyticRangePickerData(selectedDateRange)),
    }
}
AnalyticsDateDropdown = connect(mapStateToProps, mapDispatchToProps)(AnalyticsDateDropdown);
AnalyticsDateDropdown = withRouter(AnalyticsDateDropdown);
export default AnalyticsDateDropdown;