import NetworkInfoCard from "../UserInfoCard/NetworkInfoCard";
import React from "react";


const Recents = (props) => {

    return (
        <>
            {props?.data.map((details, index) => (
                <NetworkInfoCard data={details} curentTabb={props.currentTabValue}/>
            ))}
        </>
    )
}

export default Recents;