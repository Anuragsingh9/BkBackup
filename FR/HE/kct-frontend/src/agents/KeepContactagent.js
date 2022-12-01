/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file is used to store the api related objects.
 * Here all the api triggering methods are described
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @module API Redux Agent
 */

import axios from "axios";
// import Store from '../../../store'
import _ from "lodash";

var querystring = require("query-string");


// setting up the domain for the local use
if (process.env.NODE_ENV == "development" || process.env.NODE_ENV == "test") {

    var BASE_ROOT = `${process.env.REACT_APP_HE_TESTHOSTNAME}/api/v1/p/`;
    var DOWNLOAD_URL = BASE_ROOT;
    var IMG_PATH = BASE_ROOT + "public/";

} else {
    // for online use and preparing the build, as the sub domain is available on live so that can be split from url
    var SUB_DOMAIN = window.location.host.split(".")[1]
        ? window.location.host.split(".")[0]
        : false;
    var BASE_DOMAIN = process.env.REACT_APP_HE_HOSTNAME;
    var DOWNLOAD_URL =
        window.location.protocol + "//" + window.location.host + "/";
    var BASE_ROOT =
        window.location.protocol +
        "//" +
        SUB_DOMAIN +
        "." +
        BASE_DOMAIN +
        "/api/v1/p/";
    var IMG_PATH = DOWNLOAD_URL + "public";
}

//=================End
const API_ROOT = BASE_ROOT;
const COMMON_API_ROOT = BASE_ROOT;
const GLOBLE_API_ROOT = BASE_ROOT;
// const API_ROOT = BASE_ROOT + 'api/CRM';
//cors
const CORS = "https://cors-anywhere.herokuapp.com/";

var BASE_URL =
    window.location.hostname == "localhost"
        ? BASE_ROOT
        : window.location.protocol + "//" + window.location.host + "/";
if (window.location.protocol == "") {
    BASE_URL =
        window.location.hostname == "localhost"
            ? BASE_ROOT
            : "http://" + window.location.protocol + "/" + window.location.host + "/";
}

axios.interceptors.response.use(
    function (response) {
        return response;
    },
    function (error) {
        if (error.response != undefined && error.response.status == 401 && !localStorage.getItem('ignore401')) {
            window.location = "/";
        } else if (error.response != undefined && error.response.status == 422) {
        }
        return Promise.reject(error);
    }
);

// Store.makeAuth(window.auth);


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This object will provide the basic api call functionality with different types
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @type {Object}
 * @property {Function} postWithFile Api for post method with file handling
 * @property {Function} deleteWithToken Api method for delete with access token
 * @property {Function} globleRawPostapi Api function for post method
 * @property {Function} mocKBadgeApi To hit the mock api of user badge
 * @property {Function} del To hit the delete api
 * @property {Function} postMockApi To hit the post api on mock server
 * @property {Function} postWithToken To hit the post api with user access token
 * @property {Function} updateMockApi To hit the update api on mock server
 * @property {Function} delete To hit the delete api
 * @property {Function} put To hit the put method api by method spoofing
 * @property {Function} globleApiPost To hit the post api
 * @property {Function} commonPostWithFile To hit the post api with file handling
 * @property {Function} post To hit the simple post api
 * @property {Function} postWithJSONToken To hit the post api with user access token
 * @property {Function} globleApipostWithFile To hit the post api with file handling
 * @property {Function} get To hit the get api
 * @property {Function} getMockApi To hit the get api on mock server
 * @property {Function} deleteMockApi To hit the delete api on mock server
 * @property {Function} postWithOuterApi To hit the post api without adding any prefix to path
 * @property {Function} commonApiPost To hit the post api
 * @property {Function} getWithToken To hit the get api with user access token
 * @property {Function} postWithFileProg To hit the post api with file handling
 * @property {Function} rawpostapi To hit the post api with json data handling
 * @property {Function} getWithOuterApi To hit the get api without path prefix
 * @property {Function} globleApiGet To hit the simple get api
 * @property {Function} postFileWithToken To hit the post api with file handling and user access token
 * @property {Function} commonApiGet To hit the simple get api
 */
const requests = {

    del: (url) => axios.get(`${API_ROOT}${url}`),
    get: (url) => axios.get(`${API_ROOT}${url}`, {
        // config,
        headers: {'Accept': 'Application/json', Authorization: `Bearer ${localStorage.getItem('accessToken')}`},
    }),
    globleApiGet: (url) =>
        axios.get(`${GLOBLE_API_ROOT}${url}`, {withCredentials: true}),
    getMockApi: (url) => axios.get(`${url}`),
    postMockApi: (url) => axios.post(`${url}`),
    deleteMockApi: (url) => axios.delete(`${url}`),
    updateMockApi: (url) => axios.put(`${url}`),
    mocKBadgeApi: (url) => axios.post(`${url}`),
    globleApiGet: (url) =>
        axios.get(`${GLOBLE_API_ROOT}${url}`, {withCredentials: true}),
    commonApiGet: (url) =>
        axios.get(`${COMMON_API_ROOT}${url}`, {withCredentials: true}),

    post: (url, body, config = null) =>
        axios.post(`${API_ROOT}${url}`, querystring.stringify(body), {
            config,
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            withCredentials: true,
        }),
    postWithToken: (url, body, config = null) =>
        axios.post(`${API_ROOT}${url}`, querystring.stringify(body), {
            config,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                Accept: "Application/json",
                Authorization: `Bearer ${localStorage.getItem("accessToken")}`,
            },
        }),
    postWithJSONToken: (url, body, config = null) =>
        axios.post(`${API_ROOT}${url}`, body, {
            config,
            headers: {
                "Content-Type": "application/json",
                Accept: "Application/json",
                Authorization: `Bearer ${localStorage.getItem("accessToken")}`,
            },
        }),
    getWithToken: (url, body, config = null) =>
        axios.get(`${API_ROOT}${url}`, {
            // config,
            headers: {
                Accept: "Application/json",
                Authorization: `Bearer ${localStorage.getItem("accessToken")}`,
            },
        }),
    getWithOuterApi: (url) =>
        axios.get(`${BASE_ROOT}api/${url}`, {withCredentials: true}),
    postWithOuterApi: (url, body, config = null) =>
        axios.post(`${BASE_ROOT}api/${url}`, querystring.stringify(body), {
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            withCredentials: true,
        }),

    put: (url, body, config = null) =>
        axios.post(`${API_ROOT}${url}`, body, {
            config,

            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            withCredentials: true,
        }),

    rawpostapi: (url, body, config = null) =>
        axios.post(`${API_ROOT}${url}`, body, {
            config,
            headers: {"Content-Type": "application/json"},
            withCredentials: true,
        }),
    postWithJson: (url, body, config = null) =>
        axios.post(`${API_ROOT}${url}`, body, {
            config,
            headers: {"Content-Type": "application/json"},
        }),
    globleRawPostapi: (url, body, config = null) =>
        axios.post(`${GLOBLE_API_ROOT}${url}`, body, {
            config,
            headers: {"Content-Type": "application/json"},
            withCredentials: true,
        }),

    commonApiPost: (url, body, config = null) =>
        axios.post(`${COMMON_API_ROOT}${url}`, querystring.stringify(body), {
            config,
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            withCredentials: true,
        }),
    globleApiPost: (url, body, config = null) =>
        axios.post(`${GLOBLE_API_ROOT}${url}`, querystring.stringify(body), {
            config,
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            withCredentials: true,
        }),

    commonPostWithFile: (url, body, config = null) =>
        axios.post(`${COMMON_API_ROOT}${url}`, body, {
            headers: {
                "content-type": "multipart/form-data",
                Accept: "Application/json",
            },
            withCredentials: true,
        }),
    postFileWithToken: (url, body, config = null) =>
        axios.post(`${API_ROOT}${url}`, body, {
            // config,
            headers: {
                "content-type": "multipart/form-data",
                Accept: "Application/json",
                Authorization: `Bearer ${localStorage.getItem("accessToken")}`,
            },
        }),
    postWithFile: (url, body, config = null) =>
        axios.post(`${API_ROOT}${url}`, body, {
            headers: {"content-type": "multipart/form-data"},
        }),

    //for upload via progress bar
    globleApipostWithFile: (url, body, config = null) =>
        axios.post(`${GLOBLE_API_ROOT}${url}`, body, {
            headers: {"content-type": "multipart/form-data"},
            withCredentials: true,
        }),
    postWithFileProg: (url, body, config) =>
        axios.post(`${API_ROOT}${url}`, body, {
            headers: {
                "content-type": "multipart/form-data",
                Accept: "Application/json",
            },
            withCredentials: true,
            onUploadProgress: config,
        }),
    delete: (url) => axios.delete(`${API_ROOT}${url}`),
    deleteWithToken: (url) =>
        axios.delete(`${API_ROOT}${url}`, {
            headers: {
                "content-type": "multipart/form-data",
                Accept: "Application/json",
                Authorization: `Bearer ${localStorage.getItem("accessToken")}`,
            },
        }),
};
/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This object will provide the search api for mock use only currently
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @type {Object}
 * @property {Function} fetchSearchData To fetch the search data for event
 */
const SearchApi = {
    fetchSearchData: (keyword) => requests.get(`event/17ae0a36-d2f4-11ea-aba0-024260a98272`),
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This object provides the api hitting related to auth user
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @type {Object}
 * @property {Function} getEventDetails To get the event details
 * @property {Function} login To login the user
 * @property {Function} registerBasic To signup within event
 * @property {Function} getDefaultEventList To get the event list
 * @property {Function} registerOtp To submit the otp for verify
 * @property {Function} registerSpaceMood To join the event with selected space
 * @property {Function} resendOtp To resent the OTP to user email
 * @property {Function} getOtpData To decrypt the event and user data on otp page by email encrypted key
 * @property {Function} sendForgetPasswordLink To send the forget password link to user email
 * @property {Function} resetPassword To reset the password for user
 * @property {Function} changePassword To change the password for the user
 * @property {Function} logout To logout the user and remove user related data from local
 * @property {Function} getOrganisation To get the current account details
 * @property {Function} getOrganisationWithToken To get the account details without user logged in
 * @property {Function} getUsersData To get the user data
 * @property {Function} changeLanguage To change the language for user
 * @property {Function} getUserInfo To get the user data
 * @property {Function} addInvite To invite the system or outer user to event by email
 * @property {Function} addJoin To join th event
 * @property {Function} addLog To add the logs for the user devices
 */
const Auth = {
    getEventDetails: (id) => requests.get(`event/${id}`),
    login: (data) => requests.postWithJson(`login`, data),
    registerBasic: (data) => requests.postWithFile(`register`, data),
    getDefaultEventList: (id) => requests.get(`events/list`),
    registerOtp: (data) => requests.postWithJSONToken("verify/email", data),
    registerSpaceMood: (data) => requests.postWithJSONToken("events/join", data),
    resendOtp: (data) => requests.postWithJSONToken(`resend/verify/email`, data),
    getOtpData: (data) => requests.get(`otp/page/data?key=${data.key}`, data),
    sendForgetPasswordLink: (data) =>
        requests.postWithJSONToken(`users/password/forget`, data),
    resetPassword: (data) =>
        requests.postWithJSONToken(`users/password/reset`, data),
    changePassword: (data) => requests.postWithJSONToken(`change/password`, data),
    logout: (val) => requests.postWithJSONToken(`logout`, val),
    getOrganisation: () => requests.get(`init/data`),
    getOrganisationWithToken: () => requests.getWithToken(`init/data`),

    getUsersData: () => requests.getWithToken(`users/settings`),
    changeLanguage: (val) => requests.postWithJSONToken(`users/lang`, val),
    getUserInfo: (eventId) =>
        requests.getWithToken(`login-join-data?event_uuid=` + eventId),
    addInvite: (data) => requests.postWithJSONToken(`send/invite`, data),
    addJoin: (data) => requests.postWithJSONToken(`quick-join-event`, data),
    addLog: (data) => requests.postWithJSONToken('logs', data),

};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This object contains the method for the event related api's
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @type {Object}
 * @property {Function} getMainHost To get the main host data
 * @property {Function} mocKBadgeApi To get the user data from mock api
 * @property {Function} getEventGraphics To get the event details and graphics setting applied at that time
 * @property {Function} getEventSpaces To get the event spaces with conversations
 * @property {Function} spaceJoin To join the specific space in event
 * @property {Function} conversationJoin To start the conversation
 * @property {Function} leaveConversation To leave the conversation
 * @property {Function} eventList To get the future/past event list
 * @property {Function} updatePass To update the password for the user
 * @property {Function} getProfile To get the user profile
 * @property {Function} updateProfile To update the user profile data
 * @property {Function} getCurrentConversation To get the user current conversation
 * @property {Function} getTag To get the professional and personal tags data
 * @property {Function} getBadge To get the user badge
 * @property {Function} deleteTag To remove the user tag
 * @property {Function} addTag To attach a tag with user
 * @property {Function} addEntityUser To add a user in existing/new entity
 * @property {Function} removeEntityUser To remove the user from the entity
 * @property {Function} updateSocialLink To update the social links for the user
 * @property {Function} deleteProfilePic To remove the user profile picture
 * @property {Function} toggleDnd To toggle the dnd mode for the user
 * @property {Function} getEmbeddedUrl To get the embedded url for the zoom if its live
 * @property {Function} updateProfileData To update the user profile specific field
 * @property {Function} setVisibility To update the specific user profile field visibility to others
 * @property {Function} privateConversation To make the conversation private
 * @property {Function} spaceHostData To Get the space host data for specific space
 * @property {Function} userInfo To get the user info with all details that are set to visible
 * @property {Function} singleUser To get the single user data
 * @property {Function} banUser To ban a user from platform/event
 * @property {Function} removeTag To remove the personal professional tag
 * @property {Function} createTag To create a personal professional tag
 * @property {Function} updateTag To update the relation of user with personal professional tag
 * @property {Function} updateInfo To update the user profile multiple fields
 * @property {Function} getGroupGraphicsByEvent To get the current event's group's graphics setting
 */
const Event = {
    getMainHost: () =>
        requests.getMockApi(
            `https://607e612502a23c0017e8b3b8.mockapi.io/api/v1/events/1`
        ),
    mocKBadgeApi: () =>
        requests.mocKBadgeApi(
            `https://60b0bfd91f26610017fff1f2.mockapi.io/api/v1/Badge`
        ),
    getEventGraphics: (id, accessCode = null) =>
        requests.getWithToken(`event/kct-customization/${id}${accessCode ? '?access_code=' + accessCode : ''}`),
    getEventSpaces: (id) => requests.getWithToken(`event/space/all/${id}`),
    spaceJoin: (data) => requests.postWithJSONToken(`event/space/join`, data),
    conversationJoin: (data) =>
        requests.postWithJSONToken(`event/space/conversation/join`, data),
    leaveConversation: (data) =>
        requests.postWithJSONToken(`event/space/conversation/leave`, data),
    eventList: (data, page, item_per_page, search, order_by, order, group_key) => {
        let path = `events?${data.tense !== undefined && !_.isEmpty(data.tense) ? `tense=${data.tense}` : ""}`;
        path += data.page !== undefined && !_.isEmpty(data.page) ? `&page=${data.page}` : "";
        path +=
            data.sizePerPage !== undefined && !_.isEmpty(data.sizePerPage)
                ? `&item_per_page=${data.sizePerPage}`
                : "";
        path += data.searchText !== undefined && !_.isEmpty(data.searchText) ? `&key=${data.searchText}` : "";
        path +=
            data.orderBy !== undefined && !_.isEmpty(data.orderBy)
                ? `&order_by=${data.orderBy}`
                : "";
        path += data.order !== undefined && !_.isEmpty(data.order) ? `&order=${data.order}` : "";
        path += data.groupKey !== undefined && !_.isEmpty(data.groupKey) ? `&group_key=${data.groupKey}` : "";
        return requests.getWithToken(path);
    },
    updatePass: (data) => requests.postWithJSONToken(`users/password`, data),
    getProfile: () => requests.getWithToken(`users/profiles`),
    updateProfile: (data) => requests.postWithJSONToken(`users/profiles`, data),
    getCurrentConversation: (eventId) =>
        requests.getWithToken(`event/space/conversation/${eventId}`),
    getTag: () => requests.getWithToken(`get-user-tags`),
    getBadge: (event_uuid = null) => requests.getWithToken(`badge${event_uuid ? `?eventId=${event_uuid}` : ""}`),
    deleteTag: (data) => requests.postWithToken(`tag-delete`, data),
    addTag: (data) => requests.postWithToken(`add-tag`, data),
    addEntityUser: (data) =>
        requests.postWithJSONToken(`users/badges/entities`, data),
    removeEntityUser: (data) =>
        requests.postWithJSONToken(`users/badges/entities`, data),
    updateSocialLink: (data) =>
        requests.postWithJSONToken(`users/badges/socials`, data),
    deleteProfilePic: (val) =>
        requests.postWithJSONToken("users/profiles/pictures", val),
    toggleDnd: (val) => requests.postWithJSONToken("event/dnd", val),
    getEmbeddedUrl: (id) => requests.getWithToken(`event/embedded-url/` + id),
    updateProfileData: (val) =>
        requests.postWithJSONToken(`users/badges/profiles`, val),
    setVisibility: (data) =>
        requests.postWithJSONToken(`users/badges/visibility`, data),
    privateConversation: (data) =>
        requests.postWithJSONToken(`change/conversion/type`, data),
    spaceHostData: () =>
        requests.getMockApi(
            "https://607e612502a23c0017e8b3b8.mockapi.io/api/v1/Space/3"
        ),
    userInfo: () =>
        requests.getMockApi(
            "https://607e612502a23c0017e8b3b8.mockapi.io/api/v1/kk"
        ),
    singleUser: () =>
        requests.getMockApi(
            "https://607e612502a23c0017e8b3b8.mockapi.io/api/v1/Space/3"
        ),
    banUser: (data) => requests.postWithJSONToken("event/user/ban", data),
    getEventGraphicData: (data) =>
        requests.getWithToken(
            `graphics/customization?${data ? `event_uuid=${data}` : ""}`
        ),
    //professional and personal tag
    removeTag: (data) => requests.postWithJSONToken(`users/tag/delete`, data),
    createTag: (data) => requests.postWithJSONToken(`users/tag/create`, data),
    updateTag: (data) => requests.postWithJSONToken(`users/tag/attach`, data),
    updateInfo: (data) => requests.postWithJSONToken(`users/info/update`, data),
    getGroupGraphicsByEvent: (eventId) => requests.getWithToken(`graphics/event/${eventId}`),

};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This object contains the api for the entity management related
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @type {Object}
 *
 * @property {Function} searchEntity To search the entity
 * @property {Function} searchEntityUser To search the user within entity
 * @property {Function} updateBadge To update the specific field of user profile
 * @property {Function} searchApi To search the tag for the user
 */
const EntityTypeSearch = {
    searchEntity: (val, type, entity_id = null, sub_type) =>
        requests.getWithToken(`users/badges/entity/search/${val}/${type}`),
    searchEntityUser: (val, type, id) =>
        requests.getWithToken(`users/badges/entity/search/${val}/${type}`),
    updateBadge: (data) =>
        requests.postWithJSONToken(`users/badges/entities`, data),
    searchApi: (data) => requests.getWithToken(`users/tag/search?${data}`),
};
export default {
    SearchApi,
    Auth,
    Event,
    EntityTypeSearch,
};
