import React, {useEffect, useState} from 'react';
import TextField from '@material-ui/core/TextField';
import {Button, Grid} from '@material-ui/core';
import _ from 'lodash';
import KeyboardArrowDownIcon from '@material-ui/icons/KeyboardArrowDown';
import {confirmAlert} from 'react-confirm-alert';
import CloseIcon from '../../../Svg/closeIcon.js';
import './ProfileUpload.css';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to upload an image(currently using for event logo) with addition to delete
 * and preview of uploaded image features.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.callBack Function is used for group logo to update field and value
 * @param {Object} props.group_logo Is used for group logo
 * @param {Object} props.group_logo.value Group logo image
 * @param {Object} props.event_image Is used for event image
 * @param {Function} props.updateDesignSetting Function used for update the design setting
 * @returns {JSX.Element}
 * @constructor
 */
const ImageUpload = (props) => {
    const [fileName, setFileName] = useState('');
    const [file, setFile] = useState(null);

    useEffect(() => {
        if (_.has(props.group_logo, ['value'])) {
            if (props.group_logo.value == null) {
                setFileName('');

            } else {
                setFileName(props.group_logo.value);

            }
        }
    }, [props.group_logo])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will listen click event on upload image button('+' icon) and it will perform click
     * event on image selector(to upload image from system).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const chooseFile = () => {
        const input = document.getElementById(props.event_image ? "eventImage" : "fileUpload");
        if (input) {
            input.click();
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user select any image from the device's storage and save it in a
     * state to  trigger an callback function to update event user grid image.
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
            props.callBack(
                {
                    field: props.event_image ? "event_image" : "group_logo",
                    value: fileData
                }
            )
        }

    }

    const bgImage = _.has(props.group_logo, ['value']) ? props.group_logo.value : '';

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will open a popup component and take confirmation to perform delete
     * event image action. That popup component contains 2 button('Yes', 'No'). If user click on 'Yes' then it will
     * remove event image and update design setting otherwise it will close the popup if user clicks on 'No' button.
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
                        props.updateDesignSetting({
                            field: props.event_image ? "event_image" : "group_logo",
                            value: null
                        });
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

    let defaultLogofromGetGraphics = _.has(props, "group_logo")
    && _.has(props.group_logo, "is_default")
    && props.group_logo.is_default == 1 

    let showCrossIcon = _.has(props,"logoIsDefault") && props.logoIsDefault == true && props.logoIsDefault;


    // console.log('showCrossIcon', props.logoIsDefault,"--",props.group_logo.is_default,props)

    return (
        <div className='profileUploadWrap'>
            <Grid container className="VerticleFlex">
                <Grid item className='profileUploadTxtDiv'>
                    <TextField size="small" value={fileName} disabled={true} label="" variant="outlined" />
                    <input
                        type="file"
                        id={props.event_image ? "eventImage" : "fileUpload"}
                        style={{display: 'none'}}
                        onChange={fileChange}
                        accept="image/png, image/gif, image/jpeg"
                    />
                    <Button variant="contained" className="theme-btn" color="primary" onClick={chooseFile}>
                        <KeyboardArrowDownIcon />
                    </Button>
                </Grid>
                {/* current  */}
                {_.has(props.group_logo, ['value']) && props.group_logo.value != null &&
                <Grid className="left-50px imgPreviewProfileUpload">
                    {!showCrossIcon &&
                    <CloseIcon className="previewCloseIcon" style={{cursor: "pointer"}} onClick={deleteIcon} />}

                    {console.log('kkk', props)}
                    <div className="LogoPreviewDiv" style={{backgroundImage: "url(" + bgImage + ")"}}></div>
                </Grid>
                }

            </Grid>
        </div>
    )


}

export default ImageUpload;