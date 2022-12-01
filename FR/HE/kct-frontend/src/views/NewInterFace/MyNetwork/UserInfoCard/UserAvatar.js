import React from "react";

const UserAvatar = (props) => {

    return (
        <div className="user-avatar">
            <img src={props.image}/>
        </div>
    );
}

export default UserAvatar;