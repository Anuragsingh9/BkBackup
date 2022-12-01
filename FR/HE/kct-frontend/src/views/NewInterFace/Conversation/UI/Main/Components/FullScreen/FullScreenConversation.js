import React, {useEffect, useState} from 'react';
import '../../../../../HostSection/SpaceHost/SpaceHost.css';
import _ from 'lodash';
import '../spacehostblock.css';
import newInterfaceActions from "../../../../../../../redux/actions/newInterfaceAction";
import {connect, useDispatch} from "react-redux";
import Helper from "../../../../../../../Helper";
import videoElementRepo from "../../../../../../VideoMeeting/VideoElementRepository";
import {
    askToPrivateConversation,
    checkDummyUser,
    getUserData,
    leaveConversation,
    removeUser
} from "../../../../Utils/Conversation";
import DummyVideo from "../../../../../VideoPlayer/DummyVideo";
import ConvSHButtons from "../../Common/ConvSHButtons";
import OtherButtons from '../../../Main/Components/SpaceHostOtherButtons';
import VideoMeetingClass from "../../../../../../VideoMeeting/VideoMeetingClass";
import ConversationButton from "../../Common/ConversationButtons";
// import "./FullScreenConversation.css";
import "./FullScreen.css";
import Slider from "../../../../../MyBadge/BadgeSideComponent/NewSlider";
import OtherBadgeButton from "../../Common/OtherBadgeButton";
import VideoUserName from '../../Common/VideoUserName/VideoUserName';
import Svg from "../../../../../../../Svg";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for display conversation section main component for space host
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.updateConversationFullScreen  To toggle the current conversation full screen mode
 * @param {Function} props.updateVideoMuteText To update the conversation mute state and text related to it
 * @param {ConversationMeta} props.conversationMeta Current conversation state from redux
 * @param {InterfaceSpaceData} props.event_space Spaces data including conversations from redux store
 * @param {UserBadge} props.auth User badge details
 * @param {Function} props.toggle_ban To ban a user from platform by space host
 *
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const FullScreenConversation = (props) => {
    let {selfSeat, auth} = props;
    const dispatch = useDispatch();
    const conversation_users = _.has(props.event_space, ['current_joined_conversation'])
        && props.event_space.current_joined_conversation !== null
        && props.event_space.current_joined_conversation.conversation_users.filter((val) => !val.hasOwnProperty("is_self"));

    let [offset, setOffset] = useState(null);
    let [buttonsVisible, setButtonVisible] = useState({});
    const [sliderOpen, setHoverSlider] = useState(false)
    const [userCountGrid, setUserCountGrid] = useState(0);
    const [gridHeightCol, setGridHeightCol] = useState(100);
    const [userCountClass, setUserCountClass] = useState(0)


    useEffect(() => {
        const user = 12 / (conversation_users.length + 1);

        if (conversation_users.length == 3 || conversation_users.length == 2 || conversation_users.length == 1) {
            setUserCountGrid(6)
            conversation_users.length == 1 ? setGridHeightCol(65) : setGridHeightCol(50);
        } else if (conversation_users.length == 4) {
            setUserCountGrid(4);
            setGridHeightCol(50);
        } else if (conversation_users.length == 5) {
            setUserCountGrid(4);
            setGridHeightCol(50);
        } else if (conversation_users.length == 6 || conversation_users.length == 7) {
            setUserCountGrid(4);
            setGridHeightCol(33.33);
        } else if (conversation_users.length == 8) {
            setUserCountGrid(4);
            setGridHeightCol(33.33);
        } else {
            setUserCountGrid(4);
            setGridHeightCol(50);
        }
    }, [conversation_users])


    useEffect(() => {
        VideoMeetingClass.updateSelfTile(props.selfSeat.tileState);
        VideoMeetingClass.updateVideoTile(null, props.selfSeat);
        videoElementRepo.getSeats().forEach(seat => {
            VideoMeetingClass.updateVideoTile(seat.userId);
        })
    }, [])

    const updateButtonVisibility = (i, value) => {
        {
            setButtonVisible({
                ...buttonsVisible,
                [videoElementRepo.SEATS[i].userId]: value,
            })
        }
    }

    return (
        <div className={"overlay-static fullScreenWrapper"}>
            <div className={`fs-video-grid fullScreenCount-${conversation_users.length}`}>
                <div className={"container-fluid"}>
                    <div className={`row fullScreen_wrap fullScreenCount-${conversation_users.length}`}>
                        <div
                            className={`col-sm-${userCountGrid} fs-single-grid fs-real-video`}
                            style={{
                                height: `${gridHeightCol}%`,
                            }}
                        >
                            {(selfSeat.tileState === null || selfSeat.tileState.active !== true) &&
                            <Helper.pageLoading />}
                            <video className="first-person-video first-person-thumb no-texture" id="self-video" />
                            <audio id="self-audio" />
                            <VideoUserName
                                className="nameAudioBar"
                                user={{
                                    ...props.event_space?.current_joined_conversation?.conversation_users.find(u => u.is_self),
                                    is_mute: props.conversationMeta.mute
                                }}
                            />
                        </div>

                        {props.event_space.current_joined_conversation !== null && videoElementRepo.getSeats().map((seat, i) => {

                            if (auth.user_id != videoElementRepo.SEATS[i].userId && videoElementRepo.SEATS[i].userId) {
                                return (
                                    <div className={
                                        `col-sm-${userCountGrid} ` +
                                        `fs-single-grid ` +
                                        `${videoElementRepo.SEATS[i].userId ? '' : 'hidden'} ` +
                                        `${!_.isEmpty(checkDummyUser(conversation_users, videoElementRepo.SEATS[i].userId)) ? 'fs-dummy-video' : 'fs-real-video'}`
                                    }
                                         style={{height: `${gridHeightCol}%`}}
                                    >

                                        {!_.isEmpty(checkDummyUser(conversation_users, videoElementRepo.SEATS[i].userId))
                                            ?
                                            <>
                                                <DummyVideo
                                                    user={checkDummyUser(conversation_users, videoElementRepo.SEATS[i].userId)}
                                                >
                                                    <VideoUserName
                                                        className="nameAudioBar"
                                                        user={getUserData(conversation_users, videoElementRepo.SEATS[i].userId)}
                                                    />
                                                </DummyVideo>
                                                <div className={"fs-other-btn"}>
                                                    {props.isSpaceHost
                                                        ? <>
                                                            <OtherButtons
                                                                setHoverSlider={setHoverSlider}
                                                                removeUser={(e, cb) => {
                                                                    dispatch(removeUser(e, cb, videoElementRepo.SEATS[i].isDummy))
                                                                }}
                                                                toggleBan={props.toggleBan}
                                                                buttonState={false}
                                                                userId={videoElementRepo.SEATS[i].userId}
                                                            />
                                                            {(sliderOpen === videoElementRepo.SEATS[i].userId) &&
                                                            <div
                                                                className="other-user-badge-popup"
                                                            >
                                                                <Slider onBlur={() => {
                                                                    setHoverSlider(false)
                                                                }}
                                                                        item={getUserData(conversation_users, videoElementRepo.SEATS[i].userId)} />
                                                            </div>
                                                            }
                                                        </>
                                                        : <OtherBadgeButton
                                                            setHoverSlider={setHoverSlider}
                                                            userId={videoElementRepo.SEATS[i].userId}
                                                            conversation_users={conversation_users}
                                                            sliderOpen={sliderOpen}
                                                            isDummy={videoElementRepo.SEATS[i].isDummy}
                                                        />
                                                    }
                                                </div>

                                            </>
                                            :
                                            <React.Fragment>
                                                <>

                                                    {
                                                        props.event_space?.current_space_host?.length
                                                        && props.event_space.current_space_host[0].user_id
                                                        && videoElementRepo.SEATS[i].userId == props.event_space.current_space_host[0].user_id
                                                        // && <div className={"hostIndicator"} > <span dangerouslySetInnerHTML={{__html: Svg.ICON.reception}} /> </div>
                                                        // && <div className={"hostIndicator"} > HOST </div>
                                                        && <button type="button"
                                                                   className="hostIndicator"
                                                                   data-for='conversation'
                                                                   disabled
                                                        >
                                                            <span className="svgicon no-texture"
                                                                  dangerouslySetInnerHTML={{__html: Svg.ICON.reception}}></span>
                                                        </button>
                                                    }

                                                    <video
                                                        id={`other-video${i}`}
                                                        className="h-100 w-100 no-texture membersVideos"
                                                    />
                                                    <VideoUserName
                                                        className="nameAudioBar"
                                                        user={conversation_users?.find(u => u.user_id === videoElementRepo.SEATS[i].userId)}
                                                    />
                                                </>
                                                {
                                                    videoElementRepo.SEATS[i].userId
                                                    && (videoElementRepo.SEATS[i].tileState === null || videoElementRepo.SEATS[i].tileState.active === false)
                                                    && <Helper.pageLoading />
                                                }
                                                <div className={"fs-other-btn"}>
                                                    {props.isSpaceHost ?
                                                        <>
                                                            <OtherButtons
                                                                setHoverSlider={setHoverSlider}
                                                                removeUser={(e, cb) => {
                                                                    dispatch(removeUser(e, cb))
                                                                }}
                                                                toggleBan={props.toggleBan}
                                                                buttonState={false}
                                                                userId={videoElementRepo.SEATS[i].userId}
                                                            />
                                                            {(sliderOpen === videoElementRepo.SEATS[i].userId) &&
                                                            <div
                                                                className="other-user-badge-popup"
                                                            >
                                                                <Slider onBlur={() => {
                                                                    setHoverSlider(false)
                                                                }}
                                                                        item={getUserData(conversation_users, videoElementRepo.SEATS[i].userId)} />
                                                            </div>
                                                            }
                                                        </>
                                                        :
                                                        <OtherBadgeButton
                                                            setHoverSlider={setHoverSlider}
                                                            userId={videoElementRepo.SEATS[i].userId}
                                                            conversation_users={conversation_users}
                                                            sliderOpen={sliderOpen}
                                                            isDummy={videoElementRepo.SEATS[i].isDummy}
                                                        />
                                                    }
                                                </div>
                                            </React.Fragment>
                                        }

                                        {/*<ConvSHButtons*/}
                                        {/*    setHoverSlider={setHoverSlider}*/}
                                        {/*    removeUser={(e, cb) => {*/}
                                        {/*        dispacth(removeUser(e, cb))*/}
                                        {/*    }}*/}
                                        {/*    toggleBan={toggleBan}*/}
                                        {/*    buttonState={buttonState}*/}
                                        {/*    userId={videoElementRepo.SEATS[i].userId}*/}
                                        {/*/>*/}
                                        {/*{banPopup && banUserId &&*/}
                                        {/*<BanPopup eventId={event_data.event_uuid}*/}
                                        {/*          toggleBan={hideBanPopUp}*/}
                                        {/*          banPopup={banPopup}*/}
                                        {/*          msg={msg}*/}
                                        {/*          user_id={banUserId}*/}
                                        {/*/>}*/}
                                        {/*{(sliderOpen === videoElementRepo.SEATS[i].userId) &&*/}
                                        {/*<div*/}
                                        {/*    className="other-user-badge-popup"*/}
                                        {/*>*/}
                                        {/*    <Slider onBlur={() => {*/}
                                        {/*        setHoverSlider(false)*/}
                                        {/*    }}*/}
                                        {/*            item={getUserData(conversation_users, videoElementRepo.SEATS[i].userId)}/>*/}
                                        {/*</div>*/}

                                    </div>
                                )
                            }
                            return <></>
                        })
                        }
                    </div>
                </div>
            </div>
            <div className={"full-screen-conv-btn container col-md-8 px-2"}>
                <div className={"fs-self-btn col-md-12 px-0"}>
                    {props.isSpaceHost
                        ? <ConvSHButtons
                            leaveConversation={leaveConversation}
                            setIsMute={props.setIsMute}
                            togglePopup={props.togglePopup}
                            isMute={props.conversationMeta.mute}
                            buttonState={false}
                            is_conversation_private={props.event_space.current_joined_conversation.is_conversation_private}
                            askToPrivateConversation={askToPrivateConversation}
                            toggleMediaDevicePopup={props.toggleMediaDevicePopup}
                            setShowDeviceSelector={props.setShowDeviceSelector}
                            setAvailableMediaDevice={props.setAvailableMediaDevice}
                            availableMediaDevices={props.availableMediaDevices}
                            setNoPreviewDiv={props.setNoPreviewDiv}
                        />
                        : <ConversationButton
                            togglePopup={props.togglePopup}
                            isMute={props.conversationMeta.mute}
                            setIsMute={props.setIsMute}
                            buttonState={false}
                            setShowDeviceSelector={props.setShowDeviceSelector}
                            setAvailableMediaDevice={props.setAvailableMediaDevice}
                            availableMediaDevices={props.availableMediaDevices}
                            setNoPreviewDiv={props.setNoPreviewDiv}
                            toggleMediaDevicePopup={props.toggleMediaDevicePopup}
                            is_conversation_private={props.event_space.current_joined_conversation.is_conversation_private}
                        />
                    }
                </div>
            </div>
        </div>

    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        updateConversationFullScreen: (data) => dispatch(newInterfaceActions.NewInterFace.setConversationFullScreen(data)),
        updateVideoMuteText: (data) => dispatch(newInterfaceActions.NewInterFace.updateVideoMuteText(data)),
    }
}

const mapStateToProps = (state) => {
    return {
        conversationMeta: state.NewInterface.conversationMeta,
        event_space: state.NewInterface.interfaceSpacesData,
        auth: state.NewInterface.interfaceAuth,
        SelfData: state.NewInterface.interfaceBadgeData,
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(FullScreenConversation);
