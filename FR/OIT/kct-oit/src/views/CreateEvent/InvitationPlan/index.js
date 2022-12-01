import React, {useEffect, useState} from 'react';
import {Grid} from '@material-ui/core';
import {useSelector, useDispatch} from 'react-redux';
import _ from 'lodash';
import './invitationPlan.css';
import EventInfo from './EventInfo.js';
import InvitationPlanIcon from '../../Svg/InvitationPlanIcon.js';
import LoadingContainer from '../../Common/Loading/Loading.js';
import eventAction from '../../../redux/action/apiAction/event'


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a parent component for invitation plan  which contains all type of details of events like
 * start time, end time , date , type , title , description , and registration window where event registration can be
 * manage, and can be edit like open and close time of registration of event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.setShowEventLinks To set show event links
 * @param {Function} props.setShowLiveTab To set show live tab
 * @param {Function} props.isAutoCreated Is auto created event
 * @returns {JSX.Element}
 * @constructor
 */
const InvitationPlan = (props) => {
    console.log("dddddddddddd here");
    // user data to get group id
    const event_data = useSelector((data) => data.Auth.eventDetailsData)
    // dispatch hook from redux
    const dispatch = useDispatch();

    const [invitationData, setInvitaitonData] = useState({
        name: "",
        description: '',
        start_time: '',
        end_time: '',
        type: '',
        reg_start: '',
        reg_end: '',
        agenda: '',
        share_agenda: '',
        status: '',
        isRegOpen: ""
    })

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method is used for fetching data on first time rendering of the component
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    useEffect(() => {
        if (_.has(event_data, ['event_uuid'])) {
            getInviatationData(event_data.event_uuid)

        }
    }, []);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to display data of event by fetch the data from server by using API call when
     * page loads first time and updates states values like name , description , start time , end time
     * event type , registration start and end time ,agenda , status , and registration status.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id   Event uuid of the event
     */
    const getInviatationData = (id) => {
        try {
            dispatch(eventAction.getDraft(id)).then((res) => {
                const data = res.data.data;
                setInvitaitonData({
                    name: _.has(data, ["name"]) && data.name !== null ? data.name : '',
                    description: _.has(data, ["description"]) && data.description !== null ? data.description : '',
                    start_time: _.has(data, ["start_time"]) && data.start_time !== null ? data.start_time : '',
                    end_time: _.has(data, ["end_time"]) && data.end_time !== null ? data.end_time : '',
                    type: _.has(data, ["type"]) && data.type !== null ? data.type : '',
                    reg_start: _.has(data, ["reg_start"]) && data.reg_start !== null ? data.reg_start : '',
                    reg_end: _.has(data, ["reg_end"]) && data.reg_end !== null ? data.reg_end : '',
                    agenda: _.has(data, ["agenda"]) && data.agenda !== null ? data.agenda : '',
                    share_agenda: _.has(data, ["share_agenda"]) && data.share_agenda !== null ? data.share_agenda : '',
                    status: _.has(data, ["status"]) && data.status !== null ? data.status : '',
                    isRegOpen: _.has(data, ["is_reg_open"]) && data.is_reg_open !== null ? data.is_reg_open : 1,
                    recur_data: _.has(data, ['recur_data']) ? data.recur_data : null,
                })
                if (_.has(data, ["status"]) && data.status !== null && data.status === 1) {
                    props.setShowEventLinks(true);
                }

            }).catch((err) => {
                console.log(err)

            })
        } catch (err) {
            console.log(err)
        }

    }

    return (
        <LoadingContainer>
            <div className="invitationPlanWrap">
                <Grid item xs={12} className="TogglerRow">
                    <Grid container className="ToggleMainFlex">
                        <Grid item className="Flex-1"><InvitationPlanIcon /></Grid>
                        <Grid item className="Flex-2">
                            Event Details
                            <span className="SmallSubHeading">
                                Publish your Event and make it open for Participant's Registration
                            </span>
                        </Grid>
                    </Grid>
                </Grid>
                <div className="invitation_info_wrap">
                    <EventInfo
                        name={invitationData.name}
                        description={invitationData.description}
                        start_date={invitationData.start_time}
                        end_date={invitationData.end_time}
                        event_type={invitationData.type}
                        reg_start={invitationData.reg_start}
                        reg_end={invitationData.reg_end}
                        agenda={invitationData.agenda}
                        share_agenda={invitationData.share_agenda}
                        status={invitationData.status}
                        is_reg_open={invitationData.isRegOpen}
                        setShowEventLinks={props.setShowEventLinks}
                        setShowLiveTab={props.setShowLiveTab}
                        isAutoCreated={props.isAutoCreated}
                        recur_data={invitationData.recur_data}
                    />
                </div>
            </div>
        </LoadingContainer>
    )
}

export default InvitationPlan;