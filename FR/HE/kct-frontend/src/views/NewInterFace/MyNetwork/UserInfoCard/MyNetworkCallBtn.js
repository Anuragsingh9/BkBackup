import React from 'react';
import ReactTooltip from "react-tooltip";
import Svg from "../../../../Svg";

const MyNetworkCallBtn = (props) => {
    return (
        <>
            <ReactTooltip type="dark" effect="solid" id='my_network_call_btn'/>
            <button
                onClick={() => {
                }}
                className=""
                type="button"
            >
                <span className="grey-private-btn" data-for='my_network_call_btn' data-tip={"Dataaaa"}>
                    <span
                        className="svgicon"
                        dangerouslySetInnerHTML={{__html: Svg.ICON.enter_meeting}}
                    >
                    </span>
                </span>
            </button>
        </>
    )
}


export default MyNetworkCallBtn;

