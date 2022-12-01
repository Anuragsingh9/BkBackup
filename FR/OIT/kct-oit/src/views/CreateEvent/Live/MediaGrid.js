import React, {useEffect, useState} from "react";
import {Grid} from "@material-ui/core";
import {confirmAlert} from "react-confirm-alert";
import CircularProgress from '@mui/material/CircularProgress';
import {Button} from "@mui/material";
import AddIcon from '@mui/icons-material/Add';
import {useDispatch} from "react-redux";
import {useParams} from "react-router-dom";
import _ from "lodash";
import {useAlert} from "react-alert";
import Helper from "../../../Helper";
import EventLiveUploadIcon from "../../Svg/EventLiveUploadIcon";
import EventLiveImgUploadIcon from "../../Svg/EventLiveImgUploadIcon";
import MediaGridImage from "./MediaGridImage";
import Cropper from "../../Common/Cropper/Cropper";
import eventAction from "../../../redux/action/apiAction/event";
import "./MediaGridImage.css";
import ImgIcon from "../../v4/Svg/ImgIcon";
import Constants from "../../../Constants";
import YoutubeIcon from "../../v4/Svg/YoutubeIcon";
import LoadingSkeleton from "../../Common/Loading/LoadingSkeleton";
import MediaGridSkeleton from "../../v4/Skeleton/MediaGridSkeleton";


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component will be used to render common structure for uploading assets(image and video) for content
 * player. This component will provide 2 features:
 * 1. User can upload multiple demo image/video in one click
 * 2. User can upload images/videos from his local system by himself.
 * <br>
 * <br>
 * Uploaded assets(image/video) will be shown in the tile form with delete option on hovering it.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from the parent component
 * @param {Array} props.gridData Event's all live tab data
 * @param {EventLiveImage} props.gridData.props.event_live_images All images used in Event live tab
 * @param {EventLiveVideo} props.gridData.props.event_live_video_links All video data used in Event live tab
 * @param {Function} props.onDelete This function is for handling the logic to delete an asset from the grid data
 * @param {Function} props.showPopup This function will show pop-up when user tries to delete any asset
 * @param {Function} props.onUpload This function will upload the image selected by the user
 * @returns {JSX.Element}
 * @constructor
 */
const MediaGrid = (props) => {
    const [file, setFile] = useState("");
    //show loader after click on save button
    const [showLoader, setShowLoader] = useState(false)
    const [imageData, setImageData] = useState();
    const dispatch = useDispatch();
    const {event_uuid} = useParams();
    const alert = useAlert();
    const [fileValue, setFileValue] = useState('');


    //this hook is  used for update iamge data state from props
    useEffect(() => {
        if (_.has(props, ["gridData"])) {
            setImageData(props.gridData);
        }
    }, []);
    const fileChange = (e) => {
        const files = e.target.files;
        if (files[0]) {
            const fileData = files[0];
            setFileValue(fileData);
            var binaryData = [];
            binaryData.push(fileData);
            const base_url = window.URL.createObjectURL(
                new Blob(binaryData, {type: "image/png"})
            );
            const reader = new FileReader();
            reader.addEventListener('load', () => {
                setFile(reader.result);
            }
            );
            reader.readAsDataURL(e.target.files[0]);

            e.target.value = null


            // props.capturedImg(base_url);
            // if (fileData) {

            //   uploadImage();
            // }
        }
    };

    // this hook is used for sending capture image when file value changes
    useEffect(() => {
        if (file !== '') {
            props.capturedImg(file);
        }

    }, [file])

    //this hook is used for show popup when file selected
    useEffect(() => {
        if (file !== "") {
            props.showPopup();
            // uploadImage();

        }
    }, [fileValue]);

    useEffect(() => {
        setImageData(props.gridData);
    }, [props]);

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user uploaded some assets already and then click on 'upload demo
     * image/video'. This will open a confirmation popup and if user click on 'yes' then it will override all the uploaded
     * assets(by the user) with demo assets.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} defaultImage To check if event live tab all images are default images or not
     * @param {Number} defaultVideo To check if event live tab all videos are default videos or not
     * @param {String} type Type of asset (1. Image 2. Video)
     */
    const showConfirmAlert = (defaultImage, defaultVideo, type) => {
        confirmAlert({
            message: `Adding demo media will replace existing uploaded ${type}. Are you sure you want to continue?`,
            confirmLabel: "confirm",
            cancelLabel: "cancel",
            buttons: [
                {
                    label: "Yes",
                    onClick: () => {
                        uploadImage("", defaultImage, defaultVideo);
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
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to add demo assets(images/videos). This will call when user
     * click on 'add demo image/video' option from event live tab component.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {File} ImgFile File which need to be uploaded
     * @param {Number} defaultImage To check if event live tab all images are default images or not
     * @param {Number} defaultVideo To check if event live tab all videos are default videos or not
     */
    const uploadImage = (ImgFile, defaultImage, defaultVideo) => {
        setShowLoader(true)
        const formData = new FormData();
        formData.append("event_uuid", event_uuid);
        formData.append("_method", "POST");
        ImgFile && formData.append("event_live_image", ImgFile);
        defaultImage && formData.append("is_default_image", defaultImage);
        defaultVideo && formData.append("is_default_video", defaultVideo);

        try {
            dispatch(eventAction.uploadEventLiveImage(formData))
                .then((res) => {
                    setShowLoader(false)
                    alert.show("Successfully Uploaded", {type: "success"});
                    props.onUpload();
                })
                .catch((err) => {
                    if (err && _.has(err.response.data, ["errors"])) {
                        var errors = err.response.data.errors;
                        for (let index in errors) {
                            setShowLoader(false)
                            alert.show(errors[index], {type: "error"});
                        }
                    } else if (err && _.has(err.response.data, ["msg"])) {
                        var er = err.response.data.msg;
                        if (er) {
                            setShowLoader(false)
                            alert.show(er, {type: "error"});
                        }
                    } else {
                        setShowLoader(false)
                        alert.show(Helper.handleError(err), {type: "error"});
                    }
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user click on 'add demo image/video' button in live tab component.
     * This method will listen which button(add demo image or add demo video) is clicked then this method will check that
     * some assets are uploaded already or not if yes then it will trigger confirmation popup other wise it will
     * add demo assets with respect to type(which button is clicked - image/video).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const showDefaultImages = () => {
        let defaultImage = 0;
        let defaultVideo = 0;
        props.type === "images" ? defaultImage = 1 : defaultImage = 0;
        props.type === "videos" ? defaultVideo = 1 : defaultVideo = 0;
        if (props.type === "images" && props.gridData.length > 0) {
            showConfirmAlert(defaultImage, defaultVideo, props.type)
        } else if (props.type === "videos" && props.gridData.length > 0) {
            showConfirmAlert(defaultImage, defaultVideo, props.type)
        } else {//first is for image file so its blank here for this method
            uploadImage("", defaultImage, defaultVideo)
        }
    }


    return (
        <Grid container spacing={0}>
            <Grid item md={12} className='mediaTabFlexRow'>
                {props.iconType == Constants.mediaTabIcon.VIDEO ? <YoutubeIcon /> : <ImgIcon />}
                <span className="customPara mediaTabHeading">{props.title}:</span>
                <Button
                    variant="text"
                    color="primary"
                    disabled={props.disableButton}
                    onClick={showDefaultImages}
                    className="add_demo_assetsBtn"
                >
                    {props.otherTitle}
                    &nbsp;&nbsp;
                    {showLoader ?
                        <CircularProgress
                            className="add_demoLoaders"
                        />
                        :
                        <AddIcon />}
                </Button>
            </Grid>

            {/* existing media preview section */}
            <Grid item md={12} style={{"display": "flex"}}>
                <Grid className="parantImageWrapper">
                    {imageData &&
                        imageData.map((media) => {
                            return (
                                <Grid item>
                                    <MediaGridImage
                                        keyValue={media.key}
                                        type={props.title.toLowerCase()}
                                        onDelete={props.onDelete}
                                        onUpload={props.onUpload}

                                        src={
                                            media.thumbnail_path
                                        }
                                    />
                                </Grid>
                            );
                        })}
                    {/* to add new section */}
                    {(
                        <>
                            {props?.type == "videos" && (
                                <div onClick={props.showPopup}>
                                    <EventLiveUploadIcon />
                                </div>
                            )}
                        </>
                    )}
                    {props.title === "Images" && (
                        <div className={'crop_input_withIcon'} style={{position: "relative"}}>
                            <EventLiveImgUploadIcon />
                            <Cropper
                                aspect={16 / 9}
                                saveImage={uploadImage}
                                showLoader={showLoader}
                                subHeading="For best viewing experience use 1920x1080 image size (in px)."
                            />
                        </div>
                    )}
                    <p className="customPara"></p>
                </Grid>
            </Grid>
            {props.popupComponent}
        </Grid>
    );
};

export default MediaGrid;
