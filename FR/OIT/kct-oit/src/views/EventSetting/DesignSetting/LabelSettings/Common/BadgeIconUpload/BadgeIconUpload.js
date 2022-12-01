import React, {useEffect, useState} from 'react';
import _ from 'lodash';
import {Button, Grid} from '@material-ui/core';
import {confirmAlert} from 'react-confirm-alert';
import {useSelector} from 'react-redux';
import CloseIcon from '../../../../../Svg/closeIcon.js';
import AttachmentIcon from '../../../../../Svg/AttachmentIcon.js';
import './BadgeIconUpload.css';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component developed to upload customized icon to for their relative roles.This
 * component also provide an additional feature of icon preview(how icon will look on profile) with delete icon
 * functionality.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.updateDesignSetting Function to update the design settings data
 * @param {String} props.customClass CSS class name
 * @param {Object} props.icon Icon to update the value
 * @param {String} props.icon.field Icon field name (ex:-business_team_icon)
 * @param {String} props.icon.value Icon updated image url
 * (ex:-https://s3.eu-west-2.amazonaws.com/kct-dev/testingaccount.humannconnect.dev/label_icons/business_team_altImage/galaxy-space-mu-1366x768.jpg)
 * @param {Boolean} props.changeIcon To verify Icon should be updated or not
 * @param {String} props.label The label for showing Icon or Alt image
 * @return {JSX.Element}
 * @constructor
 */
const BadgeIconUpload = (props) => {
    console.log("anuragdd", props)
    const [fileName, setFileName] = useState('');
    const [file, setFile] = useState(null);
    const user_badge = useSelector((state) => state.Auth.userSelfData);

    useEffect(() => {
        if (_.has(props.icon, ['value'])) {
            if (props.icon.value == null) {
                setFileName('');

            } else {
                setFileName(props.icon.value);

            }
        }
    }, [props.icon])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will listen click event on upload image button(paper pin icon) and it will perform
     * click event on image selector(to upload images from system).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const chooseFile = () => {
        const input = document.getElementById(props.icon.field ? `${props.icon.field}` : "");
        if (input) {
            input.click();
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user select any icon from his device's storage.And save it in a
     * state to  trigger an callback function to update icons.
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
            props.updateDesignSetting(
                {
                    field: props.icon.field ? `${props.icon.field}` : "",
                    value: fileData
                }
            )
        }
    }

    //  image file value
    const bgImage = _.has(props.icon, ['value']) ? props.icon.value : '';

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will open a popup component and take confirmation to perform delete any
     * role's icon action. That popup component contains 2 button('Yes', 'No'). If user click on 'Yes' then it will
     * remove icon and update design setting otherwise it will close the popup if user clicks on 'No' button.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const deleteIcon = () => {
        confirmAlert({
            message: 'Are you sure, you want to delete?',
            confirmLabel: 'Confirm',
            cancelLabel: 'Cancel',
            buttons: [
                {
                    label: 'Yes',
                    onClick: () => {
                        props.updateDesignSetting({field: props.icon.field ? `${props.icon.field}` : "", value: null});

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
            <Grid container className="IconUploadWrap">
                <Grid item>
                    <span className="LabelTxt">{props.label}</span>
                    <input
                        type="file"
                        id={props.icon.field ? `${props.icon.field}` : ""}
                        style={{display: 'none'}}
                        onChange={fileChange}
                        disabled={props?.disabled}
                        accept="image/png, image/gif, image/jpeg"
                    />
                    <Button variant="contained" className="theme-btn" color="primary" onClick={chooseFile}
                            disabled={props?.disabled}>
                        <AttachmentIcon />
                    </Button>
                </Grid>
                {/* current  */}
                <Grid className="left-50px">

                    {_.has(props.icon, ['value']) && props.icon.value != null &&
                    <CloseIcon className="previewCloseIcon" style={{cursor: "pointer"}} onClick={deleteIcon}
                               disabled={props?.disabled} />
                    }
                    <div className={`ProfilePreviewDiv ${props.customClass}`}>
                        {props.changeIcon && <img src={user_badge.avatar} className="UserProfileView" />}

                        {_.has(props.icon, ['value']) && props.icon.value != null
                            ? <div className="BadgeIconUploadDiv" style={{backgroundImage: "url(" + bgImage + ")"}}>
                                {/* <img src={bgImage} /> */}
                            </div>
                            : <div className="BadgeIconUploadDiv">
                                <div>
                                    {props.defaultIcon ? props.defaultIcon : ''}
                                </div>
                            </div>
                        }
                    </div>
                </Grid>

                {/* comming   */}
                {/* {_.has(props.group_logo,['value']) && props.group_logo.value != null &&
                    <Grid className="LogoPreviewDiv">
                        <img src={_.has(props.group_logo,['value'])?props.group_logo.value:''} />
                        <Button onClick={deleteIcon}>Delete</Button>
                    </Grid>
                } */}
                {/* comming  end */}
            </Grid>
        </div>
    )
}

export default BadgeIconUpload;