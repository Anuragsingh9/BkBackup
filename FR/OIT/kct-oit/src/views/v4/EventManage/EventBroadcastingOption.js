import React, {useEffect, useState} from 'react';
import {Grid, Tooltip} from '@mui/material';
import "./EventManage.css"
import SelectField from '../Common/FormInput/SelectField';
import MenuItem from "@material-ui/core/MenuItem";
import Constants from '../../../Constants';
import BroadcastIcon from '../Svg/BroadcastIcon';
import ModeratorIcon from '../Svg/ModeratorIcon';
import {useLocation} from "react-router-dom";
import {useTranslation} from "react-i18next";
import {change, getFormValues} from "redux-form";
import eventAction from "../../../redux/action/reduxAction/event";
import {connect} from "react-redux";
import ErrorOutlineIcon from "@mui/icons-material/ErrorOutline";


const validate = () => {
};


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To provide the component for event broadcast drop downs
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent class
 * @returns {JSX.Element}
 * @constructor
 */
let EventBroadcastingOption = (props) => {

    const {t} = useTranslation("eventCreate");

    const location = useLocation();
    const formMode = location.state?.formMode;

    const broadcastOptions = [
        {value: Constants.broadcastType.NONE, label: t("No Broadcasting")},
        {value: Constants.broadcastType.MEETING, label: t('Broadcasting Meeting')},
        {value: Constants.broadcastType.WEBINAR, label: t('Broadcasting Webinar')},
    ];

    const [moderatorOptions, setModeratorOptions] = useState([{
        value: 0, label: "No Moderator",
    }]);


    useEffect(() => {
        // no moderator will shown when there is no broadcasting or respective broadcasting doesn't have moderators
        let moderators = [{
            value: 0, label: "No Moderator",
        }];
        if (props.formValues?.event_broadcasting === Constants.broadcastType.MEETING && props.broadcastOptions.meetingModerators.length) {
            moderators = props.broadcastOptions.meetingModerators;
        } else if (props.formValues?.event_broadcasting === Constants.broadcastType.WEBINAR && props.broadcastOptions.webinarModerators.length) {
            moderators = props.broadcastOptions.webinarModerators;
        }

        setModeratorOptions(moderators);

        let moderatorValue = moderators[0].value;
        // checking if form selected moderator present in current broadcasting moderators
        if (props.formValues?.event_moderator) {
            // form moderator is present in moderators list so selecting that
            if (moderators.find(e => e.value === props.formValues?.event_moderator)) {
                moderatorValue = props.formValues?.event_moderator;
            }
        }
        if (props.broadcastOptions.meetingModerators.length || props.broadcastOptions.webinarModerators.length) {
            props.updateEventForm('event_moderator', moderatorValue);
        }
    }, [props.broadcastOptions, props.formValues?.event_broadcasting]);


    return (
        <>
            {
                formMode !== Constants.eventFormType.CAFETERIA &&
                <>
                    <Grid
                        container
                        specing={0}
                        lg={6}
                        sm={8}
                        className="flexCenter__row v4_colorTheme sceneryRowSelect"
                    >
                        <Grid item lg={1} sm={1}>
                            <Tooltip title={t("ContentType")} arrow>
                                    <span>
                                        <BroadcastIcon />
                                    </span>
                            </Tooltip>
                        </Grid>
                        <Grid item lg={6} sm={4}>
                            <SelectField
                                name={"event_broadcasting"}
                                id={'selectScenery_v4'}
                                disabled={
                                    !props.broadcastOptions.isConfigured // if not configured then disable it
                                    || (
                                        props.current_event
                                        && (
                                            !props.current_event.event_state.is_future
                                            || props.current_event.event_is_published
                                        )
                                    )
                                }
                            >
                                {broadcastOptions && broadcastOptions.map((option, index) => {
                                    return (<MenuItem
                                        value={option.value}
                                    >
                                        {option.label}
                                    </MenuItem>);
                                })}
                            </SelectField>
                        </Grid>
                        {
                            !props.broadcastOptions.isConfigured &&
                            <Grid item lg={2} sm={2}>
                                <Tooltip className={"broadcastingErrorTag"} arrow title={t("configureZoomAccount")} placement="top-start">
                                    <ErrorOutlineIcon color="error" />
                                </Tooltip>
                            </Grid>
                        }
                    </Grid>
                    {
                        (props.formValues?.event_broadcasting === Constants.broadcastType.WEBINAR
                            || props.formValues?.event_broadcasting === Constants.broadcastType.MEETING)
                        &&
                        <Grid
                            container
                            specing={0}
                            lg={6}
                            sm={8}
                            className="flexCenter__row v4_colorTheme sceneryRowSelect"
                        >
                            <Grid item lg={1} sm={1}>
                                <Tooltip title={t("Moderator")} arrow>
                                <span>
                                    <ModeratorIcon />
                                </span>
                                </Tooltip>
                            </Grid>
                            <Grid item lg={6} sm={4}>
                                <SelectField
                                    name={"event_moderator"}
                                    id={'selectScenery_v4'}
                                    disabled={
                                        (props.current_event
                                            && (!props.current_event.event_state.is_future || props.current_event.event_is_published)
                                        )
                                        || (!props.formValues || props.formValues.event_broadcasting === 0)
                                    }
                                >
                                    {moderatorOptions && moderatorOptions.map((option, index) => {
                                        return (<MenuItem
                                            value={option.value}
                                        >
                                            {option.label}
                                        </MenuItem>);
                                    })}
                                </SelectField>

                            </Grid>
                        </Grid>
                    }
                </>
            }
        </>
    )
}

const mapStateToProps = (state) => {
    return {
        formValues: getFormValues('eventManageForm')(state),
        current_event: eventAction.getCurrentEvent(state),
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        updateEventForm: (field, value) => dispatch(change('eventManageForm', field, value)),
    }
};


EventBroadcastingOption = connect(mapStateToProps, mapDispatchToProps)(EventBroadcastingOption);


export default EventBroadcastingOption;