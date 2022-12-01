import React, {useState} from "react";
import ReactTooltip from 'react-tooltip';
import ConverSationButton from '../Common/Button.js'

import {useTranslation} from 'react-i18next';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component will render conversation buttons for space host conversation component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Function} setHoverSlider
 * @param {Function} removeUser
 * @param {Boolean} buttonState
 * @param {String} userId
 * @param {Function} toggleBan
 * @returns {JSX.Element}
 * @constructor
 */
const ConvSHButtons = ({setHoverSlider, removeUser, buttonState, userId, toggleBan}) => {
    const {t} = useTranslation('myBadgeBlock')
    const [badgeClose, setbadgeClose] = useState("close");
    // const [showLeaveLoader, setShowLeaveLoader] = useState(false);
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

    return (
        <div className="video-control d-inline w-100 pt-10 text-center">
            <ReactTooltip type="dark" effect="solid" id={`user-${userId}`} />
            <ConverSationButton
                dataTip={t("See User Badge")}
                dataFor={'See_UserBadge_tooltip'}
                icon={'contact_popup'}
                onClick={badgeVisible}
            />
            {!showKickButtonLoader
                ? <ConverSationButton
                    onClick={() => {
                        let cb = (val) => {
                            setShowKickButtonLoader(val);
                        }
                        setShowKickButtonLoader(true);
                        return removeUser(userId, cb)
                    }}
                    dataTip={t("Remove user from conversation")} dataFor={"RemoveUser_tooltip"}
                    dataFor={"RemoveUser_tooltip"}
                    disabled={buttonState}
                    icon={'exit'}
                />
                :
                <div
                    className='control-button no-texture video-buttons'
                    style={{
                        display: 'inline-flex',
                        flexDirection: 'column',
                        justifyContent: 'center'
                    }}
                >
                    <div class="loader_custom"></div>
                </div>
            }
            <ConverSationButton
                dataTip={t("Ban user from conversation")}
                dataFor={'Ban_User_tooltip'}
                onClick={() => {
                    toggleBan(userId)
                }}
                disabled={buttonState}
                icon={'member_ban_btn'}
            />

        </div>
    )
}

export default ConvSHButtons;