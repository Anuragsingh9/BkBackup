/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file provides the user model to ensure when the user data is sent from the backend it contains all
 * the keys inside that
 * ---------------------------------------------------------------------------------------------------------------------
 */

import Helper from '../../Helper.js';

const userModel = {
    company: null,
    instance: null,
    is_mute: 0,
    is_self: 1,
    personal_info: {field_1: null, field_2: null, field_3: null},
    personal_tags: [],
    press: null,
    professional_tags: [],
    social_links: {facebook: null, twitter: null, instagram: null, linkedin: null},
    tags_data: {used_tag: [], unused_tag: []},
    unions: [],
    user_avatar: null,
    user_email: "amanptl333@mailinator.com",
    user_fname: "AMAN",
    user_id: 2448,
    user_lname: "RAMAN",
    visibility: {
        company: 1,
        p_field_1: 1,
        p_field_2: 1,
        p_field_3: 1,
        personal_tags: 1,
        professional_tags: 1,
        unions: 1,
        user_lname: 1
    }

};

const userOthers = {
    company: null,
    instance: null,
    is_mute: 0,
    is_self: 1,
    personal_info: {field_1: null, field_2: null, field_3: null},
    personal_tags: [],
    press: null,
    professional_tags: [],
    social_links: {facebook: null, twitter: null, instagram: null, linkedin: null},
    tags_data: {used_tag: [], unused_tag: []},
    unions: [],
    user_avatar: null,
    user_email: "amanptl333@mailinator.com",
    user_fname: "AMAN",
    user_id: 2448,
    user_lname: "RAMAN"
};

class User {


    checkSelfUser = (data) => {
        return Helper.compareObjects(data, userModel);

    }


    checkOtherUsers = (data) => {

        return Helper.compareObjects(data, userOthers);
    }


}

const user = new User();

const selfCheck = user.checkSelfUser;

const otherCheck = user.checkOtherUsers;

export default {
    selfCheck,
    otherCheck
} 