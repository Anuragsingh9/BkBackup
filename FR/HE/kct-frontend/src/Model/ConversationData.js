import UserBadge from "./UserBadge";

/**
 * @type {Object}
 *
 * @property {String} conversation_uuid Uuid of conversation
 * @property {String} conversation_type Type of conversation
 * @property {Number} is_conversation_private To indicate if conversation is in private mode
 * @property {UserBadge[]} conversation_users Users of conversations are in
 *
 */
const ConversationData = {
    conversation_uuid: null,
    conversation_type: "single_user",
    is_conversation_private: 0,
    conversation_users: [
        UserBadge
    ]
}
export default ConversationData;
