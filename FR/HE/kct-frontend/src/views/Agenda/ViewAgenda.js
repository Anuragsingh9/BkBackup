import React, {useEffect, useState} from 'react';
import _ from 'lodash';
import moment from 'moment';
import Moment from 'moment';
import ReactTooltip from 'react-tooltip';
import {connect} from "react-redux";
import {NavLink} from 'react-router-dom';
import {useTranslation} from 'react-i18next';
import VideoPlayer from '../NewInterFace/VideoPlayer/VideoPlayer';
import ToggleInfo from './ToggleInfo';
import Svg from '../../Svg';
import Helper from '../../Helper';
import CountDownTimer from '../NewInterFace/CountDownTimer/CountDownTImer';
import authActions from '../../redux/actions/authActions'
import {useLocation} from "react-router-dom";
import {KeepContact as KCT} from '../../redux/types';
import KeepContactagent from "../../agents/KeepContactagent";
import {useParams} from "react-router-dom";
import './ViewAgenda.css';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to view event agenda(no of moments present in the event).This component will
 * be render on a new tab of the browser when user click on agenda icon from the event list - past/future.
 * This component will consist following details:
 * 1. Content name
 * 2. Content start/end - date & time
 * 3. Content description - if added while creating an event.
 * 4. Product intro video - handled from super admin side.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.getEventDetails [Dispatcher|API] Method used to get the event details from api
 * @param {GraphicsData} props.graphics_data [State] This variable holds the current graphics data set in redux
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const ViewAgenda = (props) => {
    const {t} = useTranslation('qss');
    const [agenda, setagenda] = useState({
        moment_name: "",
        moment_type: '',
        start_time: '',
        end_time: '',
        moment_description: ''
    })
    const [showData, setData] = useState(t("Starts in"));
    const [eventDetails, setEventDetails] = useState({
        eventStartTime: "",
        eventEndTime: "",
        eventDate: "",
        eventTitle: "",
        agenda: []
    })

    const {event_uuid} = useParams();

    useEffect(() => {
        if (event_uuid) {
            getEventDetails(event_uuid);
            showTextAsTime()
        }
        getEventGraphicData(event_uuid ? event_uuid : '')
        return () => {
            Helper.implementGraphicsHelper(props.main_color)
        }
    }, [])

    // get event design setting
    const getEventGraphicData = (data) => {

        try {
            return KeepContactagent.Event.getEventGraphicData(data).then((res) => {
                props.setEventDesignSetting(res.data.data);
                Helper.implementGraphicsHelper(res.data.data.graphic_data)
            }).catch((err) => {
                console.error("err in graphics fetch", err)
            })


        } catch (err) {
            console.error("err in graphics fetch", err)
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will take event's start time and end time and prepare a timer to show on the page
     * so that user can how much time remaining to start this particular event(currently showing top right corner on the
     * show event agenda page).
     * If the event started already then it will show '<event name> has already started'
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const showTextAsTime = () => {
        const timeZone = 'Europe/Paris';
        const {eventStartTime, eventEndTime, eventDate} = eventDetails;
        const endTimes = moment(`${eventDate} ${eventEndTime}`).toDate();
        const startTimes = moment(`${eventDate} ${eventStartTime}`).toDate();
        const endTimeDiff = Helper.getTimeDifference(timeZone, endTimes);
        const startTimeDiff = Helper.getTimeDifference(timeZone, startTimes);

        if (startTimeDiff < 0 && endTimeDiff > 0) {
            setData(t("has already started"));
            setTimeout(() => {
                showTextAsTime();
            }, endTimeDiff)
        } else if (startTimeDiff > 0) {
            setData(t("Starts in"));
            setTimeout(() => {
                showTextAsTime();
            }, startTimeDiff)
        } else {
            setData(t("is over"));
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to get current event's data(start time, end time etc.) and
     * once the call executed successfully then it will update all relative states.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id Id of the event to fetch the details
     */
    const getEventDetails = (id) => {
        try {
            props.getEventDetails(id).then((res) => {
                if (res.data.status) {
                    const data = res.data.data;
                    setEventDetails({
                        ...eventDetails,
                        eventDate: data.date,
                        eventStartTime: data.start_time,
                        eventEndTime: data.end_time,
                        eventTitle: data.event_title,
                        agenda: data.agenda
                    })
                }
            }).catch((err) => {
                console.error(err)
            })
        } catch (err) {
            console.error(err)
        }
    }

    useEffect(() => {
        showTextAsTime()
    }, [eventDetails])

    // start time end time in usable format inside calender as per user's time-zone
    let startTime = Moment(Helper.getTimeUserTimeZone('Europe/Paris',
        `${eventDetails.eventDate} ${eventDetails.eventStartTime}`)).utc().format('YYYY-MM-DDTHH:mm:ss');

    let endTime = Moment(Helper.getTimeUserTimeZone('Europe/Paris',
        `${eventDetails.eventDate} ${eventDetails.eventEndTime}`)).utc().format('YYYY-MM-DDTHH:mm:ss');


    return (
        <div className='view_agenda'>
            <div className="white-bg col-md-12 text-center">
                <div className="col-lg-12 col-sm-12 text-right">
                    <div className="row">
                        <div className="col-lg-12 col-sm-12 text-right">
                            <div className="row pb-5">
                                <div className="regi-heading col-md-4 text-left">
                                    <h4 className="heading-color">Event Agenda</h4>
                                </div>
                                <div className='col-md-8'>
                                    <div className="blackbox no-border">
                                        <div className="d-inline-block text-left pull-left black-keep">
                                            <h4 className="">{eventDetails.eventTitle || ''}</h4>
                                            {/* <p className="">{state.description || ''}</p> */}
                                            <p className="heading-color">{showData}</p>
                                        </div>
                                        <div className="d-inline-block black-timer">
                                            {
                                                eventDetails
                                                && eventDetails.eventDate
                                                &&
                                                <CountDownTimer
                                                    fromQuickSignUp={true}
                                                    customisation={true}
                                                    page_Customization={{
                                                        event_date: Moment(eventDetails.eventDate).format('YYYY-MM-DD'),
                                                        event_start_time: eventDetails.eventStartTime,
                                                        time_zone: 'Europe/Paris'
                                                    }}
                                                />
                                            }
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {eventDetails.agenda && !_.isEmpty(eventDetails.agenda) &&
                eventDetails.agenda.map((v, i) => (
                        <div className="agenda_block text-left regi-heading col-md-12">
                            <ReactTooltip type="dark" effect="solid" id='agenda_icon' />
                            <h3 class="heading-color">
                                <span class="number-outer-agenda heading-color">
                                    <span class="number-inner-agenda agenda_counter">.</span>
                                </span>&nbsp;
                                <span
                                    dangerouslySetInnerHTML={
                                        v.moment_type == 1
                                            ? {__html: Svg.ICON.network_event_icon}
                                            : {__html: Svg.ICON.content_event_icon}
                                    }
                                    data-for='agenda_icon'
                                    data-tip={t(`${v.moment_type == 1 ? "Networking Moment" : "Content Moment"}`)}
                                ></span>
                                &nbsp;
                                {v.moment_name}
                            </h3>

                            <p className='agenda_timing'>
                                {Helper.dateTimeFormat(v.start_time, 'MMMM DD YYYY,')}
                                {Helper.dateTimeFormat(v.start_time, 'hh:mm A')}
                                - {Helper.dateTimeFormat(v.end_time, 'hh:mm A')}
                            </p>
                            {!_.isEmpty(v.moment_description) && <ToggleInfo discription={v.moment_description} />}
                        </div>
                    )
                )}
                <div className="col-md-12 col-sm-12 mb-20 agenda_video">
                    <div className="regi-heading dark-video text-left">
                        <h3 class="heading-color">Have a look at this short video</h3>
                        <div className="dark-video-inner">
                            <VideoPlayer isFromUpdate={true} url={props.graphics_data.video_url || ''} />
                        </div>
                    </div>
                </div>
            </div>
            <span className="col-md-12 btn_groups text-center">
                <NavLink to={{pathname: "/event-list"}}>
                    <button className="btn agenda_btn">Go to Event List</button>
                </NavLink>
            </span>
        </div>
    )
}

const mapStateToProps = (state) => {
    return {
        main_color: state.page_Customization.initData.graphics_data
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        getEventDetails: (data) => dispatch(authActions.Auth.getEventDetails(data)),
        setEventDesignSetting: (data) => dispatch({type: KCT.EVENT.SET_EVENT_DESIGN_SETTINGS, payload: data})

    }
}

export default connect(mapStateToProps, mapDispatchToProps)(ViewAgenda)