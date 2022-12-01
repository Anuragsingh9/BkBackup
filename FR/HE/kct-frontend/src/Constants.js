/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Constants File which holds the constant values for providing the global environment to application
 * so all component can use these constants to provide the decoupling of types value
 * ---------------------------------------------------------------------------------------------------------------------
 * @module
 */

/**
 *
 * @type {Object}
 * @property {Number} CNT_MGMT_ZOOM_SDK Zoom sdk type content
 * @property {Number} CNT_MGMT_VIDEO Video type content
 * @property {Number} CNT_MGMT_IMAGE Image type content
 * @property {String} EVENT_DEFAULT_IMAGE Event default image content type
 */

const contentManagement = {
    CNT_MGMT_ZOOM_SDK: 1,
    CNT_MGMT_VIDEO: 2,
    CNT_MGMT_IMAGE: 3,
    EVENT_DEFAULT_IMAGE: 'http://projectdevzone.com/keepcontact/images/video-dummy-img.jpg',
}

/**
 * @type {Object}
 * @property {Number} MUTE Mute state of networking
 * @property {Number} CLOSE Close state of networking
 */
const networkingState = {
    MUTE: 1,
    CLOSE: 2,
}

/**
 * @type {Object}
 * @property {Number} MUTE Mute state of content
 * @property {Number} CLOSE Close state of content
 */
const contentState = {
    MUTE: 1,
    CLOSE: 2,
}

/**
 * @type {Object}
 * @property {Number} MODE_DEVICE_SET Media device popup, device selector mode
 * @property {Number} MODE_CAPTURE_AND_PREVIEW Media device popup, Capture mode
 * @property {Object} PREVIEW_MODE Media device popup preview mode keys
 * @property {Number} PREVIEW_MODE.LOADING Loading state of popup
 * @property {Number} PREVIEW_MODE.PERMISSION_ISSUE Permission issue display mode
 * @property {Number} PREVIEW_MODE.PREVIEW Preview device mode
 * @property {Number} PREVIEW_MODE.DEVICE_OCCUPIED Device occupied display mode
 */
const mediaDevicePop = {
    MODE_DEVICE_SET: 1,
    MODE_CAPTURE_AND_PREVIEW: 2,
    PREVIEW_MODE: {
        LOADING: 1,
        PERMISSION_ISSUE: 2,
        PREVIEW: 3,
        DEVICE_OCCUPIED: 4,
    }
}

/**
 * @type {Object}
 * @property {Number} HOST_OFFLINE Host offline status
 * @property {Number} HOST_ONLINE Host online status
 * @property {Number} HOST_IN_CONVERSATION Host is busy status
 * @property {Number} HOST_IN_SAME_CONVERSATION Host is in self conversation status
 */
const hostStatus = {
    HOST_OFFLINE: 0,
    HOST_ONLINE: 1,
    HOST_IN_CONVERSATION: 2,
    HOST_IN_SAME_CONVERSATION: 3,
}

const EVENT_TYPES = {
    CAFETERIA: 1,
    EXECUTIVE: 2,
    MANAGER: 3,
    ALL_DAY: 4,
}

const spaceFallbackRemoveTime = 5; // seconds
const INT32BIT_MAX = 2147483647;

const chimeBackgroundBlurStrength = 10

const SIGN_UP_FORM_MODE = {
    REGISTER: 1,
    OTP_VERIFY: 2,
}

let CHIME_BG= {
    TYPE: {
        NONE: 1,
        BLUR: 2,
        SYSTEM: 3,
        STATIC: 4,
    },
    BLUR_STRENGTH: 50,
    filterCPUUtilization: 10
}

export default {
    contentManagement,
    networkingState,
    contentState,
    mediaDevicePop,
    hostStatus,
    CONVERSATION_MAX: 8,
    EVENT_TYPES,
    spaceFallbackRemoveTime,
    INT32BIT_MAX,
    SIGN_UP_FORM_MODE: SIGN_UP_FORM_MODE,
    chimeBackgroundBlurStrength: chimeBackgroundBlurStrength,
    CHIME_BG: CHIME_BG,
};