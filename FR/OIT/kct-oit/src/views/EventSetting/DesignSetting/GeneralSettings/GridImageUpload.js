import React, {useEffect, useState} from "react";
import TextField from "@material-ui/core/TextField";
import {Button, Grid} from "@material-ui/core";
import KeyboardArrowDownIcon from "@material-ui/icons/KeyboardArrowDown";
import {confirmAlert} from "react-confirm-alert";
import CloseIcon from "../../../Svg/closeIcon.js";
import "../ProfileUpload/ProfileUpload.css";
import _ from "lodash";


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to upload an image(currently using in - event grid image) with addition to
 * delete and preview of uploaded image features.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.callBack Function is used for group logo to update field and value
 * @param {Object} props.group_logo Is used for group logo
 * @param {Object} props.group_logo.field Is used for group logo field
 * @param {File} props.group_logo.value Group logo image
 * @param {Function} props.updateDesignSetting Function used for update the design setting
 * @returns {JSX.Element}
 * @constructor
 */
const ImageUpload = (props) => {
    const [fileName, setFileName] = useState("");
    const [file, setFile] = useState(null);


    useEffect(() => {
        if (_.has(props.group_logo, ["value"])) {
            if (props.group_logo.value == null) {
                setFileName("");
            } else {
                setFileName(props.group_logo.value);
            }
        }
    }, [props.group_logo]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will listen click event on upload image button(bottom arrow symbol) and it will perform
     * click event on image selector(to upload images from system).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const chooseFile = () => {
        const input = document.getElementById(
            props.group_logo.field ? props.group_logo.field : ""
        );
        if (input) {
            input.click();
        }
    };


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will listen click event on upload image button(bottom arrow symbol) and it will perform
     * click event on image selector(to upload images from system).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const fileChange = (e) => {
        const files = e.target.files;
        if (files[0]) {
            const fileData = files[0];
            setFile(fileData);
            setFileName(fileData.name);
            props.callBack({
                field: props.group_logo.field ? props.group_logo.field : "",
                value: fileData,
            });
        }
    };

    // this shows image
    const bgImage = _.has(props.group_logo, ["value"])
        ? props.group_logo.value
        : "";

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is used to delete the icon
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const deleteIcon = () => {
        confirmAlert({
            message: "Are you sure, you want to delete?",
            confirmLabel: "Confirm",
            cancelLabel: "Cancel",
            buttons: [
                {
                    label: "Yes",
                    onClick: () => {
                        props.updateDesignSetting({
                            field: props.group_logo.field ? props.group_logo.field : "",
                            value: null,
                        });
                    },
                },
                {
                    label: "No",
                    onClick: () => {
                        return null;
                    },
                },
            ],
        });
    };

    return (
        <div>
            <Grid container className="VerticleFlex">
                <Grid item>
                    <TextField
                        size="small"
                        value={fileName}
                        disabled={true}
                        label=""
                        variant="outlined"
                    />
                    <input
                        type="file"
                        id={props.group_logo.field ? props.group_logo.field : ""}
                        style={{display: "none"}}
                        onChange={fileChange}
                        accept="image/png, image/gif, image/jpeg"
                    />
                    <Button
                        variant="contained"
                        className="theme-btn"
                        color="primary"
                        onClick={chooseFile}
                    >
                        <KeyboardArrowDownIcon />
                    </Button>
                </Grid>
                {/* current  */}
                {_.has(props.group_logo, ["value"]) && props.group_logo.value != null && (
                    <Grid className="left-50px">
                        <CloseIcon className="previewCloseIcon" style={{cursor: "pointer"}} onClick={deleteIcon} />
                        <div
                            className="LogoPreviewDiv"
                            style={{backgroundImage: "url(" + bgImage + ")"}}
                        >
                            {/* <img src={bgImage} /> */}
                        </div>
                    </Grid>
                )}

            </Grid>
        </div>
    );
};

export default ImageUpload;
