import React, {useEffect, useState} from "react";
import {Grid} from "@material-ui/core";
import {useDispatch} from "react-redux";
import MediaGrid from "./MediaGrid";
import LoadingContainer from "../../Common/Loading/Loading";
import VideoUploadPopup from "./VideoUploadPopup";
import eventAction from "../../../redux/action/apiAction/event";
import EventLiveIcon from "../../Svg/EventLiveIcon";
import VideoLinkForm from "./VideoLinkForm";
import "./MediaGridImage.css"
import Constants from "../../../Constants";
import MediaGridSkeleton from "../../v4/Skeleton/MediaGridSkeleton";
import LoadingSkeleton from "../../Common/Loading/LoadingSkeleton";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This page/component allows user to upload media (images and Youtube/Vimeo video) which are then
 * broadcast and displayed to all the online users on the attendee side interface for a Content + Networking event
 * through the pilot panel component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component is received route related props eg - history,location,match
 * @returns {JSX.Element}
 * @constructor
 */
const EventMedia = (props) => {
    const [loading, setLoading] = useState(true);
    const dispatch = useDispatch();
    const params = props.match.params;

    const [videoGridData, setVideoGridData] = useState([]);
    const [imageGridData, setImageGridData] = useState([]);
    const [videoPopupVisible, setVideoPopupVisible] = useState(false);
    const [imagePopupVisible, setImagePopupVisible] = useState(false);
    const [imageUrl, setImgUrl] = useState(null);
    const [disableImageButton, setDisableImageButton] = useState(false)
    const [disableVideoButton, setDisableVideoButton] = useState(false)
    // useEffect is uesed to call getLiveEventData when params changes
    useEffect(() => {
        getLiveEventData();
    }, [props.match.params]);

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to get live tab(component to mange content player's assets) and
     * once the call execute successfully it will update states(setVideoGridData,setImageGridData,setDisableImageButton,
     * setDisableVideoButton,setLoading) from its response data otherwise throw an error.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const getLiveEventData = () => {
        dispatch(
            eventAction.getEventLiveData({eventUuid: params.event_uuid})
        ).then((res) => {
            let response = res.data.data
            setVideoGridData(res.data.data.event_live_video_links);
            setImageGridData(res.data.data.event_live_images);
            setDisableImageButton(response.is_default_image ? response.is_default_image : false)
            setDisableVideoButton(response.is_default_video ? response.is_default_video : false)
            setLoading(false);
        });
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will open the the popup by which user can upload videos(youtube, vimeo) for content
     * player.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const openVideoUploadPopup = () => {
        setVideoPopupVisible(true);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will close the the popup of upload videos(youtube, vimeo) for content player.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const closeVideoUploadPopup = () => {
        setVideoPopupVisible(false);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will open a popup when user select an image(to show on content player) from local
     * storage and user can crop their selected image.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const openImageUploadPopup = () => {
        setImagePopupVisible(true);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will close the popup which is opened to crop their selected image for the content
     * player.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const closeImageUploadPopup = () => {
        setImagePopupVisible(false);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user delete any asset(uploaded image and video for content player)
     * from live tab component and it will call 'getLiveEventData' method to get updated assets(remove last deleted asset)
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const onDeleteEvent = (data) => {
        getLiveEventData();
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user upload any asset(image and video for content player)
     * from live tab component and it will call 'getLiveEventData' method to get updated assets(show last added asset).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const onUpload = () => {
        getLiveEventData();
    };


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user select an image from his local system(to upload for content
     * player) and this function will save selected image's object in a state(setImgUrl).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data  Object of image
     */
    const onCapturedImg = (data) => {
        setImgUrl(data)
    }


    return (
        <LoadingSkeleton loading={loading} skeleton={<MediaGridSkeleton/>}>
            {/* <Grid item xs={12} className="TogglerRow">
                <Grid container className="ToggleMainFlex">
                    <Grid item className="Flex-1">
                        <EventLiveIcon />
                    </Grid>
                    <Grid item className="Flex-2">
                        Live Media
                        <span className="SmallSubHeading">
                            Choose the Content to display during Broadcasting
                        </span>
                    </Grid>
                </Grid>
            </Grid> */}
            <div>
                <MediaGrid
                    title={"Images"}
                    otherTitle={"Add Demo Images"}
                    showPopup={openImageUploadPopup}
                    gridData={imageGridData}
                    type="images"
                    iconType={Constants.mediaTabIcon.IMAGES}
                    onDelete={onDeleteEvent}
                    onUpload={onUpload}
                    capturedImg={onCapturedImg}
                    disableButton={disableImageButton}
                    {...props}
                />

                <MediaGrid
                    title={"Videos"}
                    otherTitle={"Add Demo Videos"}
                    showPopup={openVideoUploadPopup}
                    gridData={videoGridData}
                    onDelete={onDeleteEvent}
                    onUpload={onUpload}
                    type="videos"
                    iconType={Constants.mediaTabIcon.VIDEO}
                    disableButton={disableVideoButton}
                    popupComponent={
                        videoPopupVisible ? (
                            <VideoUploadPopup
                                popupVisibility={videoPopupVisible}
                                closePopup={closeVideoUploadPopup}
                                getLiveEventData={getLiveEventData}
                                {...props}
                            >
                                <VideoLinkForm
                                    popupVisibility={videoPopupVisible}
                                    closePopup={closeVideoUploadPopup}
                                    onUpload={onUpload}
                                    onDelete={onDeleteEvent}
                                    getLiveEventData={getLiveEventData}
                                    {...props}
                                />
                            </VideoUploadPopup>
                        ) : (
                            ""
                        )
                    }
                    {...props}
                />
            </div>
        </LoadingSkeleton>
    );
};

export default EventMedia;
