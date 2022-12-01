import SimpleReactValidator from "simple-react-validator";
import KCTLocales from "../localization/KeepContactlLocales";
import Helper from "../Helper";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a module to manage different kind of custom validations.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @module commons
 */

const lang = Helper.currLang;
const Localization = KCTLocales[lang];
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
            if (/^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\\\/]?){0,})(?:[\-\.\ \\\\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\\\/]?(\d+))?$/.test(val)) {
                let fLetter = val.slice(0, 1);
                if ((fLetter == '+' || fLetter == '-' || fLetter == '#')) {
                    if (val.length != 13) {
                        return false
                    }
                    return true
                } else {
                    if (val.length != 10) {
                        return false
                    }
                    return true
                }
            } else {
                return false
            }
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
            if (/^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\\\/]?){0,})(?:[\-\.\ \\\\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\\\/]?(\d+))?$/.test(val)) {
                let fLetter = val.slice(0, 1);
                if ((fLetter == '+' || fLetter == '-' || fLetter == '#')) {
                    if (val.length != 13) {
                        return false
                    }
                } else {
                    if (val.length != 10) {
                        return false
                    }
                }
                return true
            } else {
                return false
            }
        }
    },
    phoneValidation: {
        message: "The :attribute must be valid Number",
        rule: function (val, options) {
            return /^[0-9 +()-]*$/i.test(val);
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
        }
    },
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This Function inherits 'SimpleReactValidator' to expand its Functionality
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
                return /^[A-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ`'’,-]*$/i.test(val);
            }
        },
        only_alpha_names_space: {
            message: "The :attribute may only contain letters, numbers.",
            rule: function (val, options) {
                return /^[a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ_'’\- ]*$/i.test(val);
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
                return /^[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ `’']*$/i.test(val);
            }
        },
        alpha_names_underscore_hypn: {
            message: "The :attribute may only contain letters, numbers, _ ,' and -.",
            rule: function (val, options) {
                return /^[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ _'’\-]*$/i.test(val);
            }
        },
        alpha_names_underscore_hypn_space: {
            message: "The :attribute may only contain letters, numbers, _ ,' and -.",
            rule: function (val, options) {
                return /^[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ _'’\-]*$/i.test(val);
            }
        },
        mobile_validation: {
            message: "The :attribute must be valid Mobile Number",
            rule: function (val, options) {
                if (/^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\\\/]?){0,})(?:[\-\.\ \\\\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\\\/]?(\d+))?$/.test(val)) {
                    let fLetter = val.slice(0, 1);
                    if ((fLetter == '+' || fLetter == '-' || fLetter == '#')) {
                        if (val.length != 13) {
                            return false
                        }
                    } else {
                        if (val.length != 10) {
                            return false
                        }
                    }
                    return true
                } else {
                    return false
                }
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
                return /^[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ _,’'\-.;]*$/i.test(val);
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
                    var ext = val.name.split('.').pop();
                    let extCheck = ['jpg', 'jpeg', 'gif', 'png', 'pdf']
                    return extCheck.includes(_.toLower(ext))
                } else {
                    return false
                }
            }
        },
        supportFileValidation: {
            message: "The :attribute is Invalid, it must be a Valid type",
            rule: function (val, options) {
                if (val.name != undefined) {
                    var ext = val.name.split('.').pop();
                    let extCheck = ['jpg', 'jpeg', 'gif', 'png', 'pdf']
                    return extCheck.includes(_.toLower(ext))
                } else {
                    return false
                }
            }
        },
        excelValidation: {
            message: "The :attribute is Invalid, it must be a Valid type",
            rule: function (val, options) {
                if (val.name != undefined) {
                    var ext = val.name.split('.').pop();
                    let extCheck = ['xls', 'csv', 'xlsx']
                    return extCheck.includes(_.toLower(ext))
                } else {
                    return false
                }
            }
        },
        siretValidation: {
            message: "The :attribute is Invalid, it must be 14 character",
            rule: function (val, options) {
                return (val.length == 14) ? true : false
            }
        },
        templateValidation: {
            message: "The :attribute is Invalid, it must be a Valid type",
            rule: function (val, options) {
                if (val.name != undefined) {
                    var ext = val.name.split('.').pop();
                    let extCheck = ['xls', 'csv', 'xlsx', 'doc', 'docx', 'pdf']
                    return extCheck.includes(_.toLower(ext))
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
        alpha_space: Validations.alpha_names,
        url: Validations.url,
        opt_email: Validations.opt_email,
        mobile: Validations.mobile,
        phoneValidation: Validations.phoneValidation,
        name: Validations.name,

    });
    return validator;
}

export {
    getAlphaValidator,
    Localization,
}








