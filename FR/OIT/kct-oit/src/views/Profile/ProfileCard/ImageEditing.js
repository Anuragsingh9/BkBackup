import React from 'react'
import AvatarImageCr from 'react-avatar-image-cropper';
import {confirmAlert} from 'react-confirm-alert';
import './ImageEditing.css';
import {useTranslation} from 'react-i18next';
import {useAlert} from 'react-alert';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This Component is for displaying the Profile Image with features to update and delete in.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {string} userTxt  Text to show on component
 * @param {function} handleRemovePic  Function for removing image
 * @param {File} avatar  Image to show in profile field
 * @param {function} onSaveImage  Function for saving image
 * @return {JSX.Element}
 */
function ImageEditing({userTxt, handleRemovePic, avatar, onSaveImage, msg}) {
    const alert = useAlert();
    const {t} = useTranslation('details', 'notification')
    //  for validate image
    const actions = [
        <button key={0}>{t("Cancel")}</button>,
        <button key={1}>{t("Validate")}</button>,
    ];

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function invokes on SaveImage method upon validating the uploaded image and pass the data
     * of image file in it
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {File} file  Image file
     */
    const apply = (file) => {
        onSaveImage && onSaveImage(file)

    }

    /**
     * @deprecated
     */
    function blobToDataURL(blob, callback) {
        var a = new FileReader();
        a.onload = function (e) {
            callback(e.target.result);
        }
        a.readAsDataURL(blob);
    }

    /**
     * -----------------------------------------------------------------------------------------------
     * @description This function restricts user to upload an image greater than 5Mb and shows an alert message.
     * -----------------------------------------------------------------------------------------------
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
     * -----------------------------------------------------------------------------------------------
     * @description Function handles error fo image sizing
     * -----------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} type type is used for image size
     */
    const errorHandler = (type) => {
        if (type === 'maxsize') {
            alert.show("Image size is too large !", {type: "error"});
        }
    }
    const maxsize = 1024 * 1024 * 5;

    return (
        <div className=" uploadePicCrop imgUploadeStyle1">
            <div className="picUpload_parent"
                 style={{backgroundImage: `url(${avatar})`, "background-size": "cover", height: "100%", width: "95%"}}>
                <AvatarImageCr maxsize={maxsize} errorHandler={errorHandler}
                               text={<span className="upload-file">{t("Upload File")}</span>}
                               isBack={true} actions={actions} sliderConClassName={`red`}
                               className="picUpload_div" apply={apply} />
                {(avatar == '' || avatar == null) && <div className="noUserPicTxt">
                    <span className="userPicName" id="user-img">{userTxt}</span>
                </div>}
            </div>
        </div>
    )
}

export default ImageEditing