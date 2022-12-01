import React, { useEffect } from "react";
import "./MediaDevicePopup.css";
import _ from 'lodash';
import { checkDevicePermission, showDevicePreview } from "../../NewInterFace/Conversation/Utils/Conversation";
import Helper from "../../../Helper";
import Constants from "../../../Constants";
import { useTranslation } from "react-i18next";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render preview of selected camera device from the list ao that user can
 * check their appearance from current selected device.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {String} props.currentVideoDevice Current selected video device id
 * @param {String} props.deviceError To show the error message during fetching device access
 * @param {Number} props.mode To indicate the mode of popup
 * @param {Function} props.onNavigatorPermission Handler when the permission for devices list is granted
 * @param {Function} props.setDeviceError Handler when the permission for the device list is denied
 * @param {Function} props.setVideoPreviewMode To update the popup mode
 * @returns {JSX.Element}
 * @constructor
 */
function VideoDevicePreview(props) {
    const { t } = useTranslation("mediaDevicePopup");
    useEffect(() => {
        if (props.currentVideoDevice) {
            setDevicePreview(props.currentVideoDevice);
        }
    }, [props.currentVideoDevice]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will take id of selected camera device and pass it to 'testDeviceForOccupy' function
     * to make sure that this device is not occupied by different application.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} deviceId Id of the device to show the preview
     */
    const setDevicePreview = (deviceId) => {
        testDeviceForOccupy(deviceId);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will take device id(from parameter) and check device occupancy and if the device is
     * occupied then it will  show an error on interface other wise it will show the live preview form current
     * selected camera device.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} deviceId Id of the device to test for occupy
     */
    const testDeviceForOccupy = (deviceId) => {
        navigator.mediaDevices.getUserMedia({ video: { deviceId: deviceId } }).then(() => {
            props.setVideoPreviewMode(Constants.mediaDevicePop.PREVIEW_MODE.PREVIEW)
            showDevicePreview(props.currentVideoDevice);
        }).catch(error => {
            props.setVideoPreviewMode(Constants.mediaDevicePop.PREVIEW_MODE.DEVICE_OCCUPIED);
        });

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will check the permissions(allow from the browser) for the select device.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const getMediaPermission = () => {
        checkDevicePermission(() => {
            props.onNavigatorPermission();
        }, (error) => {
            props.setDeviceError(error.message);
        })
    }

    return <>
        <div className="body_preview_wrap">
            {
                props.mode === Constants.mediaDevicePop.PREVIEW_MODE.LOADING &&
                <div className="noPreviewDiv">
                    <Helper.pageLoading />
                </div>
            }
            {
                props.mode === Constants.mediaDevicePop.PREVIEW_MODE.PERMISSION_ISSUE &&
                <div className="noPreviewDiv">
                    <p>
                        {props.deviceError ?
                            <>
                                {props.deviceError}
                                <br />
                            </>
                            :
                            <p>
                                It seems your browser is
                                <br />blocking access to webcam
                                <br />
                            </p>
                        }
                        <span onClick={getMediaPermission} className="linkTxt">Click Here</span>&nbsp;
                        to allow access
                    </p>
                </div>
            }
            {
                props.mode === Constants.mediaDevicePop.PREVIEW_MODE.DEVICE_OCCUPIED &&
                <div className="noPreviewDiv">
                    <p>
                        Selected Camera Occupied by another application
                    </p>
                </div>

            }
            <>
                {!_.isEmpty(props.currentVideoDevice) ?
                    <>
                        <video
                            className={`selfPreview ${props.mode === Constants.mediaDevicePop.PREVIEW_MODE.PREVIEW ? '' : 'hidden'}`}
                            id="selfPreviewCompo"
                            // style={{
                            //     height: "auto",
                            //     width: "100%",
                            //     maxWidth: "350px"
                            // }}
                            autoPlay
                        />
                        <canvas id="grabFrameCanvas" className="hide__canvas" />
                    </>
                    :
                    <div
                        className={
                            `noPreviewDiv 
                            ${props.mode === Constants.mediaDevicePop.PREVIEW_MODE.PREVIEW
                                ? ''
                                : 'hidden'}`
                        }>
                        {t("Choose Camera")}
                    </div>
                }
            </>
        </div>
    </>
}

export default VideoDevicePreview;
