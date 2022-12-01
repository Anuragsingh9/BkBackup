import React, {useState, useEffect} from 'react'
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
 * @description This is a child component will show Draft events data which is coming in props from EventsList.
 * Draft events are the ones which are created successfully but are not published by the organiser (Pilot).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component "EventList.js"
 * @param {Object} props.columns To get the draft event columns(Event name, Event date, Event start time)
 * @param {Function} props.fetchList To fetch the draft event list
 * @param {Function} props.getRowId To get every row id
 * @param {Function} props.onPageChange When we change the page from next button
 * @param {Function} props.onPageSizeChange Handler for change the page size(10,20,30)
 * @param {Array} props.rows For event listing in table format
 * @param {Number} props.totalItems Event count
 * @returns {JSX.Element}
 * @constructor
 */
const DraftEvents = (props) => {
    // state to store current group past event data
    const [groupValues, setGroupValues] = useState({})
    const [pageSizeValue, setPageSizeValue] = useState('');
    const [draftEventData, setDraftEventData] = useState({
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


    useEffect(() => {
        getEvents()
    }, [props.selectedKey])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method fetch the initial data of user details and show in fields.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id User id
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
                //method to get events data
                getEvents();
                // props.setUser(res.data.data)
            }).catch((err) => {
                console.log(err)
                alert.show(Helper.handleError(err), {type: 'error'})
            })
        } catch (err) {
            console.log(err)
            alert.show(Helper.handleError(err), {type: 'error'})
        }
    }
    var draftList = {
        data: [],
        meta: {},
        links: {},
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will get the past event data of a specific group and show it in fields.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} page Number's of page
     * @param {Number} itemPerPage Item per page in draft event list
     */
    const getEvents = (page = 0, itemPerPage = 10) => {
        console.log(groupValues)
        console.log('gi', props.userGroupId.current_group && props.userGroupId.current_group.id)

        const groupId = props.userGroupId.current_group && props.userGroupId.current_group.id

        const sendData = {
            limit: itemPerPage,
            event_type: 'draft',
            isPaginated: 1,
            page: page + 1,
            //    limit: itemPerPage,
            groupKey: gKey,
        }

        try {
            props.getEvents(sendData).then(res => {
                // id property is nesseccery in data list material ui  component so we added id from event_uuid
                draftList = res.data;
                getFutureEvents();
                // const groupLength = meta.groups.length
                // props.showFilter( groupLength > 0)

                // const groupList= meta.groups
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
     * @description This method  divide  the future list of events
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const getFutureEvents = () => {
        draftList.data.map((event) => event["id"] = event.event_uuid)
        setDraftEventData(draftList);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will fetch future events data with respect to page number.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} page Number's of page
     */
    const fetchEventByPage = (page) => {
        getEvents(page, pageSizeValue);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will fetch future events data with respect to page row size.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} pageSize Number's of page in draft even list
     */
    const handlePageSizeChange = (pageSize) => {
        setPageSizeValue(pageSize)
        getEvents(0, pageSize);
    }


    return (

        <div>
            <EventTable
                columns={props.columns}
                // rows={draftEventData}
                getRowId={(row) => row.event_uuid}
                rows={draftEventData.data}
                totalItems={draftEventData.meta.total}
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
        // getDraftEvents: () => dispatch(userAction.getDraftEvents()),
        getEvents: (data) => dispatch(userAction.getEvents(data)),
        getUserData: (id) => dispatch(userAction.getUserData(id)),

    }
}

export default connect(mapStateToProps, mapDispatchToProps)(DraftEvents);

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