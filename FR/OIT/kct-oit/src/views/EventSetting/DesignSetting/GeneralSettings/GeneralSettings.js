import React from "react";
import _ from "lodash";
import {Button, Grid} from "@material-ui/core";
import InfoOutlinedIcon from "@material-ui/icons/InfoOutlined";
import Tooltip from "@material-ui/core/Tooltip";
import GridImageUpload from "./GridImageUpload";
import ToggleKey from "../Common/ToggleKey/ToggleKey";
import TooltipObject from "../../../../Models/TooltipObject";

import ImageUploader from "../../../Common/ImageUploader/ImageUploader";
import {confirmAlert} from "react-confirm-alert";
import "./GeneralSettings.css";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to manage general settings customization for attendee side interface. which
 * are :
 * 1. Hide/Show the feature to Invite Users on the Event Registration page
 * 2. Hide/Show the product intro Youtube video (saved in the Super Admin interface) on the Event Registration page and
 * User Grid Component of the Event Dashboard page (before the Event starts). If Pilot decides to hide the Product
 * Intro Video, then an image will be displayed (can be modified by Pilot) on the User Grid Component of the Event
 * Dashboard page
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received general settings related values and functions from it's parent
 * file("DesignSetting.js").
 * @param {String} props.SubHeading Small heading placed at top of general setting section in design setting.
 * @param {Function} props.callBack Function for color picker to take current value.
 * @param {Function} props.callBackCancel Function for cancel button in general setting section.
 * @param {Function} props.child Function which return child components for general setting section when maiun switch is
 * ON.
 * @param {Boolean} props.color_modal Boolean to render reset color modal in general setting section.
 * @param {String} props.dataKey Unique key for component.
 * @param {Function} props.getKeyData Function to get current value for a specific key eg - get data for general setting
 * section.
 * @param {Array} props.graphicSetting Array which consist all design settings value in key value pair object.
 * @param {String} props.heading Main heading text placed at top of general setting section in design setting.
 * @param {Object} props.icon Icon to render at top of general setting section in design setting.
 * @param {Function} props.setShowGridImageField Function to manage show/hide user grid image component in design
 * setting>general setting section.
 * @param {String} props.showGridImageState State to manage rendering grid image component.
 * @param {TooltipObject} props.tooltip_labels Object that contains tooltips.
 * @param {Function} props.updateDesignSetting Function used to update design setting values.
 * @returns {JSX.Element}
 * @constructor
 */
const GeneralSettings = (props) => {
    const keyName = "video_explainer_alternative_image"
    const gridImage = props.getKeyData(keyName);
    const vdo = props.getKeyData("video_explainer");
    const inv = props.getKeyData("invite_attendee");
    const isDefaultImage = _.has(gridImage, ["is_default"]) && gridImage.is_default == 1;
    const {checkGroupDisable} = props;


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handle image file change and uploads on  content setting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} fileObj File object
     */
    const fileChange = (fileObj) => {
        if (fileObj) {
            // setFile(fileObj);
            // setFileName(fileObj.name);
            props.callBack(
                {
                    field: keyName,
                    value: fileObj
                }
            )
        }

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for delete image file
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const deleteImage = () => {
        confirmAlert({
            message: 'Are you sure, you want to delete?',
            confirmLabel: 'Confirm',
            cancelLabel: 'Cancel',
            buttons: [
                {
                    label: 'Yes',
                    onClick: () => {
                        props.updateDesignSetting({field: keyName, value: null});
                    }
                },
                {
                    label: 'No',
                    onClick: () => {
                        return null
                    }
                }
            ],

        })
    }
    return (
        <div>
            <ToggleKey
                dataSubKey={"invite_attendee"}
                toolTip={props.tooltip_labels.invite_users}
                iconInfo={<InfoOutlinedIcon />}
                {...props}
                disabled={checkGroupDisable}
            />
            <ToggleKey
                dataSubKey={"video_explainer"}
                setShowGridImageField={props.setShowGridImageField}
                toolTip={props.tooltip_labels.show_video_tooltip}
                iconInfo={<InfoOutlinedIcon />}
                {...props}
                disabled={checkGroupDisable}
            />
            {props.showGridImageState && (
                <Grid container xs={12} className="FlexRow QuickSettingRow">
                    <Grid item xs={3}>
                        <p className="customPara customPara-2 QuickSettingLabel">
                            {" "}
                            User Grid Image (1158x630) :{" "}
                            <Tooltip arrow title={props.tooltip_labels.user_grid_image}>
                                <span>{<InfoOutlinedIcon />}</span>
                            </Tooltip>
                        </p>
                    </Grid>
                    <Grid item className="userGridImageRow">
                        {/* <GridImageUpload
              group_logo={gridImage}
              callBack={props.callBack}
              updateDesignSetting={props.updateDesignSetting}
              disabled={checkGroupDisable}
            /> */}
                        <ImageUploader
                            isImageUploader="true"
                            // isDefaultImage={isDefaultImage}
                            imageUrl={gridImage}
                            updateDesignSetting={props.updateDesignSetting}
                            saveImage={fileChange}
                            deleteImage={deleteImage}
                            callBack={props.callBack}
                            getKeyData={props.getKeyData}
                            getSettings={props.getSettings}
                            imgDefaultData={props.userGridImgDefaultData}
                            disabled={checkGroupDisable}
                            keyName={keyName}
                            aspect={1158 / 630}
                        />
                    </Grid>
                </Grid>
            )}
        </div>
    );
};

export default GeneralSettings;
