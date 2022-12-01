import React, {useState} from 'react';
import Profile from './Profile';
import ProfileCard from './ProfileCard/ProfileCard.js'
import NavTabs from '../Common/NavTabs/NavTabs.js';
import {useTranslation} from 'react-i18next';
import Container from '@material-ui/core/Container';
import _ from 'lodash';
import ProfileIcon from '../Svg/ProfileIcon';
import './Profile.css';
import Constants from "../../Constants";
import BreadcrumbsInput from "../v4/Common/Breadcrumbs/BreadcrumbsInput";
import {connect} from "react-redux";

const queryString = require('query-string');

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is container component for user's profile page. This is called when the organiser (pilot)
 * clicks on the Profile from  the Header Dropdown.This would render on ProfileCard component when we pass user id in
 * props or else would render on Profile component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component is received route related props eg - history,location,match
 * @return {JSX.Element}
 */
let ProfileContainer = (props) => {
    // for localization
    const {t} = useTranslation('details');
    var params = queryString.parse(props.location.search);
    const [targetUserData, setTargetUserData] = useState({fname: null, lname: null});

    /**
     * @const
     * @returns array
     */
    const tabData = [
        {
            label: t('User Profile'),
            href: '',
            child: <ProfileCard
                targetUserData={targetUserData}
                setTargetUserData={setTargetUserData}
                id={params.id}
                {...props}
            />
        }
    ]

// to get parameter form url search query
    var params = queryString.parse(props.location.search);

    // condition based rendering for profile page with or with out id
    if (_.has(params, ['id'])) {
        return (
            <>
                <BreadcrumbsInput
                    links={[
                        // Constants.breadcrumbsOptions.GROUPS_LIST,
                        Constants.breadcrumbsOptions.GROUP_NAME,
                        Constants.breadcrumbsOptions.USERS_LIST,
                        Constants.breadcrumbsOptions.OTHER_USER_NAME
                    ]}
                    otherUser={targetUserData}
                />
                <div className="tab-row ProfileNavTabWrap">
                    <Container>
                        <ProfileIcon />
                        <NavTabs tabData={tabData} />
                    </Container>
                </div>
            </>
        )
    } else {
        return (
            <>
                <BreadcrumbsInput
                    links={[
                        // Constants.breadcrumbsOptions.GROUPS_LIST,
                        Constants.breadcrumbsOptions.GROUP_NAME,
                        Constants.breadcrumbsOptions.USERS_LIST,
                        Constants.breadcrumbsOptions.SELF_USER,
                    ]}
                />
                <Profile {...props} />
            </>
        )
    }
}

const mapStateToProps = (state) => {
    return {
        userGroupId: state.Auth.userSelfData,
    };
};

ProfileContainer = connect(mapStateToProps)(ProfileContainer);

export default ProfileContainer;