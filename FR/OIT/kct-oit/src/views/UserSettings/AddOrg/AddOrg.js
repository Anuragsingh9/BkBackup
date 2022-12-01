import React from 'react';
import AddUser from '../AddUser/AddUser';
import User from '../../../Models/User'

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used as a container component  which is providing horizontal tab structure for Manage
 * Organiser component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from the parent
 * @param {User} props.user_badge [State] User's badge data from redux store
 * @return {JSX.Element}
 * @constructor
 */
const AddOrg = (props) => {
    return (
        <AddUser {...props} org={props.add} />
    )
}

export default AddOrg;