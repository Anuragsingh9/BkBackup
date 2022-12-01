import moment from "moment-timezone";
import i18n from "i18next";
import Constants from "../Constants";
/**
 * @module
 */

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the value is less then max number.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} max Maximum value
 * @return {String|undefined}
 */
const minLength = max => value => {
    return value && value.length < max
        ? `${i18n.t("validation:Must be")} ${max} ${i18n.t("validation:characters or greater")}`
        : undefined
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the entered value is in correct form of email or not.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} value Email value
 * @return {String|undefined}
 */
const email = value =>
    value && !/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(value) ?
        i18n.t("validation:Invalid Email Address") : undefined

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the entered value is a correct form of youtube url or not.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} link Entered URL for youtube video
 * @return {String}
 */
const matchYoutubeUrl = (link) => {
    if (!link) {
        return "Required";
    }
    var p =
        /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
    if (link && link.match(p)) {
        return link.match(p)[1] ? undefined : "Invalid Url";
    }
    return "Invalid Url";
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the entered value is a correct form of vimeo url or not.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} link Entered URL for vimeo video
 * @return {String}
 */
const matchVimeoUrl = (link) => {
    if (!link) {
        return "Required";
    }
    var p = /^(http\:\/\/|https\:\/\/)?(www\.)?(vimeo\.com\/)([0-9]+)$/;
    if (link && link.match(p)) {
        return link.match(p)[1] ? undefined : "Invalid Url";
    }
    return "Invalid Url";
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the value is not empty.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @return {String|undefined}
 */
const required = (value) => (value ? undefined : i18n.t("validation:Required"));

const validDate = (value) => {
    return moment(value, "DD/MM/YYYY", true).isValid() ? undefined : "Date is invalid";
}


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the entered date value must be before end date value.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} value Date value
 * @param {Object} field Event's end date object
 * @param {String} field.end_date Event's end date
 * @return {String|undefined}
 */const dateAfterEndDate = (value, field) =>
    moment(value, "DD/MM/YYYY").isAfter(
        moment(field.end_date, "DD/MM/YYYY"),
        "day"
    )
        ? undefined
        : "Date must be after end date";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the entered date value must be after start date value.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} value Date value
 * @param {Object} field Event's start date object
 * @param {String} field.start_date Event's start date
 * @return {String|undefined}
 */
const dateAfterStartDate = (value, field) => {
    if(field.event_rec_start_date instanceof moment && value instanceof moment) {
        return field.event_rec_start_date.format('YYYY-MM-DD') >= value.format('YYYY-MM-DD')
            ? 'Date must be after start date'
            : undefined;
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To validate the recurrence end time with respect to event start time
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param value
 * @param field
 * @returns {string|undefined}
 */
const recDateAfterStartDate = (value, field) => {
    if(field.event_start_date instanceof moment && field.event_rec_end_date instanceof moment) {
        return field.event_rec_type !== Constants.recurrenceType.NONE
        && field.event_start_date.format('YYYY-MM-DD') >= field.event_rec_end_date.format('YYYY-MM-DD')
            ? 'End Date must be after start date'
            : undefined;
    }
}



/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To validate if event end time is after event start time or not
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param value
 * @param fields
 * @returns {string|undefined}
 */
const endTimeAfterStartTime = (value, fields) => {
    if (value && value instanceof moment && fields && fields.event_start_time && fields.event_start_time instanceof moment) {
        let start = fields.event_start_time
        let end = value;
        if (start.format('HH:mm') > end.format("HH:mm")) {
            return "End time must after start time";
        }
    }
    return undefined;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the entered date value must be after today's date value.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} value Date value
 * @return {String|undefined}
 */const dateAfterToday = (value, field) =>
    moment(value, "DD/MM/YYYY").isAfter(new Date(), "day")
        ? undefined
        : "Date must be after today date";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the value is greater then max number.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} max Maximum value
 */
const maxLength = (max) => (value) => value && value.length > max
    ? `Must be ${max} characters or less`
    : undefined;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the value is greater then max number.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} max Maximum value
 */
const maxNumber = (max) => (value) => value && value > max
    ? `Must be ${max} number or less`
    : undefined;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the value is less then max number.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} min Minimum value
 * @return {String|undefined}
 */
const minNumber = min => value => value && value < min
        ? `${i18n.t("validation:Must be")} ${min} number or greater`
        : undefined;

const maxLength50 = maxLength(50);
const max30 = maxLength(50);
const max14 = maxLength(14);
const max100 = maxLength(100);
const min2 = minLength(2);
const min6 = minLength(6);

const maxNumber1000 = maxNumber(1000);
const minNumber12 = minNumber(12);

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will  validate the min character of an email must be 3.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} max Maximum value
 */
const max20 = maxLength(20);
const minimum3 = (max) => (value) =>
    value && value.trim().length < max
        ? `${i18n.t("validation:Must be")} ${max} Characters minimum`
        : undefined;

const min3 = minimum3(3);

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the entered value should not be contained any number and special characters.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} value Value to validate
 */const alpha = (value) =>
    value && /^[A-Z&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ`',-]*$/i.test(value)
        ? undefined
        : "This field should not contain numbers and special characters";

const alpha_names_hypn_space = (value) =>

    value && /^[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ,'\-]*$/i.test(value) ?
        undefined
        : "The attribute may only contain letters, numbers and '-'";


// for special char.  -> \u00E0-\u00FC
const max70 = maxLength(70);

const Validation = {
    required,
    maxLength50,
    validDate,
    dateAfterEndDate,
    dateAfterStartDate,
    recDateAfterStartDate,
    endTimeAfterStartTime,
    dateAfterToday,
    minLength,
    email,
    max14,
    max20,
    max30,
    max70,
    max100,
    min2,
    min3,
    min6,
    maxNumber1000,
    minNumber12,
    alpha,
    matchYoutubeUrl,
    matchVimeoUrl,
    alpha_names_hypn_space,

};
export default Validation;