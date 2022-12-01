import React, {useEffect, useState} from 'react';
import CloseIcon from '../../Svg/closeIcon';
import Cropper from '../Cropper/Cropper';
import _ from 'lodash';
import "./ImageUploader.css"

/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a wrapper component developed to update image and show the uploaded image preview.This component
 * has additional feature to remove uploaded image.This component is currently using in
 * design setting > content setting> content image.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Selected image data to use crop functionality
 * @param {String} props.imageUrl Image URL
 * @param {Boolean} props.isDefaultImage Boolean to manage default image state
 * @param {Function} props.callBack Function to save cropped image data
 * @param {Function} props.saveImage Function to save cropped image
 * @param {Function} props.updateDesignSetting Function to update design setting  data
 * @param {Function} props.showCustomImgPreview  Function to manage show/hide custom image preview
 * @return {JSX.Element}
 */
const ImageUploader = (props) => {
    const [showCrossIcon, setShowCrossIcon] = useState(false);
    // const {gKey} = useParams();
    // props.getSettings


    useEffect(() => {
        var isDefaultImage = _.has(props, ["imgDefaultData"]) && props.imgDefaultData == true;
        if (isDefaultImage) {
            setShowCrossIcon(false)
        } else {
            setShowCrossIcon(true)
        }
        // setShowCrossIcon(!isCrossIcon);

    }, [props.imgDefaultData])

    const imgPreviewUrl = _.has(props, ["imageUrl"]) ? props.imageUrl.value : "";

    return (
        <div className='image__uploader'>
            <div
                className='Img__preview_div'
                style={{
                    "background-image": `url(${imgPreviewUrl})`,
                    "background-size": "cover"
                }}
            >
                {/* image preview */}
            </div>

            {
                showCrossIcon &&
                <CloseIcon
                    className="previewCloseIcon"
                    style={{"cursor": "pointer"}}
                    disabled={props?.disabled}
                    onClick={props?.disabled ? '' : props.deleteImage}
                />
            }

            <div className='profileUploader__cropper_wrap'>
                <Cropper
                    aspect={props.aspect}
                    isImageUploader="true"
                    showCustomImgPreview={props.showCustomImgPreview}
                    updateDesignSetting={props.updateDesignSetting}
                    saveImage={props.saveImage}
                    callBack={props.callBack}
                    disabled={props.disabled}
                    keyName={props?.keyName}
                />
                {
                    !props?.disabled &&
                    <div className='image_uploader_hover_div'>
                        <small>Click here to upload.</small>
                    </div>
                }
            </div>
        </div>
    )
}

export default ImageUploader