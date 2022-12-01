import React, {Fragment, useEffect} from 'react';
import {ZoomMtg} from "@zoomus/websdk";
import {useDispatch, useSelector} from "react-redux";
import {KeepContact as KCT} from "../../../../redux/types";

/**
 * @deprecated
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Zoom SDK Client view to load the SDK with zoom meeting data
 * Here the zoom sdk will be loaded if the application has loaded the zoom sdk for once this will be hidden to avoid the
 * re initialization which cause the exception to application
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} zoomConfig Zoom Configurations
 * @param {Function} updateZoomPosition
 * @param {Object} zoomInitializeState
 * @param {Object} alert
 * @param {Boolean} isZoomMute
 * @returns {JSX.Element}
 * @constructor
 */
const Zoom = ({zoomConfig, updateZoomPosition, zoomInitializeState, alert, isZoomMute}) => {

    // redux state to store if the zoom sdk is already being initialized or not
    const isInitialized = useSelector(state => state.NewInterface.zoomSdkState.isInitialized);

    const dispatch = useDispatch();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will check if page is required to reload or not for Zoom SDK
     * Mostly the page reload will required when the zoom sdk is initialized second time in same page application
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @returns {boolean}
     */
    const reloadPageIfRequired = () => {
        if (isInitialized) {
            // the component is already initialized so reloading the page because of zoom sdk compatibility.
            alert.show("Page will reload", {type: "success"})
            setTimeout(function () {
                window.location.reload(true);
            }, 3000);
            return true;
        }
        return false;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To initialize the zoom sdk component
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @warn this must be executed only once in application, reload the page before executing it twice
     */
    const preInitZoomSdk = () => {
        ZoomMtg.setZoomJSLib('https://dmogdx0jrul3u.cloudfront.net/1.9.9/lib', '/av')
        ZoomMtg.preLoadWasm();
        ZoomMtg.prepareWebSDK();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description the handler for the zoom when the meeting is joined successfully after the join button click
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param success
     */
    const zoomJoinHandler = (success) => {
        // as program reach here without being reload, that means it is first time component is loading
        // so updating redux to store that this component has already initialized
        dispatch({
            type: KCT.NEW_INTERFACE.ZOOM_INITIALIZE_UPDATE,
            payload: true,
        });

        return ZoomMtg.join({
            signature: zoomConfig.signature,
            meetingNumber: zoomConfig.meetingNumber,
            userName: zoomConfig.userName,
            apiKey: zoomConfig.apiKey,
            userEmail: zoomConfig.userEmail,
            passWord: zoomConfig.passWord,
            success: (success) => {
                ZoomMtg.showJoinAudioFunction({show: false});
                try {
                    ZoomMtg.mute({mute: isZoomMute});
                } catch (e) {
                    console.error(' ERROR zoom mute', e);
                }
            },
            error: (error) => {
                console.error(error)
            }
        });
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To initialize the zoom sdk for displaying the zoom component
     * where user can click on Join Meeting button
     * -----------------------------------------------------------------------------------------------------------------
     */
    const initializeZoomSdk = () => {
        ZoomMtg.init({
            leaveUrl: zoomConfig.leaveUrl,
            isSupportChat: true,
            isSupportCC: true,
            rwcBackup: true,
            videoHeader: true,
            showMeetingHeader: false,
            disableInvite: false,
            disableCallOut: false,
            disableRecord: false,
            disableJoinAudio: false,
            audioPanelAlwaysOpen: false,
            showPureSharingContent: false,
            isSupportAV: true,
            isSupportQA: true,
            screenShare: true,
            videoDrag: false,
            sharingMode: 'both',
            isLockBottom: true,
            isSupportNonverbal: true,
            isShowJoiningErrorDialog: true,
            inviteUrlFormat: '',
            success: zoomJoinHandler,
            error: (error) => {
                console.error(error)
            }
        });
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description The handler for the component when the component is being unmount the zoom meeting must call the
     * leave method to release the resources.
     * -----------------------------------------------------------------------------------------------------------------
     */
    const handleZoomSdkUnmount = () => {
        try {
            ZoomMtg.leaveMeeting({});
        } catch (e) {
            console.error("ERROR in zoom leave", e);
        }
        const zoomTag = document.getElementById('zmmtg-root');
        if (zoomTag) {
            zoomTag.remove();
            // window.location.reload();
        }
        delete require.cache[require.resolve('@zoomus/websdk')];
        require.cache = {};
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description the component initializer for starting all the process of sdk related
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @returns {Promise<void>}
     */
    const initializeComponent = async () => {
        const {ZoomMtg} = await import('@zoomus/websdk');
        await updateZoomPosition();
        await import('./zoom.css');

        // checking if this component or zoom sdk has already initialized or not
        if (reloadPageIfRequired()) {
            return;
        }

        preInitZoomSdk();
        initializeZoomSdk();
    };

    useEffect(() => {
        initializeComponent().then();
        // component unmount handler for zoom sdk
        return () => {
            handleZoomSdkUnmount();
        };
    }, []);

    return (
        <Fragment></Fragment>
    )
}

export default Zoom;