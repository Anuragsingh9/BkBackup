import React, {useEffect, useRef, useState} from 'react';
import newInterfaceActions from '../../redux/actions/newInterfaceAction';
import eventActions from "../../redux/actions/eventActions";
import {KeepContact as KCT} from '../../redux/types';
import Dashboard from '../NewInterFace/Index.js';
import {Provider as AlertContainer, useAlert} from 'react-alert';
import {connect} from "react-redux";
import Helper from '../../Helper';
import '../Index/Index.css';
import KeepContactagent from "../../agents/KeepContactagent";
import {useLocation, useParams} from "react-router-dom";
import _ from 'lodash';
import VideoElementRepository from "../VideoMeeting/VideoElementRepository";
import graphicActions from "../../redux/actions/graphicActions";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for dashboard container to get and set event details and load dashboard component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 *  @class
 * @component
 * @param {Object} props
 * @param {Function} props.setEventGroupLogo To update the logo of the header with respect to event group settings
 * @returns {JSX.Element}
 * @constructor
 */
const DashboardContainer = (props) => {
    const [sceneryData, setSceneryData] = useState({})
    let accessCodeValidated = false;

    const [eventGraphics, setEventGraphics] = useState(null)

    // Page loading state decides the rending of page loader.
    const [pageLoading, setLoading] = useState(true);
    const [eventMeta, setEventMeta] = useState({});

    // Initialisation fo message / alert ref to show alerts on success or error.
    const msg = useAlert(null);
    const {event_uuid} = useParams();

    let accessCode = null;

    useEffect(() => {
        accessCode = query.get('access_code');
        if (accessCode) { // query string contains access code then store it in redux
            localStorage.setItem('accessCode', accessCode);
        } else {
            accessCode = localStorage.getItem('accessCode');
        }
        getGraphics();
        getEventGraphicData(event_uuid ? event_uuid : '')
        return () => {
            if (accessCodeValidated && !query.get('access_code')) {
                // when component unmount access code should be removed so it will not used when
                // user come here through redirections
                localStorage.removeItem("accessCode");
            }
            props.setEventGroupLogo(null);
            props.implementGraphics(props.graphics_data)
        }

    }, [props.langChange])

    // get event design setting
    const getEventGraphicData = (data) => {

        try {
            return KeepContactagent.Event.getEventGraphicData(data).then((res) => {
                props.setEventGroupLogo(Helper.prepareEventGroupLogo(res.data.data));
                setEventGraphics(res.data.data);
                props.updateEventGraphics(res.data.data.graphic_data);
                // Helper.implementGraphicsHelper(res.data.data.graphic_data, true)
                let sceneryData = res.data.data.current_scenery_data;
                props.implementGraphics(
                    res.data.data.graphic_data,
                    _.has(sceneryData, ['category_type'])
                    && (sceneryData.category_type === 1 || sceneryData.category_type === 2)
                );
            }).catch((err) => {
                console.error("err in graphics fetch", err)
            })
        } catch (err) {
            console.error("err in graphics fetch", err)
        }
    }

    const redirection = (url, event_uuid) => {
        if (url && url.includes('keepcontact.events/')) {
            const redirect = !_.isEmpty(url.split('keepcontact.events')) && url.split('keepcontact.events')[1] ? url.split('keepcontact.events')[1] : '';
            if (redirect) {
                if (redirect.includes('login')) {
                    localStorage.removeItem('accessToken');
                }
                props.history && props.history.push(redirect);
            } else {
                props.history && props.history.push(`/quick-user-info/${event_uuid}`);
            }
        } else {
            props.history && props.history.push(url || `/quick-user-info/${event_uuid}${accessCode ? "?access_code=" + accessCode : ''}`);
        }
    }

    const isSceneryAvailable = (secenryData) => {
        return _.has(sceneryData, ['category_type']) && (sceneryData.category_type === 1 || sceneryData.category_type === 2);
    }


    const {search} = useLocation();
    const query = React.useMemo(() => new URLSearchParams(search), [search]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles getting event data and setting the event data to redux store as a global state
     * using actions.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    const getGraphics = () => {
        try {
            let accessCode = localStorage.getItem('accessCode');
            props.getEventGraphics(event_uuid, accessCode).then((val) => {
                localStorage.setItem('event_grid_rows', val.data.data.event_grid_rows);
                // val.data.data.header_line_one="custom text"
                if (query.get('access_code') && val.data.meta.access_code_valid) {
                    props.history.push({
                        pathname: `/dashboard/${event_uuid}`
                    })
                }
                let eventData = val.data.data;
                eventData = {
                    ...eventData,
                    event_actual_date: val.data.meta.event_actual_date,
                    event_actual_end_time: val.data.meta.event_actual_end_time,
                    event_actual_start_time: val.data.meta.event_actual_start_time,
                    event_actual_end_date: val.data.meta.event_actual_end_date,
                };
                setEventMeta(val.data.meta);
                let sceneryData = val.data.meta.current_scenery_data;
                if (_.has(val.data.meta, ["current_scenery_data"])) {
                    setSceneryData(sceneryData)
                    if (isSceneryAvailable(sceneryData)) {
                        // props.implementGraphics(props.graphics_data, true);
                    }
                }
                if (_.has(eventData, ['header_line_one'])) {
                    let {auth} = val.data.meta;
                    eventData = {
                        ...eventData,
                        event_version: 2,
                        accessCode,
                    };
                    props.setUserAuth(auth);
                    VideoElementRepository.MAX_USERS = eventData.event_conv_limit;
                    props.setEventData(eventData);
                    setLoading(false);

                } else {
                    // setting default graphics data
                    props.setEventGraphics(val.data.data);
                    props.setIsOnlineDataReceived(false);
                    setLoading(false);
                }
                if (val.data.meta.access_code_valid) {
                    accessCodeValidated = true;
                }
                props.getGroupGraphicsByEvent(event_uuid).then(res => {}).catch(err => {
                    console.error(err)
                })

            }).catch((err) => {
                if (_.has(err, ['response', 'status']) && err.response.status == '403') {
                    // error handling in case of redirection status of api - 403

                    return msg.show(Helper.handleError(err), {
                        type: "success",
                        onClose: () => {
                            let accessCode = query.get('access_code');
                            if (accessCode) { // query string contains access code then store it in redux
                                localStorage.setItem('accessCode', accessCode);
                            }
                            // if access code is present in local storage then redirect with access code
                            props.history.push({
                                pathname: `/event-list`
                            })
                        }
                    })
                } else {
                    msg && msg.show(Helper.handleError(err), {type: 'error'})
                }
            });
        } catch (err) {
            msg && msg.show(Helper.handleError(err), {type: 'error'})
        }
    }

    // Conditional rendering in case of pageLoading state is true
    if (pageLoading) {
        return (
            <div>
                <Helper.pageLoading />
                <AlertContainer
                    ref={msg}
                    {...Helper.alertOptions}
                />
            </div>
        )
    }
    // Conditional rendering in case of pageLoading state is false
    return (
        <React.Fragment>
            <AlertContainer
                ref={msg}
                {...Helper.alertOptions}
            />

            <Dashboard {...props} alert={msg}
                       eventMeta={eventMeta}
                       setEventMeta={setEventMeta}
                       getGraphics={getGraphics}
                       getEventGraphics={getEventGraphicData}
                       sceneryData={sceneryData}
                       setSceneryData={setSceneryData}
                       eventGraphics={eventGraphics} />

        </React.Fragment>
    )

}

const mapDispatchToProps = (dispatch) => {
    return {
        getEventGraphics: (id, accessCode = null) => dispatch(eventActions.Event.getEventGraphics(id, accessCode)),
        getGroupGraphicsByEvent: (id) => dispatch(eventActions.Event.getGroupGraphicsByEvent(id)),
        setEventGraphics: (data) => dispatch({type: KCT.EVENT.SET_EVENT_GRAPHICS, payload: data}),
        setIsOnlineDataReceived: (data) => dispatch({type: KCT.EVENT.CHANGE_ONLINE_USERS_IDENTIFIER, payload: data}),
        setEventData: (data) => dispatch(newInterfaceActions.NewInterFace.setEventData(data)),
        setUserAuth: (data) => dispatch({type: KCT.NEW_INTERFACE.SET_USER_AUTH, payload: data}),
        getMainHost: () => dispatch(eventActions.Event.getMainHostData()),
        setMainHost: (data) => dispatch(newInterfaceActions.NewInterFace.setMainHost(data)),
        setEventGroupLogo: (data) => dispatch({type: KCT.EVENT.SET_EVENT_GROUP_LOGO, payload: data}),

        updateEventGraphics: (data) => dispatch(graphicActions.updateEventGraphics(data)),
    }
}

const mapStateToProps = (state) => {
    return {
        graphics_data: state.NewInterface.interfaceGraphics,
        langChange: state.NewInterface.langChange,
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(DashboardContainer);