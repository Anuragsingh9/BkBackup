import React, {useEffect, useState} from 'react';
import {FormControlLabel, Grid, Hidden, Radio, RadioGroup, Tooltip} from '@mui/material';
import "./EventManage.css"
import SelectField from '../Common/FormInput/SelectField';
import DateInput from '../Common/FormInput/DateInput';
import TextAreaInput from '../Common/FormInput/TextAreaInput';
import NumberInput from '../Common/FormInput/NumberInput';
import {change, getFormValues, reduxForm} from 'redux-form';
import EventManageHelper from "./EventManageHelper";
import {connect} from "react-redux";
import useEventData from "./Containers/EventContainer";
import TextInput from "../Common/FormInput/TextInput";
import MenuItem from "@material-ui/core/MenuItem";
import RecurrencePopup from '../Common/RecurrencePopup/RecurrencePopup';
import Constants from '../../../Constants';
import {
    CapacityIcon,
    DateTimeIcon,
    DemoUserIcon,
    DescriptionIcon,
    EventCreationIcon,
    LinkIcon,
    RecurrenceIcon,
    SceneryIcon,
    SelectSHIcon
} from '../Svg';
import SpaceHostSearch from "../Common/FormInput/SpaceHostSearch";
import TimeAutoComplete from '../Common/FormInput/TimeAutoComplete';
import eventFormAction from "../../../redux/action/reduxAction/event"
import eventAction from "../../../redux/action/reduxAction/event"
import useEventInitData from "./Containers/EventInitHandler";
import ArrowIcon from '../Svg/ArowIcon';
import CheckBoxInput from "../Common/FormInput/CheckBoxInput";
import RecurrenceModel from "../Models/RecurrenceModel";
import ColorPickerInput from "../Common/FormInput/ColorPickerInput";
import SliderInput from "../Common/FormInput/SliderInput";
import Validation from "../../../functions/ReduxFromValidation";
import {useTranslation} from 'react-i18next';
import Helper from "../../../Helper";
import SpaceList from './EventSpaces/SpaceList'
import {Prompt, useLocation} from 'react-router-dom';
import EventBroadcastingOption from "./EventBroadcastingOption";
import moment from 'moment-timezone';
import RadioButtonInput from "../Common/FormInput/RadioButtonInput";
import ConversationIcon from "../Svg/ConversationIcon";
import LoadingSkeleton from "../../Common/Loading/LoadingSkeleton"
import EventManageSkeleton from '../Skeleton/EventManageSkeleton';
import ErrorOutlineIcon from "@mui/icons-material/ErrorOutline";
import {CopyToClipboard} from "react-copy-to-clipboard";
import {IconButton} from "@material-ui/core";
import CopyBtnIcon from "../../Svg/CopyBtnIcon";
import LaunchIcon from "@mui/icons-material/Launch";
import {useAlert} from "react-alert";
import GridSizeIcon from '../Svg/GridSizeIcon';


const validate = () => {
};


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To provide the component for create/update the event
 * Here the event will be managed with different sets of data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */
let EventManage = (props) => {
    const alert = useAlert();

    // for localization
    const {t} = useTranslation("eventCreate");
    const {handleSubmit, initialize} = props;

    const location = useLocation();
    const formMode = location.state?.formMode || Constants.eventFormType.CAFETERIA;

    const eventData = useEventData({...props, formMode});
    const eventInitData = useEventInitData(props);
    const [loading, setLoading] = useState(true)

    const [recOptionsLabel, setRecOptionsLabel] = useState({
        daily: "Daily",
        weekly: "Weekly",
        monthly: "Monthly",
        weekday: "Weekday",
    })
    const {formSyncErrors, formAsyncErrors, isValid, formError} = props;
    const [recModalOpen, setRecModalOpen] = useState(false)
    const isDisabled = props.current_event && !props.current_event.event_is_published
        ? false :
        (formMode === Constants.eventFormType.ALL_DAY ? false :
            props.current_event && !props.current_event.event_state.is_future
        );
    useEffect(() => {
        initialize(eventData);
        props.updateTempRecData(eventData);
        return () => {
            setTimeout(() => {
                setLoading(false)
            }, 400)
        }
    }, [eventData]);

    useEffect(() => {
        if (props.formValues?.event_title) {
            EventManageHelper.updateRecOptionLabel(props.formValues, setRecOptionsLabel);
        }
        if (props.formValues?.event_rec_type === Constants.recurrenceType.NONE) {
            props.updateTempRecData(RecurrenceModel);
        }
        if (loading) {
            // if (props.match.params.event_uuid && props.current_event) {
            //     setLoading(false);
            // } else if (props.formValues?.event_start_time) {
            //     setLoading(false)
            // }
            // console.log('loading-starttime', loading, props.formValues?.event_start_time)
            // loading && setLoading(false);
            // setTimeout(() => {
            // }, 400)
        }
    }, [props.formValues]);
    useEffect(() => {
        console.log('loading', loading)
    }, [loading])





    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used handling event recurrence end data
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleSaveRecurrence = () => {
        props.updateTempRecData(props.formValues);
        props.updateEventForm('event_start_date', props.formValues.event_rec_start_date);
        let formValues = props.formValues;
        if (props.formValues
            && props.formValues.event_rec_type === Constants.recurrenceType.WEEKLY
            && Helper.convertWeekDayToNumber(props.formValues.event_rec_selected_weekday) === Constants.recWeekDayBinary
        ) {
            props.updateEventForm('event_rec_type', Constants.recurrenceType.WEEKDAY);
            formValues = {
                ...formValues,
                event_rec_selected_weekday: {
                    mon: true, tue: true, wed: true, thu: true, fri: true, sat: false, sun: false,
                },
                event_rec_type: Constants.recurrenceType.WEEKDAY,
            }
        }
        EventManageHelper.updateRecOptionLabel(formValues, setRecOptionsLabel);
        setRecModalOpen(false);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the popup closing and revert the data of redux form back to its value before open
     * -----------------------------------------------------------------------------------------------------------------
     */
    const handleClosePopup = () => {
        // setRecModelMode(state.recurrence_type);
        for (const tempRecurrenceData in props.temp_recurrence_data) {
            props.updateEventForm(tempRecurrenceData, props.temp_recurrence_data[tempRecurrenceData]);
        }
        setRecModalOpen(false);
    }

    const updateInputFieldHandler = () => {
        props.updateInputField(true);
    }

    const {is_event_form_updated} = props;

    useEffect(() => {
        console.log("is_event_form_updated", props.is_event_form_updated)

        //This event enables a web page to trigger a confirmation dialog asking the user if they really want to leave 
        // the page. If the user confirms, the browser navigates to the new page, otherwise it cancels the navigation.
        if (is_event_form_updated) {
            window.onbeforeunload = () => true
        } else {
            window.onbeforeunload = undefined
        }
    }, [props.is_event_form_updated])



    useEffect(() => {
        if (is_event_form_updated) {
            props.updateInputField(false);
        }
        return () => {
            if (is_event_form_updated) {
                props.updateInputField(false);
            }

            console.log('unmount',)
            initialize({});
            setLoading(true)
        }
    }, [])


    return (
        <LoadingSkeleton loading={loading} skeleton={<EventManageSkeleton />}>
            <>
                <Prompt
                    when={is_event_form_updated}
                    message={location => `All unsaved data will be lost. Are you sure you want to leave this page?`}
                />
                <div>
                    <form onSubmit={handleSubmit} onChange={updateInputFieldHandler}>
                        <Grid container specing={0} lg={6} sm={8} className="flexCenter__row v4_colorTheme">
                            <Grid item lg={1} sm={1}>
                                <Tooltip title={t("eventName")} arrow>
                                    <span>
                                        <EventCreationIcon />
                                    </span>
                                </Tooltip>
                            </Grid>
                            <Grid item lg={11} sm={11}>
                                <TextInput
                                    name={"event_title"}
                                    placeholder={"Event Name"}
                                    id={'eventName_v4'}
                                    validate={[Validation.required, Validation.min2, Validation.max100]}
                                    disabled={isDisabled}
                                />
                            </Grid>
                        </Grid>
                        {formMode !== Constants.eventFormType.ALL_DAY &&
                            <Grid container specing={0} lg={6} sm={8} className="flexCenter__row v4_colorTheme">
                                <Grid item lg={1} sm={1}>
                                    <Tooltip title={t("eventRecurrence")} arrow>
                                        <span>
                                            <RecurrenceIcon />
                                        </span>
                                    </Tooltip>
                                </Grid>
                                <Grid item lg={6} sm={3}>
                                    <SelectField
                                        name={"event_rec_type"}
                                        disabled={props.current_event && props.current_event.event_uuid}
                                        id="eventRercurrence_label_v4"
                                        onOpen={(e) => EventManageHelper.updateRecOptionLabel(null, setRecOptionsLabel)}
                                        onClose={e => EventManageHelper.updateRecOptionLabel(props.formValues, setRecOptionsLabel)}
                                        validate={[Validation.recDateAfterStartDate]}
                                    >
                                        <MenuItem value={Constants.recurrenceType.NONE} onClick={() => setRecModalOpen(false)}>
                                            Does Not Repeat
                                        </MenuItem>
                                        <MenuItem
                                            value={Constants.recurrenceType.DAILY}
                                            onClick={() => {
                                                setRecModalOpen(Constants.recurrenceType.DAILY)
                                            }}
                                        >
                                            {recOptionsLabel.daily}
                                        </MenuItem>
                                        <MenuItem
                                            value={Constants.recurrenceType.WEEKDAY}
                                            onClick={() => setRecModalOpen(Constants.recurrenceType.WEEKDAY)}
                                        >
                                            {recOptionsLabel.weekday}
                                        </MenuItem>
                                        <MenuItem
                                            value={Constants.recurrenceType.WEEKLY}
                                            onClick={() => setRecModalOpen(Constants.recurrenceType.WEEKLY)}
                                        >
                                            {recOptionsLabel.weekly}

                                        </MenuItem>
                                        <MenuItem
                                            value={Constants.recurrenceType.MONTHLY}
                                            onClick={() => setRecModalOpen(Constants.recurrenceType.MONTHLY)}
                                        >
                                            {recOptionsLabel.monthly}

                                        </MenuItem>
                                    </SelectField>
                                    {
                                        recModalOpen !== false
                                        &&
                                        <RecurrencePopup
                                            disabled={false}
                                            onSave={handleSaveRecurrence}
                                            closePopup={handleClosePopup}
                                            formValues={props.formValues}
                                            isShowSaveBtn={true}
                                        />
                                    }
                                </Grid>
                            </Grid>
                        }
                        {formMode !== Constants.eventFormType.ALL_DAY &&
                            <Grid container specing={0} lg={6} sm={8} className="flexCenter__row v4_colorTheme dateTimeRow">
                                <Grid item lg={1} sm={1}>
                                    <Tooltip title={t("eventDateTime")} arrow>
                                        <span>
                                            <DateTimeIcon />
                                        </span>
                                    </Tooltip>
                                </Grid>
                                <Grid item lg={6} sm={6} style={{"display": "flex"}}
                                    className="dateTimeRowLayout dateCustomElement">
                                    <DateInput
                                        name={"event_start_date"}
                                        id={'eventDate_v4'}
                                        disabled={isDisabled}
                                        validate={[Validation.validDate]}
                                    />
                                    &nbsp;&nbsp;
                                    <TimeAutoComplete
                                        name={"event_start_time"}
                                        id={'eventStartTime_v4'}
                                        disabled={isDisabled}
                                        onChange={(e) => {
                                            if (e instanceof moment) {
                                                props.updateEventForm('event_end_time', e.clone().add(1, 'hours'));
                                            }
                                        }}
                                        minimum={
                                            Helper.timeHelper.isToday(props.formValues?.event_start_date)
                                            && moment()
                                        }
                                        validate={[Validation.validDate]}
                                    />
                                </Grid>
                                <Grid item lg={1} sm={1} className='dateTimeRowLayout'>
                                    <ArrowIcon />
                                </Grid>
                                <Grid item lg={4} sm={4} className='dateTimeRowLayout'>
                                    <TimeAutoComplete
                                        name={"event_end_time"}
                                        id={'eventEndTime_v4'}
                                        validate={Validation.endTimeAfterStartTime}
                                        disabled={isDisabled}
                                        minimum={props.formValues?.event_start_time}
                                    />
                                </Grid>
                            </Grid>
                        }
                        {formMode !== Constants.eventFormType.CAFETERIA &&
                            <Grid container specing={0} lg={6} sm={8} className=" v4_colorTheme">
                                <Grid item lg={1} sm={1}>
                                    <Tooltip title={t("spaceHost")} arrow>
                                        <span>
                                            <SelectSHIcon className='pt-10' />
                                        </span>
                                    </Tooltip>
                                </Grid>
                                <Grid item lg={6} sm={8} className="spaceGridRow">
                                    <SpaceList
                                        disabled={isDisabled}
                                    />
                                </Grid>
                            </Grid>
                        }
                        {formMode === Constants.eventFormType.CAFETERIA &&
                            <>
                                <Grid container specing={0} lg={6} sm={8} className="flexCenter__row v4_colorTheme">
                                    <Grid item lg={1} sm={1}>
                                        <Tooltip title={t("spaceHost")} arrow>
                                            <span>
                                                <SelectSHIcon />
                                            </span>
                                        </Tooltip>
                                    </Grid>
                                    <Grid item lg={6} sm={8}>
                                        <SpaceHostSearch
                                            name={"event_space_data.0.space_host"}
                                            disabled={isDisabled}
                                        />
                                    </Grid>
                                </Grid>
                                <Grid container specing={0} lg={6} sm={8} className="flexCenter__row v4_colorTheme">
                                    <Grid item lg={1} sm={1}>
                                        <Tooltip title={t("maxCapacity")} arrow>
                                            <span>
                                                <CapacityIcon />
                                            </span>
                                        </Tooltip>
                                    </Grid>
                                    <Grid item lg={6} sm={4}>
                                        <NumberInput
                                            name={"event_space_data.0.space_max_capacity"}
                                            inputProps={{min: 12, max: Constants.space.MAX_CAPACITY}}
                                            disabled={isDisabled}
                                        />
                                    </Grid>
                                </Grid>
                            </>
                        }
                        <Grid container specing={0} lg={6} sm={8} className="flexCenter__row v4_colorTheme">
                            <Grid item lg={1} sm={1}>
                                <Tooltip title={t("customLink")} arrow>
                                    <span>
                                        <LinkIcon />
                                    </span>
                                </Tooltip>
                            </Grid>
                            <Grid item lg={6} sm={4}>
                                <TextInput
                                    name="event_custom_link"
                                    id='eventCustomUrl_v4'
                                    placeholder="Enter Custom Url"
                                    disabled={formMode === Constants.eventFormType.ALL_DAY ? false : props.current_event && props.current_event.event_is_published}
                                    validate={
                                        props.formValues && props.formValues.event_custom_link
                                        && props.formValues.event_custom_link.length > 0
                                        && [Validation.min3, Validation.max20, Validation.alpha_names_hypn_space]
                                    }
                                />
                            </Grid>
                            {(props?.current_event?.event_is_published) ?
                                <>
                                    {props?.current_event && props?.current_event?.event_uuid &&
                                <Grid item lg={2} sm={2}>
                                    <span className="iconCoppier">
                                        <CopyToClipboard text={props?.current_event?.event_links && props?.current_event?.event_links[0].link} onCopy={() => {
                                            alert.show('Copied to Clipboard', {type: 'success'})
                                        }}>
                                            <Tooltip title={t("copyLink")} arrow>
                                                <IconButton size="small">
                                                    <CopyBtnIcon fontSize="inherit" />
                                                </IconButton>
                                            </Tooltip>
                                        </CopyToClipboard>
                                        <Tooltip title={t("redirectEvent")} arrow>
                                            <a href="" target="_blank" rel="noopener noreferrer">
                                                {/*<LaunchIcon fontSize="small" color="action" />*/}
                                            </a>
                                        </Tooltip>
                                    </span>

                                    {/* <Tooltip className={"broadcastingErrorTag"} arrow title={t("configureZoomAccount")} placement="top-start">*/}
                                    {/*<ErrorOutlineIcon color="error" />*/}

                                    {/*</Tooltip>*/}
                                </Grid>}
                                </>
                                : ""
                            }
                        </Grid>

                        <Grid
                            container
                            specing={0}
                            lg={6}
                            sm={8}
                            className="flexCenter__row v4_colorTheme sceneryRowSelect"
                        >
                            <Grid item lg={1} sm={1}>
                                <Tooltip title={t("scenery")} arrow>
                                    <span>
                                        <SceneryIcon />
                                    </span>
                                </Tooltip>
                            </Grid>
                            <Grid item lg={6} sm={4}>
                                <SelectField
                                    name={"event_scenery"}
                                    id={'selectScenery_v4'}
                                    disabled={isDisabled}
                                    onChange={updateInputFieldHandler}
                                    defaultValue={
                                        eventInitData?.scenery?.allSceneryData
                                        && eventInitData?.scenery?.allSceneryData.length
                                        && eventInitData?.scenery?.allSceneryData[0].category_id
                                    }
                                >
                                    <MenuItem value={0}>None</MenuItem>
                                    {eventInitData?.scenery && eventInitData?.scenery?.allSceneryData.map(scenery => {
                                        return (<MenuItem
                                            value={scenery.category_id}
                                        >
                                            {scenery.category_name}
                                        </MenuItem>);
                                    })}
                                </SelectField>

                            </Grid>
                        </Grid>
                        <Grid
                            container
                            specing={0}
                            lg={6}
                            sm={8}
                            className="flexCenter__row v4_colorTheme sceneryRowSelect"
                        >
                            <Grid item lg={1} sm={1}>
                            </Grid>
                            <Grid item lg={6} sm={4}>
                                {eventInitData?.scenery?.currentSceneryData
                                    &&
                                    <>
                                        <Grid container specing={0} lg={12} sm={12}
                                            className="flexCenter__row v4_colorTheme desLayoutRow">
                                            <Grid item lg={4} sm={1}>
                                                <div className="PickerLabel-2">Background<br />Color</div>
                                            </Grid>
                                            <Grid item lg={8} sm={10}>
                                                <ColorPickerInput
                                                    name={"event_top_bg_color"}
                                                    disabled={isDisabled}
                                                />
                                            </Grid>
                                        </Grid>
                                        <Grid container specing={0} lg={12} sm={12}
                                            className="flexCenter__row v4_colorTheme desLayoutRow">
                                            <Grid item lg={4} sm={1}>
                                                <div className="PickerLabel-2">Component<br />Opacity</div>
                                            </Grid>
                                            <Grid item lg={8} sm={10}>
                                                <div className='MainSlidersPadding compOpacity'>
                                                    <SliderInput
                                                        name={"event_component_op"}
                                                        disabled={isDisabled}
                                                    />
                                                    <div className="ColorPickerLabelDiv">
                                                        <div className="PickerLabel-1">50</div>
                                                        <div className="PickerLabel-3">100</div>
                                                    </div>
                                                </div>
                                            </Grid>
                                        </Grid>
                                    </>
                                }
                            </Grid>
                            {
                                eventInitData?.scenery?.currentSceneryData
                                && eventInitData?.scenery?.currentSceneryData.category_type === 1
                                &&
                                <Grid item lg={4} sm={4} className="sceneryImgDropdown">
                                    <SelectField
                                        name={"event_scenery_asset"}
                                        id={'selectScenery_v4'}
                                        onChange={updateInputFieldHandler}
                                        className="sceneryPreviewSelect"
                                        disabled={isDisabled}
                                    >
                                        {eventInitData?.scenery?.currentSceneryData.category_assets.map(asset =>
                                            <MenuItem value={asset.asset_id} className="Scenery_li_item">
                                                <div className='img_scenery'
                                                    style={{backgroundImage: `url(${asset.asset_path})`}}></div>
                                            </MenuItem>
                                        )}
                                    </SelectField>
                                </Grid>
                            }
                        </Grid>
                        {formMode !== Constants.eventFormType.ALL_DAY &&
                            <EventBroadcastingOption
                                broadcastOptions={eventInitData.broadcasting}
                            />
                        }
                        <Grid container specing={0} lg={6} sm={8} className="flexCenter__row v4_colorTheme desLayoutRow">
                            <Grid item lg={1} sm={1}>
                                <Tooltip title={t("description")} arrow>
                                    <span>
                                        <DescriptionIcon />
                                    </span>
                                </Tooltip>
                            </Grid>
                            <Grid item lg={11} sm={11}>
                                <TextAreaInput
                                    name={"event_description"}
                                    id={'eventDescription_v4'}
                                    disabled={isDisabled}
                                    placeholder={t("description_placeholder")}
                                />
                            </Grid>
                        </Grid>
                        <div style={{display: 'none'}}>
                            <Hidden
                                implementation={'css'}
                                children={<Grid item lg={0} sm={0}>
                                    <CheckBoxInput
                                        name={"event_is_published"}
                                    />
                                </Grid>} />
                        </div>
                        <Grid container specing={0} lg={6} sm={8}>
                            <Grid item lg={1} sm={1} className="flexCenter__row v4_colorTheme">
                                <Tooltip title={t("conversation")} arrow>
                                    <div>
                                        <ConversationIcon />
                                    </div>
                                </Tooltip>
                            </Grid>
                            <Grid item lg={11} sm={11}>
                                <RadioButtonInput
                                    name={"event_conv_limit"}
                                    disabled={isDisabled}
                                    row={true}
                                    defaultValue={4}
                                >
                                    <FormControlLabel value={4} control={<Radio />} disabled={isDisabled} label={`4 ${t("way_conversation")}`} />
                                    <FormControlLabel value={8} control={<Radio />} disabled={isDisabled} label={`8 ${t('way_conversation')}`} />
                                </RadioButtonInput>
                            </Grid>
                        </Grid>
                        <Grid container specing={0} lg={6} sm={8}>
                            <Grid item lg={1} sm={1} className="flexCenter__row v4_colorTheme">
                                <Tooltip title={t("dummy_user")} arrow>
                                    <div>
                                        <DemoUserIcon />
                                    </div>
                                </Tooltip>
                            </Grid>
                            <Grid item lg={11} sm={11}>
                                <CheckBoxInput
                                    name={"event_is_demo"}
                                    disabled={formMode === Constants.eventFormType.ALL_DAY ? false : props.current_event && props.current_event.event_uuid}
                                />
                            </Grid>
                        </Grid>
                        <Grid
                            container
                            specing={0}
                            lg={6}
                            sm={8}
                            className="flexCenter__row v4_colorTheme sceneryRowSelect"
                        >
                            <Grid item lg={1} sm={1}>
                                <Tooltip title={t("UserGridSize")} arrow>
                                    <span>
                                        <GridSizeIcon />
                                    </span>
                                </Tooltip>
                            </Grid>
                            <Grid item lg={6} sm={4}>
                                <SelectField
                                    name={"event_grid_rows"}
                                    id={'selectEventGridRows'}
                                    disabled={isDisabled}
                                    onChange={updateInputFieldHandler}

                                >
                                    {
                                        Constants.gridRowSize.map((size, index) => (
                                            <MenuItem value={size}>{size}</MenuItem>
                                        ))
                                    }
                                </SelectField>

                            </Grid>
                        </Grid>
                    </form>
                </div>
            </>
        </LoadingSkeleton>
    )
}


const mapDispatchToProps = (dispatch) => {
    return {
        addEvent: eventObject => dispatch(eventFormAction.addEvent(eventObject)),
        currentEventUuid: (data) => dispatch(eventFormAction.currentEventUuid(data)),
        updateInputField: (data) => dispatch(eventFormAction.updateInputField(data)),
        updateEventForm: (field, value) => dispatch(change('eventManageForm', field, value)),
        updateTempRecData: (recData) => dispatch(eventFormAction.updateTempRecData(recData)),
        updateCurrentEvent: (eventData) => dispatch(eventFormAction.updateCurrentEvent(eventData)),
        publishEventSubmit: (submitValue) => dispatch(eventAction.publishEventSubmit(submitValue)),
        saveEventSubmit: (submitValue) => dispatch(eventAction.saveEventSubmit(submitValue)),
    }
};

const mapStateToProps = (state) => {
    return {
        formValues: getFormValues('eventManageForm')(state),
        user_badge: state.Auth.userSelfData,
        temp_recurrence_data: state.Event.temp_recurrence_data,
        publish_submit_action: state.Event.event_form.publish_submit_action,
        current_event: eventAction.getCurrentEvent(state),
        is_event_form_updated: state.Event.is_event_form_updated,

    }
}

EventManage = reduxForm({
    form: "eventManageForm", // unique key for form
    validate,
    asyncValidate: EventManageHelper.asyncValidate,
    asyncBlurFields: ['event_custom_link'],
    onSubmit: EventManageHelper.handleEventFormSubmit,
    keepDirtyOnReinitialize: true,
})(EventManage);

EventManage = connect(mapStateToProps, mapDispatchToProps)(EventManage);
export default EventManage;