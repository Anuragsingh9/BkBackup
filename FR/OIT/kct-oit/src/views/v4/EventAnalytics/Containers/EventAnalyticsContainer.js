import React, {useEffect, useState} from 'react';
import {useLocation} from "react-router-dom";
import Constants from "../../../../Constants";
import useRouteService from "../../../../routes/RouteService";
import eventV4Api from "../../../../redux/action/apiAction/v4/event";
import {useDispatch} from "react-redux";

let useEventAnalytics = (props) => {
    const {event_uuid, gKey} = props.match.params;
    const location = useLocation();
    const query = new URLSearchParams(location.search);

    const [queryData, setQueryData] = useState({
        event_uuid: event_uuid,
        r: Constants.EVENT_ANALYTICS.ALL_REC_DROP_DOWN
    });
    const routes = useRouteService(props);

    const dispatch = useDispatch();

    useEffect(() => {
        props.setPageRefresh(false)
        const data = {
            event_uuid: event_uuid,
        }

        let flag = true;

        if (query.get('r') && query.get('r') !== Constants.EVENT_ANALYTICS.ALL_REC_DROP_DOWN) {
            data['recurrence_uuid'] = query.get('r');
            let rec = props.recurrences_analytics?.recurrence_uuid ? props.recurrences_analytics.find(rec => rec.recurrence_uuid === query.get('r')) : null;
            if (rec) {
                flag = false;
                props?.filterAnalyticsList(rec.recurrence_uuid);
            }
        }
        if (query.get('from_date')) {
            data['from_date'] = query.get('from_date');
        }
        if (query.get('to_date')) {
            data['to_date'] = query.get('to_date');
        }

        setQueryData(data) // storing the query data in state

        if (flag) {
            props.getEventAnalytics(data)
                .then(res => {
                    if (res.data?.meta) {
                        props.updateAnalyticsRecList(res.data.meta.recurrences_list);
                    }
                    let resLength = res.data.data;
                    if (resLength) {
                        props.updateAnalyticsList(res.data.data);
                    }
                    if (!query.get('r') && !query.get('from_date') && !query.get('to_date') && resLength) {
                        let data = {
                            r: res.data.data[resLength - 1].recurrence_uuid
                        }
                        setQueryData(data) // storing the query data in state
                        props.history.push(routes.eventAnalytics(event_uuid, queryData))
                    }
                })
                .catch(res => console.log('dddddddddd res'))
        }
        // props?.setShowSkeleton(false)
    }, [query.get('r'), query.get('from_date'), query.get('to_date'), props.refreshPage, event_uuid]);


    useEffect(() => {
        // to check if its edit mode or create mode
        if (event_uuid) { // current mode is edit as event uuid is present in url
            dispatch(eventV4Api.getEvent(event_uuid))
                .then((res) => {
                    props.addEvent(res.data.data);
                    props.currentEventUuid(res.data.data.event_uuid);
                })
                .catch(err => {
                    if (err.response?.status === 404) {
                        props.history.push(`/${gKey}/v4/event-create`);
                    }
                });
        }
    }, [event_uuid])

    return '';
}

export default useEventAnalytics;