import React, {useEffect, useState} from "react";
import ReactDOM from 'react-dom';
import "./MediaDevicePopup.css";
import "./ProfilePopup.css";
import RoundedCrossIcon from "../../Svg/RoundedCrossIcon";
import CapturedPreview from "./CapturedPreview";
import UpdateProfile from "./UpdateProfile";
import MediaDeviceSelector from "./MediaDeviceSelector";
import Constants from "../../../Constants";
import {useTranslation} from 'react-i18next';
import _ from 'lodash';
import  MediaDevicePopupTabs from './MediaDevicePopupTabs';
import MediaGrid from "./MediaGrid";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to manage device settings(select camera/mic/speaker) for the conversation.
 * In this component user can see all available/connected devices for camera,mic,speaker with current selected device
 * preview(image and sound bar). <br>
 * If user does not upload his profile picture then this component will help to click an image from selected
 * camera and upload it in user profile.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Boolean} props.allowClose To indicate if the popup can be closed or not
 * @param {Number} props.mode To indicate the mode of popup like device input mode or capture image mode
 * @param {Function} props.onClose Handler method when the popup is being closed
 * @param {Function} props.onSaveImage Handler method to handle the image when capture from capture mode
 * @param {Function} props.onSubmit Handler method for device selection mode
 * @param {String} props.userFirstName User First Name
 * @param {String} props.userLastName User Last Name
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
function MediaDevicePopup(props) {
    const {t} = useTranslation('myBadgeBlock')
    const popupState_preview = 1;
    const popupState_captured = 2;
    const popupState_updateProfile = 3;
    const popupState_uploadProfile = 4;

    const [currentPopupContent, setCurrentPopupContent] = useState(popupState_preview);

    const [capturedImageBase64, setCapturedImageBase64] = useState(null);

    const [selectorMode, setSelectorMode] = useState(Constants.mediaDevicePop.MODE_DEVICE_SET);

    useEffect(() => {
        if (_.has(props, ["mode"])) {
            setSelectorMode(props.mode)
        }
    }, [])


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will take captured image data(from it's parameter) in the form of base 64 object and
     * save it in a state(setCapturedImageBase64,setCurrentPopupContent).
     * This component is divided into 2 major parts which is device selector and image capture.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} capturedImageBase64 Base64 Image File Value that is captured from camera
     */
    const handleDeviceCapture = (capturedImageBase64) => {
        setCapturedImageBase64(capturedImageBase64);
        if (selectorMode === Constants.mediaDevicePop.MODE_DEVICE_SET) {
            setCurrentPopupContent(popupState_updateProfile);
        } else {
            setCurrentPopupContent(popupState_captured);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle media popup mode and change it to preview mode to capture an
     * image(for profile picture).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleRetake = () => {
        setCurrentPopupContent(popupState_preview);
    }

    const {userFirstName, userLastName} = props;
    const MediaDeviceComponent = () =>
                <div className={`modal-body ${currentPopupContent === popupState_captured ? "profilePopup" : ""}`}>

                    {
                        currentPopupContent === popupState_preview &&
                        <MediaDeviceSelector
                            onSubmit={props.onSubmit}
                            onCapture={handleDeviceCapture}
                            // to indicate current mode is submit device or capture image from device
                            mode={selectorMode}
                        />
                    }
                    {
                        currentPopupContent === popupState_captured &&
                        <>
                            <CapturedPreview
                                capturedImageUrl={capturedImageBase64}
                                onRetake={handleRetake}
                                onSaveCapturedImage={props.onSaveImage}
                            />

                        </>
                    }
                    {
                        currentPopupContent === popupState_updateProfile &&
                        <>
                            <UpdateProfile
                                saveImage={props.saveImage}
                                onClose={props.onClose}
                                setCurrentPopupContent={setCurrentPopupContent}
                                handleDeviceCapture={handleDeviceCapture}
                                setSelectorMode={setSelectorMode}
                                // profileData={userProfileData}
                                userFirstName={userFirstName}
                                userLastName={userLastName}
                            />
                        </>
                    }
                </div>

    const MediaTitleComp = () => (
            <i className="fa fa-headphones" aria-hidden="true"></i>
    );
    const MediaBGComp = () => (
        <i className="fa fa-user" aria-hidden="true"></i>
    );

    const componentTabs = [
        {title: "Device Settings", eventKey: 'media', icon: <MediaTitleComp />, component: <MediaDeviceComponent />},
        {title: "Background Effects", eventKey: 'background_img', icon: <MediaBGComp />, component: <MediaGrid />}
    ]


    // ReactDOM.createPortal(MediaDeviceComponent,document.getElementById("root_modal"))
    return ReactDOM.createPortal(
        <div
            id="deviceModal"
            className={`
            modal fade in audioVideoModal overlayDiv 
            ${selectorMode === 2 ? "capture_image_css" : "device_selector_css"}
        `}
            role="dialog"
            style={{display: "block", opacity: '1'}}>
            <div className="modal-dialog modal-xl">
                <div className="modal-header">
                    {props.allowClose &&
                        <button type="button" onClick={props.onClose} className={"close"}>
                            <RoundedCrossIcon />
                        </button>
                    }
                </div>
                <div className="modal-content">
                    <MediaDevicePopupTabs tabs = {componentTabs}/>
                </div>
            </div>
        </div>,
        document.getElementById("root_modal")
    )
}

export default MediaDevicePopup;
