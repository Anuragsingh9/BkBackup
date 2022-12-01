import Constants from "../../Constants";
import VideoMeetingClass from "./VideoMeetingClass";
import {
    BackgroundBlurVideoFrameProcessor,
    BackgroundReplacementVideoFrameProcessor,
    ConsoleLogger,
    DefaultVideoTransformDevice,
    LogLevel
} from "amazon-chime-sdk-js";

let meetingSession = null;
let selectedVideoDeviceId = null;

const applyBackground = async (bgType, value) => {
    meetingSession = VideoMeetingClass.getMeetingSession();
    console.log('dddddddddddddd bg type applying', bgType, meetingSession);
    if (!meetingSession) return;

    selectedVideoDeviceId = localStorage.getItem('user_video');

    let selfVideoDOM = document.getElementById("self-video");

    switch (bgType) {
        case Constants.CHIME_BG.TYPE.NONE:
            if (selectedVideoDeviceId) {
                await meetingSession.audioVideo.startVideoInput(selectedVideoDeviceId);
                await meetingSession.audioVideo.bindVideoElement(1, selfVideoDOM);
            }
            backgroundReplacementProcessor = null;
            break;
        case Constants.CHIME_BG.TYPE.BLUR:
            await blurBackground()
            backgroundReplacementProcessor = null;
            break;
        case Constants.CHIME_BG.TYPE.SYSTEM:
            await applySystemFileBackground(value);
            break;
        case Constants.CHIME_BG.TYPE.STATIC:
            break;
        default:
            return;
    }

}

let backgroundBlurProcessor = null;

const blurBackground = async () => {
    if (selectedVideoDeviceId) {
        let logLevel = process.env.REACT_APP_HE_PROJECT_ENV === 'production' ? LogLevel.OFF : LogLevel.DEBUG;
        const logger = new ConsoleLogger('MyLogger', logLevel);
        // check AWS Chime blur feature is supported in current browser
        if (await BackgroundBlurVideoFrameProcessor.isSupported()) {
            if (!backgroundBlurProcessor) {
                backgroundBlurProcessor = await BackgroundBlurVideoFrameProcessor.create(
                    undefined,
                    {
                        blurStrength: Constants.CHIME_BG.BLUR_STRENGTH,
                        filterCPUUtilization: Constants.CHIME_BG.filterCPUUtilization,
                    }
                )
            }
            if (backgroundBlurProcessor) {
                const chosenVideoTransformDevice = new DefaultVideoTransformDevice(
                    logger,
                    selectedVideoDeviceId,
                    [backgroundBlurProcessor]
                );

                await meetingSession?.audioVideo?.startVideoInput(chosenVideoTransformDevice);
                await meetingSession.audioVideo.bindVideoElement(
                    1,
                    document.getElementById("self-video")
                );
            }
        } else {
            await meetingSession.audioVideo.startVideoInput(selectedVideoDeviceId);
        }
    }
}

const applySystemFileBackground = async (file) => {
    if (!file) {
        await meetingSession?.audioVideo?.startVideoInput(selectedVideoDeviceId);
        await meetingSession?.audioVideo.bindVideoElement(1, document.getElementById("self-video"));
        console.warn('Image not found to apply on background');
        return;
    }
    const reader = new FileReader();
    reader.onload = async (event) => {
        let blob = new Blob([event.target.result], {type: file.type});
        await changeBackgroundByBlob(blob);
    }
    reader.readAsArrayBuffer(file);
}

let backgroundReplacementProcessor = null;

const changeBackgroundByBlob = async blob => {
    let logLevel = process.env.REACT_APP_HE_PROJECT_ENV === 'production' ? LogLevel.OFF : LogLevel.DEBUG;
    const logger = new ConsoleLogger('MyLogger', logLevel);
    const options = {imageBlob: blob};

    // preparing processor
    if (!backgroundReplacementProcessor) {
        backgroundReplacementProcessor = await BackgroundReplacementVideoFrameProcessor.create(undefined, options);

        let transformDevice = new DefaultVideoTransformDevice(logger, selectedVideoDeviceId, [backgroundReplacementProcessor]);

        await meetingSession?.audioVideo?.startVideoInput(transformDevice);
        await meetingSession?.audioVideo.bindVideoElement(1, document.getElementById("self-video"));
    } else {
        await backgroundReplacementProcessor.setImageBlob(blob);
    }
}

let ChimeBGService = {
    applyBackground: applyBackground,
}

export default ChimeBGService;
