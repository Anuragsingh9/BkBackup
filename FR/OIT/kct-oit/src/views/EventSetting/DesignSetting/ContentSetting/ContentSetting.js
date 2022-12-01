import React from 'react';
import _ from 'lodash';
import {Button, Grid} from '@material-ui/core';
import {confirmAlert} from 'react-confirm-alert';
import Tooltip from '@material-ui/core/Tooltip';
import InfoOutlinedIcon from '@material-ui/icons/InfoOutlined';
import ColorPicker from '../Common/ColorPicker/ColorPicker';
import ImageUploader from "../../../Common/ImageUploader/ImageUploader";
import "./ContentSetting.css"


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to customize content section of attendee side interface.With the help of
 * this component user can change color of content player section(top of the user profile component section on attendee
 * side) and can also change event image(waiting image, appear when no content is running).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received content settings related values and functions from it's parent
 * file("DesignSetting.js").
 * @param {String} props.SubHeading Small heading placed at top of content setting section in design setting.
 * @param {Function} props.callBack Function for color picker to take current value.
 * @param {Function} props.callBackCancel Function for cancel button in content setting section.
 * @param {Function} props.child Function which return child components for content setting section when maiun switch is
 * ON.
 * @param {Boolean} props.color_modal Boolean to render reset color modal in content setting section.
 * @param {String} props.dataKey Unique key for component.
 * @param {Function} props.getKeyData Function to get current value for a specific key eg - get data for content setting
 * section.
 * @param {Array} props.graphicSetting Array which consist all design settings value in key value pair object.
 * @param {String} props.heading Main heading text placed at top of content setting section in design setting.
 * @param {Object} props.icon Icon to update the value
 * @param {String} props.icon.field Icon field name (ex:-business_team_icon)
 * @param {String} props.icon.value Icon updated image url (ex:-https://s3.eu-west-2.amazonaws.com/kct-dev/testingaccount.humannconnect.dev/label_icons/business_team_altImage/galaxy-space-mu-1366x768.jpg)
 * @param {Function} props.resetColorHandler Function to reset all colors of content setting section in primary color.
 * @param {String} props.reset_color Current section name to render reset color functionality into it eg - here
 * reset_color value will be "content".
 * @param {Function} props.updateDesignSetting Function used to update design setting values.
 * @returns {JSX.Element}
 * @constructor
 */
const ContentSetting = (props) => {
    // const {t} = useTranslation("confirm");

    // getting conetent background value
    const keyName = "event_image";
    const content_background = props.getKeyData('content_background');
    const eventImage = props.getKeyData(keyName);
    const isDefaultImage = _.has(eventImage, ["is_default"]) && eventImage.is_default == 1;
    const {checkGroupDisable} = props;


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will run when user upload an image(from local storage) from event image section and
     * this will take selected file object from it's parameter and pass it to the callback function(received from props)
     * to update event image.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} fileObj Image object
     */
    const fileChange = (fileObj) => {
        if (fileObj) {
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
     * @description This function will open a popup component and take confirmation to perform delete event image.
     * That popup component contains 2 button('Confirm', 'Cancel').If user click on 'Confirm' then it will add 'null'
     * to event image value and pass them in the function(updateDesignSetting) to delete event image.
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
    const {setShowContentCropPreview, showContentCropPreview} = props;
    return (
        <div>
            <Grid container xs={12} className="FlexRow QuickSettingRow contentSettingSection">
                <Grid item xs={3}>
                    <p className='customPara customPara-2 QuickSettingLabel'>
                        Event Image :
                        <Tooltip arrow title={props.tooltip_labels.event_image}>
                            <InfoOutlinedIcon />
                        </Tooltip>
                    </p>
                </Grid>
                <Grid item className='contentImgCropperWrap'>
                    {/* This component can be used in v3 */}

                    {/* <p className='small_info_txt'>Click Here to Upload.</p> */}
                    {/* <ProfileUpload
                        group_logo={eventImage}
                        event_image={true}
                        callBack={props.callBack}
                        updateDesignSetting={props.updateDesignSetting}
                    /> */}
                    {/* <Cropper
                        aspect={16 / 9}
                        isImageUploader = "true"
                        updateDesignSetting={props.updateDesignSetting}
                        saveImage={fileChange}
                        setShowContentCropPreview={setShowContentCropPreview}
                        showContentCropPreview={showContentCropPreview}
                    /> */}

                    <ImageUploader
                        isImageUploader="true"
                        // isDefaultImage={isDefaultImage}
                        imageUrl={eventImage}
                        updateDesignSetting={props.updateDesignSetting}
                        saveImage={fileChange}
                        deleteImage={deleteImage}
                        callBack={props.callBack}
                        getKeyData={props.getKeyData}
                        getSettings={props.getSettings}
                        imgDefaultData={props.contentImgDefaultData}
                        // contentImgDefaultData={props.contentImgDefaultData}
                        disabled={checkGroupDisable}
                        keyName={keyName}
                        aspect={16 / 9}
                    />
                </Grid>
            </Grid>
            <Grid container xs={12}>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Background Color : </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={content_background} callBack={props.callBack}
                                     disabled={checkGroupDisable} />
                    </Grid>
                </Grid>
            </Grid>
        </div>

    )
}

export default ContentSetting;