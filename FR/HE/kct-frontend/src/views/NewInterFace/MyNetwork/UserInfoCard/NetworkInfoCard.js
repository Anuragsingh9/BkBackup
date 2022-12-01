import React from "react";
import UserAvatar from "./UserAvatar";
import UserCallLogs from "./UserCallLogs";
import MyNetworkCallBtn from "./MyNetworkCallBtn";
import EllipsisBtn from "./EllipsisBtn";


const NetworkInfoCard = (props) => {

    const recentOptions = ["Add to MyList", "View Card"];
    const myListOptions = ["Remove", "View Card"];
    const options = props.curentTabb === "recent" ? recentOptions : myListOptions;

    return (
        <div className="info-card">
            <div className="info-card-item">
                <UserAvatar image={props.data.avatar}/>
            </div>
            <div className="info-card-item user-call-info">
                <UserCallLogs userCallData={props.data.callData} currentTab={props.curentTabb}/>
            </div>
            <div className="info-card-item user-call-btn">
                <MyNetworkCallBtn/>
            </div>
            <div className="info-card-item options">
                <EllipsisBtn options={options}/>
            </div>
        </div>
    );

}

export default NetworkInfoCard;