import React from 'react'
import moment from 'moment/moment'
import i18n from "i18next";
/**
 * @module
 */


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the value is less then max number.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Function} max Function that will validate the string for passed value
 * @returns {Function}
 * @method
 */
const minLength = max => value =>
    value && value.length < max ? `${i18n.t("qss:Must be")} ${max} ${i18n.t("qss:characters or greater")}` : undefined

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the entered value is in correct form of email or not.
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {String} value Value to check
 * @returns {String}
 * @method
 */
const email = value =>
    value && !/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(value) ?
        i18n.t("qss:Invalid Email Address") : undefined

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will validate the value is not empty.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} value Value to check for empty
 * @returns {String}
 * @method
 */
const required = value => value ? undefined : i18n.t("qss:Required")

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To check if date is after end date of event
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} value Date Value
 * @param {EventData} field Event data to check
 * @returns {String}
 * @method
 */
const dateAfterEndDate = (value, field) =>
    moment(value, 'DD/MM/YYYY').isAfter(moment(field.end_date, 'DD/MM/YYYY'), 'day')
        ? undefined
        : "Date must be after end date"

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To check if date is after start date of event
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} value Date Value
 * @param {EventData} field Event data to check
 * @returns {String}
 * @method
 */
const dateAfterStartDate = (value, field) =>
    moment(value, 'DD/MM/YYYY').isAfter(moment(field.start_date, 'DD/MM/YYYY'), 'day')
        ? undefined
        : "Date must be after start date"

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To check if date is after today
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} value Date Value
 * @param {String} field Id of the field
 * @returns {undefined|string}
 * @method
 */
const dateAfterToday = (value, field) => moment(value, 'DD/MM/YYYY').isAfter(new Date(), 'day') ?
    undefined : "Date must be after today date"

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To validate the character length for provided string
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Number} max Maximum length of string
 * @returns {Function}
 * @method
 */
const maxLength = max => value =>
    value && value.length > max ? `Must be ${max} characters or less` : undefined

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To validate the maximum length of string must be lesser than 50
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @type {Function}
 */
const maxLength50 = maxLength(50)

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To validate the minimum length of string must be 6
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @type {Function}
 */
const min6 = minLength(6)

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To validate the character length for provided string that must be minimum 3
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Number} max Minimum length of string
 * @returns {Function}
 * @method
 */
const minimum3 = max => value =>
    value && value.trim().length < max
        ? `${i18n.t("qss:Must be")} ${max} ${i18n.t("qss:Invalid Email Address")}`
        : undefined

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To validate the character length for provided string that must be minimum 3
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @type {Function}
 */
const min3 = minimum3(3)

const Validation = {
    required,
    maxLength50,
    dateAfterEndDate,
    dateAfterStartDate,
    dateAfterToday,
    min6,
    minLength,
    email, min3
}
export default Validation