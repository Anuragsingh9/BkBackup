/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file provide the pagination related dispatcher method for the user grid
 * ---------------------------------------------------------------------------------------------------------------------
 */

import _ from 'lodash';
import {rowsGenerator} from './paginationUtils.js';
import Helper from '../../Helper.js';
import {reArrangeConversations} from "./common";

export const prepareGridUsersData = (conversations, totalUsers = 0) => {
    totalUsers = totalUsers || countConversationUsers(conversations);

    return {
        current_page: 0,
        totalpages: 0,
        currentPageData: [],
    };
}

const countConversationUsers = (conversations) => {
    let totalUsers = 0;
    conversations.forEach(conversation => {
        totalUsers += conversation?.conversation_users?.length;
    })
    return totalUsers;
}

export const prepareGridDataData = (conversations, oldState) => {
    const state = oldState;
    const {gridPagination} = state;
    const {current_page} = gridPagination;

    return sortGridData({
        ...state,
        interfaceSpacesData: {
            ...state.interfaceSpacesData,
            current_space_conversations: conversations
        }
    }, current_page);

}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will convert the grid data to the page wise in different rows divided
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param oldState
 * @param page
 * @returns {{currentPageData: [], totalpages: number, current_page: *}}
 */
export const sortGridData = (oldState, page) => {
    const state = oldState;
    const {gridPagination} = state;
    const {
        current_page,
    } = gridPagination;
    const newPage = page ? page : current_page;
    let conversations = _.has(state, ['interfaceSpacesData', 'current_space_conversations']) ? state.interfaceSpacesData.current_space_conversations : [];
    conversations = reArrangeConversations(conversations);
    const totalData = Helper.reFilterConversations(conversations);
    return rowsGenerator(totalData, newPage, state);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description As the single users in the conversation need to be sorted by the roles so here the users will be sorted
 * by the roles assigned in event
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param data
 * @returns {*[]}
 */
export const sortSingleUser = (data) => {
    const teamVip = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users) ? item.conversation_users[0] : {};
        if (val.event_role == 1 && val.is_vip) {
            return item;
        }
    });

    const expertVip = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users) ? item.conversation_users[0] : {};
        if (val.event_role == 2 && val.is_vip) {
            return item;
        }
    });

    const simpleTeam = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users) ? item.conversation_users[0] : {};
        if (val.event_role == 1 && !val.is_vip) {
            return item;
        }
    });

    const simpleExpert = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users) ? item.conversation_users[0] : {};
        if (val.event_role == 2 && !val.is_vip) {
            return item;
        }
    });

    const simpleVip = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users) ? item.conversation_users[0] : {};
        if (val.event_role == 0 && val.is_vip) {
            return item;
        }
    });


    const simpleUser = data.filter((item) => {
        const val = !_.isEmpty(item.conversation_users) ? item.conversation_users[0] : {};
        if (val.event_role == 0 && !val.is_vip) {
            return item;
        }
    });

    return [...teamVip, ...expertVip, ...simpleTeam, ...simpleExpert, ...simpleVip, ...simpleUser]

}


export const filteringDataForCurrentPage = (allConversation, totalData, page) => {


    const {single, two, three, four, five} = allConversation;

    const singleLength = _.isEmpty(single) ? 0 : single.length;
    const twoLength = _.isEmpty(two) ? 0 : two.length;
    const threeLength = _.isEmpty(three) ? 0 : three.length;
    const fourLength = _.isEmpty(four) ? 0 : four.length;
    const fiveLength = _.isEmpty(five) ? 0 : five.length;


    const totalLength = singleLength + (twoLength * 2) + (threeLength * 3) + (fourLength * 4) + (fiveLength * 5);

    const maxPage = Math.ceil(totalLength / 144);

    const previousMaxLength = (page - 1) * 144;
    let lastType = checkLastType(singleLength, twoLength, threeLength, fourLength, fiveLength, previousMaxLength);
    const filteredData = filterTotalData(lastType, allConversation, previousMaxLength);

    return {data: filteredData, maxPage, currentPage: page};
}


const filterTotalData = (type, totalData, startPoint) => {

    const {single, two, three, four, five} = totalData;

    switch (type) {
        case 1:
            if (single.length > startPoint) {
                const startingIndex = startPoint;
                const newSingle = single.splice(startingIndex);
                return [...newSingle, ...two, ...three, ...four, ...five];
            } else {
                return [...single, ...two, ...three, ...four, ...five];
                ;
            }

        case 2:

            let newtwoIndex = (single.length + 1) - startPoint;
            const newDouble = two.splice(newtwoIndex);
            return [...newDouble, ...three, ...four, ...five];

        case 3:
            const newThreeIndex = startPoint - (single.length + (two.length * 2) + 1);
            const newThree = two.splice(newThreeIndex);
            return [...newThree, ...four, ...five];

        case 4:
            const newFourIndex = startPoint - (single.length + (two.length * 2) + (three.length * 3) + 1);
            const newFour = two.splice(newFourIndex);
            return [...newFour, ...five];

        case 5:
            const newFiveIndex = startPoint - (single.length + (two.length * 2) + (three.length * 3) + (four.length * 4) + 1);
            const newFive = two.splice(newFiveIndex);
            return [...newFive];

        default:
            break;
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will compare key values with respect to length param(all received from parameter).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Number} s Key name
 * @param {Number} a Key name
 * @param {Number} t Key name
 * @param {Number} f Key name
 * @param {Number} fi Key name
 * @param {Number} length Key name
 * @returns {Number}
 * @method
 */
const checkLastType = (a, s, t, f, fi, length) => {
    const b = s * 2;
    const c = t * 3;
    const d = f * 4;
    const e = fi * 5;
    if (a >= length) {
        return 1
    } else if (a + b >= length) {
        return 2
    } else if (a + b + c >= length) {
        return 3
    } else if (a + b + c + d >= length) {
        return 4;
    } else if (a + b + c + d + e >= length) {
        return 5;
    } else {
        return 1;
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will filter spacehost(system role) from received data.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} id Space host unique id
 * @param {Array} users Array of users data
 * @returns {Object}
 */
const filterSpaceHost = (id, users) => {
    const flag = users.filter((val) => {
        if (val.user_id != id) {
            return val
        }
    });
    return flag;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will increment the counter(received from parameter) by 1.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {number} counters
 * @returns {Number}
 * @method
 */
const getPosition = (counters) => {
    return counters + 1;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function checks and returns the position of user inside the row
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {number} position - position constant
 * @method
 **/
const getLastInList = (position) => {
    return (position + 1) % 12 == 0 || (position + 2) % 12 == 0 || position % 12 == 0;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function checks and returns the if the user is last in row or not.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {number} position - position constant
 * @method
 **/
const getLastPerson = (position) => {
    return ((position + 1) % 12 == 0 || position % 12 == 0);
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function calculates and returns the space left in row.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {number} position - position constant
 * @method
 **/
const leftSpace = (counters) => {
    return (12 - (counters % 12))
}


const createPagesData = (conversation) => {

    const pages = [];

    let counter = 0;

    conversation.map((val, index) => {
        let row = 1;
        let page = 1;

        const spacesLeft = leftSpace(counter);
        if (row == 1) {
            pages.push([]);
            page = page + 1;
        }
        const spaceHost = [];

        const usersData = !_.isEmpty(spaceHost) ? filterSpaceHost(spaceHost[0].user_id, val.conversation_users) : val.conversation_users;


        if (spacesLeft >= usersData.length) {

            usersData.map((item, key) => {
                const position = getPosition(counter);
                const lastInList = getLastInList(position);
                const lastPersons = getLastPerson(position);
                counter = counter + 1;
            });
        } else {
            let blankDiv = [];
            for (var i = 0; i < spacesLeft; i++) {
                counter = counter + 1;

            }

            usersData.map((item, key) => {
                const position = getPosition(counter);
                const lastInList = getLastInList(position);
                const lastPersons = getLastPerson(position);
                counter = counter + 1;


            });
        }

    })

}
  
  