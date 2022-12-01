import React from "react";

const UserCallLogs = (props) => {

    return (
        <>
            <p>{props.userCallData.name}</p>
            <div>
                {props?.currentTab === 'recent' ?
                    <>
                        <i className="fa fa-phone"></i>

                        <span className="call-timing">{props.userCallData.callTime}</span>
                    </>
                    :
                    <span className="user-company">{props.userCallData.position},{props.userCallData.company}</span>
                }
            </div>
        </>
    );
}

export default UserCallLogs;