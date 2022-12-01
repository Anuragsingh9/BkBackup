import React, {useEffect, useState} from 'react';
import useEventAnalytics from "./Containers/EventAnalyticsContainer";
import {CircularProgress, Grid} from "@mui/material";
import eventV4Api from "../../../redux/action/apiAction/v4/event";
import {connect} from "react-redux";
import "./EventAnalytics.css"
import eventAction from '../../../redux/action/reduxAction/event';
import eventFormAction from '../../../redux/action/reduxAction/event';
import analyticsAction from "../../../redux/action/reduxAction/analytics";
import Helper from '../../../Helper';
import LiveAttendanceCardWrap from '../Common/AnalyticCards/LiveAttendance/LiveAttendanceCardWrap';
import RegistrationAndAttendenceCardWrap
    from '../Common/AnalyticCards/RegistrationAndAttendenceCard/RegistrationAndAttendenceCardWrap';
import EngagementCardWrap from '../Common/AnalyticCards/Engagement/EngagementCardWrap';
import PeakAttendanceCardWrap from '../Common/AnalyticCards/PeakAttendance/PeakAttendanceCardWrap';
import OverviewCardWrap from "../Common/AnalyticCards/Overview/OverviewCardWrap";
import moment from "moment-timezone";
import _ from "lodash";
import AnalyticsSkeleton from '../Skeleton/AnalyticsSkeleton';
import LoginCardWrap from '../Common/AnalyticCards/LoginCard/LoginCardWrap';
import LogoutCardWrap from '../Common/AnalyticCards/LogoutCard/LogoutCardWrap';
import AverageUserCardWrap from '../Common/AnalyticCards/AverageUser/AverageUserCardWrap';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for rendering components conditionally related to an event's analytics data.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parents
 * @param {Object} props.current_event Redux store data for current event
 * @param {Object} props.recurrences_list Redux store data for events recurrence list
 * @param {Object} props.range_picker_val Redux store data for events range picker value
 * @param {Function} props.addEvent Redux function to add event in redux store
 * @param {Function} props.updateAnalyticsRecList Redux function to update analytics recurrence list data
 * @param {Function} props.getEventAnalytics Redux function to get event analytics data
 * @returns {JSX.Element}
 * @constructor
 */
let EventAnalytics = (props) => {

    const [eventRecurrenceLine, setEventRecurrenceLine] = useState('')
    const [refresh, setRefresh] = useState(true);
    const [showSkeleton, setShowSkeleton] = useState(true)

    useEventAnalytics({...props, refresh, setRefresh, setShowSkeleton});

    //This hook will take current event recurrence data and prepare a line using helper method to show on analytic page
    useEffect(() => {
        if (props?.current_event?.event_recurrence !== null && props?.current_event?.event_recurrence !== undefined) {
            let current_event_recurrence = props?.current_event?.event_recurrence;
            let selectedWeekDay = Helper.convertNumberToWeekDay(current_event_recurrence.rec_selected_weekday);
            setEventRecurrenceLine(Helper.prepareRecurrenceLine(
                current_event_recurrence?.rec_interval,
                current_event_recurrence?.rec_type,
                current_event_recurrence?.rec_end_date,
                current_event_recurrence?.rec_month_date,
                selectedWeekDay,
                current_event_recurrence?.rec_month_type,
                current_event_recurrence?.rec_on_month_week,
                current_event_recurrence?.rec_on_month_week_day,
            ))
        }
    }, [props?.current_event?.event_type, props.recurrences_analytics])

    // Show skeleton while calculating data for analytics page
    useEffect(() => {
        if (_.has(props, ['recurrences_analytics']) && props?.recurrences_analytics.length > 0) {
            setTimeout(() => {
                setShowSkeleton(false)
            }, 2000);
        }
    }, [props?.recurrences_analytics])


    const eventDate = props?.current_event?.event_start_date;
    let dateMomentObj = new moment(eventDate)

    const refreshAnalyticsData = () => {
        setRefresh(true);
    }

    return (
        <>
            {showSkeleton ? <AnalyticsSkeleton />
                :
                <>
                    {props.current_event?.event_is_published === 1 ?
                        <>
                            {!props.current_event?.event_uuid
                                ?
                                <div className="wrap_lodder">
                                    {/* Show skeleton here */}
                                    < CircularProgress />
                                </div>
                                :
                                <>
                                    <Grid container xs={12} className="flex-Row analyticsWrap">
                                        <Grid item lg={12} xs={12}>
                                            <OverviewCardWrap />
                                        </Grid>
                                    </Grid>
                                    <Grid container spacing={1} xs={12} className='flex-Row'>
                                        <Grid item lg={6} xs={12} className='mx-10'>
                                            <RegistrationAndAttendenceCardWrap />
                                        </Grid>
                                        <Grid item lg={6} xs={12} className='mx-10'>
                                            <EngagementCardWrap />
                                        </Grid>
                                    </Grid>
                                    <Grid container spacing={1} xs={12} className='flex-Row'>
                                        <Grid item lg={4} xs={12} className='mx-10'>
                                            <LoginCardWrap
                                                className="p_0"
                                            />
                                        </Grid>
                                        <Grid item lg={4} xs={12} className='mx-10'>
                                            <LogoutCardWrap
                                                className="p_0"
                                            />
                                        </Grid>
                                        <Grid item lg={4} xs={12} className='mx-10'>
                                            <AverageUserCardWrap
                                                className="p_0"
                                            />
                                        </Grid>
                                    </Grid>
                                    <Grid container spacing={1} xs={12} className='flex-Row'>
                                        <Grid item lg={12} xs={12} className='mx-10'>
                                            <LiveAttendanceCardWrap
                                                className="p_0"
                                            />
                                        </Grid>
                                    </Grid>
                                </>


                            }
                        </>
                        :
                        <>
                            {"No Data To Display"}
                        </>
                    }
                </>
            }
        </>
    )
}

const mapStateToProps = (state) => {
    return {
        recurrences_list: state.Analytics.recurrences_list,
        recurrences_analytics: state.Analytics.recurrences_analytics,
        current_event: eventAction.getCurrentEvent(state),
        range_picker_val: state.Analytics.date_range,
        refreshPage: state.Analytics.refreshPage
    }
}
const mapDispatchToProps = (dispatch) => {
    return {
        addEvent: eventObject => dispatch(eventFormAction.addEvent(eventObject)),
        currentEventUuid: (data) => dispatch(eventFormAction.currentEventUuid(data)),
        getEventAnalytics: (eventUuid) => dispatch(eventV4Api.getEventAnalytics(eventUuid)),
        filterAnalyticsList: (eventUuid) => dispatch(analyticsAction.filterAnalyticsList(eventUuid)),
        updateAnalyticsRecList: (recurrencesList) => dispatch(analyticsAction.updateAnalyticsRecList(recurrencesList)),
        updateAnalyticsList: (analyticsList) => dispatch(analyticsAction.updateAnalyticsList(analyticsList)),
        analyticsDateRange: selectedDateRange => dispatch(analyticsAction.updateAnalyticsDropdownData(selectedDateRange)),
    }
}

EventAnalytics = connect(mapStateToProps, mapDispatchToProps)(EventAnalytics);
export default EventAnalytics;