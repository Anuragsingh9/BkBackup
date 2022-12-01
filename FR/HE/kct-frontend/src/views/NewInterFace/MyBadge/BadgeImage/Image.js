import React from 'react';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common image component  to render an user's profile image in square shape - 200x200
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {String} props.image Image url to show
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const Image = props => {
    return (
        <div>
            <img src={props.image} alt="image" height="200" width="200" />
        </div>
    )
}

export default Image;