import React, {useEffect, useRef, useState} from 'react';
import './Style.css';
import Helper from "../../../Helper";
import {connect} from "react-redux";
import {Provider as AlertContainer } from 'react-alert';
import authActions from "../../../redux/actions/authActions";
import newInterfaceActions from '../../../redux/actions/newInterfaceAction';
import HeaderDropDown from './HeaderDropdown/HeaderDropdown.js';
import eventActions from '../../../redux/actions/eventActions';
import _ from 'lodash';
import SvgIcon from './SvgIcon.js';
import CSSGenerator from '../../DynamicCss.js';
import {reactLocalStorage} from 'reactjs-localstorage';
import socketManager from "../../../socket/socketManager";
import videoElementRepo from '../../VideoMeeting/VideoElementRepository';
import videoMeeting from '../../VideoMeeting/VideoMeetingClass';
import {KeepContact as KCT} from '../../../redux/types';
import routeAgent from "../../../agents/routeAgent";
import Constants from "../../../Constants";
import {useNavigate} from "react-router-dom";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description - This is common component of Header which renders on event page and have features like shows events
 * logo in left side and header dropdown which consist of option like My Event Registration, Logout and Change password
 * pages option
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {UserBadge} props.event_badge User badge details
 * @param {GraphicsData} props.graphics_data [State] This variable holds the current graphics data set in redux
 * @param {EventData} props.event_data Current event data
 * @param {String} props.event_group_logo Current event group logo
 *
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
var Header = (props) => {

    const [badge_updated, setBadge_updated] = useState(false);

    const alert = useRef(null)
    const navigate = useNavigate();

    const {event_badge, graphics_data, event_data, dropdown} = props;


    useEffect(() => {
        const {event_badge} = props;
        if (reactLocalStorage.get("accessToken") && (!_.has(event_badge, ['user_fname'])
            || reactLocalStorage.get("fname") != event_badge.user_fname) && checkOtp()) {
            if (badge_updated == false) {
                getBadgeData();
                setBadge_updated(true)
            } else {
                getBadgeData()
            }

        }

    }, [])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for return Boolean value on location based of url when project loads.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @return {Boolean}
     */
    const checkOtp = () => {
        const location = window.location.href;
        return !(location && (location.includes('otp') || location.includes('register') || location.includes('login')))
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This Method is used to Fetch users badge data from server by using API call on successful response
     * it updates users badge data by using setBadge() other wise returns error and upon clicking on  close button of
     * error notification toast it redirect on "quick-otp" page.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const getBadgeData = () => {
        try {
            props.getBadge().then((res) => {
                const data = res.data.data;
                props.setBadge(data);
            }).catch((err) => {
                if (err?.response?.status === 403 && err?.response.data?.redirect_code === 1002) {
                    return alert && alert.current.show(Helper.handleError(err), {
                        type: "error",
                        onClose: () => {
                            props.history.push({
                                pathname: routeAgent.routes.QUICK_REGISTER(err.response.data.last_event_uuid),
                                state: {
                                    formMode: Constants.SIGN_UP_FORM_MODE.OTP_VERIFY,
                                    fname: err.response.data.user.fname,
                                    lname: err.response.data.user.lname,
                                    email: err.response.data.user.email,
                                }
                            })
                        }

                    })
                } else {
                    alert && alert.current.show(Helper.handleError(err), {type: 'error'})
                }
            })
        } catch (err) {
            alert && alert.current.show(Helper.handleError(err), {type: 'error'})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle the leave conversation on logout. When user is in conversation
     * with some one in networking and between the conversation if user logouts then this method will help in leaving
     * conversation and sends data on server using API call and on successful response it removes the conversation on
     * socket  .
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const leaveConversation = () => {
        const {spaces_data, event_badge} = props;
        if (spaces_data.current_joined_conversation !== null &&
            _.has(spaces_data, ['current_joined_conversation', 'conversation_uuid'])) {
            const formData = new FormData()
            formData.append('conversation_uuid', spaces_data.current_joined_conversation.conversation_uuid)
            formData.append('_method', 'DELETE')
            try {
                props.leaveConversation(formData)
                    .then((res) => {
                        props.conversationLeave({
                            conversationId: spaces_data.current_joined_conversation.conversation_uuid,
                            type: 'delete',
                            userId: event_badge.user_id
                        });

                        props.deleteConversation(spaces_data.current_joined_conversation)
                        const authId = spaces_data.current_joined_conversation.conversation_users.find((user) => {
                            return user.hasOwnProperty('is_self');
                        });

                        const data = {
                            conversationId: spaces_data.current_joined_conversation.conversation_uuid,
                            type: res.data.data === true ? 'delete' : 'remove',
                            userId: authId.user_id,
                        }
                        socketManager.emitEvent.CONVERSATION_LEAVE(data);
                        videoElementRepo.resetSeats();
                        videoMeeting.stopVideo();
                        socketLogout();
                    })
                    .catch((err) => {
                        console.error(err, 'this error in leave call')
                    })
            } catch (err) {
                console.error(err, 'this error in leave call')
            }
        } else {
            socketLogout();
        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to call leaveConversation() method on logout action.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const logout = () => {
        leaveConversation();

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle logout action from socket side and on getting successful response
     * it updates default designs and redirect to quick-login page.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const socketLogout = () => {
        try {
            const formData = new FormData;
            props.logout(formData)
                .then((res) => {
                    const colorObj = reactLocalStorage.get("colorObj");
                    if (Helper.objLength(colorObj)) {
                        const parsedColor = JSON.parse(colorObj);
                        CSSGenerator.generateDefaultCSS(parsedColor);
                    }
                    localStorage.removeItem('accessToken');
                    navigate(routeAgent.routes.QUICK_LOGIN());
                })
                .catch((err) => {
                    alert && alert.current.show(Helper.handleError(err), {type: 'error'})
                })
        } catch (err) {
            alert && alert.current.show(Helper.handleError(err), {type: 'error'})
        }
    }


    const activeEventId = localStorage.getItem("active_event_uuid");

    return (
        <section id="header">
            <div className="container">
                <AlertContainer
                    ref={alert}
                    {...Helper.alertOptions}
                />
                <div className="row header-logo-row col-sm-12">
                    <div className="col-md-9 col-sm-8 header-logo">
                        <div className="logo">
                            <img src={
                                props.event_group_logo ? props.event_group_logo : graphics_data.kct_graphics_logo
                            } className="d-inline-block align-top" alt="" />
                        </div>
                        <div className="header-title">
                            <div className="header-title-flex">
                                <span className="header-title1 d-block">
                                    {
                                        (!_.has(event_data, ['header_line_one']) || _.isEmpty(event_data.header_line_one) ? ""
                                            : event_data.header_line_one)
                                    }

                                </span>
                                <span className="header-title2 d-block">
                                    {
                                        (!_.has(event_data, ['header_line_two']) || _.isEmpty(event_data.header_line_two) ? ""
                                            : event_data.header_line_two)
                                    }
                                </span>
                            </div>
                        </div>
                    </div>
                    <div className="col-md-3 col-sm-4 pr-sm-0 header-name text-right">
                        <div class="d-inline-block">
                            <div className="d-inline-block wonder-event">
                                <SvgIcon graphics_data={graphics_data} />
                            </div>
                            {reactLocalStorage.get("accessToken") && checkOtp() &&
                                <HeaderDropDown event_badge={event_badge}
                                    activeEventId={activeEventId ? activeEventId : ''}
                                    logout={logout} />
                            }
                        </div>
                    </div>
                </div>
            </div>
        </section>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        logout: (data) => dispatch(authActions.Auth.logout(data)),
        getBadge: () => dispatch(eventActions.Event.getBadge()),
        setBadge: (data) => dispatch(newInterfaceActions.NewInterFace.setBadgeData(data)),
        leaveConversation: (id) => dispatch(eventActions.Event.leaveConversation(id)),
        conversationLeave: (data) => dispatch({type: KCT.NEW_INTERFACE.UPDATE_EVENT_CONVERSATIONS, payload: data}),
        deleteConversation: (id) => dispatch({type: KCT.NEW_INTERFACE.DELETE_CONVERSATIONS, payload: id}),
    }
}

const mapStateToProps = (state) => {
    return {
        event_badge: state.NewInterface.interfaceBadgeData,
        graphics_data: state.NewInterface.interfaceGraphics,
        spaces_data: state.NewInterface.interfaceSpacesData,
        event_group_logo: state.page_Customization.event_group_logo,
    };
};

Header = connect(mapStateToProps, mapDispatchToProps)(Header);

export default Header
