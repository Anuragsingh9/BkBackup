import React from 'react';
import "./HostSection.css";
import ZoomComponentView from "./ZoomPlayer/ZoomComponentView";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description - This is a child component which is used to Component renders Host conference section.  which consist
 * of a common component ZoomComponentView to display and handles display visibility.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @class
 * @component
 * @param {Object} props.alert Reference object for displaying notification popup
 * @param {Boolean} props.display To indicate to show or hide the zoom popup
 */
class Host extends React.Component {
    render() {
        return (
            <div
                className={`col-sm-12 col-md-12 videoframe tt kct-customization zoom-height ${this.props.display ? '' : 'hidden'}`}>
                <div className="host-video-frame no-texture" id="host-video-frame">
                    <ZoomComponentView
                        alert={this.props.alert}
                    />
                </div>
            </div>
        );
    }
}


export default Host;
