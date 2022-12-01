import React from 'react'
import CardTab from '../../InfoCard/CardTab'
import InfoCard from '../../InfoCard/InfoCard'
import OverviewCard from "./OverviewCard";
import "../AnalyticsCard.css"

const tabs = [
    {label: "Overview", component: <OverviewCard />},
]
const OverviewCardWrap = () => {
    return (
        <InfoCard className="analyticsCardCSS">
            <CardTab tabs={tabs} />
        </InfoCard>
    )
}

export default OverviewCardWrap