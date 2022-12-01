import React, {useEffect, useState} from 'react'
import Helper from '../../../Helper';
import ImgCropper from '../../NewInterFace/Conversation/UI/Main/Common/ImgCropper/ImgCropper';
import Constants from '../../../Constants';
import {useTranslation} from 'react-i18next';

/**
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.saveImage Function to save the image
 * @param {Function} props.setSelectorMode To handle the selector mode
 * @return {JSX.Element}
 * @constructor
 */
const UpdateProfile = (props) => {
    const [showCropper, setShowCropper] = useState(false);
    const [selectImage, setSelectImage] = useState(null);
    const [checkProfileData, setCheckProfileData] = useState(true);
    const {t} = useTranslation('mediaDevicePopup')

    const {userFirstName, userLastName} = props;

    useEffect(() => {
        if (selectImage !== null) {
            setShowCropper(true)
        }
    }, [selectImage])

    const onSelectFile = (e) => {
        if (e.target.files && e.target.files.length > 0) {
            const reader = new FileReader();
            reader.addEventListener('load', () => {
                setSelectImage(reader.result)
            });
            reader.readAsDataURL(e.target.files[0]);
        }
    };

    const saveImgHandler = (Img) => {
        props.saveImage && props.saveImage(Img);
        props.onClose();
    }
    const clickImgHandler = () => {
        props.setSelectorMode(Constants.mediaDevicePop.MODE_CAPTURE_AND_PREVIEW);
        props.setCurrentPopupContent(1)
    }
    // const isProfileData = _.has(props,["profileData"]) && props.profileData !== null && props.profileData !== undefined;

    return (
        <div className='updateProfile_mode'>
            <p className="profilePopup_title">
                Add a photo to start the experience.
            </p>
            {
                showCropper ?

                    <ImgCropper
                        aspect={1}
                        saveImage={(Img) => saveImgHandler(Img)}
                        capturedImgURL={selectImage}
                    />
                    :
                    <div className='profile-wrap'>
                         {checkProfileData && 
                            <div className="username-slider-dp no-texture">
                                {/* {props.profileData} */}
                                { Helper.nameProfile(userFirstName,userLastName)}
                        </div>
                        }
                    </div>
            }

            {!showCropper &&
            <div className='footer-wrap'>
                <input
                    type="file"
                    onChange={onSelectFile}
                    accept="image/*"
                    class="custom-file-input"
                />
                <label for="file">{t("Upload from computer")}</label>
                {/* <button>Upload</button> */}
                <button onClick={clickImgHandler}>{t("Capture from camera")}</button>
            </div>
            }
        </div>
    )
}

export default UpdateProfile