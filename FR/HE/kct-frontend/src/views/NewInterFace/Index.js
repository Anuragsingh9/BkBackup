import React from 'react';
import NewBlock from './NewBlock/NewBlock.js';
import {connect} from 'react-redux';
import Header from './Header/Header.js';
import SliderSPersons from './EspaceSlider/EspaceSlider.js'
import Helper from '../../Helper.js';
import Footer from './Footer/Footer';
import eventActions from '../../redux/actions/eventActions';
import {reactLocalStorage} from 'reactjs-localstorage';
import newInterfaceActions from '../../redux/actions/newInterfaceAction';
import CSSGenerator from '../DynamicCss.js';
import './Index.css';
import './MainSpaceResponsive.css';
import _ from 'lodash';
import {Provider as AlertContainer } from 'react-alert';
import socketManager from "../../socket/socketManager";
import {KeepContact as KCT} from '../../redux/types';
import "./EspaceSlider/slick.css";
import "./EspaceSlider/slick-theme.css";
import videoElementRepo from '../VideoMeeting/VideoElementRepository.js';
import videoMeeting from '../VideoMeeting/VideoMeetingClass.js';
import moment from 'moment';
import ConversationPopup from './ConversationPopup/ConversationPopup.js';
import SpaceHostVideoBlock from './HostSection/SpaceHost/SpaceHostVideoBlock.js';
import IsolationPopup from '../../views/IsolationPopup/IsolationPopup';
import MainHostVideo from './HostSection/MainHost/MainHostWebinar.js';
import {withTranslation} from 'react-i18next'
import i18next from 'i18next';
import {ZoomMtg} from "@zoomus/websdk";
import PilotPanel from "./PilotPanel/index.js";
import Constants from "../../Constants";
import MediaDevicePopup from "../Index/MediaDevicePopup/MediaDevicePopup";
import {runDeviceTest} from "./Conversation/Utils/Conversation";
import GridComponent from "./GridComponent/GridComponent";
import graphicActions from "../../redux/actions/graphicActions";
import MyNetworkDropDown from "./MyNetwork/MyNetworkDropDown";

let spacesTimeout = null;

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for rendering dashboard components and provides socket on handlers from props
 * Here all the subcomponents are rendered and the states for all dashboard which are common to sub components is
 * managed here
 * ---------------------------------------------------------------------------------------------------------------------
 */
class Dashboard extends React.Component {

    /**
     * @param props
     * @param {Object} props.alert Reference object for displaying notification popup
     * @param {Function} props.getGraphics To fetch the graphics, event and moments data of event specific from server
     * @param {SceneryData} props.sceneryData Current Scenery data of event
     * @param {Function} props.getBadge To get the user badge data
     * @param {Function} props.getTag To get the tags of user profile
     * @param {Function} props.getEventSpaces To fetch the spaces of event
     * @param {Function} props.setTags To update the tags value in user badge
     * @param {Function} props.setBadge To update the badge details of user
     * @param {Function} props.setEventSpaces To update the spaces data in redux store
     * @param {Function} props.setSpaceHostData To update the space host data in redux store
     * @param {Function} props.setEventGraphics To set the graphics settings to apply the customization
     * @param {Function} props.filterOnline To update the online users data and remove the offline users data from redux
     * @param {Function} props.conversationJoin To start the join process of with a user or conversation
     * @param {Function} props.updateUserPosition To update other user grids position on grid
     * @param {Function} props.conversationLeave To leave the current conversation and release the devices
     * @param {Function} props.leaveConversation To leave the conversation and release the devices
     * @param {Function} props.addNewUser To add a new user in current conversation
     * @param {Function} props.updateProfile To update the user profile with server
     * @param {Function} props.spaceUserCountUpdate To update the online user count in space
     * @param {Function} props.getCurrentConversation To fetch the current conversation details from server
     * @param {Function} props.changeConversationId To change the conversation id when someone join/leave conversation
     * @param {Function} props.selfJoinedNewConversation To update the self current joined conversation data
     * @param {Function} props.deleteConversation To leave and delete the self conversation data
     * @param {Function} props.setIsOnlineDataReceived To update the flag that online users data is fetched now
     * @param {Function} props.askJoinConversation To ask other user to join by emitting event and starting conversation
     * @param {Function} props.handlePrivateConversation To update the conversation private state in redux
     * @param {Function} props.handlePrivateConversation2 To update the conversation private state in redux
     * @param {Function} props.handleConfereceUpdate To update the conference data updated by the zoom web hooks
     * @param {Function} props.triggerPagination To update the pagination data to go to next page on user grid
     * @param {Function} props.updateConversationMute To update the conversation mute state with respect to chime
     * @param {Function} props.updateGridVisibility To update the grid visibility so when pilot hide the grid its hidden
     * @param {Function} props.updateContentData To update the current content data by the pilot from pilot panel
     * @param {Function} props.setContentComponentVisibility To update the current component visibility set by pilot
     * @param {Function} props.setZoomMuteButton To perform the zoom mute state update, updated by the pilot
     * @param {Function} props.callOff To show or hide the calling popup when calling to other user
     * @param {Function} props.setZoomUserAdmitState To handle the zoom loading when zoom hooks tells user joined
     * @param {Function} props.updateProfileData To update the user data on server
     * @param {Function} props.updateProfileTrigger To update the profile data on backend server
     * @param {Function} props.updateInitName To update the user name in init data response
     * @param {GraphicsData} props.graphics_data [State] This variable holds the current graphics data set in redux
     * @param {EventData} props.event_data Current event data
     * @param {InterfaceSpaceData} props.spaces_data Spaces data including conversations from redux store
     * @param {UserBadge} props.event_badge User badge details
     * @param {UserBadge} props.auth User badge details
     * @param {InterfaceSliderData} props.sliderData All spaces with pages required and sorted by type of spaces
     * @param {Boolean} props.space_load To indicate if the spaces data has been loaded or not
     * @param {Boolean} props.conversationModal To indicate if user is in conversation or not and data is loaded or not
     * @param {Boolean} props.isolationConversationModal To indicate if user conversation is in private mode or not
     * @param {UserBadge} props.spaceHost Current space host data
     * @param {GridPagination} props.gridPagination Grid paginated data for displaying conversation sorted
     * @param {ConversationMeta} props.conversationMeta Current conversation state from redux
     * @param {GridMeta} props.gridMeta Current grid visibility variable from redux store
     * @param {ContentManagementMeta} props.contentManagementMeta Content Related Props from redux store for current content
     */
    constructor(props) {
        super(props);
        this.state = {
            modal: true,
            loading: true,
            event_during: false,
            is_event_end: false,
            before_on: false,
            after_on: false,
            is_networking: false,
            current_moment_type: 1,
            recorded_url: '',
            isZoomInitialized: false,
            notifierBarMessageType: null,
            wlcmTxt: "future",
            currentLiveConference: null,
            pilotPanelVisible: false,
            // showDeviceSelector: false,
            // availableDevices: {},
            // showCaptureBtn: false,
            // noPreviewDiv: false,
            showMediaDevicePopup: false,
            userGridSceneryStyle: {},
            bgColorWrapSceneryStyle: {},
            myBadgeBlockSceneryStyle_childDiv: {},
            myBadgeBlockSceneryStyle: {},
            designSettingState: {},
            gridSectionSceneryColor: {},
            spaceSectionSceneryColor: {},
            extendedSpaceComponent: {},
            isGraphicApplied: false,
            isConferenceOn: false,
        };
        this.conversationRef = React.createRef()
    }  // change welcome txt when event time completed


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles scrolling to the conversation div.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    executeScroll = () => {
        this.conversationRef.current.scrollIntoView()
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles resetting state of modal.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    resetState = () => {
        this.setState({modal: !this.state.modal});
    }

    resetSceneryData = () => {
        this.setState({
            userGridSceneryStyle: {},
            myBadgeBlockSceneryStyle_childDiv: {},
            bgColorWrapSceneryStyle: {},
            myBadgeBlockSceneryStyle: {},
        })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles the scenery graphic data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    applyScenerygraphicData = (customSceneryData = null) => {
        let sceneryData = customSceneryData || this.props.sceneryData;

        if ((this.props.eventGraphics === null || this.state.isGraphicApplied) && !customSceneryData) {
            return
        }
        this.setState({
            isGraphicApplied: true,
        })

        if (!sceneryData.asset_id && !sceneryData.category_type) {
            this.resetSceneryData();
            return;
        }
        const isSceneryData = _.has(this.props, ["sceneryData"]) &&
            !_.isEmpty(sceneryData) &&
            sceneryData.category_type !== 0;

        const {
            customized_colors,
            space_background,
            user_grid_background,
            sh_customized,
            user_grid_customization,
            event_color_1,
            event_color_2,
            space_customization,
            extends_color_user_guide
        } = this.props.eventGraphics.graphic_data;
        // const graphicDatas :

        if (isSceneryData) {
            const sceneryBgColor = sceneryData.top_background_color;
            const SCENERY_DATA = sceneryData;
            this.props.updateEventScenery(sceneryData);

            if (customized_colors == 1) {
                // if (sh_customized == 1) {
                if (space_customization === 1) {
                    if (extends_color_user_guide === 1) {
                        this.setState({
                            extendedSpaceComponent: {
                                background: `rgba(${space_background.r}, ${space_background.g},${space_background.b},${SCENERY_DATA.component_opacity})`,
                            }
                        })
                        this.setState({
                            spaceSectionSceneryColor: {
                                "background-image": `unset`,
                            }
                        })
                    } else {
                        this.setState({
                            spaceSectionSceneryColor: {
                                "background": `rgba(${space_background.r}, ${space_background.g},${space_background.b},${SCENERY_DATA.component_opacity})`,
                                "background-image": `unset`,
                            }
                        })
                    }
                }

                if (user_grid_customization == 1) {
                    this.setState({
                        gridSectionSceneryColor: {
                            "background": `rgba(${user_grid_background.r}, ${user_grid_background.g},${user_grid_background.b},${SCENERY_DATA.component_opacity})`,
                        }
                    })
                } else {
                    this.setState({
                        gridSectionSceneryColor: {
                            "background": `rgba(${event_color_2.r}, ${event_color_2.g},${event_color_2.b},${SCENERY_DATA.component_opacity})`,
                        }
                    })
                }
            }


            if (SCENERY_DATA.category_type == 1) {
                this.setState({
                    userGridSceneryStyle:
                        {
                            "width": "100%",
                            "background-image": `url(${SCENERY_DATA.asset_path})`,
                            "background-size": "cover",
                            "margin-bottom": "0px",
                            "margin-top": "0px",
                            "padding-bottom": "130px",
                            "padding-top": "65px",
                            "position": "relative"
                        }
                })
            }
            if (SCENERY_DATA.category_type == 2) {
                this.setState({
                    userGridSceneryStyle:
                        {
                            "width": "100%",
                            "background-color": `rgba(${sceneryBgColor.r}, ${sceneryBgColor.g},${sceneryBgColor.b},${sceneryBgColor.a})`,
                            "background-size": "cover",
                            "margin-bottom": "0px",
                            "margin-top": "0px",
                            "padding-bottom": "130px",
                            "padding-top": "65px",
                            "position": "relative"
                        }
                })
            }
            this.setState({
                myBadgeBlockSceneryStyle_childDiv: {
                    "margin-bottom": "0px",
                    "margin-top": "0px",
                }
            })
            this.setState({
                bgColorWrapSceneryStyle: {
                    "background-color": `rgba(${sceneryBgColor.r}, ${sceneryBgColor.g},${sceneryBgColor.b},${sceneryBgColor.a})`,
                }
            })
            this.setState({
                myBadgeBlockSceneryStyle: {
                    "background-color": `rgba(${sceneryBgColor.r}, ${sceneryBgColor.g},${sceneryBgColor.b},${sceneryBgColor.a})`,
                    "padding-top": "40px"
                }
            })
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function is a lifecycle method which triggers initial api calls<br>
     * Device permission <br>
     * Graphic data <br>
     * Badge data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    componentDidMount() {
        CSSGenerator.setCssDefault('');
        this.setGraphicData();
        this.getMediaPermissions();
        this.getBadgeData();
        this.handleMoments(this.props.event_data);
        if (_.has(this.props.event_data, ['pilot_panel'])
            && _.has(this.props.event_data, ['is_auto_created']) && this.props.event_data.is_auto_created) {
            this.setState({
                pilotPanelVisible: true,
            })
        }


    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the states when the component is update
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Readonly<P>} prevProps Previous props before the component was updated
     * @param {Readonly<S>} prevState Previous state of component before it was updated
     * @param {React.Component} snapshot Component to be render now
     */
    componentDidUpdate(prevProps, prevState, snapshot) {
        if (_.has(this.props.eventGraphics, ['graphic_data'])) {
            this.applyScenerygraphicData();
        }
        if (this.props.event_data.event_end_time) {
            const timeZone = "Europe/Paris";
            const endTimes = moment(`${this.props.event_data.event_end_date} ${this.props.event_data.event_end_time}`).toDate();
            const endTimeDiff = Helper.getTimeDifference(timeZone, endTimes);
            if (endTimeDiff <= 0 && this.state.pilotPanelVisible) {
                this.setState({
                    pilotPanelVisible: false,
                })
                this.props.updateContentData({
                    contentMediaType: null,
                    currentMediaData: {}
                })
            }
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function create a color object and triggers customisation handler inside dynamic css.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    setGraphicData = () => {
        const {graphics_data} = this.props;
        if (_.has(graphics_data, ['event_color_1']) && _.has(graphics_data, ['event_color_2']) && _.has(graphics_data, ['event_color_3'])) {

            const {
                event_color_1, event_color_2, event_color_3,
                background_color, separation_line_color, text_color, unselected_spaces_square,
                selected_spaces_square, bottom_bg_color,
                has_custom_background, bottom_bg_is_colored, tag_color, badge_bg_color, join_bg_color, join_text_color,
                customized_texture, texture_square_corner, texture_remove_frame, texture_remove_shadow,
                customized_colors, professional_tag_color, personal_tag_color, space_background, user_grid_background
            } = graphics_data;

            const color1 = event_color_1;
            const color2 = event_color_2;
            const color3 = event_color_3

            const colorObj = {
                color1: Helper.rgbaObjectToStr(color1),
                color2: Helper.rgbaObjectToStr(color2),
                color3: Helper.rgbaObjectToStr(color3),
                hasHeaderBackground: has_custom_background,
                headerBackground: Helper.rgbaObjectToStr(background_color),
                headerTextColor: Helper.rgbaObjectToStr(text_color),
                separationLineColor: Helper.rgbaObjectToStr(separation_line_color),
                bottomBackgroundColor: Helper.rgbaObjectToStr(bottom_bg_color),
                hasBottomBackgroundColor: bottom_bg_is_colored,
                customizeTexture: customized_texture,
                textureRound: texture_square_corner,
                tagColor: Helper.rgbaObjectToStr(tag_color),
                textureWithFrame: texture_remove_frame,
                selectedSpacesSquare: selected_spaces_square,
                unselectedSpacesSquare: unselected_spaces_square,
                textureWithShadow: texture_remove_shadow,
                customizeColor: customized_colors,
                transparent: (color1) ? 'rgb(' + color1.r + ' ' + color1.g + ' ' + color1.b + ' / ' + '60%)' : '',
                badgeBgColor: Helper.rgbaObjectToStr(badge_bg_color),
                joinButtonBgColor: Helper.rgbaObjectToStr(join_bg_color),
                joinButtonTextBgColor: Helper.rgbaObjectToStr(join_text_color),
                professional_tag_color: Helper.rgbaObjectToStr(professional_tag_color),
                personal_tag_color: Helper.rgbaObjectToStr(personal_tag_color),
            }
            // CSSGenerator.generateNewInterfaceCSS(colorObj);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the device selector visibility
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Boolean} value Visibility value of the device selector
     */
    setShowDeviceSelector(value = null) {
        this.setState({
            showDeviceSelector: !!value
        })
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function collects media permissions. <br>
     * If not given asks for media device permission
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    getMediaPermissions = () => {
        const mediaDeviceAccepted = (stream) => {
            if (!localStorage.getItem('user_audio') || !localStorage.getItem('user_video')) {
                this.setState({showMediaDevicePopup: true});
            }
            stream.getVideoTracks().forEach(function (track) {
                track.stop();
            });
        }
        const mediaDeviceRejected = e => {

        }
        navigator.getUserMedia = (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.mediaDevices.getUserMedia);

        navigator.mediaDevices.getUserMedia({audio: true, video: true}).then(mediaDeviceAccepted, mediaDeviceRejected);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function triggers api call and sets user badge data inside redux
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    getBadgeData = () => {
        let event_uuid = this.props.event_data?.event_uuid;
        try {
            this.props.getBadge(event_uuid).then((res) => {
                const data = res.data.data;
                this.props.setBadge(data);
                this.getSpacesData();
            }).catch((err) => {
                this.alert && this.alert.show(Helper.handleError(err), {type: 'error'})
            })
        } catch (err) {
            this.alert && this.alert.show(Helper.handleError(err), {type: 'error'})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function triggers api call and sets user tags data inside redux user badge.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    getTagData = () => {
        try {
            this.props.getTags().then((res) => {
                let {event_badge} = this.props
                const data = res.data.data;
                this.props.setBadge({...event_badge, 'tag_data': data});

            }).catch((err) => {
                this.alert && this.alert.show(Helper.handleError(err), {type: 'error'})
            })
        } catch (err) {
            this.alert && this.alert.show(Helper.handleError(err), {type: 'error'})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function compares the current time to event time and sets states accordingly<br>
     * If Event is in during before or after stage.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    checkEventTime = (props = {}) => {
        clearTimeout(spacesTimeout);
        const {event_data} = this.props;
        const timeZone = 'Europe/Paris';
        const {event_start_time, event_end_time, event_date, manual_opening, event_uuid, event_end_date} = event_data;
        const endTime = moment(`${event_end_date} ${event_end_time}`).toDate();
        const startTime = moment(`${event_date} ${event_start_time}`).toDate();
        const endTimeDiff = Helper.getTimeDifference(timeZone, endTime);
        let eventIsLive = false;
        if (endTimeDiff > 0 && manual_opening) {
            this.setState({event_during: true, is_event_end: false});
            eventIsLive = true;
            spacesTimeout = setTimeout(() => {
                this.checkEventTime()
            }, endTimeDiff);

            this.startSocket(props);
        } else {
            const startTimeDiff = Helper.getTimeDifference(timeZone, startTime);

            if (startTimeDiff < 0 && endTimeDiff > 0) {
                // event started but not ended
                this.setState({event_during: true, is_event_end: false});
                eventIsLive = true;
                this.setState({wlcmTxt: "present"});
                this.startSocket(props);
                spacesTimeout = setTimeout(() => {
                    this.checkEventTime()
                }, Helper.getMaxSetTimeoutValue(endTimeDiff));
            } else if (startTimeDiff > 0) {
                // event started
                spacesTimeout = setTimeout(() => {
                    this.checkEventTime()
                }, startTimeDiff);

                this.getSetGraphics(startTimeDiff);

                this.setState({event_during: false});
                this.setState({wlcmTxt: "future"});

                this.stopSocket();
            } else {
                this.setState({event_during: false, is_event_end: true});
                this.setState({wlcmTxt: "present"});
                this.leaveConversation();
                this.stopSocket();
            }
        }

        this.props.setEventGraphics({event_is_live: eventIsLive});
        let actualStartTime = moment(`${event_data.event_actual_date} ${event_data.event_actual_start_time}`).toDate();
        let actualEndTime = moment(`${event_data.event_actual_date} ${event_data.event_actual_end_time}`).toDate();
        if (this.props.eventMeta.is_rehearsal_mode) {
            let actualStartDiff = Helper.getTimeDifference(timeZone, actualStartTime);
            let actualEndDiff = Helper.getTimeDifference(timeZone, actualEndTime);
            if (actualEndDiff > 0) {
                // event actually is live
                this.getSetGraphics(actualEndDiff);
            }
            if (actualStartDiff > 0) {
                // event is not live really
                this.getSetGraphics(actualStartDiff, () => {
                    this.alert && this.alert.show(i18next.t("notification:rehearsal_mode_end"), {type: 'info'})
                    socketManager.emitEvent.USER_CON_IN_LIVE_MODE();
                });
            }
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function compares the current time to event time and sets states accordingly in case of
     * opening hours enabled If Event is in during before or after stage.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    openingHoursSet = () => {
        const {opening_before, opening_after} = this.props.event_data;
        clearTimeout(spacesTimeout);
        const {event_data} = this.props;
        const timeZone = 'Europe/Paris';
        const {event_start_time, event_end_time, event_date, manual_opening, event_uuid, event_end_date} = event_data;
        const endTime = moment(`${event_end_date} ${event_end_time}`).toDate();
        const startTime = moment(`${event_date} ${event_start_time}`).toDate();
        const endTimeDiff = Helper.getTimeDifference(timeZone, endTime) - (opening_after * 60 * 1000);

        const normalStartTime = Helper.getTimeDifference(timeZone, startTime);
        const normalEndTime = Helper.getTimeDifference(timeZone, endTime);

        if (endTimeDiff > 0 && 0) {
            this.setState({event_during: true, is_event_end: false, before_on: false, after_on: false});
            spacesTimeout = setTimeout(() => {
                this.openingHoursSet()
            }, endTimeDiff);
            this.startSocket();
        } else {
            const startTimeDiff = Helper.getTimeDifference(timeZone, startTime) + (opening_before * 60 * 1000);

            if (startTimeDiff < 0 && endTimeDiff > 0) {
                this.setState({event_during: true, is_event_end: false, before_on: false, after_on: false});
                this.startSocket();
                spacesTimeout = setTimeout(() => {
                    this.openingHoursSet()
                }, endTimeDiff);

                this.getSetGraphics(endTimeDiff);

            } else if (startTimeDiff > 0) {
                const timer = startTimeDiff > normalStartTime && normalStartTime > 0 ? normalStartTime : startTimeDiff;

                spacesTimeout = setTimeout(() => {
                    this.openingHoursSet()
                }, timer);

                this.getSetGraphics(startTimeDiff);

                if (startTimeDiff > 0 && normalStartTime < 0) {
                    this.setState({event_during: false, before_on: true, after_on: false});
                    this.startSocket();

                } else if (normalStartTime < 0) {
                    this.setState({event_during: true, before_on: false, after_on: false});
                    this.startSocket();

                } else {
                    this.stopSocket();
                }

            } else if (normalEndTime > 0 && endTimeDiff < 0) {
                this.getSetGraphics(startTimeDiff);
                this.setState({event_during: false, is_event_end: false, before_on: false, after_on: true});
                this.startSocket();
                spacesTimeout = setTimeout(() => {
                    this.openingHoursSet()
                }, normalEndTime);
            } else {
                this.setState({event_during: false, is_event_end: true, before_on: false, after_on: false});
                this.leaveConversation();
                this.stopSocket();
            }
        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find the current live moment and set the data according to moment type
     * there can be two moments running at same time, one from networking and one from content type
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {EventData} data event data to find the moments with respect to current and event start end time
     **/
    handleMoments = (data) => {
        const {moments, event_date, event_start_time} = data;
        const time_zone = 'Europe/Paris';
        let nextTimer = 0;

        let networking = false;

        let url = '';

        let typeFlag = false;

        /**
         * to indicate if current time is inside any moment time
         *
         * @type {boolean}
         */
        let isCurrentMomentPresent = false;
        let isConferenceOn = false;
        let previousLiveConference = `${this.state.currentLiveConference}`;
        let newLiveConference = null;
        moments.map((item) => {
            const time = Helper.getTimeUserTimeZone(time_zone, `${event_date} ${item.start_time}`);
            const endTime = Helper.getTimeUserTimeZone(time_zone, `${event_date} ${item.end_time}`);
            const currentTime = moment(new Date);
            const startTime = moment(time);
            const endTimedata = moment(endTime);
            const difference = Helper.getTimeDifference(time_zone, time);
            const endDiff = Helper.getTimeDifference(time_zone, endTime);

            if (startTime.diff(currentTime) < 0 && endTimedata.diff(currentTime) > 0) {
                isCurrentMomentPresent = true;
                if (item.moment_type == 1) {
                    // handling networking moment
                    networking = true;
                }
                if (item.moment_type == 2 || item.moment_type == 3 || item.moment_type == 4) {
                    isConferenceOn = true;
                    newLiveConference = item.id;
                }
                // handling content moment
                if (_.has(item, ['video_url']) && !_.isEmpty(item.video_url)) {
                    url = item.video_url;
                    if (item.moment_type == 5) {
                        url = new URL(url);
                        url.searchParams.set('t', `${currentTime.diff(startTime, 'seconds')}`);
                        url = url.href;
                    } else if (item.moment_type == 6) {
                        url = url.replace('#t=', '#temp=');
                        url = `${url}#t=${currentTime.diff(startTime, 'seconds')}`;
                    }
                }
                if (item.moment_type == 2 || item.moment_type == 3 || item.moment_type == 4) {
                    this.props.getGraphics();
                } else {
                    this.props.setEventGraphics({...this.props.event_data, embedded_url: ''})
                }
                typeFlag = true;
            }

            if (startTime.diff(currentTime) > 0 && startTime.diff(currentTime) < nextTimer && nextTimer > 0) {
                nextTimer = startTime.diff(currentTime)
            } else if (endTimedata.diff(currentTime) > 0 && endTimedata.diff(currentTime) < nextTimer && nextTimer > 0) {
                nextTimer = endTimedata.diff(currentTime)
            } else if (nextTimer == 0) {
                if (startTime.diff(currentTime) > 0) {
                    nextTimer = startTime.diff(currentTime)
                } else if (endTimedata.diff(currentTime) > 0) {
                    nextTimer = endTimedata.diff(currentTime)
                }
            }
        });

        this.setState({
            isConferenceOn: isConferenceOn,
        })

        // if conference is now stopped or previous conference is different from new conference
        if (!isConferenceOn || previousLiveConference !== newLiveConference) {
            try {
                ZoomMtg.leaveMeeting({});
                this.props.setEventGraphics({...this.props.event_data, conference_type: 'event_image'})
            } catch (e) {
                console.error("error in zoom leave", e);
            }
        }

        this.setState({currentLiveConference: newLiveConference});

        if (!typeFlag) {
            if (this.state.is_networking && !networking) {
                this.leaveWithoutSocket();
            }
            this.props.updateNetworkingAllow(networking);
            this.setState({is_networking: networking});
        } else {
            this.setState({is_networking: networking});
            this.props.setEventGraphics({...this.props.event_data, embedded_url: ''})
        }
        if (isCurrentMomentPresent === false) {
            // as there is no moment present in current time then allow to do networking
            networking = true;
            this.props.updateNetworkingAllow(networking);
            this.setState({is_networking: networking});
        }
        if (!networking) {
            this.leaveConversation();
        }
        this.setState({recorded_url: url});
        if (nextTimer > 0) {
            setTimeout(() => {
                this.handleMoments(data);
            }, nextTimer)
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles api trigger for graphics data.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} time Number of milliseconds after which the event graphics needs to be re-fetched
     **/
    getSetGraphics = (time, callbackAfterFetch = null) => {
        const {event_data} = this.props;
        setTimeout(() => {
            this.props.getGraphics();
            if (callbackAfterFetch) {
                callbackAfterFetch();
            }
        }, time);

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles api trigger for space data from socket when manual opening is triggered.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    spaceApiTrigger = () => {
        this.props.getGraphics();
        this.getSpacesData()
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description The getter of space data as passing the spaces data directly to socket file will just clone the
     * data of spaces and that data will not be latest from redux so for that the getter is sending which will return
     * latest data from redux
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {InterfaceSpaceData}
     **/
    getCurrentSpace = () => {
        return this.props.spaces_data;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description The getter of space data as passing the spaces data directly to socket file will just clone the
     * data of spaces and that data will not be latest from redux so for that the getter is sending which will return
     * latest data from redux
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {InterfaceEventData}
     **/
    getEventData = () => {
        return this.props.event_data;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description The getter of content data as passing the content data directly to socket file will just clone the
     * data of spaces and that data will not be latest from redux so for that the getter is sending which will return
     * latest data from redux
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    getContentManagementMeta = () => {
        return this.props.contentManagementMeta;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles starting the socket and passing all handlers of events from props
     * -----------------------------------------------------------------------------------------------------------------.
     *
     * @method
     **/
    startSocket = (props = {}) => {
        props = {...this.props, ...props};
        socketManager.initiateSocket(
            {
                ...props,
                askToPrivateConversation: this.askToPrivateConversation,
                handleBanUser2: this.removeUser,
                handleBanUser: this.handleBanUser,
                spaceApiTrigger: this.spaceApiTrigger,
                showAlert: this.alert,
                getCurrentSpace: this.getCurrentSpace,
                leaveConversation: this.leaveWithoutSocket,
                getContentManagementMeta: this.getContentManagementMeta,
                refreshUserData: this.refreshUserData,
                getEventData: this.getEventData,
                applyScenerygraphicData: this.applyScenerygraphicData,
            }
        );
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles stop socket
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    stopSocket = () => {
        socketManager.disconnectSocket();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles redirection of banned user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    redirectToHome = () => {
        const {history} = this.props;
        if (history) history.push('/event-list');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles handle checking of banned user is self or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {object} data socket data object.
     **/
    removeUser = (data) => {
        const banId = data.targetUserId;
        const userId = this.props.auth.user_id;
        if (banId == userId) {
            this.alert && this.alert.show(("notification:ban"), {
                type: 'error', onClose: () => {
                    this.redirectToHome();
                }
            })
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles leaving of conversation api trigger and response/error handling <br>
     * It is in case of user leaves the dashboard page<br>
     * Also stops socket from running
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    leaveConversation = () => {
        const {spaces_data, event_badge} = this.props;
        if (spaces_data.current_joined_conversation !== null && _.has(spaces_data, ['current_joined_conversation', 'conversation_uuid'])) {
            const formData = new FormData()
            formData.append('conversation_uuid', spaces_data.current_joined_conversation.conversation_uuid)
            formData.append('_method', 'DELETE')
            try {
                this.props.leaveConversation(formData)
                    .then((res) => {
                        this.props.conversationLeave({
                            conversationId: spaces_data.current_joined_conversation.conversation_uuid,
                            type: 'delete',
                            userId: event_badge.user_id
                        });

                        this.props.deleteConversation(spaces_data.current_joined_conversation)
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
                        this.stopSocket();
                    })
                    .catch((err) => {
                        this.alert && this.alert.show(Helper.handleError(err), {type: 'error'})
                    })
            } catch (err) {
                this.alert && this.alert.show(Helper.handleError(err), {type: 'error'})
            }
        } else {

            this.stopSocket();
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles leaving of conversation for a banned user api trigger and response/error handling
     * It is in case of user is removed from conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    leaveWithoutSocket = (cb = null) => {
        const {spaces_data} = this.props;
        if (spaces_data.current_joined_conversation !== null && _.has(spaces_data, ['current_joined_conversation', 'conversation_uuid'])) {
            const formData = new FormData()
            formData.append('conversation_uuid', spaces_data.current_joined_conversation.conversation_uuid)
            formData.append('_method', 'DELETE')
            try {
                this.props.leaveConversation(formData)
                    .then((res) => {
                        if (res.data.data) {
                            this.props.deleteConversation(spaces_data.current_joined_conversation)
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
                            if (cb) {
                                cb();
                            }
                        }

                    })
                    .catch((err) => {
                        this.alert && this.alert.show(Helper.handleError(err), {type: 'error'})
                    })
            } catch (err) {
                this.alert && this.alert.show(Helper.handleError(err), {type: 'error'})
            }
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles triggering the space data get api
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    getSpacesData = (callback = null) => {
        const event_uuid = this.props.event_data?.event_uuid;
        try {
            this.props.getEventSpaces(event_uuid).then((res) => {
                const {current_space_host} = res.data.data;
                if (!res.data.data.current_joined_conversation) {
                    runDeviceTest(null, () => {
                        this.setState({
                            showMediaDevicePopup: true,
                        })
                    });
                } else {
                }
                this.props.setEventSpaces(res.data.data);
                this.setState({loading: false});
                this.props.setSpaceHostData(current_space_host);
                const {conference_type, opening_before, opening_after} = this.props.event_data;
                if (conference_type && opening_before && opening_after && ((opening_before) > 0 || (opening_after) > 0)) {
                    this.openingHoursSet(this.props.event_data);
                } else {
                    this.checkEventTime({spaces_data: res.data.data});
                }
                if (callback) {
                    callback({
                        spaces_data: res.data.data,
                        ...this.props
                    })
                }
            }).catch((err) => {
                this.alert && this.alert.show(Helper.handleError(err), {type: 'error'})
            })
        } catch (err) {
            this.alert && this.alert.show(Helper.handleError(err), {type: 'error'});
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Component life cycle method which runs when component is going to unmount<br>
     * It Passes default css in dynamic css generator<br>
     * Triggers Leave conversation if user is in conversation<br>
     * Hides zoom sdk div from DOM
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    componentWillUnmount() {
        const colorObj = reactLocalStorage.get("colorObj");
        if (Helper.objLength(colorObj)) {
            const parsedColor = JSON.parse(colorObj);
            CSSGenerator.generateDefaultCSS(parsedColor);
        }
        this.leaveConversation();
        this.hideZoom();
        try {
            ZoomMtg.leaveMeeting({});
            this.props.setEventGraphics({...this.props.event_data, conference_type: 'event_image'})
        } catch (e) {
            console.error("error in zoom leave ", e);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Hides zoom sdk div from DOM
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    hideZoom = () => {
        const zoomTag = document.getElementById('zmmtg-root');
        if (zoomTag) {
            zoomTag.style.display = "none";
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handles user removed from current conversation by space host socket event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    handleBanUser = () => {
        this.alert && this.alert.show("You have been removed from the conversation", {
            type: 'error', onClose: () => {
                this.leaveWithoutSocket();
            }
        })
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handles conversation private state update <br>
     * By normal user<br>
     * By space host
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data
     * @param {Number} data.senderId Id of the the user to invite
     * @param {Number} data.is_private To tell if the conversation is private
     * @returns {null|*}
     */
    askToPrivateConversation = (data) => {
        const {spaces_data, spaceHost, event_badge} = this.props;
        const {senderId} = data;

        if (senderId == event_badge.user_id) {
            return null;
        }

        if (spaces_data.current_joined_conversation !== null && _.has(spaces_data, ['current_joined_conversation', 'conversation_uuid']) && spaces_data.current_joined_conversation.conversation_uuid == data.conversationId && data.is_private == 0) {
            this.alert && this.alert.show(i18next.t("myBadgeBlock:Host broke isolation"), {type: 'success'})
        }

        if (!_.isEmpty(spaceHost) && spaceHost[0] && _.has(spaceHost[0], ['user_id']) && spaceHost[0].user_id == senderId) {


            const senderExist = spaces_data.current_joined_conversation != null && spaces_data.current_joined_conversation.conversation_users.filter((item) => {
                if (item.user_id == senderId) {
                    return item;
                }
            });
            if (!_.isEmpty(senderExist) && data.is_private == 1) {
                this.alert && this.alert.show(i18next.t("myBadgeBlock:Host request"), {type: 'success'})
            }
            return this.props.handlePrivateConversation2(data);
        }

        if (spaces_data.current_joined_conversation !== null) {

            const senderExist = spaces_data.current_joined_conversation.conversation_users.filter((item) => {
                if (item.user_id == senderId) {
                    return item;
                }
            });

            if (_.isEmpty(senderExist)) {
                this.props.handlePrivateConversation2(data);

            } else {
                this.props.handlePrivateConversation(data);
                this.props.handlePrivateConversation2(data);
            }
        } else {
            this.props.handlePrivateConversation2(data);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handler when pilot mute the conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    handlePilotPanelMute = () => {
        socketManager.emitEvent.PILOT_UPDATE_NETWORKING({
            action: Constants.networkingState.MUTE,
            value: this.props.conversationMeta.mute === 1 ? 0 : 1,
            eventId: this.props.event_data.event_uuid,
        })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handler when pilot close the networking
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    handlePilotPanelClose = () => {
        socketManager.emitEvent.PILOT_UPDATE_NETWORKING({
            action: Constants.networkingState.CLOSE,
            eventId: this.props.event_data.event_uuid,
            value: this.props.gridMeta.visible ? 0 : 1,
        });
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handler when pilot close the content player
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    handleContentCloseButton = () => {
        socketManager.emitEvent.PILOT_UPDATE_CONTENT({
            currentMediaType: null,
            currentMediaData: {},
            value: this.props.contentManagementMeta.componentVisibility ? 0 : 1,
            action: Constants.contentState.CLOSE,
            eventId: this.props.event_data.event_uuid,
        })
    }

    refreshUserData = (callback => {
        this.getSpacesData(callback);
    })

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handler when pilot toggle the video from available videos
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} key id of video to play
     */
    handlePilotSelectVideo = (key = null) => {
        let dataToSend = {
            currentMediaType: null,
            currentMediaData: {},
        }
        if (key) {
            let linkToSend = this.props.event_data.event_live_video_links.find((link) => {
                return link.key === key;
            });
            let contentType = Constants.contentManagement.CNT_MGMT_VIDEO;
            if (!linkToSend) {
                return;
            }
            dataToSend = {
                currentMediaType: contentType,
                currentMediaData: linkToSend,
            }
        }
        socketManager.emitEvent.PILOT_UPDATE_CNT_MNGM({
            eventId: this.props.event_data.event_uuid,
            ...dataToSend,
        })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handler when pilot select/deselect the image to show on content player
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} key Id of image to fetch from redux and display
     */
    handlePilotSelectImage = (key = null) => {
        let dataToSend = {
            currentMediaType: null,
            currentMediaData: {},
        }
        if (key) {
            let linkToSend = this.props.event_data.event_live_images.find((link) => {
                return link.key === key;
            });
            let contentType = Constants.contentManagement.CNT_MGMT_IMAGE;
            if (!linkToSend) {
                return;
            }
            dataToSend = {
                currentMediaType: contentType,
                currentMediaData: linkToSend,
            }
        }
        socketManager.emitEvent.PILOT_UPDATE_CNT_MNGM({
            eventId: this.props.event_data.event_uuid,
            ...dataToSend,
        })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handler when pilot turn on or off the zoom sdk display to other user
     * when sdk is turned off, instead of closing that it will just hide it and sdk will keep running in background
     * silently
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    handleZoomBroadcastButton = () => {
        let mediaType = null;
        if (this.props.contentManagementMeta.currentMediaType !== Constants.contentManagement.CNT_MGMT_ZOOM_SDK) {
            // here current content type is either null or set to something else than zoom
            // so setting media type to zoom and zoom sdk will be visible then
            mediaType = Constants.contentManagement.CNT_MGMT_ZOOM_SDK;
        }
        socketManager.emitEvent.PILOT_UPDATE_CNT_MNGM({
            eventId: this.props.event_data.event_uuid,
            currentMediaType: mediaType,
            currentMediaData: {key: 'zoom'},
        });
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handler when pilot update the audio state for zoom mic
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Boolean} val Value of mute state for zoom sdk
     */
    handleZoomMute = (val) => {
        if (_.has(this.props.event_data, ['embedded_url']) && !_.isEmpty(this.props.event_data.embedded_url)) {
            socketManager.emitEvent.PILOT_UPDATE_CONTENT({
                eventId: this.props.event_data.event_uuid,
                action: Constants.contentState.MUTE,
                value: val,
            })
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle when user select the device from device selector, here the selected devices will be stored
     * in localstorage for all type (video input, audio output and audio input)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} audioDevice Selected Audio input device id
     * @param {String} videoDevice Selected video input device id
     * @param {String} audioOutputDevice Selected Audio output device id
     * @param {Boolean} showPopup To indicate to show or hide the popup
     */
    onDeviceSubmit = (audioDevice, videoDevice, audioOutputDevice, showPopup = false) => {
        localStorage.setItem("user_audio", audioDevice);
        localStorage.setItem("user_video", videoDevice);
        localStorage.setItem("user_audio_o", audioOutputDevice);

        this.setState({showMediaDevicePopup: showPopup});
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To upload and save the image that needs to be uploaded as user profile picture
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {File} file Image file that will be uploaded as profile picture
     */
    saveImage = (file) => {
        this.updateProfileData({field: "avatar", value: file})
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To call the api and send the image data to upload and set that as profile picture for user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Data that needs to be send in api
     * @param {String} data.field Field name that is updated
     * @param {String|File} data.value Value of that respective fields that needs to be updated
     * @param {Function} onUpdate Call back method when the image is successfully update to update the redux store also
     */
    updateProfileData = (data, onUpdate) => {
        const {event_data} = this.props;
        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("field", data.field);
        formData.append("value", data.value);
        formData.append('event_uuid', event_data.event_uuid);
        //  &&


        try {
            this.props.updateProfileData(formData)
                .then((res) => {
                    const badgeData = res.data.data;
                    this.props.setBadge(badgeData);
                    const event_uuid = event_data.event_uuid;
                    this.props.updateProfileTrigger(badgeData, event_uuid);

                    if (onUpdate) {
                        onUpdate(badgeData);
                    }
                    if (_.has(data, ['resetFunc']) && data.resetFunc) {
                        data.resetFunc(true);
                    }
                    data.field === 'fname' && this.props.updateInitName(badgeData.user_fname)
                    this.alert && this.alert.show(i18next.t("myBadgeBlock:Record Updated"), {type: "success"});
                })
                .catch((err) => {
                    console.error(err)
                    this.alert && this.alert.show(Helper.handleError(err), {type: "error"});
                    if (_.has(data, ['resetFunc']) && data.resetFunc) {
                        data.resetFunc(true);
                    }
                })
        } catch (err) {
            this.alert && this.alert.show(Helper.handleError(err), {type: "error"});
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handler when the image is updated from the popup of device selector here image will be sent to
     * save image method which will prepare and trigger the api to upload the image and after that the popup will be
     * closed
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {File} file image file
     */
    onImageUploadFromPopup = (file) => {
        this.saveImage(file);
        this.setState({showMediaDevicePopup: false});
    };

    // const extendedSpaceComponent = extends_color_user_guide === 1 ? this.state.spaceSectionSceneryColor : "";
    render() {

        const {
            graphics_data,
            event_data,
            spaces_data,
            sliderData,
            space_load,
            conversationModal,
            isolationConversationModal,
            spaceHost,
            event_badge
        } = this.props;

        const {loading} = this.state;
        if (loading) {
            return (<div>
                <Helper.pageLoading />
                <AlertContainer
                    ref={(a) => (this.alert = a)}
                    {...Helper.alertOptions}
                /></div>)
        }
        const {
            userGridSceneryStyle,
            bgColorWrapSceneryStyle,
            myBadgeBlockSceneryStyle_childDiv,
            myBadgeBlockSceneryStyle
        } = this.state;

        const hideConversation = this.state.event_during && event_data.opening_during && (parseInt(event_data.opening_during) == 0);
        const spacesActive = (this.state.event_during || this.state.before_on || this.state.after_on);

        const showContentSection = _.has(event_data, ['moments'])
            && Object.keys(event_data.moments).length == 1
            && event_data.moments[0].moment_type == 1;

        return (
            <div className="badge-block" style={{}}>
                {this.props.spaces_data && this.state.showMediaDevicePopup && !this.props.spaces_data.current_joined_conversation &&
                <MediaDevicePopup
                    allowClose={true}
                    onClose={() => this.setState({showMediaDevicePopup: false})}
                    msg={this.alert && this.alert.show}
                    mode={Constants.mediaDevicePop.MODE_DEVICE_SET}
                    onSubmit={this.onDeviceSubmit}
                    onSaveImage={this.onImageUploadFromPopup}
                    userFirstName={event_badge.user_fname}
                    userLastName={event_badge.user_lname}
                    saveImage={this.saveImage}
                />
                }
                <AlertContainer
                    ref={(a) => (this.alert = a)}
                    {...Helper.alertOptions}
                />
                <Header
                    graphics_data={graphics_data}
                    event_data={event_data}
                    eventGraphics={this.props.eventGraphics}
                />
                {/* {this.state.recorded_url &&
                    <VideoPlayer url={this.state.recorded_url} />
                } */}

                <div className='scenery_bgcolorWrap' style={bgColorWrapSceneryStyle}>
                    {_.has(event_data, ['conference_type'])
                    && _.has(event_data, ['embedded_url'])
                    && event_data.embedded_url != null
                    && event_data.conference_type
                    && (this.state.before_on || this.state.after_on)
                    &&
                    <MainHostVideo
                        event_data={event_data}
                        isZoomInitialized={this.state.isZoomInitialized}
                        setZoomInitialize={(s) => {
                            this.setState({isZoomInitialized: s})
                        }}
                    />
                    }


                    {!_.isEmpty(spaceHost) &&
                    <SpaceHostVideoBlock
                        sh_hide_on_off={_.has(this.props.graphics_data, ['sh_hide_on_off']) ? this.props.graphics_data.sh_hide_on_off : 0}
                        event_data={event_data}
                        event_during={this.state.event_during}
                        video_url={this.state.recorded_url}
                        active={spaces_data.current_joined_conversation !== null}
                        zoomInitializeState={{
                            get: this.state.isZoomInitialized,
                            set: (value) => {
                                this.setState({
                                    isZoomInitialized: value,
                                })
                            }
                        }}
                        alert={this.alert}
                        hideHostCallBtn={this.props.gridMeta.visible}
                        updateProfileData={this.updateProfileData}
                        showContentSection={!showContentSection}

                    />
                    }
                </div>
                <div ref={this.conversationRef} className="my_badgeBlock"
                     style={myBadgeBlockSceneryStyle}>
                    <NewBlock
                        event_during={spacesActive}
                        alert={this.alert}
                        active={spaces_data.current_joined_conversation !== null}
                        event_data={event_data}
                        graphics_data={graphics_data}
                        is_event_end={this.state.is_event_end}
                        spaceHost={spaceHost}
                        event_badge={event_badge}
                        updateProfileData={this.updateProfileData}
                        userFirstName={event_badge.user_fname}
                        userLastName={event_badge.user_lname}
                        hideSHButton={this.props.gridMeta.visible == true && !hideConversation}

                        //scenery style data
                        myBadgeBlockSceneryStyle_childDiv={myBadgeBlockSceneryStyle_childDiv}
                    />
                </div>
                <div className="spaceContainer" style={userGridSceneryStyle}>


                    <div
                        className={`bottom-background-color 
                    ${(graphics_data.bottom_bg_is_colored != undefined
                            && graphics_data.bottom_bg_is_colored == true
                            && graphics_data.space_customization == 1)
                            ? "kct-customization" : ""}
                        color-extend`}
                        style={this.state.extendedSpaceComponent}
                    >

                        {_.has(event_data, ['is_mono_present']) && event_data.is_mono_present == 1 ?
                            null
                            :
                            <div className="container main-space kct-customization"
                                 style={this.state.spaceSectionSceneryColor}>
                                <SliderSPersons
                                    spacesActive={spacesActive}
                                    welcome_txt={this.state.wlcmTxt}
                                    event_during={this.state.event_during}
                                    sliderData={sliderData}
                                    alert={this.alert}
                                    event_data={event_data}
                                    event_space={spaces_data}
                                    active_space={_.has(spaces_data, ['current_joined_space']) ? spaces_data.current_joined_space : {}}
                                    triggerPagination={this.props.triggerPagination}
                                />
                            </div>
                        }

                        <GridComponent />
                    </div>
                </div>
                {conversationModal && <ConversationPopup alert={this.alert} />}
                {isolationConversationModal && <IsolationPopup />}
                {/*<NotifierBar*/}
                {/*    graphics_data={graphics_data}*/}
                {/*    isZoomInitialized={this.state.isZoomInitialized}*/}
                {/*    notificationType={this.state.notifierBarMessageType}*/}
                {/*/>*/}
                <div style={{display:"flex",justifyContent:"flex-end"}}>
                    <MyNetworkDropDown />
                </div>
                {this.state.pilotPanelVisible && this.state.event_during &&
                <PilotPanel
                    event_data={event_data}
                    event_meta={this.props.eventMeta}
                    contentManagementMeta={this.props.contentManagementMeta}
                    // networking component related states
                    handlePilotPanelMute={this.handlePilotPanelMute}
                    handlePilotPanelClose={this.handlePilotPanelClose}
                    // content section related states
                    handleContentCloseButton={this.handleContentCloseButton}
                    videoLinks={this.props.event_data.event_live_video_links}
                    imageLinks={this.props.event_data.event_live_images}
                    // content management related states
                    handlePilotSelectVideo={this.handlePilotSelectVideo}
                    handlePilotSelectImage={this.handlePilotSelectImage}
                    handleBroadcastToggle={this.handleZoomBroadcastButton}
                    handleZoomMute={this.handleZoomMute}
                    isConferenceOn={this.state.isConferenceOn}
                />
                }
                <Footer graphics_data={graphics_data} />
            </div>
        )
    }
}


const mapDispatchToProps = (dispatch) => {
    return {
        getBadge: (event_uuid) => dispatch(eventActions.Event.getBadge(event_uuid)),
        getTags: () => dispatch(eventActions.Event.getTag()),
        getEventSpaces: (id) => dispatch(eventActions.Event.getEventSpaces(id)),
        setTags: (data) => dispatch(newInterfaceActions.NewInterFace.setTagsData(data)),
        setBadge: (data) => dispatch(newInterfaceActions.NewInterFace.setBadgeData(data)),
        setEventSpaces: (data) => dispatch(newInterfaceActions.NewInterFace.setSpacesData(data)),
        updateSpacesData: (data) => dispatch(newInterfaceActions.NewInterFace.updateSpacesData(data)),
        setSpaceHostData: (data) => dispatch(newInterfaceActions.NewInterFace.setSpaceHostData(data)),//SapceHsot
        setEventGraphics: (data) => dispatch(newInterfaceActions.NewInterFace.setEventData(data)),
        filterOnline: (data) => dispatch(newInterfaceActions.NewInterFace.filterOnline(data)),
        conversationJoin: (id) => dispatch(eventActions.Event.conversationJoin(id)),
        updateUserPosition: (data) => dispatch({type: KCT.NEW_INTERFACE.UPDATE_USERS_CONVERSATION, payload: data}),
        conversationLeave: (data) => dispatch({type: KCT.NEW_INTERFACE.UPDATE_EVENT_CONVERSATIONS, payload: data}),
        leaveConversation: (id) => dispatch(eventActions.Event.leaveConversation(id)),
        addNewUser: (data) => dispatch({type: KCT.NEW_INTERFACE.ADD_NEW_EVENT_MEMBERS, payload: data}),
        updateProfile: (data) => dispatch(newInterfaceActions.NewInterFace.updateProfile(data)),
        spaceUserCountUpdate: (data) => dispatch({type: KCT.NEW_INTERFACE.UPDATE_SPACE_USERS_COUNTS, payload: data}),
        getCurrentConversation: (eventId) => dispatch(eventActions.Event.getCurrentConversation(eventId)),
        changeConversationId: (id) => dispatch({type: KCT.NEW_INTERFACE.CHANGE_CONVERSATION_IDS, payload: id}),
        selfJoinedNewConversation: (data, userId) => dispatch({type: KCT.NEW_INTERFACE.ADD_EVENT_MEMBER_BY_USER_IDS, payload: data, userId: userId}),
        deleteConversation: (id) => dispatch({type: KCT.NEW_INTERFACE.DELETE_CONVERSATIONS, payload: id}),
        setIsOnlineDataReceived: (id) => dispatch(() => {}),
        askJoinConversation: (data) => dispatch(newInterfaceActions.NewInterFace.askToJoin(data)),
        handlePrivateConversation: (data) => dispatch(newInterfaceActions.NewInterFace.askToPrivateConversation(data)),//spcket handle for private conversation
        handlePrivateConversation2: (data) => dispatch(newInterfaceActions.NewInterFace.askToPrivateConversation2(data)),
        handleConfereceUpdate: (data) => dispatch(newInterfaceActions.NewInterFace.handleConfereceUpdate(data)),
        triggerPagination: (data) => dispatch(newInterfaceActions.NewInterFace.triggerPagination(data)),
        updateConversationMute: (data) => dispatch(newInterfaceActions.NewInterFace.setConversationMute(data)),
        updateGridVisibility: (data) => dispatch(newInterfaceActions.NewInterFace.setGridVisibility(data)),
        updateContentData: (data) => dispatch(newInterfaceActions.NewInterFace.setCurrentContent(data)),
        setContentComponentVisibility: (data) => dispatch(newInterfaceActions.NewInterFace.setContentComponentVisibility(data)),
        setZoomMuteButton: (data) => dispatch(newInterfaceActions.NewInterFace.setZoomMuteButton(data)),
        callOff: () => dispatch(newInterfaceActions.NewInterFace.callOff()),
        setZoomUserAdmitState: (data) => dispatch(newInterfaceActions.NewInterFace.setZoomUserAdmitState(data)),
        updateProfileData: (data) => dispatch(eventActions.Event.updateProfileData(data)),
        updateProfileTrigger: (data, id) => dispatch(newInterfaceActions.NewInterFace.updateProfileTrigger(data, id)),
        updateInitName: (data, index) => dispatch({type: KCT.EVENT.UPDATE_INIT_NAME, payload: data}),
        resetSpace: (data) => dispatch({type: KCT.NEW_INTERFACE.RESET_SPACE, payload: data}),
        changeSpace: (data) => dispatch({type: KCT.NEW_INTERFACE.CHANGE_SPACES, payload: data,}),
        spaceJoin: (id) => dispatch(eventActions.Event.spaceJoin(id)),
        updateNetworkingAllow: (networking) => dispatch({type: KCT.DASHBOARD.UPDATE_NETWORKING_ALLOW, payload: networking}),
        updateEventScenery: (data) => dispatch(graphicActions.updateEventScenery(data)),

    }
}

const mapStateToProps = (state) => {
    return {
        graphics_data: state.NewInterface.interfaceGraphics,
        event_data: state.NewInterface.interfaceEventData,
        spaces_data: state.NewInterface.interfaceSpacesData,
        event_badge: state.NewInterface.interfaceBadgeData,
        auth: state.NewInterface.interfaceAuth,
        sliderData: state.NewInterface.interfaceSliderData,
        space_load: state.NewInterface.spacesDataLoad,
        conversationModal: _.has(state.NewInterface.callJoinState, ['valid']) ? state.NewInterface.callJoinState.valid : false,
        isolationConversationModal: _.has(state.NewInterface.privateCallJoinState, ['valid']) ? state.NewInterface.privateCallJoinState.valid : false,
        spaceHost: state.NewInterface.interfaceSpaceHostData,
        gridPagination: state.NewInterface.gridPagination,
        conversationMeta: state.NewInterface.conversationMeta,
        gridMeta: state.NewInterface.gridMeta,
        contentManagementMeta: state.NewInterface.contentManagementMeta,
    };
};
withTranslation(['myBadgeBlock', 'notification'])(Dashboard)
export default connect(mapStateToProps, mapDispatchToProps)(Dashboard);


