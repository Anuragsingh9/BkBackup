/**
 * @type {Object}
 * @property {Number} user_id User id of user
 * @property {String} user_fname User's first name
 * @property {String} user_lname User's Last name
 * @property {String} email User's email
 * @property {String} user_avatar Profile Picture Image URL
 * @property {Entity[]} unions Collection of all unions
 * @property {Entity} company User company details
 * @property {Object} visibility Visibilities of fields
 * @property {Number} visibility.user_lname To indicate if others can see current user last name or not
 * @property {Undefined} visibility.company To indicate if others can see current user company or not
 * @property {Undefined} visibility.unions To indicate if others can see current user union or not
 * @property {Object} tags_data User organiser tags data
 * @property {OrgTag[]} tags_data.used_tag Used organiser tags for user
 * @property {OrgTag[]} tags_data.unused_tag Unused Organiser tags for user
 * @property {Number} is_dummy To indicate if user is dummy or real
 * @property {String} dummy_video_url If user is dummy it will be dummy user video url for converstaion
 * @property {Number} active_state To indicate if user is online or offline
 * @property {Number} event_role The role of user in current event
 * @property {Number} is_self To indicate if the badge is self user badge or other's badge
 * @property {Number} is_space_host To indicate if user is space host or not
 */
const UserBadge =  {
    user_id: 12,
    user_fname: "Test",
    user_lname: "Test",
    email: "test@test.com",
    user_avatar: "https://[bucket_name].s3.amazonaws.com/a/b.png",
    unions: [
        {
            entity_id: 1,
            long_name: "hello",
            short_name: "hello",
            position: "hello"
        }
    ],
    company: {
        entity_id: 1,
        long_name: "hello",
        short_name: "hello",
        position: "hello"
    },
    visibility: {
        user_lname: "1",
        company: "1",
        unions: "1"
    },
    tags_data: {
        used_tag: [],
        unused_tag: [
            {
                id: 1,
                name: "tag"
            }
        ]
    },
    is_dummy: 1,
    dummy_video_url: "https://www.youtube.com/watch?v=abcd",
    active_state: 1,
    event_role: 1,
    is_self: 1,
    is_space_host: 1
};
export default UserBadge;