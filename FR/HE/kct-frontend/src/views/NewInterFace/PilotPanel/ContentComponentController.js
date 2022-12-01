import React, {useEffect, useState} from "react";
import _ from 'lodash';
import MediaSelector from "./MediaSelector";
import Constants from "../../../Constants";
import "./PilotPannel.css";
import {connect} from 'react-redux';
import moment from 'moment';
import Helper from "../../../Helper";
import Svg from "../../../Svg";
import {useTranslation} from 'react-i18next';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for showing the content management related action buttons
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {String[]} props.videoLinks Pilot videos link of youtube or vimeo
 * @param {String[]} props.imageLinks Pilot Image player links
 * @param {Function} props.handlePilotSelectVideo To call the parent handler when video is (de)selected
 * @param {Function} props.handlePilotSelectImage To call the parent handler when image is (de)selected
 * @param {EventData} props.event_data Event Data for showing event details in pilot panel
 * @param {Function} props.handleBroadcastToggle To handle the zoom broadcasting when available from moderator
 * @param {Function} props.handleZoomMute Handler to call the zoom mute unmute functionality
 * @param {ContentManagementMeta} props.contentManagementMeta Content Related Props from redux store for current content
 * @param {GridMeta} props.gridMeta Current grid visibility variable from redux store
 *
 *  @returns {JSX.Element}
 * @constructor
 */
const ContentComponentController = (props) => {
    const {t} = useTranslation('pilotPannel')
    const [zoomBroadcastVisibility, setZoomBroadcastVisibility] = useState(false);
    const [zoomMuteVisibility, setZoomMuteVisibility] = useState(false);
    const [muteAllBtn, setMuteAllBtn] = useState(false)
    const [contentText, setContentText] = useState("");

    //Zoom allow to On/Off
    const zoomState = props.contentManagementMeta.currentMediaType === Constants.contentManagement.CNT_MGMT_ZOOM_SDK;

    useEffect(() => {
        if (_.has(props.event_data, ['moments'])) {
            props.event_data.moments.map((currentMoment) => {
                const {end_time, start_time} = currentMoment;
                const time_zone = 'Europe/Paris';

                const time = Helper.getTimeUserTimeZone(time_zone, `${props.event_data.event_date} ${start_time}`);
                const endTime = Helper.getTimeUserTimeZone(time_zone, `${props.event_data.event_date} ${end_time}`);
                const currentTime = moment(new Date);
                const startTime = moment(time);
                const endTimedata = moment(endTime);

                if (startTime.diff(currentTime) < 0 && endTimedata.diff(currentTime) > 0) {
                    const contentMoments = props.event_data.moments.filter((moment) => moment.moment_type == 2 || moment.moment_type == 3 || moment.moment_type == 4);
                    if (contentMoments.length > 0) {
                        const zoom_icon_txt = contentMoments[0].moment_type == 4 ? "Meeting" : contentMoments[0].moment_type == 3 ? "Webinar" : contentMoments[0].moment_type == 2 ? "Webinar" : "";
                        setContentText(zoom_icon_txt);
                    }
                }
            })
        }
    }, [])


    useEffect(() => {
        if (_.has(props.event_data, ['embedded_url'])) {
            setZoomBroadcastVisibility(true);
            setZoomMuteVisibility(true);
        }

    }, []);

    useEffect(() => {
        // if zoom sdk is ready to initialize then show the zoom content section
        if (_.has(props.event_data, ['embedded_url']) && !_.isEmpty(props.event_data.embedded_url)) {
            setZoomBroadcastVisibility(true);
            setZoomMuteVisibility(true);
        } else {
            setZoomBroadcastVisibility(false);
            setZoomMuteVisibility(false);
        }
    }, [props.event_data])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To call the handler for the zoom mute for parent component
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleZoomMuteButton = () => {
        try {
            props.handleZoomMute(1);
        } catch (e) {
            console.error('error in muting zoom', e);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To call the handler method for zoom unmute
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleZoomUnMuteButton = () => {
        try {
            props.handleZoomMute(0);
        } catch (e) {
            console.error('error in un-muting zoom ', e);
        }
    }

    return (
        <div className={"row justify-content-center"}>
            <div className={"col-lg-12 row pannel_btn_row"}>
                {
                    props.isConferenceOn &&
                    <div className="zoomBtn_wrap_pannel ">
                        <button
                            onClick={props.handleBroadcastToggle}

                            className={`${zoomState && "pannel_active_media vide"} pannel_zoomBtn  ${zoomBroadcastVisibility ? '' : 'BtnDisabled'} `}
                            disabled={!zoomBroadcastVisibility}
                        >
                            {!zoomBroadcastVisibility && <div className="disabledLayer"></div>}
                            <span style={{height: "26px"}}
                                  dangerouslySetInnerHTML={{__html: Svg.ICON.zoomIcon_pannel_btn}}></span>
                            &nbsp;&nbsp;
                            {contentText}
                        </button>
                        {zoomBroadcastVisibility &&
                        <div className="volume_adjuster_row">
                            <button
                                onClick={handleZoomMuteButton}
                                className={`pannel_zoomMuteBtn`}
                                disabled={false}
                            >
                                <span dangerouslySetInnerHTML={{__html: Svg.ICON.volume_mute_pannel}}></span>
                            </button>
                            <button
                                onClick={handleZoomUnMuteButton}
                                className={`pannel_zoomUnMuteBtn`}
                                disabled={false}
                            >
                                <span dangerouslySetInnerHTML={{__html: Svg.ICON.volume_pannel}}></span>
                            </button>
                        </div>}
                    </div>
                }

                {props.videoLinks.length > 0 &&
                <div className={""}>
                    <MediaSelector
                        data={props.videoLinks}
                        handleMediaChange={props.handlePilotSelectVideo}
                        contentMediaType={Constants.contentManagement.CNT_MGMT_VIDEO}
                        name="video"
                    />
                </div>
                }
                {/* <div className={"col-lg-1 text-right"}>
                Select Image
            </div> */}
                {props.imageLinks.length > 0 &&
                <div className={""}>
                    <MediaSelector
                        data={props.imageLinks}
                        handleMediaChange={props.handlePilotSelectImage}
                        contentMediaType={Constants.contentManagement.CNT_MGMT_IMAGE}
                        name="image"
                    />
                </div>
                }
            </div>
        </div>
    );
}

const mapDispatchToProps = (dispatch) => {
    return {}
}

const mapStateToProps = (state) => {
    return {
        // event_labels: state.page_Customization.initData.labels,
        // spaceHostData: state.NewInterface.interfaceSpaceHostData,
        // spaceData: state.NewInterface.interfaceSpacesData,
        // event_badge: state.NewInterface.interfaceBadgeData,
        // event_data: state.NewInterface.interfaceEventData,
        // isPrivate: state.NewInterface.isPrivate,
        // availabilityHost: state.NewInterface.availabilityHost,
        contentManagementMeta: state.NewInterface.contentManagementMeta,
        gridMeta: state.NewInterface.gridMeta,
    }
}
export default connect(mapStateToProps, mapDispatchToProps)(ContentComponentController);
// export default ContentComponentController;