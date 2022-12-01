import React from 'react'
import "./SideBar.css"
import CreateEventDropdownBtn from './CreateEventDropdownBtn/CreateEventDropdownBtn'
import DashboardBtn from './DashboardBtn/DashboardBtn'
import AnalyticsBtn from "./AnalyticsBtn/AnalyticsBtn";
import WaterFountainBtn from "./WaterFountainBtn/WaterFountainBtn";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is responsible for rendering all the components used in Side Bar
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parents
 * @returns {JSX.Element}
 * @constructor
 */
const SideBar = (props) => {
  const userOnSetPswd = window.location.pathname == "/oit/set-password";

  return (
    <>
      {!userOnSetPswd &&
        <div className='SidebarWrap'>
          <DashboardBtn {...props} />
          <CreateEventDropdownBtn {...props} />
          <AnalyticsBtn {...props} />
          <WaterFountainBtn {...props} />
        </div>
      }
    </>
  )
}

export default SideBar