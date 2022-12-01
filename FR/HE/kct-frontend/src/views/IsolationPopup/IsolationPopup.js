import React, {useEffect, useRef, useState} from 'react';
import newInterfaceActions from '../../redux/actions/newInterfaceAction';
import eventActions from '../../redux/actions/eventActions';
import socketManager from '../../socket/socketManager';
import {KeepContact as KCT} from '../../redux/types';
import {Provider as AlertContainer, useAlert } from 'react-alert';
import {connect} from 'react-redux';
import Helper from '../../Helper.js';
import _ from 'lodash';
import {useTranslation} from 'react-i18next';


let callInterval = null;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for rendering/displaying isolation popup in Dashboard
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props
 * @param {InterfaceSpaceData} props.spaces_data Spaces data including conversations from redux store
 * @param {Object} props.join_data Ask to join data when other user is asking to join the conversation
 * @param {String} props.join_data.conversation_id Conversation id to which user is asked to join
 * @param {String} props.join_data.space_id Space id of respective conversation
 * @param {String} props.join_data.event_id Event id of respective conversation
 * @param {String} props.join_data.target_user_id Target id for asking to join in conversation
 * @param {String} props.join_data.inviter_id Id of inviter user
 * @param {String} props.join_data.is_dummy_user To indicate if the user is dummy or real
 *
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const IsolationPopup = (props) => {
    const [usersData, setUser] = useState([]);
    const {join_data, spaces_data} = props;
    let styles;
    let currentUserIds;
    const {t} = useTranslation(['popup', 'notification'])

    if (spaces_data.current_joined_conversation !== null) {
        currentUserIds = spaces_data.current_joined_conversation.conversation_users.map(({user_id}) => user_id);
        styles = (join_data.senderId != props.event_badge.user_id && currentUserIds.includes(props.event_badge.user_id)) ? {
            display: "block",
            opacity: '1'
        } : {display: "none", opacity: '0'};
    }

    const msg = useAlert();

    useEffect(() => {
        callInterval = setTimeout(() => {
            props.toggleReset();
        }, 10000)
    }, []);


    useEffect(() => {

        if (_.has(join_data, ['conversationId'])) {
            getConversationData(join_data.conversationId);
        }
    }, [join_data]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles get conversation data.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} conversation_uuid Uuid of current conversation
     */
    const getConversationData = (conversation_uuid) => {
        const {spaces_data} = props;
        if (_.has(spaces_data, ['current_space_conversations']) && !_.isEmpty(spaces_data.current_space_conversations)) {
            spaces_data.current_space_conversations.map((item) => {
                if (item.conversation_uuid == conversation_uuid) {
                    setUser(item.conversation_users);
                }
            });
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles rejection for private Conversation .
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const rejectPrivateConversation = () => {
        clearInterval(callInterval);
        askToPrivateConversation();

        let userNmae = props.event_badge.user_fname + ' ' + props.event_badge.user_lname;
        if (currentUserIds.includes(props.event_badge.user_id)) {
            msg.show(userNmae + ' ' + t("notification:isolation notification normal"), {
                type: "error",
            });
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles asking for private Conversation to other users
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const askToPrivateConversation = () => {
        const {event_data, event_badge} = props;
        if (spaces_data.current_joined_conversation === null) {
            return null;
        }

        const formData = new FormData()
        const conversation_uuid = spaces_data.current_joined_conversation.conversation_uuid
        formData.append('conversation_uuid', conversation_uuid);
        formData.append('is_private', 0);
        try {
            props.privateConversation(formData).then((res) => {
                socketManager.emitEvent.PRIVATE_CONVERSATION({
                    namespace: Helper.getNameSpace(),
                    spaceId: spaces_data.current_joined_space.space_uuid,
                    eventId: event_data.event_uuid,
                    conversationId: conversation_uuid,
                    is_private: res.data.data.is_conversation_private,
                    senderId: event_badge.user_id,
                })
                msg.show(res.data.data.is_conversation_private ? t("Conversation Isolated") : t("Conversation is not isolated anymore"), {
                    type: "success",
                });
                const data = {
                    conversation_uuid: conversation_uuid,
                    current_state: res.data.data.is_conversation_private
                }
                props.updatePrivateConversation(data);
                props.toggleReset();

            });

        } catch (e) {
            console.error(e);
        }
    }
    return (
        <div id="" className="modal fade in" role="dialog" style={styles}>
            <div className="modal-dialog">
                <div className="">
                    <div className="modal-header">
                        <AlertContainer ref={msg}{...Helper.alertOptions} />
                        <h4 className="modal-title">
                            <span>{t("Join Private Conversation")}</span>
                        </h4>
                    </div>
                    <div className="modal-body">
                        <p>
                            {!_.isEmpty(usersData) && usersData.map((item, key) => {
                                if (_.has(join_data, ['senderId']) && join_data.senderId == item.user_id) {
                                    return (
                                        <span>{item.user_fname} {_.has(item, ['user_lname']) ? item.user_lname : ''} {t("has been switched conversation in isolated mode")} </span>)
                                }

                            })}
                            {!_.isEmpty(usersData) && usersData.map((item, key) => {
                                if (_.has(join_data, ['senderId']) && join_data.senderId != item.user_id) {
                                    return (
                                        <span>, {item.user_fname} {_.has(item, ['user_lname']) ? item.user_lname : ''}</span>)
                                }

                            })}

                        </p>
                        <button className="btn btn-primary" onClick={props.toggleReset}>{t("Accept")}</button>
                        {" "}
                        <button className="btn btn-primary" onClick={rejectPrivateConversation}>{t("Reject")}</button>
                    </div>
                </div>
            </div>
        </div>
    );

};

const mapDispatchToProps = (dispatch) => {
    return {
        conversationJoin: (id) => dispatch(eventActions.Event.conversationJoin(id)),
        addMemberSingle: (data, index) => dispatch({
            type: KCT.NEW_INTERFACE.ADD_EVENT_MEMBERS_SINGLE,
            payload: data,
            index: index
        }),
        addMemberConversation: (data, index) => dispatch({
            type: KCT.NEW_INTERFACE.ADD_EVENT_MEMBER_CONVERSATIONS,
            payload: data,
            userId: index
        }),
        changeConversationId: (id) => dispatch({type: KCT.NEW_INTERFACE.CHANGE_CONVERSATION_IDS, payload: id}),
        toggleReset: () => dispatch(newInterfaceActions.NewInterFace.privateTurnOffJoin()),
        privateConversation: (id) => dispatch(eventActions.Event.privateConversation(id)),
        updatePrivateConversation: (data) => dispatch(newInterfaceActions.NewInterFace.updatePrivateConversation(data))
    }
}

const mapStateToProps = (state) => {
    return {
        event_badge: state.NewInterface.interfaceBadgeData,
        event_data: state.NewInterface.interfaceEventData,
        modal: state.NewInterface.privateCallJoinState.valid,
        join_data: state.NewInterface.privateCallJoinState.data,
        spaces_data: state.NewInterface.interfaceSpacesData,

    };
};


export default connect(mapStateToProps, mapDispatchToProps)(IsolationPopup);


