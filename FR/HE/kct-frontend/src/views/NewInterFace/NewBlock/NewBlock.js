import React, {useEffect, useRef} from 'react';
import CountDownTimer from '../CountDownTimer/CountDownTImer.js';
import MyBadgeBlock from '../MyBadge/MyBadgeBlock.js';
import _ from 'lodash';
import HostConversationBlock from '../Conversation/UI/Wrapper/HostConversationBlock.js';
import ConversationWrapper from '../Conversation/UI/Wrapper/ConversationWrapper.js';
import {connect} from "react-redux";
import Constants from "../../../Constants";
import Helper from "../../../Helper";
import {useAlert} from 'react-alert';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for rendering conditionally conversation block or user self info block
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Boolean} isSpaceHost To indicate if the conversation user is space host or not
 * @param {Object} props Props passed from parent component
 * @param {Function} props.updateProfileData To update the user data on server
 *
 * @returns {JSX.Element}
 * @constructor
 */
const RenderVideo = (isSpaceHost, props) => {
    return (
        <React.Fragment>
            {isSpaceHost ?
                <HostConversationBlock />
                :
                <ConversationWrapper updateProfileData={props.updateProfileData} isSpaceHost={false} />
            }
        </React.Fragment>
    )
}

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used to show user badge component
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Boolean} props.active To indicate if space host is active or not
 * @param {EventData} props.event_data Current event data
 * @param {Boolean} props.event_during To indicate if event is live so event live text can be displayed instead timer
 * @param {Boolean} props.is_event_end To indicate if event is ended or not as ended event will have different text
 * @param {UserBadge} props.spaceHost Current space host data
 * @param {UserBadge} props.event_badge User badge details
 * @param {Object} props.myBadgeBlockSceneryStyle_childDiv CSS Style object to apply the css on scenery data
 * @param {String} props.userFirstName User First Name
 * @param {String} props.userLastName User Last Name
 * @param {Function} props.hideSHButton To hide the space host calling button
 * @returns {JSX.Element}
 * @constructor
 */
let NewBlock = (props) => {
    const {
        active,
        event_data,
        event_during,
        is_event_end,
        spaceHost,
        event_badge,
        myBadgeBlockSceneryStyle_childDiv,
        userFirstName,
        userLastName,
        hideSHButton,
    } = props;

    const msg = useAlert();

    /**
     * @deprecated
     */
    const showAlert = (data, options = {}) => {
        msg && msg && msg.show(data, options);
    }

    // to indicate if current user is space host or not
    const isCurrentUserSpaceHost = !_.isEmpty(spaceHost) && spaceHost[0].user_id == event_badge.user_id;
    // to indicate if its possible to call host or not

    useEffect(() => {
        if(!props.spaces_data?.current_joined_conversation) {
            if (props.contentManagementMeta?.currentMediaType === Constants.contentManagement.CNT_MGMT_ZOOM_SDK) {
                Helper.zoom.unmute();
            } else {
                Helper.zoom.mute();
            }
        }
    }, [props.spaces_data?.current_joined_conversation]) ;

    const conversationOn = active && event_during;
    return (
        <div className={`container waiting-bg kct-customization ${conversationOn ? 'sm-waiting-bg' : ''}`}
             style={myBadgeBlockSceneryStyle_childDiv}>
            <div className={`badge-bg ${conversationOn ? 'sm-badge-bg' : ''}`} id="dynamicZindex">
                {active && event_during ?
                    <div className="video-meeting-top" id='video-meeting-top-zIndex'>
                        {RenderVideo(isCurrentUserSpaceHost, props)}
                    </div>
                    :
                    <div className="row">
                        <div className="col-md-9 posi-fix">
                            <MyBadgeBlock
                                event_data={event_data}
                                event_during={props.event_during}
                                is_event_end={is_event_end}
                                userFirstName={userFirstName}
                                userLastName={userLastName}
                                hideSHButton={hideSHButton}
                            />
                        </div>
                        <div className="col-md-3 pt-20">
                            {!props.event_during &&
                            <CountDownTimer
                                event_during={props.event_during}
                                page_Customization={{
                                    time_zone: 'Europe/Paris',
                                    event_start_time: event_data.event_start_time,
                                    event_date: event_data.event_date
                                }}
                                is_event_end={is_event_end}
                            />
                            }
                        </div>
                    </div>
                }
                {/* npm */}
            </div>
        </div>
    )
}


const mapStateToProps = (state) => {
    return {
        contentManagementMeta: state.NewInterface.contentManagementMeta,
        spaces_data: state.NewInterface.interfaceSpacesData,
    };
};

NewBlock = connect(mapStateToProps, null)(NewBlock);
export default NewBlock;

