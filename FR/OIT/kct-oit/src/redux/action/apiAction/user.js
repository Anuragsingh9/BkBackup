/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Here all the user related api dispatcher are stored
 * To call the api of user related, this file will provide the redux action dispatcher which can be mapped withing the
 * props of component for easy access.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @module UserReduxActions
 */
import agents from "../../../agents"
import DesignDataWithReset from "../../../Models/DesignDataWithReset"
import UserDataObject from "../../../Models/UserDataObject"
import LanguageObj from "../../../Models/LanguageObj"
import FileObject from "../../../Models/FileObject"

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to get self user data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} id user ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getSelfUserById = (id) => dispatch => {
    return agents.User.getSelfUserById(id)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to add multiple users at a time in system
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} data Data of multiple users
 * @param {Number} data.allow_update State to manage edit user functionality
 * @param {Number} data.group_id Current group ID
 * @param {Number} data.group_role Current group role
 * @param {Object[]} data.user Array of object where each object contain user details <br/>
 * ex - {"fname": "Asdasd","email": "sdfsdfsdfsdf@gmail.com","lname": "Asdas"}
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const addMultiple = (data) => dispatch => {
    return agents.User.addMultiple(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to fetch an user's data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {DesignDataWithReset} data This component received content settings related values and functions from it's
 * parent file("DesignSetting.js").
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getUserData = (data) => dispatch => {
    return agents.User.getUserData(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to update an user's data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {UserDataObject} data User details object
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const updateUser = (data) => dispatch => {
    return agents.User.updateUser(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to set user's language through out the application
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {LanguageObj} data Language object
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const setLanguage = (data) => dispatch => {
    return agents.User.setLanguage(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to update password of an user
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} data Required details for update password
 * @param {String} data._method Method name
 * @param {String} data.user_id Current user ID
 * @param {String} data.field Field name
 * @param {String} data.value Field value
 * @param {String} data.current_password Current password value
 * @param {String} data.password_confirmation Confirm password value
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const updatePassword = (data) => dispatch => {
    return agents.User.updatePassword(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to update user's profile picture
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} data Required details for update avatar
 * @param {String} data._method Method name
 * @param {String} data.user_id Current user ID
 * @param {String} data.field Field name
 * @param {String} data.avatar Avatar data
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const updateProfileImage = (data) => dispatch => {
    return agents.User.updateProfileImage(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to delete multiple users at a time
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} data Data of multiple users
 * @param {Number} data.allow_update State to manage edit user functionality
 * @param {Number} data.group_id Current group ID
 * @param {Number} data.group_role Current group role
 * @param {Object[]} data.user Array of object where each object contain user details <br/>
 * ex - {"fname": "Asdasd","email": "sdfsdfsdfsdf@gmail.com","lname": "Asdas"}
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const deleteMultiUser = (data) => dispatch => {
    return agents.User.deleteMultiUser(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to upload the excel file used in import process
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {FileObject} data File object data for import user
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const userImportFile = (data) => dispatch => {
    return agents.User.userImportFile(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API which handles the step 2 of Import user process
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} data User date after import process
 * @param {String} data.file_name File name
 * @param {Function} data.aliases Generate data function
 * @param {String} data.group_id Group ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const userImportStep2 = (data) => dispatch => {
    return agents.User.userImportStep2(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to fetch all events
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} data Current group ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getEvents = (data) => dispatch => {
    return agents.User.getEvents(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To get the minimum events
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} data Current group ID
 * @returns {function(*): Promise<AxiosResponse<*>>}
 */
const getMinEvents =  (data) => dispatch => {
    return agents.User.getMinEvents(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to logout an user from the application
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const logOut = () => dispatch => {
    return agents.User.logOut()
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to search user through out the application
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} data Object that contain details to search users
 * @param {String} data.key Unique key
 * @param {String} data.mode Data mode
 * @param {String} data.group_id Current group ID
 * @param {String} data.filter Text which need to be filter
 * @param {String[]} data.search Array of object that contain column in which search will work
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const userSearch = (data) => dispatch => {
    return agents.User.userSearch(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to search entity
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} data Details to search emails that added already in the system.
 * @param {String} data.key Value of search term
 * @param {Number} data.type User type
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const entitySearch = (data) => dispatch => {
    return agents.User.entitySearch(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to update role of a user in event
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} data Details to update a role
 * @param {Number} data.users User ID
 * @param {String} data.role User current role
 * @param {String} data.event_uuid Event unique ID
 * @param {String} data._method Method name
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const updateRole = (data) => dispatch => {
    return agents.User.updateRole(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to get all draft events
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getDraftEvents = (data) => dispatch => {
    return agents.User.getDraftEvents(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to allow user to login into the application
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} data Data to login
 * @param {Object} data._method method name
 * @param {Object} data.email User's email address
 * @param {Object} data.password User's password
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const login = (data) => dispatch => {
    return agents.User.login(data)
}

/**
 *---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to handle the forget password request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} data Details for forget password action
 * @param {Object} data._method Method name
 * @param {Object} data.email USer's email address
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const forgetPassword = (data) => dispatch => {
    return agents.User.forgetPassword(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to allow user to change the default password
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} data Details required for set password action
 * @param {String} data.method Method name
 * @param {String} data.email User's current email address
 * @param {String} data.current_password Current password value
 * @param {String} data.password_confirmation Confirm password value
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const setPassword = (data) => dispatch => {
    return agents.User.setPassword(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API which handle the request for reset password
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} data Details required for reset password action
 * @param {String} data.method Method name
 * @param {String} data.email User's current email address
 * @param {String} data.current_password Current password value
 * @param {String} data.password_confirmation Confirm password value
 * @param {String} data.identifier Extract identifier from URL
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const resetPassword = (data) => dispatch => {
    return agents.User.resetPassword(data)
}

const userAction = {
    getSelfUserById,
    addMultiple,
    getUserData,
    updateUser,
    setLanguage,
    updatePassword,
    updateProfileImage,
    deleteMultiUser,
    userImportFile,
    userImportStep2,
    logOut,
    getEvents,
    userSearch,
    entitySearch,
    updateRole,
    getDraftEvents,
    login,
    forgetPassword,
    setPassword,
    resetPassword,
    getMinEvents,
  
}

export default userAction;