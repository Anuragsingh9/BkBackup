/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Here some of the user related api dispatcher are stored
 * To call the api of user related, this file will provide the redux action dispatcher which can be mapped withing the
 * props of component for easy access.
 * ---------------------------------------------------------------------------------------------------------------------
 */
import OitAgent from "../../../agents/index";

const updatePassword = (data) => dispatch => {
    return OitAgent.User.updatePassword(data)
}


const AuthAction = {
    updatePassword
}

export default AuthAction;