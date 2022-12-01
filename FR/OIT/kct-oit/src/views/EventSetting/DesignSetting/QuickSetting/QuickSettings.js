import React from 'react';
import {Grid} from '@material-ui/core';
import _ from 'lodash';
import ProfileUpload from '../ProfileUpload/ProfileUpload';
import ColorCommon from '../ColorCommon/ColorCommon';
import {Button} from '@material-ui/core';
import InfoOutlinedIcon from '@material-ui/icons/InfoOutlined';
import ImageUploader from "../../../Common/ImageUploader/ImageUploader";
import {confirmAlert} from 'react-confirm-alert';
import "./QuickSettings.css"

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is quick design setting wrapper component which gives us to customise the skin of the Attendee
 * side quickly in the form of primary color 1 and primary color 2(fetched from logo automatically).This component also
 * provide a feature to upload a logo for attendee side.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received quick settings related values and functions from it's parent
 * file("DesignSetting.js").
 * @param {String} props.SubHeading Small heading placed at top of quick setting section in design setting.
 * @param {Function} props.callBack Function for color picker to take current value.
 * @param {Function} props.callBackCancel Function for cancel button in quick setting section.
 * @param {Function} props.child Function which return child components for quick setting section when maiun switch is
 * ON.
 * @param {Boolean} props.color_modal Boolean to render reset color modal in quick setting section.
 * @param {String} props.dataKey Unique key for component.
 * @param {Function} props.getKeyData Function to get current value for a specific key eg - get data for quick setting
 * section.
 * @param {Array} props.graphicSetting Array which consist all design settings value in key value pair object.
 * @param {String} props.heading Main heading text placed at top of quick setting section in design setting.
 * @param {Object} props.icon Icon to update the value
 * @param {String} props.icon.field Icon field name (ex:-business_team_icon)
 * @param {String} props.icon.value Icon updated image url
 * (ex:-https://s3.eu-west-2.amazonaws.com/kct-dev/testingaccount.humannconnect.dev/label_icons/business_team_altImage/galaxy-space-mu-1366x768.jpg)
 * @param {Function} props.resetColorHandler Function to reset all colors of quick setting section in primary color.
 * @param {String} props.reset_color Current section name to render reset color functionality into it eg - here
 * reset_color value will be "content".
 * @param {Function} props.updateDesignSetting Function used to update design setting values.
 * @returns {JSX.Element}
 * @constructor
 */
const QuickSetting = (props) => {

    const keyName = "group_logo"
    const mainColor1 = props.getKeyData('main_color_1');
    const mainColor2 = props.getKeyData('main_color_2');
    const groupLogo = props.getKeyData(keyName);

    // const isDefaultImage = _.has(groupLogo, ["is_default"]) && groupLogo.is_default == 1;
    const isDefaultImage = false;
    const {checkGroupDisable} = props;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handle image file change and uploads on  content setting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} fileObj Object of file
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
        <div className="QuickDesignDiv">
            <Grid container xs={12}>
                <Grid container xs={12} className="FlexRow QuickSettingRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2 QuickSettingLabel'> Event Logo : </p>
                    </Grid>
                    <Grid item className='eventLogoUploader'>
                        <ProfileUpload
                            group_logo={groupLogo}
                            callBack={props.callBack}
                            updateDesignSetting={props.updateDesignSetting}
                            logoIsDefault={props.eventLogoDefaultData}
                        />
                        {/*<ImageUploader*/}
                        {/*    isImageUploader="true"*/}
                        {/*    // isDefaultImage={isDefaultImage}*/}
                        {/*    imageUrl={groupLogo}*/}
                        {/*    updateDesignSetting={props.updateDesignSetting}*/}
                        {/*    saveImage={fileChange}*/}
                        {/*    deleteImage={deleteImage}*/}
                        {/*    callBack={props.callBack}*/}
                        {/*    getKeyData={props.getKeyData}*/}
                        {/*    getSettings={props.getSettings}*/}
                        {/*    imgDefaultData={props.eventLogoDefaultData}*/}
                        {/*    disabled={checkGroupDisable}*/}
                        {/*    keyName={keyName}*/}
                        {/*    aspect={1}*/}
                        {/*/>*/}
                    </Grid>
                    {/* <Grid className="LogoPreviewDiv">
                        <img src={groupLogo.value} />
                    </Grid> */}
                </Grid>
                <Grid container xs={12} className="FlexRow">
                    <Grid container xs={3} className="verticleFlex">
                        <Grid item xs={12}>
                            <p className='customPara customPara-2'> Primary Color 1 :</p>
                        </Grid>
                        <Grid item xs={12}>
                            <p className='customPara customPara-2'> Primary Color 2 :</p>
                        </Grid>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorCommon
                            main_color_2={mainColor2}
                            main_color_1={mainColor1}
                            callBack={props.callBack}
                            colorCallback={props.colorCallback}
                            disabled={checkGroupDisable}
                        />
                    </Grid>
                </Grid>
            </Grid>
        </div>
    )

}

export default QuickSetting;