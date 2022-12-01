// TODO GOURAV DOCUMENTATION


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
import Helper from "../Helper";
import _ from 'lodash';

var querystring = require('querystring');

const BASE_ROOT = Helper.findBaseRoot('v4');

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
    if (error.response !== undefined && error.response.status === 401) {
        let newLoc = window.location.href && window.location.href.includes('oit')
            ? window.location.href.split('/oit')[0]
            : window.location.href;
        localStorage.removeItem('oitToken');
        if (newLoc) {
            window.location.replace(`${newLoc}/signin`)
        }
    }
    return Promise.reject(error);
});


const authRequests = {
    get: (url) =>
        axios.get(
            `${BASE_ROOT}${url}`,
            {
                headers: {
                    Accept: 'Application/json',
                    Authorization: `Bearer ${localStorage.getItem('oitToken')}`
                },
            }
        ),
    postJSON: (url, jsonObject, config = null) =>
        axios.post(`${BASE_ROOT}${url}`,
            jsonObject,
            {
                config,
                headers: {
                    "Content-Type": "application/json",
                    'Accept': 'Application/json',
                    Authorization: `Bearer ${localStorage.getItem('oitToken')}`
                },
            }
        ),
}

const requests = {
    get: url => axios.get(`${BASE_ROOT}${url}`),
    postFormData: (url, formData, config = null) =>
        axios.post(`${BASE_ROOT}${url}`, querystring.stringify(formData), {
            ...config,
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            withCredentials: true,
        }),


    postWithToken: (url, body, config = null) =>
        axios.post(`${BASE_ROOT}${url}`, querystring.stringify(body), {
            config,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                'Accept': 'Application/json',
                Authorization: `Bearer ${localStorage.getItem('oitToken')}`
            },

        }),

    getWithToken: (url, body, config = null) =>
        axios.get(`${BASE_ROOT}${url}`, {
            // config,
            headers: {'Accept': 'Application/json', Authorization: `Bearer ${localStorage.getItem('oitToken')}`},
        }),
    getInitialWithToken: (url, token) =>
        axios.get(`${BASE_ROOT}${url}`, {
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
        axios.post(`${BASE_ROOT}${url}`, body, {
            config,

            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            withCredentials: true
        }),

    rawpostapi: (url, body, config = null) =>
        axios.post(`${BASE_ROOT}${url}`, body, {
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
        axios.post(`${BASE_ROOT}${url}`, body, {
            // config,
            headers: {
                'content-type': 'multipart/form-data',
                'Accept': 'Application/json',
                Authorization: `Bearer ${localStorage.getItem('oitToken')}`
            },
        }),
    postWithFile: (url, body, config = null) =>

        axios.post(`${BASE_ROOT}${url}`, body, {
            headers: {'content-type': 'multipart/form-data'},

        }),

    //for upload via progress bar
    globleApipostWithFile: (url, body, config = null) =>

        axios.post(`${GLOBLE_API_ROOT}${url}`, body, {
            headers: {'content-type': 'multipart/form-data'},
            withCredentials: true,
        }),
    postWithFileProg: (url, body, config) =>

        axios.post(`${BASE_ROOT}${url}`, body, {
            headers: {'content-type': 'multipart/form-data', 'Accept': 'Application/json'},
            withCredentials: true,
            onUploadProgress: config,
        }),
    delete: (url) => axios.delete(`${BASE_ROOT}${url}`),
    deleteWithToken: (url) => axios.delete(`${BASE_ROOT}${url}`, {
        headers: {
            'content-type': 'multipart/form-data',
            'Accept': 'Application/json',
            Authorization: `Bearer ${localStorage.getItem('oitToken')}`
        },

    })
};



const Event = {
    getEvent: (eventUuid) => authRequests.get(`events?event_uuid=${eventUuid}`),
    createEvent: (jsonData) => authRequests.postJSON(`events`, jsonData),
    getEventInitData : (eventUuid) => authRequests.get(`events-init${eventUuid ? '?event_uuid=' + eventUuid : ''}`),
    getEventUsers: (data) => authRequests.get(`users?event_uuid=${data.event_uuid}&key=${data.key}&pagination=${data.isPaginated}&row_per_page=${data.rowPerPage}&page=${data.page}&order_by=${data.orderBy}&order=${data.order}${data.event_participants ? `&event_participants=${data.event_participants}` : ''}`),
    getAnalyticsData: (data) => authRequests.get(`events/analytics?groupKey[]=${data.groupKey.join("&groupKey[]=")}&pagination=${data.pagination}&row_per_page=${data.row_per_page}&page=${data.page}&from_date=${data.from_date}&to_date=${data.to_date}${ _.has(data, ["key"]) ?`&key=${data.key}`:''}${ _.has(data, ["order_by"]) ?`&order_by=${data.order_by}&order=${data.order}`:''}`),
    getEventAnalytics: (data) => authRequests.get(`events/analytics/single?${Helper.toQueryParam(data)}`),
}


export default {
    Event
};
