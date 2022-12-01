/**
 * @type {Object}
 * @property {MediaDeviceInfo[]} audioDevices Available Audio input devices list
 * @property {MediaDeviceInfo[]} videoDevices Available Video Devices List
 * @property {MediaDeviceInfo[]} audioOutputDevices Available Audio Output devices list
 * @property {String} selectedAudioDevice Current selected audio input device id
 * @property {String} selectedVideoDevice Current selected video input device id
 * @property {String} selectedAudioOutput Current selected audio output device id
 * @property {String} defaultAudioInput System Default audio input device id
 * @property {String} defaultAudioOutput System Default selected audio output device id
 * @property {String} defaultVideoInput System Default selected video input device id
 */
const AvailableMediaDevices = {
    audioDevices: [MediaDeviceInfo],
    videoDevices: [MediaDeviceInfo],
    audioOutputDevices: [MediaDeviceInfo],
    selectedAudioDevice: 'selectedAudioDevice',
    selectedVideoDevice: 'selectedVideoDevice',
    selectedAudioOutput: 'selectedAudioOutput',
    defaultAudioInput: 'defaultAudioInput',
    defaultAudioOutput: 'defaultAudioOutput',
    defaultVideoInput: 'defaultVideoInput',
};

export default AvailableMediaDevices;
