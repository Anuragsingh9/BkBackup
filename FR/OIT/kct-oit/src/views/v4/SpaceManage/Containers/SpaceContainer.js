import React, {useEffect, useState} from 'react';
import {useParams} from "react-router-dom";
import {useDispatch} from "react-redux";
import eventV4Api from "../../../../redux/action/apiAction/v4/event";
import SpaceManageHelper from "../SpaceManageHelper";
import _ from "lodash";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Event data manager to provide and manipulate the event data from api or from redux
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.addEvent To add the event in redux store
 * @param {Function} props.currentEventUuid To update the current event uuid in redux store
 * @returns {EventModel|Object}
 * @component
 * @class
 */
let useSpaceData = (props) => {
    const [event, setEventData] = useState({});

    const {event_uuid, gKey} = useParams();
    let availableSpaceData = props.eventFormValues.event_space_data;

    const editModeON = _.has(props, ['spaceEditMode']) && props.spaceEditMode == true && _.has(props, ['spaceIndex']);

    useEffect(() => {
        if (editModeON) {
            setEventData({...availableSpaceData[props.spaceIndex], space_index:props.spaceIndex});
        } else {
            setEventData(SpaceManageHelper.prepareEmptySpaceForm());
        }
    }, [event_uuid]);

    return event;
}

export default useSpaceData;