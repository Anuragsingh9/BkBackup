
import UserBadge from "./UserBadge";
import SpaceData from "./SpaceData";
import ConversationData from "./ConversationData";

/**
 * @type {Object}
 * @property {UserBadge[]} current_space_host Badge of space host
 * @property {ConversationData} current_joined_conversation Current joined conversation if user is in conversation
 * @property {Object} current_joined_space Current space details
 * @property {String} current_joined_space.space_uuid Space uuid of current joined space
 * @property {ConversationData[]} current_space_conversations All the conversation of space inc. single and dummy users
 * @property {SpaceData[]} spaces All the spaces of current event
 */
const InterfaceSpaceData = {
    current_space_host: [
        UserBadge
    ],
    current_joined_conversation: ConversationData,
    current_joined_space: {
        space_uuid: "6f643422-e660-11ec-963e-0a502021c365"
    },
    current_space_conversations: [
        ConversationData
    ],
    spaces: [
        SpaceData
    ]
};

export default InterfaceSpaceData;
