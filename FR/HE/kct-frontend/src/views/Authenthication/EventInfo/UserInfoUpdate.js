import React, {useEffect, useRef, useState} from "react";
import moment from "moment";
import {useTranslation} from "react-i18next";
import i18n from "i18next";
import {NavLink, useNavigate, useLocation, useParams} from "react-router-dom";
import {connect} from "react-redux";
import _ from "lodash";
import {Provider as AlertContainer, useAlert } from 'react-alert';
import Moment from "moment/moment";
import Inviter from "./Inviter";
import VideoPlayer from "../../NewInterFace/VideoPlayer/VideoPlayer";
import ProgressButton from "react-progress-button";
import authActions from "../../../redux/actions/authActions";
import eventActions from "../../../redux/actions/authActions";
import newInterfaceActions from "../../../redux/actions/newInterfaceAction";
import Helper from "../../../Helper";
import Spaces from "./Spaces";
import EntitySelectInput from "../../NewInterFace/MyBadge/BadgePopup/EntitySelectInput";
import Calender from "./Calender";
import UserTag from "./UserTag";
import CountDownTimer from "../../NewInterFace/CountDownTimer/CountDownTImer";
import ShowAgenda from "../../MyEventList/ShowAgenda/ShowAgenda";
import "./UserInfo.css";
import KeepContactagent from "../../../agents/KeepContactagent";
import {KeepContact as KCT} from '../../../redux/types';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to manage some user details related to a specific event.This page will
 * render when user is not registered for the event and click on 'register' button from event list page.From this page
 * user can :
 * 1. Select event tags(added during event creation process) which can show his point of interest in event.
 * 2. Direct navigate to a specific selected space(It can be either normal space or VIP space)
 * 3. User can add company name and position or union name to show others in his profile section.
 * 4. Add a reminder of this event.
 * 5. Invite other users to join event by entering their details(name and email).
 * 6. Watch product intro video.
 * Intro video and invite user sections will not be rendered if not allowed from OIT>Design setting>General
 * setting.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.getData To get the data of single space
 * @param {Function} props.addJoin To join the selected space if space has limit
 * @param {String} props.videoLink Video link of the grid video if not networking is there
 * @param {GraphicsData} props.graphic_data [State] This variable holds the current graphics data set in redux
 * @param {Function} props.getUserInfoData To fetch the user badge data
 * @param {Function} props.setUserInfoData To update the user badge data in redux store
 * @param {String} props.invite_attendee Id of invite attendee
 * @param {String} props.video_explainer Image url of video explainer if any
 * @param {String} props.video_explainer_alternative_image Alternate Image url of video explainer if any
 * @param {Function} props.setEventGroupLogo To update the logo of the header with respect to event group settings
 *
 * @returns {JSX.Element}
 * @constructor
 */
function UserInfoUpdate({
    getData,
    addJoin,
    videoLink,
    graphic_data,
    getUserInfoData,
    setUserInfoData,
    invite_attendee,
    video_explainer,
    video_explainer_alternative_image,
    setEventDesignSetting,
    main_color,
    ...props
}) {
    const {t} = useTranslation("qss");

    // Initialisation fo message / alert ref to show alerts on success or error.
    let msg = useAlert();
    // current space state
    const [currentSpace, setCurrentSpace] = useState(null);
    // organiser tags data state
    const [tagsData, setTagData] = useState(null);
    // user badge data state specific to user info page
    const [userBadge, setUserBadge] = useState(null);
    // loading state
    const [loading, setLoading] = useState(true);
    // event data state
    const [eventData, setEventData] = useState(null);
    // invited users state
    const [invites, setInvites] = useState([]);
    // All spaces data for slider state
    const [sliderData, setSliderData] = useState([]);
    // Check group has own customization is applying or not
    const [groupOwnDesign, setgroupOwnDesign] = useState(0)
    // button control state
    const [button, setButton] = useState({
        buttonState: "",
        buttonControl: true,
    });
    const {search} = useLocation();
    const query = React.useMemo(() => new URLSearchParams(search), [search]);
    var COUNT = 1;
    // timer label state changes depending on time of event
    const [showData, setData] = useState(t("Starts in"));
    // change welcome txt when event time completed
    const [wlcmTxt, setwelcmTxt] = useState("future");

    // history hook to navigate between pages.
    let history = useNavigate()
    const {event_uuid} = useParams();


    useEffect(() => {
        if (!localStorage.getItem("accessToken")) {
            history.push(`/`);
        }

        fetchData();

        getEventGraphicData(event_uuid ? event_uuid : '')
        return () => {
            Helper.implementGraphicsHelper(main_color)
            props.setEventGroupLogo(null);
        }
    }, [i18n.language])


    const getEventGraphicData = (data) => {

        try {
            return KeepContactagent.Event.getEventGraphicData(data).then((res) => {
                setEventDesignSetting(res.data.data);
                props.setEventGroupLogo(Helper.prepareEventGroupLogo(res.data.data));
                let sceneryData = res.data.data.current_scenery_data;
                props.implementGraphics(
                    res.data.data.graphic_data,
                    _.has(sceneryData, ['category_type'])
                    && (sceneryData.category_type === 1 || sceneryData.category_type === 2)
                );
                Helper.implementGraphicsHelper(res.data.data.graphic_data)
                if(res.data.data.graphic_data){
                    setgroupOwnDesign(res.data.data.graphic_data.customized_colors)
                }

            }).catch((err) => {
                msg &&
                    msg &&
                    msg.show(Helper.handleError(err), {
                        type: "error",
                    });
            })


        } catch (err) {
            msg &&
                msg &&
                msg.show(Helper.handleError(err), {
                    type: "error",
                });
        }
    }


    // useParams hook for getting query params of router
    let parameter = useParams();
    let eventUuid = parameter && parameter.event_uuid ? parameter.event_uuid : "";
    var COUNT = 1;

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to fetch all data(tags, event time, invites, spaces) related to
     * this quick registration page and update all related states once the call excuted successfully.
     * -------------------------------------------------------------------------------------------------------------------
     *
     */
    const fetchData = () => {
        setLoading(true);
        getData(eventUuid)
            .then((res) => {
                if (res.data.status) {
                    let responseData = res.data.data;
                    setTagData(responseData.user_badge.tags_data);
                    setUserBadge(responseData.user_badge);
                    setCurrentSpace(responseData.event_resource.current_space.space_uuid);
                    setEventData({
                        ...responseData.event_resource,
                        is_space_host: responseData.is_space_host,
                    });
                    showTextAsTime(responseData.event_resource);
                    setInvites(responseData.invites);
                    setSliderData(responseData.event_resource.spaces);
                } else {
                    msg &&
                        msg &&
                        msg.show(res.data.msg, {
                            type: "error",
                        });
                }
                setLoading(false);
            })
            .catch((err) => {
                console.error(err);
                setLoading(false);
                msg &&
                    msg &&
                    msg.show(Helper.handleError(err), {
                        type: "error",
                    });
            });
    };


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user input some value in company name and position input fields and
     * this function will save data(input value) in a state(setUserBadge).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @param {UserBadge} data Updated user badge
     */
    const updateCompany = (data) => {
        setUserBadge(data);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to access dashboard page.
     * access dashboard action can be restrict if event registration is opened from OIT>invitation plan page.
     * -------------------------------------------------------------------------------------------------------------------
     */
    const joinEvent = () => {
        setButton({buttonState: "loading", buttonControl: false});
        let accessCode = localStorage.getItem("accessCode");
        addJoin({event_uuid: eventUuid, space_uuid: currentSpace})
            .then((res) => {
                if (res.data.status) {
                    let responseData = res.data.data;
                    history.push(`/dashboard/${eventUuid}`);
                } else {
                    msg &&
                        msg &&
                        msg.show(res.data.msg, {
                            type: "error",
                        });
                }

                setButton({buttonState: "", buttonControl: true});
            })
            .catch((err) => {
                let errorMessage = Helper.handleError(err);
                // showing proper error message for event uuid validation
                if (err.response != undefined && err.response.status == 422) {
                    if (_.has(err.response.data, ['errors', 'event_uuid'])) {
                        errorMessage = err.response.data.errors.event_uuid.join(', ');
                    }
                }
                setButton({buttonState: "", buttonControl: true});

                msg &&
                    msg &&
                    msg.show(errorMessage, {
                        type: "error",
                    });
            });
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function handles state updating of timer text according to time comparison(with event current
     * time and event start time) results.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @param {EventData} event_data Event data object.
     */
    const showTextAsTime = (event_data) => {
        const timeZone = "Europe/Paris";
        const {start_time, end_time, date, event_end_date} = event_data;
        const startTimes = moment(`${date} ${start_time}`).toDate();
        const endTimes = moment(`${event_end_date} ${end_time}`).toDate();
        const endTimeDiff = Helper.getMaxSetTimeoutValue(Helper.getTimeDifference(timeZone, endTimes));
        const startTimeDiff = Helper.getMaxSetTimeoutValue(Helper.getTimeDifference(timeZone, startTimes));

        if (startTimeDiff < 0 && endTimeDiff > 0) {
            setData(t("has already started"));
            setTimeout(() => {
                showTextAsTime(event_data);
            }, endTimeDiff);
            setwelcmTxt("present");
        } else if (startTimeDiff > 0) {
            setData(t("Starts in"));
            setTimeout(() => {
                showTextAsTime(event_data);
            }, startTimeDiff);
            setwelcmTxt("future");
        } else {
            setData(t("is over"));
            setwelcmTxt("present");
        }
    };


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will we trigger when user click on '+' icon to add some rows to invite users.This will
     * handles state(setInviteData) updating of inviter array with new data object.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} data Data to send to backend for api
     * @param {Object} data.fname First name of user
     * @param {Object} data.lname Last name of user
     * @param {Object} data.email Email of user to invite
     */
    const setInviteData = (data) => {
        setInviteData([...invites, data]);
    };

    // rendering page loading in case of loading state true
    if (loading) {
        return <Helper.pageLoading />;
    }

    // in case of eventData state false or null render static text
    if (!eventData) {
        return <h2>Event data not found</h2>;
    }

    // start time end time in useable format inside calender as per user's time-zone
    let startTime = Moment(
        Helper.getTimeUserTimeZone(
            "Europe/Paris",
            `${eventData.date} ${eventData.start_time}`
        )
    )
        .utc()
        .format("YYYY-MM-DDTHH:mm:ss");
    let endTime = Moment(
        Helper.getTimeUserTimeZone(
            "Europe/Paris",
            `${eventData.event_end_date} ${eventData.end_time}`
        )
    )
        .utc()
        .format("YYYY-MM-DDTHH:mm:ss");

    // calender data object
    let calenderData = {
        title: eventData.event_title,
        description: "",
        location: "",
        startTime: startTime,
        endTime: endTime,
    };

    // union data object
    let unionData =
        userBadge && userBadge.unions ? _.last(userBadge.unions) : null;
    const agenda = parameter;

    return (
        <div className="join-border">
            <div className="row white-bg no-texture">
                <AlertContainer ref={msg} {...Helper.alertOptions} />
                {/* ------------- right side timer ------------------- */}
                <div className="col-lg-12 col-sm-12 text-right">
                    <div className="row">
                        {(_.has(graphic_data, ["customized_colors"]) &&
                            graphic_data.customized_colors || groupOwnDesign == 1)
                            ? (
                                <div className="col-lg-12 col-sm-12 text-right">
                                    <div className="row">
                                        <div className="blackbox no-border">
                                            <div className="d-inline-block text-left pull-left black-keep">
                                                <h4>{eventData.event_title || ""}</h4>
                                                <p>{showData}</p>
                                            </div>
                                            <div className="d-inline-block black-timer">
                                                {eventData && (
                                                    <CountDownTimer
                                                        fromQuickSignUp={true}
                                                        customisation={true}
                                                        page_Customization={{
                                                            event_date: Moment(
                                                                eventData.date,
                                                                "YYYY/MM/DD"
                                                            ).format("YYYY-MM-DD"),
                                                            event_start_time: eventData.start_time,
                                                            time_zone: "Europe/Paris",
                                                        }}
                                                    />
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div className="col-md-offset-6 col-md-6">
                                    <div className="d-inline-block text-left black-keep d-flex text-w-border-shade">
                                        <div className="innertext">
                                            <h3 className="heading-color">
                                                {eventData.event_title || ""}
                                            </h3>
                                            <p className="heading-color">
                                                {eventData.description || ""}
                                            </p>
                                            <h3 className="heading-color">{showData}</h3>
                                        </div>
                                    </div>
                                    <div className="d-inline-block text-left black-timer no-timer-content">
                                        {eventData && (
                                            <CountDownTimer
                                                fromQuickSignUp={true}
                                                page_Customization={{
                                                    event_date: Moment(eventData.date, "YYYY/MM/DD").format(
                                                        "YYYY-MM-DD"
                                                    ),
                                                    event_start_time: eventData.start_time,
                                                    time_zone: "Europe/Paris",
                                                }}
                                            />
                                        )}
                                    </div>
                                </div>
                            )}
                    </div>
                </div>

                <div className="col-md-12 col-sm-12">
                    <div className="regi-tags regi-heading">
                        <h3 className="heading-color">
                            <span class="number-outer heading-color">
                                <span class="number-inner"> {COUNT}.</span>
                            </span>
                            {t("Optimize your networking")}
                        </h3>
                        <h4 className="heading-color">
                            {t("Choose your point of interests")}
                        </h4>
                        <div className="black-tags">
                            <UserTag event_badge={userBadge} tagsData={tagsData} event_uuid={eventUuid} />
                        </div>
                    </div>
                </div>

                {_.has(eventData, ["is_mono_present"]) &&
                    eventData.is_mono_present == 1 ? null : (
                    <div className="col-md-12 col-sm-12 mt-30">
                        <div className="dark-spaces regi-heading">
                            <h3 className="heading-color">
                                <span className="number-outer heading-color">
                                    <span class="number-inner">{(COUNT += 1)}.</span> {/*2 */}
                                </span>
                                {t("In which space do you want to start networking")}
                            </h3>
                            <Spaces
                                t={t}
                                welcome_txt={wlcmTxt}
                                eventData={eventData}
                                setCurrentSpace={setCurrentSpace}
                                currentSpace={currentSpace}
                                eventUuid={eventUuid}
                                sliderData={sliderData}
                                alert={msg}
                            />
                        </div>
                    </div>
                )}

                <div className="col-lg-12 col-sm-12 mb-20">
                    <div className="regi-heading dark-form">
                        <h3 className="heading-color">
                            <span class="number-outer heading-color">
                                <span class="number-inner">{(COUNT += 1)}.</span>
                                {/*2 */}
                            </span>
                            {t("want to network with you")}
                        </h3>
                        <h4 className="heading-color">
                            {t("If you want to display your company")}
                        </h4>
                        <EntitySelectInput
                            t={t}
                            hideEye={true}
                            userView={true}
                            updateCompany={updateCompany}
                            eyeState={false}
                            visibility={null}
                            position={
                                userBadge && userBadge.company ? userBadge.company.position : ""
                            }
                            entityType="company"
                            visibilityType="company"
                            entityTypeId={2}
                            alert={msg && msg}
                            selectedEntity={
                                userBadge && userBadge.company
                                    ? {
                                        value: userBadge.company.entity_id,
                                        label: userBadge.company.long_name,
                                    }
                                    : null
                            }
                        />

                        {/* company */}
                        <h4 className="heading-color">
                            {t("If you are part of an association or a union")}
                        </h4>
                        <EntitySelectInput
                            t={t}
                            hideEye={true}
                            userView={true}
                            visibility={null}
                            visibilityType="unions"
                            entityType="union"
                            updateCompany={updateCompany}
                            entityTypeId={3}
                            alert={msg && msg}
                            selectedEntity={
                                unionData
                                    ? {value: unionData.entity_id, label: unionData.long_name}
                                    : null
                            }
                            position={unionData ? unionData.position : ""}
                        />
                    </div>
                </div>
                <div className="col-lg-12 col-sm-12 mb-20">
                    <div className="regi-heading cal-add">
                        <h3 className="mb-15 heading-color">
                            <span class="number-outer">
                                <span class="number-inner">{(COUNT += 1)}.</span> {/*3 */}
                            </span>
                            {t("Add to calendar")}
                        </h3>
                        <Calender calenderData={calenderData} />
                    </div>
                </div>
                {invite_attendee === 1 && (
                    <div className="col-lg-12 col-sm-12 mb-20">
                        <div className="regi-heading">
                            <h3 className="mb-15 heading-color">
                                <span class="number-outer">
                                    <span class="number-inner">{(COUNT += 1)}.</span>
                                </span>
                                {t("Invite friends or colleagues")}
                            </h3>
                            <Inviter
                                setInvites={setInvites}
                                event_uuid={eventUuid}
                                invites={invites}
                                t={t}
                            />
                        </div>
                    </div>
                )}
                {video_explainer === 1 && (
                    <div className="col-md-12 col-sm-12 mb-20">
                        <div className="regi-heading dark-video">
                            <h3 className="mb-15 heading-color">
                                <span class="number-outer">
                                    <span class="number-inner">
                                        {(COUNT += 1)}
                                    </span>{" "}
                                </span>
                                {t("Have a look at this short video")}
                            </h3>

                            <div className="dark-video-inner">
                                <VideoPlayer isFromUpdate={true} url={videoLink || ""} />
                            </div>
                        </div>
                    </div>
                )}
            </div>

            <div></div>
            <div className="text-center pro-btn">
                <ProgressButton
                    onClick={joinEvent}
                    className="dark-join-btn no-border"
                    controlled={button.buttonControl}
                    state={button.buttonState}
                >
                    {t("Join The Event")}
                </ProgressButton>
                {parameter.event_type == 2 && parameter.share_agenda == 1 && (
                    <NavLink
                        className="dark-join-btn no-border"
                        to={{
                            pathname: "/event-viewagenda",
                            state: agenda,
                        }}
                    >
                        <ShowAgenda agenda={agenda}></ShowAgenda>
                    </NavLink>
                )}
            </div>
        </div>
    );
}

const mapDispatchToProps = (dispatch) => {
    return {
        getData: (uuid) => dispatch(authActions.Auth.getUserInfo(uuid)),
        addInvite: (data) => dispatch(authActions.Auth.addInvite(data)),
        addJoin: (data) => dispatch(authActions.Auth.addJoin(data)),
        getUserInfoData: () => dispatch(eventActions.Event.getUserInfoData()),//user info
        setUserInfoData: (data) => dispatch(newInterfaceActions.NewInterFace.setUserInfoData(data)),//user info
        setEventDesignSetting: (data) => dispatch({type: KCT.EVENT.SET_EVENT_DESIGN_SETTINGS, payload: data}),
        setEventGroupLogo: (data) => dispatch({type: KCT.EVENT.SET_EVENT_GROUP_LOGO, payload: data}),
        setEventGraphics: (data) => dispatch({type: KCT.EVENT.SET_EVENT_GRAPHICS, payload: data}),
    }
}

const mapStateToProps = (state) => {
    return {
        event_badge: state.NewInterface.interfaceBadgeData,
        graphic_data: state.NewInterface.interfaceGraphics,
        invite_attendee:
            state.page_Customization.initData.graphics_data.invite_attendee,
        video_explainer:
            state.page_Customization.initData.graphics_data.video_explainer,
        main_color: state.page_Customization.initData.graphics_data
    };
};


UserInfoUpdate = connect(mapStateToProps, mapDispatchToProps)(UserInfoUpdate);
export default UserInfoUpdate;
