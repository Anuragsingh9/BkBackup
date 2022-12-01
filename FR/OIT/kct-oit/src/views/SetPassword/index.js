import React from 'react';
import NavTabs from '../Common/NavTabs/NavTabs';
import SetPassword from './SetPassword';
import './SetPassword.css';
import SettingsIcon from '@material-ui/icons/Settings';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a horizontal tab container component for set password page. It allows Organiser (Pilot) who
 * are accessing the account for the first time to change his/her default password and set a desired password for their
 * Humannconnect account.
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @returns {JSX.Element}
 */
const setPswdMain = () => {
  const tabData = [
    {
      label: 'Set Password',
      href: '/set-password',
      child: <SetPassword />
    },
  ]

  return (
    <div className="setPswd_main">
      <span className="setPswdIconTab">
        <SettingsIcon />
      </span>
      <NavTabs tabId="user-tabs" tabData={tabData} />
    </div>
  )
}
export default setPswdMain
