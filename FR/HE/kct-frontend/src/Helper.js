import React from "react";
import {reactLocalStorage} from "reactjs-localstorage";
import Moment from "moment";
import moment from "moment";
import FrLocale from "moment/locale/fr";
import moments from "moment-timezone";
import ReactLoading from "react-loading";
import SimpleReactValidator from "simple-react-validator";
import i18n from "i18next";
import CSSGenerator from "./views/DynamicCss";
import Constants from "./Constants";
import ZoomMtgEmbedded from "@zoomus/websdk/embedded";

const _ = require("lodash");

// host name
var str = window.location.hostname;

var hostName = str.split(".");

var realHost = "";

if (hostName[1] != undefined && hostName[1] != "ooionline") {
    realHost = `${hostName[1]}.${hostName[2]}/`;
} else {
    realHost = "ooionline.com/";
}
// current language of interface
let currLang = "EN";

if (localStorage.getItem("current_lang")) {
    currLang = localStorage.getItem("current_lang");
}
window["OPS"] = {lang: currLang};

// amazon s3 path for the account.
const amznS3Patth = "https://s3-eu-west-2.amazonaws.com/" + realHost;

// Default alert options
const alertOptions = {
    offset: 14,
    position: "top center",
    theme: "dark",
    time: 5000,
    transition: "scale",
};

// Global ref for alert
let globalAlertRef = null;

/**
 * @module Helper
 */

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function is used to set the global ref on mounting of main route component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} ref Reference object for AlertContainer
 * @method
 **/
const setGlobalRef = (ref) => {
    globalAlertRef = ref;
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function returns the name space for socket trigger.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 **/
const getNameSpace = () => {
    return window.location.host.split(".")[1]
        ? window.location.host.split(".")[0]
        : process.env.REACT_APP_HE_DEV_SUB_DOMAIN || "humann";
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function handles the global alerts out side the components without callbacks.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} msg Reference object for displaying notification popup
 * @param {String} type Type of the message
 * @method
 */
const globalAlert = (msg, type) => {
    if (type !== "error") {
        return (
            globalAlertRef &&
            globalAlertRef.show(msg, {
                type: "success",
            })
        );
    } else {
        return (
            globalAlertRef &&
            globalAlertRef.show(handleError(msg), {
                type: "error",
            })
        );
    }
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To compare two objects by each key
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {Object} mainObj First Object
 * @param {Object} valObj Second Object
 * @returns {Boolean}
 * @method
 */
const compareObjects = (mainObj, valObj) => {
    let flag = true;

    Object.keys(valObj).map((keys) => {
        if (!mainObj.hasOwnProperty(keys)) {
            flag = false;
        }
    });

    return flag;
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description pageLoading Function is used to render loader component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 **/
const pageLoading = () => (
    <div className="site-loader">
        <ReactLoading
            type="bars"
            color="#006ab0"
            delay={1}
            className="center-block pg-content-loader"
            height="100px"
        />
    </div>
);

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function is used to calculate difference between two dates and render it as a span.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} date1 First date,
 * @param {String} date2 Second date,
 * @param {String} sentDate Data on which message was sent,
 * @method
 **/
const diffYMDHMS = (date1, date2, sentDate) => {
    let years = date1.diff(date2, "year");
    if (years > 0) {
        date2.add(years, "years");
    }

    let months = date1.diff(date2, "months");
    if (months > 0) {
        date2.add(months, "months");
    }

    let days = date1.diff(date2, "days");
    if (days > 0) {
        date2.add(days, "days");
    }

    let hours = date1.diff(date2, "hours");

    if (hours > 0) {
        date2.add(hours, "hours");
    }

    let minutes = date1.diff(date2, "minutes");

    if (minutes > 0) {
        date2.add(minutes, "minutes");
    }

    let seconds = date1.diff(date2, "seconds");
    return (
        <span title={sentDate}>
      {years > 0 ? years + "y" : ""}{" "}
            {months > 0 && years == 0 ? months + "m" : ""}{" "}
            {days > 0 && months == 0 && years == 0 ? days + "d" : ""}{" "}
            {hours > 0 && days == 0 && months == 0 && years == 0 ? hours + "h" : ""}{" "}
            {minutes > 0 && hours == 0 && days == 0 && months == 0 && years == 0
                ? minutes + "min"
                : ""}{" "}
            {seconds > 0 &&
            minutes == 0 &&
            hours == 0 &&
            days == 0 &&
            months == 0 &&
            years == 0
                ? seconds + "sec"
                : ""}
    </span>
    );
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function provides alert message translations
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} currLang Current language of application.
 * @method
 **/
const alertMsg =
    currLang == "EN"
        ? {
            FLASH_MSG_REC_FAILED: "Failed Process!",
            FLASH_MSG_LOGIN_1: "Successfully loggedin !",
            FLASH_MSG_LOGIN_0: "Invalid credentials",
            FLASH_MSG_REC_ADD_1: "Record added successfully!",
            FLASH_MSG_FILE_ADD_1: "Process done",
            FLASH_MSG_FILE_ADD_0: "Process not done",
            FLASH_MSG_REC_ADD_0: "Record not added!",
            FLASH_MSG_REC_UPDATE_1: "Record updated successfully tt!",
            FLASH_MSG_REC_UPDATE_LEAVE_CONVERSATION: "Conversation has Ended",
            FLASH_MSG_REC_UPDATE_0: "Record not updated!",
            FLASH_MSG_REC_DELETE_1: "Record deleted successfully!",
            FLASH_MSG_REC_DELETE_0: "Record not deleted!",
            FLASH_MSG_CHILD_EXIST_1:
                "Record cannot be deleted! Please delete child records first?",
            FLASH_MSG_RESET_PASS_0: "Password not changed! Try again.",
            FLASH_MSG_RESET_PASS_1: "Password update successfully!",
            FLASH_MSG_RESET_PASS_2: "Old password invalid!",
            FLASH_MSG_REC_SEND_INITAITION_1: "Invitation send successfully !",
            FLASH_MSG_EXIST_COMMISION_IN_CODE1:
                "commission already exist in code 1 :",
            FLASH_MSG_EXIST_COMMISION_IN_CODE2:
                "commission already exist in code 2 :",
            OTP_FAILED: "Invalid !",
            USER_EXIST: "User exist.",
            SOMETHING_WORNG: "Something Wrong Happened.",
            DOMAIN_EXIST: "Domain Already Registered.",
            FLASH_MEMBER_EXIST: "is already a member of this workshop",
            SUCCESS_MESSAGE: "Successfully Done",
            MEMBERS_FULL: "Members are full",
            LEAVE_CONVERSATION: "Leave Conversation to join",
            CHECK_VIP: "You are currently not a member of VIP Space",
            CHECK_PRIVATE:
                "This Conversation is currently in isolated mode. Please try again later",
            BANNING: "The user has been banned from the event",
            BAN: "You have been banned from the event",
            ISOLATION_NOTIFICATION_NORMAL: "has just ended isolated mode",
            CHECK_USED_TAG: "Tag already created",
            SPACE_HOST_CHANGE: "Space host cannot switch space",
        }
        : {
            FLASH_MSG_REC_FAILED: "Échec du processus!",
            FLASH_MSG_LOGIN_1: "Connecté avec succès!",
            FLASH_MSG_LOGIN_0: "les informations d'identification invalides",
            FLASH_MSG_REC_ADD_1: "Enregistrement ajouté avec succès!",
            FLASH_MSG_FILE_ADD_1: "Opération effectuée",
            FLASH_MSG_FILE_ADD_0: "Opération non effectuée",
            FLASH_MSG_REC_ADD_0: "Enregistrement non ajouté!",
            FLASH_MSG_REC_UPDATE_1: "Enregistrement mis à jour avec succès!",
            FLASH_MSG_REC_UPDATE_LEAVE_CONVERSATION: "Conversation has Ended",
            FLASH_MSG_REC_UPDATE_0: "Enregistrement non mis à jour!",
            FLASH_MSG_REC_DELETE_1: "Enregistrement supprimé avec succès!",
            FLASH_MSG_REC_DELETE_0: "Enregistrement non supprimé!",
            FLASH_MSG_CHILD_EXIST_1:
                "L'enregistrement ne peut pas être supprimé! Veuillez supprimer les enregistrements enfants en premier?",
            FLASH_MSG_RESET_PASS_0: "Mot de passe non changé! Réessayer.",
            FLASH_MSG_RESET_PASS_1: "Mot de passe mis à jour avec succès",
            FLASH_MSG_RESET_PASS_2: "Ancien mot de passe invalide!",
            FLASH_MSG_REC_SEND_INITAITION_1: "Invitation envoyée avec succès!",
            FLASH_MSG_EXIST_COMMISION_IN_CODE1:
                "commission existe déjà dans le code 1:",
            FLASH_MSG_EXIST_COMMISION_IN_CODE2:
                "commission existe déjà dans le code 2:",
            OTP_FAILED: "OTP invalide!",
            USER_EXIST: "Utilisateur existe.",
            SOMETHING_WORNG: "Quelque chose s'est mal passé.",
            DOMAIN_EXIST: "Domaine déjà enregistré.",
            FLASH_MEMBER_EXIST: "est déjà membre de cet atelier",
            SUCCESS_MESSAGE: "Terminé avec succès",
            MEMBERS_FULL: "Les membres sont pleins",
            LEAVE_CONVERSATION: "Quitter la conversation pour rejoindre",
            CHECK_VIP: "You are currently not a member of VIP Space",
            CHECK_PRIVATE:
                "Cette conversation est mode isolation actuellement. Veuillez réessayer plus tard",
            BANNING: "Le participant a été retiré de l’évènement",
            BAN: " Vous avez été retiré de l’évènement",
            PRIVATE_NORMAL: "This Conversation is currently in normal mode",
            ISOLATION_NOTIFICATION_NORMAL: "vient de terminer le mode isolé",
            SPACE_HOST_CHANGE: "L'hôte de l'espace ne peut pas changer d'espace",
            CHECK_USED_TAG: "Balise déjà créée",
        };

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function checks and returns the length of an object
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} obj Target object for which the length needs to be calculated
 * @method
 **/
function objLength(obj) {
    if (obj != undefined) return Object.keys(obj).length;

    return 0;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function returns localstorage value of a key.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} key Item's key
 * @method
 **/
function getLocalStorage(key) {
    return reactLocalStorage.getObject(key);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function changes date format as per the parameter value.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} dateFormat Date new format
 * @param {String} dateTime Date to format with provided format
 * @method
 **/
function dateTimeFormat(dateTime, dateFormat = null) {
    // if (localStorage.getItem('current_lang') == 'EN') {
    if (localStorage.getItem("i18nextLng").toUpperCase() == "EN") {
        Moment.locale("en");
    } else {
        Moment.locale("fr", FrLocale);
    }

    if (dateFormat == null) return Moment(dateTime).format("YYYY/MM/DD");
    else return Moment(dateTime).format(dateFormat);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function takes label and creates renders a span with a title.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} label Label of span
 * @param {String} toolTip Title of span
 * @returns {JSX.Element}
 * @method
 */
function showToolTip(label, toolTip) {
    return (
        <span>
      {label}
            <span
                data-toggle="tooltip"
                title={toolTip}
                className="fa fa-question-circle "
            ></span>
    </span>
    );
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function takes two calculates time in users time zone for a date and time of specific timezone.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} timezone Event time zone
 * @param {String} date Event date
 * @method
 **/
function getTimeUserTimeZone(timezone, date, format) {
    const myTimeZone = moments.tz.guess();
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
    return moment(time).toDate();
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function takes a string and returns first letter capital.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} string Any string to capitalize first letter
 * @method
 **/
function jsUcfirst(string) {
    if (typeof string === "string") {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    return "";
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function takes a string and returns first letter capital.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} string Any string
 * @method
 **/
function jsUcfirst2(string) {
    return string.replace(/ /g, "").charAt(0).toUpperCase() + string.slice(1);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function takes a string removes white spaces and returns.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} string Target string to strip
 * @method
 **/
function tagStrip(string) {
    if (string != undefined && string != null) {
        return string.replace(/(<([^>]+)>)/gi, "");
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function returns a alpha validator with few rules defined by default.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 **/
function getAlphaValidator() {
    var validator = new SimpleReactValidator({
        alpha_names: {
            message: "The :attribute may only contain letters, numbers.",
            rule: function (val, options) {
                return /^[0-9a-zA-ZÀ-ÿ]*$/i.test(val);
            },
        },
        alpha_names_space: {
            message: "The :attribute may only contain letters, numbers.",
            rule: function (val, options) {
                return /^[0-9a-zA-ZÀ-ÿ _]*$/i.test(val);
            },
        },
    });
    return validator;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function takes error object and returns correct label for it.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Error} error Error object to handle and display in popup
 * @method
 **/
function handleError(error) {
    if (error.message != undefined && error.message == "Network error") {
        return "Network Error";
    } else {
        if (error.response) {
            // The request was made and the server responded with a status code
            // that falls out of the range of 2xx

            if (error.response.status == 500) {
                // return alertMsg.SOMETHING_WORNG;
                return i18n.t("notification:something worng");
            }
            if (error.response.data.msg != undefined) {
                return (
                    <div
                        dangerouslySetInnerHTML={{
                            __html: error.response.data.msg.split(".,").join(". <br/>"),
                        }}
                    />
                );
            } else {
                // return alertMsg.SOMETHING_WORNG;
                return i18n.t("notification:something worng");
            }
        } else {
            // Something happened in setting up the request that triggered an error
            // return alertMsg.SOMETHING_WORNG
            return i18n.t("notification:something worng");
        }
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function color object and converts it to a rgba string which can be used directly in css.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ColorRGBA} colorObj Color object
 * @method
 **/
const rgbaObjectToStr = (colorObj) => {
    return colorObj
        ? "rgba(" +
        colorObj.r +
        ", " +
        colorObj.g +
        ", " +
        colorObj.b +
        ", " +
        colorObj.a +
        ")"
        : "";
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function takes two calculates time in users time zone for a date and time of specific timezone.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} timezone Event time zone
 * @param {String} date Event date
 * @method
 **/
const getTimeDifference = (timezone, date) => {
    const myTimeZone = moments.tz.guess();
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
    const differences = time - moment().toDate().getTime();
    return differences;
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function takes two string first name and last name.
 * And return First letter of first name + first letter of last name.
 * If last name is not present it returns first and second letter of first name.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} fname User first name
 * @param {String} lname User last name
 * @method
 **/
const nameProfile = (fname, lname) => {
    if (fname != undefined && lname != undefined) {
        if (fname && lname) {
            return fname.charAt(0) + lname.charAt(0);
        } else if (fname != "" || lname != "") {
            return fname.charAt(0) + fname.charAt(1);
        } else {
            return;
        }
    } else return;
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function crops the string/text till given limit
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} text Text to limit till the length provided
 * @param {String} limit Limit till which the text will be used
 * @method
 **/
const limitText = (text, limit) => {
    if (text.length > limit) {
        const textLimit = limit - 3;
        const clippedText = text.substring(0, textLimit);

        return `${clippedText}...`;
    } else {
        return text;
    }
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Validate the availability of user from conversations to join the conversation
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {ConversationData} conversation Conversation object from to validate the availability
 * @param {Number} userId Id of user to search
 * @returns {Boolean}
 * @method
 */
const validateAvailablity = (conversation, userId) => {
    let flag = true;
    conversation.map((data) => {
        data.conversation_users.map((user) => {
            data.conversation_users.map((val) => {
                if (
                    _.has(user, ["user_id"]) &&
                    _.has(val, ["user_id"]) &&
                    val.user_id == user.user_id
                ) {
                    flag = false;
                }
            });

            if (_.has(user, ["user_id"]) && user.user_id == userId) {
                flag = false;
            }
        });
    });
    return flag;
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To filter the conversations array to remove the duplicate user
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {ConversationData[]} conversations Collection of conversations to check for duplicate users
 * @returns {ConversationData[]}
 * @method
 */
const reFilterConversations = (conversations) => {
    const final_conversation = [];
    const oneOfDuplicates = [];
    conversations.map((item, key) => {
        let flag = false;
        conversations.map((val, index) => {
            if (key != index) {
                item.conversation_users.map((user) => {
                    val.conversation_users.map((userData) => {
                        if (
                            _.has(user, ["user_id"]) &&
                            _.has(userData, ["user_id"]) &&
                            user.user_id == userData.user_id
                        ) {
                            flag = true;
                            if (
                                oneOfDuplicates.indexOf(item) == -1 &&
                                validateAvailablity(oneOfDuplicates, userData.user_id)
                            ) {
                                oneOfDuplicates.push(item);
                            }
                        }
                    });
                });
            }
        });
        if (flag == false) {
            final_conversation.push(item);
        }
    });
    return [...oneOfDuplicates, ...final_conversation];
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To sort the single user's conversation where user count in conversation is one
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ConversationData} data User's conversations array to sort
 * @returns {UserBadge[]}
 * @method
 */
const sortSingleUser = (data) => {
    const teamVip = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users)
            ? item.conversation_users[0]
            : {};
        if (val.event_role == 1 && val.is_vip) {
            return item;
        }
    });

    const expertVip = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users)
            ? item.conversation_users[0]
            : {};
        if (val.event_role == 2 && val.is_vip) {
            return item;
        }
    });

    const simpleTeam = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users)
            ? item.conversation_users[0]
            : {};
        if (val.event_role == 1 && !val.is_vip) {
            return item;
        }
    });

    const simpleExpert = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users)
            ? item.conversation_users[0]
            : {};
        if (val.event_role == 2 && !val.is_vip) {
            return item;
        }
    });

    const simpleVip = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users)
            ? item.conversation_users[0]
            : {};
        if (val.event_role == 0 && val.is_vip) {
            return item;
        }
    });

    const simpleUser = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users)
            ? item.conversation_users[0]
            : {};
        if (val.event_role == 0 && !val.is_vip) {
            return item;
        }
    });

    return [
        ...teamVip,
        ...expertVip,
        ...simpleTeam,
        ...simpleExpert,
        ...simpleVip,
        ...simpleUser,
    ];
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To sort the conversations by conversation users count in them
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {ConversationData[]} conversations  All the conversations of current space
 * @returns {String}
 * @method
 */
const reArrangeConversations = (conversations) => {
    return conversations;
    const sortedSingleUsers = conversations.filter((val) => {
        if (val.conversation_users.length == 1) {
            return val;
        }
    });

    const singleUserConversations = sortSingleUser(sortedSingleUsers);

    const twoUsersConversations = conversations.filter((val) => {
        if (val.conversation_users.length == 2) {
            return val;
        }
    });

    const threeConversations = conversations.filter((val) => {
        if (val.conversation_users.length == 3) {
            return val;
        }
    });

    const fourConversations = conversations.filter((val) => {
        if (val.conversation_users.length == 4) {
            return val;
        }
    });
    const fiveConversations = conversations.filter((val) => {
        if (val.conversation_users.length == 5) {
            return val;
        }
    });
    return reFilterConversations([
        ...singleUserConversations,
        ...twoUsersConversations,
        ...threeConversations,
        ...fourConversations,
        ...fiveConversations,
    ]);
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function filters updated badge data before sending it to other users.
 * According to the visibility.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {UserBadge} data User badge object
 * @method
 **/
const filterBadgeData = (data) => {
    const newBadgeData = {
        ...data,
        user_lname:
            _.has(data, ["visibility", "user_lname"]) &&
            data.visibility.user_lname == 1
                ? data.user_lname
                : "",
        company:
            _.has(data, ["visibility", "company"]) && data.visibility.company == 1
                ? data.company
                : {},
        unions:
            _.has(data, ["visibility", "unions"]) && data.visibility.unions == 1
                ? data.unions
                : [],
        personal_info: {
            ...data.personal_info,
            field_1:
                _.has(data, ["visibility", "p_field_1"]) &&
                data.visibility.p_field_1 == 1
                    ? data.personal_info.field_1
                    : "",
            field_2:
                _.has(data, ["visibility", "p_field_2"]) &&
                data.visibility.p_field_2 == 1
                    ? data.personal_info.field_2
                    : "",
            field_3:
                _.has(data, ["visibility", "p_field_3"]) &&
                data.visibility.p_field_3 == 1
                    ? data.personal_info.field_3
                    : "",
        },
        personal_tags: !_.isEmpty(data.personal_tags)
            ? data.personal_tags.filter((val) => val.is_moderated == 1)
            : [],
        professional_tags: !_.isEmpty(data.professional_tags)
            ? data.professional_tags.filter((val) => val.is_moderated == 1)
            : [],
    };
    delete newBadgeData.is_self;

    return newBadgeData;
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To search the label by name from the labels
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {String} labelName Name of the label
 * @param {Label[]} labels Collection of labels
 * @returns {String}
 * @method
 */
const getLabel = (labelName, labels) => {
    let currentLang;
    if (localStorage.getItem("current_lang")) {
        currentLang = localStorage.getItem("current_lang").toLowerCase();
    } else {
        localStorage.setItem("current_lang", "FR");
        currentLang = "fr";
    }
    // const currentLang = localStorage.getItem("current_lang").toLowerCase();
    let val = "";
    if (labelName && labels) {
        labels.map((v) => {
            if (labelName == v.name) {
                v.locales.map((l) => {
                    if (currentLang == l.locale) {
                        val = l.value;
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
 * @description To prepare the group logo if its enabled from the settings of group graphics
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param target
 * @returns {String|Number|File|null}
 */
const prepareEventGroupLogo = (target) => {
    return target.graphic_data.customized_colors
    && target.graphic_data.group_has_own_customization
        ? target.graphic_data.kct_graphics_logo
        : null
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function prepares graphics data in terms of colorObj which can be used in dynamic css as per the
 * customisation.
 * Directly triggers a function of dynamic css which is responsible for applying all the customisation
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {GraphicsData} graphics_data - graphics_data contains all the customisation data.
 * @method
 */
const implementGraphicsHelper = (graphics_data) => {
    const {
        event_color_1,
        event_color_2,
        event_color_3,
        background_color,
        separation_line_color,
        text_color,
        unselected_spaces_square,
        selected_spaces_square,
        bottom_bg_color,
        has_custom_background,
        bottom_bg_is_colored,
        tag_color,
        customized_texture,
        texture_square_corner,
        texture_remove_frame,
        texture_remove_shadow,
        customized_colors,
        badge_bg_color,
        join_bg_color,
        join_text_color,
        video_url,
        professional_tag_color,
        personal_tag_color,
        sh_hide_on_off,
        sh_background,
        sh_customized,
        conv_customization,
        conv_background,
        badge_customization,
        badge_background,
        user_grid_customization,
        user_grid_background,
        user_grid_pagination_color,
        tags_customization,
        tags_text_color,
        button_customized,
        content_customized,
        content_background,
        space_customization,
        space_background,
        extends_color_user_guide,
    } = graphics_data;
    const color1 = event_color_1;
    const color2 = event_color_2;
    const color3 = event_color_3;

    const colorObj = {
        color1: rgbaObjectToStr(color1),
        color2: rgbaObjectToStr(color2),
        color3: rgbaObjectToStr(color3),
        hasHeaderBackground: has_custom_background,
        headerBackground: rgbaObjectToStr(background_color),
        headerTextColor: rgbaObjectToStr(text_color),
        separationLineColor: rgbaObjectToStr(separation_line_color),
        bottomBackgroundColor: rgbaObjectToStr(bottom_bg_color),
        hasBottomBackgroundColor: bottom_bg_is_colored,
        customizeTexture: customized_texture,
        textureRound: texture_square_corner,
        tagColor: rgbaObjectToStr(tag_color),
        textureWithFrame: texture_remove_frame,
        selectedSpacesSquare: selected_spaces_square,
        unselectedSpacesSquare: unselected_spaces_square,
        textureWithShadow: texture_remove_shadow,
        customizeColor: customized_colors,
        transparent: color1
            ? "rgb(" + color1.r + " " + color1.g + " " + color1.b + " / " + "40%)"
            : "",
        badgeBgColor: rgbaObjectToStr(badge_bg_color),
        joinButtonBgColor: rgbaObjectToStr(join_bg_color),
        joinButtonTextBgColor: rgbaObjectToStr(join_text_color),
        professional_tag_color: rgbaObjectToStr(professional_tag_color),
        personal_tag_color: rgbaObjectToStr(personal_tag_color),
        sh_hide_on_off: sh_hide_on_off,
        sh_background: rgbaObjectToStr(sh_background),
        sh_customized: sh_customized,
        conv_customization: conv_customization,
        conv_background: rgbaObjectToStr(conv_background),
        badge_customization: badge_customization,
        badge_background: rgbaObjectToStr(badge_background),
        user_grid_customization: user_grid_customization,
        user_grid_background: rgbaObjectToStr(user_grid_background),
        tags_customization: tags_customization,
        tags_text_color: rgbaObjectToStr(tags_text_color),
        button_customized: button_customized,
        user_grid_pagination_color: rgbaObjectToStr(user_grid_pagination_color),
        content_customized: content_customized,
        content_background: rgbaObjectToStr(content_background),
        space_customization: space_customization,
        space_background: rgbaObjectToStr(space_background),
        extends_color_user_guide: extends_color_user_guide,
    };

    CSSGenerator.generateNewInterfaceCSS(colorObj);
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To set default graphics helper for applying the graphics customization
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {Object} mainColor Main colors of application from graphics setting
 * @param {ColorRGBA} mainColor.mainColor1 Main color 1 of application
 * @param {ColorRGBA} mainColor.mainColor2 Main color 2 of application
 * @param {ColorRGBA} mainColor.mainColor3 Main color 3 of application
 * @param {ColorRGBA} mainColor.headerColor header color of application
 * @param {ColorRGBA} mainColor.headerTextColor text color on header of application
 * @method
 */
const setDefaultGraphicsHelper = (mainColor) => {
    const {color1, color2, head_bg, head_tc} = mainColor;
    const colorObj = {
        mainColor1: rgbaObjectToStr(color1),
        mainColor2: rgbaObjectToStr(color2),
        mainColor3: rgbaObjectToStr(color2),
        headerColor: rgbaObjectToStr(head_bg),
        headerTextColor: rgbaObjectToStr(head_tc),
    };
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description In set timeout value we can have at max 32 BIT Integer so checking if timeout is having larger
 * than that it will make it 32 BIT max value else value will be returned
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param value
 * @returns {number|*}
 */
const getMaxSetTimeoutValue = value=> value > Constants.INT32BIT_MAX ? Constants.INT32BIT_MAX : value;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description to try to unmute the zoom sdk
 * ---------------------------------------------------------------------------------------------------------------------
 */
const unmuteZoomSdk = () => {
    updateZoomMute(false);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description to try to mute the zoom sdk
 * ---------------------------------------------------------------------------------------------------------------------
 */

const muteZoomSdk = () => {
    updateZoomMute(true);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description to update the zoom sdk mute state to provided input
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Boolean} mute // target value of mute state to apply
 */
const updateZoomMute = (mute) => {
    try {
        const client = ZoomMtgEmbedded.createClient();
        client.mute(mute).then(() => {}).catch(e => {});
    } catch (e) {

    }
}

function rgba2hex(target) {
    const {r,g,b,a} = target;
    var outParts = [
        r.toString(16),
        g.toString(16),
        b.toString(16),
        Math.round(a * 255).toString(16).substring(0, 2)
    ];
    // Pad single-digit output values
    outParts.forEach(function (part, i) {
        if (part.length === 1) {
            outParts[i] = '0' + part;
        }
    })
    return ('#' + outParts.join(''));
}



export default {
    reArrangeConversations,
    limitText,
    nameProfile,
    getTimeDifference,
    rgbaObjectToStr,
    getTimeUserTimeZone,
    currLang,
    alertOptions,
    alertMsg,
    dateTimeFormat,
    handleError,
    pageLoading,
    diffYMDHMS,
    jsUcfirst,
    objLength,
    amznS3Patth,
    tagStrip,
    jsUcfirst2,
    getAlphaValidator,
    showToolTip,
    filterBadgeData,
    setGlobalRef,
    globalAlert,
    compareObjects,
    getNameSpace,
    reFilterConversations,
    getLabel,
    implementGraphicsHelper,
    prepareEventGroupLogo,
    setDefaultGraphicsHelper,
    getMaxSetTimeoutValue,
    zoom: {
        mute : muteZoomSdk,
        unmute: unmuteZoomSdk
    },
    rgba2hex: rgba2hex,
};
