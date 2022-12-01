import React, {useState} from 'react'
import CardTab from '../../InfoCard/CardTab'
import InfoCard from '../../InfoCard/InfoCard'
import LogoutCard from './LogoutCard'
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
let LogoutCardWrap = (props) => {

    const [recUuid, setRecUuid] = useState();

    let LogoutCardTab = [];
    props.recurrences_analytics.map((list, i) => {

        LogoutCardTab[i] = {
            label: list.rec_start_date.format("DD MMM"),
            rec_uuid: list.recurrence_uuid,
            component: <LogoutCard
                recUuid={recUuid}
            />
        }
    })

    return (
        <InfoCard className="LiveAttendenceCard analyticsCardCSS">
            <CardTab
                setRecUuid={setRecUuid}
                tabs={LogoutCardTab}
                tabHeading={'Logout'}
            />
        </InfoCard>
    )
}

const mapStateToProps = (state) => {
    return {
        recurrences_analytics: state.Analytics.recurrences_analytics,
    }
}

LogoutCardWrap = connect(mapStateToProps, null)(LogoutCardWrap);
export default LogoutCardWrap