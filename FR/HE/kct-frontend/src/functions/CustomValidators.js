import React from 'react';
import moment from 'moment/moment'
import SimpleReactValidator from "simple-react-validator";
import Helper from '../Helper';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a module which is used to manage different kind of custom validation to use in our application.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @module CustomValidator
 */
const _ = require('lodash')
var emailRegex = /^$|^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

const Validations = {
    alpha_names: {
        message: "The :attribute may only contain letters, numbers.",
        rule: function (val, options) {
            return /^[0-9a-zA-ZÀ-ÿ]*$/i.test(val);
        }
    },

    mobileValidation: {
        message: "The :attribute must be a valid mobile number",
        rule: function (val, options) {
            return /^[0-9a-zA-ZÀ-ÿ _]*$/i.test(val);
        }
    },
    alpha_space: {
        message: "The :attribute may only contain letters, numbers, and space.",
        rule: function (val, options) {
            return /^[a-zA-ZÀ-ÿ-.'`_" ]+$/.test(val);
        }
    },
    opt_email: {
        message: "The :attribute must be valid email address",
        rule: function (val, options) {
            return emailRegex.test(val);
        }
    },
    url: {
        message: "The :attribute must be valid URL(http://www.example.com).",
        rule: function (val, options) {
            return /^$|^((https?|ftp|smtp):\/\/)?(www.)?[a-z0-9]+\.[a-z]+(\/[a-zA-Z0-9#]+\/?)*$/.test(val);
        }
    },
    mobile: {
        message: "The :attribute must be valid Mobile Number",
        rule: function (val, options) {
            return /^(?=(?:\D*\d){5,18}\D*$)\+?[0-9]{1,3}[\s-]?(?:\(0?[0-9]{1,5}\)|[0-9]{1,5})[-\s]?[0-9][\d\s-]{3,10}\s?(?:x[\d-]{0,4})?$/.test(val);
        }
    },
    phoneValidation: {
        message: "The :attribute must be valid Number",
        rule: function (val, options) {
            return /^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$/i.test(val);
        }
    },

    destination: {
        message: "The :attribute is invalid",
        rule: function (val, options) {
            return /^$|^[a-zA-Z]+(?:[\s-][a-zA-Z]+)*$/.test(val);
        }
    },
    name: {

        message: "The :attribute is Invalid, it must be a Valid Name",
        rule: function (val, options) {
            return /^(\w+[ ]*\w+)$/.test(val);
        },
    },
    numerical: {
        message: 'The :attribute is Invalid, it must be a Valid Number',
        rule: function (val, options) {
            return /^[0-9]*$/i.test(val);
        }
    },


}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This Function In-Herits SimpleReactValidator to expand its Functionality
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @method
 */

function getAlphaValidator() {
    var validator = new SimpleReactValidator({
        alpha_names: {
            message: "The :attribute may only contain letters, numbers.",
            rule: function (val, options) {
                return /^[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]*$/i.test(val);
            }
        },
        only_alpha: {
            message: "The :attribute may only contain letters.",
            rule: function (val, options) {
                return /^[A-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ`',-]*$/i.test(val);
            }
        },
        only_alpha_names_space: {
            message: "The :attribute may only contain letters, numbers.",
            rule: function (val, options) {
                return /^[a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ_'\- ]*$/i.test(val);
            }
        },
        alpha_names_space: {
            message: "The :attribute may only contain letters, numbers.",
            rule: function (val, options) {
                return /^[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ ]*$/i.test(val);
            }
        },
        alpha_names_space_quotes: {
            message: "The :attribute may only contain letters, numbers,'.",
            rule: function (val, options) {
                return /^[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ `']*$/i.test(val);
            }
        },
        alpha_names_underscore_hypn: {
            message: "The :attribute may only contain letters, numbers, _ ,' and -.",
            rule: function (val, options) {
                return /^[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ _'\ ."-]*$/i.test(val);
            }
        },
        alpha_names_underscore_hypn_space: {
            message: "The :attribute may only contain letters, numbers, _ ,' and -.",
            rule: function (val, options) {
                // return /^[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ _'\-.]*$/i.test(val);
                return /^[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ _'"\-]*$/i.test(val);
            }
        },
        mobile_validation: {
            message: "The :attribute must be valid Mobile Number",
            rule: function (val, options) {
                return /^(?=(?:\D*\d){5,18}\D*$)\+?[0-9]{1,3}[\s-]?(?:\(0?[0-9]{1,5}\)|[0-9]{1,5})[-\s]?[0-9][\d\s-]{3,10}\s?(?:x[\d-]{0,4})?$/.test(val);
            }
        },
        sub_domain_url_validation: {
            message: "The :attribute must be valid Url",
            rule: function (val, options) {
                return /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/.test(val);
            }
        },
        alpha_names_underscore_hypn_space_samiclon: {
            message: "The :attribute may only contain letters, numbers, _ ,' and -.",
            rule: function (val, options) {
                return /^[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ _,'\-.;]*$/i.test(val);
            }
        },
        email_french: {
            message: "The :attribute must be a valid email.",
            rule: function (val, options) {
                return /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i.test(val);
            }
        },

        empty_search_object: {
            message: "The :attribute may not be empty.",
            rule: function (val, options) {
                if (val != undefined)
                    return Object.keys(val).length > 0;
            }
        },
        imageValidation: {
            message: "The :attribute is Invalid, it must be a Valid image type",
            rule: function (val, options) {
                if (val.name != undefined) {
                    var ext = val.name.split('.').pop();
                    let extCheck = ['jpg', 'jpeg', 'gif', 'png']
                    return extCheck.includes(_.toLower(ext))
                } else {
                    return false
                }
            }
        },
        qualificationValidation: {
            message: "The :attribute is Invalid, it must be a Valid type",
            rule: function (val, options) {
                if (val.name != undefined) {
                    return val.name.match(/\.(xls|xlsx|jpg|jpeg|png|pdf)$/) ? true : false
                } else {
                    return false
                }
            }
        },
        excelValidation: {
            message: "The :attribute is Invalid, it must be a Valid type",
            rule: function (val, options) {
                if (val.name != undefined) {
                    return val.name.match(/\.(xls|csv|xlsx)$/) ? true : false
                } else {
                    return false
                }
            }
        },
        templateValidation: {
            message: "The :attribute is Invalid, it must be a Valid type",
            rule: function (val, options) {
                if (val.name != undefined) {
                    return val.name.match(/\.(xls|csv|xlsx|doc|docx|pdf)$/) ? true : false
                } else {
                    return false
                }
            }
        },
        required_array: {
            message: "The :attribute is not allowed to be empty",
            rule: function (val, options) {
                if (val.length) {
                    return true
                } else {
                    return false
                }
            }
        },
        maxSize: {
            message: " greater than 100 characters.",
            rule: function (val, options) {
                if (val.length <= 100) {
                    return true
                } else {
                    return false
                }
            }
        },
        alpha_space: Validations.alpha_names,
        url: Validations.url,
        opt_email: Validations.opt_email,
        mobile: Validations.mobile,
        phoneValidation: Validations.phoneValidation,
        name: Validations.name,
        numerical: Validations.numerical
    });
    return validator;
}


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To make the field required if value is empty
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {Any} value Value of field
 * @param {String} field Id of the field
 * @returns {String}
 * @method
 */
const required = (value, field) => value ? undefined : 'Required';

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
const dateAfterEndDate = (value, field) => moment(value, 'DD/MM/YYYY').isAfter(moment(field.end_date, 'DD/MM/YYYY'), 'day') ?
    undefined : "Date must be after end date"

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
const dateAfterStartDate = (value, field) => moment(value, 'DD/MM/YYYY').isAfter(moment(field.start_date, 'DD/MM/YYYY'), 'day') ?
    undefined : "Date must be after start date"

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
const dateAfterToday = (value, field) =>
    moment(value, 'DD/MM/YYYY').isAfter(new Date(), 'day')
        ? undefined
        : "Date must be after today date"


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
    Helper.tagStrip(value) && Helper.tagStrip(value).length > max ? `Must be ${max} characters or less` : undefined

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To validate the maximum length of string must be lesser than 50
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @type {Function}
 */
const maxLength50 = maxLength(50)

const Validation = {
    required,
    maxLength50,
    dateAfterEndDate,
    dateAfterStartDate,
    dateAfterToday,
}


export {
    getAlphaValidator,
    Validation
}