
import React, {useState, useRef} from 'react';
import CloseIcon from '@mui/icons-material/Close';
import "./VideoUploadPopup.css";
import {
    Dialog,
    DialogContent,
    DialogTitle,
    IconButton,
} from "@mui/material";


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a wrapper modal box component to show video upload form(Form to upload a video for content
 * player in live tab component - vimeo/youtube).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from the parent component
 * @param {Function} props.closePopup This method will close the video upload popup
 * @param {Boolean} props.popupVisibility To indicate the visibility of popup
 * @returns {JSX.Element}
 * @constructor
 */
var VideoUploadPopup = (props) => {
    console.log('iiiimm',props);
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will close the video upload popup component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handlePopupClose = () => {
        props.closePopup();
    }


    return (
        <Dialog open={props.popupVisibility} onClose={handlePopupClose} disablePortal>
            <DialogTitle>
                <IconButton
                    aria-label="close"
                    onClick={handlePopupClose}
                    sx={{
                        position: 'absolute',
                        right: 8,
                        top: 8,
                        color: (theme) => theme.palette.grey[500],
                    }}
                >
                    <CloseIcon />
                </IconButton>
            </DialogTitle>

            <DialogContent style={{padding: "26px 24px 0 24px"}} className="videoUpload_modalBody">
                {props.children}
            </DialogContent>
        </Dialog>

    );
}

export default VideoUploadPopup;