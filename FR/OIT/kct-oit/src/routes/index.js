import React, {useEffect, useState} from 'react';
import {BrowserRouter, Switch, useHistory} from "react-router-dom";
import Login from '../views/Auth/Login/Login.js';
import AddUserContainer from '../views/UserSettings/index.js';
import Dashboard from '../views/Dashborad/Dashboard.js';
import "./ProgressLodder.css";

import ProfileContainer from '../views/Profile/ProfileContainer.js';
import CreateEvent from '../views/CreateEvent/CreateEvent.js';
import AuthRoute from './AuthLayout.js';
import SimpleRoute from './SimpleLayout'
// import ManageOrg from '../views/ManageOrg/index.js'
// import OrgTag from '../views/OrgTag/OrgTags';
import OrgTagsWrap from '../views/OrgTag/index.js'
import {connect} from 'react-redux';
import UpdatePassword from '../views/UpdatePassword/UpdatePassword.js';
import EventSetting from '../views/EventSetting/index.js';
import userAction from '../redux/action/apiAction/user.js';
import groupAction from '../redux/action/apiAction/group.js';
import userReduxAction from '../redux/action/reduxAction/user.js';
import Helper from '../Helper.js';
import {useAlert} from 'react-alert';
import _ from 'lodash';
import ManageOrg from '../views/ManageOrg/index.js';
import SignIn from '../views/SignIn/SignIn';
import GroupCreation from '../views/Group/GroupCreation/GroupCreationTab';
import GroupList from '../views/Group/GroupList/GroupList.js';
import SetPassword from '../views/SetPassword/index'
import ForgetPassword from '../views/ForgetPassword/ForgetPassword'
import ResetPassword from '../views/ResetPassword/ResetPassword'
import EventsList from "../views/EventList/EventsList";
import {CircularProgress} from '@material-ui/core';
import EventManageWrap from "../views/v4/EventManage/EventManageWrap.js"
import Constants from "../Constants";
import groupReduxAction from "../redux/action/reduxAction/group";
import UserManageWrap from "../views/v4/UserManagement/UserManageWrap";
import AnalyticsWrap from "../views/v4/Analytics/AnalyticsWrap";

const queryString = require('query-string');


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for declaring the routes of the different components.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props
 * @param {Function} props.getSelfUserById Function to get data using ID
 * @param {Function} props.labels Function to get labels data.
 * @param {Function} props.setUser Function to save loggedIn data.
 * @param {UserDataObject} props.user_badge Object that contain user details.
 * @returns {JSX.Element}
 */
const Routes = (props) => {
    console.log('props141', props)
    // const loc = useLocation();
    const history = useHistory()
    const alert = useAlert();
    const [loading, setLoading] = useState(true);


    useEffect(() => {
        const publicUrls = ['oit/access', '/oit/signin', "/oit/reset-view", "/oit/forget-password"]

        if (localStorage.getItem('oitToken')) {
            const token = localStorage.getItem('oitToken');
            loginCheck(token)
        } else {
            setLoading(false);
            var params = queryString.parse(window.location.search);
            if (!_.has(params, ['token'])) {
                const newLoc = window.location.href.split('/oit/');

                const newLocation = newLoc[0];
                if (_.includes(publicUrls, window.location.pathname)) {

                } else if (newLocation != window.location.href) {
                    window.location.replace(`${newLocation}/oit/signin`)

                }
            } else {
                // alert('kkkkkkkk')
                // const loc = window.location.href;
                // let newLoc ='';
                // if(loc && loc.includes('oit')) {
                //     newLoc = loc.split('/oit')[0];
                // }else{
                //     newLoc = loc
                // }
                //     localStorage.clear();
                // if (newLoc) {
                //     window.location.replace(`${newLoc}/signin`)
                // }
            }
        }
    }, [])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function checks status of login
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} tokenToken ID
     * @method
     */
    const loginCheck = (token) => {
        try {
            // setLoading(true);
            const data = {
                token: token, send_labels: 1
            }
            props.getSelfUserById(data).then((res) => {
                const meta = res.data.meta
                const response = res.data.data
                const groupKey = res.data.data.current_group_key
                props.getSingleGroup(groupKey).then((grpRes) => {
                    // props.updateCurrentGroup(grpRes.data.data);
                    props.updateOrganisationName(res.data.meta.organisation_name);
                    localStorage.setItem('Current_group_data', JSON.stringify(grpRes.data.data));
                })
                if (meta) {
                    props.labels(meta)
                }
                localStorage.setItem('user_data', JSON.stringify(res.data.data));
                localStorage.setItem('userId', res.data.data.id);
                localStorage.setItem('oitToken', token);
                props.setUser(res.data.data)
                props.setAppSettings({
                    is_multi_group_enable: res.data.meta.is_multi_group_enable,
                })

                props.setUsersMetaData(meta)
                const newLoc = window.location.href.split('/oit/');

                const newLocation = newLoc[0];
                const grp = localStorage.getItem('Current_group_data');
                const grpData = JSON.parse(grp)
                console.log('anuGrou', grpData.group_key);
                const grpKey = grpData.group_key;
                if (newLoc[1] == '') {
                    window.location.replace && window.location.replace(`${newLocation}/oit/${grpKey}/dashboard`)
                }
                const curr_grp = localStorage.getItem("user_data")
                const idData = JSON.parse(curr_grp)
                const login_count = idData.login_count
                //if user not set password then should go set-password page first
                if (login_count == 0 && newLoc[1] !== "set-password") {
                    if (newLoc[1] !== "set-password") window.location.replace("/oit/set-password")
                }

                //if user already set his password  then should not go set password page again
                if (newLoc[1] == "set-password" && login_count == 1) {
                    window.location.replace && window.location.replace(`/${newLocation}/oit/${grpKey}/dashboard`)
                    // window.history.back();
                }

                // if (res && res.data.status && res.data.status == 403) {
                //     if (res.data.code && res.data.code == 1001) {
                //         window.location.replace && window.location.replace(`${newLocation}/oit/set-passwordd`)
                //
                //     }
                // }


                setLoading(false);
            }).catch((err) => {
                console.log("blankkkkkk", err)
                if (err.response && err.response.status === 403) {
                    if (err.response.data.code === 1001) {
                        if (window.location.pathname === "/oit/set-password") {

                        } else {
                            window.location.replace(`/oit/set-password`)
                        }
                    }
                } else {

                    alert.show(Helper.handleError(err), {type: 'error'})
                }
            })

        } catch (err) {
            console.log("blankkkkkk", err)
            alert.show(Helper.handleError(err), {type: 'error'})
        }
    }


    if (loading) {
        return (
            <div class="wrap_lodder">
                < CircularProgress />
            </div>
        )
    }

    const {user_badge} = props;

    return (
        <BrowserRouter basename="oit">
            <Switch>

                <SimpleRoute exact path="/reset-view" name="Reset Password" component={ResetPassword} />
                <SimpleRoute exact path="/forget-password" name="Forget Password" component={ForgetPassword} />
                <AuthRoute exact path="/set-password" name="Set Password" component={SetPassword} />
                <SimpleRoute exact path="/signin" name="Sign In" component={SignIn} />
                <AuthRoute exact path="/:gKey/dashboard/" name="Dashboard" component={Dashboard} />
                <AuthRoute path="/access" name="Access Token" headerLoad={false} component={Login} />
                <AuthRoute exact path="/:gKey/profile" name="Profile" component={ProfileContainer} />
                {/*<AuthRoute exact path="/:gKey/event-list/" name="Event List" component={EventList}/>*/}
                <AuthRoute exact path="/:gKey/event-list/:eventType?" name="Event List" component={EventsList} />
                {/*<AuthRoute exact path="/:gKey/event-list/past-events" name=" Past Event" component={PastEventList}/>*/}
                {/*<AuthRoute exact path="/:gKey/event-list/future-events" name="Future Event" component={FutureEventList}/>*/}
                <AuthRoute exact path="/:gKey/manage-groups" name="Manage Groups" component={GroupList} />
                {/*<AuthRoute exact path="/manage-group-users" name="manage group users" component={ManageGroupUsers}/>*/}
                <AuthRoute exact path="/:gKey/create-group" name="Create Group" mode={"create"}
                           component={GroupCreation} />
                {(_.has(user_badge, ['is_organiser']) && user_badge.is_organiser) &&
                <React.Fragment>
                    <AuthRoute exact path="/:gKey/manage-groups" name="Manage Groups" component={GroupList} />
                    <AuthRoute exact path="/:gKey/create-group" name="Create Group" mode={"create"}
                               component={GroupCreation} />
                    <AuthRoute path="/:gKey/edit-group" name="Create Group" mode={"edit"} component={GroupCreation} />
                    <AuthRoute exact path="/:gKey/user-profile" name="Profile" component={ProfileContainer} />
                    <AuthRoute path="/:gKey/user-setting" name="Access Token" component={AddUserContainer} />

                    <AuthRoute exact path="/:gKey/update-password" name="Update password" component={UpdatePassword} />

                    <AuthRoute exact path="/:gKey/org-tags" name="Org Tags" component={OrgTagsWrap} />
                    {/* <AuthRoute exact path = "/add" name = "Add Participants" component = {AddParticipants} /> */}
                    <AuthRoute exact path="/:gKey/create-event" name="Create Event" component={CreateEvent} />

                    <AuthRoute exact path="/:gKey/manage-org" name="Manage Org" component={ManageOrg} />

                    <AuthRoute exact path="/:gKey/edit-event/:event_uuid" name="Edit Event" component={CreateEvent} />
                    <AuthRoute exact path="/:gKey/access-event/:event_uuid" name="Edit Event" component={CreateEvent} />

                    <AuthRoute exact path="/:gKey/event-setting" name="Event Setting" component={EventSetting} />
                    <AuthRoute exact path="/:gKey/event-setting/technical-setting" name="Technical Setting"
                               component={EventSetting} />

                    {/* Version 4 routes here */}
                    <AuthRoute
                        exact
                        path="/:gKey/v4/event-create"
                        name={"Event Create"}
                        component={EventManageWrap}
                    />
                    <AuthRoute
                        exact
                        path="/:gKey/v4/event/media/:event_uuid"
                        name={"Event Media"}
                        tabValue={Constants.eventTabType.MEDIA}
                        component={EventManageWrap}
                    />
                    <AuthRoute
                        exact
                        path="/:gKey/v4/event/user/:event_uuid"
                        name={"Event User"}
                        tabValue={Constants.eventTabType.USER}
                        component={EventManageWrap}
                    />
                    <AuthRoute
                        exact
                        path="/:gKey/v4/event/user/event-team/:event_uuid"
                        name={"Event Team"}
                        tabValue={Constants.eventTabType.USER}
                        userTabValue={Constants.userManagementTabType.EVENT_TEAM}
                        component={EventManageWrap}
                    />
                    <AuthRoute
                        exact
                        path="/:gKey/v4/event/user/participants/:event_uuid"
                        name={"Participants"}
                        tabValue={Constants.eventTabType.USER}
                        userTabValue={Constants.userManagementTabType.PARTICIPANTS}
                        component={EventManageWrap}
                    />
                    <AuthRoute
                        exact
                        path="/:gKey/v4/event/analytics/:event_uuid"
                        name={"Analytics"}
                        tabValue={Constants.eventTabType.ANALYTICS}
                        component={EventManageWrap}
                    />
                    <AuthRoute
                        exact
                        path="/:gKey/v4/analytics"
                        name={"Analytics"}
                        tabValue={Constants.analyticsTabType.ENGAGEMENT}
                        component={AnalyticsWrap}
                    />
                    <AuthRoute exact
                               path="/:gKey/v4/event-update/:event_uuid"
                               name={"Event Update"}
                               component={EventManageWrap}
                    />


                </React.Fragment>

                }


            </Switch>
        </BrowserRouter>

    )

}

const mapDispatchToProps = (dispatch) => {
    return {
        getSelfUserById: (id) => dispatch(userAction.getSelfUserById(id)),
        setUser: (data) => dispatch(userReduxAction.setUserData(data)),
        setAppSettings: (data) => dispatch(userReduxAction.setAppSettings(data)),
        labels: (data) => dispatch(userReduxAction.setLabelData(data)),
        setUsersMetaData: (data) => dispatch(userReduxAction.setUsersMetaData(data)),
        getSingleGroup: (groupKey) => dispatch(groupAction.getSingleGroupData(groupKey)),
        updateCurrentGroup: (groupData) => dispatch(groupReduxAction.updateCurrentGroup(groupData)),
        updateOrganisationName: (name) => dispatch(groupReduxAction.updateOrganisationName(name)),
    }
}

const mapStateToProps = (state) => {
    return {
        user_badge: state.Auth.userSelfData
    };
};
export default connect(mapStateToProps, mapDispatchToProps)(Routes);