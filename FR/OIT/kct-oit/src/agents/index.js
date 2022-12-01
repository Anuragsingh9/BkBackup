/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file contains the api related methods and variables
 * As application have multi mode to run example on localhost and on production environment so on local environment
 * the subdomain is fixed and on production its auto fetched from browser absolute url<br />
 * This file also hold the api triggering methods with different types of HTTP Verbs (ex. POST, GET, etc)
 * and those api methods have used here to call the specific api of the backend server
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @module API Redux Agent
 */
import axios from 'axios';
import _ from 'lodash';

var querystring = require('querystring');

// here checking if the application is in development mode to add the subdomain manually
if (process.env.NODE_ENV == 'development' || process.env.NODE_ENV == 'test') {
    var BASE_ROOT = `${process.env.REACT_APP_HO_TESTHOST}/api/v1/admin/`
    var DOWNLOAD_URL = BASE_ROOT;
    var IMG_PATH = BASE_ROOT + 'public/';

} else {
    // here production environment will handle to auto fetch the sub domain from url
    var SUB_DOMAIN = window.location.host.split('.')[1] ? window.location.host.split('.')[0] : false;
    var BASE_DOMAIN = window.globalBaseDomain ? window.globalBaseDomain : process.env.REACT_APP_HO_HOSTNAME;
    var DOWNLOAD_URL = window.location.protocol + '//' + window.location.host + '/';
    var BASE_ROOT = window.location.protocol + '//' + SUB_DOMAIN + '.' + BASE_DOMAIN + '/api/v1/admin/';
    var IMG_PATH = DOWNLOAD_URL + 'public';
}

const API_ROOT = BASE_ROOT;
const COMMON_API_ROOT = BASE_ROOT;
const GLOBLE_API_ROOT = BASE_ROOT;
const CORS = 'https://cors-anywhere.herokuapp.com/';

var BASE_URL = (window.location.hostname == "localhost") ? BASE_ROOT : window.location.protocol + '//' + window.location.host + '/';
if (window.location.protocol == '') {
    BASE_URL = (window.location.hostname == "localhost") ? BASE_ROOT : 'http://' + window.location.protocol + '/' + window.location.host + '/';
}
// redirecting user to /oit if in url the /oit prefix is missing
axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (error.response != undefined && error.response.status == 401) {
        const loc = window.location.href;
        let newLoc = '';
        if (loc && loc.includes('oit')) {
            newLoc = loc.split('/oit')[0];
        } else {
            newLoc = loc
        }
        localStorage.removeItem('oitToken');
        if (newLoc) {
            window.location.replace(`${newLoc}/signin`)
        }
        // window.location = '/';
    } else if (error.response != undefined && error.response.status == 422) {

    }
    return Promise.reject(error);
});

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This object contains the api calling method with different verbs and user access token inclusion
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @type {{
 * postWithFile: (function(*, *=, *=): Promise<AxiosResponse<any>>),
 * deleteWithToken: (function(*): Promise<AxiosResponse<any>>),
 * globleRawPostapi: (function(*, *=, *=): Promise<AxiosResponse<any>>),
 * mocKBadgeApi: (function(*): Promise<AxiosResponse<any>>),
 * del: (function(*): Promise<AxiosResponse<any>>),
 * postMockApi: (function(*): Promise<AxiosResponse<any>>),
 * postWithToken: (function(*, *=, *=): Promise<AxiosResponse<any>>),
 * updateMockApi: (function(*): Promise<AxiosResponse<any>>),
 * delete: (function(*): Promise<AxiosResponse<any>>),
 * put: (function(*, *=, *=): Promise<AxiosResponse<any>>),
 * globleApiPost: (function(*, *=, *=): Promise<AxiosResponse<any>>),
 * commonPostWithFile: (function(*, *=, *=): Promise<AxiosResponse<any>>),
 * post: (function(*, *=, *=): Promise<AxiosResponse<any>>),
 * postWithJSONToken: (function(*, *=, *=): Promise<AxiosResponse<any>>),
 * globleApipostWithFile: (function(*, *=, *=): Promise<AxiosResponse<any>>),
 * get: (function(*): Promise<AxiosResponse<any>>),
 * getMockApi: (function(*): Promise<AxiosResponse<any>>),
 * getInitialWithToken: (function(*, *): Promise<AxiosResponse<any>>),
 * deleteMockApi: (function(*): Promise<AxiosResponse<any>>),
 * postWithOuterApi: (function(*, *=, *=): Promise<AxiosResponse<any>>),
 * commonApiPost: (function(*, *=, *=): Promise<AxiosResponse<any>>),
 * getWithToken: (function(*, *, *=): Promise<AxiosResponse<any>>),
 * postWithFileProg: (function(*, *=, *=): Promise<AxiosResponse<any>>),
 * rawpostapi: (function(*, *=, *=): Promise<AxiosResponse<any>>),
 * getWithOuterApi: (function(*): Promise<AxiosResponse<any>>),
 * globleApiGet: (function(*): Promise<AxiosResponse<any>>),
 * postFileWithToken: (function(*, *=, *=): Promise<AxiosResponse<any>>),
 * commonApiGet: (function(*): Promise<AxiosResponse<any>>)
 * }}
 */
const requests = {
    del: url =>
        axios.get(`${API_ROOT}${url}`),
    get: url =>
        axios.get(`${API_ROOT}${url}`, {withCredentials: true}),
    globleApiGet: url => axios.get(`${GLOBLE_API_ROOT}${url}`, {withCredentials: true}),
    getMockApi: url => axios.get(`${url}`,),
    postMockApi: url => axios.post(`${url}`,),
    deleteMockApi: url => axios.delete(`${url}`,),
    updateMockApi: url => axios.put(`${url}`,),
    mocKBadgeApi: url => axios.post(`${url}`),
    commonApiGet: url => axios.get(`${COMMON_API_ROOT}${url}`, {withCredentials: true}),

    post: (url, body, config = null) =>
        axios.post(`${API_ROOT}${url}`, querystring.stringify(body), {
            config,
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            withCredentials: true
        }),
    postWithToken: (url, body, config = null) =>
        axios.post(`${API_ROOT}${url}`, querystring.stringify(body), {
            config,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                'Accept': 'Application/json',
                Authorization: `Bearer ${localStorage.getItem('oitToken')}`
            },

        }),
    postWithJSONToken: (url, body, config = null) =>
        axios.post(`${API_ROOT}${url}`, body, {
            config,
            headers: {
                "Content-Type": "application/json",
                'Accept': 'Application/json',
                Authorization: `Bearer ${localStorage.getItem('oitToken')}`
            },

        }),
    getWithToken: (url, body, config = null) =>
        axios.get(`${API_ROOT}${url}`, {
            // config,
            headers: {'Accept': 'Application/json', Authorization: `Bearer ${localStorage.getItem('oitToken')}`},
        }),
    getInitialWithToken: (url, token) =>
        axios.get(`${API_ROOT}${url}`, {
            // config,
            headers: {'Accept': 'Application/json', Authorization: `Bearer ${token}`},
        }),
    getWithOuterApi: url =>
        axios.get(`${BASE_ROOT}api/${url}`, {withCredentials: true}),
    postWithOuterApi: (url, body, config = null) =>
        axios.post(`${BASE_ROOT}api/${url}`, querystring.stringify(body), {

            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            withCredentials: true
        }),


    put: (url, body, config = null) =>
        axios.post(`${API_ROOT}${url}`, body, {
            config,

            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            withCredentials: true
        }),

    rawpostapi: (url, body, config = null) =>
        axios.post(`${API_ROOT}${url}`, body, {
            config,
            headers: {"Content-Type": "application/json"},
            withCredentials: true
        }),
    globleRawPostapi: (url, body, config = null) =>
        axios.post(`${GLOBLE_API_ROOT}${url}`, body, {
            config,
            headers: {"Content-Type": "application/json",},
            withCredentials: true
        }),


    commonApiPost: (url, body, config = null) =>
        axios.post(`${COMMON_API_ROOT}${url}`, querystring.stringify(body), {
            config,
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            withCredentials: true
        }),
    globleApiPost: (url, body, config = null) =>
        axios.post(`${GLOBLE_API_ROOT}${url}`, querystring.stringify(body), {
            config,
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            withCredentials: true
        }),

    commonPostWithFile: (url, body, config = null) =>
        axios.post(`${COMMON_API_ROOT}${url}`, body, {
            headers: {'content-type': 'multipart/form-data', 'Accept': 'Application/json'},
            withCredentials: true,
        }),
    postFileWithToken: (url, body, config = null) =>
        axios.post(`${API_ROOT}${url}`, body, {
            // config,
            headers: {
                'content-type': 'multipart/form-data',
                'Accept': 'Application/json',
                Authorization: `Bearer ${localStorage.getItem('oitToken')}`
            },
        }),
    postWithFile: (url, body, config = null) =>

        axios.post(`${API_ROOT}${url}`, body, {
            headers: {'content-type': 'multipart/form-data'},

        }),

    //for upload via progress bar
    globleApipostWithFile: (url, body, config = null) =>

        axios.post(`${GLOBLE_API_ROOT}${url}`, body, {
            headers: {'content-type': 'multipart/form-data'},
            withCredentials: true,
        }),
    postWithFileProg: (url, body, config) =>

        axios.post(`${API_ROOT}${url}`, body, {
            headers: {'content-type': 'multipart/form-data', 'Accept': 'Application/json'},
            withCredentials: true,
            onUploadProgress: config,
        }),
    delete: (url) => axios.delete(`${API_ROOT}${url}`),
    deleteWithToken: (url) => axios.delete(`${API_ROOT}${url}`, {
        headers: {
            'content-type': 'multipart/form-data',
            'Accept': 'Application/json',
            Authorization: `Bearer ${localStorage.getItem('oitToken')}`
        },

    })
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This Object contains the API calls which are related to user
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @type {{
 * setPassword: (function(*=): Promise<AxiosResponse<*>>),
 * resetPassword: (function(*=): Promise<AxiosResponse<*>>),
 * getDraftEvents: (function(): Promise<AxiosResponse<*>>),
 * updatePassword: (function(*=): Promise<AxiosResponse<*>>),
 * updateRole: (function(*=): Promise<AxiosResponse<*>>),
 * forgetPassword: (function(*=): Promise<AxiosResponse<*>>),
 * entitySearch: (function(*): Promise<AxiosResponse<*>>),
 * updateUser: (function(*=): Promise<AxiosResponse<*>>),
 * updateProfileImage: (function(*=): Promise<AxiosResponse<*>>),
 * login: (function(*=): Promise<AxiosResponse<*>>),
 * getEvents: (function(*): Promise<AxiosResponse<*>>),
 * userImportFile: (function(*=): Promise<AxiosResponse<*>>),
 * userImportStep2: (function(*=): Promise<AxiosResponse<*>>),
 * getSelfUserById: (function(*): Promise<AxiosResponse<*>>),
 * deleteMultiUser: (function(*=): Promise<AxiosResponse<*>>),
 * addMultiple: (function(*=): Promise<AxiosResponse<*>>),
 * userSearch: (function(*=): Promise<AxiosResponse<*>>),
 * getUserData: (function(*): Promise<AxiosResponse<*>>),
 * logOut: (function(*): Promise<AxiosResponse<*>>),
 * setLanguage: (function(*=): Promise<AxiosResponse<*>>)
 * }}
 */
const User = {

  forgetPassword: (data) => requests.postWithFile(`forgot-password`, data),
  login: (data) => requests.postWithFile(`login`, data),
  setPassword: (data) => requests.postWithJSONToken(`default/password`,data),
  resetPassword: (data) => requests.postWithJSONToken(`reset-password`,data),
  getSelfUserById: (data) => requests.getInitialWithToken(`users?send_labels=${data.send_labels}`, data.token),
  getUserData: (data) => requests.getWithToken(`users?id=${data}`),
  addMultiple: (data) => requests.postWithJSONToken(`users/multi`, data),
  deleteMultiUser: (data) => requests.postWithJSONToken(`users/multi`, data),
  updateUser: (data) => requests.postWithJSONToken(`users`, data),
  setLanguage: (data) => requests.postWithJSONToken(`users/field`, data),
  updatePassword: (data) => requests.postWithJSONToken(`users/field`, data),
  updateProfileImage: (data) => requests.postWithJSONToken(`users/field`, data),
  userImportFile: (data) => requests.postWithJSONToken(`users/import/step1`, data),
  userImportStep2: (data) => requests.postWithJSONToken(`users/import/step2`, data),
  logOut: (data) => requests.postWithJSONToken(`logout`),
  getEvents: (data) => requests.getWithToken(`events/list/${data.groupKey}?${new URLSearchParams(data).toString()}`),
    getMinEvents: (data) => requests.getWithToken(`events/min/list/${data.groupKey}?${_.has(data,["limit"])? `&limit=${data.limit}`:''}`),
    userSearch: (data) => requests.getWithToken(`users/search${_.has(data, ["groupKey"]) ? `/${data.groupKey}` : ""}?key=${data.key}${ _.has(data, ["search"]) ?`&search[]=${data.search.join("&search[]=")}` :[]}${ _.has(data, ["mode"]) ?`&mode=${data.mode}`:''}${ _.has(data, ["filter"]) ?`&filter=${data.filter}`:''}${ _.has(data, ["pagination"]) ?`&pagination=${data.pagination}`:''}${ _.has(data, ["row_per_page"]) ?`&row_per_page=${data.row_per_page}`:''}${ _.has(data, ["page"]) ?`&page=${data.page}`:''}`),
  entitySearch: (data) => requests.getWithToken(`entities/search?key=${data.key}&type=${data.type}`),
  updateRole: (data) => requests.postWithJSONToken(`events/participants`, data),

    getDraftEvents: () => requests.getWithToken(`events/draft/all`),
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Here all the group related api's are stored to perform the group related task with api
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @type {{
 * updateGroupSettings: (function(*=): Promise<AxiosResponse<*>>),
 * getLabels: (function(*): Promise<AxiosResponse<*>>),
 * getTechnicalSettings: (function(*): Promise<AxiosResponse<*>>),
 * getGroupSettings: (function(*): Promise<AxiosResponse<*>>),
 * tagUpdate: (function(*=): Promise<AxiosResponse<*>>),
 * updateLabels: (function(*=): Promise<AxiosResponse<*>>),
 * getGroupOrganiser: (function(*): Promise<AxiosResponse<*>>),
 * getTags: (function(*): Promise<AxiosResponse<*>>),
 * getGroupUsers: (function(*): Promise<AxiosResponse<*>>),
 * updateTechnicalSettings: (function(*=): Promise<AxiosResponse<*>>)
 * }}
 */
const Group = {
    groupSearch: (data)=> requests.getWithToken(`groups/fetch/list?key=${data.key}`),
  getGroupUsers: (data) => requests.getWithToken(`groups/users/${data.groupKey}?type[]=1&pagination=${data.isPaginated}&page=${data.page}&order=${data.order}&order_by=${data.order_by}&row_per_page=${data.row_per_page}`),
    getGroupOrganiser: (data) => requests.getWithToken(`groups/users${_.has(data, ["groupKey"]) ? `/${data.groupKey}` : ""}?${ _.has(data, ["type"]) ?`&type[]=${data.type.join("&type[]=")}`:''}&pagination=${data.isPaginated}&page=${data.page}&order=${data.order}&order_by=${data.order_by}&row_per_page=${data.row_per_page} ` ),
//   getGroupOrganiser: (data) => requests.getWithToken(`groups/users/${data.groupKey}?${new URLSearchParams(data).toString()}`),
    getTags: (data) => requests.getWithToken(`tags/all/${data}`),
    tagUpdate: (data) => requests.postFileWithToken(`tags`, data),
    getGroupSettings: (groupKey) => requests.getWithToken(`groups/settings/${groupKey}`),
    updateGroupSettings: (data) => requests.postWithJSONToken(`groups/settings`, data),
    getLabels: (groupKey) => requests.getWithToken(`labels/${groupKey}`),
    updateLabels: (data) => requests.postWithJSONToken(`labels`, data),
    getTechnicalSettings: (groupKey) => requests.getWithToken(`groups/settings/technical/${groupKey}`),

  updateTechnicalSettings: (data) => requests.postWithJSONToken(`groups/settings/technical`, data),
  createGroup: (data) =>requests.postWithJSONToken(`groups`, data),
  getGroups: (data) => requests.getWithToken(
      `groups/fetch/list?${_.has(data, ['filter'])  ? `&filter=${data.filter}` :''}${ (_.has(data, ["type"]) &&  !_.isEmpty(data.type) ) ?`&group_type[]=${data.type ? data.type :[]}` :[]}`+
      `${_.has(data,["isPaginated"])?`&isPaginate=${data.isPaginated}` :''}${ _.has(data,['page']) ?`&page=${data.page}`:''}`+
      `${_.has(data,["row_per_page"]) ?`&group_limit=${data.row_per_page}`:'' }${_.has(data, ['order'])  ? `&order=${data.order}` :''}`+
      `${_.has(data, ['order_by'])  ? `&order_by=${data.order_by}` :''}` ), //?${ _.has(data, ["gtype"]) ?`&group_type[]=${data.gtype}` :[]}
  updateGroup : (data) =>requests.postWithJSONToken(`groups`,data),
  getSingleGroupData : (data) =>requests.getWithToken(`groups/single/${data}`),
  deleteGroup : (data) => requests.deleteWithToken(`groups?groupKey=${data.group_key}&delete_mode=${data.delete_mode } ${_.has(data,["confirmation_email"]) ?`&confirmation_email=${data.confirmation_email}`:'' } `)
}


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This object contains all the event related api's
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @type {{
 * getEvent: (function(*): Promise<AxiosResponse<*>>),
 * updateSpaces: (function(*=): Promise<AxiosResponse<*>>),
 * postKeyMoments: (function(*=): Promise<AxiosResponse<*>>),
 * updateSceneryData: (function(*=): Promise<AxiosResponse<*>>),
 * getEventLiveData: (function(*): Promise<AxiosResponse<*>>),
 * updateEventLiveData: (function(*=): Promise<AxiosResponse<*>>),
 * getEventLinks: (function(*): Promise<AxiosResponse<*>>),
 * getParticipant: (function(*): Promise<AxiosResponse<*>>),
 * getSingleEvent: (function(*): Promise<AxiosResponse<*>>),
 * updateDraft: (function(*=): Promise<AxiosResponse<*>>),
 * uploadEventLiveImage: (function(*=): Promise<AxiosResponse<*>>),
 * getSpaces: (function(*=): Promise<AxiosResponse<*>>),
 * getMoments: (function(*): Promise<AxiosResponse<*>>),
 * deleteEventLiveImage: (function(*=): Promise<AxiosResponse<*>>),
 * createEvent: (function(*=): Promise<AxiosResponse<*>>),
 * deleteEvent: (function(*=): Promise<AxiosResponse<*>>),
 * getDraft: (function(*): Promise<AxiosResponse<*>>)
 * }}
 */
const Event = {
    getEvent: (data) => requests.getWithToken(`events?event_uuid=${data}`),
    createEvent: (data) => requests.postWithJSONToken(`events`, data),
    updateSceneryData: (data) => requests.postWithJSONToken(`spaces/scenery`, data),getEventLinks: (data) => requests.getWithToken(`events/links?event_uuid=${data}`),
    getSingleEvent: (data) => requests.getWithToken(`events/find?event_uuid=${data}`),
    getSpaces: (data) => requests.postWithJSONToken(`spaces`, data),
    updateSpaces: (data) => requests.postWithJSONToken(`spaces`, data),
    deleteEvent: (data) => requests.postWithJSONToken(`events`, data),
    postKeyMoments: (data) => requests.postWithJSONToken(`events/moments`, data),
    getMoments: (data) => requests.getWithToken(`events/moments?event_uuid=${data}`),
    getParticipant: (data) => requests.getWithToken(`events/users?event_uuid=${data.event_uuid}${data.key ? `&key=${data.key}` : ''}`),
    getDraft: (data) => requests.getWithToken(`events/draft/find?event_uuid=${data}`),
    updateDraft: (data) => requests.postWithJSONToken(`events/draft/update`, data),
  getEventLiveData: (data) => requests.getWithToken(`events/live/settings?event_uuid=${data.eventUuid}`),
  updateEventLiveData: (data) => requests.postWithJSONToken(`events/live/settings`, data),
  deleteEventLiveImage : (data) => requests.postWithJSONToken('events/live/settings',data),
  uploadEventLiveImage : (data) => requests.postWithJSONToken('event/live/setting', data),
  checkEventCode : (data) => requests.postWithJSONToken('events/validate/join-code', data),

}


export default {
    User,
    Group,
    Event
};
