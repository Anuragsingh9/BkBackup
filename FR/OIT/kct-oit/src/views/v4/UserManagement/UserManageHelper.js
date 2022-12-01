import eventV4Api from "../../../redux/action/apiAction/v4/event";
import Helper from "../../../Helper";


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is used for deleting the event users
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Passed from parent component
 * @param {Array} props.toDeleteUserData Array of user id's to delete
 * @param {String} props.current_event_uuid Current event uuid
 * @param {Function} props.alert Function is used to show notification
 * @param {Function} props.alert.show Function is used to show notification
 * @param {Function} props.t Function is used for localization
 * @param {Function} dispatch Used for hit the api
 * @param {Function} callBack call back function used for synchronous api calling
 */
const deleteUser = (props, dispatch, callBack) => {

    const data = {
        users: props.toDeleteUserData,
        event_uuid: props.current_event_uuid,
        _method: "DELETE",
    };

    dispatch(eventV4Api.updateUserRole(data)).then((res) => {
        props.alert.show(`${props.t("confirm:successDelete")}`, {type: 'success'})
        callBack();
    }).catch((err) => {
        Helper.handleApiError(err, props.alert);
    });
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is used for updating the user role of the event users
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Passed from parent component
 * @param {String} props.current_event_uuid Current event uuid
 * @param {Function} props.dispatch Used for hit the api
 * @param {Function} props.alert Function is used to show notification
 * @param {Function} props.alert.show Function is used to show notification
 * @param {Function} props.t Function is used for localization
 * @param {Array} user Array of user's id
 * @param {Number} type Role of the user
 * @param {Function} callBack call back function used for synchronous api calling
 */
const updateUserRole = (props, user, type, callBack) => {
    const event_uuid = props.current_event_uuid;
    const group_key = props.match.params.gKey;
    const data = {
        users: user,
        role: type,
        event_uuid: event_uuid,
        group_key: group_key,
        _method: "PUT",
    };
    props.dispatch(eventV4Api.updateUserRole(data)).then((res) => {
        props.alert.show(`${props.t("confirm:successRoleUpdate")}`, {type: 'success'})
        callBack();
    }).catch((err) => {
        // Helper.handleApiError(err, props.alert);
    });
}

let UserManageHelper = {
    deleteUser,
    updateUserRole
}

export default UserManageHelper;