import React, {useEffect, useState} from 'react';
import {connect} from 'react-redux';
import _ from 'lodash';
import "./SpaceHost.css";
import newInterfaceActions from '../../../../redux/actions/newInterfaceAction';
import Constants from "../../../../Constants";
import ContentVideoPlayer from "./ContentVideoPlayer";
import ContentSdkPlayer from "./ContentSdkPlayer";
import ContentImagePlayer from "./ContentImagePlayer";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description - This component is developed to broadcast media and/or Zoom SDK to all the participants of the event.
 *  Pilots can choose to broadcast between Youtube videos, Vimeo videos, Images uploaded from the system and Zoom
 *  Meetings / Webinar to the online users.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Boolean} props.hostThere To indicate if space host is online or offline
 * @param {String} props.video_url Url of video player for current moment if moment is video type
 * @param {Label[]} props.event_labels All the event labels with different locales
 * @param {UserBadge} props.spaceHostData Space host data in form of user badge
 * @param {InterfaceSpaceData} props.spaceData Spaces data including conversations from redux store
 * @param {UserBadge} props.event_badge Redux store mapped variable for holding user badge data
 * @param {EventData} props.event_data Redux store state variable to provide the event data
 * @param {ContentManagementMeta} props.contentManagementMeta Content Related Props from redux store for current content
 * @param {Boolean} props.isInitialized To indicate if zoom sdk has initialized for once in application
 * @param {Function} props.updateContentData Redux Action to update the content data in redux
 *
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const ContentManagement = props => {
    const [currentContent, setCurrentContent] = useState(null);
    // keeping zoom sdk load state different as this will decide the sdk will load or not
    // and current content will decide only display visible or hidden
    const [zoomSdkLoad, setZoomSdkLoad] = useState(false);
    const [videoUrl, setVideoUrl] = useState(null);
    const [imageUrl, setImageUrl] = useState(null);

    useEffect(() => {
        handleEventData();
    }, []);

    useEffect(() => {
        handleEventData();
    }, [props.video_url, props.contentManagementMeta, props.event_data]);

    useEffect(() => {
    }, [currentContent]);


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the content player with respect to event type
     * if event is auto created then pilot panel selection will be taken (if selected) than moment data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleEventData = () => {
        if (_.has(props.event_data, ['is_auto_created']) && props.event_data.is_auto_created) {
            // event is auto created to so managing the content with respect to pilot panel
            handleAutoCreateEventLoad();
        } else {
            // event is manual so managing the data from the props (api response) directly
            handleManualEventLoad();
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description - This method is used to handle the content data in content section it shows content according to
     * contentManagementMeta data  if there is content type is image then image will show , if there is content type
     * is zoom sdk it will show zoom sdk in content section.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleAutoCreateEventLoad = () => {
        // if nothing is set as content type then return
        if (!props.contentManagementMeta.currentMediaType) {
            setCurrentContent(Constants.contentManagement.CNT_MGMT_IMAGE);
            setImageUrl(props.event_data.event_image);
            hideZoomPopup();
            return;
        }

        if (props.contentManagementMeta.currentMediaType !== Constants.contentManagement.CNT_MGMT_ZOOM_SDK) {
            // if current content is not zoom then hide all its popup if any like chats, participants, settings etc
            hideZoomPopup();
        }

        if (props.contentManagementMeta.currentMediaType === Constants.contentManagement.CNT_MGMT_VIDEO) {
            // current content is video
            setVideoUrl(props.contentManagementMeta.currentMediaData.value);
        } else if (props.contentManagementMeta.currentMediaType === Constants.contentManagement.CNT_MGMT_IMAGE) {
            // current content type is image
            setImageUrl(props.contentManagementMeta.currentMediaData.value);
        }

        // setting current content type what ever it is right now
        setCurrentContent(props.contentManagementMeta.currentMediaType);
        checkAndKeepSdkLoaded();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description - This method is used to set current content type from props values, if content is not seted by
     * pilot it will show image in content section and if props have video url then shows the video and updates state
     * for videos and image values.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleManualEventLoad = () => {
        if (props.video_url) {
            // if props have video url then show the video
            setCurrentContent(Constants.contentManagement.CNT_MGMT_VIDEO);
            setVideoUrl(props.video_url);
        } else if (_.has(props.event_data, ['embedded_url']) && !_.isEmpty(props.event_data.embedded_url)) {
            // conference type zoom
            setCurrentContent(Constants.contentManagement.CNT_MGMT_ZOOM_SDK);
        } else {
            // else setting the event image now
            setCurrentContent(Constants.contentManagement.CNT_MGMT_IMAGE);
            setImageUrl(props.event_data.event_image);
        }

        checkAndKeepSdkLoaded();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check the sdk status and if sdk has loaded for at-least once then it should kept available
     * as SDK throws error on reinitialization so if the sdk has loaded then hide it in application and keep it rendered
     * internally
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const checkAndKeepSdkLoaded = () => {
        if (props.isInitialized || (_.has(props.event_data, ['embedded_url']) && !_.isEmpty(props.event_data.embedded_url))) {
            // if zoom sdk is already initialized then keep it on so the div remain on page
            setZoomSdkLoad(true);
        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Reusable method to hide the DOM by role attribute
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} value Role attribute value of DOM to hide
     */
    const hideDomByAtrRole = (value) => {
        let dom = document.querySelectorAll(`[role="${value}"]`);
        if (dom.length) {
            dom.forEach(d => {
            if(_.isEmpty(d.id)) {
                d.classList.add('hidden');
                d.style.display = 'none';
            }
            })
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description As after SDK Hide zoom sdk popup also needs to be hidden as they are outside the sdk dom
     * so hiding them by DOM selected from root level of application
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const hideZoomPopup = () => {
        hideDomByAtrRole('dialog');
    }

    return (
        <section className={`${props.contentManagementMeta.componentVisibility ? '' : 'hidden'}`}>
            <div className="container container_videoBlock">
                <div className="col-md-12 row Sh_block_row">
                    {currentContent === Constants.contentManagement.CNT_MGMT_VIDEO && videoUrl !== null &&
                    <ContentVideoPlayer
                        videoUrl={videoUrl}
                    />}

                    {zoomSdkLoad &&
                    <ContentSdkPlayer
                        display={currentContent === Constants.contentManagement.CNT_MGMT_ZOOM_SDK}
                        alert={props.alert}
                    />
                    }
                    {currentContent === Constants.contentManagement.CNT_MGMT_IMAGE &&
                    <ContentImagePlayer
                        image_url={imageUrl}
                        event_image={props.event_data.event_image}
                    />
                    }

                </div>
            </div>
        </section>
    );
}

const mapDispatchToProps = (dispatch) => {
    return {
        updateContentData: (data) => dispatch(newInterfaceActions.NewInterFace.setCurrentContent(data)),
    }
}

const mapStateToProps = (state) => {
    return {
        event_labels: state.page_Customization.initData.labels,
        spaceHostData: state.NewInterface.interfaceSpaceHostData,
        spaceData: state.NewInterface.interfaceSpacesData,
        event_badge: state.NewInterface.interfaceBadgeData,
        event_data: state.NewInterface.interfaceEventData,
        isPrivate: state.NewInterface.isPrivate,
        availabilityHost: state.NewInterface.availabilityHost,
        contentManagementMeta: state.NewInterface.contentManagementMeta,
        isInitialized: state.NewInterface.zoomSdkState.isInitialized,
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(ContentManagement);

