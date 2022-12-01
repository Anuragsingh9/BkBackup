import React from 'react';
import DummyVideo from '../../../../VideoPlayer/DummyVideo';
import Helper from '../../../../../../Helper';
import {checkDummyUser} from '../../../Utils/Conversation.js';
import _ from 'lodash';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render videos of other user who is in conversation with them.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} userId Id of user selected
 * @param {Object} tileState Current user tile state
 * @param {Boolean} tileState.active To indicate if user video is loaded or not
 * @param {Number} i Index of user in grid
 * @property {UserBadge[]} conversation_users Users of conversations are in
 * @returns {JSX.Element}
 * @constructor
 */
const OtherUserVideo = ({userId, tileState, i, conversation_users}) => {

    return (
        <div className={`member-user-thumbs no-texture h-100 w-100 bg-cover`}>

            {!_.isEmpty(checkDummyUser(conversation_users, userId)) ?
                <DummyVideo user={checkDummyUser(conversation_users, userId)} />
                :
                <React.Fragment>
                    <video id={`other-video${i}`} className="h-100 w-100 no-texture membersVideos"></video>
                    {
                        userId
                        && (tileState === null || tileState.active === false)
                        && <Helper.pageLoading />
                    }
                </React.Fragment>
            }
            <div className="online-circle"></div>
        </div>
    )
}

export default OtherUserVideo;