import User from '../Models/User'
import DraftEvent from '../Models/DraftEvent'

/**
 * @type {Object}
 * @property {Number} id Unique uuid of for the moment
 * @property {String} moment_name Moment name
 * @property {String} moment_description Moment description
 * @property {String} start_time Start time of the moment
 * @property {String} end_time End time of the moment
 * @property {Number} moment_type Moment type(networking , networking+content)
 * @property {Object} moderator Moderator details
 * @property {User} moderator Moderator details
 * @property {User} speakers Moderator details
 */
const MomentObj = {
    id: 702,
    moment_name: "content-1",
    moment_description: null,
    start_time: "12:50:00",
    end_time: "23:10:00",
    moment_type: 4,
    moderator: {
        id: 12,
        fname: "Om",
        lname: "Bissa",
        email: "om.bissa@kct-technologies.com",
        avatar: null,
        company: null,
        union: []
    },
    speakers: []
}

export default MomentObj;