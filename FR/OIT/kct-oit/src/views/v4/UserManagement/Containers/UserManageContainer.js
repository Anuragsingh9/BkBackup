import React, {useEffect, useState} from "react";
import eventV4Api from "../../../../redux/action/apiAction/v4/event";
import {useDispatch} from "react-redux";
import {useParams} from "react-router-dom";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This container use for get the event users
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props passed from parent component
 * @param {Number} props.userTabValue user tab value
 * @param {Object} props.tableMetaData User table meta data
 * @param {Number} props.tableMetaData.rowPerPage row per page
 * @param {Number} props.tableMetaData.page page number
 * @param {Boolean} props.tableMetaData.reFetch To re-fetch the data
 * @returns {unknown}
 */
const useEventUserData = (props) => {
    const [userData, setUserData] = useState(null);
    const dispatch = useDispatch();
    const {event_uuid} = useParams();

    let data = {event_uuid: event_uuid}
    if (props.userTabValue === 1) {
        data.event_participants = 1;
    }

    /**
     * This use effect get the event user data
     */
    useEffect(() => {
        data.isPaginated = 1;
        data.rowPerPage = props.tableMetaData.rowPerPage;
        data.page = props.tableMetaData.page;
        data.key = props.searched_key
        data.orderBy = props.sort_user_model[0].field
        data.order = props.sort_user_model[0].sort
        if (props.userTabValue === 0 || props.userTabValue === 1 || props.tableMetaData.reFetch === true) {
            dispatch(eventV4Api.getEventUsers(data))
                .then((res) => {
                        setUserData(res.data);
                    }
                )
                .catch(err => {
                });
        }
    }, [props.tableMetaData,props.searched_key,props.sort_user_model])

    return userData;
}

export default useEventUserData;