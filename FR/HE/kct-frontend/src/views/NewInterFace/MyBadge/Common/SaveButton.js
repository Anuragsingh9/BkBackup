import React from 'react';

const SaveButton = ({className, label, onClick}) => {

    return (
        <button className={className} onClick={onClick}>
            {label}
        </button>
    )
}

export default SaveButton;