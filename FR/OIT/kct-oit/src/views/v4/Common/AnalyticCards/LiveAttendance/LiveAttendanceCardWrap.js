import React, {useState} from 'react'
import CardTab from '../../InfoCard/CardTab'
import InfoCard from '../../InfoCard/InfoCard'
import LiveAttendanceCard from './LiveAttendanceCard'
import "../AnalyticsCard.css"
import {connect} from "react-redux";

/**
 * @component
 * @class
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This wrapper component is used for live attendance card
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props props is used for redux data
 * @returns {JSX.Element}
 * @constructor
 */
let LiveAttendanceCardWrap = (props) => {

    const [recUuid, setRecUuid] = useState();

    let liveAttendanceCardTab = [];
    props.recurrences_analytics.map((list, i) => {

        liveAttendanceCardTab[i] = {
            label: list.rec_start_date.format("DD MMM"),
            rec_uuid: list.recurrence_uuid,
            component: <LiveAttendanceCard
                recUuid={recUuid}
            />
        }
    })

    return (
        <InfoCard className="LiveAttendenceCard analyticsCardCSS">
            <CardTab
                setRecUuid={setRecUuid}
                tabs={liveAttendanceCardTab}
                tabHeading={'Live Attendance'}
            />
        </InfoCard>
    )
}

const mapStateToProps = (state) => {
    return {
        recurrences_analytics: state.Analytics.recurrences_analytics,
    }
}

LiveAttendanceCardWrap = connect(mapStateToProps, null)(LiveAttendanceCardWrap);
export default LiveAttendanceCardWrap