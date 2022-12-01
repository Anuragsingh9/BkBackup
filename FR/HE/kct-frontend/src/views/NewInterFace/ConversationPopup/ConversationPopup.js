import React, {useEffect, useState} from 'react';
import PopupClose from "../../../images/cross.svg";
import socketManager from '../../../socket/socketManager';
import {KeepContact as KCT} from '../../../redux/types';
import eventActions from '../../../redux/actions/eventActions';
import newInterfaceActions from '../../../redux/actions/newInterfaceAction';
import _ from 'lodash';
import Helper from '../../../Helper.js';
import {connect} from 'react-redux';
import {useTranslation} from "react-i18next"


let callInterval = null;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for rendering/displaying Conversation popup in Dashboard
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Object} props.alert Reference object for displaying notification popup
 * @param {UserBadge} props.event_badge Redux store mapped variable for holding user badge data
 * @param {EventData} props.event_data Redux store state variable to provide the event data
 * @param {Boolean} props.modal To indicate if conversation popup is visible or hidden
 * @param {Object} props.join_data Ask to join data when other user is asking to join the conversation
 * @param {String} props.join_data.conversation_id Conversation id to which user is asked to join
 * @param {String} props.join_data.space_id Space id of respective conversation
 * @param {String} props.join_data.event_id Event id of respective conversation
 * @param {String} props.join_data.target_user_id Target id for asking to join in conversation
 * @param {String} props.join_data.inviter_id Id of inviter user
 * @param {String} props.join_data.is_dummy_user To indicate if the user is dummy or real
 * @param {InterfaceSpaceData} props.spaceData Spaces data including conversations from redux store
 * @param {Function} props.conversationJoin To update current joined conversation data in redux
 * @param {Function} props.addMemberSingle To add the add the single user in existing conversation in redux
 * @param {Function} props.addMemberConversation To add the new conversation on grid from redux
 * @param {Function} props.changeConversationId To update the current conversation id when joined/leave conversation
 * @param {Function} props.toggleReset To update the visibility of conversation popup
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const ConversationPopup = (props) => {
    const [usersData, setUser] = useState([]);
    const [inviter_id, setInviter] = useState({});
    const {t} = useTranslation('popup');

    let styles = (props.modal) ? {display: "block", opacity: '1'} : {display: "none", opacity: '0'};

    useEffect(() => {

        callInterval = setTimeout(() => {
            props.toggleReset();
        }, 60000)
    }, []);


    useEffect(() => {
        const {join_data} = props;
        if (_.has(join_data, ['conversation_id'])) {
            if (join_data.conversation_id) {
                getConversationData(join_data.conversation_id);
            } else {
                getInviterData(join_data.inviter_id);
            }
        }
    }, [props.join_data]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the data of user to which current user is inviting in conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id Id of user to which inviting
     */
    const getInviterData = (id) => {
        const {spaces_data} = props;
        if (_.has(spaces_data, ['current_space_conversations']) && !_.isEmpty(spaces_data.current_space_conversations)) {
            spaces_data.current_space_conversations.map((item) => {
                if (item.conversation_users.length == 1) {
                    const data = item.conversation_users.filter((val) => {
                        if (val.user_id == id) {
                            return val;
                        }
                    });
                    if (!_.isEmpty(data)) {
                        setUser(data);
                    }
                }
            });
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the conversation data of current conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} conversation_uuid Conversation id for which data needs to set in state
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
     * @description To start the conversation with selected user from state variable
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    const startCall = () => {
        const {join_data} = props;
        let inviterId = _.has(props.join_data, ['inviter_id']) ? props.join_data.inviter_id : null;

        socketManager.emitEvent.CALL_ACKNOWLEDGEMENT({
            event_id: props.event_data.event_uuid,
            inviter_id: inviterId,
            state: 1,
        })

        if (!_.isEmpty(usersData) && _.has(join_data, ['space_id'])) {
            clearInterval(callInterval);
            joinConversation(join_data.space_id);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the rejection button by the user in join request popup
     * This will reject the call as well as send the socket event
     * to tell the inviter that current user has rejected the call
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const rejectCall = () => {
        props.toggleReset();
        let inviterId = _.has(props.join_data, ['inviter_id']) ? props.join_data.inviter_id : null;

        if (inviterId) {
            socketManager.emitEvent.CALL_ACKNOWLEDGEMENT({
                event_id: props.event_data.event_uuid,
                inviter_id: inviterId,
                state: 0,
            })
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles second user's id for start the call .
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {ConversationData[]} conversation Conversations data to find the other user conversation if available
     * @returns {Number}
     */
    const getSecondUserId = (conversation) => {
        let userId = null;
        conversation.conversation_users.forEach((value) => {
            if (!value.hasOwnProperty('is_self')) {
                userId = value.user_id;
            }
        })
        return userId;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To join the conversation with selected user and send the data to backend and start
     * chime conversation
     * After successful join a socket event will be emitted to tell all the other user that current
     * user has started conversation with selected user(s)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} space_uuid Space id in which the user is starting conversation
     */
    const joinConversation = (space_uuid) => {
        try {
            const formData = new FormData()
            const user = usersData[0];
            const userId = user.user_id
            formData.append('space_uuid', space_uuid);
            if (user.is_dummy == 1) {

                formData.append('dummy_user_id', userId);

            } else {

                formData.append('user_id', userId);
            }
            let accessCode = props.event_data.accessCode;
            if (!accessCode) {
                accessCode = localStorage.getItem("accessCode");
            }
            if (accessCode) {
                formData.append('access_code', accessCode);
            }
            props.conversationJoin(formData).then((res) => {
                if (res.data.data) {
                    props.addMemberConversation(res.data.data, userId);
                    props.changeConversationId(res.data.data)
                    const user_Id = getSecondUserId(res.data.data)
                    props.executeScroll && props.executeScroll();
                    let conversationType = 'joined';
                    if (res.data.data.conversation_users.length === 2) {
                        socketManager.emitEvent.CONVERSATION_CREATE({
                            eventId: props.event_data.event_uuid,
                            userId: user_Id
                        });
                        conversationType = 'created';
                    }
                    socketManager.emitEvent.CONVERSATION_JOIN({
                        type: conversationType,
                        fromUserId: props.event_badge.user_id,
                        toUserId: user_Id,
                        conversationId: res.data.data.conversation_uuid
                    });
                    props.toggleReset();
                }
            })
                .catch((err) => {
                    console.error("errrrr", err)
                    props.alert.show(Helper.handleError(err), {type: 'error'})
                    props.toggleReset();

                })
        } catch (err) {
            console.error("errrrr", err)
            props.alert.show(Helper.handleError(err), {type: 'error'})
            props.toggleReset();
        }
    }

    return (
        <div id="" className="modal fade in" role="dialog" style={styles}>
            <div className="modal-dialog">
                <div className="">
                    <div className="modal-header">
                        <button type="button" onClick={props.toggleReset} className={"close"}><img src={PopupClose} />
                        </button>
                        <h4 className="modal-title">
                            <span>{t("Join Conversation")}</span>
                        </h4>
                    </div>
                    <div className="modal-body">
                        <p>
                            {!_.isEmpty(usersData) && usersData.map((item, key) => {
                                if (_.has(props.join_data, ['inviter_id']) && props.join_data.inviter_id == item.user_id) {
                                    return (
                                        <span>{item.user_fname} {_.has(item, ['user_lname']) ? item.user_lname : ''} {t("wants to start a conversation with you")} </span>)
                                }

                            })}
                            {!_.isEmpty(usersData) && usersData.length > 1 &&
                            <span>{t("and")}</span>
                            }
                            {!_.isEmpty(usersData) && usersData.map((item, key) => {
                                if (_.has(props.join_data, ['inviter_id']) && props.join_data.inviter_id != item.user_id) {
                                    return (
                                        <span>, {item.user_fname} {_.has(item, ['user_lname']) ? item.user_lname : ''}</span>)
                                }

                            })}

                        </p>
                        <button className="btn btn-primary" onClick={startCall}>{t("Join")}</button>
                        {" "}
                        <button className="btn btn-primary" onClick={rejectCall}>{t("Reject")}</button>
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
        toggleReset: () => dispatch(newInterfaceActions.NewInterFace.turnOffJoin())
    }
}

const mapStateToProps = (state) => {
    return {
        event_badge: state.NewInterface.interfaceBadgeData,
        event_data: state.NewInterface.interfaceEventData,
        modal: state.NewInterface.callJoinState.valid,
        join_data: state.NewInterface.callJoinState.data,
        spaces_data: state.NewInterface.interfaceSpacesData,

    };
};


export default connect(mapStateToProps, mapDispatchToProps)(ConversationPopup);


