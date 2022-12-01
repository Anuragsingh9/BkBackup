import React from 'react'
import {IconButton} from '@material-ui/core'
import DashboardLineIcon from '../../Svg/DashboardLineIcon'
import { useParams } from 'react-router-dom'

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is component is responsible for rendering the Dashboard Icon on Side Bar component
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent components
 * @returns {JSX.Element}
 * @constructor
 */
const DashboardBtn = (props) => {
    const {gKey} = useParams();

    const redirectToDashboard = ()=>{
        props.history.push(`/${gKey}/dashboard`);
    }
  return (
    <IconButton onClick={redirectToDashboard}>
        <DashboardLineIcon />
    </IconButton>
  )
}

export default DashboardBtn