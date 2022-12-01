import React, {useEffect, useState} from 'react'
import _ from 'lodash';
import {Button} from '@material-ui/core';
import ClickAwayListener from '@material-ui/core/ClickAwayListener';
import Grow from '@material-ui/core/Grow';
import AddCircleOutlineIcon from '@material-ui/icons/AddCircleOutline';
import Paper from '@material-ui/core/Paper';
import Popper from '@material-ui/core/Popper';
import MenuItem from '@material-ui/core/MenuItem';
import MenuList from '@material-ui/core/MenuList';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import {connect} from 'react-redux';
import userAction from '../../../redux/action/apiAction/user';
import Helper from '../../../Helper';
import userReduxAction from '../../../redux/action/reduxAction/user';
import './EventsDropdown.css';
import {useAlert} from 'react-alert';
import {useParams} from "react-router-dom";
import NoDataDiv from '../../Common/NoDataDiv/NoDataDiv.js';
import EventDropdownSkeleton from './EventDropdownSkeleton.js';
import Constants from "../../../Constants";
import {RecurrenceIcon} from "../../v4/Svg";
import {useTranslation} from "react-i18next";
import Tooltip from "@material-ui/core/Tooltip";


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to access all types of event(future/past/draft events).This component is
 * divided into 2 main parts :
 * 1.'Events' button on which user can click to access event list page component.
 * 2.Events dropdown component which consist 3 future events + 3 past events(recently expired) and a 'create new event'
 *   button on which user can click to access create an event page component.
 *
 * <br>
 * 'View all' button can also navigate to event list page component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.getEvents To fetch future and past events
 * @param {Function} props.getUserData To fetch an user data by given user id.
 * @param {User} props.userAuth Logged in user data
 * @returns {JSX.Element}
 * @constructor
 */
const EventsDropdown = (props) => {
    const {t} = useTranslation(["eventList"]);

    // const classes = useStyles();
    const [open, setOpen] = React.useState(false);
    const [groupValues, setGroupValues] = useState({})
    const anchorRef = React.useRef(null);
    const [futureEventData, setFutureEventData] = useState([]);
    const [skeleton, setSkeleton] = useState(false)
    const [pastEventData, setPastEventData] = useState([])
    const errorAlert = useAlert();
    const handleToggle = () => {
        setOpen((prevOpen) => !prevOpen);
        getEvents()
    };
    const {gKey} = useParams();

    useEffect(() => {

        let user_badge = {};
        const data = localStorage.getItem('user_data');
        const userId = localStorage.getItem("userId")
        if (data) {

            user_badge = JSON.parse(data);
        }
        if (userId) {
            getProfileData(userId);
        }


    }, []);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method fetch the initial data of user details and show in fields
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id User ID
     */
    const getProfileData = (id) => {

        try {
            props.getUserData(id).then((res) => {
                const {data} = res.data
                // if set password not done redirect to set- password page
                if (res.data.status == 403) {
                    Helper.replaceSetPassword(res.data.data)
                }

                setGroupValues({
                    ...groupValues,
                    groupId: data.current_group.id
                })
                const group_id = data.current_group.id;
                // getEvents(group_id)
                props.setUser(res.data.data)
                props.setUsersMetaData(res.data.meta)
            }).catch((err) => {

                errorAlert.show(Helper.handleError(err), {type: 'error'})
            })
        } catch (err) {
            errorAlert.show(Helper.handleError(err), {type: 'error'})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to manage dropdowns close state
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} event Javascript event object
     */
    const handleClose = (event) => {
        if (anchorRef.current && anchorRef.current.contains(event.target)) {
            return;
        }

        setOpen(false);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to manage dropdowns close state
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} event Javascript event object
     */
    function handleListKeyDown(event) {
        if (event.key === 'Tab') {
            event.preventDefault();
            setOpen(false);
        }
    }

    // return focus to the button when we transitioned from !open -> open
    const prevOpen = React.useRef(open);
    React.useEffect(() => {
        if (prevOpen.current === true && open === false) {
            anchorRef.current.focus();
        }

        prevOpen.current = open;
    }, [open]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method fetch all events data including - future event, past event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const getEvents = () => {
        setSkeleton(true)
        const groupId = props.userGroupId.current_group && props.userGroupId.current_group.id
        if (groupId) {
            try {
                props.getMinEvents({groupKey: gKey, limit: 5}).then(res => {
                    setSkeleton(false)
                    setFutureEventData(res.data.future_events);
                    console.log('res.data.future_events', res.data.future_events)
                    setPastEventData(res.data.past_events)
                }).catch((err) => {
                    console.error(err)
                })
            } catch (err) {
                console.error(err)
            }
        }
    }

    return (
        <>
            {/* <Button onClick={() => props.history.push(`/${gKey}/event-list`)} className="EventBtn">
                <EventListIcon className="px-6 customIco" />
                Events
            </Button> */}
            <Button
                ref={anchorRef}
                aria-controls={open ? 'menu-list-grow' : undefined}
                aria-haspopup="true"
                className=" EventlistIcoDrop header__dropdownButton"
                disableRipple="true"
                onClick={handleToggle}

            >
                Events
                <ExpandMoreIcon className="px-6 EventlistIcoDrop" />

            </Button>
            <Popper
                open={open}
                anchorEl={anchorRef.current}
                role={undefined}
                transition
                disablePortal
                placement={'top-center'}
            >
                {({TransitionProps, placement}) => (
                    <Grow
                        {...TransitionProps}
                    >
                        <Paper>
                            <ClickAwayListener onClickAway={handleClose}>
                                <MenuList autoFocusItem={open} id="menu-list-grow" className="EventDropDownList rem_1"
                                    onKeyDown={handleListKeyDown}
                                >
                                    <MenuItem onClick={() => {
                                        props.history.push(`/${gKey}/v4/event-create`, {formMode: Constants.eventFormType.CAFETERIA})
                                    }} className=" CreateSvg">
                                        Create New Event
                                        <AddCircleOutlineIcon />
                                    </MenuItem>
                                    <hr className='ListSaprator' />
                                    {/* <p className="EventListHeading DisabledBlack" disabled='true'>Future Events </p> */}

                                    {skeleton && !futureEventData.length >= 1 &&
                                        <EventDropdownSkeleton />
                                    }

                                    {
                                        futureEventData &&
                                        futureEventData.map((value, i) => i < 5 && (

                                            <div>
                                                <MenuItem key={i} onClick={(e) => {
                                                    handleClose(e);
                                                    props.history.push(`/${gKey}/v4/event-update/${value.event_uuid}`)
                                                }}>
                                                    <div>
                                                        <span className='rem_1'>{Helper.limitText(value.title, 26)}</span>
                                                        {_.has(value, ['is_recurrence']) && value.is_recurrence === 1 &&
                                                            <Tooltip arrow title={t('reccEvent')} placement="top-start" className="tooltipRecIcon">
                                                                <span className='e_drw_rec_icon'>
                                                                    <RecurrenceIcon />
                                                                </span>
                                                            </Tooltip>
                                                        }
                                                    </div>
                                                    <span className='rem_08'> {Helper.toMonthName(value.date)}</span>
                                                </MenuItem>
                                            </div>
                                        ))

                                    }
                                    {futureEventData && futureEventData.length > 0
                                        ? ''
                                        :
                                        <>
                                            {!skeleton && <NoDataDiv showText="No Data">No data</NoDataDiv>}
                                        </>
                                    }
                                    {
                                        // (futureEventData.length >= 5) &&
                                        <div className='viewAllItem'>
                                            <hr className='ListSaprator' />
                                            <MenuItem className="EventListHeading rem_08" onClick={(e) => {
                                                handleClose(e);
                                                props.history.push(`/${gKey}/event-list/future-events`)
                                            }}>View all</MenuItem>
                                        </div>
                                    }
                                    {/* <hr className='ListSaprator' />
                                    <p className="EventListHeading DisabledBlack" disabled='true'>Past Events</p>

                                    {
                                        pastEventData &&
                                        pastEventData.map((value, i) => i < 3 && (
                                            <MenuItem key={i} onClick={(e) => {
                                                handleClose(e);
                                                props.history.push(`/${gKey}/edit-event/${value.event_uuid}`)
                                            }}><span>{Helper.limitText(value.title, 25)}</span><span>{value.date}</span></MenuItem>
                                        ))
                                    }
                                    {
                                        (pastEventData.length >= 3) &&
                                        <MenuItem className="EventListHeading" onClick={(e) => {
                                            handleClose(e);
                                            props.history.push(`/${gKey}/event-list/past-events`)
                                        }}>View all

                                        </MenuItem>
                                    } */}
                                </MenuList>
                            </ClickAwayListener>
                        </Paper>
                    </Grow>
                )}
            </Popper>
        </>


    );
}

const mapDispatchToProps = (dispatch) => {
    return {
        getUserData: (id) => dispatch(userAction.getUserData(id)),
        getMinEvents: (data) => dispatch(userAction.getMinEvents(data)),
        setUser: (data) => dispatch(userReduxAction.setUserData(data)),
        setUsersMetaData: (data) => dispatch(userReduxAction.setUsersMetaData(data))
    }
}

const mapStateToProps = (state) => {
    return {
        userAuth: state.Auth.userSelfData,
        userGroupId: state.Auth.userSelfData
    }
}


export default connect(mapStateToProps, mapDispatchToProps)(EventsDropdown);