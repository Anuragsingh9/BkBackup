import React, {useState} from 'react'
import CardTab from '../../InfoCard/CardTab'
import InfoCard from '../../InfoCard/InfoCard'
import LoginCard from './LoginCard'
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
let LoginCardWrap = (props) => {

    const [recUuid, setRecUuid] = useState();

    let LoginCardTab = [];
    props.recurrences_analytics.map((list, i) => {

        LoginCardTab[i] = {
            label: list.rec_start_date.format("DD MMM"),
            rec_uuid: list.recurrence_uuid,
            component: <LoginCard
                recUuid={recUuid}
            />
        }
    })

    return (
        <InfoCard className="LiveAttendenceCard analyticsCardCSS">
            <CardTab
                setRecUuid={setRecUuid}
                tabs={LoginCardTab}
                tabHeading={'Login'}
            />
        </InfoCard>
    )
}

const mapStateToProps = (state) => {
    return {
        recurrences_analytics: state.Analytics.recurrences_analytics,
    }
}

LoginCardWrap = connect(mapStateToProps, null)(LoginCardWrap);
export default LoginCardWrap