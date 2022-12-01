/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file provides the reducer methods for applying the pagination on grid
 * As grid is divided into rows and each row contains the respective sorted users so pagination will reset the rows
 * with column data
 * ---------------------------------------------------------------------------------------------------------------------
 */


import {KeepContact as KCT} from '../../types';
import {sortGridData} from '../../utils/pagination.js';

const user = {
    company: {entity_id: null, long_name: "Reveal Agency", short_name: "Reveal Agency", position: null},
    dummy_video_url: "https://kct-videos.s3.eu-west-1.amazonaws.com/13-Work-Man-07-1.mp4",
    event_role: 0,
    is_dummy: 1,
    is_mute: 0,
    is_space_host: 1,
    is_vip: 0,
    personal_info: {field_1: null, field_2: null, field_3: null},
    personal_tags: [],
    professional_tags: [],
    social_links: {facebook: null, twitter: null, instagram: null, linkedin: null},
    tags_data: {used_tag: null, unused_tag: [],},
    unions: [{entity_id: null, long_name: "UEF", short_name: "UEF", position: null}],
    user_avatar: "https://s3.eu-west-2.amazonaws.com/kct-dev/humann.seque.in/dummy_users/13-Work-Man-07.jpg",
    user_email: null,
    user_fname: "Jonathan",
    user_id: 1,
    user_lname: "Vermont",
    visibility: {user_lname: 1, company: 1, unions: 1, p_field_1: 1, p_field_2: 1, p_field_3: 1, personal_tags: 1}
}

const single = {
    conversation_type: "single_user",
    conversation_users: [{...user, user_id: 1}],
    conversation_uuid: null,

}

const two = {
    conversation_type: "double_user",
    conversation_users: [{...user, user_id: 2}, {...user, user_id: 3}],
    conversation_uuid: null,
}

const three = {
    conversation_type: "three_user",
    conversation_users: [{...user, user_id: 4}, {...user, user_id: 5}, {...user, user_id: 6}],
    conversation_uuid: null,
}

const four = {
    conversation_type: "four_user",
    conversation_users: [{...user, user_id: 4}, {...user, user_id: 5}, {...user, user_id: 6}, {...user, user_id: 7}],
    conversation_uuid: null,
}

const five = {
    conversation_type: "five_user",
    conversation_users: [{...user, user_id: 4}, {...user, user_id: 5}, {...user, user_id: 6}, {
        ...user,
        user_id: 7
    }, {...user, user_id: 8}],
    conversation_uuid: null,
}


const paginationCases = (state, action) => {

    switch (action.type) {

        case KCT.NEW_INTERFACE.PAGINATION_UPDATE:
            const {page} = action.payload;
            const {gridPagination} = state;
            const {current_page} = gridPagination;

            const stateData = state;
            // const data = Array(163).fill(single);
            // const secondData = Array(95).fill(two);
            // const thirdData =  Array(11).fill(three);
            // const fourth =  Array(21).fill(four);
            const newData =

                state = {
                    ...state,
                    gridPagination: sortGridData(stateData, page ? page : current_page)
                }

            break;

    }

    return state;

}

export default paginationCases;