import React, {useEffect, useState} from 'react'
import {Chart} from "react-google-charts";
import ChartOptionsHandler from "../ChartOptionsHandler"
import eventAction from "../../../../../redux/action/reduxAction/event";
import {connect} from "react-redux";
import Helper from "../../../../../Helper";
import Constants from "../../../../../Constants";
import AttendanceHelper from "../../../EventAnalytics/AttendanceHelper";
import NoDataCard from '../NoDataCard/NoDataCard';

/**
 * @component
 * @class
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used for peek attendance card to show the peek attendance card
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Passed from parent component
 * @param {Event} props.current_event Object of event
 * @param {Array} props.recurrences_analytics Array of single event analytics
 * @param {String} props.recUuid recurrence uuid
 * @param {String} props.gradeOption selected grade option
 * @returns {JSX.Element}
 * @constructor
 */
let PeekAttendanceCard = (props) => {

    const [peekAtttenChartData, setPeekAtttenChartData] = useState([]);
    const [showNoDataIcon, setShowNoDataIcon] = useState(true);
    console.log('peekAtttenChartData', peekAtttenChartData)

    let chartDataForManager = [];
    let chartDataForEmployee = [];
    let chartDataForExecutive = [];
    let chartDataForOther = [];
    let chartDataForAllGrade = [];

    useEffect(() => {
        let eventTime = Helper.timeHelper.prepareEventStartEndTime(props.current_event);

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

            let durationGap = Helper.timeHelper.prepareChartIntervalGap(recurrenceData.rec_start_date, recurrenceData.rec_end_date);
            let timeIntervals = Helper.timeHelper.prepareTimeIntervalsForChart(durationGap, recurrenceData.rec_start_date, recurrenceData.rec_end_date);

            // Prepare the attendance  data for chart
            let data = AttendanceHelper.prepareDataForPeekAttenChart(recurrenceData, timeIntervals);

            chartDataForAllGrade = data.allGradeChartData;
            chartDataForManager = data.chartDataForManager;
            chartDataForEmployee = data.chartDataForEmployee;
            chartDataForExecutive = data.chartDataForExecutive;
            chartDataForOther = data.chartDataForOther;

            if (Constants.gradeOptions.ALL === props.gradeOption) {
                setPeekAtttenChartData(chartDataForAllGrade);
            }
            if (Constants.gradeOptions.MANAGER === props.gradeOption) {
                setPeekAtttenChartData(chartDataForManager);
            }
            if (Constants.gradeOptions.EMPLOYEE === props.gradeOption) {
                setPeekAtttenChartData(chartDataForEmployee);
            }
            if (Constants.gradeOptions.EXECUTIVE === props.gradeOption) {
                setPeekAtttenChartData(chartDataForExecutive);
            }
            if (Constants.gradeOptions.OTHER === props.gradeOption) {
                setPeekAtttenChartData(chartDataForOther);
            }

        }
    }, [props.recurrences_analytics, props.recUuid, props.gradeOption])

    useEffect(() => {
        let totalAttCount = 0;
        if (peekAtttenChartData) {
            peekAtttenChartData.map((attendance) => {
                totalAttCount += attendance[1]
            })
        }
        totalAttCount >= 1 ? setShowNoDataIcon(false) : setShowNoDataIcon(true)
        console.log('totalAttCount', totalAttCount)
    }, [peekAtttenChartData])


    const peekChartData = [
        ["Time", "LogIn"],
        ...peekAtttenChartData,
    ];

    let options = ChartOptionsHandler.columnChart();
    let flag = true;
    peekChartData.forEach((data, i) => {
        if (data[1] !== 0 && i !== 0)
            flag = false;
    })
    if (flag) {
        options = ChartOptionsHandler?.columnChart({
            vAxis: {
                title: "User Count",
                minValue: 0,
                maxValue: 100,
            },
        })
    }

    console.log('finalData - P', peekChartData)
    return (
        <>
            {
                showNoDataIcon
                    ? <NoDataCard />
                    : <Chart
                        chartType="ColumnChart"
                        width="100%"
                        data={peekChartData}
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

PeekAttendanceCard = connect(mapStateToProps, null)(PeekAttendanceCard)
export default PeekAttendanceCard;