import moment from "moment-timezone";

moment.tz.setDefault("Europe/Paris");
let currentTime = moment();

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is used to prepare start and end time as per requirment by performing some manipulations. 
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @method
 * @param {Number} number Number that is used to subtract from current date & time.
 * @param {String} type Type is used to subtract from current date & time (type can be week,day,month,year)
 * @returns {Array} Array of start and end time(in moement object format)
 */
const prepareStartEndTime = (number, type) => {
    let timeArray = []
    let startTime;
    let endTime;

    //IF we need time interval from a week/month/year to last day
    // if (number <= 1) {
    //     startTime = currentTime.clone().subtract(number, type).startOf(type).format()
    //     endTime = currentTime.clone().subtract(number, type).endOf(type).format()
    // } else {
    //     startTime = currentTime.clone().subtract(number, type).startOf(type).format()
    //     endTime = currentTime.clone().subtract(1, type).endOf(type).format()
    // }
    startTime = currentTime.clone().subtract(number, type).startOf(type)
    endTime = currentTime.clone().subtract(number <= 1 ? number : 1, type).endOf(type)


    timeArray.push(startTime, endTime)

    return timeArray
}

const CustomDateRangePickerModal = {
    today: prepareStartEndTime(0, "day"),
    yesterday: prepareStartEndTime(1, "day"),
    lastWeek: prepareStartEndTime(1, "week"),
    lastMonth: prepareStartEndTime(1, "month"),
    last7Days: prepareStartEndTime(7, "day"),
    last30Days: prepareStartEndTime(30, "day")
}
export default CustomDateRangePickerModal;