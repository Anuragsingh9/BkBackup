import Helper from "../../../Helper";
import EventModel from "../Models/EventModel";
import moment from "moment-timezone";
import eventV4Api from "../../../redux/action/apiAction/v4/event";
import RecurrenceModel from "../Models/RecurrenceModel";
import Constants from "../../../Constants";
import eventAction from "../../../redux/action/apiAction/event";
import _ from "lodash";


/**
 * @global
 */



/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To prepare the event form with the empty values
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {{event_end_time: {h: *, m: *}, event_custom_link: string, event_space_max_capacity: number, event_description: string, event_title: string, event_start_time: {h: *, m: *}, event_start_date: moment | moment.Moment, event_space_host: null}}
 */
const prepareEmptySpaceForm = () => {
    return {
        space_line_1: "",
        space_line_2: "",
        space_max_capacity: '',
        space_is_vip: false,
        space_is_default: 0,
    }
}



/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is developed to perform delete space action from event creation from component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Number} index Index value of a selected space for edit.
 * @param {Object} props Getting event form current value and redux method("updateEventForm") to update main form data.
 */
 const handleDeleteSpace = (index, props) => {
    let eventCurrentData = props.eventFormValues.event_space_data;

    //Removing space by their index value received from props, 2nd arguments is for no of elements should be delete.
    eventCurrentData.splice(index,1);

    props.updateEventForm('event_space_data', eventCurrentData);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To handle the submit form values for the space form
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param val
 * @param dispatch
 * @param props
 */
const handleSpaceFormSubmit = (val, dispatch, props) => {
    let eventCurrentData = props.eventFormValues.event_space_data
    let allSpaceData = eventCurrentData;
    if(_.has(val,['space_index'])){
        allSpaceData[val.space_index] = val;
    }
    else{
        allSpaceData = [...eventCurrentData, val]
    }
    props.updateEventForm('event_space_data', allSpaceData);
    props.closeSpacePopup();
}

let SpaceManageHelper = {
    prepareEmptySpaceForm,
    handleSpaceFormSubmit,
    handleDeleteSpace,
}

export default SpaceManageHelper;