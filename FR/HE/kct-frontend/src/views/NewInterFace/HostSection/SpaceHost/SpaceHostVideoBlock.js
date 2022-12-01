import React from 'react';
import {connect} from 'react-redux';
import _ from 'lodash';
import socketManager from '../../../../socket/socketManager';
import {KeepContact as KCT} from '../../../../redux/types';
import eventActions from '../../../../redux/actions/eventActions';
import newInterfaceActions from '../../../../redux/actions/newInterface/index';
import "./SpaceHost.css";
import {withTranslation} from 'react-i18next';
import ContentManagement from "./ContentManagement";
import HostConversationBlock from "./HostConversationBlock";
// import { useTranslation } from 'react-i18next';


var statusInterval;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description - This component is developed to call the Space Host of a Space.
 * This button is not displayed when the Space Host has not connected and is not present on the dashboard.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Label[]} props.event_labels All the event labels with different locales
 * @param {UserBadge} props.spaceHostData Space host data in form of user badge
 * @param {InterfaceSpaceData} props.spaceData Spaces data including conversations from redux store
 * @param {UserBadge} props.event_badge User badge details
 * @param {EventData} props.event_data Current event data
 * @param {Boolean} props.isPrivate To indicate if conversation is private or not
 * @param {Boolean} props.availabilityHost To indicate if space host is available to take call or not
 * @param {ContentManagementMeta} props.contentManagementMeta Content Related Props from redux store for current content
 * @param {Function} props.leaveConversation To leave the conversation and release the devices
 * @param {Function} props.deleteConversation To leave and delete the self conversation data
 * @param {Function} props.addMemberConversation To add the new conversation on grid from redux
 * @param {Function} props.addUserToActiveCon To add the user to current active conversation
 * @param {Function} props.changeConversationId To change the conversation id when someone join/leave conversation
 * @param {Function} props.callOff To show or hide the calling popup when calling to other user
 * @param {Function} props.callTrigger To call the api for joining conversation
 * @param {Function} props.setCalledUserId To update the caller id to display the calling popup
 * @param {Function} props.privateConversation To make the conversation private
 * @class
 * @component
 */
class SpaceHostVideoBlock extends React.Component {


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to ask to join call to space host that emits an event on socket with
     *  data like conversation_id , space_id , event_id , target_user_id and inviter_id . if user is dummy it will be
     *  directly and if space host is not available it will show notification of not availability.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    askToJoinCall = () => {
        const {spaceData,} = this.props;
        const {spaceHostData} = this.props;
        const {hideHostCallBtn} = this.props;
        const spaceHost = spaceHostData[0];
        if (!_.isEmpty(spaceHostData)) {
            const user = spaceHost;
            const userId = spaceHost.user_id;
            this.props.setCalledUserId(userId);
            const socket_data = {
                conversation_id: spaceData.current_joined_conversation
                    ? spaceData.current_joined_conversation.conversation_uuid
                    : '',
                space_id: spaceData.current_joined_space.space_uuid,
                event_id: this.props.event_data.event_uuid,
                target_user_id: userId.toString(),
                inviter_id: this.props.event_badge.user_id,
                is_dummy_user: _.has(user, ['is_dummy']) ? user.is_dummy : 0,
            };

            socketManager.emitEvent.ASK_JOIN_CONVERSATION(socket_data);

            if (!_.has(user, ['is_dummy'])) {
                this.checkInviteSucess(user.user_id);
            }

        } else {
            this.props.alert.show("Space Host not available", {type: 'error'})
        }

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to check the invite status in every 2 sec interval. and handles calls off or
     *  exist from the call in 2 sec if conversation is not started .
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id Id of the user to check for the invite status
     */
    checkInviteStatus = (id) => {
        const {spaceData} = this.props;
        let successInterval = setInterval(() => {
            if (this.props.spaceData.current_joined_conversation !== null) {
                const {conversation_users} = this.props.spaceData.current_joined_conversation;
                const {spaceHostData} = this.props;
                if (!_.isEmpty(spaceHostData) && !_.isEmpty(conversation_users)) {
                    const exist = conversation_users.filter((item) => (item.user_id == id))
                    if (!_.isEmpty(exist)) {
                        clearTimeout(statusInterval);
                        this.props.callOff();
                        clearInterval(successInterval);
                    }
                } else {
                    // this.props.callOff();
                }
            } else {
                // this.props.callOff();
            }
        }, 2000);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to check if invite status is successfully or not. if user not accept the
     * invitation it will show a massage for not availability after 65 seconds.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id Id of the user to check for the invite success check
     * @param {Function} t To provide the locale translation from i18n
     */
    checkInviteSucess = (id, t) => {
        const {spaceData} = this.props;
        this.props.callTrigger();
        this.checkInviteStatus(id);
        statusInterval = setTimeout(() => {
            if (this.props.spaceData.current_joined_conversation !== null) {
                const {conversation_users} = this.props.spaceData.current_joined_conversation;
                const {spaceHostData} = this.props;
                if (!_.isEmpty(spaceHostData)) {
                    const exist = conversation_users.filter((item) => (item.user_id == id))
                    if (_.isEmpty(exist) && !_.isEmpty(conversation_users)) {
                        this.msg && this.msg.show(t("Participant is not available now"));
                        this.props.callOff();
                    }

                } else {
                    this.props.callOff();
                }
            } else {
                this.props.callOff();
            }
        }, 65000)

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to check space host from space data in conversation is joined or not and
     * return Boolean value.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {Boolean}
     */
    checkSpaceHost = () => {
        const {spaceData, spaceHostData, event_badge} = this.props;

        const {current_joined_conversation} = spaceData;

        if (_.has(current_joined_conversation, ['conversation_users'])
            && !_.isEmpty(current_joined_conversation.conversation_users) && !_.isEmpty(spaceHostData)) {

            const flag = current_joined_conversation.conversation_users.filter((val) => {
                if (val.user_id == spaceHostData[0].user_id) {
                    return val
                }
            });

            return !_.isEmpty(flag)

        } else {
            return false;
        }

    }

    render() {
        const {active, event_during, event_badge, spaceData, availabilityHost, sh_hide_on_off} = this.props;
        const {event_data, event_labels} = this.props;
        const props = this.props;
        const openPlayer = true;
        const {spaceHostData} = this.props;
        const spaceHost = !_.isEmpty(spaceHostData) ? spaceHostData[0] : {};
        const hostThere = this.checkSpaceHost();
        const {t} = props;

        let showHostSection = true;

        if (availabilityHost === false) {
            // host is offline
            if (_.has(spaceHost, ['user_id']) && event_badge.user_id == spaceHost.user_id) {
                // self user is host, show the section
                showHostSection = true;
            } else {
                // self user is not host, check does design setting allow to show or not
                showHostSection = sh_hide_on_off === 0; // if hide section is off, means show section even if host offline
            }
        }


        return (
            <>
                {
                    // currently hiding the host section as its shifted to button below user badge
                    (false && !((active && hostThere) && event_during) && showHostSection) &&
                    <section
                        className={`host-section${(active && hostThere) && event_during ? '-slide-down' : ''}`}>
                        {/* <AlertContainer ref={this.msg}{...Helper.alertOptions} /> */}
                        <div className="container container_hostBlock">
                            <div className="col-md-12 row Sh_block_row">
                                <HostConversationBlock
                                    event_labels={event_labels}
                                    askToJoinCall={this.askToJoinCall}
                                    hideHostCallBtn={this.props.hideHostCallBtn}
                                    event_badge={event_badge}
                                    availabilityHost={availabilityHost}
                                    spaceHost={spaceHost}
                                    active={active}
                                    hostThere={hostThere}
                                    event_during={event_during}
                                    openPlayer={openPlayer}
                                    event_data={event_data}
                                    up={true}
                                    conference={(_.has(event_data, ['conference_type']) && event_data.conference_type != null)}
                                    updateProfileData={this.props.updateProfileData}
                                />
                            </div>
                        </div>
                    </section>
                }


                {event_during &&  <ContentManagement
                    video_url={this.props.video_url}
                    event_data={this.props.event_data}
                    hostThere={hostThere}
                    {...this.props}
                />}

                {((active && hostThere) && event_during && showHostSection) &&
                <section className={`host-section${(active && hostThere) && event_during ? '-slide-down' : ''}`}>
                    {/* <AlertContainer ref={this.msg}{...Helper.alertOptions} /> */}
                    <div className="container container_hostBlock">
                        <div className="col-md-12 row Sh_block_row">
                            <HostConversationBlock
                                event_labels={event_labels}
                                askToJoinCall={this.askToJoinCall}
                                hideHostCallBtn={this.props.hideHostCallBtn}
                                event_badge={event_badge}
                                availabilityHost={availabilityHost}
                                spaceHost={spaceHost}
                                active={active}
                                hostThere={hostThere}
                                event_during={event_during}
                                openPlayer={openPlayer}
                                event_data={event_data}
                                up={true}
                                conference={(_.has(event_data, ['conference_type']) && event_data.conference_type != null)}
                                updateProfileData={this.props.updateProfileData}
                            />
                        </div>
                    </div>
                </section>

                }
            </>

        )
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        leaveConversation: (id) => dispatch(eventActions.Event.leaveConversation(id)),
        deleteConversation: (id) => dispatch({type: KCT.EVENT.DELETE_CONVERSATION, payload: id}),
        addMemberConversation: (data, index) => dispatch({
            type: KCT.NEW_INTERFACE.ADD_EVENT_MEMBER_CONVERSATIONS,
            payload: data,
            userId: index
        }),
        addUserToActiveCon: (userId) => dispatch({
            type: KCT.NEW_INTERFACE.ADD_USER_TO_ACTIVE_CONVERSATIONS,
            payload: userId
        }),
        changeConversationId: (id) => dispatch({type: KCT.NEW_INTERFACE.CHANGE_CONVERSATION_IDS, payload: id}),
        callOff: () => dispatch(newInterfaceActions.callOff()),
        callTrigger: () => dispatch(newInterfaceActions.callTrigger()),
        setCalledUserId: (id) => dispatch(newInterfaceActions.setCalledUserId(id)),
        privateConversation: (id) => dispatch(eventActions.Event.privateConversation(id)),
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
    }
}

SpaceHostVideoBlock =  connect(mapStateToProps, mapDispatchToProps)(SpaceHostVideoBlock);
withTranslation('spaceHost')(SpaceHostVideoBlock)
export default SpaceHostVideoBlock;
