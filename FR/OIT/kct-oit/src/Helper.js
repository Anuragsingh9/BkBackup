import i18n from "i18next";
import {reactLocalStorage} from "reactjs-localstorage";
import moment from "moment-timezone";
import Constants from "./Constants";
import _ from 'lodash';

/**
 * @module Helper
 */

// Default alert options
const alertOptions = {
    offset: 14,
    position: "top center",
    theme: "dark",
    time: 5000,
    transition: "scale",
};


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function takes a string and returns first letter capital.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} string Any text
 * @method
 */
function jsUcfirst(string) {
    if (typeof string === "string") {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    return "";
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function takes current language from local storage and convert it into lower case.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @return {String|Null}
 * @method
 */
const currLang = () => {
    if (reactLocalStorage.get("current_lang")) {
        return reactLocalStorage.get("current_lang").toLowerCase();
    }
};


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function takes error object and returns correct label for it.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ErrorObj} error Error object of API call
 * @method
 */
function handleError(error) {
    if (error.message != undefined && error.message == "Network error") {
        return "Network Error";
    } else {
        if (error.response) {
            // The request was made and the server responded with a status code
            // that falls out of the range of 2xx

            if (error.response.status == 500) {
                // return alertMsg.SOMETHING_WORNG;
                return i18n.t("notification:something wrong");
            }

            if (error.response.data.message != undefined) {
                return (
                    <div
                        dangerouslySetInnerHTML={{
                            __html: error.response.data.message.split(".,").join(". <br/>"),
                        }}
                    />
                );
            } else {
                // return alertMsg.SOMETHING_WORNG;
                return i18n.t("notification:something wrong");
            }
        } else {
            // Something happened in setting up the request that triggered an error
            // return alertMsg.SOMETHING_WORNG
            return i18n.t("notification:something wrong");
        }
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function is used to format date into specific format(ex - 01-01-2000).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} date Date in a specific format
 * @returns {String}
 */
function formatDate(date) {
    var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = "0" + month;
    if (day.length < 2) day = "0" + day;

    return [year, month, day].join("-");
}

const formatDateTime = (target, format) => {
    let date = new moment(target);
    return date.format(format);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will take first name and last name of the user and return their first letter.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} fname  User's first name
 * @param {String} lname User's last name
 * @returns {String|Null}
 */
const nameProfile = (fname, lname) => {
    if (fname != undefined && lname != undefined) {
        if (fname && lname) {
            return fname.charAt(0) + lname.charAt(0);
        } else if (fname != "" || lname == "") {
            return fname.charAt(0) + fname.charAt(1);
        } else {
            return;
        }
    }
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function takes two calculates time in users time zone for a date and time of specific timezone.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} timezone Event time zone
 * @param {String} date Event date
 * @method
 * @returns {String}
 */
function getTimeUserTimeZone(timezone, date, format) {
    const myTimeZone = moment.tz.guess();
    const eventTimezone = moment()
        .toDate()
        .toLocaleString("en-US", {timeZone: timezone});
    const userTimeZone = moment()
        .toDate()
        .toLocaleString("en-US", {timeZone: myTimeZone});
    const userTime = moment(userTimeZone).toDate().getTime();
    const eventTime = moment(eventTimezone).toDate().getTime();
    const difference = userTime - eventTime;

    const time = moment(date).toDate().getTime() + difference;
    return time - userTime >= 0;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function crops the string/text till given limit
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} text User first name
 * @param {Number} limit Number
 * @returns {String}
 */
const limitText = (text, limit) => {
    if (text && text != "" && text.length > limit) {
        const textLimit = limit - 3;
        const clippedText = text.substring(0, textLimit);

        return <span title={text}>{`${clippedText}...`}</span>;
    } else {
        return <span>{text}</span>
    }
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To set by input ("key", labels array) for event roles labels
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} labelName Label name
 * @param {Array} labels Array of labels
 * @returns {string|*}
 */
const getLabel = (labelName, labels) => {
    let currentLang;
    if (localStorage.getItem("current_lang")) {
        currentLang = localStorage.getItem("current_lang").toLowerCase();
    } else {
        localStorage.setItem("current_lang", "fr");
        currentLang = "fr";
    }
    // const currentLang = localStorage.getItem("current_lang").toLowerCase();
    let val = "";
    if (labelName && labels) {
        labels.map((v) => {
            if (labelName == v.name) {
                v.locales.map((l) => {
                    if (currentLang == l.locale) {
                        val = l.value
                    }
                });
            }
            return val;
        });
    }
    return val ? val : labelName;
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description is set password is not done , url will be set- password page
 * <br>
 * pass there res.data.data params
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} data Information of user password
 */
const replaceSetPassword = (data) => {
    // if (res && res.data.status && res.data.status == 403) {
    if (data.code && data.code == 1001) {
        window.location.replace && window.location.replace(`/oit/set-passwordd`);
    }
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To split string with ("_") and joint the first latter and make capital for group types
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} text Group type
 * @returns {string}
 */
const groupTypeCapital = (text) => {
    if (text) {
        const myArray = text.split("_");
        let add = "";
        for (let k = 0; k < myArray.length; k++) {
            add += myArray[k].charAt(0).toUpperCase();
        }
        return add;
    }
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method convert week days in number
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {{mon: Boolean, tue: Boolean, wed: Boolean, thu: Boolean, fri: Boolean, sat: Boolean, sun: Boolean}} selectedDays Week days
 * @returns {number}
 */
const convertWeekDayToNumber = (selectedDays) => {
    let binary = '';
    Object.keys(selectedDays).forEach(day => {
        binary += selectedDays[day] ? '1' : '0';
    })
    return parseInt(binary, 2);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method convert number to week days
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} number Week days
 * @returns {{thu: boolean, tue: boolean, wed: boolean, sat: boolean, fri: boolean, mon: boolean, sun: boolean}}
 */
const convertNumberToWeekDay = (number) => {
    let numberBase = 2; // binary base
    let binaryNumber = (number >>> 0).toString(numberBase);
    // making the digit 7 letter to ensure all days covered
    binaryNumber = ('0000000' + binaryNumber).slice(-7);
    let selectedDays = {
        mon: false, tue: false, wed: false, thu: false, fri: false, sat: false, sun: false,
    };
    let daysObjectName = Object.keys(selectedDays);
    for (let i = 0; i < 7; i++) { // 7 represents days as string length will be converted to 7
        // as i will contain the index and string is form in same order as keys defined above
        // so accessing the key by index
        selectedDays[daysObjectName[i]] = binaryNumber[i] === '1' || binaryNumber === 1
    }
    return selectedDays;
}
/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will take date as string and convert month number into month name.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} date Date format in string eg- 2022-08-31
 * @returns {String}
 * @method
 */
const toMonthName = (dateString) => {
    // Note : Received date format should be in 2022-08-31
    if (!dateString && typeof dateString !== "string") {
        return
    }
    const dateArray = dateString.split("-");
    const monthNumber = dateArray[1]
    const date = new Date();
    date.setMonth(monthNumber - 1);
    const result = `${dateArray[0]}-${date.toLocaleString('en-US', {
        month: 'short',
    })}-${dateArray[2]}`
    return result;
}
/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To prepare the line for the recurrence to show the basic details of current selection data of recurrence
 * for the event so user will see what type of recurrence will be there with date, type and respective selection
 * Here the type of recurrence will be detected so in week if mon-fri is selected it will be counted as weekday
 * if mon-sun is selected with 1 interval it will be counted as everyday
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} recInterval Interval of recurring with respect to type
 * @param {Number} recType Type of the recurring
 * @param {Object} selectedDays Selected weekdays if recurring is weekly or weekdays type object of each day with value
 * @param {Boolean} selectedDays.mon To indicate if Monday is selected day or not for recurring the event
 * @param {Boolean} selectedDays.tue To indicate if Tuesday is selected day or not for recurring the event
 * @param {Boolean} selectedDays.wed To indicate if Wednesday is selected day or not for recurring the event
 * @param {Boolean} selectedDays.thu To indicate if Thursday is selected day or not for recurring the event
 * @param {Boolean} selectedDays.fri To indicate if Friday is selected day or not for recurring the event
 * @param {Boolean} selectedDays.sat To indicate if Saturday is selected day or not for recurring the event
 * @param {Boolean} selectedDays.sun To indicate if Sunday is selected day or not for recurring the event
 * @param {Number} monthDay The value for the month data on which the event will recur
 * @param {String} endDate Recurrence end date
 * @param {Number} monthType Type of the week to indicate its on day or on specific week number and day
 * @param {Number} onTheMonthWeek Value of the week selected for month to recur
 * @param {String} onTheMonthWeekDay Name of the week day to occur the event
 * @returns {String}
 */
const prepareRecurrenceLine = (
    recInterval,
    recType,
    endDate,
    monthDay,
    selectedDays,
    monthType,
    onTheMonthWeek,
    onTheMonthWeekDay
) => {

    let s = recInterval > 1 ? 's' : '';
    let recurrenceTypeName = {};

    const weekDayLabel = {
        mon: 'Monday',
        tue: 'Tuesday',
        wed: 'Wednesday',
        thu: 'Thursday',
        fri: 'Friday',
        sat: 'Saturday',
        sun: 'Sunday',
    };

    recurrenceTypeName[Constants.recurrenceType.DAILY] = 'day';
    recurrenceTypeName[Constants.recurrenceType.MONTHLY] = 'month';
    recurrenceTypeName[Constants.recurrenceType.WEEKLY] = 'week';
    recurrenceTypeName[Constants.recurrenceType.WEEKDAY] = 'week';

    let typeName = s;

    if (_.has(recurrenceTypeName, [recType])) {
        // name of the type,
        // if full week is selected with 1 interval it will be day by next check
        typeName = `${recurrenceTypeName[recType]}${typeName}`;
    }

    let days = recInterval > 1 ? recInterval : '';
    let typeDetails = '';

    if (recType === Constants.recurrenceType.MONTHLY) {
        // handling line for monthly type
        let number = {
            1: 'First', 2: 'Second', 3: 'Third', 4: 'Fourth', '-1': 'Last'
        };
        if (monthType === Constants.recurrenceMonthType.ON_THE) {
            typeDetails = `on ${number[onTheMonthWeek]} ${onTheMonthWeekDay}`
        } else {
            typeDetails = `on day ${monthDay}`;
        }
    } else if (recType === Constants.recurrenceType.WEEKLY || recType === Constants.recurrenceType.WEEKDAY) {
        let selectedDaysDecimal = convertWeekDayToNumber(selectedDays);
        if (selectedDaysDecimal === Constants.recWeekDayBinary) {
            // weekdays are selected
            typeDetails = `on Weekdays`;
        } else if (selectedDaysDecimal === 127 && recInterval === 1) {
            // Full week is selected and interval is 1, that is indirectly everyday
            typeName = recurrenceTypeName[Constants.recurrenceType.DAILY];
        } else {
            // unknown combination of days selected so just showing the each selected day with name of day
            let value = [];
            Object.keys(selectedDays).forEach(weekDay => {
                if (selectedDays[weekDay]) {
                    value.push(weekDayLabel[weekDay]);
                }
            });
            if (value.length) {
                typeDetails = `on ${value.join(', ')}`;
            }
        }
    }

    return `Occur every ${days} ${typeName} ${typeDetails} until ${endDate}`;
}
// TODO GOURAV DOCUMENTATION

let findBaseRoot = (version = 'v1') => {
    if (process.env.NODE_ENV === 'development' || process.env.NODE_ENV === 'test') {
        return `${process.env.REACT_APP_HO_TESTHOST}/api/${version}/admin/`;
    } else {
        // here production environment will handle to auto fetch the sub domain from url
        let SUB_DOMAIN = window.location.host.split('.')[1] ? window.location.host.split('.')[0] : false;
        let BASE_DOMAIN = window.globalBaseDomain ? window.globalBaseDomain : process.env.REACT_APP_HO_HOSTNAME;
        return window.location.protocol + '//' + SUB_DOMAIN + '.' + BASE_DOMAIN + `/api/${version}/admin/`;
    }
}

const handleApiError = (err, alert) => {
    if (err && _.has(err.response.data, ["errors"])) {
        let errors = err.response.data.errors;
        for (let key in errors) {
            alert.show(errors[key], {type: "error"});
        }
    } else if (err && _.has(err.response.data, ["msg"])) {
        let er = err.response.data.errors;
        for (let key in er) {
            alert.show(er[key], {type: "error"});
        }
    } else {
        alert.show(handleError(err), {type: "error"});
    }
}

const leadNChar = (target, length, leadingChar) => {
    for (let i = 0; i < length; i++) {
        target = leadingChar + target;
    }
    return target.substring(target.length - length, target.length);
}

const isToday = (m) => {
    return m && m instanceof moment && m.format('YYYY-MM-DD') === moment().format("YYYY-MM-DD");
}
const strToNum = (str) => {
    return typeof (Number(str)) === "number" ? Number(str) : 0
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description to convert the object into query param
 * ---------------------------------------------------------------------------------------------------------------------
 * @param data
 */
const toQueryParam = (data) => {
    return new URLSearchParams(data).toString();
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To check if the provided string is valid with moment time format or not ,
 * if time is not valid format it will return undefined else it will return the object of moment with timezone
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} time Time to check with provided format
 * @param {String[]} formats Format to apply on the string
 */
const checkAndGetMoment = (time, formats) => {
    let result;
    formats.forEach(format => {
        let target = moment(time, format, true);
        if (target.isValid()) {
            result = target;
        }
    })
    return result;
}

/**
 *
 * @param interval
 * @param {Object<moment>} startTime
 * @param {Object<moment>} endTime
 */
const prepareTimeIntervalsForChart = (interval, startTime, endTime) => {
    startTime = startTime.clone();
    let result = [startTime.clone()];
    startTime = startTime.add(
        'm',
        startTime.minutes() % interval !== 0 ? interval - startTime.minutes() % interval : 0
    ).clone();
    startTime = startTime.add('m', interval);
    while (startTime.diff(endTime) < 0) {
        result.push(startTime.clone());
        startTime = startTime.add('m', interval);
    }
    return result;
}

const prepareEventStartEndTime = (event) => {
    let startTime = moment(`${event?.event_start_date} ${event?.event_start_time}`, Constants.DATE_TIME_FORMAT, true);
    let endTime = moment(`${event.event_start_date} ${event.event_end_time}`, Constants.DATE_TIME_FORMAT, true);
    return {
        startTime: startTime.isValid() ? startTime : null,
        endTime: endTime.isValid() ? endTime : null,
    }
}

const prepareRoutePrefix = (groupKey,version) => {
    return `/${groupKey}/${version}`
}

const prepareChartIntervalGap = (startTime, endTime) => { //Name
    let gap = endTime.diff(startTime)/(60000*15) ;
    let interval;
    if(gap < 10 || (gap >= 10 && gap < 15)){
        interval = 10;
    }
    else if(gap >= 15 && gap < 30){
        interval = 15;
    }
    else if(gap >= 30 && gap < 45){
        interval = 30;
    }
    else if(gap >= 45 && gap < 60){
        interval = 45;
    }
    else {
        interval = 60;
    }
    console.log("sssssssssss time gap", gap, interval)
    return interval;
}


export default {
    alertOptions,
    jsUcfirst,
    handleError,
    currLang,
    formatDate,
    formatDateTime,
    nameProfile,
    limitText,
    getTimeUserTimeZone,
    getLabel,
    replaceSetPassword,
    groupTypeCapital,
    convertWeekDayToNumber,
    convertNumberToWeekDay,
    prepareRecurrenceLine,
    toMonthName,
    findBaseRoot,
    handleApiError,
    strToNum,
    leadNChar,
    timeHelper: {
        isToday,
        checkAndGetMoment,
        prepareTimeIntervalsForChart,
        prepareEventStartEndTime,
        prepareChartIntervalGap,
    },
    toQueryParam,
    prepareRoutePrefix
};
