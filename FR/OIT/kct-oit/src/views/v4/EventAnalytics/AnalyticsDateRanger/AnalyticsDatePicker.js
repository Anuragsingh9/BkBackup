import React, {useEffect, useState} from 'react'
import {connect} from "react-redux";
import moment from "moment-timezone";
import DateRanger from "../../Common/DateRange/DateRanger";
import Constants from "../../../../Constants";
import {useLocation, useParams, withRouter} from "react-router-dom";
import Helper from "../../../../Helper";
import groupAction from "../../../../redux/action/reduxAction/group";

let AnalyticsDatePicker = (props) => {

    const {event_uuid, gKey} = useParams();
    const location = useLocation();
    const query = new URLSearchParams(location.search);
    const [dateRange, setDateRange] = useState({start: moment(), end: moment()});
    const [recType, setRecType] = useState("")
    const [isSameDay,setIsSameDay] = useState(false);

    useEffect(() => {
        let r = query.get('r');
        setRecType(r)
        if (r) {
            let rec = props.recurrences_list.find(rec => rec.recurrence_uuid === r);
            let totalRec = props.recurrences_list.length;
            if (rec) {
                setIsSameDay(true)
                setDateRange({
                    start: rec.rec_start_date,
                    end: null,
                })
            } else if (r === Constants.EVENT_ANALYTICS.ALL_REC_DROP_DOWN && totalRec) {
                setDateRange({
                    start: props.recurrences_list[0].rec_start_date,
                    end: props.recurrences_list[totalRec - 1].rec_start_date,
                })
            }
            console.log('ddddddd selected recurrence', rec);
        } else {
            setRangeByFormat();
        }
    }, [query.get('from_date'), query.get('to_date'), query.get('r'),props.recurrences_list])

    const setRangeByFormat = () => {
        let from = Helper.timeHelper.checkAndGetMoment(
            query.get('from_date'),
            [Constants.DATE_TIME_FORMAT, 'YYYY-MM-DD']
        );
        let to = Helper.timeHelper.checkAndGetMoment(
            query.get('to_date'),
            [Constants.DATE_TIME_FORMAT, 'YYYY-MM-DD']
        );

        if (from && to) {
            setIsSameDay(from.format('YYYY-MM-DD') === to.format('YYYY-MM-DD'))
            setDateRange({
                start: from,
                end: to,
            })
            return true;
        }
        return false;
    }

    const onDateChange = (startDate, endDate) => {
        const data = {
            from_date: startDate.format('YYYY-MM-DD'),
            to_date: endDate.format('YYYY-MM-DD'),
        }
        props.history.push(`/${gKey}/v4/event/analytics/${event_uuid}?${Helper.toQueryParam(data)}`)
        props.updateDateDropdown(1);
    }

    return (
        <DateRanger
            onDateChange={onDateChange}
            rangePickerBtnText= {recType === Constants.EVENT_ANALYTICS.ALL_REC_DROP_DOWN || !isSameDay
                ?
                `${dateRange.start.format('LL')} - ${dateRange.end ? dateRange.end.format('LL') : dateRange.start.format('LL')}`
                : dateRange.start.format('LL')
            }
            dateRange={dateRange}
        />
    )
}

const mapStateToProps = (state) => {
    return {
        recurrences_list: state.Analytics.recurrences_list,
    }
}
const mapDispatchToProps = (dispatch) => {
    return {
        updateDateDropdown: selectedDateRange => dispatch(groupAction.updateAnalyticTabDropdownData(selectedDateRange)),
        updateAnalyticRangePickerData: selectedDateRange => dispatch(groupAction.updateAnalyticRangePickerData(selectedDateRange)),
    }
}
AnalyticsDatePicker = connect(mapStateToProps, mapDispatchToProps)(AnalyticsDatePicker);
AnalyticsDatePicker = withRouter(AnalyticsDatePicker);
export default AnalyticsDatePicker;