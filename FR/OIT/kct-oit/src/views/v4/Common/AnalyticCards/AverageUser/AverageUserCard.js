import React, {useEffect, useState} from 'react'
import {Chart} from "react-google-charts";
import ChartOptionsHandler from "../ChartOptionsHandler"
import eventAction from "../../../../../redux/action/reduxAction/event";
import {connect} from "react-redux";
import Helper from "../../../../../Helper";
import AttendanceHelper from "../../../EventAnalytics/AttendanceHelper";
import NoDataCard from '../NoDataCard/NoDataCard';
import Constants from '../../../../../Constants';
import EventAnalyticsHelper from "../../../EventAnalytics/EventAnalyticsHelper";

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
let AverageUserCard = (props) => {

    const [averageUserChartData, setAverageUserChartData] = useState([]);
    const [showNoDataIcon, setShowNoDataIcon] = useState(true)
    const [options, setOptions] = useState(ChartOptionsHandler.columnChart({
        colors: ["#1ABC9C"]
    }));

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

            console.log('chartData-avg', chartData)
            // Convert event duration in Date Time object and remove the logout and average users count data
            chartData = chartData.map((c, i) => {
                c.splice(1, 2)
                return c;
            })

            setAverageUserChartData(chartData);
        }
    }, [props.recurrences_analytics, props.recUuid])

    useEffect(() => {
        console.log('averageUserChartData', averageUserChartData)
        averageUserChartData.length > 1 ? setShowNoDataIcon(false) : setShowNoDataIcon(true)

        setOptions({
            ...options,
            vAxis: {
                ...options.vAxis,
                title: "User Count",
                minValue: 0,
                maxValue: EventAnalyticsHelper.findMaxLimit(averageUserChartData) || 100,
            },
            chartArea: {...options.chartArea, height: '70%'},
            hAxis: {
                ...options.hAxis,
                title: "Time",
            }
        })
    }, [averageUserChartData])


    const liveAvgUserChartData = [
        ["Time", "Average"],
        ...averageUserChartData,
    ];

    return (
        <>
            {
                showNoDataIcon
                    ? <NoDataCard infotext="no_data_in_occurrence" />
                    :
                    <Chart
                        chartType="ColumnChart"
                        width="100%"
                        data={liveAvgUserChartData}
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

AverageUserCard = connect(mapStateToProps, null)(AverageUserCard)
export default AverageUserCard