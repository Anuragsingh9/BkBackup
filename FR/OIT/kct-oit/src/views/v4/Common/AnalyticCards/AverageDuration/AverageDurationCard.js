import React, {useEffect, useState} from "react";
import {Chart} from "react-google-charts";
import ChartOptionsHandler from "../ChartOptionsHandler"
import {connect} from "react-redux";
import eventAction from "../../../../../redux/action/reduxAction/event";
import moment from "moment-timezone"
import Helper from "../../../../../Helper";


const dummyData = [
  {
    "user_count": 2,
    "start_time": "2022-10-14 10:16:53",
    "duration": 199
  },
  {
    "user_count": 3,
    "start_time": "2022-10-14 10:20:12",
    "duration": 1023
  },
  {
    "user_count": 4,
    "start_time": "2022-10-14 10:37:15",
    "duration": 286
  },
  {
    "user_count": 2,
    "start_time": "2022-10-14 10:46:04",
    "duration": 31
  },
  {
    "user_count": 2,
    "start_time": "2022-10-14 11:50:12",
    "duration": 365
  },
  {
    "user_count": 2,
    "start_time": "2022-10-14 11:57:34",
    "duration": 80
  },
  {
    "user_count": 2,
    "start_time": "2022-10-14 12:00:24",
    "duration": 539
  }
]

let AverageDurationCard = (props) => {

  useEffect(() => {
    let startTime = moment(props.current_event?.event_start_time, 'hh:mm:ss a');
    let endTime = moment(props.current_event?.event_end_time, 'hh:mm:ss a');

    const result = Helper.timeHelper.prepareTimeIntervalsForChart(30, startTime, endTime)
    console.log('timeBarss', result)
  }, [props?.recurrences_list])



  const data = [
    [
      "Time",
      "Convo of 2",
      "Convo of 3",
      "Convo of 4",
    ],
    ["2004/05", 100, 450, 614],
    ["2005/06", 100, 100, 998],
    ["2006/07", 157, 998, 623],
    ["2007/08", 998, 450, 609],
    ["2008/09", 450, 366, 569],
  ];

  const options = ChartOptionsHandler.comboChart({
    title: "Average Conversation Duration",
    vAxis: {title: "Count"},
    // hAxis: {title: "Time"},
    bar: {groupWidth: 50},
    legend: {position: 'none'},
  });

  return (
    <Chart
      chartType="ComboChart"
      width="100%"
      data={data}
      options={options}
    />
  );
}

const mapStateToProps = (state) => {
  return {
    recurrences_list: state.Analytics.recurrences_list,
    recurrences_analytics: state.Analytics.recurrences_analytics,
    current_event: eventAction.getCurrentEvent(state),
  }
}


AverageDurationCard = connect(mapStateToProps, null)(AverageDurationCard);

export default AverageDurationCard
