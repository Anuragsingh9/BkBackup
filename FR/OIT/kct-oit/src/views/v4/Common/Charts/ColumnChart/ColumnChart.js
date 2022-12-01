import React from "react";
import {Chart} from "react-google-charts";


export const data = [
  ["Roles", "Executive", "Manager", "Employee", "Other"],
  ["Registration", 4, 4, 4, 4],
  ["Attendance", 3, 1, 4, 3],
];

export const options = {
  title: "",
  chartArea: {width: "60%", height: "70%"},
  isStacked: true,
  backgroundColor: '#E4E4E4',
  bar: {groupWidth: "40"},
  legend: {position: "top"},
  hAxis: {
    //   title: "Total Population",
    minValue: 0,
  },
  vAxis: {
    //   title: "City",
  },
};

const ColumnChart = () => {
  return (
    <Chart chartType="ColumnChart" height="400px" data={data} options={options} />
  )
}

export default ColumnChart
