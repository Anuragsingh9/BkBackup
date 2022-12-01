import React from 'react';
import _ from 'lodash';
import videoMeeting from '../../../VideoMeeting/VideoMeetingClass.js';
import EventActions from '../../../../redux/actions/newInterface/index.js';
import Helper from '../../../../Helper.js';
import socketManager from '../../../../socket/socketManager.js';
import EventAgent from '../../../../redux/actions/event/index.js';
import videoElementRepo from '../../../VideoMeeting/VideoElementRepository.js';
import {confirmAlert} from 'react-confirm-alert';
import i18n from 'i18next';


/**
 * @module ConversationUtils
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function Handles remove user
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} id Target user id.
 * @param {Function} cb Callback method to trigger on success
 * @param {Boolean} isDummy To indicate if target user is dummy or real
 * @returns {Function}
 */
export const removeUser = (id, cb = null, isDummy=false) => (dispacth, getState) => {
    const event_data = dispacth(getData('interfaceEventData', {}));
    const space_data = dispacth(getData('interfaceSpacesData'), {});

    confirmAlert({
        message: i18n.t("myBadgeBlock:Are you sure want to remove?"),
        confirmLabel: i18n.t("myBadgeBlock:Confirm"),
        cancelLabel: i18n.t("myBadgeBlock:Cancel"),
        buttons: [
            {
                label: i18n.t("myBadgeBlock:Yes"),
                onClick: () => {
                    const data = {
                        eventId: event_data.event_uuid,
                        targetUserId: id,
                        isDummyUser: isDummy ? 1 : 0,
                    };
                    if (cb) {
                        cb(false);
                    }
                    socketManager.emitEvent.REMOVED_USER(data);
                    Helper.globalAlert(i18n.t("myBadgeBlock:Remove user from conversation"), 'success')
                    if (_.has(space_data, ['current_joined_conversation', 'conversation_users'])
                        // conversation have two users only, 1 self, 2nd target user
                        && space_data.current_joined_conversation.conversation_users.length <= 2
                    ) {
                        dispacth(leaveConversation());
                    }
                }
            },
            {
                label: i18n.t("myBadgeBlock:No"),
                onClick: () => {
                    if (cb) {
                        cb(false);
                    }
                    return null
                }
            }
        ],

    })
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function provides the user data from the conversation user.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {UserBadge[]} conversation_users Conversation users data.
 * @param {String} userId User id to get the data
 * @returns {UserBadge}
 **/
export const getUserData = (conversation_users, userId) => {
    let data = {};
    !_.isEmpty(conversation_users) && conversation_users.filter((val) => {
        if (userId == val.user_id) {
            data = val;
        }
    })
    return data;

}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function initiates the conversation.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {ConversationData} conversation Conversation to start
 * @param {Object} selfSeat VideoTileState for self user
 * @param {Function} setSelfSeat Callback function to set self state.
 * @param {Function} provideVideoElementToUser Callback function to provide other users video tile.
 **/
export const StartConversation = (conversation, selfSeat, setSelfSeat, provideVideoElementToUser) => (dispatch) => {
    const elements = {
        selfVideoElement: document.getElementById("self-video"),
        selfAudioElement: document.getElementById('self-audio')
    }
    const meeting = conversation.meeting;
    const additional = {
        states: {
            selfSeat: {
                get: selfSeat,
                set: setSelfSeat
            }
        },
        handlers: {
            provideVideoElementToUser: provideVideoElementToUser,
            conversationErrorHandler: () => {
                console.error("error in conversation")
            },
        },
        devices: {
            audioInput: localStorage.getItem("user_audio"),
            videoInput: localStorage.getItem("user_video"),
            audioOutput: localStorage.getItem('user_audio_o'),
        },
        dispatch,
    }
    return videoMeeting.start(meeting, elements, additional);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function provides the current state of given key.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} key State object key name to search and return.
 * @returns {Object}
 **/
export const getData = (key, defaultData) => (dispatch, getState) => {
    return _.get(getState().NewInterface, key);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function handles api call for private conversation
 * ---------------------------------------------------------------------------------------------------------------------
 *
 *  @method
 **/
export const askToPrivateConversation = () => (dispatch, getState) => {
    const event_space = dispatch(getData('interfaceSpacesData'));
    // const {t} = useTranslation('myBadgeBlock')

    if (event_space.current_joined_conversation === null) {
        return null;
    }
    const {conversation_uuid} = event_space.current_joined_conversation;
    const formData = new FormData()
    formData.append('conversation_uuid', conversation_uuid);
    if (event_space.current_joined_conversation.is_conversation_private == 0) {
        formData.append('is_private', 1);
    } else {
        formData.append('is_private', 0);
    }

    try {
        dispatch(EventAgent.privateConversation(formData)).then((res) => {
            dispatch(privateConvSocketTrigger(conversation_uuid, res));

            Helper.globalAlert(res.data.data.is_conversation_private ? i18n.t("myBadgeBlock:Conversation Isolated") : i18n.t("myBadgeBlock:Broke Isolation successfully"), 'success');

            const data = {
                conversation_uuid: conversation_uuid,
                current_state: res.data.data.is_conversation_private
            }
            dispatch(EventActions.updatePrivateConversation(data));

        }).catch((err) => {
            Helper.globalAlert(err, "error");
        })


    } catch (err) {
        Helper.globalAlert(err, "error");
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function handles socket trigger for isolated/private conversation
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} conversation_uuid Conversation id to make private.
 * @param {Object} res Api response to handle.
 **/
export const privateConvSocketTrigger = (conversation_uuid, res) => (dispatch) => {
    const event_space = dispatch(getData('interfaceSpacesData', {}));
    const event_data = dispatch(getData('interfaceEventData', {}));
    const event_badge = dispatch(getData('interfaceBadgeData', {}));


    socketManager.emitEvent.PRIVATE_CONVERSATION({
        namespace: Helper.getNameSpace(),
        spaceId: event_space.current_joined_space.space_uuid,
        eventId: event_data.event_uuid,
        conversationId: conversation_uuid,
        is_private: res.data.data.is_conversation_private,
        senderId: event_badge.user_id,
    });
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function returns whether space host is present in the conversation or not.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {ConversationData} current_joined_conversation Current conversation.
 * @param {UserBadge} spaceHostData Space hosts data.
 * @returns {Boolean}
 **/
export const checkSpaceHost = (current_joined_conversation, spaceHostData) => {

    if (_.has(current_joined_conversation, ['conversation_users']) && !_.isEmpty(current_joined_conversation.conversation_users) && !_.isEmpty(spaceHostData)) {

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

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function checks whether the user is dummy user or not.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {UserBadge[]} conversation_users Conversation users data.
 * @param {String} id User id to check if user is dummy or real
 * @returns {Boolean}
 **/
export const checkDummyUser = (conversation_users, id) => {

    return !_.isEmpty(conversation_users) && conversation_users.filter((item) => {

        if (item.user_id == id && item.is_dummy == 1) {
            return item
        }
    });

}


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function Handles api call of leave the conversation
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Function} setButtonState Set button state callback.
 * @param {Function} cb Callback to execute on promise complete
 * @returns {Promise}
 */
export const leaveConversation = (setButtonState = null, cb = null) => (dispatch, getState) => {
    const event_space = _.get(getState().NewInterface, "interfaceSpacesData");
    // const {t} = useTranslation('notification')

    if (event_space.current_joined_conversation === null) {
        return null;
    }

    if (event_space.current_joined_conversation && event_space.current_joined_conversation.is_conversation_private == 1 && event_space.current_joined_conversation.conversation_users.length == 2) {
        const data = {
            conversation_uuid: event_space.current_joined_conversation.conversation_uuid,
            current_state: 0
        }
        dispatch(EventActions.updatePrivateConversation(data));
    }

    dispatch(EventActions.callOff());

    // setButtonState(true);
    const formData = new FormData()
    formData.append('conversation_uuid', event_space.current_joined_conversation.conversation_uuid)
    formData.append('_method', 'DELETE')
    try {
        dispatch(EventAgent.leaveConversation(formData))
            .then((res) => {
                if (cb) {
                    cb();
                }
                // setButtonState(false);
                if (res.status) {
                    Helper.globalAlert(i18n.t("notification:flash msg rec leave conversation"), "success");
                    if (res.data.data) {
                        dispatch(EventActions.deleteConversation(event_space.current_joined_conversation))
                        dispatch(triggerLeaveSocket(res, event_space.current_joined_conversation.conversation_uuid));
                    }
                }
            })
            .catch((err) => {
                if (cb) {
                    cb();
                }
                Helper.globalAlert(err, "error");
            });
    } catch (err) {
        Helper.globalAlert(err, "error");

    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function Handles socket trigger for leave conversation.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} res Api response object
 * @param {String} id User id to leave from conversation
 **/
const triggerLeaveSocket = (res, id) => (dispacth, getState) => {

    const authId = _.get(getState().NewInterface, 'interfaceAuth');
    const data = {
        conversationId: id,
        userId: authId.user_id,
        type: res.data.data === true ? 'delete' : 'remove',
    }
    socketManager.emitEvent.CONVERSATION_LEAVE(data);
    videoElementRepo.resetSeats();
    videoMeeting.stopVideo();
}


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function Handles change of mute state
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} muteValue New state of mute for conversation of chime
 */
export const handleMute = (muteValue) => {
    videoMeeting.toggleMute(muteValue);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function Handles media device permissions
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Function} a Callback to set current device in use.
 * @param {Function} b Callback to set all available devices.
 **/
export const getMediaPermissions = (a, b, errorHandler = null) => {
    // navigator.getUserMedia = (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.mediaDevices.getUserMedia);
    getMediaDevices(b, errorHandler);
    // navigator.mediaDevices.getUserMedia({ audio: true, video: true }).then((s)=>{mediaDeviceAccepted(s,a,b)}, mediaDeviceRejected);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description this method will check for the permission of the device listing
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Function} onSuccess Callback method to execute on success of device fetch
 * @param {Function} onFail Callback method to execute on fail of device fetch
 */
export const checkDevicePermission = (onSuccess = null, onFail = null) => {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        const navigatorConstraints = {audio: true, video: true};
        // getting access for device
        navigator.mediaDevices.getUserMedia(navigatorConstraints).then(onSuccess).catch(onFail);
    }
}


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function handles accept of the media permissions.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Function} stream Stream fetched from navigator
 * @param {Function} a Callback to set current device in use.
 * @param {Function} b Callback to set all available devices.
 **/
const mediaDeviceAccepted = (stream, a, b) => {
    stream.getVideoTracks().forEach(function (track) {
        track.stop();
    });
    // a(true);
    getMediaDevices(b);
}

/**
 * @deprecated
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function handles reject of the media permissions.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 **/
const mediaDeviceRejected = e => {
    // Helper.globalAlert(i18n.t("myBadgeBlock:Devices Not Found"),"error");
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function handles accept of the media permissions.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Function} setAvailableMediaDevice Callback to set all available devices in parent component state
 * @param {Function} errorHandler Handler method to handle the device fetch failure
 */
const getMediaDevices = (setAvailableMediaDevice, errorHandler = null) => {
    if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
        return;
    }
    navigator.mediaDevices.enumerateDevices()
        .then(function (devices) {
            let audioDevices = [];
            let audioOutputDevices = [];
            let videoDevices = [];
            let defaultAudioInput = null;
            let defaultVideoInput = null;
            let defaultAudioOutput = null;
            devices.forEach(function (device) {
                if (device.kind === "audioinput" && device.deviceId !== 'communications') {
                    if (device.deviceId === 'default') {
                        defaultAudioInput = device;
                    } else {
                        audioDevices.push(device);
                    }
                } else if (device.kind === "videoinput" && device.deviceId !== 'communications') {
                    if (device.deviceId === 'default') {
                        defaultVideoInput = device;
                    } else {
                        videoDevices.push(device);
                    }
                } else if (device.kind === 'audiooutput' && device.deviceId !== 'communications') {
                    if (device.deviceId === 'default') {
                        defaultAudioOutput = device;
                    } else {
                        audioOutputDevices.push(device);
                    }
                }
            });

            if (!_.isEmpty(audioDevices) && !_.isEmpty(videoDevices)) {
                if (_.isEmpty(audioOutputDevices)) {
                    audioOutputDevices.push({
                        deviceId: 'custom',
                        groupId: "",
                        kind: "audiooutput",
                        label: "Same As System",
                    })
                    defaultAudioOutput = {
                        deviceId: "default",
                        groupId: "",
                        kind: "audiooutput",
                        label: "Default - Same As System",
                    }
                }

                if (!defaultVideoInput && videoDevices.length) {
                    defaultVideoInput = videoDevices[0];
                }

                const allDevices = {
                    audioDevices: audioDevices,
                    videoDevices: videoDevices,
                    audioOutputDevices: audioOutputDevices,
                    selectedAudioDevice: localStorage.getItem("user_audio"),
                    selectedVideoDevice: localStorage.getItem("user_video"),
                    selectedAudioOutput: localStorage.getItem("user_audio_o"),
                    defaultAudioInput,
                    defaultAudioOutput,
                    defaultVideoInput,
                };
                setAvailableMediaDevice(allDevices);
            } else {
                Helper.globalAlert(i18n.t("myBadgeBlock:Devices Not Found"), "error");
            }
        })
        .catch((error) => {
            if (errorHandler !== null) {
                errorHandler(error);
            }
        });
}


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function handles capture image stop/show preview from selected device.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} imageCaptured Base64 encoded string image
 * @param {Function} onImageProcess Callback to execute on successful image process
 **/
export const captureImageHandlerBlob = (imageCaptured, onImageProcess) => {
    const video = document.querySelector('#selfPreviewCompo');
    const canvas = document.querySelector('#grabFrameCanvas');
    const context = canvas.getContext('2d');
    return new Promise((resolve, reject) => {
        const {videoWidth, videoHeight} = video;
        const crop = videoWidth / 2 - 150;
        canvas.width = videoWidth;
        canvas.height = videoHeight;

        try {
            context.drawImage(video, 0, 0, videoWidth, videoHeight);
            canvas.toBlob(resolve, 'image/png');

            //BLOB TON IMAGE CONVERT
            canvas.toBlob(function (blob) {
                let reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function () {
                    const base64data = reader.result;
                    onImageProcess(blob, base64data);
                }
                const img = document.querySelector('img#grabFrameCanvas');
                if (img) {
                    img.style.backgroundImage = `url(${URL.createObjectURL(blob)})`
                    // const trackDevice = imageCaptured?.track;
                    if ('ImageCapture' in window) {
                        const trackDevice = imageCaptured?.track;
                        trackDevice.stop()
                    } else {
                        imageCaptured[0].stop();
                    }
                }
                // trackDevice.stop()
            }, 'image/png');

        } catch (error) {
            reject(error);
        }
        // });
    });

}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To draw the image on canvas
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {HTMLCanvasElement} canvas Canvas element to manipulate on page
 * @param {HTMLImageElement} img image file object to process
 */
function drawCanvas(canvas, img) {
    canvas.width = getComputedStyle(canvas).width.split('px')[0];
    canvas.height = getComputedStyle(canvas).height.split('px')[0];
    let ratio = Math.max(canvas.width / img.width, canvas.height / img.height);
    let x = (canvas.width - img.width * ratio) / 2;
    let y = (canvas.height - img.height * ratio) / 2;
    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    canvas.getContext('2d').drawImage(img, 0, 0, img.width, img.height,
        x, y, img.width * ratio, img.height * ratio);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To show the device preview for video camera on media device popup
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} deviceId Device ID of which the preview will be displayed on canvas
 */
export const showDevicePreview = (deviceId) => {
    var constraints = {
        video: {
            deviceId: {exact: deviceId},
            width: {ideal: 1920},
            height: {ideal: 1080}
        },
    };

    const videoEle = document.querySelector('#selfPreviewCompo');
    if (!videoEle) return;
    videoEle.srcObject = null;

    navigator.mediaDevices.getUserMedia(constraints).then(function (mediaStream) {
        // setNoPreviewDiv(true)
        if (mediaStream) {
            videoEle.srcObject = mediaStream;
            videoEle.onloadedmetadata = function (e) {
                videoEle.play();
            };
        }

    }).catch((error) => console.error("dddddddddddddddddddddddddddddddddddddddd error preview method", error));
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To prepare the navigator object for fetching the device list with permission asking
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} deviceId Device ID of which navigator preparing the stream
 * @returns {Promise<MediaStream>}
 */
const prepareNagivatorForDeviceId = (deviceId) => {
    var constraints = {
        video: {
            deviceId: deviceId,
            width: {ideal: 1920},
            height: {ideal: 1080}
        },
    };
    navigator.getUserMedia = (navigator.getUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia ||
        navigator.msGetUserMedia);


    return navigator.mediaDevices.getUserMedia(constraints);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To capture the image from the camera
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} deviceId Device ID to capture the image from
 * @param {Function} onImageCapture Handler method for capturing the image
 */
export const captureImage = (deviceId, onImageCapture) => {
    prepareNagivatorForDeviceId(deviceId).then(function (mediaStream) {
        if (mediaStream) {
            let selectedDeviceTrack = mediaStream.getTracks()[0];
            if ('ImageCapture' in window) {
                onImageCapture(new ImageCapture(selectedDeviceTrack));
            } else {
                onImageCapture(mediaStream.getTracks());
            }
        }
    }).catch((error) => console.error("error preview for image capture method", error));

}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To show the selected device preview in media device popup
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} selectedId Selected device id
 * @param {Function} setNoPreviewDiv Callback to set the preview method
 * @param {Function} setImageCaptured Handler to set the image captured in local state
 */
export const showSelectedDevicePreview = (selectedId, setNoPreviewDiv, setImageCaptured) => {
    var constraints = {
        video: {
            deviceId: selectedId,
            width: {ideal: 1920},
            height: {ideal: 1080}
        },
    };
    navigator.getUserMedia = (navigator.getUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia ||
        navigator.msGetUserMedia);


    navigator.mediaDevices.getUserMedia(constraints).then(function (mediaStream) {
        // setNoPreviewDiv(true)
        if (mediaStream) {
            setNoPreviewDiv(false);
            const videoEle = document.querySelector('#selfPreviewCompo');
            videoEle.srcObject = null;
            // let videoTracks = mediaStream.getTracks();
            let videoTracks = mediaStream.getTracks();
            let selectedDeviceTrack = mediaStream.getTracks()[0];
            videoEle.srcObject = mediaStream;
            videoEle.onloadedmetadata = function (e) {
                videoEle.play();
            };
            // videoEle.play();

            //to capture
            if ('ImageCapture' in window) {
                setImageCaptured(new ImageCapture(selectedDeviceTrack));
            } else {
                setImageCaptured(mediaStream.getTracks());
            }

        }

    }).catch((error) => console.error("error preview method", error));


}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To stop the camera preview on video DOM to release the camera
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} divId Id of DOM from where the video needs to be stopped
 */
export const stopStreamedVideo = (divId = 'selfPreviewCompo') => {
    const video = document.querySelector(`#${divId}`);
    if (video) {
        const stream = video.srcObject;
        const tracks = stream?.getVideoTracks();
        if (tracks) {
            tracks[0].stop();
            video.srcObject = null;
        }
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To stop the audio occupancy with selected audio device
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {MediaStream} stream media stream object
 */
export const stopAudioStream = (stream) => {
    // if(stream) {
    //     stream.getAudioTracks().forEach((track) => {
    //         track.stop();
    //     })
    // }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To run the device test to check if devices stored in local are still connected or not
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Function} onSuccess Callback to execute on success
 * @param {Function} onFail Callback to execute on fail
 */
export const runDeviceTest = (onSuccess = null, onFail = null) => {
    let audioInput = localStorage.getItem("user_audio");
    let videoInput = localStorage.getItem("user_video");
    // let audioOutput = localStorage.getItem('user_audio_o');

    let test = true;

    // if any of device is missing it will return false;
    if (!(audioInput && videoInput)) { // && audioOutput
        test = false;
    }


    const isDeviceConnected = (devices, selectedDevice) => {
        let flag = false;
        devices.forEach(device => {
            if (device.deviceId === selectedDevice) {
                flag = true;
            }
        })
        return flag;
    }

    getMediaDevices((devices) => {
        if (!(isDeviceConnected(devices.audioDevices, audioInput) &&
            isDeviceConnected(devices.videoDevices, videoInput)
            // isDeviceConnected(devices.audioOutputDevices, audioOutput)
        )) {
            test = false;
        }
        if (test) {
            if (onSuccess !== null) {
                onSuccess();
            }
        } else {
            if (onFail !== null) {
                onFail();
            }
        }
    })

}







