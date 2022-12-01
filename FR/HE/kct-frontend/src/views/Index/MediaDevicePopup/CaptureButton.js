import React from "react";
import "./MediaDevicePopup.css";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a button component used to perform submit action for all selected devices from media device
 * selector component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param props
 * @param {Boolean} props.disabled To indicate if button is enabled or disabled
 * @param {String} props.title Title of button
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
function MediaDevicePopup(props) {
    return (<div className={`form-group text-center mt-5`}>
            <button
                className="btn btn_outline_dark"
                disabled={props.disabled}
                type={"submit"}
            >
                <span>{props.title}</span>
            </button>
        </div>
    )
}

export default MediaDevicePopup;
