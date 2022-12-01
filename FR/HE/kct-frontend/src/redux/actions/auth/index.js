/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file is used for calling the api methods via dispatcher and export them as dispatcher action of
 * redux
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @module
 */

import KeepContactagent from "../../../agents/KeepContactagent";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To get the event details by event uuid
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} id Event Uuid to get
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const getEventDetails = (id) => dispatch => {
    return KeepContactagent.Auth.getEventDetails(id)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To send the login data to backend so user can be logged in and access token can be fetched to use in
 * future api's in single environment of application
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {FormData} data Api data to send along with login @see Swagger API Documentation for request and response
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const login = (data) => dispatch => {
    return KeepContactagent.Auth.login(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To send the registration data for creating a user account with event uuid
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {FormData} data Api data to send along with login @see Swagger API Documentation for request and response
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const registerBasic = (data) => dispatch => {
    return KeepContactagent.Auth.registerBasic(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To get the events list based on the tense of the current list
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {FormData} data Api data to send along with login @see Swagger API Documentation for request and response
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const getDefaultEventList = (data) => dispatch => {
    return KeepContactagent.Auth.getDefaultEventList(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To send the otp of the user for verifying the OTP details
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {FormData} data Api data to send along with login @see Swagger API Documentation for request and response
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const registerOtp = (data) => dispatch => {
    return KeepContactagent.Auth.registerOtp(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To enter in the space for changing the space
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {FormData} data Api data to send along with login @see Swagger API Documentation for request and response
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const registerSpaceMood = (data) => dispatch => {
    return KeepContactagent.Auth.registerSpaceMood(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description to ask for the sending the otp again to email for verifying the email
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {FormData} data Api data to send along with login @see Swagger API Documentation for request and response
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const resendOtp = (data) => dispatch => {
    return KeepContactagent.Auth.resendOtp(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To get the data on the otp page
 * As when user visits the otp page there is encrypted key present in url by using that key front client can fetch the
 * event uuid and user email by this api
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {FormData} data Api data to send along with login @see Swagger API Documentation for request and response
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const getOtpData = (data) => dispatch => {
    return KeepContactagent.Auth.getOtpData(data);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To send the forget password link to provided email of user so a reset password link can sent over that
 * email and user can reset the password
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {FormData} data Api data to send along with login @see Swagger API Documentation for request and response
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const sendForgetPasswordLink = (data) => dispatch => {
    return KeepContactagent.Auth.sendForgetPasswordLink(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This api is actually using to reset the password after getting the request of sending password reset
 * link user will come on reset password page and this api will actually reset the password with previous one
 * and validate it
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {FormData} data Api data to send along with login @see Swagger API Documentation for request and response
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const resetPassword = (data) => dispatch => {
    return KeepContactagent.Auth.resetPassword(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To change the password of user personal account
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {FormData} data Api data to send along with login @see Swagger API Documentation for request and response
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const changePassword = (data) => dispatch => {
    return KeepContactagent.Auth.changePassword(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description As mentioned this api is used to vanish the user token on backend side so the token becomes invalid for
 * further use
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {FormData} data Api data to send along with login @see Swagger API Documentation for request and response
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const logout = (data) => dispatch => {
    return KeepContactagent.Auth.logout(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To get the organisation data by hitting this api there will be basic details of application name
 * and current account name to display on front side
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const getOrganisation = () => dispatch => {
    return KeepContactagent.Auth.getOrganisation()
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To get the user data this will send the token and demand for the user badge data
 * Here user badge data keyword represents the all basic data of user
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const getUsersData = () => dispatch => {
    return KeepContactagent.Auth.getUsersData()
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To change the language of the application so next time user visits the application user will see the
 * same languages as selected last time
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {FormData} data Api data to send along with login @see Swagger API Documentation for request and response
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const changeLanguage = (data) => dispatch => {
    return KeepContactagent.Auth.changeLanguage(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To get the data for the Event registration to display the user current selected data with respect to
 * current event as well as basic details of user
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Number} uuid Id of user to fetch the data
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const getUserInfo = (uuid) => dispatch => {
    return KeepContactagent.Auth.getUserInfo(uuid)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To send the invite to provided email addresses for the event join
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {FormData} data Api data to send along with login @see Swagger API Documentation for request and response
 * @returns {function(*): Promise<*>}
 * @method
 */
const addInvite = (data) => dispatch => {
    return KeepContactagent.Auth.addInvite(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To join the event from the event registration, this api is used for adding a user into the event
 * with either selected or default space (if not selected anything)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {FormData} data Api data to send along with login @see Swagger API Documentation for request and response
 * @returns {Function} Dispatcher with api trigger
 * @method
 */
const addJoin = (data) => dispatch => {
    return KeepContactagent.Auth.addJoin(data)
}


const AuthActions = {
    getEventDetails,
    login,
    registerBasic,
    getDefaultEventList,
    registerOtp,
    registerSpaceMood,
    resendOtp,
    getOtpData,
    sendForgetPasswordLink,
    resetPassword,
    logout,
    getOrganisation,
    getUsersData,
    getUserInfo,
    addInvite,
    changeLanguage,
    addJoin,
    changePassword
}
export default AuthActions;