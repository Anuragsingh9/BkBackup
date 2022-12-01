import React, {useEffect, useState} from 'react'
import EventTable from './EventTable'
import Helper from '../../Helper';
import {useAlert} from 'react-alert';
import {connect} from 'react-redux';
import userAction from '../../redux/action/apiAction/user';
import eventAction from '../../redux/action/apiAction/event'
import userReduxAction from '../../redux/action/reduxAction/user';
import {useParams} from "react-router-dom";


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This child component will show future events data which is coming in props from EventsList.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Received column and row data in the form of array of objects.Inherited from parent component
 * "EventList.js" file.
 * @param {Array} props.rows Array that consist information of a row data in the object form. eg - in future event list
 * table all events data will be received as an array of object where each object contain specific event information.
 * @param {Array} props.columns Array that consist information of a column category data in the object form  to show
 * in the event list table's column.
 * @returns {JSX.Element}
 * @constructor
 */
const FutureEvents = (props) => {
    // state to store current group future event data
    const [groupValues, setGroupValues] = useState({})
    const [pageSizeValue, setPageSizeValue] = useState('');
    const [futureEventData, setFutureEventData] = useState({
        data: [],
        meta: {},
        links: {},
    });
    const {gKey} = useParams();
    const alert = useAlert();

    useEffect(() => {

        let user_badge = {};
        const data = localStorage.getItem('user_data');
        const userId = localStorage.getItem("userId")
        if (data) {

            user_badge = JSON.parse(data);
        }
        if (userId) {
            getProfileData(userId);

        }
    }, []);



    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method fetch the initial data of user details and show in fields.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id userID
     */
    const getProfileData = (id) => {
        try {
            props.getUserData(id).then((res) => {
                const {data} = res.data
                console.log(data.current_group.id);
                setGroupValues({
                    ...groupValues,
                    groupId: data.current_group.id
                })
                const group_id = data.current_group.id;
                getEvents();
                //set user profile data in state
                props.setUser(res.data.data)
            }).catch((err) => {
                console.log(err)
                alert.show(Helper.handleError(err), {type: 'error'})
            })
        } catch (err) {
            console.log(err)
            alert.show(Helper.handleError(err), {type: 'error'})
        }
    }
    var futureList = {
        data: [],
        meta: {},
        links: {},
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will get the future event data of a specific group and show it in fields.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} page Page number
     * @param {Number} itemPerPage Item per page
     */
    const getEvents = (page = 0, itemPerPage = 10) => {
        console.log(groupValues)
        console.log('gi', props.userGroupId.current_group && props.userGroupId.current_group.id)

        const groupId = props.userGroupId.current_group && props.userGroupId.current_group.id

        const sendData = {
            limit: itemPerPage,
            event_type: 'future',
            isPaginated: 1,
            page: page + 1,
            groupKey: gKey,

        }

        // if(props.selectedKey) {
        //     sendData.group_key = props.selectedKey
        // }

        try {
            props.getEvents(sendData).then(res => {
                // id property is nesseccery in data list material ui  component so we added id from event_uuid
                futureList = res.data;
                console.log("ddddddddddd", futureList)
                getFutureEvents();
                // let meta = res.meta ? res.meta:[]
                // const groupLength = meta && meta.groups.length
                //  props.showFilter( groupLength > 0)
                //  const groupList= meta.groups

                // props.setList(groupList)

            }).catch((err) => {
                console.log(err)
            })
        } catch (err) {
            console.log(err)
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function to get future events and set it into a state.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const getFutureEvents = () => {
        futureList.data.map((event) => event["id"] = event.event_uuid)
        setFutureEventData(futureList);
    }
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will fetch future events data with respect to page number.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} page Number of pages
     */
    const fetchEventByPage = (page) => {
        console.log('pageee', page)
        getEvents(page, pageSizeValue);
    }
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will fetch future events data with respect to page row size.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} pageSize Page size
     */
    const handlePageSizeChange = (pageSize) => {
        console.log("pageSize", pageSize)
        setPageSizeValue(pageSize)
        getEvents(0, pageSize);
    }

    console.log("listttttttttttttttt", props, futureEventData)
    return (

        <div>
            <EventTable
                columns={props.columns}
                rows={futureEventData.data}
                getRowId={(row) => row.event_uuid}
                totalItems={futureEventData.meta.total}
                fetchList={fetchEventByPage}
                onPageSizeChange={handlePageSizeChange}
                onPageChange={fetchEventByPage}
            />
        </div>
    )
}

const mapStateToProps = (state) => {
    return {
        userGroupId: state.Auth.userSelfData
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        getDraftEvents: () => dispatch(userAction.getDraftEvents()),
        getUserData: (id) => dispatch(userAction.getUserData(id)),
        getEvents: (data) => dispatch(userAction.getEvents(data)),
        setUser: (data) => dispatch(userReduxAction.setUserData(data)),
        deleteEvent: (data) => dispatch(eventAction.deleteEvent(data))
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(FutureEvents);

/**
 * @deprecated
 */
let meta = {
    "groups": [
        {
            "group_key": "default",
            "group_name": "default"
        },
        {
            "group_key": "newgroup",
            "group_name": "newgroup"
        },
        {
            "group_key": "fifth",
            "group_name": "fifth"
        },
        {
            "group_key": "fi0007",
            "group_name": "fi0007"
        },
        {
            "group_key": "fi0008",
            "group_name": "fi0008"
        },
        {
            "group_key": "FI0027",
            "group_name": "FI0027"
        }
    ]
}