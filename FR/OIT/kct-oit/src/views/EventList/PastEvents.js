import React, { useEffect, useState } from 'react'
import EventTable from './EventTable'
import Helper from '../../Helper';
import { useAlert} from 'react-alert';
import { connect } from 'react-redux';
import userAction from '../../redux/action/apiAction/user';
import userReduxAction from '../../redux/action/reduxAction/user';
import { useParams } from "react-router-dom";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This child component will show past events data which is coming in props from EventList.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Columns} props.columns Columns required on event list
 * @param {Event} props.rows All past event data
 */
const PastEvents = (props) => {

    // state to store current group past event data
    const [groupValues, setGroupValues] = useState({})
    const [pageSizeValue , setPageSizeValue] = useState('');
    const [pastEventData, setPastEventData] = useState({
        data: [],
        meta: {},
        links: {},
    })
    const alert = useAlert();
    const { gKey } = useParams();

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
        // getDraft();


    }, []);


    // useEffect(()=>{
    //     getEvents()
    //     console.log("ussseeeee runnnnn  1111111111",props.selectedKey)
    // }, [props.selectedKey] )

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method fetch the initial data of user details and show in fields.
     * -----------------------------------------------------------------------------------------------------------------
     * 
     * @method 
     * @param {String} id User ID
     */
    const getProfileData = (id) => {

        try {
            props.getUserData(id).then((res) => {
                const { data } = res.data
                console.log(data.current_group.id);
                setGroupValues({
                    ...groupValues,
                    groupId: data.current_group.id
                })
                const group_id = data.current_group.id;
                getEvents();
                props.setUser(res.data.data)
            }).catch((err) => {
                console.log(err)
                alert.show(Helper.handleError(err), { type: 'error' })
            })
        } catch (err) {
            console.log(err)
            alert.show(Helper.handleError(err), { type: 'error' })
        }
    }
    var futureList = [];
    var pastList = {
        data: [],
        meta: {},
        links: {},
    };
    var draftList = [];

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will get the past event data of a specific group and show it in fields.
     * -----------------------------------------------------------------------------------------------------------------
     * 
     * @method
     * @param {number} page Page number
     * @param {number} itemPerPage Item per page
     */
    const getEvents = (page = 0, itemPerPage = 10) => {
        console.log("sendddddddddd",itemPerPage)
        console.log('gi', props.userGroupId.current_group && props.userGroupId.current_group.id)

        const sendData = {
            limit: itemPerPage,
            event_type: 'past',
            isPaginated: 1,
            page: page + 1,
            groupKey: gKey,

        }
        // if(props.selectedKey) {
        //     sendData.group_key = props.selectedKey
        // }

        try {
            props.getEvents(sendData).then(res => {
                console.log(res.data.data)
                // id property is nesseccery in data list material ui  component so we added id from event_uuid
                pastList.data = res.data.data;
                pastList.meta = res.data.meta;
                pastList.links = res.data.links;
                gePastEvents();

                // let meta = res.meta ? res.meta :[]

                // const groupLength =  meta.groups.length
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
     * @description This method prepares the list of all past events
     * -----------------------------------------------------------------------------------------------------------------
     * 
     * @method
     */
    const gePastEvents = () => {
        pastList.data.map((event) => event["id"] = event.event_uuid)
        setPastEventData(pastList)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will fetch future events data with respect to page number.
     * -----------------------------------------------------------------------------------------------------------------
     * 
     * @method
     * @param {number} page Page number
     */
    const fetchEventByPage = (page) => {
        console.log('pagee', page)
        getEvents(page, pageSizeValue);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description It will fetch future events data with respect to page row size.
     * -----------------------------------------------------------------------------------------------------------------
     * 
     * @method
     * @param {number} pageSize Page size
     */
    const handlePageSizeChange = (pageSize) => {
        console.log("pageSize",pageSize )
        setPageSizeValue(pageSize)
        getEvents(0, pageSize);
    }




    return (
        <div>
            <EventTable
                columns={props.columns}
                rows={pastEventData.data}
                totalItems={pastEventData.meta.total}
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
        getUserData: (id) => dispatch(userAction.getUserData(id)),
        getEvents: (data) => dispatch(userAction.getEvents(data)),
        setUser: (data) => dispatch(userReduxAction.setUserData(data)),
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(PastEvents);

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
    ]}