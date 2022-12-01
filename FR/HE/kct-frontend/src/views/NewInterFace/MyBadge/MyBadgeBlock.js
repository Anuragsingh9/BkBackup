import React, {useEffect, useRef, useState} from 'react';
import {connect} from 'react-redux';
import {Animated} from "react-animated-css";
import './Animated.css'
import _ from 'lodash'
import BadgePopUp3 from './BadgePopup/BadgePopUp3.js';
import eventActions from '../../../redux/actions/eventActions';
import newInterfaceActions from '../../../redux/actions/newInterfaceAction';
import './BadgePopup.css';
import Helper from '../../../Helper.js';
import ImageEditing from './BadgeEditor/imageEditor/imageEditing';
import KeepContactagent from '../../../agents/KeepContactagent';
import {Provider as AlertContainer, useAlert } from 'react-alert';
import CallingStatus from '../VideoConference/CallingStatus/CallingStatus.js';
import NewSlider from './BadgeSideComponent/NewSlider';
import {getMediaPermissions} from '../Conversation/Utils/Conversation';
import MediaDevicePopup from '../../Index/MediaDevicePopup/MediaDevicePopup';
import './MyBadgeBlock.css';
import Svg from '../../../Svg';

import {useTranslation} from 'react-i18next';
import Constants from "../../../Constants";
import SpaceHostCallButton from "../Common/SpaceHostCallButton";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used to show user badge
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {EventData} props.event_data Current event data
 * @param {Boolean} props.event_during To indicate if the event is live or not
 * @param {Boolean} props.is_event_end To indicate if event is ended or not as ended event will have different text
 * @param {String} props.userFirstName User First Name
 * @param {String} props.userLastName User Last Name
 * @param {Function} props.hideSHButton To hide the space host calling button
 * @param {Function} props.setBadge To update the badge details of user
 * @param {Function} props.updateProfileTrigger To update the profile data on backend server
 * @param {Function} props.updateInitName To update the user name in init data response
 * @param {Function} props.updateProfileData To update the user data on server
 * @returns {JSX.Element}
 * @constructor
 */
const MyBadgeBlock = (props) => {

    const {active, event_data, event_during, is_event_end} = props;
    const [showPanel, setShowPanal] = useState(true);
    const [modal, setModal] = useState(false);
    const [badgeData, setBadgeData] = useState({});
    // device popup show state
    const [showDeviceSelector, setShowDeviceSelector] = useState(false);
    const [showMediaDevicePopup, setShowMediaDevicePopup] = useState(false);
    const [devicePopupMode, setDevicePopupMode] = useState(Constants.mediaDevicePop.MODE_DEVICE_SET);
    // all available devices
    const [availableMediaDevices, setAvailableMediaDevice] = useState({});
    // state to handle no preview div
    const [noPreviewDiv, setNoPreviewDiv] = useState(true);
    //state for media device popup to show capture buttomn in it
    const [showCaptureBtn, setShowCaptureBtn] = useState(false)
    //draw image
    const [imageCaptured, setImageCaptured] = useState("")
    const msg = useAlert()
    const {t} = useTranslation(['myBadgeBlock', 'notification'])
    const [first, setfirst] = useState(0)


    useEffect(() => {
        const {event_badge} = props;
        setBadgeData(event_badge);
        getMediaPermissions(setShowDeviceSelector, setAvailableMediaDevice);
    }, [])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles delete avatar data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const deleteAvatar = () => {
        const {event_data} = props;
        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append("event_uuid", event_data.event_uuid);

            KeepContactagent.Event.deleteProfilePic(formData).then(response => {
                if (response.data.status) {
                    msg && msg.show(t("notification:flash msg rec update 1"), {
                        type: "success"
                    });
                    const badge_data = badgeData;
                    let badge_Data = {
                        ...badge_data,
                        user_avatar: null
                    }

                    props.setBadge(badge_Data);

                    const event_uuid = event_data.event_uuid;
                    props.updateProfileTrigger(badge_Data, event_uuid);

                    setBadgeData(badge_Data)
                } else {
                    msg && msg.show(t("notification:flash msg rec update 0"), {
                        type: "error"
                    });
                }
            }).catch(err => {
                msg && msg.show(Helper.handleError(err), {
                    type: "error"
                });
            })

        } catch (err) {
            if (msg) msg.show(Helper.handleError(err), {
                type: "error"
            });
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles upload avatar data using api
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Data to be updated for single field
     * @param {String} data.field Field name which visibility needs to be updated
     * @param {String} data.value Value of visibility for respective field name
     * @param {Function} data.resetFunc Function to reset the badge popup visibility
     */
    const updateProfileData = (data) => {
        const {event_data} = props;
        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("field", data.field);
        formData.append("value", data.value);
        formData.append('event_uuid', event_data.event_uuid);
        //  && 


        try {
            props.updateProfileData(formData)
                .then((res) => {
                    const badgeData = res.data.data;
                    props.setBadge(badgeData);
                    const event_uuid = event_data.event_uuid;
                    props.updateProfileTrigger(badgeData, event_uuid);

                    setBadgeData(badgeData)
                    if (_.has(data, ['resetFunc']) && data.resetFunc) {

                        data.resetFunc(true);
                    }

                    data.field === 'fname' && props.updateInitName(badgeData.user_fname)
                    msg && msg.show(t("Record Updated"), {type: "success"});

                })
                .catch((err) => {
                    console.error(err)
                    msg && msg.show(Helper.handleError(err), {type: "error"});
                    if (_.has(data, ['resetFunc']) && data.resetFunc) {

                        data.resetFunc(true);
                    }
                })
        } catch (err) {

            msg && msg.show(Helper.handleError(err), {type: "error"});

        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the popup visibility
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number|Boolean} captureMode Mode value of capture
     */
    const handleSettingPopup = (captureMode = false) => {
        setShowMediaDevicePopup(true);

        setDevicePopupMode(captureMode ?
            Constants.mediaDevicePop.MODE_CAPTURE_AND_PREVIEW :
            Constants.mediaDevicePop.MODE_DEVICE_SET
        );
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To toggle the panel, if its true it will se false and vice versa
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const togglePanel = () => {
        setShowPanal(!showPanel)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To toggle the popup visibility
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const togglePopup = () => {
        setModal(!modal)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To save the image file and upload it to user profile data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {File} file File value of user selected/captured image
     */
    const saveImage = (file) => {
        updateProfileData({field: "avatar", value: file})
    }

    let userTags = []
    if (props.event_tag && props.event_tag.used_tag) {
        userTags = props.event_tag.used_tag
    }

    const BadgeData = props.event_badge ? props.event_badge : badgeData;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if space host is in current conversation or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {boolean}
     */
    const checkSpaceHost = () => {
        const {spaceData, spaceHostData, event_badge} = props;

        const {current_joined_conversation} = spaceData;

        if (
            _.has(current_joined_conversation, ['conversation_users'])
            && !_.isEmpty(current_joined_conversation.conversation_users)
            && !_.isEmpty(spaceHostData)
        ) {
            const flag = current_joined_conversation.conversation_users.filter((val) => {
                if (val.user_id == spaceHostData[0].user_id) {
                    return val
                }
            });
            return !_.isEmpty(flag)
        } else {
            return false;
        }
    }

    const hostThere = checkSpaceHost();


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle when the device is submitted from media popup
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} audioDevice Selected Audio input device id
     * @param {String} videoDevice Selected video input device id
     * @param {String} audioOutput Selected Audio output device id
     * @param {Boolean} showPopup To indicate to show or hide the popup
     */
    const onDeviceSubmit = (audioDevice, videoDevice, audioOutput, showPopup) => {
        localStorage.setItem("user_audio", audioDevice);
        localStorage.setItem("user_video", videoDevice);
        localStorage.setItem("user_audio_o", audioOutput);

        if (devicePopupMode === Constants.mediaDevicePop.MODE_DEVICE_SET) {
            // if media device is opened for setting the device only then close after submit
            setShowMediaDevicePopup(showPopup);
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle when image is uploaded from the popup
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {File} file File value of the user selected/captured image
     */
    const onImageUploadFromPopup = (file) => {
        saveImage(file);
        setShowMediaDevicePopup(false);
    };

    return (
        <div>
            <AlertContainer
                ref={msg}
                {...Helper.alertOptions}
            />
            <div className="row" style={{zIndex: 5}}>
                {(props.callStatus) && <CallingStatus calledUserId={props.calledUserId} show={props.callStatus} />}
                <div className="col-xs-3 col-sm-2 col-md-12 px-0">
                    <div className="content-middle-alignment content-middle-alignment-md">
                        <div className="d-inline-block badge-inner float-left">

                            <ImageEditing
                                handleSettingPopup={() => handleSettingPopup(true)}
                                setShowCaptureBtn={setShowCaptureBtn}
                                msg={msg && msg && msg.show}
                                userTxt={Helper.nameProfile(BadgeData.user_fname, BadgeData.user_lname)}
                                handleRemovePic={deleteAvatar} avatar={BadgeData.user_avatar}
                                onSaveImage={saveImage} />

                            <span className="badge-arrow" style={{cursor: 'pointer'}}
                                  onClick={togglePanel}>{showPanel ? '<' : '>'}</span>
                        </div>
                        <div className="d-inline-block float-left UserCompletInfobadge">
                            < Animated style={{width: '100px', marginleft: '0px'}} animationIn="fadeInLeft"
                                       animationOut="fadeOutLeft" animationInDuration={1500} animationOutDuration={800}
                                       isVisible={showPanel}>

                                <NewSlider tagData={userTags} item={BadgeData} />
                            </Animated>
                        </div>
                    </div>
                    {/* <div>
                            <ImgCropper saveImage={saveImage}/>
                        </div> */}
                </div>
                <div
                    className="col-xs-5 col-sm-2 col-md-1 col-lg-1 px-0 badge-contact-edit WigyButton-md userBadgeBtnFlex">
                    <button className="no-texture" onClick={(e) => togglePopup()}>
                        <svg width="21px" height="21px" version="1.0" viewBox="0 0 493 354"
                             xmlns="http://www.w3.org/2000/svg">
                            <g transform="translate(0 354) scale(.1 -.1)">
                                <path
                                    d="m445 3521c-201-58-378-237-430-437-13-51-15-219-15-1312 0-1394-4-1308 68-1447 49-94 163-208 257-257 141-73-24-68 2140-68s1999-5 2140 68c94 49 208 163 258 258 71 138 67 50 67 1444s4 1306-67 1444c-50 95-164 209-258 258-142 73 25 68-2146 67-1820 0-1958-2-2014-18zm3967-310c71-27 131-82 168-155l35-69v-1217-1217l-35-69c-40-78-104-134-179-156-68-19-3804-19-3872 0-75 22-139 78-179 156l-35 69-3 1186c-2 809 1 1202 8 1237 24 114 112 209 221 238 79 21 3814 18 3871-3z"></path>
                                <path
                                    d="m1465 2769c-77-11-191-67-248-123-277-268-130-734 251-798 306-51 582 220 534 527-41 263-270 432-537 394zm114-319c71-20 120-96 106-166-8-45-57-100-101-114-100-33-209 62-189 166 7 40 71 110 107 117 39 7 42 7 77-3z"></path>
                                <path
                                    d="m2704 2300c-33-13-81-75-88-114-10-52 9-107 49-146l36-35 756-3 755-2 35 21c39 24 73 86 73 134s-34 110-72 133l-35 22-744-1c-409 0-753-4-765-9z"></path>
                                <path
                                    d="m1450 1695c-8-2-49-9-90-15-337-55-631-313-739-649-46-143-9-237 103-261 35-8 295-10 851-8l800 3 38 23c81 51 91 133 35 279-128 336-410 566-758 619-85 12-199 17-240 9zm225-330c119-25 227-86 322-181 46-45 83-87 83-93 0-8-163-11-540-11s-540 3-540 11c0 6 37 48 83 93 62 62 101 92 159 121 143 70 288 90 433 60z"></path>
                                <path
                                    d="m2700 1678c-105-54-114-199-17-272 28-20 38-21 397-21 357 0 369 1 396 21 53 39 69 71 69 134s-16 95-69 134c-26 20-42 21-379 24-350 3-352 3-397-20z"></path>
                            </g>
                        </svg>
                    </button>
                    {/* newdevice */}
                    <button className="no-texture audioVideoBtn" onClick={() => handleSettingPopup()}
                            dangerouslySetInnerHTML={{__html: Svg.ICON.setting}}
                    >
                    </button>
                    {showMediaDevicePopup &&
                    <MediaDevicePopup
                        allowClose={true}
                        onClose={() => setShowMediaDevicePopup(false)}
                        mode={devicePopupMode}
                        msg={msg && msg && msg.show}
                        onSubmit={onDeviceSubmit}
                        onSaveImage={onImageUploadFromPopup}
                        //new props
                        profileData={BadgeData}
                        saveImage={saveImage}
                        userFirstName={props.event_badge.user_fname}
                        userLastName={props.event_badge.user_lname}
                    />
                    }

                    {_.has(props, ['hideSHButton']) && props.hideSHButton &&
                    <SpaceHostCallButton
                        alert={msg}
                    />
                    }
                    {/* <SpaceHostCallButton /> */}
                </div>
                <div className="col-xs-3 col-sm-7 col-md-10 col-lg-10  posi-fix WigyDis-md">
                    {!event_during ? (is_event_end ? <p className="badge-decription">{t("badge text after event")}</p> :
                            <p className="badge-decription">{t("badge text before event")}</p>) :
                        <p className="badge-decription">{t("badge text during event")}</p>}
                </div>
            </div>
            <div className="row badge-edit-position">

            </div>
            {modal &&
            <BadgePopUp3 alert={msg && msg} togglePopup={togglePopup} event_uuid={event_data.event_uuid}
                         toggleReset={togglePopup} modal={modal} />}
        </div>
    )
}


const mapDispatchToProps = (dispatch) => {
    return {
        updateProfileTrigger: (data, id) => dispatch(newInterfaceActions.NewInterFace.updateProfileTrigger(data, id)),
        updateProfileData: (data) => dispatch(eventActions.Event.updateProfileData(data)),
        getBadge: () => dispatch(eventActions.Event.getBadge()),
        setBadge: (data) => dispatch(newInterfaceActions.NewInterFace.setBadgeData(data)),
        callTrigger: () => dispatch(newInterfaceActions.NewInterFace.callTrigger()),
        setCalledUserId: (id) => dispatch(newInterfaceActions.NewInterFace.setCalledUserId(id)),

    }
}

const mapStateToProps = (state) => {

    return {
        event_badge: state.NewInterface.interfaceBadgeData,
        event_tag: state.NewInterface.interfaceTagData,
        callStatus: state.NewInterface.makingCall,
        calledUserId: state.NewInterface.calledUserId,
        spaceHostData: state.NewInterface.interfaceSpaceHostData,
        spaceData: state.NewInterface.interfaceSpacesData,
    };
};


export default connect(mapStateToProps, mapDispatchToProps)(MyBadgeBlock);
