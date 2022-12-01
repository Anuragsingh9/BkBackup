import React from 'react'
import moment from "moment-timezone";

const AnalyticsDate = (props) => {
  let date = new moment(props.analyticsDate);
  return (
    <p>{date.format('MMMM D, YYYY')}</p>
  )
}

export default AnalyticsDate