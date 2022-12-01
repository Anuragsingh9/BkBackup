import React from 'react';
import ZoomComponentView from "./ZoomComponentView";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component renders zoom sdk player.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Object} props.alert Reference object for displaying notification popup
 * @class
 * @component
 * @constructor
 */
class ZoomPlayer extends React.Component {
    render() {
        return (
            <ZoomComponentView
                alert={this.props.alert}
            />
        );

    }
}

export default ZoomPlayer;

