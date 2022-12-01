import React, {useEffect, useRef, useState} from 'react';
import newInterfaceActions from '../../../../redux/actions/newInterfaceAction';
import ToolBar from '../ToolBar/ToolBar.js';
import Helper from '../../../../Helper.js';
import {Provider as AlertContainer, useAlert } from 'react-alert';
import {connect} from 'react-redux';
import Svg from '../../../../Svg';
import _ from 'lodash';
// import '../ToolBar/ToolBar.css';
import {useTranslation} from 'react-i18next'
// check call status reject/accept variable
let statusInterval = null;
// call success interval
let successInterval = null;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is for rendering/displaying an user Tile in grid.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Function} props.callOff To show or hide the calling popup when calling to other user
 * @param {UserBadge} props.currentUser Current user badge data
 * @param {UserBadge} props.event_badge User badge details
 * @param {GraphicsData} props.event_roles [State] This variable holds the current graphics data set in redux
 * @param {InterfaceSpaceData} props.event_space Current spaces data
 * @param {UserBadge} props.spaceHost Space host data
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
let UserTile = (props) => {
    const {t} = useTranslation('grid');
    // space host data and current user
    const {spaceHost, currentUser} = props;
    // Initialisation fo message / alert ref to show alerts on success or error.
    const alertRef = useAlert();
    // tile is hovered state
    const [hovered, setHover] = useState(false);
    // toolbar is hovered state
    const [toolbarHovered, setToolHover] = useState(false);
    const [buttonState, setButton] = useState(false);
    const [vipIcon, setVipIcon] = useState(null);
    const [teamIcon, setTeamIcon] = useState(null);
    const [expertIcon, setExpertIcon] = useState(null)


    useEffect(() => {
        making.current = props.makingCall;
    }, [props.makingCall]);


    let making = useRef(props.makingCall);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function handles state(setHover) of mouse hover on toolbar on onMouseLeave event.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const leaveUser = () => {
        setTimeout(() => {
            if (!toolbarHovered) {
                setHover(false);
            }
        }, 100)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles out side click, for touch devices.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const clickReset = () => {
        setHover(false);
        setToolHover(false);
    }

    useEffect(() => {
        setRoleData()
    }, [props])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the user data like Organiser tags,User tags,fname,lname,company and union in the grid
     * section.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {UserBadge} currentUser Current user badge object
     */
    const updateGridUser = (currentUser) => {
        props.setFocusUser(currentUser);

    }

    useEffect(() => {
        if (toolbarHovered) {
            updateGridUser(currentUser);
        } else {
            props.setFocusUser(null);
        }
    }, [hovered, toolbarHovered]);


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update different states(setVipIcon,setExpertIcon,setTeamIcon) related to the event role icon.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const setRoleData = () => {
        let {vip_icon, expert_icon, business_team_icon, business_team_altImage} = props.event_roles;
        if (vip_icon && vip_icon !== null) {
            setVipIcon(vip_icon);
        }
        if (expert_icon && expert_icon !== null) {
            setExpertIcon(expert_icon);
        }
        if (business_team_icon && business_team_icon !== null) {
            setTeamIcon(business_team_icon)
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check status of call made to another user for conversation.
     * It checks whether the called user id exists or not in conversation users.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id User id
     */
    const checkInviteStatus = (id) => {
        successInterval = setInterval(() => {
            if (props.event_space.current_joined_conversation !== null) {
                const {conversation_users} = props.event_space.current_joined_conversation;
                if (!_.isEmpty(conversation_users)) {
                    // checking if user exists in conversation users
                    const exist = conversation_users.filter((item) => (item.user_id == id))
                    if (!_.isEmpty(exist)) {
                        clearTimeout(statusInterval);
                        props.callOff();
                        clearInterval(successInterval);
                    }
                } else {
                    props.callOff();
                }
            } else {
                props.callOff();
            }
        }, 2000);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check the user role in event and accordingly return the classes.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {UserBadge} item User object.
     */
    const userClass = (item) => {
        const {event_role, is_vip} = item;
        switch (event_role) {
            case 0:
                if (is_vip) {
                    return 'simple-vip'
                } else {
                    return ''
                }
            case 1:
                return 'team-user'
            case 2:
                return 'expert-user'

            default:
                return ''
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check the success status of the call.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id User id to check the invite success status
     **/
    const checkInviteSucess = (id) => {
        props.callTrigger();
        checkInviteStatus(id);
        statusInterval = setTimeout(() => {
            if (props.event_space.current_joined_conversation !== null) {
                const {conversation_users} = props.event_space.current_joined_conversation;
                if (!_.isEmpty(conversation_users)) {
                    const exist = conversation_users.filter((item) => (item.user_id == id))
                    if (_.isEmpty(exist)) {
                        if (making.current) {
                            alertRef.show(t("Participant is not available now"));
                        }
                        props.callOff();
                    }
                } else {
                    props.callOff();
                }
            } else {
                props.callOff();
            }
        }, 65000)
    }



    // if user is self user it should not be displayed in grid
    if (_.has(props.event_badge, ['user_id']) && _.has(currentUser, ['user_id']) &&
        currentUser.user_id == props.event_badge.user_id) {
        return <AlertContainer
            ref={alertRef}
            {...Helper.alertOptions}
        />;
    }
    // if a user is space host it should not be displayed in grid
    if (_.has(currentUser, ['user_id']) && !_.isEmpty(spaceHost) &&
        _.has(spaceHost[0], ['user_id']) && spaceHost[0].user_id == currentUser.user_id) {
        return <AlertContainer
            ref={alertRef}
            {...Helper.alertOptions}
        />;

    }

    return (
        <div className="col-1 col-sm-1 col-md-1 user-profile" style={{padding: '5px', position: 'relative'}}>
            {/* <AlertContainer
                ref={alertRef}
                {...Helper.alertOptions}
            /> */}

            {_.has(currentUser, ['user_avatar']) && currentUser.user_avatar ?
                <img
                    onClick={() => setHover(true)}
                    onMouseEnter={() => setHover(true)}
                    onMouseLeave={(e) => leaveUser()} className="no-texture" src={currentUser.user_avatar}
                />
                :
                <div
                    onClick={() => setHover(true)}
                    onMouseEnter={() => setHover(true)}
                    onMouseLeave={(e) => leaveUser()}
                    className="grid-user-dp-name no-border"
                >
                    {Helper.nameProfile(currentUser.user_fname, currentUser.user_lname)}
                </div>
            }

            <div className={`${userClass(currentUser)} ${props.is_dummy == 1 ? "dummy_user_wrap" : ""}`}>
                {_.has(currentUser, ['is_dummy']) && currentUser.is_dummy == 1
                && process.env.REACT_APP_HE_PROJECT_ENV !== 'production' &&
                <div title={t("Dummy user")} className="svgicon dummy-user"
                     dangerouslySetInnerHTML={{__html: Svg.ICON.dummy_icon}} />}

                {_.has(currentUser, ['event_role']) && userClass(currentUser) && (userClass(currentUser).includes('team')
                    || userClass(currentUser).includes('vip')) &&
                <div className="svgicon" dangerouslySetInnerHTML={userClass(currentUser).includes('team') ?
                    teamIcon !== null ? {
                            __html: `<div class="customRoleIcon" 
                                            style="background-image:url(${teamIcon});background-size:cover;"></div>`
                        }
                        : {__html: Svg.ICON.team_user} :
                    vipIcon != null ? {
                            __html: ` <div class="customRoleIcon" 
                                            style="background-image:url(${vipIcon}); background-size:cover;"></div>`
                        }
                        : {__html: Svg.ICON.triangle_user}}


                />}
                {_.has(currentUser, ['event_role']) && userClass(currentUser) && userClass(currentUser).includes('expert') &&
                <div className="svgicon" dangerouslySetInnerHTML={userClass(currentUser).includes('expert') ?
                    expertIcon !== null ? {__html: ` <img class= "customRoleIcon" src=${expertIcon} />`} :
                        {__html: Svg.ICON.triangle_user} : ""}>
                </div>}
            </div>
            {
                (hovered || toolbarHovered) && (props.data.conversation_users[props.position]) &&
                <ToolBar
                    setButton={setButton}
                    buttonState={buttonState}
                    checkInviteSucess={checkInviteSucess}
                    clickReset={clickReset}
                    setHover={setToolHover}
                    {...props}
                    alert={alertRef}
                    updateGridUser={updateGridUser}
                />
            }
        </div>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        callOff: () => dispatch(newInterfaceActions.NewInterFace.callOff()),
        callTrigger: () => dispatch(newInterfaceActions.NewInterFace.callTrigger()),
    }
}

const mapStateToProps = (state) => {
    return {
        event_roles: state.NewInterface.interfaceGraphics,
        event_space: state.NewInterface.interfaceSpacesData,
        event_badge: state.NewInterface.interfaceBadgeData,
        makingCall: state.NewInterface.makingCall,
    };
};

UserTile = connect(mapStateToProps, mapDispatchToProps)(UserTile);
export default UserTile;
