import React from 'react';
import NavTabs from '../Common/NavTabs/NavTabs.js';
import Container from '@material-ui/core/Container';
import './Profile.css';
import SettingsIcon from '@material-ui/icons/Settings';
import ProfileCard from './ProfileCard/ProfileCard.js'
import ProfileCard1 from './ProfileCard/ProfileCard1.js'

import {useTranslation} from 'react-i18next';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a child component for rendering/displaying profile page of self user from Header's Dropdown
 * menu.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component is received route related props eg - history,location,match
 * @returns {JSX.Element}
 */
const Profile = (props) => {
    console.log('object', props)
    const {t} = useTranslation('details')
    const tabData = [
        {
            label: t('My Profile'),
            href: '',
            child: <ProfileCard {...props} /> // without redux form
            // with redux form 
            // child: <ProfileCard1 {...props}/>

        }
    ]

    return (
        <div className="tab-row ProfileNavTabWrap">
            <Container>
                <SettingsIcon />
                <NavTabs tabData={tabData} />
            </Container>
        </div>
    )
}

export default Profile