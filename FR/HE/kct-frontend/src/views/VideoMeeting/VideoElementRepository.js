import React from "react";
import _ from 'lodash';

let MAX_USERS = 8; // users possible in conversation excluding self
let SEATS = [];

const updateMaxSeats = count => {
    MAX_USERS = count;
}

/**
 * @module VideoElementRepository
 */

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function handles the reset of video tile seats
 * and sets data of max_users
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 */
const resetSeats = () => {
    // MAX_USERS = total;
    for (let i = 0; i < 8; i++) {
        SEATS[i] = {
            userId: null,
            tileState: null,
        }
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function filters the user array on basis of user id and returns the index in array
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {UserBadge[]} users Users data from to find the user
 * @param {Number} userId Id of the user to find the index for
 * @returns {Number}
 */
const findUserFromConversation = (users, userId) => {
    let i = 0;
    let j = !_.isEmpty(users) && users.length;

    while (i < j) {
        if (users[i].user_id == userId) {
            return i;
        }
        i++;
    }
    return -1;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function returns the current seats
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 */
const getSeats = () => {
    return SEATS;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function checks empty seats and returns the index if there are any
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 */
const getAvailableSeatIndex = () => {
    for (let i = 0; i < MAX_USERS; i++) {
        if (SEATS[i] && SEATS[i].userId === null) {
            return i;
        }
    }
    return -1;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function checks userId in allocated seats and returns the index of the seat
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} userId Id of user to find the index for
 */
const getUserIndex = (userId) => {
    for (let i = 0; i < MAX_USERS; i++) {
        if (!_.isEmpty(SEATS) && SEATS[i] && SEATS[i].userId == userId) {
            return i;
        }
    }
    return -1;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Function checks empty seats and fills it with user id and tile state
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {UserBadge[]} users Users array from where the seat will be searched
 * @method
 */
const allocateUsersSeat = (users) => {
    // freeing seat on which user is left (from conversation users)
    for (let i = 0; i < MAX_USERS; i++) {
        if (!_.isEmpty(SEATS) && SEATS[i] && SEATS[i].userId && !users.find((u) => u.user_id == SEATS[i].userId)) {
            SEATS[i] = {
                userId: null, // removing user id as its not present in active conversation users list
                tileState: null
            }
        }
    }

    let j = !_.isEmpty(users) && users.length;

    // allocating new users a seat;
    for (let i = 0; i < j; i++) {
        if (!SEATS.find((seat) => seat.userId == users[i].user_id)) {
            let availableIndex = getAvailableSeatIndex();
            SEATS[availableIndex] = {
                userId: users[i].user_id,
                tileState: null,
                isDummy: users[i].is_dummy == 1 ? 1 : 0
            }
        }
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To set the provided seat has now receiving the video
 * Currently using to show loader or not
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Number} userId Id of the user to update the seat value
 * @param {Object} tileState Tile state of respective user
 * @method
 */
const updateSeatTileState = (userId, tileState) => {
    for (let i = 0; i < MAX_USERS; i++) {
        if (userId && SEATS[i].userId && SEATS[i].userId == userId) {
            SEATS[i] = {
                ...SEATS[i],
                tileState: tileState
            }
        }
    }
}


export default {
    MAX_USERS,
    SEATS,
    resetSeats,
    allocateUsersSeat,
    getSeats,
    getUserIndex,
    findUserFromConversation,
    updateSeatTileState,
    updateMaxSeats: updateMaxSeats,
}



