import React, {useEffect, useState} from 'react';
import _ from 'lodash';
import Helper from '../../../../../src/Helper';
import Svg from "../../../../Svg";
import "./SpaceHost.css";
import ConversationWrapper from '../../Conversation/UI/Wrapper/ConversationWrapper.js';
import {useTranslation} from 'react-i18next';
import ReactTooltip from 'react-tooltip';
import newInterfaceActions from "../../../../redux/actions/newInterfaceAction";
import {connect} from "react-redux";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used for host conversation.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Label[]} event_labels All the event labels with different locales
 * @param {Function} askToJoinCall Function is used to ask user to join conversation
 * @param {UserBadge} event_badge User badge details
 * @param {Boolean} availabilityHost To indicate if space host is available to take call or not
 * @param {Object} spaceHost Current space host data
 * @param {Boolean} active To indicate if space host is available or not
 * @param {Boolean} hostThere To indicate if space host is online or offline
 * @param {Boolean} event_during To indicate if the event is live or not
 * @param {Function} hideHostCallBtn To hide space host call button
 * @param {Function} updateProfileData Redux Dispatcher to update profile all data
 *
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
let HostConversationBlock = (
    {
        event_labels,
        askToJoinCall,
        event_badge,
        availabilityHost,
        spaceHost,
        active,
        hostThere,
        event_during,
        hideHostCallBtn,
        updateProfileData,
        ...props
    }) => {
    const [showHostCallBtn, setShowHostCallBtn] = useState(false)
    const {t} = useTranslation('spaceHost');
    const spaceHostData = !_.isEmpty(spaceHost) ? spaceHost : {};
    const isSpaceHost = _.has(event_badge, ['user_id']) && _.has(spaceHostData, ['user_id'])
        && spaceHostData.user_id == event_badge.user_id;

    useEffect(() => {
        setShowHostCallBtn(hideHostCallBtn)
    }, [])

    useEffect(() => {

    }, [props.conversationMeta.fullScreen]);

    return (
        <>
            {
                !isSpaceHost && props.conversationMeta.fullScreen
                    ?
                    <></>
                    :
                    <React.Fragment>
                        <div className="col-md-4 col-sm-4 main-host" id="main-host-zIndex">

                            <div
                                className={`text-center position-relative ${(active && hostThere) && event_during &&
                                _.has(spaceHost, ['user_id']) && 'shortHostOuter'} host-outer kct-customization`}>
                                {(active && hostThere) && event_during ?
                                    <ConversationWrapper
                                        isSpaceHost={true}
                                        hostVideo={isSpaceHost}
                                        updateProfileData={updateProfileData}
                                    />
                                    :
                                    <React.Fragment>
                                        {spaceHost.avatar ?
                                            <img className="img-fluid hostphoto no-texture" src={spaceHost.avatar}
                                                 alt="" />

                                            :
                                            <div className="background-space-host">
                                                <div
                                                    className="username-slider-dp no-texture">{Helper.nameProfile(spaceHost.fname, spaceHost.lname)}</div>
                                            </div>

                                        }
                                    </React.Fragment>
                                }
                                <h6>{spaceHost.fname + ' ' + spaceHost.lname}</h6>

                                <span>

                                    {(event_badge.user_id != spaceHost.user_id && availabilityHost) && !((active && hostThere) && event_during) && hideHostCallBtn == true &&
                                    <ul className="d-inline-block host-left-icon tt" data-for='call_sh'
                                        data-tip={t("Call spaceHost")}>
                                        <li className="mb-2 host-reception">
                                            <button type="button"
                                                    className="control-button video-buttons"
                                                    data-for='conversation'
                                                    onClick={askToJoinCall}
                                            >
                                                    <span className="svgicon no-texture"
                                                          dangerouslySetInnerHTML={{__html: Svg.ICON.reception}}></span>
                                            </button>
                                        </li>
                                        <ReactTooltip type="dark" effect="solid" id='call_sh' />
                                    </ul>
                                    }

                                </span>
                            </div>


                        </div>
                    </React.Fragment>
            }
        </>
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
    };
};
HostConversationBlock = connect(mapStateToProps, mapDispatchToProps)(HostConversationBlock);
export default HostConversationBlock;