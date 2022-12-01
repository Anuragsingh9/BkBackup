import React, {useState} from "react";
import NetworkingController from "./NetworkingController";
import ContentComponentController from "./ContentComponentController";
import PanelTitle from "./PanelTitle";
import ContentController from "./ContentController";
import "./PilotPannel.css";
import Svg from "../../../Svg";
import {connect} from "react-redux";
import ReactTooltip from "react-tooltip";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used to show pilot panel for only pilot
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.handleContentCloseButton To handle the content close button
 * @param {EventData} props.event_data Current event data
 * @param {Object} props.event_meta Current event meta data
 * @param {ContentManagementMeta} props.contentManagementMeta Content Related Props from redux store for current content
 * @param {Number} props.contentManagementMeta.componentVisibility Used to check component visible or not
 * @param {String[]} props.videoLinks Pilot videos link of youtube or vimeo
 * @param {String[]} props.imageLinks Pilot Image player links
 * @param {Function} props.handlePilotSelectVideo To call the parent handler when video is (de)selected
 * @param {Function} props.handlePilotSelectImage To call the parent handler when image is (de)selected
 * @param {ContentManagementMeta} props.contentManagementMeta Content Related Props from redux store for current content
 * @param {Function} props.handleBroadcastToggle To handle the zoom broadcasting when available from moderator
 * @param {Function} props.handleZoomMute Handler to call the zoom mute unmute functionality
 * @returns {JSX.Element}
 * @constructor
 */
let PilotPanel = (props) => {
    const [carrotIcon, setCarrotIcon] = useState(true)

    function pannelScroll() {
        setCarrotIcon(!carrotIcon);
        const pannel = document.getElementById("pilot_pannel");
        if (pannel.style.bottom == "0px") {
            document.getElementById("pilot_pannel").style.bottom = "-140px";
        } else {
            document.getElementById("pilot_pannel").style.bottom = "0px";
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the content close button
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleContentCloseButton = () => {
        props.handleContentCloseButton();
    }
    // onClick={()=>setCarrotIcon(!carrotIcon)} dangerouslySetInnerHTML={{ __html: carrotIcon ? Svg.ICON.carrot_icon_up : Svg.ICON.carrot_Icon }}

    return (

        <div className={"Pilot_pannel_wrap"} id="pilot_pannel" style={{bottom: "-140px", transition: "bottom 1s"}}>
            <div className="scrollerBtn"
                dangerouslySetInnerHTML={{__html: carrotIcon ? Svg.ICON.carrot_icon_up : Svg.ICON.carrot_Icon}}
                onClick={pannelScroll}></div>

            <span className="gradiend_wrap">
                <div className={"container pannel_container"}>
                    <div className={"col-lg-3"}>
                        <PanelTitle
                            event_data={props.event_data} event_meta={props.event_meta}
                        />
                    </div>
                    <div className={"col-lg-3"}>
                        <div className={"col-lg-12"}>
                            <NetworkingController
                                {...props}
                            />
                        </div>

                        <div className={"col-lg-12 mt-5"}>
                            <ContentController
                                {...props}
                                handleContentCloseButton={handleContentCloseButton}
                            />
                        </div>
                    </div>
                    <div className={"col-lg-6"}>
                        {
                            (props.contentManagementMeta.componentVisibility === 1 || props.contentManagementMeta.componentVisibility === true)
                            &&
                            <ContentComponentController
                                videoLinks={props.videoLinks}
                                imageLinks={props.imageLinks}
                                handlePilotSelectVideo={props.handlePilotSelectVideo}
                                handlePilotSelectImage={props.handlePilotSelectImage}
                                event_data={props.event_data}
                                contentManagementMeta={props.contentManagementMeta}
                                handleBroadcastToggle={props.handleBroadcastToggle}
                                handleZoomMute={props.handleZoomMute}
                                isConferenceOn={props.isConferenceOn}
                            />
                        }
                    </div>
                </div>
            </span>
        </div>
    );
}

const mapDispatchToProps = (dispatch) => {
    return {}
}

const mapStateToProps = (state) => {
    return {
        contentManagementMeta: state.NewInterface.contentManagementMeta,
    }
}
PilotPanel = connect(mapStateToProps, mapDispatchToProps)(PilotPanel);
export default PilotPanel;
