import React, {useState} from 'react'
import CardTab from '../../InfoCard/CardTab'
import InfoCard from '../../InfoCard/InfoCard'
import AverageUserCard from './AverageUserCard'
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
let AverageUserCardWrap = (props) => {

    const [recUuid, setRecUuid] = useState();

    let AverageUserCardTab = [];
    props.recurrences_analytics.map((list, i) => {

        AverageUserCardTab[i] = {
            label: list.rec_start_date.format("DD MMM"),
            rec_uuid: list.recurrence_uuid,
            component: <AverageUserCard
                recUuid={recUuid}
            />
        }
    })

    return (
        <InfoCard className="LiveAttendenceCard analyticsCardCSS">
            <CardTab
                setRecUuid={setRecUuid}
                tabs={AverageUserCardTab}
                tabHeading={'Average'}
            />
        </InfoCard>
    )
}

const mapStateToProps = (state) => {
    return {
        recurrences_analytics: state.Analytics.recurrences_analytics,
    }
}

AverageUserCardWrap = connect(mapStateToProps, null)(AverageUserCardWrap);
export default AverageUserCardWrap