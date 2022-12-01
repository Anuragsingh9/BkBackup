import React, {useEffect, useState} from 'react';
import {connect, useDispatch} from "react-redux";
import {KeepContact as KCT} from "../../../../redux/types";
import ZoomMtgEmbedded from "@zoomus/websdk/embedded";
import _ from 'lodash';
import Helper from "../../../../Helper";
import "./ZoomComponentView.css";

let joinAttempt = 0;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Zoom component view to show the zoom component which contains the sdk inside
 * here the sdk initialization will handled and the sdk and join button will shown until the loading is completed
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Object} props.alert Reference object for displaying notification popup
 * @param {EventData} props.event_data Current event data
 * @param {UserBadge} props.event_badge User badge details
 * @param {ContentManagementMeta} props.contentManagementMeta Content Related Props from redux store for current content
 * @param {Boolean} props.isInitialized To indicate if the zoom sdk have initialized for once or not
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
let ZoomComponentView = (props) => {

    const [isObserverStarted, setIsObserverStarted] = useState(false);

    // redux state to store if the zoom sdk is already being initialized or not
    const dispatch = useDispatch();
    const [loading, setLoading] = useState(true);
    const [showJoinBtn, setShowJoinBtn] = useState(false);

    const client = ZoomMtgEmbedded.createClient();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will prepare the required data for joining a zoom meeting/webinar by using props
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @returns {ZoomConfig}
     */
    const prepareZoomConfig = () => {
        return {
            sdkKey: props.event_data.embedded_url.conf_api_key,
            signature: props.event_data.embedded_url.embedded_url, // role in SDK Signature needs to be 1
            meetingNumber: props.event_data.embedded_url.conf_meeting_id,
            password: '',
            userName: `${_.has(props.event_badge, ['user_fname']) ? props.event_badge.user_fname : ''} `
                + `${_.has(props.event_badge, ['user_lname']) ? props.event_badge.user_lname : ''}`,
        }
        return {
            apiKey: props.event_data.embedded_url.conf_api_key,
            signature: props.event_data.embedded_url.embedded_url,
            meetingNumber: props.event_data.embedded_url.conf_meeting_id,
            userName: `${_.has(props.event_badge, ['user_fname']) ? props.event_badge.user_fname : ''} `
                + `${_.has(props.event_badge, ['user_lname']) ? props.event_badge.user_lname : ''}`,
            password: '',
            userEmail: props.event_badge.user_email,
        };
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the successful join project for zoom
     * This will select the dom for grid button if grid button is found it will click that button
     * as from requirement when join is successful default view must be grid so it will click on that
     * -----------------------------------------------------------------------------------------------------------------
     */
    const successJoinHandler = () => {
        startObserverForGridButton();
        checkForJoinAudio();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to check and hit the zoom sdk button join audio button
     * as user needs to auto join the audio this will hit the join audio button
     * -----------------------------------------------------------------------------------------------------------------
     */
    const checkForJoinAudio = () => {
        let zoomUser = client.getCurrentUser();
        if (!_.isEmpty(zoomUser) && zoomUser.userId && (props.contentManagementMeta.zoomJoinedUsers.find(zu => zu == zoomUser.userId))) {
            clickOnJoinButton();
            let checkI = setInterval(() => {
                if (joinAttempt > 10) {
                    clearInterval(checkI);
                }
                clickOnJoinButton();
                joinAttempt += 1;
            }, 3000);
            setLoading(false);
        }
    }

    useEffect(() => {
        let zoomUser = client.getCurrentUser();
        checkForJoinAudio();
    }, [props.contentManagementMeta]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check the div existence by dom id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} id Id of the DOM to click programmatically
     */
    const clickDivById = (id) => {
        let dom = document.getElementById(id);
        if (dom) {
            dom.click();
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will click on audio join button
     * this will find the button inside the page and will try to click on it
     * -----------------------------------------------------------------------------------------------------------------
     *
     */
    const clickOnJoinButton = () => {
        let joinAudioBtnDom = document.querySelector(`[title="Audio"]`)
            || document.querySelector(`[title="Join Audio"]`);
        if (joinAudioBtnDom) {
            clickDivById('joinBtnFallback');
            clickDivById('zoomComponentViewSection');
            joinAudioBtnDom.click();
            clickDivById('joinBtnFallback');
            clickDivById('zoomComponentViewSection');
            // as no required to mute after joining
            // muteAfterJoin();
        }
    }

    /**
     * @deprecated
     */
    const muteAfterJoin = () => {
        if (props.event_data.is_auto_created) {
            setTimeout(() => {
                client.mute(true).then(() => {}).catch(e => console.error('error in mute ', e));
            }, 4000);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To click on the gallery button
     * This method will safely check for the dom existence and will click on gallery button
     * if there is speaker view activated it will not click on gallery button
     * -----------------------------------------------------------------------------------------------------------------
     *
     *
     * @returns {HTMLElement}
     */
    const clickOnGalleryViewButton = () => {
        let dom = document.getElementById('suspension-view-tab-thumbnail-gallery');
        let speakerViewDom = document.getElementById('suspension-view-tabpanel-speaker');
        if (dom && !speakerViewDom) {
            dom.click();
        }
        return dom;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To start the observer for the grid button
     * This button will rapidly hit the grid button
     * Current purpose of this observer is to get back user in grid when screen share is turned off
     * -----------------------------------------------------------------------------------------------------------------
     */
    const startObserverForGridButton = () => {
        if (isObserverStarted) {
            return;
        } else {
            setIsObserverStarted(true);
        }

        // clicking on gallery button as user needs to be remain in gallery button for first time
        clickOnGalleryViewButton();


        var observer = new MutationObserver(function (mutations) {
            let isClicked = false;
            mutations.forEach(mutation => {
                let joinAudioBtnDom = document.querySelector(`[title="Audio"]`) || document.querySelector(`[title="Join Audio"]`);
                let canvasDom = document.getElementById(`zoom-sdk-video-canvas`);
                if (joinAudioBtnDom && canvasDom) {
                    checkForJoinAudio();
                }

                if (mutation.target.nodeName === 'DIV' && !isClicked) {
                    // using this dom to ensure click on grid button only when screen share is being turned off
                    // removing this will lead to grid view even when screen share is started
                    let isShareOnDom = document.getElementsByClassName('in-sharing');

                    if (isShareOnDom.length === 0) {
                        isClicked = true;
                        // here grid button is found and there is no screen share element present so clicking on grid button
                        clickOnGalleryViewButton();
                    }
                }
            })
        });
        let config = {
            attributes: true,
            attributeFilter: ['id'],
            childList: true,
            subtree: true
        };
        observer.observe(document.querySelector('#zoomComponentViewSection'), config);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the failure of zoom join process
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const joinFailHandler = (e) => {
        setLoading(false);
        props.alert.show(Helper.handleError(e), {type: 'error'})
        console.error('ERROR in join zoom meeting', e)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description the handler for the zoom for the meeting to be joined successfully after the join button click
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} success zoom initialize success message
     */
    const joinZoom = (success = null) => {
        try {
            let config = prepareZoomConfig();
            client.join(config)
                .then(successJoinHandler)
                .catch(joinFailHandler);
        } catch (e) {
            joinFailHandler(e);
        }
        // as program reach here without being reload, that means it is first time component is loading
        // so updating redux to store that this component has already initialized

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To initialize the zoom sdk for displaying the zoom component
     * where user can click on Join Meeting button
     * -----------------------------------------------------------------------------------------------------------------
     */
    const initializeZoomSdk = () => {
        let meetingSDKElement = document.getElementById('zoomComponentViewSection');
        if (_.has(props.event_data, ['embedded_url']) && !_.isEmpty(props.event_data.embedded_url)) {
            // init/join only if embedded url is present
            if (!props.isInitialized) {
                client.init({
                    debug: true,
                    zoomAppRoot: meetingSDKElement,
                    language: "en-US",
                    customize: {
                        video: {
                            isResizable: false,
                            viewSizes: {
                                default: {
                                    width: 1188,
                                    height: 551,
                                }
                            },
                            popper: {
                                disableDraggable: true,
                            }
                        }
                    }
                }).then((success) => {
                    dispatch({
                        type: KCT.NEW_INTERFACE.ZOOM_INITIALIZE_UPDATE,
                        payload: true,
                    });
                    joinZoom(success);
                })
                    .catch(r => console.error('ERROR sdk has not been initialized', r));
            } else {
                joinZoom();
            }
        } else {
            console.warn('dddddddddddddddddd component is loaded but not joined due to lack of data', props.event_data);
        }

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description The handler for the component when the component is being unmount the zoom meeting must call the
     * leave method to release the resources.
     * -----------------------------------------------------------------------------------------------------------------
     */
    const handleZoomSdkUnmount = () => {
        try {
            client.leaveMeeting().then(() => {}).catch(e => console.error('error in leave zoom meeting', e));
        } catch (e) {
            console.error("ERROR in zoom leave", e);
        }
        // delete require.cache[require.resolve('@zoomus/websdk')];
        // require.cache = {};
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handler method for successful join of zoom meeting
     * -----------------------------------------------------------------------------------------------------------------
     *
     */
    const joinBtnHandler = () => {
        clickOnJoinButton();
        setShowJoinBtn(false);
        startObserverForGridButton();
    }

    useEffect(() => {
        // component unmount handler for zoom sdk
        return () => {
            handleZoomSdkUnmount();
        };
    }, []);

    useEffect(() => {
        if (_.has(props.event_data, ['embedded_url']) && !_.isEmpty(props.event_data.embedded_url) || props.isManualEvent) {
            initializeZoomSdk();
        } else {
            // embedded url is not present so try to leave meeting
            handleZoomSdkUnmount();
        }
    }, [props.event_data.embedded_url]);

    return (
        <div>
            <div id={"joinBtnFallback"}></div>
            {/*<div className={` join_audio_btn_wrap ${showJoinBtn ? '' : 'hidden'}`}>*/}
            {/*    <button onClick={joinBtnHandler}>Join Audio By Computer</button>*/}
            {/*</div>*/}
            <div id={"zoomComponentViewSection"} className={loading ? 'hidden' : ''}>
            </div>
            <div className={loading ? '' : 'hidden'} style={{"width": "100%", "height": "100%"}}>
                <Helper.pageLoading />
                <div className='zoom__waiting-txt'>
                    <p>“The meeting will begin shortly. Please wait…”</p>
                </div>
            </div>
        </div>
    )
}

const mapStateToProps = (state) => {
    return {
        event_data: state.NewInterface.interfaceEventData,
        event_badge: state.NewInterface.interfaceBadgeData,
        contentManagementMeta: state.NewInterface.contentManagementMeta,
        isInitialized: state.NewInterface.zoomSdkState.isInitialized,
    }
};


ZoomComponentView = connect(mapStateToProps, null)(ZoomComponentView);
export default ZoomComponentView;