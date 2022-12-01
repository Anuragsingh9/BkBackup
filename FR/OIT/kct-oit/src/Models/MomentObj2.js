import User from '../Models/User'
import DraftEvent from '../Models/DraftEvent'

/**
 @type {Object}
 @property {String} date Date for the moment
 @property {String} start_time Start time of the moment
 @property {String} end_time End time of the moment
 @property {String} name Moment name
 @property {Number} moment_type Moment type(networking , networking+content)
 @property {Number} contentType Content type(zoom webinar, youtube, vimeo)
 @property {Number} broadcastType Broadcast type(Webinar , Meeting)
 @property {String} localKey Moment unique KEY
 */
const MomentObj2 = {
    date: "22-02-2022",
    start_time: "12:50:00",
    end_time: "12:50:00",
    name: "name",
    moment_type: 4,
    contentType: 1,
    broadcastType: 2,
    localKey: "KEY"
}

export default MomentObj2;