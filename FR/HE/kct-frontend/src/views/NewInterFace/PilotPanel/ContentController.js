import React, {useEffect, useState} from "react";
import Svg from "../../../Svg";
import {connect} from 'react-redux';
import ReactTooltip from "react-tooltip";
import { useTranslation } from 'react-i18next';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Main pilot panel content controller to show the button for content management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {ContentManagementMeta} props.contentManagementMeta Content Related Props from redux store for current content
 * @param {GridMeta} props.gridMeta Current grid visibility variable from redux store
 *
 * @returns {JSX.Element}
 * @constructor
 */
const PilotPanel = (props) => {
    const { t } = useTranslation('pilotPannel')
    const [btn, setBtn] = useState(true);

    useEffect(() => {
        // to handle the button visibility when there is content enabled in event
        setBtn(props.contentManagementMeta.componentVisibility);
    }, [props.contentManagementMeta.componentVisibility]);

    return (
        <div className={"content_pannel_btn"}>
            <div className={"content_controller_pannel"}>
                <div className="center_element">
                    <div dangerouslySetInnerHTML={{__html: Svg.ICON.content_panel_icon}}></div>
                    &nbsp;
                    <div>CONTENT</div>
                </div>
                <ReactTooltip type="dark" effect="solid" id={`enter_left`} />
                <button className="pannel_controllerBtn"
                        onClick={props.handleContentCloseButton}
                        dangerouslySetInnerHTML={{__html: btn ? Svg.ICON.powerOn_pannel_icon : Svg.ICON.powerOff_panel_icon}}
                    data-for='enter_left'
                    data-tip={btn ? t("Zoom Main btn off") : t("Zoom Main btn on")}
                >
                </button>
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
// export default PilotPanel;
export default connect(mapStateToProps, mapDispatchToProps)(PilotPanel);