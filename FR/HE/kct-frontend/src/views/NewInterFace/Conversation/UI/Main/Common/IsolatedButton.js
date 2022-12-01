import React from 'react';
import Svg from '../../../../../../Svg.js';
import {useDispatch} from 'react-redux';
import ReactTooltip from 'react-tooltip';
import {useTranslation} from 'react-i18next';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render a button to make conversation isolate(when user can't break
 * current conversation and other user can not join current conversation if its a isolated conversation)
 * Component for displaying conversation Isolation button
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Function} askToPrivateConversation To Ask other user for making the conversation private
 * @param {Boolean} is_conversation_private To indicate if conversation is private or open for all event space user
 * @param {String} className Name of the class
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const IsolatedButton = ({askToPrivateConversation, is_conversation_private, className}) => {
    const {t} = useTranslation('spaceHost');
    const dispacth = useDispatch();
    return (
        <>
            <ReactTooltip type="dark" effect="solid" id='isolation_btn' />
            <button
                onClick={() => {
                    dispacth(askToPrivateConversation())
                }}
                className={`${className}  no-texture`}
                type="button"
            >
                <span className="grey-private-btn" data-for='isolation_btn' data-tip={
                    is_conversation_private ? t("Conversation Not Private") : t("Conversation Private")
                }>
                    <span
                        className="svgicon"
                        dangerouslySetInnerHTML={
                            is_conversation_private
                                ? {__html: Svg.ICON.new_lock_icon}
                                : {__html: Svg.ICON.new_unlock_icon}
                        }
                    >
                    </span>
                </span>
            </button>
        </>
    )
}

export default IsolatedButton;