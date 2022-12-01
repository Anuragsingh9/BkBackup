import React, {useEffect, useState} from 'react';
import "../EventManage.css"
import {useParams} from "react-router-dom";
import {useDispatch} from "react-redux";
import eventV4Api from "../../../../redux/action/apiAction/v4/event";
import EventManageHelper from "../EventManageHelper";

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
let useEventData = (props) => {
    const [event, setEventData] = useState({});

    const {event_uuid, gKey} = useParams();
    const dispatch = useDispatch();

    useEffect(() => {
        if (props.location.state?.formMode) {
            props.updateEventForm('event_type', props.location.state.formMode);
        }
    }, [props.location]);

    useEffect(() => {
        // to check if its edit mode or create mode
        if (event_uuid) { // current mode is edit as event uuid is present in url
            dispatch(eventV4Api.getEvent(event_uuid))
                .then((res) => {
                    let data = EventManageHelper.mapApiResponseToEventForm(res.data.data);
                    setEventData(data);
                    props.history.replace({...props.history.location, state: {
                        ...props.history.location.state,
                            formMode: res.data.data.event_type,
                    }});
                    props.addEvent(res.data.data);
                    props.currentEventUuid(res.data.data.event_uuid);
                })
                .catch(err => {
                    if (err.response?.status === 404) {
                        props.history.push(`/${gKey}/v4/event-create`);
                    }
                });
        } else { // create mode is there so providing the fresh set of data with empty or default values

            setEventData(EventManageHelper.prepareEmptyEventForm(props.user_badge, props.formMode));
            props.currentEventUuid(null);
        }
    }, [event_uuid, props.location?.state?.formMode]);

    return event;
}

export default useEventData;