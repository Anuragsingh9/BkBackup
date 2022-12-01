import React, {useEffect, useState} from "react";
import TabContext from "@mui/lab/TabContext";
import Constants from "../../../Constants";
import {Select} from "@material-ui/core";
import UserTabComponent from "./UserTabComponent";
import AddUserModal from "./AddUserModal";
import {connect, useDispatch} from "react-redux";
import eventAction from "../../../redux/action/reduxAction/event";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used for user management and manage the participant
 * and event team list under the user management tab
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props props passed from parent
 * @constructor
 */
let UserManagementTopBar = (props) => {
    const [value, setValue] = React.useState(Constants.userManagementTabType.EVENT_TEAM);
    // This state is used for show the model of add user manually either import user
    const [mode, setMode] = useState(0);

    // This state is used for to add user popup show or not
    const [addUserModalOpen, setAddUserModalOpen] = useState(false)

    // This state is used for refetch the users for add user manually and import user
    const [fetch, setFetch] = useState(false);


    useEffect(() => {
        setValue(props.userTabValue || Constants.userManagementTabType.EVENT_TEAM)
    }, []);

    return (
        <TabContext value={value}>
            <div className='verticleTab__EventManage UserTablePage'>

                {/*Show the user Table list*/}
                <UserTabComponent
                    value={value}
                    setValue={setValue}
                    fetch={props.fetch_user}
                    {...props}
                />

            </div>

            {/*Add user modal*/}
            <AddUserModal
                mode={mode}
                setMode={setMode}
                setAddUserModalOpen={setAddUserModalOpen}
                addUserModalOpen={addUserModalOpen}
                setFetch={props.updateAddUserPopUpDisplay}
                {...props}
            />

        </TabContext>
    )

};

const mapStateToProps = (state) => {
    return {
        fetch_user: state.Event.add_user_pop_up.fetch,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        updateAddUserPopUpDisplay : (display,mode,fetch) => dispatch(eventAction.updateAddUserPopUpDisplay(display,mode,fetch)),
    }
}

UserManagementTopBar = connect(mapStateToProps, mapDispatchToProps)(UserManagementTopBar);

export default UserManagementTopBar;





