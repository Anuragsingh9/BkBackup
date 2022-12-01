import Helper from "../../../Helper";
import EventModel from "../Models/EventModel";
import moment from "moment-timezone";
import eventV4Api from "../../../redux/action/apiAction/v4/event";
import RecurrenceModel from "../Models/RecurrenceModel";
import Constants from "../../../Constants";
import eventAction from "../../../redux/action/apiAction/event";


/**
 * @global
 */

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To convert the event form submission into api request keys
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param val
 * @param gKey
 * @returns {{event_end_time: string, event_custom_link: (Object|*), event_space_max_capacity: (Object|*), event_description: (string|Object|*), event_title: (string|Object|*), event_start_time: string, event_start_date, group_key}}
 */
const mapFormToApiRequest = (val, gKey) => {

    let spaces = val.event_space_data.map(space => {
        let data = {
            space_line_1: space.space_line_1,
            space_line_2: space.space_line_2,
            space_is_vip: space.space_is_vip ? 1 : 0,
            space_host: space.space_host?.id,
            space_max_capacity: space.space_max_capacity,
        }
        if (space?.space_uuid) {
            data['space_uuid'] = space.space_uuid
        }
        return data;
    });

    let additional = {};

    if (val.event_type === Constants.eventFormType.EXECUTIVE || val.event_type === Constants.eventFormType.MANAGER) {
        additional = {
            ...additional,
            event_broadcasting: val.event_broadcasting,
            event_moderator: val.event_moderator,
        }
    }

    return {
        group_key: gKey,
        event_title: val.event_title,
        event_start_date: val.event_start_date.format('YYYY-MM-DD'),
        event_start_time: val.event_start_time.format('HH:mm:ss'),
        event_end_time: val.event_end_time.format('HH:mm:ss'),
        event_custom_link: val.event_custom_link,
        event_description: val.event_description,
        event_is_published: val.event_is_published ? 1 : 0,
        event_is_demo: val.event_is_demo ? 1 : 0,

        event_recurrence: {
            rec_type: val.event_rec_type,
            rec_end_date: val.event_rec_end_date.format('YYYY-MM-DD'),
            rec_weekdays: Helper.convertWeekDayToNumber(val.event_rec_selected_weekday),
            rec_month_date: val.event_rec_month_date,
            rec_interval: val.event_rec_interval,
            rec_month_type: val.event_rec_month_type,
            rec_on_month_week: val.event_rec_on_month_week,
            rec_on_month_week_day: val.event_rec_on_month_week_day,
        },

        event_scenery: val.event_scenery,
        event_grid_rows: val.event_grid_rows,
        event_scenery_asset: val.event_scenery_asset,
        event_top_bg_color: val.event_top_bg_color ? JSON.stringify(val.event_top_bg_color.value) : 0,
        event_component_op: val.event_component_op,
        event_spaces: spaces,
        event_type: val.event_type,
        event_conv_limit: val.event_conv_limit,
        ...additional,
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To convert the event get api response into event form fields keys
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param response
 * @returns {{event_end_time: (string|Object|*), event_custom_link: string, event_space_max_capacity: number, event_description: (string|Object|*), event_title: (string|Object|*), event_start_time: (string|Object|*), event_start_date: (Object|*), event_space_host: string}}
 */
const mapApiResponseToEventForm = (response) => {
    // preparing start time
    let startTime = new moment(response.event_start_time, 'HH:mm:ss');
    let endTime = new moment(response.event_end_time, 'HH:mm:ss');

    return {

        event_title: response.event_title,
        event_start_date: new moment(response.event_start_date, 'YYYY-MM-DD'),
        event_start_time: startTime,
        event_end_time: endTime,
        event_custom_link: response.custom_link.code,
        event_description: response.event_description,
        event_is_demo: response.event_is_demo,
        event_is_published: response.event_is_published,

        event_rec_start_date: startTime,
        event_rec_type: response.event_recurrence
            ? response.event_recurrence.rec_type
            : RecurrenceModel.event_rec_type,
        event_rec_end_date: response.event_recurrence
            ? new moment(response.event_recurrence.rec_end_date, 'YYYY-MM-DD')
            : startTime,
        event_rec_month_date: response.event_recurrence
            ? response.event_recurrence.rec_month_date
            : RecurrenceModel.event_rec_month_date,
        event_rec_interval: response.event_recurrence
            ? response.event_recurrence.rec_interval
            : RecurrenceModel.event_rec_interval,
        event_rec_month_type: response.event_recurrence
            ? response.event_recurrence.rec_month_type
            : RecurrenceModel.event_rec_month_type,
        event_rec_on_month_week: response.event_recurrence
            ? response.event_recurrence.rec_on_month_week
            : RecurrenceModel.event_rec_on_month_week,
        event_rec_on_month_week_day: response.event_recurrence
            ? response.event_recurrence.rec_on_month_week_day
            : RecurrenceModel.event_rec_on_month_week_day,
        event_rec_selected_weekday: response.event_recurrence
            ? Helper.convertNumberToWeekDay(response.event_recurrence.rec_weekdays)
            : RecurrenceModel.event_rec_selected_weekday,

        event_scenery: response.event_scenery,
        event_grid_rows: response.event_grid_rows,
        event_scenery_asset: response.event_scenery_asset,
        event_top_bg_color: {
            field: 'top_background_color',
            value: response.event_top_bg_color,
        },
        event_component_op: response.event_component_op,

        event_space_data: response.event_spaces,
        event_broadcasting: response.event_broadcasting,
        event_moderator: response.event_moderator,

        event_conv_limit: response.event_conv_limit,
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To prepare the event form with the empty values
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {{event_end_time: {h: *, m: *}, event_custom_link: string, event_space_max_capacity: number, event_description: string, event_title: string, event_start_time: {h: *, m: *}, event_start_date: moment | moment.Moment, event_space_host: null}}
 */
const prepareEmptyEventForm = (authUser, formMode) => {
    let eventModelObject = new EventModel();
    let eventModel = eventModelObject.get();
    return {
        // event basic fields
        event_title: eventModel.event_title,
        event_start_date: eventModel.event_start_date,
        event_start_time: eventModel.event_start_time,
        event_end_time: eventModel.event_end_time,
        event_description: eventModel.event_description,
        event_custom_link: eventModel.custom_link.code,
        event_is_published: false,
        event_is_demo: false,

        // recurrence fields
        event_rec_start_date: RecurrenceModel.event_rec_start_date,
        event_rec_type: RecurrenceModel.event_rec_type,
        event_rec_end_date: RecurrenceModel.event_rec_end_date,
        event_rec_month_date: RecurrenceModel.event_rec_month_date,
        event_rec_interval: RecurrenceModel.event_rec_interval,
        event_rec_month_type: RecurrenceModel.event_rec_month_type,
        event_rec_on_month_week: RecurrenceModel.event_rec_on_month_week,
        event_rec_on_month_week_day: RecurrenceModel.event_rec_on_month_week_day,
        event_rec_selected_weekday: RecurrenceModel.event_rec_selected_weekday,

        // scenery fields
        event_scenery: 0,
        event_grid_rows: Constants.gridRowSize[0],
        event_scenery_asset: 0,
        event_top_bg_color: {
            field: 'top_background_color',
            value: {"r": 255, "g": 255, "b": 255, "a": 1},
        },
        event_component_op: 92,

        // event spaces data
        event_space_data: [
            {
                space_line_1: "Welcome",
                space_line_2: "Space",
                space_max_capacity: 144,
                space_host: authUser,
                space_is_vip: false,
                space_is_default: 1,
            },
        ],
        event_type: formMode ? formMode : 1,

        // broadcasting keys
        event_broadcasting: 0,
        event_moderator: 0,

        // event conversation related keys
        event_conv_limit: EventModel.event_conv_limit,
    }
}

const handleEventFormSubmit = (val, dispatch, props) => {
    props.updateInputField(false)
    let gKey = props.match.params.gKey;
    // marking the flag false so form does not get submit twice due to publish button click
    val = {
        ...val,
        // adding the publish state from the button click
        event_is_published: props.publish_submit_action,
    }
    let dataToSend = mapFormToApiRequest(val, gKey);
    if (props.match.params.event_uuid) {
        dataToSend = {
            ...dataToSend,
            _method: "PUT",
            event_uuid: props.match.params.event_uuid,
        }
    }

    dispatch(eventV4Api.createEvent(dataToSend)).then((res) => {
        props.alert.show("Record Added SuccessFully", {type: "success"});
        props.publishEventSubmit(false);
        props.saveEventSubmit(false);
        if (!props.match.params.event_uuid) {
            props.history.push(`/${gKey}/v4/event-update/${res.data.data.event_uuid}`);
        } else if (res.data.data.event_links) {
            props.updateCurrentEvent({...val, ...res.data.data});
            props.updateEventForm('event_space_data', res.data.data.event_spaces);
        }
    }).catch((err) => {
        Helper.handleApiError(err, props.alert);
        props.publishEventSubmit(false);
        props.saveEventSubmit(false);
        props.updateInputField(true)
    });
}

const updateRecOptionLabel = (formValues = null, setRecOptionsLabel) => {
    if (formValues === null) {
        setRecOptionsLabel({
            daily: "Daily",
            weekly: "Weekly",
            monthly: "Monthly",
            weekday: "Weekday",
        })
    } else {
        let label = Helper.prepareRecurrenceLine(
            formValues.event_rec_interval,
            formValues.event_rec_type,
            formValues.event_rec_end_date.format('YYYY-MM-DD'),
            formValues.event_rec_month_date,
            formValues.event_rec_selected_weekday,
            formValues.event_rec_month_type,
            formValues.event_rec_on_month_week,
            formValues.event_rec_on_month_week_day,
        )
        setRecOptionsLabel({
            daily: formValues.event_rec_type === Constants.recurrenceType.DAILY ? label : 'Daily',
            weekly: formValues.event_rec_type === Constants.recurrenceType.WEEKLY ? label : 'Weekly',
            monthly: formValues.event_rec_type === Constants.recurrenceType.MONTHLY ? label : 'Monthly',
            weekday: formValues.event_rec_type === Constants.recurrenceType.WEEKDAY ? label : 'Weekday',
        })
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To validate the event manage form asynchronously as api is being hit here so the validation will be
 * added after the result is fetched for custom link
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param values
 * @param dispatch
 * @param props
 * @returns {*}
 */
const asyncValidate = (values, dispatch, props) => {
    let data = {
        key: `${values.event_custom_link}`,
    }
    if (props.current_event?.event_uuid) {
        data['current_event'] = props.current_event?.event_uuid;
    }
    return dispatch(eventAction.checkEventCode(data)).then(res => {
        if (!res.data?.data?.available) {
            throw {event_custom_link: "Selected link not available"}
        }
    });
}

let EventManageHelper = {
    mapFormToApiRequest,
    mapApiResponseToEventForm,
    prepareEmptyEventForm,
    handleEventFormSubmit,
    updateRecOptionLabel,
    asyncValidate,
}

export default EventManageHelper;
