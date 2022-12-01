/**
 * @type {Object}
 *
 * @property {String} space_uuid Space uuid
 * @property {String} space_name Name of the space
 * @property {Object} space_short_name Short name of space
 * @property {String} space_mood Mood of space
 * @property {Number} max_capacity Maximum number of users can register in space
 * @property {Number} is_vip_space To indicate if the space is vip or not
 * @property {Number} space_type The type of space
 * @property {String} event_uuid Event uuid of space to which space belongs
 * @property {Number} users_count Current registered users count in space
 * @property {UserBadge[]} space_hosts Space hosts data
 * @property {Number} is_mono_space To indicate if the space is mono type space or not
 */
const SpaceData = {
    space_uuid: "6f643422-e660-11ec-963e-0a502021c365",
    space_name: "Welcome Space",
    space_short_name: null,
    space_mood: "Welcome Space",
    max_capacity: 144,
    is_vip_space: 0,
    space_type: 0,
    event_uuid: "6f6257ec-e660-11ec-9e29-0a502021c365",
    users_count: 0,
    space_hosts: [
        {
            user_id: 1,
            fname: "Abhishek",
            lname: "Vyas",
            email: "abhishek.vyas@kct-technologies.com",
            avatar: null
        }
    ],
    is_mono_space: 0
};

export default SpaceData;
