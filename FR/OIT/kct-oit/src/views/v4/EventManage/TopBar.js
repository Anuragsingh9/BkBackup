import React, {useEffect, useState} from 'react';
import CustomContainer from '../../Common/CustomContainer/CustomContainer';
import {Box, Button, Grid, Tooltip, Typography} from '@mui/material';
import Tab from '@mui/material/Tab';
import TabContext from '@mui/lab/TabContext';
import TabList from '@mui/lab/TabList';
import {connect, useDispatch} from "react-redux";
import {useAlert} from "react-alert";
import EventManage from './EventManage';
import './TopBar.css'
import {confirmAlert} from "react-confirm-alert";
import {useTranslation} from "react-i18next";
import Constants from "../../../Constants";
import {useParams} from "react-router-dom";
import {getFormValues, isValid, submit} from "redux-form";
import EventMedia from "../../CreateEvent/Live/index";
import _ from 'lodash';
import ButtonGroup from '@mui/material/ButtonGroup';
import ArrowDropDownIcon from '@mui/icons-material/ArrowDropDown';
import ClickAwayListener from '@mui/material/ClickAwayListener';
import Grow from '@mui/material/Grow';
import Paper from '@mui/material/Paper';
import Popper from '@mui/material/Popper';
import MenuList from '@mui/material/MenuList';
import LinkComponent from "./LinkComponents";
import eventAction from "../../../redux/action/reduxAction/event";
import eventFormAction from "../../../redux/action/reduxAction/event";
import Helper from "../../../Helper";
import BreadcrumbsInput from "../Common/Breadcrumbs/BreadcrumbsInput";
import UserManageWrap from "../UserManagement/UserManageWrap";
import AddUserModal from "../UserManagement/AddUserModal";
import {LoadingButton} from "@mui/lab";
import EventAnalytics from "../EventAnalytics/EventAnalytics";
import AnalyticsDateDropdown from "../EventAnalytics/AnalyticsDateDropdown/AnalyticsDateDropdown";
import AnalyticsDatePicker from "../EventAnalytics/AnalyticsDateRanger/AnalyticsDatePicker";
import AnalyticsDateRangeDropdown from "../EventAnalytics/AnalyticsDateRangeDropdown/AnalyticsDateRangeDropdown";
import AnalyticsDate from "../EventAnalytics/AnalyticsDate/AnalyticsDate";
import IconButton from "@mui/material/IconButton";
import CachedIcon from "@mui/icons-material/Cached";
import useEventAnalytics from "../EventAnalytics/Containers/EventAnalyticsContainer";
import eventV4Api from "../../../redux/action/apiAction/v4/event";
import analyticsAction from "../../../redux/action/reduxAction/analytics";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will take data(for create a basic link component for nav tab) from parameter
 * and return a component(JSX) on which if user clicks then it will render relative child components to it.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Props passed from parent component
 * @param {String} props.aria-controls Css related key for link tab
 * @param {Boolean} props.disabled To disable the link tab
 * @param {Boolean} props.fullWidth To get the full width view
 * @param {String} props.href link's
 * @param {String} props.id User's Id
 * @param {Boolean} props.indicator To indicate the current tab by showing a horizontal line below selected tab
 * @param {String} props.label Label on link tab
 * @param {Function} props.onChange Function is used change the state
 * @param {Boolean} props.selected Link is selected or not
 * @param {String} props.textColor Text color
 * @param {Number} props.value Link value
 * @returns {JSX.Element}
 * @constructor
 */
function LinkTab(props) {
    return (
        <Tab wrapped
             onClick={(e) => {
                 e.preventDefault()
             }}
             {...props}
        />
    );
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function is receiving props(from main nav tab components - down below)  to render content box of
 * nav tab's child.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Inherited from parent component
 * @param {JSX} props.children JSX element for tab panel
 * @param {Number} props.value Index of the tab panel
 * @param {Number} props.index Index of tab panel
 * @returns {JSX.Element}
 * @constructor
 */
let TabPanel = (props) => {
    const {children, value, index, ...other} = props;

    return (
        <div
            role="tabpanel"
            hidden={value !== index}
            id={`nav-tabpanel-${index}`}
            aria-labelledby={`nav-tab-${index}`}
            {...other}
        >
            {value === index && (
                <Box p={3}>
                    <Typography>{children}</Typography>
                </Box>
            )}
        </div>
    );
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will take index(from parameter) and return an object to set 'ID' and 'aria-controls'
 * attribute.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} index Index of nav tab
 * @returns {Object} HTML attributes
 */
function a11yProps(index) {
    return {
        id: `nav-tab-${index}`,
        'aria-controls': `nav-tabpanel-${index}`,
    };
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Top bar component for event management containing event actions and buttons for submitting event in
 * different attributed i.e. publish and save/update and close
 *
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from the parent component
 * @param {String} props.redirectValue Redirect value from the component rendered
 * @param {Number} props.tabValue Current tab value 1 for details 2 for media and 3 for users
 * @param {Number} props.formMode Event details page current form mode 1 for cafeteria 2 for executive 3 for manager
 *
 * @param {Function} props.updateInputField Redux action dispatcher for updating the if user have changed form or not
 * @param {Function} props.submitEventForm Redux action dispatcher for submitting the event form remotely
 * @param {Function} props.publishEventSubmit Redux action dispatcher for updating the flag so event form will submit with published
 * @param {Function} props.saveEventSubmit Redux action dispatcher for updating that event form is being saved or updated
 * @param {Function} props.updateAddUserPopUpDisplay Redux action dispatcher for updating the add user popup visibility

 * @param {Object} props.is_event_form_updated To indicate if event form is touched and updated or not
 * @param {Object} props.formValues Event form values
 * @param {Object} props.events Current events present in redux store
 * @param {Object} props.current_event_uuid Current event uuid from redux store
 * @param {Object} props.publish_submit_action Value to indicate if form is in submit process with published mode
 * @param {Object} props.save_submit_action Value to indicate if form is in save/update process
 * @param {Object} props.add_user_pop_up_display To indicate if user add popup is visible or not in users tab
 * @param {Object} props.add_user_mode To indicate the popup mode for the add user popup
 * @param {Object} props.fetch_user Flag for triggering the fetch user api
 * @param {Object} props.current_event Current event object from redux store
 * @param {Object} props.isValid To indicate if event form have any sync or async errors
 *
 * @returns {JSX.Element}
 * @constructor
 */
let TopBar = (props) => {
    const {t} = useTranslation(["events"]);
    const {gKey} = useParams();
    const [value, setValue] = React.useState(Constants.eventTabType.DETAILS);
    const alert = useAlert();
    const dispatch = useDispatch();
    const params = useParams();
    const anchorRef = React.useRef(null);
    const [open, setOpen] = React.useState(false);
    const [saveButtonLabel, setSaveButtonLabel] = React.useState('Save Draft');
    const [eventNameLabel, setEventNameLabel] = React.useState('Create Event');

    // This state is used for to add user popup show or not
    useEffect(() => {
        setValue(props.tabValue || Constants.eventTabType.DETAILS)
        setSaveButtonLabel(params.event_uuid ? 'Update' : 'Save Draft')
    }, []);

    // This state is used for changing the current event_uuid if event_uuid in url is changed
    useEffect(() => {
        props.currentEventUuid(params.event_uuid)
    },[params.event_uuid])

    /*
     * This use effect is to identify if the event publish button is clicked or not
     * After clicking the publish button redux form field is updated and this hook will catch that change for
     * first time only so publish button will update field -> this will catch that -> hit the submit form
     * so the form will be only submitted after the publish field is updated
     *
     * Because the field value update is asynchronous so we can not submit the form just after updating the value
     */
    useEffect(() => {
        setEventNameLabel(
            props.formValues && props.formValues.event_title
                ?
                props.formValues.event_title
                :
                `${params.event_uuid && props.current_event ? props.current_event.event_title : 'Create Event'}`
        )
    }, [props.formValues, props.current_event]);

    useEffect(() => {
        console.log('gg props.publish_submit_action', props.publish_submit_action)
        if (props.publish_submit_action) {
            props.submitEventForm();
        }
    }, [props.publish_submit_action]);

    const handleChange = (event, newValue) => {
        setValue(newValue);
        if (props.redirectValue) {
            props.history.push(props.redirectValue[newValue]);
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handler for the publish button this will just update the hidden field of event create and the
     * hook will catch that change and that hook will submit the form to create/update the event with published mode
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const publishEvent = () => {
        props.isValid && props.publishEventSubmit(true);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the save and update button click
     * This button will update the button to loading if form is valid and api is possible to be triggered
     * -----------------------------------------------------------------------------------------------------------------
     */
    const handleUpdateAndDraft = () => {
        props.isValid && props.saveEventSubmit(true);
        props.updateInputField(false);
        dispatch(submit('eventManageForm'))
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to show pop up when user want to close the event creation form
     * 1. If form is not modify then it directed redirect to event list page
     * 2. If form is modify then it show the popup and show two option
     *      a. User want to continue editing on form
     *      b. User want to discard form
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const eventFormClose = () => {
        if (props.is_event_form_updated === false) {
            props.history.push(`/${gKey}/event-list/${Constants.defaultEventType}`);
        } else {
            confirmAlert({
                message: `${t("confirm:Do you want to discard edits or continue editing?")}`,
                confirmLabel: t("confirm:confirm"),
                cancelLabel: t("confirm:cancel"),
                buttons: [
                    {
                        label: t("confirm:Discard"),
                        onClick: () => {
                            props.history.push(`/${gKey}/event-list/${Constants.defaultEventType}`);
                        },
                    },
                    {
                        label: t("confirm:Continue editing"),
                        onClick: () => {
                            return false;
                        },
                    },
                ],
            });
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for open and close drop down when we click arrow button
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleToggle = () => {
        setOpen((prevOpen) => !prevOpen);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handle the list of drop down
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} event Javascript event object
     */
    function handleListKeyDown(event) {
        if (event.key === "Tab") {
            event.preventDefault();
            setOpen(false);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for close the join drop down when we click outside the popup
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
     * @description This method is used for join the event
     * This method prepare the participant link url
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const joinEvent = () => {
        let link = null;
        Object.keys(props.current_event.event_links).forEach(key => {
            if (props.current_event.event_links[key].type === "participants_link") {
                link = props.current_event.event_links[key].link;
            }
        });
        window.open(link, '_blank', 'noopener,noreferrer');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will open the manual add user pop up.
     * -----------------------------------------------------------------------------------------------------------------
     */
    const openAddUserPopUp = () => {
        props.updateAddUserPopUpDisplay(true, Constants.addUserType.ADD_USER, false)
        console.log('add user')
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is responsible for fetching the analytics data.
     * -----------------------------------------------------------------------------------------------------------------
     */
    const refreshAnalyticsData = () => {
        props.setPageRefresh(true)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will open the Import user pop to import multiple users at a time
     * -----------------------------------------------------------------------------------------------------------------
     */
    const openImportUserPopUp = () => {
        props.updateAddUserPopUpDisplay(true, Constants.addUserType.IMPORT_USER, false)
    }

    const tabData = [
        {
            label: 'Details',
            href: '/event-create',
            child: <EventManage alert={alert} {...props} />
            // child: <h1>manage event</h1>
        },
        {
            label: 'Media',
            href: '/media',
            disable: !_.has(params, ['event_uuid']),
            child: <EventMedia {...props} />
        },
        {
            label: 'User',
            href: '/user',
            disable: !_.has(params, ['event_uuid']),
            child: <UserManageWrap {...props} />
        },
        {
            label: 'Analytics',
            href: '/analytics',
            disable: !_.has(params, ['event_uuid']),
            child: <EventAnalytics {...props} />
        }
    ]

    return (
        <>
            <BreadcrumbsInput
                links={[
                    Constants.breadcrumbsOptions.GROUP_NAME,
                    Constants.breadcrumbsOptions.EVENTS_LIST,
                    props.current_event
                        ? Constants.breadcrumbsOptions.EVENT_NAME
                        : Constants.breadcrumbsOptions.NEW_EVENT,
                    props.tabValue === 1 ?
                        Constants.breadcrumbsOptions.MEDIA_TAB
                        : (
                            props.tabValue === 2
                                ? Constants.breadcrumbsOptions.USERS_TAB
                                : null
                        ),
                ]}
            />
            <CustomContainer className="EventManageWrap">
                <Grid container specing={0} lg={12} sm={8}>

                    <Grid item lg={12} sm={12} className="publishEvent_Tabwrap">
                        <div className='publishEvent_wrap'>
                            <Tooltip title={eventNameLabel} arrow>
                                <span>
                                    <h3>{Helper.limitText(eventNameLabel, 25)}</h3>
                                </span>
                            </Tooltip>

                            {/*These buttons will only be shown when user is on the Details tab */}
                            {(props.tabValue === Constants.eventTabType.DETAILS || props.tabValue) === undefined &&
                                <div className='saveEventBtnGroup'>
                                    {!props.current_event || !props.current_event.event_is_published ? (
                                        <LoadingButton
                                            variant="contained"
                                            color={props.publish_submit_action ? 'secondary' : 'primary'}
                                            type="submit"
                                            size='small'
                                            onClick={publishEvent}
                                            loading={props.publish_submit_action}
                                        >
                                            Publish
                                        </LoadingButton>
                                    ) : (
                                        <>
                                            <ButtonGroup ref={anchorRef} aria-label="split button">
                                                <Button
                                                    onClick={joinEvent}
                                                    variant="contained"
                                                    color="primary"
                                                    size='small'
                                                >
                                                    Join
                                                </Button>
                                                <Button
                                                    variant="contained"
                                                    color="primary"
                                                    size='small'
                                                    aria-controls={open ? 'split-button-menu' : undefined}
                                                    aria-expanded={open ? 'true' : undefined}
                                                    aria-label="select merge strategy"
                                                    aria-haspopup="menu"
                                                    onClick={handleToggle}
                                                >
                                                    <ArrowDropDownIcon/>
                                                </Button>
                                            </ButtonGroup>
                                            <Popper
                                                open={open}
                                                anchorEl={anchorRef.current}
                                                role={undefined}
                                                transition
                                                disablePortal
                                                className='topBarBtnGroup'
                                                placement="bottom-end"
                                            >
                                                {({TransitionProps, placement}) => (
                                                    <Grow
                                                        {...TransitionProps}
                                                    >
                                                        <Paper>
                                                            <ClickAwayListener onClickAway={handleClose}>
                                                                <MenuList
                                                                    autoFocusItem={open}
                                                                    id="split-button-menu"
                                                                    className="GroupDropDownList"
                                                                    onKeyDown={handleListKeyDown}
                                                                >
                                                                    {props.current_event.event_links.map((option, index) => (
                                                                        <div>
                                                                            <LinkComponent
                                                                                value={option}
                                                                            />
                                                                            {/* <MenuItem
                                                                        key={index}
                                                                        onClick={(event) => handleMenuItemClick(event, option)}
                                                                    >
                                                                    </MenuItem> */}
                                                                        </div>
                                                                    ))}
                                                                </MenuList>
                                                            </ClickAwayListener>
                                                        </Paper>
                                                    </Grow>
                                                )}
                                            </Popper>
                                        </>
                                    )}

                                    &nbsp;
                                    <LoadingButton
                                        variant="contained"
                                        color={props.save_submit_action ? 'secondary' : 'primary'}
                                        size='small'
                                        onClick={handleUpdateAndDraft}
                                        loading={props.save_submit_action}
                                    >
                                        {saveButtonLabel}
                                    </LoadingButton>
                                    &nbsp;
                                    <Button
                                        variant="outlined"
                                        size='small'
                                        onClick={eventFormClose}
                                    >
                                        Close
                                    </Button>

                                </div>
                            }

                            {/*These buttons will only be shown when user is on the user tab */}
                            {(props.tabValue === Constants.eventTabType.USER && props.tabValue) &&
                                <div className='saveEventBtnGroup'>
                                    <>
                                        <Button
                                            variant="contained"
                                            color="primary"
                                            type="submit"
                                            size='small'
                                            onClick={openAddUserPopUp}
                                        >
                                            Add New User
                                        </Button>
                                        &nbsp;&nbsp;
                                        <Button
                                            variant="contained"
                                            color="primary"
                                            type="submit"
                                            size='small'
                                            onClick={openImportUserPopUp}
                                        >
                                            Import User
                                        </Button>

                                        <AddUserModal
                                            mode={props.add_user_mode}
                                            setMode={props.updateAddUserPopUpDisplay}
                                            setAddUserRecModalOpen={props.updateAddUserPopUpDisplay}
                                            addUserModalOpen={props.add_user_pop_up_display}
                                            {...props}
                                        />
                                    </>
                                </div>
                            }

                            {(props.tabValue === Constants.eventTabType.ANALYTICS && props.tabValue && props?.current_event?.event_uuid) &&
                                <div className='saveEventBtnGroup inlineFlex'>
                                    {(props.current_event?.event_is_recurrence || props.current_event?.event_type === 4) ?
                                        <>
                                            <Grid item>
                                                <AnalyticsDateDropdown/>
                                            </Grid>
                                            <Grid item>
                                                <AnalyticsDatePicker/>
                                            </Grid>
                                        </>
                                        : <AnalyticsDate analyticsDate={props?.current_event?.event_start_date}/>
                                    }
                                    <Grid item>
                                        <IconButton className='refresh-icon' onClick={refreshAnalyticsData}>
                                            <CachedIcon/>
                                        </IconButton>
                                    </Grid>
                                </div>
                            }

                        </div>
                        <TabContext value={value}>
                            <div className='verticleTab__EventManage'>
                                <TabList onChange={handleChange} aria-label="vertical_tab" value={value}>
                                    {tabData.map((item, key) => (
                                        <LinkTab
                                            label={item.label}
                                            href={item.href}
                                            disabled={item.disable}
                                            {...a11yProps(key)} />
                                    ))}
                                </TabList>
                            </div>
                            {tabData.map((item, key) => {
                                return (
                                    <TabPanel value={value} index={key}>
                                        {item.child}
                                    </TabPanel>
                                )
                            })}
                        </TabContext>
                    </Grid>
                </Grid>
            </CustomContainer>
        </>

    )
}

const mapStateToProps = (state) => {
    return {
        is_event_form_updated: state.Event.is_event_form_updated,
        formValues: getFormValues('eventManageForm')(state),
        events: state.Event.events,
        current_event_uuid: state.Event.current_event_uuid,
        publish_submit_action: state.Event.event_form.publish_submit_action,
        save_submit_action: state.Event.event_form.save_submit_action,
        add_user_pop_up_display: state.Event.add_user_pop_up.display,
        add_user_mode: state.Event.add_user_pop_up.mode,
        fetch_user: state.Event.add_user_pop_up.fetch,
        current_event: eventAction.getCurrentEvent(state),
        isValid: isValid('eventManageForm')(state),
        recurrences_analytics: state.Analytics.recurrences_analytics,
        refresh:state.Analytics.refreshPage,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        updateInputField: (data) => dispatch(eventFormAction.updateInputField(data)),
        submitEventForm: () => dispatch(submit("eventManageForm")),
        publishEventSubmit: (submitValue) => dispatch(eventAction.publishEventSubmit(submitValue)),
        saveEventSubmit: (submitValue) => dispatch(eventAction.saveEventSubmit(submitValue)),
        updateAddUserPopUpDisplay: (display, mode, fetch) => dispatch(eventAction.updateAddUserPopUpDisplay(display, mode, fetch)),
        getEventAnalytics: (eventUuid) => dispatch(eventV4Api.getEventAnalytics(eventUuid)),
        updateAnalyticsRecList: (recurrencesList) => dispatch(analyticsAction.updateAnalyticsRecList(recurrencesList)),
        updateAnalyticsList: (analyticsList) => dispatch(analyticsAction.updateAnalyticsList(analyticsList)),
        setPageRefresh: (data) => dispatch(analyticsAction.setPageRefresh(data)),
        currentEventUuid: (data) => dispatch(eventFormAction.currentEventUuid(data)),

    }
}

TopBar = connect(mapStateToProps, mapDispatchToProps)(TopBar);

export default TopBar;