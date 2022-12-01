import React from 'react'
import AvatarImageCr from 'react-avatar-image-cropper';
import {confirmAlert} from 'react-confirm-alert';
import './imageEditing.css';
import RoundedCrossIcon from '../../../../Svg/RoundedCrossIcon.js';
import {useTranslation} from 'react-i18next';
import Svg from '../../../../../Svg';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed top edit uploaded image to use it as profile picture.For image editing(zoom,
 * crop) we are using a package of 'react-avatar-image-cropper'.
 * This component will also preview the uploaded image and a option to remove it.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} userTxt Text to show as an name
 * @param {Function} handleRemovePic Handler to remove the picture file from the state so no image being selected
 * @param {String} avatar Image url of user profile picture
 * @param {Function} onSaveImage Handler method for user profile picture save
 * @param {Object} msg Reference object for displaying notification popup
 * @param {Function} handleSettingPopup Handler to show the setting popup
 * @param {Function} setShowCaptureBtn Method to set the capture button
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
function ImageEditing({userTxt, handleRemovePic, avatar, onSaveImage, msg, handleSettingPopup, setShowCaptureBtn}) {
    const {t} = useTranslation('myBadgeBlock')
    const actions = [
        <button key={0}>{t("Cancel")}</button>,
        <button key={1}>{t("Validate")}</button>,
    ];

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user click on 'save image' button after edit the uploaded image.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {File} file File to save as an image
     */
    const apply = (file) => {
        var files = new Blob([file], {type: file.type}, file.name)
        onSaveImage && onSaveImage(files)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to remove profile picture(If uploaded).First It will open a
     * popup component to take confirmation.If user click on yes it will remove current profile picture otherwise
     * it will close the popup component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const removePic = () => {
        confirmAlert({
            message: t("Are you sure want to remove?"), //Localization[lang].CONFIRM_REMOVE,
            confirmLabel: t("Confirm"),
            cancelLabel: t("Cancel"),
            buttons: [
                {
                    label: t("Yes"),
                    onClick: () => {
                        handleRemovePic()
                    }
                },
                {
                    label: t("No"),
                    onClick: () => {
                        return null
                    }
                }
            ],

        })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will check the uploaded image size and if it will exceed  max size then this function
     * will throw an error.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} type Type of error to display the error message accordingly
     */
    const errorHandler = (type) => {
        if (type == 'maxsize') {
            msg && msg("Image size is too large !", {type: "error"});
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will open the capture image popup component to click an image and then upload it
     * as profile picture.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const clickPicture = () => {
        setShowCaptureBtn(true);
        handleSettingPopup();
    }

    const uploadIcon = (
        <div className='picUpload_div_icon'>
            <div className='icon_upload' dangerouslySetInnerHTML={{__html: Svg.ICON.upload_Icon}}></div>
            {/* <p className='inner-profileText'>{t("Upload File")}</p> */}
        </div>
    )


    return (
        <div className="uploadePicCrop imgUploadeStyle1">
            <div className="picUpload_parent no-texture" style={{background: `url(${avatar})`}}>
                <div className='white_bg_div'></div>
                <AvatarImageCr
                    errorHandler={errorHandler}
                    text={<span className="upload-file">{t("Upload File")}</span>}
                    isBack={true}
                    actions={actions}
                    sliderConClassName={`red`}
                    className="picUpload_div"
                    apply={apply}
                    icon={uploadIcon}
                />
                {
                    (avatar == '' || avatar == null)
                    && <div className="noUserPicTxt">
                        <span className="userPicName" id="user-img">{userTxt}</span>
                    </div>
                }
                <div className='picCapture_div'>
                    <div
                        className='icon_capture'
                        dangerouslySetInnerHTML={{__html: Svg.ICON.camera}}
                        onClick={clickPicture}
                    ></div>
                    <p className='inner-profileText'>{t("Open Camera")}</p>
                </div>
            </div>

            <div className="img-close-btn roundedCrossBtn">
                {(avatar != '' && avatar != null) && <RoundedCrossIcon onClick={() => removePic()} />}
            </div>
        </div>
    )
}

export default ImageEditing