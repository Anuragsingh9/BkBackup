import React, {useEffect, useState} from "react";
import Svg from "../../../Svg";
import _ from 'lodash';
import {connect} from 'react-redux';
import ReactTooltip from "react-tooltip";
import {useTranslation} from 'react-i18next';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Networking related action are stored here
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {GridMeta} props.gridMeta Current grid visibility variable from redux store
 * @param {ConversationMeta} props.conversationMeta Current conversation state from redux
 * @param {ContentManagementMeta} props.contentManagementMeta Content Related Props from redux store for current content
 * @param {Function} props.handlePilotPanelClose To handle when the pilot is closed and call the parent method
 * @param {Function} props.handlePilotPanelMute To handle when the pilot is muted the conversation
 *
 * @returns {JSX.Element}
 * @constructor
 */
const NetworkingController = (props) => {
    const {t} = useTranslation('pilotPannel')
    const [btn, setBtn] = useState(true);
    const [mute, setMute] = useState(true)

    useEffect(() => {
        if (_.has(props, ['gridMeta'])) {
            setBtn(props.gridMeta.visible);
        }

    }, [props.gridMeta.visible]);

    useEffect(() => {
        // if parent has mute settings then syncing the same with child component for internal use
        if (_.has(props, ['conversationMeta'])) {
            setMute(props.conversationMeta.mute);
        }

    }, [props.conversationMeta.mute]);

    return (
        <div className={"networking_pannel_btn"}>
            <div className={"networking_controller_pannel"}>
                <div>
                    <span dangerouslySetInnerHTML={{__html: Svg.ICON.networking_grid_icon}}></span>
                    <span>NETWORKING</span>
                </div>
                <div className="center_element">
                    <ReactTooltip type="dark" effect="solid" id={`powerbtn`} />
                    <button className="pannel_controllerBtn"
                            onClick={props.handlePilotPanelClose}
                            dangerouslySetInnerHTML={{__html: btn ? Svg.ICON.powerOn_pannel_icon : Svg.ICON.powerOff_panel_icon}}
                            data-for='powerbtn'
                            data-tip={btn ? t("Networking btn off") : t("Networking btn on")}
                    >
                    </button>
                    <ReactTooltip type="dark" effect="solid" id={`vloumebtn`} />
                    <button className="pannel_controllerBtn"
                            onClick={props.handlePilotPanelMute}
                            dangerouslySetInnerHTML={{__html: mute ? Svg.ICON.volume_mute_pannel : Svg.ICON.volume_pannel}}
                            data-for='vloumebtn'
                            data-tip={mute ? t("Mute btn off") : t("Mute btn on")}
                    >
                    </button>
                </div>
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
        conversationMeta: state.NewInterface.conversationMeta,
        contentManagementMeta: state.NewInterface.contentManagementMeta,
        gridMeta: state.NewInterface.gridMeta,
    }
}
// export default PilotPanel;
export default connect(mapStateToProps, mapDispatchToProps)(NetworkingController);

// export default NetworkingController;