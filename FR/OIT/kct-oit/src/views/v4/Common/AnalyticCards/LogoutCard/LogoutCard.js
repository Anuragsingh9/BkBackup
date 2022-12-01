import React, {useEffect, useState} from 'react'
import {Chart} from "react-google-charts";
import ChartOptionsHandler from "../ChartOptionsHandler"
import eventAction from "../../../../../redux/action/reduxAction/event";
import {connect} from "react-redux";
import Helper from "../../../../../Helper";
import AttendanceHelper from "../../../EventAnalytics/AttendanceHelper";
import NoDataCard from '../NoDataCard/NoDataCard';

/**
 * @component
 * @class
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used for live attendance card to show the peek attendance card
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Passed from parent component
 * @param {Event} props.current_event Object of event
 * @param {Array} props.recurrences_analytics Array of single event analytics
 * @param {String} props.recUuid recurrence uuid
 * @returns {JSX.Element}
 * @constructor
 */
let LogoutCard = (props) => {
    const [logoutChartData, setLogoutChartData] = useState([]);
    const [showNoDataIcon, setShowNoDataIcon] = useState(true)

    useEffect(() => {
        let recurrenceData;
        // fetch the current selected recurrence if not send the latest recurrence
        if (!props.recUuid) {
            recurrenceData = props.recurrences_analytics[0]
        } else {
            props.recurrences_analytics.map((recData) => {
                if (recData.recurrence_uuid === props.recUuid) {
                    recurrenceData = recData;
                }
            })
        }

        if (recurrenceData) {
            let timeInterval = Helper.timeHelper.prepareTimeIntervalsForChart(15, recurrenceData.rec_start_date, recurrenceData.rec_end_date);
            let chartData = AttendanceHelper.prepareDataForLiveAttenChart(recurrenceData, timeInterval);

            // Convert event duration in Date Time object and remove the logout and average users count data
            chartData = chartData.map((c, i) => {
                c.splice(1, 1)
                c.splice(2, 1)
                return c;
            })

            setLogoutChartData(chartData);
        }
    }, [props.recurrences_analytics, props.recUuid])

    useEffect(() => {
        logoutChartData.length > 1 ? setShowNoDataIcon(false) : setShowNoDataIcon(true)
    }, [logoutChartData])


    const LogoutChartData = [
        ["Time", "LogOut"],
        ...logoutChartData,
    ];


    let options = ChartOptionsHandler.columnChart({
        colors: ["#F39C12"]
    });
    options = {
        ...options,
        chartArea: {...options.chartArea, height: '70%'},
        hAxis: {
            ...options.hAxis,
            title: "Time",
        }
    }
    let customMinMaxApplyFlag = true;
    LogoutChartData.forEach((data, i) => {
        if ((data[1] !== 0 || data[2] !== 0 || data[3] !== 0) && i !== 0)
            customMinMaxApplyFlag = false;
    })
    if (customMinMaxApplyFlag) {
        options = ChartOptionsHandler?.columnChart({
            vAxis: {
                title: "User Count",
                minValue: 0,
                maxValue: 100,
            },
        })
    }




    return (
        <>
            {
                showNoDataIcon
                    ? <NoDataCard infotext="no_data_in_occurrence" />
                    :
                    <Chart
                        chartType="ColumnChart"
                        width="100%"
                        data={LogoutChartData}
                        options={options}

                    />
            }
        </>
    );
}

const mapStateToProps = (state) => {
    return {
        recurrences_analytics: state.Analytics.recurrences_analytics,
        current_event: eventAction.getCurrentEvent(state),
    }
}

LogoutCard = connect(mapStateToProps, null)(LogoutCard)
export default LogoutCard