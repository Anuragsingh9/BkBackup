import React from 'react';
import {
    BackgroundBlurVideoFrameProcessor,
    BackgroundReplacementVideoFrameProcessor,
    ConsoleLogger,
    DefaultDeviceController,
    DefaultMeetingSession,
    DefaultVideoTransformDevice,
    LogLevel,
    MeetingSessionConfiguration
} from 'amazon-chime-sdk-js';
import Helper from '../../Helper.js';
import _ from 'lodash';
import videoElementRepo from "./VideoElementRepository.js";
import VideoElementRepository from "./VideoElementRepository";
import {KeepContact as KCT} from "../../redux/types";
import Constants from "../../Constants";


let meetingSession = null;
let logger = null;
let deviceController = null;
let audioOutputDevices = null;

let currentMeetingId = null;

let isConversationInitialized = false;

let audioInputElement = null;
let attendeeVolumeSubscribed = {};
let selectedVideoDeviceId = null;

let backgroundReplacementProcessor = null;
let backgroundBlurProcessor = null;

/**
 * @module VideoMeeting
 */

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will initialize the component and inject the needed object to respective variables
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} meetingResponse Meeting response from chime
 * @param {Object} attendeeResponse Meeting attendee response from chime api
 */
const initializeComponent = (meetingResponse, attendeeResponse) => {
    logger = new ConsoleLogger('MyLogger', LogLevel.OFF)
    deviceController = new DefaultDeviceController(logger)

    // AWS CONFIGURATION
    const configuration = new MeetingSessionConfiguration(meetingResponse, attendeeResponse);
    meetingSession = new DefaultMeetingSession(
        configuration,
        logger,
        deviceController
    );
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description The main method which is responsible for
 * starting the video conversation
 * allocate self video to video element
 * allocate other users to respective other video tags via web socket
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} meeting Meeting response from chime
 * @param {Object} elements audio and video elements
 * @param {Object} additional The additional value passed which contains handlers
 * @returns {Promise<void>}
 */
const start = async (meeting, elements, additional) => {
    try {
        let meetingResponse = meeting && _.has(meeting, ['meeting_response', 'Meeting', 'MeetingId']) && meeting.meeting_response.Meeting.MeetingId ? meeting.meeting_response : null;
        let attendeeResponse = meeting.attendee_response;
        let selfVideoElement = elements.selfVideoElement;
        let audioElement = elements.selfAudioElement;
        let awsMeetingId = meetingResponse ? meetingResponse.Meeting.MeetingId : null;

        if (meetingResponse && awsMeetingId && awsMeetingId !== currentMeetingId) {
            currentMeetingId = awsMeetingId;
            isConversationInitialized = false;
            audioInputElement = audioElement;

            initializeComponent(meetingResponse, attendeeResponse);
            startObserver(selfVideoElement, additional);

            if (additional.devices.videoInput && additional.devices.audioInput) {
                await assignAudioVideoDevice(additional.devices.audioInput, additional.devices.videoInput);
            }
        }
    } catch (err) {
        Helper.globalAlert(err, 'error');
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To toggle the mute and unmute of current meeting
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Boolean} isMute New mute state to apply on chime video
 * @returns {Boolean}
 */
const toggleMute = (isMute) => {
    if (meetingSession !== null) {
        if (isMute === 1) {
            meetingSession.audioVideo.realtimeMuteLocalAudio();
        } else {
            return meetingSession.audioVideo.realtimeUnmuteLocalAudio();
        }
    }
}


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will choose the default devices set for system for audio input/output video input
 * to meeting Session
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @returns {Promise<void>}
 */
const assignAudioVideoDevice = async (audioInputDeviceId, videoInputDeviceId, audioOutputId) => {
    try {
        if (!meetingSession || !audioInputDeviceId || !videoInputDeviceId) {
            return;
        }

        audioOutputDevices = await meetingSession.audioVideo.listAudioOutputDevices();
        console.log('dddddddddddd updating selected video device');
        selectedVideoDeviceId = videoInputDeviceId;

        await meetingSession.audioVideo.startAudioInput(audioInputDeviceId);
        await meetingSession.audioVideo.startVideoInput(videoInputDeviceId);
        await meetingSession.audioVideo.chooseAudioOutput(audioOutputId);

        if (!_.isEmpty(audioOutputDevices)) {
            await meetingSession.audioVideo.chooseAudioOutput(audioOutputDevices[0].deviceId);
        } else {
            // Helper.globalAlert('No audio speaker found','error');
        }
        if (!isConversationInitialized) {
            isConversationInitialized = true;


            startAudioTask(audioInputElement);
            startSharingVideo(videoInputDeviceId);

            // toggleMute(0); // unmute the user firstly to show the proper icon
        }
        setTimeout(() => updateSelfTile(null), 500);
    } catch (e) {
        console.error("error in audio video assign chime", e);
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will start the audio conversation and assign a observer which detect audio changes
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {HTMLAudioElement} audioElement Audio element to play the conversation audio
 */
const startAudioTask = (audioElement) => {
    meetingSession.audioVideo.bindAudioElement(audioElement);
    meetingSession.audioVideo.start();
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will start the self user video and assign the self video element to show current camera video
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 */
const startSharingVideo = async () => {
    meetingSession.audioVideo.startLocalVideoTile();
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will stop the video conversation
 * and dis-allocate the self video tag so that the camera doesn't shows keep using after leaving page
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @returns {Promise<void>}
 */
const stopVideo = async () => {
    // meeting session will be not null if video has already started
    if (meetingSession) {
        meetingSession.audioVideo.stop();
        await meetingSession?.audioVideo?.startVideoInput(null);
        currentMeetingId = null;

        isConversationInitialized = false;
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To start the observer for the chime video sdk
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {React.Component} selfVideoElement Self user video element
 * @param {Object} additional The additional value passed which contains handlers
 */
const startObserver = (selfVideoElement, additional) => {
    const provideVideoElementToUser = additional.handlers.provideVideoElementToUser;

    const observers = {
        videoTileDidUpdate: tileState => {
            try {
                // Ignore a tile without attendee ID and other attendee's tile.
                if (tileState && tileState.tileId === 1) {
                    if (!tileState.boundAttendeeId || !tileState.localTile) {
                        return;
                    }

                    additional.states.selfSeat.set({
                        ...additional.states.selfSeat.get,
                        tileState: tileState
                    }); // updating the self seat tileState so loader can be turn off when titleState.active === true
                    startSubscriberForVolume(tileState?.boundAttendeeId, additional.dispatch);
                    meetingSession.audioVideo.bindVideoElement(tileState.tileId, selfVideoElement);
                } else if (tileState && tileState.tileId && tileState.tileId !== 1) {
                    if (!tileState.boundAttendeeId || tileState.localTile || tileState.isContent) {
                        return;
                    }
                    videoElementRepo.updateSeatTileState(tileState.boundExternalUserId, tileState);
                    startSubscriberForVolume(tileState?.boundAttendeeId, additional.dispatch);
                    const videoTag = provideVideoElementToUser(tileState.boundExternalUserId);
                    if (videoTag) {
                        meetingSession.audioVideo.bindVideoElement(tileState.tileId, videoTag);
                    }
                }
            } catch (err) {
                Helper.globalAlert(err, 'error');
            }
        },
    }
    meetingSession.audioVideo.addObserver(observers);
}

const startSubscriberForVolume = (chimeAttendeeId, dispatcher) => {
    if (1 || !_.has(attendeeVolumeSubscribed, [chimeAttendeeId])) {
        attendeeVolumeSubscribed[chimeAttendeeId] = chimeAttendeeId;
        meetingSession.audioVideo.realtimeSubscribeToVolumeIndicator(
            chimeAttendeeId,
            (
                attendeeId,
                volume,
                muted,
                signalStrength,
                externalUserId
            ) => {
                if (muted !== null || volume !== null) {
                    dispatcher({
                        type: KCT.NEW_INTERFACE.UPDATE_CONVERSATION_OTHER_MUTE,
                        payload: {
                            userId: Number.parseInt(externalUserId),
                            muted,
                            volume,
                        }
                    })
                }
            });
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method provides current mute-unmute status for the self user.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @return {Boolean} mute state
 */
const getCurrentMuteState = () => {
    return meetingSession.audioVideo.realtimeIsLocalAudioMuted();
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To update the user tile state to new tile state from the existing video repository
 * this will check for the available seat and allocate that to user
 * if user tile not found it will remove that
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} userId User id to find the state for
 * @returns {null}
 */
const updateVideoTile = (userId) => {
    const i = VideoElementRepository.getUserIndex(userId);
    let element = null;
    if (i != -1) {
        element = document.getElementById(`other-video${i}`);
    } else {
        return null;
    }
    if (VideoElementRepository.SEATS[i].tileState && VideoElementRepository.SEATS[i].tileState.tileId) {
        meetingSession.audioVideo.bindVideoElement(VideoElementRepository.SEATS[i].tileState.tileId, element);
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Update the tile state for the self user
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} tileState Tile state for the respective user
 */
const updateSelfTile = (tileState) => {
    if (meetingSession) {

        if (tileState && tileState.tileId) {
            let element = document.getElementById("self-video");
            if (element) {
                meetingSession.audioVideo.bindVideoElement(tileState.tileId, element);
            }
        } else if (tileState === null) {
            let element = document.getElementById("self-video");
            if (element) {
                meetingSession.audioVideo.bindVideoElement(1, element);
            }
        }
        meetingSession.audioVideo.bindAudioElement(document.getElementById('self-audio'));

    }
}


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To refresh the user tile video
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 */
const refreshUserTiles = () => {
    VideoElementRepository.SEATS.forEach(seat => {
        if (seat.userId && seat.tileState && seat.tileState.tileId) {
            updateVideoTile(seat.userId);
        }
    });
}

const resetVolumeSubscribe = () => {
    attendeeVolumeSubscribed = {};
}


export default {
    start: start,
    toggleMute,
    stopVideo,
    assignAudioVideoDevice,
    getCurrentMuteState,
    updateVideoTile,
    updateSelfTile,
    refreshUserTiles,
    resetVolumeSubscribe,
    getMeetingSession: () => meetingSession,
};