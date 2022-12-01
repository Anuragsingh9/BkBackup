import React from 'react'
import EngagementCard from '../../EngagementCard/EngagementCard'
import CardTab from '../../InfoCard/CardTab'
import InfoCard from '../../InfoCard/InfoCard'

const tabs = [
  {label: "Conversations", component: <EngagementCard />}
]
const EngagementCardWrap = () => {
  return (
    <InfoCard className="engagementCardWrapCard analyticsCardCSS">
      <CardTab tabs={tabs} />
    </InfoCard>
  )
}

export default EngagementCardWrap