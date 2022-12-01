import React, {useEffect, useRef, useState} from 'react';
import './ToolBar.css';
import {connect} from 'react-redux';
import eventActions from '../../../../redux/actions/eventActions';
import Helper from '../../../../Helper.js';
import {KeepContact as KCT} from '../../../../redux/types';
import _ from 'lodash';
import socketManager from "../../../../socket/socketManager";
import ReactTooltip from "react-tooltip";
import OutsideClickHandler from 'react-outside-click-handler';
import {Provider as AlertContainer,useAlert } from 'react-alert';
import {useTranslation} from 'react-i18next';


import styled from "styled-components";
import Svg from '../../../../Svg.js';

const ToolbarDiv = styled.div`
    width: ${props => props.userLength * (props.clientWidth > 1300
    ? 100
    : props.clientWidth && props.clientWidth < 835 ? 64 : 80)}px;    
    right: ${props => props.lastInList ? '0%' : 'auto'} ;
    left: ${props => props.lastSingle ? 'auto' : props.margin >= 700 ? -674 : -props.margin}px ;
`;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used for displaying an user related data on mouse hover like user avatar,event role,
 * role icon and button to start conversion in user grid section.
 * It also prepares and handles the start conversation functionality.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.addMemberSingle To add the add the single user in existing conversation in redux
 * @param {Function} props.addMemberConversation To add the new conversation on grid from redux
 * @param {Function} props.changeConversationId To update the current conversation id when joined/leave conversation
 * @param {Function} props.clickReset To reset the toolbar display values
 * @param {UserBadge} props.event_badge Redux store mapped variable for holding user badge data
 * @param {GraphicsData} props.event_roles [State] This variable holds the current graphics data set in redux
 * @param {Function} props.executeScroll To scroll the toolbar
 * @param {EventData} props.event_data Current event data
 * @param {Function} props.conversationJoin To update current joined conversation data in redux
 * @param {Function} props.addMemberSingle To add the add the single user in existing conversation in redux
 * @param {Function} props.addMemberConversation To add the new conversation on grid from redux
 * @param {Function} props.changeConversationId To update the current conversation id when joined/leave conversation

 * @class
 * @component
 */
let ToolBar = (props) => {
    const clientWidth = document.documentElement.clientWidth;

    const {t} = useTranslation(['grid', 'notification'])

    // current hovered user index state
    const [currentUser, setUser] = useState(0);

    // Initialisation fo message / alert ref to show alerts on success or error.
    const msg = useAlert();

    // max users visible inside toolbar
    let maxLength = (!_.isEmpty(props.spaceHostData) &&
        props.event_badge.user_id == props.spaceHostData[0].user_id) ? props.event_data?.event_conv_limit + 1 : props.event_data?.event_conv_limit;

    // props data
    let {position, data} = props;

    let hoverUser = data.conversation_users[position];

    const toolbarUsers = [];
    data.conversation_users.forEach(user => {
        if (!props.spaceHostData.find(host => host.user_id === user.user_id)) {
            toolbarUsers.push(user);
        }
    });

    toolbarUsers.forEach((user, index) => {
        if (user.user_id === hoverUser.user_id) {
            position = index;
        }
    });

    // margin of toolbar according to position
    const margin = clientWidth > 1300 ? position * 100 : clientWidth > 835 ? position * 80 : position * 64;
    // users array


    // filtered user array excluding space host
    const users = toolbarUsers.filter((val) => {
        if (!(!_.isEmpty(props.spaceHostData) && props.spaceHostData[0].user_id == val.user_id)) {
            return val;
        }
    });

    // total columns
    const column = Number.parseInt(12 / users.length);

    // last single person in row 
    const lastSingle = (props.lastPerson && users.length == 1);

    const [vipIcon, setVipIcon] = useState(null);
    const [teamIcon, setTeamIcon] = useState(null);
    const [vipAltImage, setVipAltImage] = useState(null);
    const [teamAltImage, setTeamAltImage] = useState(null);
    const [expertIcon, setExpertIcon] = useState(null);
    const [expertAltImage, setExpertAltImage] = useState(null);

    useEffect(() => {
        setRoleData()
    }, [props])

    useEffect(() => {
        if (users.length > currentUser) {
            props.updateGridUser(users[currentUser]);
        }
    }, [currentUser]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update state of all role icons
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const setRoleData = () => {
        let {
            vip_icon,
            vip_altImage,
            business_team_icon,
            business_team_altImage,
            expert_icon,
            expert_altImage
        } = props.event_roles;
        if (vip_icon) {
            setVipIcon(vip_icon);
        }
        if (business_team_icon) {
            setTeamIcon(business_team_icon)
        }
        if (vip_altImage) {
            setVipAltImage(vip_altImage);
        }
        if (business_team_altImage) {
            setTeamAltImage(business_team_altImage);
        }
        if (expert_icon) {
            setExpertIcon(expert_icon)
        }
        if (expert_altImage) {
            setExpertAltImage(expert_altImage)
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method returns the user id of another person in the conversation.
     * It will always exclude self user.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {ConversationData} conversation Conversation data object.
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
     * @description This method prepares data and handles socket call for ask to join call(request to add user in
     * conversation) and handles the response accordingly.
     *
     * Currently there can be maximum of 4 users and 1 space host in a conversation.
     *
     * If requested user is already in a conversation then user need to disconnect from current conversation
     * to join the new conversation.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const askToJoinCall = () => {
        const {conversation_users} = props.event_space.current_joined_conversation;
        const calledUser = users[0];
        if (!_.isEmpty(users) && users.length == 1 && !_.isEmpty(conversation_users) &&
            conversation_users.length < isSpaceHostPartOfConv(toolbarUsers, calledUser)) {
            const user = users[0];
            const userId = users[0].user_id;
            const space_id = props.space_uuid;
            const {conversation_uuid} = props.event_space.current_joined_conversation;
            // preparing the socket related data to emit ASK_JOIN_CONVERSATION event.
            const socket_data = {
                conversation_id: conversation_uuid,
                space_id: space_id,
                event_id: props.event_data.event_uuid,
                target_user_id: userId.toString(),
                inviter_id: props.event_badge.user_id,
                is_dummy_user: _.has(user, ['is_dummy']) ? user.is_dummy : 0,
            };
            socketManager.emitEvent.ASK_JOIN_CONVERSATION(socket_data);
            if (!_.has(user, ['is_dummy'])) {
                props.checkInviteSucess(user.user_id);
            }
        } else {
            props.alert.show(t("Please disconnect current conversation !!"), {type: 'error'})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if space host is part of conversation and return the conversation users length accordingly.
     * If space host is part of conversation then return 5 else return 4.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {UserBadge[]} usersArr Users of conversation
     * @param {UserBadge} calledUser User badge who called the space host
     * @return {Number}
     */
    const isSpaceHostPartOfConv = (usersArr, calledUser) => {
        if (!_.isEmpty(props.spaceHostData) && (props.event_badge.user_id == props.spaceHostData[0].user_id ||
            calledUser.user_id == props.spaceHostData[0].user_id)) {
            return props.event_data?.event_conv_limit + 1; // 1 for space host
        } else {
            const flag = usersArr.filter((val) => {
                if (!_.isEmpty(props.spaceHostData) && props.spaceHostData[0].user_id == val.user_id) {
                    return val;
                }
            });
            const flag2 = props.event_space.current_joined_conversation.conversation_users.filter((val) => {
                if (!_.isEmpty(props.spaceHostData) && props.spaceHostData[0].user_id == val.user_id) {
                    return val;
                }
            });
            return (!_.isEmpty(flag) || !_.isEmpty(flag2))
                ? props.event_data?.event_conv_limit + 1
                : props.event_data?.event_conv_limit;
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if current conversation is private(isolated).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @return {boolean}
     */
    const checkPrivate = () => {
        if (data.is_conversation_private) {
            return true;
        } else if (props.event_space.current_joined_conversation !== null) {
            return props.event_space.current_joined_conversation.is_conversation_private;
        } else {
            return false
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will check if conversation is private or not.
     * CASE 1- PRIVATE:- Show error.
     * CASE 2- User wil be added in the conversation and if its a new conversation then CONVERSATION_CREATE socket event
     * will be emitted else CONVERSATION_JOIN socket event will be emitted.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    const joinConversation = () => {
        if (checkPrivate()) { // checking if current conversation is private
            msg.show(t("notification:check private"), {
                type: "error",
            });
        } else {
            try {
                if (props.event_space.current_joined_conversation !== null) {
                    return askToJoinCall();
                }
                props.setButton(true);
                const user = users[0];
                const userId = users[0].user_id;
                const formData = new FormData();
                formData.append('space_uuid', props.space_uuid);
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
                        props.setButton(false);
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
                    }
                })
                    .catch((err) => {
                        props.setButton(false);
                        props.alert.show(Helper.handleError(err), {type: 'error'})
                    })
            } catch (err) {
                props.setButton(false);
                props.alert.show(Helper.handleError(err), {type: 'error'})
            }
        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check the user role in event and accordingly return the classes.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {UserBadge} item User object.
     * @return {String}
     */
    const userClass = (item) => {
        const {event_role, is_vip} = item;
        switch (event_role) {
            case 0:
                if (is_vip) {
                    return 'simple-vip'
                } else {
                    return ''
                }
            case 1:
                return 'team-user'
            case 2:
                return 'expert-user'

            default:
                return ''
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check the user role in event and accordingly return the tooltip text.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {UserBadge} user User object.
     * @return {String}
     */
    const tip = (user) => {
        let is_vip = user && user.is_vip ? user.is_vip : '';
        const {event_role} = user;
        switch (event_role) {
            case 0:
                if (is_vip === 1) {
                    // return 'Vip'
                    return !_.isEmpty(props.event_labels) ? Helper.getLabel("vip", props.event_labels) : "VIP"
                }
                break;
            case 1:
                if (is_vip === 1) return !_.isEmpty(props.event_labels) ?
                    Helper.getLabel("business_team", props.event_labels) : "Team A" + '-Vip'
                // return 'Team'
                return !_.isEmpty(props.event_labels) ?
                    Helper.getLabel("business_team", props.event_labels) : "Team A"

            case 2:
                if (is_vip === 1) return !_.isEmpty(props.event_labels) ?
                    Helper.getLabel("expert", props.event_labels) : "Team B" + '-Vip'
                // return 'Expert'
                return !_.isEmpty(props.event_labels) ?
                    Helper.getLabel("expert", props.event_labels) : "Team B"
            default:
                return '';
        }
    }
    return (
        <OutsideClickHandler
            onOutsideClick={() => {
                props.clickReset()
            }}
        >
            <ToolbarDiv
                onMouseLeave={(e) => {
                    props.setHover(false)
                }}
                onMouseEnter={() => {
                    props.setHover(true)
                }}
                onClick={() => {
                    props.setHover(true)
                }}
                className="user-toolbar no-texture"
                lastInList={props.lastInList}
                userLength={users.length}
                lastSingle={lastSingle}
                margin={margin}
                clientWidth={clientWidth}
            >
                <div className="row" style={{margin: '0px'}}>
                    {props.lastInList == true && users.length < maxLength &&
                    <button disabled={props.buttonState} className="" onClick={joinConversation}><ReactTooltip
                        type="dark" effect="solid" id={`enter_left`} />
                        <span className="meeting-enter meeting-enter-left no-texture svgicon"
                              dangerouslySetInnerHTML={{__html: Svg.ICON.exit}} data-for='enter_left'
                              data-tip={t("Start a conversation")}></span>

                    </button>
                    }
                    {users.map(((item, key) => {
                        const lastUser = key + 1 == users.length;
                        let limit = 16;
                        limit = (limit + 2 * (users.length - 1));
                        const totalName = `${item.user_fname} ${item.user_lname}`;
                        if (key == currentUser) {
                            return (
                                <div
                                    className={`${lastUser ? 'user-hover-no-photo' : 'user-hover-photo'} 
                                    ${lastSingle ? 'last-user-photo' : 'single-user'}

                                     col-${column} col-sm-${column} col-md-${column} user-${users.length}`}
                                    style={{height: '100%', padding: '0px', width: `${100 / users.length}%`}}>
                                    <ReactTooltip type="dark" effect="solid" id={`enter`} />
                                    <div className="p-relative connect-dot" data-for='enter' data-tip={tip(item)}>
                                        {_.has(item, ['user_avatar']) && item.user_avatar ?

                                            <img className="user-circle no-texture" style={{}} src={item.user_avatar} />

                                            :
                                            <div
                                                className="grid-user-dp-name no-texture">
                                                {Helper.nameProfile(item.user_fname, item.user_lname)}</div>

                                        }
                                        <div
                                            className={`${userClass(item)} ${props.is_dummy == 1 ?
                                                "dummy_user_wrap" : ""}`}>

                                            {_.has(item, ['is_dummy']) && item.is_dummy == 1 &&
                                            process.env.REACT_APP_HE_PROJECT_ENV !== 'production' &&
                                            <div title={t("Dummy user")} className="svgicon dummy-user"
                                                 dangerouslySetInnerHTML={{__html: Svg.ICON.dummy_icon}}></div>}

                                            {_.has(item, ['event_role']) && userClass(item) &&
                                            (userClass(item).includes('team') || userClass(item).includes('vip')) &&
                                            <div className="svgicon"
                                                 dangerouslySetInnerHTML={userClass(item).includes('team') ?
                                                     teamIcon !== null ? {
                                                         __html: ` <img class= "customRoleIcon" 
                                                     src=${teamIcon} />`
                                                     } : {__html: Svg.ICON.team_user} :
                                                     vipIcon != null ? {
                                                         __html: ` <img class= "customRoleIcon" 
                                                     src=${vipIcon} />`
                                                     } : {__html: Svg.ICON.triangle_user}}


                                            ></div>}
                                            {_.has(item, ['event_role']) && userClass(item) &&
                                            userClass(item).includes('expert') &&
                                            <div className="svgicon"
                                                 dangerouslySetInnerHTML={userClass(item).includes('expert') ?
                                                     expertIcon !== null ? {
                                                         __html: ` <img class= "customRoleIcon" 
                                                     src=${expertIcon} />`
                                                     } : {__html: Svg.ICON.triangle_user} : ""}>
                                            </div>}
                                        </div>
                                        <span className="user-hover-arrow" style={{cursor: 'pointer'}}>{'^'}</span>
                                        {users.length < 2 ?
                                            _.has(item, ["event_role"]) && item.event_role == 1 ?
                                                teamAltImage !== null &&
                                                <img className="user-circle no-texture AltImage" style={{}}
                                                     src={teamAltImage} /> :
                                                _.has(item, ["is_vip"]) && item.is_vip == 1 ?
                                                    vipAltImage !== null &&
                                                    <img className="user-circle no-texture AltImage" style={{}}
                                                         src={vipAltImage} /> :
                                                    _.has(item, ["event_role"]) && item.event_role == 2 ?
                                                        expertAltImage !== null &&
                                                        <img className="user-circle no-texture AltImage" style={{}}
                                                             src={expertAltImage} /> : ""
                                            : ""
                                        }

                                    </div>
                                    <div
                                        className={`user-btn w-${users.length * 100} ml-n-${(currentUser) * 100} no-texture`}>
                                        <button style={{width: "100%", height: 'auto'}}
                                                title={totalName.length > limit ? totalName : ''}
                                                data-for={`abc${key}`}>
                                            {Helper.limitText(`${item.user_fname} ${item.user_lname}`, limit)}
                                        </button>
                                    </div>
                                </div>
                            );
                        } else {
                            return (
                                <div data-for='enter' data-tip={tip(item)} onClick={() => {
                                    setUser(key)
                                }} onMouseEnter={() => {
                                    setUser(key)
                                }}
                                     className={`${lastUser ? 'user-hover-no-photo' : 'user-hover-photo'}  
                                        col-${column} col-sm-${column} col-md-${column} user-${users.length}`}
                                     style={{height: '100%', padding: '0px', width: `${100 / users.length}%`}}>
                                    {_.has(item, ['user_avatar']) && item.user_avatar ?
                                        <img className="user-circle no-texture" style={{}} src={item.user_avatar} />
                                        :
                                        <div
                                            className="grid-user-dp-name no-texture">{item.user_fname.charAt('0')}
                                            {item.user_lname ? item.user_lname.charAt('0') : ''}</div>
                                    }
                                    <ReactTooltip type="dark" effect="solid" id={`enter`} />

                                    <div
                                        className={`${userClass(item)}  ${props.is_dummy == 1
                                            ? "dummy_user_wrap"
                                            : ""}`}>
                                        {_.has(item, ['is_dummy']) && item.is_dummy == 1 &&
                                        <div title={t("Dummy user")} className="svgicon dummy-user"
                                             dangerouslySetInnerHTML={{__html: Svg.ICON.dummy_icon}}></div>}

                                        {_.has(item, ['event_role']) && userClass(item) &&
                                        (userClass(item).includes('team') || userClass(item).includes('vip')) &&
                                        <div className="svgicon"
                                             dangerouslySetInnerHTML={userClass(item).includes('team') ?
                                                 teamIcon !== null ? {
                                                     __html: ` <img class= "customRoleIcon" 
                                                 src=${teamIcon} />`
                                                 } : {__html: Svg.ICON.team_user} :
                                                 vipIcon != null ? {
                                                     __html: ` <img class= "customRoleIcon" 
                                                 src=${vipIcon} />`
                                                 } : {__html: Svg.ICON.triangle_user}}

                                        ></div>}
                                        {_.has(item, ['event_role']) && userClass(item) &&
                                        userClass(item).includes('expert') &&
                                        <div className="svgicon"
                                             dangerouslySetInnerHTML={userClass(item).includes('expert') ?
                                                 expertIcon !== null ? {
                                                     __html: ` <img class= "customRoleIcon" 
                                                 src=${expertIcon} />`
                                                 } : {__html: Svg.ICON.triangle_user} : ""}>
                                        </div>}
                                    </div>
                                </div>
                            )
                        }
                    }))}

                    {props.lastInList == false && users.length < maxLength &&
                    <button disabled={props.buttonState} className="" onClick={joinConversation}><ReactTooltip
                        type="dark" effect="solid" id={`enter_right`} />
                        <span className="meeting-enter svgicon no-texture"
                              dangerouslySetInnerHTML={{__html: Svg.ICON.enter_meeting}} data-for='enter_right'
                              data-tip={t("Start a conversation")}></span>
                    </button>
                    }
                </div>
                <AlertContainer ref={msg}{...Helper.alertOptions} />
            </ToolbarDiv>
        </OutsideClickHandler>
    )
}

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
        changeConversationId: (id) => dispatch({type: KCT.NEW_INTERFACE.CHANGE_CONVERSATION_IDS, payload: id})
    }
}

const mapStateToProps = (state) => {
    return {
        event_labels: state.page_Customization.initData.labels,
        event_roles: state.NewInterface.interfaceGraphics,
        event_badge: state.NewInterface.interfaceBadgeData,
        event_data: state.NewInterface.interfaceEventData,
        event_space: state.NewInterface.interfaceSpacesData,
        isPrivate: state.NewInterface.isPrivate,
        spaceHostData: state.NewInterface.interfaceSpaceHostData,
    };
};

ToolBar = connect(mapStateToProps, mapDispatchToProps)(ToolBar);
export default ToolBar;
