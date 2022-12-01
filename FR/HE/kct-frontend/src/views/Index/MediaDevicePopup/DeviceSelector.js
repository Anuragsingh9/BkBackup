import React from "react";
import _ from 'lodash';
import "./MediaDevicePopup.css";
import {useTranslation} from "react-i18next";
import {stopStreamedVideo} from "../../NewInterFace/Conversation/Utils/Conversation";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will render a select component to render all available/connected
 * devices(camera/mic/speaker).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} type Type of the device e.g. video, audio, audio output
 * @param {MediaDeviceInfo[]} data List of the available devices for current type
 * @param {String} value Current selected device
 * @param {String} name Name of the field name of device by type
 * @param {JSX} icon Icon of device type
 * @param {Object} props Props passed from parent component
 * @param {Function} props.onDeviceChange Handler method when the device is selected from the list
 * @returns {JSX.Element}
 * @constructor
 */
function DeviceSelector({type, data, value, name, icon, ...props}) {
    const {t} = useTranslation('popup');

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will handle the event when the device is selected by user.This will stop the camera as well
     * once the device is changed or selected
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const handleInputChange = (e) => {
        if (e.target.value != "") {
            props.onDeviceChange(e);
            if (e.target.name !== "audioSelect") {
                stopStreamedVideo();
            }
        }
    }

    return (
        <div className="form-group">

            <div className="select-cover custom_select">
                {icon}
                <span className="fa fa-chevron-down site-color"></span>
                <select
                    className="form-control crossBrowser_select"
                    name={name}
                    value={value}
                    onChange={handleInputChange}
                    required
                >
                    <option value={""}>{t("Select")} {type} {t("Device")} </option>
                    {!_.isEmpty(data) && data.map((device, key) => {
                        return (
                            <>
                                {
                                    device && device.deviceId &&
                                    <option value={device.deviceId}>
                                        {device.label ? device.label : `${type} Device ${key + 1}`}
                                    </option>
                                }
                            </>
                        )
                    })
                    }
                </select>
            </div>
        </div>
    )
}

export default DeviceSelector;
