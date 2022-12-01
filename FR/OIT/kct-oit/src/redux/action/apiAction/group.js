/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Here all the group related api dispatcher are stored
 * To call the api of group related, this file will provide the redux action dispatcher which can be mapped withing the
 * props of component for easy access.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @module GroupReduxActions
 */
import agents from "../../../agents"

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to fetch all users of group
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} groupKey Group unique key
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getGroupUsers = (groupKey) => dispatch => {
    return agents.Group.getGroupUsers(groupKey)
}

/**
 *---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to fetch all pilots,co-pilots and owner of group
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} dataObject Unique group key
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getGroupOrganiser = (dataObject) => dispatch => {
    return agents.Group.getGroupOrganiser(dataObject)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to fetch all tags of a group
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} id Group unique ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getTags = (id) => dispatch => {
    return agents.Group.getTags(id)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to update a tag
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} id Group unique ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const updateTag = (id) => dispatch => {
    return agents.Group.tagUpdate(id)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to fetch all settings of a specific group
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} groupKey Group unique Key
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getGroupSettings = (groupKey) => dispatch => {
    return agents.Group.getGroupSettings(groupKey)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to update settings of group
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} id Group unique ID
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const updateGroupSettings = (id) => dispatch => {
    return agents.Group.updateGroupSettings(id)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to fetch all tags related to a group
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} groupKey Group unique Key
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getLabels = (groupKey) => dispatch => {
    return agents.Group.getLabels(groupKey)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to update labels
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {LabelObj} data Labels data object
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const updateLabels = (data) => dispatch => {
    return agents.Group.updateLabels(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to fetch all technical settings related to a group
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} groupKey Group key
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const getTechnicalSettings = (groupKey) => dispatch => {
    return agents.Group.getTechnicalSettings(groupKey);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is responsible for calling the API to update technical settings of group
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {LicenseObj} data License data object
 * @return {function(*): Promise<AxiosResponse<*>>}
 */
const updateTechnicalSetting = (data) => dispatch => {
    return agents.Group.updateTechnicalSettings(data);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To create a group
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} data Data of group
 * @returns {function(*): Promise<AxiosResponse<*>>}
 */
const createGroup = (data) => dispatch => {
    return agents.Group.createGroup(data);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To get group
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} data Group key
 * @returns {function(*): Promise<AxiosResponse<*>>}
 */
const getGroups = (data) => dispatch => {
    return agents.Group.getGroups(data);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is used for update the group
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} data Group key
 * @returns {function(*): Promise<AxiosResponse<*>>}
 */
const updateGroup = (data) => dispatch => {
    return agents.Group.updateGroup(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is used for get single group data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} data Group key
 * @returns {function(*): Promise<AxiosResponse<*>>}
 */
const getSingleGroupData = (data) => dispatch => {
    return agents.Group.getSingleGroupData(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is used for group search
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} data Group name
 * @returns {function(*): Promise<AxiosResponse<*>>}
 */
const groupSearch = (data) => dispatch => {
    return agents.Group.groupSearch(data)
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is used for delete group
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} data Group key
 * @returns {function(*): Promise<AxiosResponse<*>>}
 */
const deleteGroup = (data) => dispatch => {

    return agents.Group.deleteGroup(data)
}


const groupAction = {
    getGroupOrganiser,
    getGroupUsers,
    getTags,
    updateTag,
    getGroupSettings,
    updateGroupSettings,
    getLabels,
    updateLabels,
    getTechnicalSettings,
    updateTechnicalSetting,
    createGroup,
    getGroups,
    updateGroup,
    getSingleGroupData,
    groupSearch,
    deleteGroup
}

export default groupAction;