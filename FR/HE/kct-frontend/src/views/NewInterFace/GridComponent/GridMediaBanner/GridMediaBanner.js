import React from "react";
import "../GridComponent.css";
import {connect} from "react-redux";
import VideoPlayer from "../../VideoPlayer/VideoPlayer";

let GridMediaBanner = (props) => {
    return (
        <>
            {props.eventGraphics.video_explainer
                ? <VideoPlayer url={props.eventGraphics?.video_url} />
                : <div
                    style={{
                        backgroundImage: `url(${props.eventGraphics.video_explainer_alternative_image})`,
                        height: "630px",
                        backgroundSize: "contain",
                        backgroundRepeat: "no-repeat",
                        backgroundPosition: "center",
                        margin: "20px"
                    }}
                />
            }
        </>

    );
};

const mapStateToProps = (state) => {
    return {
        eventGraphics: state.Graphics.eventGraphics,
    };
};

GridMediaBanner = connect(mapStateToProps)(GridMediaBanner);
export default GridMediaBanner;



