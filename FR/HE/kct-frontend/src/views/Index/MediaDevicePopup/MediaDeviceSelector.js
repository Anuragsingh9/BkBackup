import React, {useEffect, useState} from "react";
import "./MediaDevicePopup.css";
import {
    captureImage,
    captureImageHandlerBlob,
    checkDevicePermission,
    getMediaPermissions,
    stopStreamedVideo
} from "../../NewInterFace/Conversation/Utils/Conversation";
import Svg from "../../../Svg";
import VideoDevicePreview from "./VideoDevicePreview";
import CaptureButton from "./CaptureButton";
import DeviceSelector from "./DeviceSelector";
import Constants from "../../../Constants";
import {connect} from "react-redux";
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import _ from 'lodash';
import MicInputBar from "../../NewInterFace/Common/MicInputBar";
import {useTranslation} from "react-i18next";
import eventActions from "../../../redux/actions/eventActions";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used to select all connected/available devices(camera/speaker/mic) for conversation.
 * In this component user can see all available/connected devices for camera,mic,speaker with current selected device
 * preview(image and sound bar).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.onSubmit Handler method for device selection mode
 * @param {Function} props.onSubmit Handler method for device selection mode
 * @param {Number} props.mode To indicate the mode of popup like device input mode or capture image mode
 * @param {String} props.test_audio To indicate the mode of popup like device input mode or capture image mode
 * @param {UserBadge} props.event_badge User badge details
 * @param {InterfaceSpaceData} props.spaces_data Spaces data including conversations from redux store
 * @param {EventData} props.event_data Current event data
 * @param {Function} props.addLog Dispatcher to add the logs for device list
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
function MediaDeviceSelector(props) {
    const {t} = useTranslation("mediaDevicePopup");
    const [captureBtnLabel, setCaptureBtnLabel] = useState('');

    const [currentVideoDevice, setCurrentVideoDevice] = useState('');
    const [currentAudioDevice, setCurrentAudioDevice] = useState('');
    const [currentAudioOutput, setCurrentAudioOutput] = useState('');

    const [devicesList, setDevicesList] = useState({});
    const [videoDevicesList, setVideoDevicesList] = useState([]);
    const [audioDevicesList, setAudioDevicesList] = useState([]);
    const [audioOutputList, setAudioOutputList] = useState([]);

    const [audio, setAudio] = useState(null);
    const [testAudioClickable, setTestAudioClickable] = useState(false);

    const [showAudioList, setShowAudioList] = useState(false);
    const [deviceError, setDeviceError] = useState(null);
    const [videoPreviewMode, setVideoPreviewMode] = useState(Constants.mediaDevicePop.PREVIEW_MODE.LOADING);

    const {user_avatar} = props.event_badge;

    useEffect(() => {
        if (user_avatar == null) {
            setCaptureBtnLabel(props.mode === Constants.mediaDevicePop.MODE_DEVICE_SET
                ? 'Submit & Next'
                : 'Capture'
            );
        } else {
            setCaptureBtnLabel(props.mode === Constants.mediaDevicePop.MODE_DEVICE_SET
                ? 'Submit'
                : 'Capture'
            );
        }
        setShowAudioList(props.mode === Constants.mediaDevicePop.MODE_DEVICE_SET);
    }, [props.mode, props?.userProfileData]);

    useEffect(() => {
        return () => {
            if (audio) {
                audio.pause();
            }
        }
    }, [audio]);

    useEffect(() => {
        setTestAudioClickable(!_.isEmpty(currentAudioOutput));
    }, [currentAudioOutput, currentAudioDevice])

    useEffect(() => {
        initDeviceList();
        return () => {
            stopStreamedVideo();
        }
    }, []);

    useEffect(() => {
        if (!_.isEmpty(devicesList)) {
            setAudioDevicesList(devicesList.audioDevices);
            setVideoDevicesList(devicesList.videoDevices);
            setAudioOutputList(devicesList.audioOutputDevices);

            let audioOutputSelect = getSelectedDevice(
                'user_audio_o',
                devicesList.audioOutputDevices,
                devicesList.defaultAudioOutput
            );
            let audioInputSelect = getSelectedDevice(
                'user_audio',
                devicesList.audioDevices,
                devicesList.defaultAudioInput
            );
            let videoInputSelect = getSelectedDevice(
                'user_video',
                devicesList.videoDevices,
                devicesList.defaultVideoInput
            )

            audioOutputSelect && setCurrentAudioOutput(audioOutputSelect.deviceId);
            audioInputSelect && setCurrentAudioDevice(audioInputSelect.deviceId);
            videoInputSelect && setCurrentVideoDevice(videoInputSelect.deviceId);
        }
    }, [devicesList]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will get the device to be shown as selected in the drop down
     * 1. first it will check for local storage if any device already selected
     * 2. then it will look for default device from list
     * 3. then it will check if there are devices then it will select the first device as selected
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} localStorageKey Key name of local storage to fetch the device id
     * @param {MediaDeviceInfo[]} devices Available media devices list
     * @param {String} defaultDevice Device id of default device to system
     * @returns {String}
     */
    const getSelectedDevice = (localStorageKey, devices, defaultDevice) => {
        let localStorageValue = localStorage.getItem(localStorageKey);
        let selectedDevice = null;
        let localStorageDevice = isDevicePresent(localStorageValue, devices);
        if (localStorageValue && localStorageDevice) {
            // the local storage device is still connected so showing it as selected
            selectedDevice = localStorageDevice;
        } else {
            defaultDevice = getDefaultDevice(devices, defaultDevice);
            if (defaultDevice) {
                selectedDevice = defaultDevice;
            } else if (devices.length) {
                selectedDevice = devices[0];
            }
        }
        return selectedDevice;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will return the default device object from available list
     * as the device default device have id as default and for further actual device id is required so finding default
     * device by the label and returning that device object
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {MediaDeviceInfo[]} deviceList Available device list
     * @param {String} defaultDevice Default device id
     * @returns {MediaDeviceInfo}
     */
    const getDefaultDevice = (deviceList, defaultDevice) => {
        let defaultFromList = defaultDevice && deviceList.find((device) => {
            // as default devices are labels as below so removing that and matching it list device label
            return device.label === defaultDevice.label.replace('Default - ', '');
        });

        if (defaultFromList && defaultFromList.deviceId) {
            // device is found form list
            return defaultFromList;
        }
        return null;
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if the provided device present in device list or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {MediaDeviceInfo} device Target device to check if its connected or not
     * @param {MediaDeviceInfo[]} devices Latest devices list from system
     * @returns {Boolean}
     */
    const isDevicePresent = (device, devices) => {
        return device && devices.find(d => d.deviceId === device);
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will check for the permission of devices and if possible fetch the list else show
     * the error.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const initDeviceList = () => {
        // checking for browser permissions
        checkDevicePermission(fetchDeviceList, handlePermissionError);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handler when permission of devices is not found .It will check if its blocked by device
     * If blocked show the message else fetch the list
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Error} error Error object to handle the error with permission issue
     */
    const handlePermissionError = (error) => {
        if (error.name === 'NotAllowedError') {
            // device access permission is denied
            setVideoPreviewMode(Constants.mediaDevicePop.PREVIEW_MODE.PERMISSION_ISSUE);
        } else {
            // user has permission but something else has caused device check
            fetchDeviceList();
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To fetch the available devices list and set it to state variable deviceList
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const fetchDeviceList = () => {
        getMediaPermissions(
            null, setDevicesList,
            (error) => console.error('media device handler', error)
        );
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle when device is changed for video
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const onVideoDeviceChange = (e) => {
        setCurrentVideoDevice(e.target.value);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle when the selected audio output device is changed
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const onAudioOutputChange = (e) => {
        // when the device is changed stop the audio and reset so user can play in new device directly
        if (audio) {
            audio.pause();
            setAudio(null);
        }
        setCurrentAudioOutput(e.target.value);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle when the selected audio input device is changed
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const onAudioDeviceChange = (e) => {
        setCurrentAudioDevice(e.target.value);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will play the audio to the selected speaker
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const playAudio = (e) => {
        e.preventDefault();
        if (audio) {
            // if the audio is already being playing then stop it
            audio.pause();
            setAudio(null);
        } else {
            // audio is not currently playing,
            let audioTemp = new Audio(props.test_audio);
            setAudio(audioTemp);
            // as "Same as System" is just to show user when there is no speaker found so and there will no device id
            if (currentAudioOutput && currentAudioOutput !== 'Same As System' && currentAudioOutput !== 'default' && currentAudioOutput !== 'custom') {
                // selected speaker is found playing in that speaker
                audioTemp.setSinkId(currentAudioOutput);
            }
            audioTemp.play().then(() => {}).catch(() => {});
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the image capture button
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {File} imageData Image file data captured from camera
     */
    const onImageCapture = (imageData) => {
        captureImageHandlerBlob(
            imageData,
            (blob, imageBase64) => {
                stopStreamedVideo();
                props.onCapture(imageBase64);
            }
        ).then(() => {
        });
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle when the selected audio device is changed
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const captureImageHandler = (e) => {
        e.preventDefault();
        props.addLog({
            log_type: 2,
            current_selected_device: {
                audioInput: currentAudioDevice,
                audioOutput: currentAudioOutput,
                videoInput: currentVideoDevice,
            },
            available_devices: devicesList,
            event_uuid: props.event_data.event_uuid,
        })
        if (props.mode === Constants.mediaDevicePop.MODE_CAPTURE_AND_PREVIEW || user_avatar == null) {
            captureImage(currentVideoDevice, onImageCapture);
        } else {
            stopStreamedVideo();
        }
        props.onSubmit(
            currentAudioDevice,
            currentVideoDevice,
            currentAudioOutput,
            // if mode is capture send true so popup keep opened
            props.mode === Constants.mediaDevicePop.MODE_CAPTURE_AND_PREVIEW || user_avatar == null
        );

    }

    return (
        <div className={`${showAudioList ? "deviceSelectorLayout" : "imageCaptureLayout"}`}>
            <VideoDevicePreview
                mode={videoPreviewMode}
                onNavigatorPermission={initDeviceList}
                deviceError={deviceError}
                setDeviceError={setDeviceError}
                currentVideoDevice={currentVideoDevice}
                setVideoPreviewMode={setVideoPreviewMode}
            />

            <form onSubmit={captureImageHandler}>

                <DeviceSelector
                    name={"videoSelect"}
                    type={"Video"}
                    value={currentVideoDevice}
                    data={videoDevicesList}
                    onDeviceChange={onVideoDeviceChange}
                    icon={<div className="svgicon dummy-user"
                               dangerouslySetInnerHTML={{__html: Svg.ICON.video_Icon}} />}
                />

                {showAudioList &&
                <>
                    <div className="form-group">
                        <DeviceSelector
                            name={"audioSelect"}
                            type={"Audio"}
                            value={currentAudioOutput}
                            data={audioOutputList}
                            onDeviceChange={onAudioOutputChange}
                            icon={<div className="svgicon dummy-user"
                                       dangerouslySetInnerHTML={{__html: Svg.ICON.speaker_2}}></div>}
                        />
                    </div>
                    <Row className="justify-content-md-center custom_layout_specing test_audioWrap">
                        <Col xs lg="2">
                            {audio ?
                                <div className="svgicon dummy-user"
                                     dangerouslySetInnerHTML={{__html: Svg.ICON.test_pause_icon}}
                                ></div>
                                :
                                <div className="svgicon dummy-user"
                                     dangerouslySetInnerHTML={{__html: Svg.ICON.test_play_icon}}
                                ></div>
                            }
                        </Col>
                        <Col lg="10" className='progressWrap'>
                            <button disabled={!testAudioClickable} onClick={playAudio} className="testAudioBtn">
                                {t("Test Audio")}
                                {/* {audio ? 'Pause' : 'Play'} */}
                            </button>
                        </Col>
                    </Row>

                </>
                }

                {showAudioList &&
                <>
                    <div className="form-group">
                        <DeviceSelector
                            name={"audioSelect"}
                            type={"Audio"}
                            value={currentAudioDevice}
                            data={audioDevicesList}
                            onDeviceChange={onAudioDeviceChange}
                            icon={<div className="svgicon dummy-user"
                                       dangerouslySetInnerHTML={{__html: Svg.ICON.mic_Icon}}></div>}
                        />
                    </div>
                    <MicInputBar deviceId={currentAudioDevice} />
                </>
                }
                <CaptureButton
                    title={captureBtnLabel || "Submit"}
                    disabled={false}
                />
            </form>
        </div>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        addLog: (data) => dispatch(eventActions.Event.addLog(data)),
    }

}

const mapStateToProps = (state) => {

    return {
        test_audio: state.NewInterface.testAudioUrl,
        event_badge: state.NewInterface.interfaceBadgeData,
        spaces_data: state.NewInterface.interfaceSpacesData,
        event_data: state.NewInterface.interfaceEventData,
    };
};


export default connect(mapStateToProps, mapDispatchToProps)(MediaDeviceSelector);