import React from 'react'
import CardTab from '../../InfoCard/CardTab'
import InfoCard from '../../InfoCard/InfoCard'
import AverageDurationCard from './AverageDurationCard.js'
import "../AnalyticsCard.css"

const tabs = [
    {label: "10 Oct", component: <AverageDurationCard />},
    {label: "11 Oct", component: <AverageDurationCard />},
    {label: "12 Oct", component: <AverageDurationCard />},
    {label: "13 Oct", component: <AverageDurationCard />},
    {label: "14 Oct", component: <AverageDurationCard />},
    {label: "15 Oct", component: <AverageDurationCard />},
]
const AverageDurationCardWrap = () => {
    return (
        <InfoCard className="AverageDurationCard analyticsCardCSS">
            average duration
            <CardTab tabs={tabs} />
        </InfoCard>
    )
}

export default AverageDurationCardWrap