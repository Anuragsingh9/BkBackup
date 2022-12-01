import React from 'react';
import Svg from '../../../../../../Svg.js';
import ReactTooltip from "react-tooltip";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Common component to render buttons for conversation button component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Function} onClick
 * @param {String} dataTip
 * @param {JSX} icon
 * @param {Boolean} disabled
 * @param {String} dataFor
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const Button = ({onClick, dataTip, icon, disabled, dataFor}) => {
    return (
        <>
            <ReactTooltip type="dark" effect="solid" id={dataFor} />
            <button
                type="button"
                className="control-button no-texture video-buttons"
                onClick={onClick}
                data-for={dataFor}
                data-tip={dataTip}
                disabled={disabled}
                dangerouslySetInnerHTML={{__html: Svg.ICON[icon]}}>
                {/* dangerouslySetInnerHTML={{ __html:'<div class="loader_custom"></div>' }}> */}

            </button>
        </>
    )
}

export default Button;