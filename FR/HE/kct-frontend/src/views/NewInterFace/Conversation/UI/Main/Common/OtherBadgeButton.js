import React, {useEffect, useState} from "react";
import Slider from '../../../../MyBadge/BadgeSideComponent/NewSlider.js';
import {useTranslation} from 'react-i18next';
import {getUserData, removeUser} from '../../../Utils/Conversation.js';
import Svg from '../../../../../../Svg.js';
import ReactTooltip from 'react-tooltip';
import ConverSationButton from "./Button";
import {useDispatch} from "react-redux";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render conversation buttons for other user which are :
 * 1.self badge editor - to update user's detail/profile
 * 2.mute/unmute - mute or unmute current conversation
 * 3. leave conversation - to leave current ongoing conversation
 * 4. setting - to open a popup in which user can manage device(mic/camera/speaker) settings
 * 5. Isolation button - To make conversation isolate(user can't break conversation from his side or no other user can
 * join conversation)
 * 6. Call space host - button to call space host(if available to join)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Function} setHoverSlider To update the user id for which badge is viewing
 * @param {String} userId Id of user selected
 * @param {Number} sliderOpen User id on which current hovered to view user data
 * @property {UserBadge[]} conversation_users Users of conversations are in
 * @param {Boolean} isDummy To indicate if the user is dummy or not
 * @returns {JSX.Element}
 * @constructor
 */
const OtherBadgeButton = ({setHoverSlider, userId, sliderOpen, conversation_users, isDummy}) => {
    const {t} = useTranslation('myBadgeBlock')
    const dispacth = useDispatch();
    const [badgeClose, setbadgeClose] = useState("close")
    const [showKickButton, setShowKickButton] = useState(true);

    const [showKickButtonLoader, setShowKickButtonLoader] = useState(false);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle(open/close) state of space host's badge editor component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const badgeVisible = () => {
        if (badgeClose == "close") {
            setbadgeClose("open");
            setHoverSlider(userId);
        } else {
            setbadgeClose("close");
            setHoverSlider(false);
        }
    }

    useEffect(() => {
        conversation_users.forEach((user) => {
            if (userId === user.user_id && user.is_dummy === 1) {
                setShowKickButton(true);
            } else {
                setShowKickButton(false);
            }
        })
    }, [conversation_users])

    return (
        <div className="video-control d-inline w-100 pt-10 text-left">

            <button
                type="button"
                data-for={`user-${userId}`}
                onClick={badgeVisible}
                data-tip={t("See User Badge")}
                className="control-button  no-texture video-buttons"
                dangerouslySetInnerHTML={{__html: Svg.ICON.contact_popup}}
            >
            </button>

            {isDummy == 1 &&
            (
                showKickButtonLoader ?
                    <div className='control-button no-texture video-buttons'
                         style={{display: 'inline-flex', flexDirection: 'column', justifyContent: 'center'}}>
                        <div className="loader_custom"></div>
                    </div>
                    :
                    <ConverSationButton
                        onClick={() => {
                            let cb = (val) => {
                                setShowKickButtonLoader(val);
                            }
                            setShowKickButtonLoader(true);
                            dispacth(removeUser(userId, cb, isDummy))
                        }}
                        dataTip={t("Remove user from conversation")}
                        dataFor={'RemoveUser_tooltip'}
                        icon={'exit'}
                    />
            )}

            {(sliderOpen === userId) &&
            <div className="other-user-badge-popup">
                <Slider onBlur={() => {
                    setHoverSlider(false)
                }} item={getUserData(conversation_users, userId)} />
            </div>
            }
            <ReactTooltip type="dark" effect="solid" id={`user-${userId}`} />
        </div>
    )
}

export default OtherBadgeButton;