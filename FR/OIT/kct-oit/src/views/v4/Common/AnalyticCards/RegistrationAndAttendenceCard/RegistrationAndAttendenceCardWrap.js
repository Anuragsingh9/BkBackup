import React from 'react'
import CardTab from '../../InfoCard/CardTab'
import InfoCard from '../../InfoCard/InfoCard'
import RegistrationCard from '../../RegistrationCard/RegistrationCard'
// import LiveAttendanceCard from './LiveAttendanceCard'

const tabs = [
  {label: "Registrations & Attendance", component: <RegistrationCard />}
]
const RegistrationAndAttendenceCardWrap = () => {
  return (
    <InfoCard className="registrationAndAttendenceCard analyticsCardCSS">
      <CardTab tabs={tabs} />
    </InfoCard>
  )
}

export default RegistrationAndAttendenceCardWrap