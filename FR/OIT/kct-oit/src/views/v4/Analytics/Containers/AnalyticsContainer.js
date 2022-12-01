import React, {useEffect, useState} from 'react';
import {connect, useDispatch} from "react-redux";
import eventV4Api from "../../../../redux/action/apiAction/v4/event";
import moment from 'moment-timezone'

let useAnalyticsData = (props) => {
    const [analyticsData, setAnalyticsData] = useState(null);
    const dispatch = useDispatch();
    const groupKeys = props.fetch_analytic_group_dropdown;
    const searchingEnable = props.searched_key.length > 1;
    const initialData = {
        groupKey: groupKeys,
        pagination:1,
        row_per_page:10,
        page:1,
        from_date: moment(props.range_picker_val[0]).format('YYYY-MM-DD HH:mm:ss'),
        to_date: moment(props.range_picker_val[1]).format('YYYY-MM-DD HH:mm:ss'),
        order_by: 'event_date',
        order:'desc',
    };
    if (searchingEnable){
        initialData.key = props.searched_key
    }

    const handleApiCall = () =>{
        if (props.value === 0) {
            dispatch(eventV4Api.getAnalyticsData(initialData))
                .then((res) => {
                        if (res.data.status === true) {
                            setAnalyticsData(res.data);
                        }
                    }
                )
                .catch(err => {
                });
        }
    }

    useEffect(() => {
        handleApiCall()
    }, [props.searched_key])

    useEffect(() => {
        handleApiCall()
    }, [dispatch, props.value,props.fetch_analytic_group_dropdown,props.range_picker_val])

    return analyticsData;

}

export default useAnalyticsData;