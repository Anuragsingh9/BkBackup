/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description In this file event model related repository are added
 * Here all the event model data set related manipulation are stored, if there is any reusable method needs to be
 * defined for event data set then it can be found here which can be reused anywhere in the application
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @module EventMomentRepository
 */

import _ from "lodash";
import Constants from "../Constants";
import moment from "moment-timezone";
import {v4 as uuidv4} from 'uuid';
import MomentObj from "../Models/MomentObj";
import MomentObj2 from "../Models/MomentObj2";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will convert the moment response from api to suitable format for further proceeding
 * This will add the data and moment name into each moment to show them in front so the keys match with the redux form
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Array} res.data.data  Received from parent component where it is called.This is an array of objects where
 * objects are holding each moment data.
 * @param {String} date Event date
 * @returns {Array}
 */
const mapResponseToMoments = (res, date) => {
    const moments = res.data.data;
    let mappedMoments = [];
    if (!_.isEmpty(moments)) {
        moments.forEach((item) => {
            mappedMoments.push({
                ...item,
                date: date,
                name: item.moment_name, // aliasing the key name to make it easy for redux form
                contentType: _.has(Constants.momentToContentAlias, item.moment_type)
                    ? Constants.momentToContentAlias[item.moment_type]
                    : null,
                broadcastType: _.has(Constants.momentToBroadcastAlias, item.moment_type)
                    ? Constants.momentToBroadcastAlias[item.moment_type]
                    : null,
            });
        });
    }
    return mappedMoments
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To Filter the moments on the basis of the type
 * as there can be more than one type of moments and from backend all the moments are coming single array
 * but in front side moments needs to be separated on the basis of networking type so this method will separate the
 * moments by type
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Array} momentsData This is an array of objects where objects are holding each moment data.
 * @param {Number} type Type of moment
 * @returns {Array}
 */
const filterMomentsByType = (momentsData, type = null) => {
    if (type === Constants.momentType_networking) {
        return momentsData.filter(moment => {
            return moment !== undefined && moment.moment_type === Constants.momentType_networking;
        });
    } else {
        return momentsData.filter(moment => {
            return moment !== undefined && moment.moment_type !== Constants.momentType_networking;
        });
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To get the moment which have the maximum time of ending with respect to event and current time
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Array} moments This is an array of objects where objects are holding each moment data.
 * @param {String} eventDate Event Date
 * @returns {Null}
 */
const getLastMomentByTime = (moments, eventDate) => {
    let momentMax = null;
    let currentMax = null;
    moments.forEach(m => {
        let time = moment(`${eventDate} ${m.start_time}`)._d;
        if (!currentMax || time.getTime() > currentMax) {
            currentMax = time.getTime();
            momentMax = m;
        }
    });
    return momentMax;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To prepare the moments data for sending to api
 * as the data on front has already mapped and api response have different type of moment data set required so mapping
 * the data to manipulate as required from backend side
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Array} moments This is an array of objects where objects are holding each moment data.
 * @returns {MomentObj}
 */
const prepareMomentsForApi = (moments) => {
    return moments.map((item) => {
        let data = {
            "moment_start": item.start_time,
            "moment_end": item.end_time,
            "moment_type": item.moment_type,
            "name": item.name,
            "description": item.moment_description ? item.moment_description : '',
        };
        if (_.has(item, ['video_url'])) {
            data.video_url = item.video_url;
        }
        if (_.has(item, ['id'])) {
            data.id = item.id;
        }
        const type = item.moment_type
        if (_.has(item, ['speakers']) && (type === Constants.momentType_meeting || type === Constants.momentType_webinar)) {
            let speakers = [];
            item.speakers.forEach(speaker => speakers.push(speaker.id));
            data.speakers = speakers;
        }
        if (_.has(item, ['moderator', 'id']) && (type === Constants.momentType_meeting || type === Constants.momentType_webinar)) {
            data.moderator = item.moderator.id;
        }
        return data;
    });
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will get the data of new moment and it will append that moment in all existing moments
 * the moment will be added to respective type of moment and will validate the moment time with event time
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} momentType Moment type
 * @param {Array} allMoments All available moments
 * @param {String} eventTime Event time
 * @returns {MomentObj2|Null} eventMoment
 */
const createMoment = (momentType, allMoments, eventTime) => {
    let momentStartTime = null;
    eventTime = moment(eventTime);
    if (_.isEmpty(allMoments)) {
        // there is no moment so take the first moment start time as event start time
        momentStartTime = eventTime.format('HH:mm:ss');
    } else {
        let lastMoment = getLastMomentByTime(allMoments, eventTime.date());
        if (_.has(lastMoment, ['end_time'])) {
            momentStartTime = lastMoment.end_time;
        }
    }
    let momentNumberByType = filterMomentsByType(allMoments, momentType).length + 1;
    momentStartTime = moment(`${eventTime.format(Constants.commonDateFormat)} ${momentStartTime}`);
    let eventMoment = {
        localKey: uuidv4(),
        moment_type: momentType,
        start_time: momentStartTime.format('HH:mm:ss'),
        end_time: momentStartTime.add(10, 'minutes').format("HH:mm:ss"),
        date: eventTime.format(Constants.commonDateFormat),
        name: `${momentType === Constants.momentType_networking ? 'networking' : 'content'}-${momentNumberByType}`,
        contentType: _.has(Constants.momentToContentAlias, momentType)
            ? Constants.momentToContentAlias[momentType]
            : null,
        broadcastType: _.has(Constants.momentToBroadcastAlias, momentType)
            ? Constants.momentToBroadcastAlias[momentType]
            : null,
    };
    if (momentType === Constants.momentType_networking) {
        return eventMoment;
    }
    return eventMoment;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will add the two moments to the all moments section as when auto create is turned on
 * there are two moments to be added only first with networking and second with zoom , covering all the event time
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} momentType Moment type
 * @param {Array} allMoments All available moments
 * @param {String} eventTime Event time
 * @param {String} eventEndTime Event end time
 * @returns {MomentObj2}
 */
const createAutoMoment = (momentType, allMoments, eventTime, eventEndTime) => {

    let momentStartTime = null;
    let momentEndTime = null;
    eventTime = moment(eventTime);
    eventEndTime = moment(eventEndTime)

    if (_.isEmpty(allMoments)) {
        // there is no moment so take the first moment start time as event start time
        momentStartTime = eventTime.format('HH:mm:ss');
        momentEndTime = eventEndTime.format('HH:mm:ss');
    } else {
        let lastMoment = getLastMomentByTime(allMoments, eventTime.date());
        if (_.has(lastMoment, ['end_time'])) {
            momentStartTime = lastMoment.end_time;
        }
    }
    let momentNumberByType = filterMomentsByType(allMoments, momentType).length + 1;
    momentStartTime = moment(`${eventTime.format(Constants.commonDateFormat)} ${momentStartTime}`);
    momentEndTime = moment(`${eventEndTime.format(Constants.commonDateFormat)} ${momentEndTime}`);

    let eventMoment = {
        localKey: uuidv4(),
        moment_type: momentType,
        start_time: momentStartTime.format('HH:mm:ss'),
        end_time: momentEndTime.format("HH:mm:ss"),
        date: eventTime.format(Constants.commonDateFormat),
        name: `${momentType === Constants.momentType_networking ? 'networking' : 'content'}-${momentNumberByType}`,
        contentType: _.has(Constants.momentToContentAlias, momentType)
            ? Constants.momentToContentAlias[momentType]
            : null,
        broadcastType: _.has(Constants.momentToBroadcastAlias, momentType)
            ? Constants.momentToBroadcastAlias[momentType]
            : null,
    };

    if (momentType === Constants.momentType_networking) {
        return eventMoment;
    }
    return eventMoment;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description to check if the moment is content type by moment data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {MomentObj} moment object
 * @returns {Boolean}
 */
const isMomentContent = (moment) => {
    return [
        Constants.momentType_webinar,
        Constants.momentType_meeting,
        Constants.momentType_youtube,
        Constants.momentType_vimeo,
    ].includes(moment.moment_type);
}

const momentRepo = {
    mapResponseToMoments: mapResponseToMoments,
    filterMomentsByType: filterMomentsByType,
    getLastMomentByTime: getLastMomentByTime,
    prepareMomentsForApi: prepareMomentsForApi,
    createMoment: createMoment,
    isMomentContent: isMomentContent,
    createAutoMoment: createAutoMoment
}

export default momentRepo;