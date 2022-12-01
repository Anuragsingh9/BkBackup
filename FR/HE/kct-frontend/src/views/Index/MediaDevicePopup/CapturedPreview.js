import React from "react";
import "./MediaDevicePopup.css";
import ImgCropper from "../../NewInterFace/Conversation/UI/Main/Common/ImgCropper/ImgCropper";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will render a image preview component captured by selected device from device selector
 * component.This component has additional features of upload captured image as a profile picture or a button to go
 * on capturing mode to re capture image from a selected camera device.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.onSaveCapturedImage Function to handle the captured image on parent component
 * @param {String} props.capturedImageUrl Variable to hold the Image URL
 * @param {Function} props.onRetake Function to handle the retake functionality so user can retake the image
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
function CapturedPreview(props) {

    return <>
        <img id="grabFrameCanvas" style={
            {
                width: "300px",
                height: '300px',
                borderRadius: "6%",
                border: "1px solid #3b3b3b",
                backgroundPosition: "center",
                backgroundSize: "cover",
                display: "none"
            }
        } />
        {
            props.capturedImageUrl &&
            <ImgCropper
                saveImage={props.onSaveCapturedImage}
                capturedImgURL={props.capturedImageUrl}
            />
        }
        <div className={`form-group text-center mt-5 btn_absolute`}>
            <button type="button" class="btn btn_outline_dark"
                    onClick={props.onRetake}>
                <span>Retake</span>
            </button>
        </div>
    </>
}

export default CapturedPreview;
