import React from 'react';
import AddUser from '../AddUser/AddUser.js';
import ImportUser from '../ImportUser/index.js';
import VerticalTabs from '../../Common/VerticalTab/VerticalTab';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used as a container component  which is providing vertical tab structure for Add
 * User and Import User components.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @return {JSX.Element}
 */
const AddUserContainer = () => {
    const tabData = [
        {
            label: 'Add User',
            href: '/contacts',
            child: <AddUser />,
            variant: 'fullWidth',
        },
        {
            label: 'Import User',
            href: '/add-user',
            child: <ImportUser />

        },
    ]

    return (
        <div className="verticleTabDiv">
            <VerticalTabs fullWidth tabData={tabData} />
        </div>

    )
}

export default AddUserContainer;